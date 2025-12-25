<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'names' => [], 'stocks' => [], 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);
// Even if we don't use the dates for snapshot stock, we handle the JSON to avoid errors
$start = $input['start_date'] ?? null;
$end   = $input['end_date'] ?? null;

$sql = "SELECT name, stock_quantity
        FROM products
        ORDER BY stock_quantity ASC
        LIMIT 15";  // Showing top 15 low stock for report

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['names'][]  = $row['name'];
        $response['stocks'][] = (int)$row['stock_quantity'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
