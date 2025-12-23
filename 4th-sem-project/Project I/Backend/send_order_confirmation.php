<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendOrderConfirmationEmail($conn, $order_id) {
    try {
        // 1. Fetch Order & Customer Details
        $sql = "SELECT o.order_id, o.total_amount, o.shipping_name, o.shipping_address, o.shipping_phone, p.payment_method, c.Email as customer_email 
                FROM orders o 
                JOIN customer_registration c ON o.customer_id = c.id 
                LEFT JOIN payments p ON o.order_id = p.order_id
                WHERE o.order_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$order) {
            error_log("Order Confirmation Error: Order #$order_id not found.");
            return false;
        }

        // 2. Fetch Order Items
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

        // SMTP Config (Reusing credentials from send_pickup_notification.php)
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
        $mail->Subject = 'Order Confirmed - #' . $order_id . ' | Farm2Door';

        $mail->Body = '
        <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
          <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:6px;overflow:hidden;border:1px solid #e0e0e0;">
            
            <div style="background:#4caf50;color:#ffffff;padding:20px;text-align:center;">
              <h1 style="margin:0;font-size:24px;">Order Confirmed!</h1>
              <p style="margin:5px 0 0;">Thank you for shopping with Farm2Door</p>
            </div>

            <div style="padding:20px;color:#333;">
              <p>Hi <strong>'.$order['shipping_name'].'</strong>,</p>
              <p>Your order has been successfully placed. We are now processing it for you.</p>

              <div style="background:#f9f9f9;padding:15px;border-radius:4px;margin:20px 0;">
                <h3 style="margin-top:0; color:#4caf50;">Order Summary</h3>
                <p><strong>Order ID:</strong> #'.$order_id.'</p>
                <p><strong>Payment Method:</strong> '.$order['payment_method'].'</p>
                
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

              <div style="margin:20px 0;">
                <h3 style="color:#4caf50;">Shipping Address</h3>
                <p style="margin:0;">'.$order['shipping_address'].'</p>
                <p style="margin:5px 0 0;">Phone: '.$order['shipping_phone'].'</p>
              </div>

              <p style="margin-top:30px;">
                We will notify you once your order is ready for pickup or out for delivery.
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
        return true;
    } catch (Exception $e) {
        error_log("Order Confirmation Mail Error: " . $e->getMessage());
        return false;
    }
}
?>
