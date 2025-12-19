<?php
session_start();
require "../connection.php";

// eSewa sends a GET request to this URL on success with 'data' parameter (base64 encoded JSON)
$data = $_GET['data'] ?? null;

if (!$data) {
    header("Location: ../../Frontend/Cart/checkout.html?error=No response from eSewa");
    exit;
}

// Decode eSewa response
$decoded_data = json_decode(base64_decode($data), true);

if (!$decoded_data || $decoded_data['status'] !== 'COMPLETE') {
    header("Location: ../../Frontend/Cart/checkout.html?error=Payment failed or incomplete");
    exit;
}

$transaction_uuid = $decoded_data['transaction_uuid'];
$total_amount = $decoded_data['total_amount'];
$transaction_code = $decoded_data['transaction_code'];

// New Flow: Retrieve pending order from session
if (!isset($_SESSION['pending_order'])) {
    header("Location: ../../Frontend/Cart/checkout.html?error=Order session expired or missing.");
    exit;
}

$pending = $_SESSION['pending_order'];

// Verify UUID match
if ($pending['transaction_uuid'] !== $transaction_uuid) {
    header("Location: ../../Frontend/Cart/checkout.html?error=Transaction ID mismatch.");
    exit;
}

try {
    mysqli_begin_transaction($conn);

    // 1. Insert into orders
    $orderSql = "INSERT INTO orders 
        (customer_id, total_amount, order_status, shipping_name, shipping_phone, shipping_address, shipping_notes)
        VALUES (?, ?, 'Processing', ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "idssss", $pending['customer_id'], $pending['total_amount'], $pending['full_name'], $pending['phone'], $pending['address'], $pending['notes']);
    if (!mysqli_stmt_execute($orderStmt)) throw new Exception("Order creation failed.");
    $order_id = mysqli_insert_id($conn);

    // 2. Insert into order_items and update stock
    $itemSql = "INSERT INTO order_items (order_id, product_id, farmer_id, quantity, price_per_unit, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
    $itemStmt = mysqli_prepare($conn, $itemSql);
    $stockSql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stockStmt = mysqli_prepare($conn, $stockSql);

    foreach ($pending['cart_items'] as $item) {
        mysqli_stmt_bind_param($itemStmt, "iiiidd", $order_id, $item['product_id'], $item['farmer_id'], $item['quantity'], $item['price'], $item['subtotal']);
        mysqli_stmt_execute($itemStmt);

        mysqli_stmt_bind_param($stockStmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($stockStmt);
    }

    // 3. Insert into payments
    $paySql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid, transaction_id) VALUES (?, 'ONLINE', 'Paid', ?, ?)";
    $payStmt = mysqli_prepare($conn, $paySql);
    mysqli_stmt_bind_param($payStmt, "ids", $order_id, $total_amount, $transaction_code);
    mysqli_stmt_execute($payStmt);

    // 4. Clear Cart
    $clearSql = "DELETE FROM cart WHERE customer_id = ?";
    $clearStmt = mysqli_prepare($conn, $clearSql);
    mysqli_stmt_bind_param($clearStmt, "i", $pending['customer_id']);
    mysqli_stmt_execute($clearStmt);

    mysqli_commit($conn);

    // Success! Clear session and redirect
    unset($_SESSION['pending_order']);
    header("Location: ../../Frontend/Cart/order_success.html");
    exit;

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    error_log("Payment Success Error: " . $e->getMessage());
    header("Location: ../../Frontend/Cart/checkout.html?error=Order completion failed. Please contact us.");
}

mysqli_close($conn);
?>
