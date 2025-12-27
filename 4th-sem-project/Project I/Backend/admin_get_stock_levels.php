<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

$response = ['success' => false, 'names' => [], 'stocks' => [], 'message' => ''];

$input = json_decode(file_get_contents('php://input'), true);
$start     = $input['start_date'] ?? null;
$end       = $input['end_date'] ?? null;
$farmer_id = $input['farmer_id'] ?? 'overall';

$params = [];
$types = "";
$where = " WHERE 1=1 ";

if ($farmer_id !== 'overall') {
    $where .= " AND p.farmer_id = ? ";
    $params[] = $farmer_id;
    $types .= "i";
}

$sql = "SELECT p.name, p.stock_quantity, CONCAT(f.firstName, ' ', f.lastName) as farmer_name, p.threshold
        FROM products p
        JOIN farmer_registration f ON p.farmer_id = f.farmer_id
        $where
        ORDER BY p.stock_quantity ASC
        LIMIT 20";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$response = ['success' => false, 'names' => [], 'stocks' => [], 'farmer_names' => [], 'thresholds' => [], 'message' => ''];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['names'][]        = $row['name'];
        $response['stocks'][]       = (float)$row['stock_quantity'];
        $response['farmer_names'][] = $row['farmer_name'];
        $response['thresholds'][]   = (int)$row['threshold'];
    }
    $response['success'] = true;
} else {
    $response['message'] = mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
