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
    // Query to get summary metrics for collection hub
    $query = "
        SELECT 
            SUM(oi.subtotal) as total_value,
            COUNT(DISTINCT p.name) as product_types,
            COUNT(DISTINCT CASE WHEN o.order_status = 'Received' THEN o.order_id END) as awaiting_packaging,
            COUNT(DISTINCT CASE WHEN o.order_status = 'Ready for Pickup' THEN o.order_id END) as ready_for_pickup,
            COUNT(DISTINCT CASE WHEN o.order_status = 'Dispatched' THEN o.order_id END) as dispatched_qty
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.order_status IN ('Dispatched', 'Received', 'Ready for Pickup')
    ";

    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($conn));
    }

    $summary = mysqli_fetch_assoc($result);

    echo json_encode([
        'success' => true,
        'summary' => [
            'total_value' => (float)($summary['total_value'] ?? 0),
            'product_types' => (int)($summary['product_types'] ?? 0),
            'awaiting_packaging' => (int)($summary['awaiting_packaging'] ?? 0),
            'ready_for_pickup' => (int)($summary['ready_for_pickup'] ?? 0),
            'dispatched_qty' => (int)($summary['dispatched_qty'] ?? 0)
        ]
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
