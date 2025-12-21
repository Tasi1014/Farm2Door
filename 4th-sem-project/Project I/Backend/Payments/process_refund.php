<?php
session_set_cookie_params(0, '/', 'localhost', false, true);
session_start();
include '../connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin login required.']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$input = json_decode(file_get_contents('php://input'), true);

$order_id = $input['order_id'] ?? null;
$payment_id = $input['payment_id'] ?? null;
$reason = $input['reason'] ?? 'Cancelled/Rejected';

// Generate a unique Refund Transaction ID
$refund_transaction_id = "REF-" . date('Ymd') . "-" . strtoupper(bin2hex(random_bytes(4)));

if (!$order_id || !$payment_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required refund data.']);
    exit;
}

try {
    mysqli_begin_transaction($conn);

    // 1. Verify eligibility (Online, Paid, Cancelled/Rejected)
    $stmt = mysqli_prepare($conn, "
        SELECT o.order_id, o.customer_id, o.total_amount, o.order_status, o.shipping_name,
               p.payment_id, p.payment_method, p.payment_status,
               c.Email as customer_email, c.firstName as customer_name
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        JOIN customer_registration c ON o.customer_id = c.id
        WHERE o.order_id = ? AND p.payment_id = ? FOR UPDATE
    ");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $payment_id);
    mysqli_stmt_execute($stmt);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$order) {
        throw new Exception("Order or payment not found.");
    }

    if ($order['payment_method'] !== 'ONLINE' || $order['payment_status'] !== 'Paid') {
        throw new Exception("Only paid online orders are eligible for refunds.");
    }

    if (!in_array($order['order_status'], ['Cancelled', 'Rejected'])) {
        throw new Exception("Refunds can only be processed for Cancelled or Rejected orders.");
    }

    // 2. Calculate Refund (90% of total)
    $original_amount = $order['total_amount'];
    $refund_amount = $original_amount * 0.90;

    // 3. Insert into refunds table
    $refundStmt = mysqli_prepare($conn, "
        INSERT INTO refunds (order_id, payment_id, refund_transaction_id, refund_amount, reason)
        VALUES (?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($refundStmt, "iisds", $order_id, $payment_id, $refund_transaction_id, $refund_amount, $reason);
    mysqli_stmt_execute($refundStmt);

    // 4. Update payment status
    $payUpdateStmt = mysqli_prepare($conn, "UPDATE payments SET payment_status = 'Refunded' WHERE payment_id = ?");
    mysqli_stmt_bind_param($payUpdateStmt, "i", $payment_id);
    mysqli_stmt_execute($payUpdateStmt);

    // 5. Log the action
    $logSql = "INSERT INTO order_status_logs (order_id, old_status, new_status, actor_type, actor_id, rejection_reason) 
               VALUES (?, ?, 'Refunded', 'Admin', ?, ?)";
    $logStmt = mysqli_prepare($conn, $logSql);
    $note = "Processed refund of Rs. $refund_amount (10% fee applied). Ref: $refund_transaction_id";
    mysqli_stmt_bind_param($logStmt, "isis", $order_id, $order['order_status'], $admin_id, $note);
    mysqli_stmt_execute($logStmt);

    // 6. Send Automated Email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tashisherpa10010@gmail.com';   // sender email
    $mail->Password   = 'cminnsnqbatexstg';            // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door');
    $mail->addAddress($order['customer_email'], $order['shipping_name']);

    $mail->isHTML(true);
    $mail->Subject = 'Refund Processed for Order #' . $order_id;
    
    // Use the specific template requested
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <p>Hi <b>" . $order['customer_name'] . "</b>,</p>
        <p>This is to inform you that your order (Order ID: <b>" . $order_id . "</b>) has been cancelled.</p>
        <p>The refund for this order has been successfully processed to your original payment method.</p>
        
        <div style='background: #f9f9f9; padding: 15px; border-left: 5px solid #4caf50; margin: 20px 0;'>
            <h4 style='margin-top: 0;'>Refund Details:</h4>
            <p><strong>Refund Amount:</strong> Rs. " . number_format($refund_amount, 2) . "</p>
            <p><strong>Refund Transaction ID:</strong> " . $refund_transaction_id . "</p>
            <p style='color: #d32f2f; font-size: 0.9em;'>* Please note that as per our service policy, 10% of the order amount has been deducted as a service and processing charge.</p>
        </div>

        <p>The refunded amount should reflect in your account within 3–5 working days, depending on your bank or payment provider.</p>
        <p>Thank you for your understanding and for choosing Farm2Door.</p>
        <p>Regards,<br><b>Farm2Door Team</b></p>
    </div>";

    $mail->send();

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => "Refund of Rs. " . number_format($refund_amount, 2) . " processed successfully."]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
