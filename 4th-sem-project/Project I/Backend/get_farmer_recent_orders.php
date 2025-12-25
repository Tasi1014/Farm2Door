<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// 1. Fetch 5 Most Recent Orders
$sql = "SELECT DISTINCT o.order_id, o.order_date, o.order_status, c.firstName, c.lastName
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN customer_registration c ON o.customer_id = c.id
        WHERE oi.farmer_id = ?
        ORDER BY o.order_date DESC
        LIMIT 5";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $farmer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$recent_orders = [];
while ($order = mysqli_fetch_assoc($result)) {
    // Calculate total for THIS farmer in THIS order
    $sumSql = "SELECT SUM(subtotal) as total FROM order_items WHERE order_id = ? AND farmer_id = ?";
    $sumStmt = mysqli_prepare($conn, $sumSql);
    mysqli_stmt_bind_param($sumStmt, "ii", $order['order_id'], $farmer_id);
    mysqli_stmt_execute($sumStmt);
    $sumRes = mysqli_fetch_assoc(mysqli_stmt_get_result($sumStmt));
    
    $order['customer_name'] = $order['firstName'] . ' ' . $order['lastName'];
    $order['order_total'] = (float)($sumRes['total'] ?? 0);
    $recent_orders[] = $order;
}

echo json_encode(['success' => true, 'recent_orders' => $recent_orders]);
mysqli_close($conn);
?>
