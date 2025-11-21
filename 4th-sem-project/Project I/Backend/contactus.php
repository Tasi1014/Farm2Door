<?php
include 'connection.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Insert data into database
    $sql = "INSERT INTO contactus (Name, Email, Message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        // Database insertion successful
        
        // 1. Prepare the success response
        $response = json_encode(['status' => 'success', 'message' => 'Thank you ' . $name . ' for contacting us. We will reach you soon']);

        // 2. Tell PHP to keep running even if user "disconnects" (which happens when we close connection)
        ignore_user_abort(true);
        
        // 3. Buffer the output
        ob_start();
        echo $response;
        $size = ob_get_length();
        
        // 4. Send headers to tell browser "We are done"
        header("Content-Encoding: none");
        header("Content-Length: {$size}");
        header("Connection: close");
        
        // 5. Flush the buffer to the browser
        ob_end_flush();
        @ob_flush();
        flush();
        
        // ---------------------------------------------------------
        // BROWSER HAS RECEIVED RESPONSE. SCRIPT CONTINUES IN BACKGROUND
        // ---------------------------------------------------------

        // Now send email to admin
        $mail = new PHPMailer(true);
        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tashisherpa10010@gmail.com';      // your Gmail for sending
            $mail->Password   = 'cminnsnqbatexstg';               // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            // Email recipients
            $mail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door Contact');
            $mail->addAddress('sherpajack3@gmail.com', 'Admin'); // admin inbox
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = '
                <h3>New Message Received</h3>
                <p><strong>Name:</strong> '.$name.'</p>
                <p><strong>Email:</strong> '.$email.'</p>
                <p><strong>Message:</strong><br>'.$message.'</p>
            ';

            $mail->send();

        } catch (Exception $e) {
            error_log("Admin mail error: {$mail->ErrorInfo}");
        }

        $confirmationMail = new PHPMailer(true);
        try {
            $confirmationMail->isSMTP();
            $confirmationMail->Host       = 'smtp.gmail.com';
            $confirmationMail->SMTPAuth   = true;
            $confirmationMail->Username   = 'tashisherpa10010@gmail.com'; // your sending Gmail
            $confirmationMail->Password   = 'cminnsnqbatexstg';           // App password
            $confirmationMail->SMTPSecure = 'tls';
            $confirmationMail->Port       = 587;

            // Sender and recipient
            $confirmationMail->setFrom('tashisherpa10010@gmail.com', 'Farm2Door Contact');
            $confirmationMail->addAddress($email, $name); // send to the user

            // Email content
            $confirmationMail->isHTML(true);
            $confirmationMail->Subject = 'Thanks for contacting Farm2Door';
            $confirmationMail->Body    = '
                <p>Hi ' . $name . ',</p>
                <p>Thank you for contacting us. We have received your message and will get back to you soon.</p>
                <p>— Farm2Door Team</p>
            ';

            $confirmationMail->send();
        } catch (Exception $e) {
            error_log("Confirmation mail error: {$confirmationMail->ErrorInfo}");
        }
        
    } else {
        // DB insertion failed
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
