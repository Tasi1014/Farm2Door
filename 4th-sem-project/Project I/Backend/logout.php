<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Clear cookies
if (isset($_COOKIE['c_email'])) {
    setcookie('c_email', '', time() - 3600, '/');
}
if (isset($_COOKIE['c_pass'])) {
    setcookie('c_pass', '', time() - 3600, '/');
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
