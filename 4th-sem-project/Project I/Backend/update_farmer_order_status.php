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
    //Verify Ownership
    // Check if at least one item in this order belongs to the logged-in farmer
    $checkSql = "SELECT 1 FROM order_items WHERE order_id = ? AND farmer_id = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ii", $order_id, $farmer_id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) === 0) {
        echo json_encode(['success' => false, 'message' => 'Access denied: This order does not belong to you.']);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    mysqli_begin_transaction($conn);


    // 2. Update order status and rejection reason if applicable
    $updateSql = "UPDATE orders SET order_status = ?, rejection_reason = ? WHERE order_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "ssi", $new_status, $reason, $order_id);
    mysqli_stmt_execute($updateStmt);

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
