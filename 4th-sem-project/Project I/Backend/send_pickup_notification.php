<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Missing Order ID']);
    exit;
}

try {
    // 1. Fetch Order & Customer Details (and check status)
    $sql = "SELECT o.order_id, o.shipping_name, o.order_status, o.total_amount, c.Email as customer_email 
            FROM orders o 
            JOIN customer_registration c ON o.customer_id = c.id 
            WHERE o.order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$order) {
        throw new Exception("Order not found");
    }

    if ($order['order_status'] !== 'Ready for Pickup') {
        throw new Exception("Email can only be sent for orders 'Ready for Pickup'. Current status: " . $order['order_status']);
    }

    // 2. Fetch Items for the email
    $itemsSql = "SELECT pr.name, oi.quantity, oi.price_per_unit, oi.subtotal 
                 FROM order_items oi 
                 JOIN products pr ON oi.product_id = pr.product_id 
                 WHERE oi.order_id = ?";
    $itemsStmt = mysqli_prepare($conn, $itemsSql);
    mysqli_stmt_bind_param($itemsStmt, "i", $order_id);
    mysqli_stmt_execute($itemsStmt);
    $itemsRes = mysqli_stmt_get_result($itemsStmt);
    
    $itemsHtml = '';
    while ($item = mysqli_fetch_assoc($itemsRes)) {
        $itemsHtml .= "<tr>
            <td style='padding:10px; border-bottom:1px solid #eee;'>{$item['name']} x {$item['quantity']}</td>
            <td style='padding:10px; border-bottom:1px solid #eee; text-align:right;'>Rs. ".number_format($item['subtotal'], 2)."</td>
        </tr>";
    }

    // PHPMailer Setup
    $mail = new PHPMailer(true);

    // SMTP Config
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tashisherpa10010@gmail.com';   // sender email
    $mail->Password   = 'cminnsnqbatexstg';            // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender & Receiver
    $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door');
    $mail->addAddress($order['customer_email'], $order['shipping_name']);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Order #' . $order_id . ' is Ready for Pickup';

    $mail->Body = '
    <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
      <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:6px;overflow:hidden;border:1px solid #e0e0e0;">
        
        <div style="background:#4caf50;color:#ffffff;padding:20px;text-align:center;">
          <h1 style="margin:0;font-size:24px;">📦 Ready for Pickup</h1>
          <p style="margin:5px 0 0;">Great news! Your order is waiting for you.</p>
        </div>

        <div style="padding:20px;color:#333;">
          <p>Hi <strong>'.$order['shipping_name'].'</strong>,</p>

          <p>
            Exciting news! Your order has been processed and is now ready for pickup at our collection center.
          </p>

          <div style="background:#f9f9f9;padding:15px;border-radius:4px;margin:20px 0;">
            <h3 style="margin-top:0; color:#4caf50;">Collection Details</h3>
            <p><strong>Order ID:</strong> #'.$order_id.'</p>
            <p><strong>Location:</strong> Farm2Door Central Collection Point</p>
            
            <table style="width:100%; border-collapse:collapse; margin-top:10px;">
              <thead>
                <tr style="background:#eee;">
                  <th style="padding:10px; text-align:left;">Item</th>
                  <th style="padding:10px; text-align:right;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                '.$itemsHtml.'
              </tbody>
              <tfoot>
                <tr>
                  <td style="padding:10px; font-weight:bold;">Total Amount</td>
                  <td style="padding:10px; font-weight:bold; text-align:right;">Rs. '.number_format($order['total_amount'], 2).'</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <p>
            Please visit the collection center during business hours and show your Order ID to collect your items.
          </p>

          <p style="margin-top:20px;">
            Thank you for choosing <strong>Farm2Door</strong>.
          </p>

          <p>
            Regards,<br>
            <strong>Farm2Door Team</strong>
          </p>
        </div>

        <div style="background:#eeeeee;padding:15px;text-align:center;font-size:12px;color:#666;">
          © '.date("Y").' Farm2Door. All rights reserved.<br>
          Bringing fresh farm products to your doorstep.
        </div>

      </div>
    </div>';

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Notification email sent successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mail error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>