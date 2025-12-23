<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Allow both Farmers and Admins
$is_farmer = isset($_SESSION['farmer_id']);
$is_admin = isset($_SESSION['admin_id']);

if (!$is_farmer && !$is_admin) {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Handle both 'product_id' (from farmer JS) and 'id' (from admin JS)
    $product_id = $data['product_id'] ?? $data['id'] ?? '';

    if (!empty($product_id)) {
        if ($is_admin) {
            // Admin can delete any product
            $sql = "DELETE FROM `products` WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
        } else {
            // Farmer can only delete their own product
            $farmer_id = $_SESSION['farmer_id'];
            $sql = "DELETE FROM `products` WHERE product_id = ? AND farmer_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $product_id, $farmer_id);
        }
        
        if ($stmt) {
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