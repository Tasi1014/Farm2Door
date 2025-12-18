<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, firstName, lastName, email, phone, address FROM customer_registration ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $consumers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $consumers[] = $row;
    }
    
    echo json_encode(['success' => true, 'consumers' => $consumers]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
