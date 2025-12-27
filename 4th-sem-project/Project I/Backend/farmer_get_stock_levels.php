<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'names' => [], 'stocks' => [], 'message' => ''];

if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// Fetch products and calculate dynamic threshold
// Threshold = (Last 30 Days Sales / 30) * 7. Default to 5.
$sql = "SELECT p.name, p.stock_quantity, p.threshold
        FROM products p
        WHERE p.farmer_id = ?
        ORDER BY p.stock_quantity ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $farmer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$response = ['success' => false, 'names' => [], 'stocks' => [], 'thresholds' => [], 'message' => ''];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['names'][]      = $row['name'];
        $response['stocks'][]     = (float)$row['stock_quantity'];
        $response['thresholds'][] = (int)$row['threshold'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
