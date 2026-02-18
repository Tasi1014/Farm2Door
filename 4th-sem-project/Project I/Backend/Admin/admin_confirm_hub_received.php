<?php
session_set_cookie_params(0, '/');
session_start();
include '../connection.php';

header('Content-Type: application/json');

// Verify admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

try {
    mysqli_begin_transaction($conn);

    // Verify order is in Dispatched status
    $checkSql = "SELECT order_id, order_status FROM orders WHERE order_id = ? FOR UPDATE";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $order_id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        throw new Exception('Order not found');
    }

    if ($order['order_status'] !== 'Dispatched') {
        throw new Exception('Only dispatched orders can be confirmed as received. Current status: ' . $order['order_status']);
    }

    // Update order status to Received
    $updateSql = "UPDATE orders SET order_status = 'Received' WHERE order_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "i", $order_id);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        throw new Exception('Failed to update order status');
    }

    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'message' => 'Order #' . $order_id . ' confirmed as received at collection hub'
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
