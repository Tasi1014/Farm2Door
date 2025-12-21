<?php
include 'connection.php';

// PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize inputs
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into DB
    $sql = "INSERT INTO contactus (Name, Email, Message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

    if (mysqli_stmt_execute($stmt)) {

        // JSON response to frontend
        $response = json_encode([
            'status' => 'success',
            'message' => "Thank you $name for contacting us. We will reach you soon."
        ]);

        ignore_user_abort(true);
        ob_start();
        echo $response;
        $size = ob_get_length();

        header("Content-Encoding: none");
        header("Content-Length: {$size}");
        header("Connection: close");

        ob_end_flush();
        @ob_flush();
        flush();

        /* ================= ADMIN EMAIL ================= */

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tashisherpa10010@gmail.com';
            $mail->Password   = 'cminnsnqbatexstg';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door Contact');
            $mail->addAddress('sherpajack3@gmail.com', 'Admin');

            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';

            $mail->Body = '
            <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
              <div style="max-width:600px;margin:auto;background:#fff;border-radius:6px;">
                <div style="background:#2e7d32;color:#fff;padding:15px;">
                  <h2 style="margin:0;">📩 New Contact Message</h2>
                </div>
                <div style="padding:20px;color:#333;">
                  <p><strong>Name:</strong> '.$name.'</p>
                  <p><strong>Email:</strong> '.$email.'</p>
                  <p><strong>Message:</strong></p>
                  <div style="background:#f1f1f1;padding:12px;border-radius:4px;">
                    '.nl2br($message).'
                  </div>
                </div>
                <div style="background:#eee;padding:10px;text-align:center;font-size:12px;color:#666;">
                  Farm2Door • Admin Notification
                </div>
              </div>
            </div>';

            $mail->send();
        } catch (Exception $e) {
            error_log("Admin Mail Error: {$mail->ErrorInfo}");
        }

        /* ================= USER CONFIRMATION EMAIL ================= */

        $confirmationMail = new PHPMailer(true);
        try {
            $confirmationMail->isSMTP();
            $confirmationMail->Host       = 'smtp.gmail.com';
            $confirmationMail->SMTPAuth   = true;
            $confirmationMail->Username   = 'tashisherpa10010@gmail.com';
            $confirmationMail->Password   = 'cminnsnqbatexstg';
            $confirmationMail->SMTPSecure = 'tls';
            $confirmationMail->Port       = 587;

            $confirmationMail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door');
            $confirmationMail->addAddress($email, $name);

            $confirmationMail->isHTML(true);
            $confirmationMail->Subject = 'Thanks for contacting Farm2Door';

            $confirmationMail->Body = '
            <div style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
              <div style="max-width:600px;margin:auto;background:#fff;border-radius:6px;">
                <div style="background:#43a047;color:#fff;padding:20px;text-align:center;">
                  <h2 style="margin:0;">🌱 Thank You for Contacting Farm2Door</h2>
                </div>
                <div style="padding:20px;color:#333;">
                  <p>Hi <strong>'.$name.'</strong>,</p>
                  <p>We have received your message and our team will get back to you shortly.</p>

                  <p style="background:#f1f8e9;padding:12px;border-left:4px solid #66bb6a;">
                    <strong>Your Message:</strong><br>
                    '.nl2br($message).'
                  </p>

                  <p>Thank you for trusting Farm2Door.</p>
                  <p>
                    Regards,<br>
                    <strong>Farm2Door Team</strong>
                  </p>
                </div>
                <div style="background:#eee;padding:10px;text-align:center;font-size:12px;color:#666;">
                  © '.date("Y").' Farm2Door. All rights reserved.
                </div>
              </div>
            </div>';

            $confirmationMail->send();
        } catch (Exception $e) {
            error_log("User Mail Error: {$confirmationMail->ErrorInfo}");
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
