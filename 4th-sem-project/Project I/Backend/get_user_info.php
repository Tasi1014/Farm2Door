<?php
session_set_cookie_params(0, '/');
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'name' => ''
];

if (isset($_SESSION['consumer_email'])) {
    $email = $_SESSION['consumer_email'];
    
    // Query to get user name
    $sql = "SELECT firstName, lastName FROM `customer_registration` WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $response['loggedIn'] = true;
            $response['name'] = $row['firstName'] . ' ' . $row['lastName'];
        }
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
