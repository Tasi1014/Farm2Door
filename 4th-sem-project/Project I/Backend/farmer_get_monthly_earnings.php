<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'dates' => [], 'amounts' => [], 'message' => ''];

if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];


// Handle date filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Only completed orders whose payments are marked as Paid should be counted.
$sql = "SELECT DATE(o.order_date) AS day,
               SUM(oi.subtotal) AS earnings
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN payments p ON p.order_id = o.order_id
        WHERE oi.farmer_id = ?
          AND o.order_status IN ('Fulfilled')
          AND p.payment_status = 'Paid'
          AND DATE(o.order_date) BETWEEN ? AND ?
        GROUP BY DATE(o.order_date)
        ORDER BY day";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $farmer_id, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['dates'][]   = $row['day'];
        $response['amounts'][] = (float)$row['earnings'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
