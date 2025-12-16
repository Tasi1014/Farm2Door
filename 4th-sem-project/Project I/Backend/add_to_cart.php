<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// 1. Check Auth
if (!isset($_SESSION['customer_id'])) {
    $response['message'] = 'Please login to add items to cart';
    $response['not_logged_in'] = true; // Flag for frontend to show login modal
    echo json_encode($response);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// 2. Get Input
// Support both JSON and POST
$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? intval($input['product_id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);
$quantity = isset($input['quantity']) ? intval($input['quantity']) : (isset($_POST['quantity']) ? intval($_POST['quantity']) : 1);

if ($product_id <= 0) {
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit;
}

// 3. Logic: Check if exists
$checkSql = "SELECT cart_id, quantity FROM cart WHERE customer_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($stmt, "ii", $customer_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Update Quantity
    $row = mysqli_fetch_assoc($result);
    $new_quantity = $row['quantity'] + $quantity;
    
    $updateSql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "ii", $new_quantity, $row['cart_id']);
    
    if (mysqli_stmt_execute($updateStmt)) {
        $response['success'] = true;
        $response['message'] = 'Cart updated';
    } else {
        $response['message'] = 'Error updating cart';
    }
} else {
    // Insert New
    $insertSql = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($insertStmt, "iii", $customer_id, $product_id, $quantity);
    
    if (mysqli_stmt_execute($insertStmt)) {
        $response['success'] = true;
        $response['message'] = 'Item added to cart';
    } else {
        $response['message'] = 'Error adding to cart';
    }
}

// Return current cart count for UI update
$countSql = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = ?";
$countStmt = mysqli_prepare($conn, $countSql);
mysqli_stmt_bind_param($countStmt, "i", $customer_id);
mysqli_stmt_execute($countStmt);
$countRes = mysqli_stmt_get_result($countStmt);
$countRow = mysqli_fetch_assoc($countRes);
$response['cart_count'] = $countRow['total'] ?? 0;

echo json_encode($response);
mysqli_close($conn);
?>
