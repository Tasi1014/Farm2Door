<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'stats' => [
        'total_revenue' => 0,
        'total_orders' => 0,
        'total_products' => 0,
        'total_farmers' => 0,
        'total_consumers' => 0
    ],
    'message' => ''
];

try {
    // Basic Admin Check (Assuming some admin session flag exists)
    // If not, we still fetch stats but normally admin pages are protected.
    
    // 1. Total Revenue & Orders
    // Revenue logic: 
    // - Paid orders (ONLINE or COD fulfilled): 100% of order total
    // - Refunded orders: 10% service charge from the total_amount (Admin keeps this portion)
    $orderSql = "
        SELECT 
            SUM(CASE 
                WHEN p.payment_status = 'Paid' THEN o.total_amount 
                WHEN p.payment_status = 'Refunded' THEN o.total_amount * 0.10
                ELSE 0 
            END) as revenue, 
            COUNT(o.order_id) as orders 
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
    ";
    $orderRes = mysqli_query($conn, $orderSql);
    $orderData = mysqli_fetch_assoc($orderRes);
    $response['stats']['total_revenue'] = (float)($orderData['revenue'] ?? 0);
    $response['stats']['total_orders'] = (int)($orderData['orders'] ?? 0);

    // 2. Total Products
    $prodSql = "SELECT COUNT(*) as total FROM products";
    $prodRes = mysqli_query($conn, $prodSql);
    $prodData = mysqli_fetch_assoc($prodRes);
    $response['stats']['total_products'] = $prodData['total'] ?? 0;

    // 3. Total Farmers
    $farmerSql = "SELECT COUNT(*) as total FROM farmer_registration";
    $farmerRes = mysqli_query($conn, $farmerSql);
    $farmerData = mysqli_fetch_assoc($farmerRes);
    $response['stats']['total_farmers'] = $farmerData['total'] ?? 0;

    // 4. Total Consumers
    $consumerSql = "SELECT COUNT(*) as total FROM customer_registration";
    $consumerRes = mysqli_query($conn, $consumerSql);
    $consumerData = mysqli_fetch_assoc($consumerRes);
    $response['stats']['total_consumers'] = $consumerData['total'] ?? 0;

    $response['success'] = true;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>
