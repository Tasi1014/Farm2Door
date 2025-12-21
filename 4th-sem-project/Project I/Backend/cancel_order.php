<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID missing']);
    exit;
}

try {
    mysqli_begin_transaction($conn);

    // 1. Verify order ownership and status
    $sql = "SELECT order_status, customer_id FROM orders WHERE order_id = ? FOR UPDATE";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);

    $reason = $input['reason'] ?? 'Cancelled by customer';

    if (!$order || $order['customer_id'] != $_SESSION['customer_id']) {
        throw new Exception("Unauthorized or order not found");
    }

    if ($order['order_status'] !== 'Processing' && $order['order_status'] !== 'Pending') {
        throw new Exception("Only processing orders can be cancelled.");
    }

    $old_status = $order['order_status'];

    // 2. Fetch items to restore stock
    $itemsSql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $itemsStmt = mysqli_prepare($conn, $itemsSql);
    mysqli_stmt_bind_param($itemsStmt, "i", $order_id);
    mysqli_stmt_execute($itemsStmt);
    $itemsResult = mysqli_stmt_get_result($itemsStmt);

    $restoreStmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");

    while ($item = mysqli_fetch_assoc($itemsResult)) {
        mysqli_stmt_bind_param($restoreStmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($restoreStmt);
    }

    // 3. Update order status and reason
    $updateOrderSql = "UPDATE orders SET order_status = 'Cancelled', cancellation_reason = ? WHERE order_id = ?";
    $updateOrderStmt = mysqli_prepare($conn, $updateOrderSql);
    mysqli_stmt_bind_param($updateOrderStmt, "si", $reason, $order_id);
    mysqli_stmt_execute($updateOrderStmt);

    // 4. Update payment status
    // If Paid -> Keep as Paid (for Admin to refund), If Pending -> Cancelled
    $updatePaySql = "UPDATE payments 
                    SET payment_status = CASE 
                        WHEN payment_status = 'Paid' THEN 'Paid'
                        WHEN payment_status = 'Pending' THEN 'Cancelled'
                        ELSE payment_status 
                    END
                    WHERE order_id = ?";
    $updatePayStmt = mysqli_prepare($conn, $updatePaySql);
    mysqli_stmt_bind_param($updatePayStmt, "i", $order_id);
    mysqli_stmt_execute($updatePayStmt);

    // 5. Log the status change
    $logSql = "INSERT INTO order_status_logs (order_id, old_status, new_status, actor_type, actor_id, rejection_reason) 
               VALUES (?, ?, 'Cancelled', 'Customer', ?, ?)";
    $logStmt = mysqli_prepare($conn, $logSql);
    mysqli_stmt_bind_param($logStmt, "iiss", $order_id, $old_status, $_SESSION['customer_id'], $reason);
    mysqli_stmt_execute($logStmt);

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
