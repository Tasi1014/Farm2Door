<?php
// Prevent caching so the browser always checks with the server
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'userType' => null,
    'name' => ''
];

// Check for Farmer Login
if (isset($_SESSION['farmer_email'])) {
    $email = $_SESSION['farmer_email'];
    $sql = "SELECT firstName, lastName, Phone, Address FROM `farmer_registration` WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $response['loggedIn'] = true;
            $response['userType'] = 'farmer';
            $response['name'] = $row['firstName'] . ' ' . $row['lastName'];
            $response['phone'] = $row['Phone'];
            $response['address'] = $row['Address'];
        }
        mysqli_stmt_close($stmt);
    }
} 
// Check for Consumer Login
elseif (isset($_SESSION['consumer_email'])) {
    $email = $_SESSION['consumer_email'];
    $sql = "SELECT firstName, lastName, Phone, Address FROM `customer_registration` WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $response['loggedIn'] = true;
            $response['userType'] = 'consumer';
            $response['name'] = $row['firstName'] . ' ' . $row['lastName'];
            $response['phone'] = $row['Phone'];
            $response['address'] = $row['Address'];
        }
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
