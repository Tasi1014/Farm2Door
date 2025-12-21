<?php
session_set_cookie_params(0, '/');
session_start();
require "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$input = json_decode(file_get_contents('php://input'), true);

$order_id = $input['order_id'] ?? null;
$new_status = $input['status'] ?? null;

if (!$order_id || !$new_status) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    mysqli_begin_transaction($conn);

    // 1. Get current status for logging
    $statusSql = "SELECT order_status FROM orders WHERE order_id = ? FOR UPDATE";
    $statusStmt = mysqli_prepare($conn, $statusSql);
    mysqli_stmt_bind_param($statusStmt, "i", $order_id);
    mysqli_stmt_execute($statusStmt);
    $old_status = mysqli_fetch_assoc(mysqli_stmt_get_result($statusStmt))['order_status'];

    // 2. Update order status
    $updateSql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "si", $new_status, $order_id);
    mysqli_stmt_execute($updateStmt);

    // 3. Update payment status and amount paid if collected (for COD)
    if ($new_status === 'Fulfilled') {
        $payUpdate = "UPDATE payments p 
                      JOIN orders o ON p.order_id = o.order_id 
                      SET p.payment_status = 'Paid', p.amount_paid = o.total_amount 
                      WHERE p.order_id = ? AND p.payment_status = 'Pending'";
        $payStmt = mysqli_prepare($conn, $payUpdate);
        mysqli_stmt_bind_param($payStmt, "i", $order_id);
        mysqli_stmt_execute($payStmt);
    }

    // 4. Log the status change
    $logSql = "INSERT INTO order_status_logs (order_id, old_status, new_status, actor_type, actor_id) 
               VALUES (?, ?, ?, 'Admin', ?)";
    $logStmt = mysqli_prepare($conn, $logSql);
    mysqli_stmt_bind_param($logStmt, "issi", $order_id, $old_status, $new_status, $admin_id);
    mysqli_stmt_execute($logStmt);

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => "Order status updated to $new_status"]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
