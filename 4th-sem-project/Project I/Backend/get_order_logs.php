<?php
session_start();
require "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Missing Order ID']);
    exit;
}

try {
    $sql = "SELECT log_id, old_status, new_status, actor_type, timestamp, rejection_reason 
            FROM order_status_logs 
            WHERE order_id = ? 
            ORDER BY timestamp ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) throw new Exception(mysqli_error($conn));
    
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $logs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }

    echo json_encode(['success' => true, 'logs' => $logs]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
