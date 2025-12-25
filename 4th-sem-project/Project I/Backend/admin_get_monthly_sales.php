<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'months' => [], 'amounts' => [], 'message' => ''];

// Filter for revenue generating statuses for CURRENT MONTH daily
$sql = "SELECT DATE(order_date) AS order_day,
               SUM(total_amount) AS total
        FROM orders
        WHERE order_status IN ('Fulfilled')
          AND MONTH(order_date) = MONTH(CURRENT_DATE())
          AND YEAR(order_date) = YEAR(CURRENT_DATE())
        GROUP BY order_day
        ORDER BY order_day ASC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['months'][]  = $row['order_day']; // Keeping key 'months' to avoid double-editing JS heavily
        $response['amounts'][] = (float)$row['total'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
