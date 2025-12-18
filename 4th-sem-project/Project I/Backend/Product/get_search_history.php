<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

$userId = $_SESSION['consumer_id'] ?? 0;

if (!$userId) {
    echo json_encode(['success' => false, 'history' => []]);
    exit;
}

$sql = "SELECT search_query FROM search_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = $row['search_query'];
}

echo json_encode(['success' => true, 'history' => $history]);
mysqli_close($conn);
?>
