<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check Auth
if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get Input
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : ''; 
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
    $threshold = isset($_POST['lowStockThreshold']) ? trim($_POST['lowStockThreshold']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validation
    if (empty($product_id)) $response['errors']['product_id'] = "Product ID missing.";
    if (empty($name)) $response['errors']['name'] = "Product name is required.";
    if (empty($price) || !is_numeric($price) || $price < 0) $response['errors']['price'] = "Price must be a positive number.";
    if (!is_numeric($quantity) || $quantity < 0) $response['errors']['quantity'] = "Stock must be a non-negative number.";
    if (!is_numeric($threshold) || $threshold < 0) $response['errors']['threshold'] = "Threshold must be a non-negative number.";
    
    if (empty($response['errors'])) {
        // Update Database
        $sql = "UPDATE products SET name=?, category=?, price=?, stock_quantity=?, threshold=?, description=? 
                WHERE product_id=? AND farmer_id=?";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssdiisii", $name, $category, $price, $quantity, $threshold, $description, $product_id, $farmer_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Product updated successfully!";
            } else {
                $response['message'] = "Database error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
             $response['message'] = "Database error: " . mysqli_error($conn);
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
