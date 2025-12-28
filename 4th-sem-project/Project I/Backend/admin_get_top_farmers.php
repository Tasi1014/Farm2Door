<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'names' => [], 'amounts' => [], 'message' => ''];

// Check for POST data (date range)
$input = json_decode(file_get_contents('php://input'), true);
$start = $input['start_date'] ?? null;
$end   = $input['end_date'] ?? null;

if ($start && $end) {
    $sql = "SELECT CONCAT(f.firstName, ' ', f.lastName) AS farmer_name, SUM(oi.subtotal) AS total
            FROM order_items oi
            JOIN farmer_registration f ON oi.farmer_id = f.farmer_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status IN ('Fulfilled')
              AND DATE(o.order_date) BETWEEN ? AND ?
            GROUP BY f.farmer_id
            ORDER BY total DESC
            LIMIT 5";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $start, $end);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Default dashboard
    $sql = "SELECT CONCAT(f.firstName, ' ', f.lastName) AS farmer_name, SUM(oi.subtotal) AS total
            FROM order_items oi
            JOIN farmer_registration f ON oi.farmer_id = f.farmer_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status IN ('Fulfilled')
            GROUP BY f.farmer_id
            ORDER BY total DESC
            LIMIT 5";
    $result = mysqli_query($conn, $sql);
}

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['names'][]   = $row['farmer_name'];
        $response['amounts'][] = (float)$row['total'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
