<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$userId = $_SESSION['consumer_id'] ?? 0;
// We only save history for logged in customers (or farmers buying)
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$query = trim($data['query'] ?? '');

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Empty query']);
    exit;
}

// 1. Check if this exact query already exists for this user (to keep it clean)
$checkSql = "SELECT id FROM search_history WHERE user_id = ? AND search_query = ?";
$stmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($stmt, "is", $userId, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Update timestamp
    $updateSql = "UPDATE search_history SET created_at = CURRENT_TIMESTAMP WHERE user_id = ? AND search_query = ?";
    $stmt2 = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($stmt2, "is", $userId, $query);
    mysqli_stmt_execute($stmt2);
} else {
    // Insert new
    $insertSql = "INSERT INTO search_history (user_id, search_query) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($stmt2, "is", $userId, $query);
    mysqli_stmt_execute($stmt2);
}

// Limit history to 5 items per user (keep it recent like Daraz)
$deleteOldSql = "DELETE FROM search_history 
                 WHERE user_id = ? 
                 AND id NOT IN (
                    SELECT id FROM (
                        SELECT id FROM search_history 
                        WHERE user_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ) AS recent
                 )";
$stmt3 = mysqli_prepare($conn, $deleteOldSql);
mysqli_stmt_bind_param($stmt3, "ii", $userId, $userId);
mysqli_stmt_execute($stmt3);

echo json_encode(['success' => true]);
mysqli_close($conn);
?>
