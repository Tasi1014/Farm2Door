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
    $category = isset($_POST['category']) ? trim($_POST['category']) : ''; // Optional if we allow category edit
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity = isset($_POST['stock_quantity']) ? trim($_POST['stock_quantity']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validation
    if (empty($product_id)) $response['errors']['product_id'] = "Product ID missing.";
    if (empty($name)) $response['errors']['name'] = "Product name is required.";
    if (empty($price) || !is_numeric($price)) $response['errors']['price'] = "Valid price is required.";
    if (empty($quantity) || !is_numeric($quantity)) $response['errors']['quantity'] = "Valid quantity is required.";
    
    // We are NOT handling image update in this iteration to keep logic simple as requested, 
    // but the form allows updating other fields.
    
    if (empty($response['errors'])) {
        // Update Database
        // Note: We also verify that the product belongs to the current farmer (AND farmer_id = ?)
        $sql = "UPDATE `products` SET name=?, category=?, price=?, stock_quantity=?, description=? WHERE product_id=? AND farmer_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssdssii", $name, $category, $price, $quantity, $description, $product_id, $farmer_id);
            
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) >= 0) { // >= 0 because sometimes nothing changes
                    $response['success'] = true;
                    $response['message'] = "Product updated successfully!";
                } else {
                     $response['message'] = "No changes made or product not found.";
                }
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
