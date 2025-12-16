<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart_id = isset($input['cart_id']) ? intval($input['cart_id']) : 0;

if ($cart_id > 0) {
    // Verify ownership
    $sql = "DELETE FROM cart WHERE cart_id = ? AND customer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $_SESSION['customer_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
}
mysqli_close($conn);
?>
