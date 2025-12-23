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
$input = json_decode(file_get_contents('php://input'), true);

$order_id = $input['order_id'] ?? null;
$new_status = $input['status'] ?? null;
$reason = $input['reason'] ?? null; // For rejection

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

    // 2. Update order status and rejection reason if applicable
    $updateSql = "UPDATE orders SET order_status = ?, rejection_reason = ? WHERE order_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "ssi", $new_status, $reason, $order_id);
    mysqli_stmt_execute($updateStmt);

    // 3. Log the status change
    $logSql = "INSERT INTO order_status_logs (order_id, old_status, new_status, actor_type, actor_id, rejection_reason) 
               VALUES (?, ?, ?, 'Farmer', ?, ?)";
    $logStmt = mysqli_prepare($conn, $logSql);
    mysqli_stmt_bind_param($logStmt, "issis", $order_id, $old_status, $new_status, $farmer_id, $reason);
    mysqli_stmt_execute($logStmt);

    mysqli_commit($conn);

    $response = ['success' => true, 'message' => "Order status updated to $new_status"];

    if ($new_status === 'Processing') {
        include 'send_order_confirmation.php';

        // Background processing to return response quickly to farmer
        ignore_user_abort(true);
        ob_start();
        echo json_encode($response);
        $size = ob_get_length();

        header("Content-Encoding: none");
        header("Content-Length: {$size}");
        header("Connection: close");

        ob_end_flush();
        @ob_flush();
        flush();

        // Send the email in background
        sendOrderConfirmationEmail($conn, $order_id);
        exit;
    } else {
        echo json_encode($response);
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
