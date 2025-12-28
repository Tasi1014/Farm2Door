<?php
session_start();
require "../connection.php";
require "stripe_config.php";

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    header("Location: ../../Frontend/Cart/checkout.html?error=No session ID returned");
    exit;
}

try {
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($checkout_session->payment_status !== 'paid') {
        header("Location: ../../Frontend/Cart/checkout.html?error=Payment not completed");
        exit;
    }

    $transaction_id = $checkout_session->payment_intent;
    $uuid = $checkout_session->metadata->transaction_uuid;
    $amount_paid_cents = $checkout_session->amount_total;
    $amount_paid = $amount_paid_cents / 100;

    // Retrieve pending order from session
    if (!isset($_SESSION['pending_order'])) {
        header("Location: ../../Frontend/Cart/checkout.html?error=Order session expired.");
        exit;
    }

    $pending = $_SESSION['pending_order'];

    // Verify UUID match
    if ($pending['transaction_uuid'] !== $uuid) {
        header("Location: ../../Frontend/Cart/checkout.html?error=Transaction Mismatch.");
        exit;
    }

    // --- DB INSERTION LOGIC (Mirrors esewa_success.php) ---
    mysqli_begin_transaction($conn);

    // 1. Insert into orders
    $orderSql = "INSERT INTO orders 
        (customer_id, total_amount, order_status, shipping_name, shipping_phone, shipping_address, shipping_notes)
        VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "idssss", $pending['customer_id'], $pending['total_amount'], $pending['full_name'], $pending['phone'], $pending['address'], $pending['notes']);
    mysqli_stmt_execute($orderStmt);
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
    // NOTE: Using 'Stripe' as method, and storing Payment Intent ID as transaction_id
    $paySql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid, transaction_id) VALUES (?, 'ONLINE', 'Paid', ?, ?)";
    $payStmt = mysqli_prepare($conn, $paySql);
    mysqli_stmt_bind_param($payStmt, "ids", $order_id, $amount_paid, $transaction_id);
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
    error_log("Stripe Success Error: " . $e->getMessage());
    header("Location: ../../Frontend/Cart/checkout.html?error=System Error during order finalization");
}

mysqli_close($conn);
?>
