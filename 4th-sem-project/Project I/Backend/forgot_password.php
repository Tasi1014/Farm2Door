<?php
// PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in customer_registration
    $sql = "SELECT id, firstName FROM customer_registration WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $userId = $row['id'];
        $userName = $row['firstName'];

        // Generate secure token
        $token = bin2hex(random_bytes(32));

        // Update DB with token and expiry (using DB NOW() to avoid timezone mismatch)
        $updateSql = "UPDATE customer_registration SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        
        if ($updateStmt) {
            mysqli_stmt_bind_param($updateStmt, "si", $token, $userId);

            if (mysqli_stmt_execute($updateStmt)) {
                // Send Email
                $mail = new PHPMailer(true);
                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'tashisherpa10010@gmail.com';
                    $mail->Password   = 'cminnsnqbatexstg';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door Support');
                    $mail->addAddress($email, $userName);

                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request - Farm2Door';

                    $resetLink = "http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Frontend/Login/reset_password.html?token=" . $token;

                    $mail->Body = '
                    <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
                      <div style="max-width:600px;margin:auto;background:#fff;border-radius:6px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                        <div style="background:#2e7d32;color:#fff;padding:20px;text-align:center;">
                          <h2 style="margin:0;">🌱 Password Reset</h2>
                        </div>
                        <div style="padding:30px;color:#333;line-height:1.6;">
                          <p>Hello <strong>'.$userName.'</strong>,</p>
                          <p>We received a request to reset your password for your Farm2Door account. Click the button below to set a new password:</p>
                          
                          <div style="text-align:center;margin:30px 0;">
                            <a href="'.$resetLink.'" style="background:#2e7d32;color:#fff;padding:12px 25px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-block;">Reset Password</a>
                          </div>

                          <p>This link will expire in <strong>15 minutes</strong>. If you did not request this, you can safely ignore this email.</p>
                          
                          <hr style="border:0;border-top:1px solid #eee;margin:20px 0;">
                          <p style="font-size:12px;color:#888;">If the button above doesn\'t work, copy and paste this URL into your browser:</p>
                          <p style="font-size:12px;color:#888;word-break:break-all;">'.$resetLink.'</p>
                        </div>
                        <div style="background:#f9f9f9;padding:15px;text-align:center;font-size:12px;color:#666;">
                          © '.date("Y").' Farm2Door. Freshness at your doorstep.
                        </div>
                      </div>
                    </div>';

                    $mail->send();
                } catch (Exception $e) {
                    // Fail silently or log error
                }
            }
            mysqli_stmt_close($updateStmt);
        }
    }

    // Always send success to prevent email enumeration
    echo json_encode(['status' => 'success', 'message' => 'If an account exists with that email, a reset link has been sent. Please check your inbox.']);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
