<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'products' => [],
    'message' => ''
];

// Check Auth
if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

$sql = "SELECT * FROM `products` WHERE farmer_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $farmer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    $response['success'] = true;
    $response['products'] = $products;
    mysqli_stmt_close($stmt);
} else {
    $response['message'] = "Database error: " . mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
