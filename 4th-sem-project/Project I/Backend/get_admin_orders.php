<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Fetch all orders with customer and payment info
$sql = "SELECT o.*, p.payment_id, p.payment_method, p.payment_status, c.Email as customer_email 
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        JOIN customer_registration c ON o.customer_id = c.id
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);
$orders = [];

while ($order = mysqli_fetch_assoc($result)) {
    // Fetch all items and their farmer info
    $itemsSql = "SELECT oi.*, pr.name as product_name, pr.image, 
                        CONCAT(f.firstName, ' ', f.lastName) as farmer_name 
                 FROM order_items oi
                 JOIN products pr ON oi.product_id = pr.product_id
                 JOIN farmer_registration f ON oi.farmer_id = f.farmer_id
                 WHERE oi.order_id = ?";
    $itemsStmt = mysqli_prepare($conn, $itemsSql);
    if ($itemsStmt) {
        mysqli_stmt_bind_param($itemsStmt, "i", $order['order_id']);
        mysqli_stmt_execute($itemsStmt);
        $itemsRes = mysqli_stmt_get_result($itemsStmt);
        
        $order['items'] = [];
        while ($item = mysqli_fetch_assoc($itemsRes)) {
            $order['items'][] = $item;
        }
        mysqli_stmt_close($itemsStmt);
    } else {
        $order['items'] = [];
    }
    
    $orders[] = $order;
}

echo json_encode(['success' => true, 'orders' => $orders]);
mysqli_close($conn);
?>
