<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? 0;
$type = $data['type'] ?? ''; // 'farmers' or 'consumers'

if (!$id || !in_array($type, ['farmers', 'consumers'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    if ($type === 'farmers') {
        $sql = "DELETE FROM farmer_registration WHERE farmer_id = ?";
    } else {
        $sql = "DELETE FROM customer_registration WHERE id = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>
