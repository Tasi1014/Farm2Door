<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';
header('Content-Type: application/json');
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['farmer-email']) ? trim($_POST['farmer-email']) : '';
    $password = isset($_POST['farmer-password']) ? trim($_POST['farmer-password']) : '';
    $remember = isset($_POST['f-chk']);
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
        // Check database
        $sql = "SELECT * FROM `farmer_registration` WHERE Email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['Password'])) {
                    // Login Success
                    $_SESSION['farmer_email'] = $user['Email'];
                    $_SESSION['farmer_id'] = $user['farmer_id'];

                    // Handle Remember Me (Cookies)
                    if ($remember) {
                        // Set cookies for 30 days
                        $cookie_options = [
                            'expires' => time() + (86400 * 30),
                            'path' => '/',
                            'secure' => false, // Set to true if using HTTPS
                            'httponly' => false, // False so JS can potentially access if needed, but better true for security.
                            'samesite' => 'Lax'
                        ];
                        setcookie('f_email', $email, $cookie_options);
                        setcookie('f_pass', $password, $cookie_options);
                    } else {
                        // Clear cookies
                        $cookie_options = [
                            'expires' => time() - 3600,
                            'path' => '/',
                            'samesite' => 'Lax'
                        ];
                        if (isset($_COOKIE['f_email'])) {
                            setcookie('f_email', '', $cookie_options);
                        }
                        if (isset($_COOKIE['f_pass'])) {
                            setcookie('f_pass', '', $cookie_options);
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
