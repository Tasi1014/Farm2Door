<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    // Pagination parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Count total consumers
    $countRes = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer_registration");
    $total = mysqli_fetch_assoc($countRes)['total'];

    // Fetch paginated consumers
    $sql = "SELECT id, firstName, lastName, email, phone, address, status FROM customer_registration ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    
    $consumers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $consumers[] = $row;
    }
    
    echo json_encode(['success' => true, 'consumers' => $consumers, 'total' => (int)$total]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
