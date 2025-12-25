<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'dates' => [], 'amounts' => [], 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);
$start = $input['start_date'] ?? null;
$end   = $input['end_date'] ?? null;

if (!$start || !$end) {
    $response['message'] = 'Invalid date range';
    echo json_encode($response);
    exit;
}

$sql = "SELECT DATE(order_date) AS d, SUM(total_amount) AS total
        FROM orders
        WHERE order_status IN ('Ready for Pickup', 'Fulfilled')
          AND DATE(order_date) BETWEEN ? AND ?
        GROUP BY DATE(order_date)
        ORDER BY DATE(order_date)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $start, $end);
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
