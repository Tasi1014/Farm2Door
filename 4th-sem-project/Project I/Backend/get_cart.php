<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Get Cart Items joined with Products
$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.customer_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$total_price = 0;
$total_items = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total_price += $row['subtotal'];
    $total_items += $row['quantity'];
    $items[] = $row;
}

echo json_encode([
    'success' => true, 
    'items' => $items, 
    'total_price' => $total_price,
    'total_items' => $total_items
]);

mysqli_close($conn);
?>
