<?php
include 'connection.php';

header('Content-Type: application/json');

// Check for specific Product ID (for Details Page)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// By default, fetch all. We can add limits if needed (e.g. ?limit=6 for home page)
// Pagination parameters
$limitParam = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$pageParam = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offsetParam = ($pageParam - 1) * $limitParam;

// Join with farmer_registration to get the farmer's name (LEFT JOIN to show products even if farmer missing)
// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Base query for counting
$countSql = "SELECT COUNT(*) as total FROM products p";
if (!empty($search) || !empty($category)) {
    $countSql .= " WHERE ";
    $countClauses = [];
    if (!empty($search)) $countClauses[] = "p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
    if (!empty($category)) $countClauses[] = "p.category = '" . mysqli_real_escape_string($conn, $category) . "'";
    $countSql .= implode(" AND ", $countClauses);
}
$countRes = mysqli_query($conn, $countSql);
$total = mysqli_fetch_assoc($countRes)['total'];

// Base query for fetching
$sql = "SELECT p.*, f.firstName, f.lastName 
        FROM products p 
        LEFT JOIN farmer_registration f ON p.farmer_id = f.farmer_id";

// Build WHERE clause
$where_clauses = [];
if ($id > 0) {
    $where_clauses[] = "p.product_id = " . intval($id);
}
if (!empty($search)) {
    // Escape search term for security
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "p.name LIKE '%$escaped_search%'";
}
if (!empty($category)) {
    $escaped_cat = mysqli_real_escape_string($conn, $category);
    $where_clauses[] = "p.category = '$escaped_cat'";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Order and Limit
$sql .= " ORDER BY p.created_at DESC";

if ($id == 0) {
    $sql .= " LIMIT $limitParam OFFSET $offsetParam";
}

$result = mysqli_query($conn, $sql);

$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    // If ID was requested, return just that object (or error if not found)
    if ($id > 0) {
        if (!empty($products)) {
            echo json_encode(['success' => true, 'product' => $products[0]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    } else {
        echo json_encode(['success' => true, 'products' => $products, 'total' => (int)$total]);
    }

} else {    
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
