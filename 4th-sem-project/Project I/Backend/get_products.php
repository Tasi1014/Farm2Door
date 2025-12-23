<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'products' => [],
    'message' => ''
];

// Check Auth
if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// Default pagination values
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;   // products per page
$page  = isset($_GET['page'])  ? (int)$_GET['page']  : 1;   // current page

if ($limit <= 0) $limit = 5;
if ($page  <= 0) $page  = 1;

$offset = ($page - 1) * $limit;

// First, count total products for this farmer
$countSql = "SELECT COUNT(*) AS total FROM `products` WHERE farmer_id = ?";
$countStmt = mysqli_prepare($conn, $countSql);

if ($countStmt) {
    mysqli_stmt_bind_param($countStmt, "i", $farmer_id);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $totalProducts);
    mysqli_stmt_fetch($countStmt);
    
    if (!$totalProducts) {
        $totalProducts = 0;
    }
    
    mysqli_stmt_close($countStmt);
} else {
    $response['message'] = "Database error: " . mysqli_error($conn);
    echo json_encode($response);
    exit;
}

// Now fetch only the current page
$sql = "SELECT * FROM `products` 
        WHERE farmer_id = ? 
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iii", $farmer_id, $limit, $offset);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    $response['success'] = true;
    $response['products'] = $products;
    $response['total'] = $totalProducts;     // total products
    $response['page']  = $page;              // current page
    $response['limit'] = $limit;             // items per page
    mysqli_stmt_close($stmt);
} else {
    $response['message'] = "Database error: " . mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
