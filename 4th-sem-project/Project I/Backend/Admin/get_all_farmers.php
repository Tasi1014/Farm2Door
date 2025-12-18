<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT farmer_id as id, firstName, lastName, email, phone, address FROM farmer_registration ORDER BY farmer_id";
    $result = mysqli_query($conn, $sql);
    
    $farmers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $farmers[] = $row;
    }
    
    echo json_encode(['success' => true, 'farmers' => $farmers]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
