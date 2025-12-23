<?php

session_set_cookie_params(0, '/');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';
header('Content-Type: application/json');
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = isset($_POST['consumer-email']) ? trim($_POST['consumer-email']) : '';
        $password = isset($_POST['consumer-password']) ? trim($_POST['consumer-password']) : '';
        $remember = isset($_POST['c-chk']);
        
        // Validation
        if (empty($email)) {
            $response['errors']['email'] = "Email cannot be empty!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['errors']['email'] = "Invalid email format!";
        }

        if (empty($password)) {
            $response['errors']['password'] = "Password cannot be empty!";
        }

        if (empty($response['errors'])) {
            $sql = "SELECT * FROM `customer_registration` WHERE Email = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) === 1) {
                    $user = mysqli_fetch_assoc($result);
                    
                    if ($user['status'] === 'blocked') {
                        $response['errors']['result'] = "Your account has been blocked. Please contact Farm2Door support Team.";
                    } elseif (password_verify($password, $user['Password'])) {
                        $_SESSION['consumer_email'] = $user['Email'];
                        $_SESSION['customer_id'] = $user['id']; // Fixed column name based on user feedback
                        
                        // Handle Remember Me (Cookies)
                        if ($remember) {
                            $cookie_options = [
                                'expires' => time() + (86400 * 30),
                                'path' => '/', // Global path
                                'secure' => false, // Localhost isn't https
                                'httponly' => false, // JS needs to read it for autofill
                                'samesite' => 'Lax'
                            ];
                            setcookie('c_email', $email, $cookie_options['expires'], $cookie_options['path'], '', $cookie_options['secure'], $cookie_options['httponly']);
                            setcookie('c_pass', $password, $cookie_options['expires'], $cookie_options['path'], '', $cookie_options['secure'], $cookie_options['httponly']);
                        } else {
                             // Clear cookies
                            if (isset($_COOKIE['c_email'])) {
                                setcookie('c_email', '', time() - 3600, '/');
                            }
                            if (isset($_COOKIE['c_pass'])) {
                                setcookie('c_pass', '', time() - 3600, '/');
                            }
                        }

                        $response['success'] = true;
                        $response['message'] = "Logged In successfully";
                    } else {
                        $response['errors']['result'] = "Invalid email or password!";
                    }
                } else {
                    $response['errors']['result'] = "Invalid email or password!";
                }
                mysqli_stmt_close($stmt);
            } else {
                $response['errors']['result'] = "Database prep error";
            }
        }
    }
} catch (Exception $e) {
    $response['errors']['result'] = "Server Error: " . $e->getMessage();
}

$json = json_encode($response);
if ($json === false) {
    // JSON encode failed
    echo json_encode(['success' => false, 'message' => 'JSON Encode Error']);
} else {
    echo $json;
}

mysqli_close($conn);
