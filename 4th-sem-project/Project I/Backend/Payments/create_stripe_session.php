<?php
session_start();
header('Content-Type: application/json');

require 'stripe_config.php';

try {
    if (!isset($_SESSION['pending_order'])) {
        throw new Exception("No pending order found.");
    }

    $order = $_SESSION['pending_order'];
    $uuid = $order['transaction_uuid'];
    
    // Set Stripe API Key
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Prepare Line Items for Stripe
    $line_items = [];
    foreach ($order['cart_items'] as $item) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'npr',
                'product_data' => [
                    'name' => $item['name'],
                    // 'images' => ["http://yourdomain.com/Images/products/" . $item['image']], // Optional: Add absolute URL if live
                ],
                'unit_amount' => $item['price'] * 100, // Amount in paisa (cents)
            ],
            'quantity' => $item['quantity'],
        ];
    }

    // Create Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Backend/Payments/stripe_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Frontend/Cart/checkout.html?error=Payment Cancelled',
        'client_reference_id' => $uuid,
        'metadata' => [
            'transaction_uuid' => $uuid,
            'customer_id' => $order['customer_id']
        ]
    ]);

    echo json_encode([
        'success' => true,
        'url' => $checkout_session->url
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
