<?php
include 'connection.php';

header('Content-Type: application/json');

// Check for specific Product ID (for Details Page)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// By default, fetch all. We can add limits if needed (e.g. ?limit=6 for home page)
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

// Join with farmer_registration to get the farmer's name (LEFT JOIN to show products even if farmer missing)
$sql = "SELECT p.*, f.firstName, f.lastName 
        FROM products p 
        LEFT JOIN farmer_registration f ON p.farmer_id = f.farmer_id";

// Filter
if ($id > 0) {
    $sql .= " WHERE p.product_id = $id";
} else {
    $sql .= " ORDER BY p.created_at DESC";
}

if ($limit > 0 && $id == 0) {
    $sql .= " LIMIT $limit";
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
        echo json_encode(['success' => true, 'products' => $products]);
    }

} else {    
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
