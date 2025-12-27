<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'dates' => [], 'amounts' => [], 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);
$start     = $input['start_date'] ?? null;
$end       = $input['end_date'] ?? null;
$farmer_id = $input['farmer_id'] ?? 'overall';

if (!$start || !$end) {
    $response['message'] = 'Invalid date range';
    echo json_encode($response);
    exit;
}

if ($farmer_id !== 'overall') {
    $sql = "SELECT DATE(o.order_date) AS d, SUM(oi.subtotal) AS total
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status IN ('Fulfilled')
              AND DATE(o.order_date) BETWEEN ? AND ?
              AND oi.farmer_id = ?
            GROUP BY d
            ORDER BY d";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $start, $end, $farmer_id);
} else {
    // Overall - Sum of all items in Fulfilled orders
    $sql = "SELECT DATE(o.order_date) AS d, SUM(oi.subtotal) AS total
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status IN ('Fulfilled')
              AND DATE(o.order_date) BETWEEN ? AND ?
            GROUP BY d
            ORDER BY d";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $start, $end);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $response['dates'][]   = $row['d'];
    $response['amounts'][] = (float)$row['total'];
}

$response['success'] = true;
echo json_encode($response);
mysqli_close($conn);
?>
