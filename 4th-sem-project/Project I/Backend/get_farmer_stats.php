<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'stats' => [
        'total_products' => 0,
        'total_earnings' => 0,
        'total_orders' => 0,
        'low_stock' => 0
    ],
    'message' => ''
];

try {
    if (!isset($_SESSION['farmer_id'])) {
        throw new Exception("Unauthorized");
    }

    $farmer_id = $_SESSION['farmer_id'];

    // 1. Total Products & Low Stock
    // We can do this in one query using conditional aggregation
    $prodSql = "SELECT 
                    COUNT(*) as total, 
                    SUM(CASE WHEN stock_quantity < 5 THEN 1 ELSE 0 END) as low_stock 
                FROM products 
                WHERE farmer_id = ?";
    
    $stmt = mysqli_prepare($conn, $prodSql);
    mysqli_stmt_bind_param($stmt, "i", $farmer_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $prodData = mysqli_fetch_assoc($res);
    
    $response['stats']['total_products'] = $prodData['total'] ?? 0;
    $response['stats']['low_stock'] = $prodData['low_stock'] ?? 0;
    
    mysqli_stmt_close($stmt);

    // 2. Total Earnings & Orders (from order_items)
    // We need to check if table exists first to avoid crash if they didn't run setup yet?
    // Assuming they DID run setup as per previous steps.
    
    $orderSql = "SELECT 
                    COUNT(DISTINCT oi.order_id) as total_orders, 
                    SUM(oi.subtotal) as total_earnings 
                 FROM order_items oi
                 JOIN orders o ON oi.order_id = o.order_id
                 JOIN payments p ON o.order_id = p.order_id
                 WHERE oi.farmer_id = ? 
                   AND p.payment_status = 'Paid'
                   AND o.order_status IN ('Fulfilled', 'Delivered')";
                 
    $stmt2 = mysqli_prepare($conn, $orderSql);
    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, "i", $farmer_id);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);
        $orderData = mysqli_fetch_assoc($res2);
        
        $response['stats']['total_orders'] = $orderData['total_orders'] ?? 0;
        $response['stats']['total_earnings'] = $orderData['total_earnings'] ?? 0;
        
        mysqli_stmt_close($stmt2);
    } else {
        // Table might not exist or empty
        $response['msg_debug'] = "Order table query failed (might be empty)";
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>
