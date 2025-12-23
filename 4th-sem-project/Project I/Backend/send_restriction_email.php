<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendRestrictionEmail($conn, $user_id, $type) {
    try {
        // 1. Fetch User Details
        if ($type === 'farmers') {
            $sql = "SELECT firstName, lastName, Email FROM farmer_registration WHERE farmer_id = ?";
        } else {
            $sql = "SELECT firstName, lastName, Email FROM customer_registration WHERE id = ?";
        }

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$user) {
            error_log("Restriction Email Error: User #$user_id ($type) not found.");
            return false;
        }

        $userName = $user['firstName'] . ' ' . $user['lastName'];
        $userEmail = $user['Email'];

        // 2. Prepare Content based on Type
        $subject = 'Your Farm2Door Account Has Been Restricted';
        $headerColor = '#f44336'; // Red for restriction
        $title = 'Account Restricted';

        if ($type === 'farmers') {
            $messageBody = "
                <p>Dear <strong>$userName</strong>,</p>
                <p>Your account has been temporarily restricted due to suspicious activity.</p>
                
                <div style='background:#fdf2f2; padding:15px; border-radius:4px; margin:20px 0; border-left:4px solid #f44336;'>
                    <h3 style='margin-top:0; color:#d32f2f;'>What happens now:</h3>
                    <ul style='padding-left:20px; color:#333;'>
                        <li>You cannot log in to your account</li>
                        <li>Your products are hidden from the marketplace</li>
                        <li>Your existing orders will still be processed</li>
                    </ul>
                </div>

                <p><strong>Why:</strong> We detected activity that violates our seller policies.</p>
                <p style='margin-top:20px;'>If you believe this was an error, please contact us immediately.</p>
            ";
            $footerText = "Farm2Door Support Team<br>support@farm2door.com";
        } else {
            $messageBody = "
                <p>Dear <strong>$userName</strong>,</p>
                <p>Your account has been temporarily restricted due to activity that violates our Community Guidelines.</p>
                
                <div style='background:#fdf2f2; padding:15px; border-radius:4px; margin:20px 0; border-left:4px solid #f44336;'>
                    <h3 style='margin-top:0; color:#d32f2f;'>What happens now:</h3>
                    <ul style='padding-left:20px; color:#333;'>
                        <li>You cannot place new orders</li>
                        <li>Your existing orders will be fulfilled</li>
                        <li>You cannot access your account</li>
                    </ul>
                </div>

                <p><strong>Why:</strong> Suspicious activity or violation of terms.</p>
                <p style='color:#d32f2f; font-weight:bold; margin-top:20px;'>Do not try to create another account.</p>
            ";
            $footerText = "Farm2Door Customer Support<br>support@farm2door.com";
        }

        // 3. PHPMailer Setup
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tashisherpa10010@gmail.com'; 
        $mail->Password   = 'cminnsnqbatexstg';           
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door Support');
        $mail->addAddress($userEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->Body = '
        <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
          <div style="max-width:600px;margin:auto;background:#ffffff;border-radius:6px;overflow:hidden;border:1px solid #e0e0e0;">
            
            <div style="background:'.$headerColor.';color:#ffffff;padding:20px;text-align:center;">
              <h1 style="margin:0;font-size:24px;">'.$title.'</h1>
            </div>

            <div style="padding:20px;color:#333;line-height:1.6;">
              '.$messageBody.'

              <p style="margin-top:30px;">
                Best regards,<br>
                <strong>'.$footerText.'</strong>
              </p>
            </div>

            <div style="background:#eeeeee;padding:15px;text-align:center;font-size:12px;color:#666;">
              © '.date("Y").' Farm2Door. All rights reserved.<br>
              Ensuring a safe marketplace for everyone.
            </div>

          </div>
        </div>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Restriction Mail Error: " . $e->getMessage());
        return false;
    }
}
?>
