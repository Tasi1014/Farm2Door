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

$response = [
    'success' => false,
    'items' => [],
    'total_price' => 0,
    'total_items' => 0,
    'message' => ''
];

try {
    if (!isset($_SESSION['customer_id'])) {
        $response['message'] = 'Not logged in';
        $response['not_logged_in'] = true;
        echo json_encode($response);
        exit;
    }

    $customer_id = $_SESSION['customer_id'];

    if (!isset($conn)) {
         throw new Exception("Database connection failed");
    }

    // Get Cart Items joined with Products
    $sql = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.customer_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $items = [];
    $total_price = 0;
    $total_items = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total_price += $row['subtotal'];
        $total_items++; // Count distinct items (rows), not quantity
        $items[] = $row;
    }

    $response['success'] = true;
    $response['items'] = $items;
    $response['total_price'] = $total_price;
    $response['total_items'] = $total_items;

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = "Server Error: " . $e->getMessage();
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
