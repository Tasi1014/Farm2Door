<?php
session_set_cookie_params(0, '/');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_SESSION['customer_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to continue.']);
        exit;
    }

    $customer_id = $_SESSION['customer_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    $cart_id = isset($input['cart_id']) ? intval($input['cart_id']) : 0;
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : 0;

    if ($cart_id <= 0) {
        throw new Exception('Invalid cart ID');
    }

    if ($quantity <= 0) {
        // Delete item if quantity is 0 or less
        $sql = "DELETE FROM cart WHERE cart_id = ? AND customer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $customer_id);
    } else {
        // Update quantity
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND customer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $customer_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Get updated total price
        $totalSql = "SELECT SUM(p.price * c.quantity) as total 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.product_id 
                     WHERE c.customer_id = ?";
        $totalStmt = mysqli_prepare($conn, $totalSql);
        mysqli_stmt_bind_param($totalStmt, "i", $customer_id);
        mysqli_stmt_execute($totalStmt);
        $totalResult = mysqli_stmt_get_result($totalStmt);
        $totalRow = mysqli_fetch_assoc($totalResult);
        
        $response['success'] = true;
        $response['message'] = 'Cart updated';
        $response['total_price'] = $totalRow['total'] ?? 0;
    } else {
        throw new Exception('Failed to update cart');
    }

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
if (isset($conn)) mysqli_close($conn);
?>
