<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Only admin can toggle status
if (!isset($_SESSION['admin_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$type = isset($data['type']) ? $data['type'] : ''; // 'farmers' or 'consumers'
$action = isset($data['action']) ? $data['action'] : ''; // 'block' or 'active'

if ($id <= 0 || !in_array($type, ['farmers', 'consumers']) || !in_array($action, ['active', 'blocked'])) {
    $response['message'] = 'Invalid request parameters';
    echo json_encode($response);
    exit;
}

$table = ($type === 'farmers') ? 'farmer_registration' : 'customer_registration';
$id_column = ($type === 'farmers') ? 'farmer_id' : 'id';

$sql = "UPDATE $table SET status = ? WHERE $id_column = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $action, $id);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $response['success'] = true;
        $response['message'] = 'User ' . ($action === 'blocked' ? 'blocked' : 'unblocked') . ' successfully';
        
        // Only send email if blocking
        if ($action === 'blocked') {
            include '../send_restriction_email.php';
            
            // Background processing: return response to admin immediately
            ignore_user_abort(true);
            ob_start();
            echo json_encode($response);
            $size = ob_get_length();
            header("Content-Length: {$size}");
            header("Connection: close");
            ob_end_flush();
            @ob_flush();
            flush();
            
            // Script continues after response is sent
            sendRestrictionEmail($conn, $id, $type);
            exit;
        }
    } else {
        $response['message'] = 'No changes made or user not found';
    }
    
    mysqli_stmt_close($stmt);
} else {
    $response['message'] = 'Database error: ' . mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>
