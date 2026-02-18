<?php
session_set_cookie_params(0, '/');
session_start();
include '../connection.php';

header('Content-Type: application/json');

// Verify admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Query to get current hub stock by product - Aggregated by Product Name
    // Stock includes orders with status: Dispatched, Received, Ready for Pickup
    $query = "
        SELECT 
            MIN(p.product_id) as product_id,
            p.name as product_name,
            p.category,
            SUM(oi.quantity) as total_quantity,
            COUNT(DISTINCT o.order_id) as order_count,
            SUM(CASE WHEN o.order_status = 'Dispatched' THEN oi.quantity ELSE 0 END) as dispatched_qty,
            SUM(CASE WHEN o.order_status = 'Received' THEN oi.quantity ELSE 0 END) as received_qty,
            SUM(CASE WHEN o.order_status = 'Ready for Pickup' THEN oi.quantity ELSE 0 END) as ready_qty,
            MAX(o.order_date) as last_updated
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.order_status IN ('Dispatched', 'Received', 'Ready for Pickup')
        GROUP BY p.name, p.category
        ORDER BY p.category, p.name
    ";

    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($conn));
    }

    $inventory = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'category' => $row['category'],
            'total_quantity' => (int)$row['total_quantity'],
            'order_count' => (int)$row['order_count'],
            'dispatched_qty' => (int)$row['dispatched_qty'],
            'received_qty' => (int)$row['received_qty'],
            'ready_qty' => (int)$row['ready_qty'],
            'last_updated' => $row['last_updated']
        ];
    }

    echo json_encode([
        'success' => true,
        'inventory' => $inventory,
        'count' => count($inventory)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
