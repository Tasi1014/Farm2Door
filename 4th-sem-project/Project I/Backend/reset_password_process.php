<?php
header('Content-Type: application/json');

include 'connection.php';

include 'validation.php'; // For password validation rules

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validate inputs
    if (empty($token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing token.']);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    $errors = [];
    if (!validatePassword($password, $errors)) {
        echo json_encode(['status' => 'error', 'message' => $errors['password']]);
        exit;
    }

    // 2. Verify token in DB (Check existence first to handle cleanup)
    $sqlSearch = "SELECT id, reset_expires FROM customer_registration WHERE reset_token = ? LIMIT 1";
    $stmtSearch = mysqli_prepare($conn, $sqlSearch);
    mysqli_stmt_bind_param($stmtSearch, "s", $token);
    mysqli_stmt_execute($stmtSearch);
    $resultSearch = mysqli_stmt_get_result($stmtSearch);

    if ($row = mysqli_fetch_assoc($resultSearch)) {
        $userId = $row['id'];
        $expiryTime = strtotime($row['reset_expires']);
        
        // Use DB time for comparison to be safe
        $timeRes = mysqli_query($conn, "SELECT NOW() as now");
        $timeRow = mysqli_fetch_assoc($timeRes);
        $currentTime = strtotime($timeRow['now']);

        if ($currentTime > $expiryTime) {
            // Token found but EXPIRED - Clean it up!
            $clearSql = "UPDATE customer_registration SET reset_token = NULL, reset_expires = NULL WHERE id = ?";
            $clearStmt = mysqli_prepare($conn, $clearSql);
            mysqli_stmt_bind_param($clearStmt, "i", $userId);
            mysqli_stmt_execute($clearStmt);
            
            echo json_encode(['status' => 'error', 'message' => 'This reset link has expired and has been cleared. Please request a new one.']);
            exit;
        }

        // Token is VALID
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 3. Update password and clear token
        $updateSql = "UPDATE customer_registration SET Password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $userId);

        if (mysqli_stmt_execute($updateStmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Your password has been updated successfully. You can now login with your new password.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password. Please try again.']);
        }
        mysqli_stmt_close($updateStmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid reset link. Please request a new one.']);
    }

    mysqli_stmt_close($stmtSearch);
    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
