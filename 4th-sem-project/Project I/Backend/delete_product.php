<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Check Auth
if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $product_id = isset($data['product_id']) ? $data['product_id'] : '';

    if (!empty($product_id)) {
        $sql = "DELETE FROM `products` WHERE product_id = ? AND farmer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $product_id, $farmer_id);
            if (mysqli_stmt_execute($stmt)) {
                 if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $response['success'] = true;
                    $response['message'] = "Product deleted successfully.";
                 } else {
                    $response['message'] = "Product not found or access denied.";
                 }
            } else {
                $response['message'] = "Database error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $response['message'] = "Product ID required.";
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
