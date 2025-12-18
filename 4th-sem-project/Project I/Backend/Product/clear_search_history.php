<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

$userId = $_SESSION['consumer_id'] ?? 0;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$sql = "DELETE FROM search_history WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error clearing history']);
}

mysqli_close($conn);
?>
