<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Standardize Session
session_set_cookie_params(0, '/');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buffer Output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

include 'connection.php';

// Clear buffer
ob_clean();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // 1. Check Auth
    if (!isset($_SESSION['customer_id'])) {
        $response['message'] = 'Please login to add items to cart';
        $response['not_logged_in'] = true; // Flag for frontend
        echo json_encode($response);
        exit;
    }

    $customer_id = $_SESSION['customer_id'];

    // 2. Get Input
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($input['product_id']) ? intval($input['product_id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : (isset($_POST['quantity']) ? intval($_POST['quantity']) : 1);

    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }

    // 3. Logic: Check if exists
    // Prepare statements to prevent SQL Injection & errors
    if (!isset($conn)) {
        throw new Exception("Database connection failed");
    }

    $checkSql = "SELECT cart_id, quantity FROM cart WHERE customer_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }
    
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
            throw new Exception("Error updating cart");
        }
        mysqli_stmt_close($updateStmt);

    } else {
        // Insert New
        $insertSql = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
         if (!$insertStmt) {
            throw new Exception("Prepare insert failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($insertStmt, "iii", $customer_id, $product_id, $quantity);
        
        if (mysqli_stmt_execute($insertStmt)) {
            $response['success'] = true;
            $response['message'] = 'Item added to cart';
        } else {
            throw new Exception("Error adding to cart");
        }
        mysqli_stmt_close($insertStmt);
    }
    mysqli_stmt_close($stmt);

    // Return current cart count for UI update (Distinct Items)
    $countSql = "SELECT COUNT(*) as total FROM cart WHERE customer_id = ?";
    $countStmt = mysqli_prepare($conn, $countSql);
    mysqli_stmt_bind_param($countStmt, "i", $customer_id);
    mysqli_stmt_execute($countStmt);
    $countRes = mysqli_stmt_get_result($countStmt);
    $countRow = mysqli_fetch_assoc($countRes);
    $response['cart_count'] = $countRow['total'] ?? 0;
    
    // DEBUG INFO
    $response['debug_received_qty'] = $quantity;
    $response['debug_input_raw'] = $input;
    
    mysqli_stmt_close($countStmt);

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Server Error: ' . $e->getMessage();
    // Log error cleanly if needed
}

// Clean Output & Send JSON
$json = json_encode($response);
if ($json === false) {
    echo json_encode(['success' => false, 'message' => 'JSON Encode Error']);
} else {
    echo $json;
}

if (isset($conn)) mysqli_close($conn);
?>
