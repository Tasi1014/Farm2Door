<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

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
                if (password_verify($password, $user['Password'])) {
                    $_SESSION['consumer_email'] = $user['Email'];
                    
                    // Handle Remember Me (Cookies)
                    if ($remember) {
                        $cookie_options = [
                            'expires' => time() + (86400 * 30),
                            'path' => '/',
                            'secure' => false, 
                            'httponly' => false,
                            'samesite' => 'Lax'
                        ];
                        setcookie('c_email', $email, $cookie_options);
                        setcookie('c_pass', $password, $cookie_options);
                    } else {
                        $cookie_options = [
                            'expires' => time() - 3600,
                            'path' => '/',
                            'samesite' => 'Lax'
                        ];
                        if (isset($_COOKIE['c_email'])) {
                            setcookie('c_email', '', $cookie_options);
                        }
                        if (isset($_COOKIE['c_pass'])) {
                            setcookie('c_pass', '', $cookie_options);
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
            $response['errors']['result'] = "Database error: " . mysqli_error($conn);
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>