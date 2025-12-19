<?php
session_start();
require "connection.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    if (!isset($_SESSION['customer_id'])) {
        throw new Exception("Please login to place an order.");
    }

    $customer_id = $_SESSION['customer_id'];
    
    // Get Input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception("Invalid request data.");
    }

    $full_name = trim($input['full_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');
    $notes = trim($input['notes'] ?? '');
    $payment_method = $input['payment_method'] ?? 'COD';

    if (empty($full_name) || empty($phone) || empty($address)) {
        throw new Exception("Missing required delivery details.");
    }

    // 1. Get Cart Items and check stock
    $cartSql = "SELECT c.cart_id, c.product_id, c.quantity, p.price, p.farmer_id, p.stock_quantity, p.name as product_name
                FROM cart c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE c.customer_id = ?";
    $stmt = mysqli_prepare($conn, $cartSql);
    if (!$stmt) throw new Exception("Prepare failed: " . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $cartResult = mysqli_stmt_get_result($stmt);
    
    $cartItems = [];
    $total_amount = 0;

    while ($row = mysqli_fetch_assoc($cartResult)) {
        if ($row['quantity'] > $row['stock_quantity']) {
            throw new Exception("Stock alert: Only " . $row['stock_quantity'] . " units of '" . $row['product_name'] . "' left.");
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

    // 2. Create Order
    // Schema: order_id, customer_id, total_amount, order_status, shipping_address
    $shipping_address = "Name: $full_name\nPhone: $phone\nAddress: $address";
    if (!empty($notes)) {
        $shipping_address .= "\nNotes: $notes";
    }

    $orderSql = "INSERT INTO orders (customer_id, total_amount, order_status, shipping_address) VALUES (?, ?, 'Pending', ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    if (!$orderStmt) throw new Exception("Order prepare failed: " . mysqli_error($conn));
    
    mysqli_stmt_bind_param($orderStmt, "ids", $customer_id, $total_amount, $shipping_address);
    if (!mysqli_stmt_execute($orderStmt)) {
        throw new Exception("Failed to create order: " . mysqli_stmt_error($orderStmt));
    }
    
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($orderStmt);

    // 3. Create Order Items & Update Stock
    // Schema: item_id, order_id, product_id, farmer_id, quantity, price_per_unit, subtotal
    $itemSql = "INSERT INTO order_items (order_id, product_id, farmer_id, quantity, price_per_unit, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $itemStmt = mysqli_prepare($conn, $itemSql);
    if (!$itemStmt) throw new Exception("Item prepare failed: " . mysqli_error($conn));

    $updateStockSql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stockStmt = mysqli_prepare($conn, $updateStockSql);
    if (!$stockStmt) throw new Exception("Stock prepare failed: " . mysqli_error($conn));

    foreach ($cartItems as $item) {
        mysqli_stmt_bind_param($itemStmt, "iiiidd", $order_id, $item['product_id'], $item['farmer_id'], $item['quantity'], $item['price'], $item['subtotal']);
        if (!mysqli_stmt_execute($itemStmt)) {
            throw new Exception("Failed to add order items: " . mysqli_stmt_error($itemStmt));
        }

        mysqli_stmt_bind_param($stockStmt, "ii", $item['quantity'], $item['product_id']);
        if (!mysqli_stmt_execute($stockStmt)) {
            throw new Exception("Failed to update stock: " . mysqli_stmt_error($stockStmt));
        }
    }
    mysqli_stmt_close($itemStmt);
    mysqli_stmt_close($stockStmt);

    // 4. Create Payment Record (Pending)
    // Schema: order_id, payment_method, payment_status, amount_paid
    $paySql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid) VALUES (?, ?, 'Pending', ?)";
    $payStmt = mysqli_prepare($conn, $paySql);
    if ($payStmt) {
        $zero = 0.00;
        mysqli_stmt_bind_param($payStmt, "isd", $order_id, $payment_method, $zero);
        mysqli_stmt_execute($payStmt);
        mysqli_stmt_close($payStmt);
    }

    // 5. Clear Cart
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
    if (isset($conn)) mysqli_rollback($conn);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>


