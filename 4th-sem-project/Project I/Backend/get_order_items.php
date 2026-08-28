<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID missing']);
    exit;
}

// Ensure the order belongs to the user
$checkSql = "SELECT customer_id FROM orders WHERE order_id = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "i", $order_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$order = mysqli_fetch_assoc($checkResult);

if (!$order || $order['customer_id'] != $_SESSION['customer_id']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access to order items']);
    exit;
}

// Fetch items from snapshot in order_items
$sql = "SELECT * FROM order_items WHERE order_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode(['success' => true, 'items' => $items]);
mysqli_close($conn);
?>
