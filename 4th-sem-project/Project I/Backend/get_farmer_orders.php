<?php
session_set_cookie_params(0, '/');
session_start();
require "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// Fetch orders that contain at least one product from this farmer
// We need order details, customer info, and total items for THIS farmer
$sql = "SELECT DISTINCT o.*, p.payment_method, p.payment_status 
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN payments p ON o.order_id = p.order_id
        WHERE oi.farmer_id = ?
        ORDER BY o.order_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $farmer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($order = mysqli_fetch_assoc($result)) {
    // For each order, find the specific items belonging to this farmer
    $itemsSql = "SELECT oi.*, pr.name as product_name, pr.image 
                 FROM order_items oi
                 JOIN products pr ON oi.product_id = pr.product_id
                 WHERE oi.order_id = ? AND oi.farmer_id = ?";
    $itemsStmt = mysqli_prepare($conn, $itemsSql);
    mysqli_stmt_bind_param($itemsStmt, "ii", $order['order_id'], $farmer_id);
    mysqli_stmt_execute($itemsStmt);
    $itemsResult = mysqli_stmt_get_result($itemsStmt);
    
    $order['farmer_items'] = [];
    $order['farmer_total'] = 0;
    while ($item = mysqli_fetch_assoc($itemsResult)) {
        $order['farmer_items'][] = $item;
        $order['farmer_total'] += $item['subtotal'];
    }
    
    $orders[] = $order;
}

echo json_encode(['success' => true, 'orders' => $orders]);
mysqli_close($conn);
?>
