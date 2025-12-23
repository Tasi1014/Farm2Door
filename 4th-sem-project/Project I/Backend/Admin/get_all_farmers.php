<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    // Pagination parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Count total farmers
    $countRes = mysqli_query($conn, "SELECT COUNT(*) as total FROM farmer_registration");
    $total = mysqli_fetch_assoc($countRes)['total'];

    // Fetch paginated farmers
    $sql = "SELECT farmer_id as id, firstName, lastName, email, phone, address FROM farmer_registration ORDER BY farmer_id LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    
    $farmers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $farmers[] = $row;
    }
    
    echo json_encode(['success' => true, 'farmers' => $farmers, 'total' => (int)$total]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
