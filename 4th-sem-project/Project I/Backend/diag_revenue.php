<?php
include 'connection.php';
header('Content-Type: application/json');

$date = '2025-12-21';

// 1. Total amount from orders table
$q1 = "SELECT SUM(total_amount) as total FROM orders WHERE DATE(order_date) = '$date' AND order_status='Fulfilled'";
$r1 = mysqli_query($conn, $q1);
$val1 = mysqli_fetch_assoc($r1)['total'] ?? 0;

// 2. Sum of subtotals from order_items table
$q2 = "SELECT SUM(oi.subtotal) as total 
       FROM order_items oi 
       JOIN orders o ON oi.order_id = o.order_id 
       WHERE DATE(o.order_date) = '$date' AND o.order_status='Fulfilled'";
$r2 = mysqli_query($conn, $q2);
$val2 = mysqli_fetch_assoc($r2)['total'] ?? 0;

// 3. Breakdown by farmer
$q3 = "SELECT f.firstName, f.lastName, SUM(oi.subtotal) as farmer_total
       FROM order_items oi
       JOIN farmer_registration f ON oi.farmer_id = f.farmer_id
       JOIN orders o ON oi.order_id = o.order_id
       WHERE DATE(o.order_date) = '$date' AND o.order_status='Fulfilled'
       GROUP BY f.farmer_id";
$r3 = mysqli_query($conn, $q3);
$breakdown = [];
while ($row = mysqli_fetch_assoc($r3)) {
    $breakdown[] = $row;
}

echo json_encode([
    'orders_table_total' => $val1,
    'order_items_table_total' => $val2,
    'farmer_breakdown' => $breakdown
]);
?>
