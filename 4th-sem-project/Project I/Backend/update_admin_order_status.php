<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

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


    // 1. Update order status
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


    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => "Order status updated to $new_status"]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
