<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    // 1. Check Auth (Consumer only ?)
    // Actually, farmers can also buy? Let's check session generally.
    if (!isset($_SESSION['consumer_id']) && !isset($_SESSION['farmer_id'])) {
        throw new Exception("Please login to place an order.");
    }

    $customer_id = $_SESSION['consumer_id'] ?? $_SESSION['farmer_id'];
    
    // 2. Get Input
    $input = json_decode(file_get_contents('php://input'), true);
    $full_name = trim($input['full_name'] ?? '');
    $phone = trim($input['phone'] ?? ''); // Crucial
    $address = trim($input['address'] ?? '');
    $notes = trim($input['notes'] ?? '');
    $payment_method = $input['payment_method'] ?? 'COD';

    if (empty($full_name) || empty($phone) || empty($address)) {
        throw new Exception("Missing required delivery details.");
    }

    // 3. Get Cart Items
    $cartSql = "SELECT c.cart_id, c.product_id, c.quantity, p.price, p.farmer_id, p.stock_quantity 
                FROM cart c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE c.customer_id = ?";
    $stmt = mysqli_prepare($conn, $cartSql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $cartResult = mysqli_stmt_get_result($stmt);
    
    $cartItems = [];
    $total_amount = 0;

    while ($row = mysqli_fetch_assoc($cartResult)) {
        // Stock Check
        if ($row['quantity'] > $row['stock_quantity']) {
            throw new Exception("Product ID " . $row['product_id'] . " has insufficient stock.");
        }
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total_amount += $row['subtotal'];
        $cartItems[] = $row;
    }

    if (empty($cartItems)) {
        throw new Exception("Your cart is empty.");
    }

    // Start Transaction
    mysqli_begin_transaction($conn);

    // 4. Create Order
    // We append phone to address for easy viewing by farmer, as per plan.
    $final_shipping_address = $address . " (Phone: " . $phone . ")";
    if (!empty($notes)) {
        $final_shipping_address .= " [Note: " . $notes . "]";
    }

    $orderSql = "INSERT INTO orders (customer_id, total_amount, order_status, shipping_address) VALUES (?, ?, 'Pending', ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "ids", $customer_id, $total_amount, $final_shipping_address);
    if (!mysqli_stmt_execute($orderStmt)) {
        throw new Exception("Failed to create order.");
    }
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($orderStmt);

    // 5. Create Order Items & Update Stock
    $itemSql = "INSERT INTO order_items (order_id, product_id, farmer_id, quantity, price_per_unit, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $itemStmt = mysqli_prepare($conn, $itemSql);

    $updateStockSql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stockStmt = mysqli_prepare($conn, $updateStockSql);

    foreach ($cartItems as $item) {
        // Insert Item
        mysqli_stmt_bind_param($itemStmt, "iiiidd", $order_id, $item['product_id'], $item['farmer_id'], $item['quantity'], $item['price'], $item['subtotal']);
        if (!mysqli_stmt_execute($itemStmt)) {
            throw new Exception("Failed to add order items.");
        }

        // Decrease Stock
        mysqli_stmt_bind_param($stockStmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($stockStmt);
    }
    mysqli_stmt_close($itemStmt);
    mysqli_stmt_close($stockStmt);

    // 6. Create Payment Record (Pending)
    $paySql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid) VALUES (?, ?, 'Pending', 0.00)";
    $payStmt = mysqli_prepare($conn, $paySql);
    mysqli_stmt_bind_param($payStmt, "is", $order_id, $payment_method);
    mysqli_stmt_execute($payStmt);
    mysqli_stmt_close($payStmt);

    // 7. Clear Cart
    $clearCartSql = "DELETE FROM cart WHERE customer_id = ?";
    $clearStmt = mysqli_prepare($conn, $clearCartSql);
    mysqli_stmt_bind_param($clearStmt, "i", $customer_id);
    mysqli_stmt_execute($clearStmt);
    mysqli_stmt_close($clearStmt);

    // Commit
    mysqli_commit($conn);
    
    $response['success'] = true;
    $response['message'] = "Order placed successfully!";
    $response['order_id'] = $order_id;

} catch (Exception $e) {
    mysqli_rollback($conn);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>
