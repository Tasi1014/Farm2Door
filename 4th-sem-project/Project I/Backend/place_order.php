<?php
session_start();
require "connection.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

function fetchCartItems($conn, $customer_id) {
    $cartSql = "SELECT c.product_id, c.quantity, p.price, p.farmer_id, p.stock_quantity, p.name
                FROM cart c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.customer_id = ?";
    $stmt = mysqli_prepare($conn, $cartSql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $cartItems = [];
    $total_amount = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['quantity'] > $row['stock_quantity']) {
            throw new Exception("Insufficient stock for {$row['name']}");
        }
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total_amount += $row['subtotal'];
        $cartItems[] = $row;
    }

    if (empty($cartItems)) {
        throw new Exception("Cart is empty.");
    }

    return [$cartItems, $total_amount];
}

function insertOrder($conn, $customer_id, $total_amount, $full_name, $phone, $address, $notes) {
    $orderSql = "INSERT INTO orders 
        (customer_id, total_amount, order_status, shipping_name, shipping_phone, shipping_address, shipping_notes)
        VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($stmt, "idssss", $customer_id, $total_amount, $full_name, $phone, $address, $notes);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create order: " . mysqli_stmt_error($stmt));
    }
    return mysqli_insert_id($conn);
}

function insertPayment($conn, $order_id, $payment_method) {
    $status = ($payment_method === 'COD') ? 'Pending' : 'Initiated';
    $paySql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid)
               VALUES (?, ?, ?, 0.00)";
    $stmt = mysqli_prepare($conn, $paySql);
    mysqli_stmt_bind_param($stmt, "iss", $order_id, $payment_method, $status);
    mysqli_stmt_execute($stmt);
}

function insertOrderItemsAndUpdateStock($conn, $order_id, $cartItems) {
    $itemSql = "INSERT INTO order_items 
                (order_id, product_id, farmer_id, quantity, price_per_unit, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)";
    $itemStmt = mysqli_prepare($conn, $itemSql);

    $stockSql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stockStmt = mysqli_prepare($conn, $stockSql);

    foreach ($cartItems as $item) {
        mysqli_stmt_bind_param(
            $itemStmt,
            "iiiidd",
            $order_id,
            $item['product_id'],
            $item['farmer_id'],
            $item['quantity'],
            $item['price'],
            $item['subtotal']
        );
        mysqli_stmt_execute($itemStmt);

        mysqli_stmt_bind_param($stockStmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($stockStmt);
    }
}

function clearCart($conn, $customer_id) {
    $clearSql = "DELETE FROM cart WHERE customer_id = ?";
    $stmt = mysqli_prepare($conn, $clearSql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
}

try {
    if (!isset($_SESSION['customer_id'])) {
        throw new Exception("Please login to place an order.");
    }
    $customer_id = $_SESSION['customer_id'];

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception("Invalid request data.");

    $full_name = trim($input['full_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');
    $notes = trim($input['notes'] ?? '');
    $payment_method = $input['payment_method'] ?? 'COD';

    if (!$full_name || !$phone || !$address) throw new Exception("Missing delivery details.");

    list($cartItems, $total_amount) = fetchCartItems($conn, $customer_id);

    if ($payment_method === 'COD') {
        mysqli_begin_transaction($conn);
        try {
            $order_id = insertOrder($conn, $customer_id, $total_amount, $full_name, $phone, $address, $notes);
            insertPayment($conn, $order_id, $payment_method);
            insertOrderItemsAndUpdateStock($conn, $order_id, $cartItems);
            clearCart($conn, $customer_id);
            mysqli_commit($conn);

            $response['success'] = true;
            $response['message'] = "Order placed with Cash on Delivery.";
            $response['order_id'] = $order_id;
        } catch (Exception $e) {
            if (isset($conn)) mysqli_rollback($conn);
            throw $e;
        }
    } else {
        // For ONLINE payment: Store in session for later insertion
        // Generate a temporary transaction_uuid to link the payment
        $temp_uuid = date('YmdHis') . "-" . $customer_id . "-" . bin2hex(random_bytes(4));

        $_SESSION['pending_order'] = [
            'customer_id' => $customer_id,
            'total_amount' => $total_amount,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'notes' => $notes,
            'cart_items' => $cartItems,
            'payment_method' => $payment_method,
            'transaction_uuid' => $temp_uuid,
            'created_at' => time()
        ];

        $response['success'] = true;
        $response['message'] = "Order data prepared. Proceed to payment.";
        $response['payment_required'] = true;
        $response['payment_method'] = 'ONLINE';
        $response['transaction_uuid'] = $temp_uuid;
    }

} catch (Exception $e) {
    if (isset($conn) && $payment_method === 'COD') mysqli_rollback($conn);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>
