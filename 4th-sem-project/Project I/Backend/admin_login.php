<?php
session_start();

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

//Credentials
$admin_email = "mahtoneha255@gmail.com";
$admin_pass = "RockingSiblings@";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['admin-email']) ? trim($_POST['admin-email']) : '';
    $password = isset($_POST['admin-password']) ? trim($_POST['admin-password']) : '';

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
        // Check Credentials
        if ($email === $admin_email && $password === $admin_pass) {
            // Login Success
            $response['success'] = true;
            $response['message'] = "Logged In successfully";
        } else {
            $response['errors']['result'] = "Invalid email or password!";
        }
    }
}

echo json_encode($response);
?>