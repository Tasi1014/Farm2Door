<?php
include 'connection.php';
include 'validation.php';

header('Content-Type: application/json');

// Main Processing
$response = [
    'success' => false,
    'errors' => [],
    'message' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $createTbl = "CREATE TABLE IF NOT EXISTS customer_registration (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50),
    Email VARCHAR(100) NOT NULL UNIQUE,
    Phone VARCHAR(15),
    Address VARCHAR(255),
    Password VARCHAR(255) NOT NULL, 
    Terms TINYINT(1) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
     $tbl = mysqli_query($conn, $createTbl);
     if(!$tbl){
        die("Table creation failed: " . mysqli_connect_error());
     }

    // Get and sanitize inputs
    $firstName = trim($_POST['first-name'] ?? '');
    $lastName = trim($_POST['last-name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm-password'] ?? '');
    $terms = isset($_POST['terms']) ? $_POST['terms'] : '';

    // Validate all fields
    $isFormValid = true;
    $errors = [];

    $isFormValid = validateName($firstName, $errors, 'firstName') && $isFormValid;
    $isFormValid = validateName($lastName, $errors, 'lastName') && $isFormValid;
    $isFormValid = validateEmail($email, $errors) && $isFormValid;
    $isFormValid = validatePhone($phone, $errors) && $isFormValid;
    $isFormValid = validateAddress($address, $errors) && $isFormValid;
    $isFormValid = validatePassword($password, $errors) && $isFormValid;

    // Validate confirm password
    if (trim($confirmPassword) === "") {
        $errors['confirmPassword'] = "Please confirm your password";
        $isFormValid = false;
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
        $isFormValid = false;
    }

    $isFormValid = validateTerms($terms, $errors) && $isFormValid;

    // Check if email already exists
    if ($isFormValid) {
        $checkEmailSql = "SELECT * FROM `customer_registration` WHERE Email = ?";
        $checkStmt = mysqli_prepare($conn, $checkEmailSql);
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $errors['email'] = "This email is already registered!";
            $isFormValid = false;
        }
        mysqli_stmt_close($checkStmt);
    }

    // If all validations pass, insert into database
    if ($isFormValid) {

        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Convert terms to boolean (1 for true, 0 for false)
        $termsAccepted = 1;

        // Prepare INSERT query
        $sql = "INSERT INTO `customer_registration` 
                (firstName, lastName, Email, Phone, Address, Password, Terms) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind parameters (s = string, i = integer)
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssi",
                $firstName,
                $lastName,
                $email,
                $phone,
                $address,
                $hashedPassword,
                $termsAccepted
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Registration successful! Welcome to Farm2Door!";
            } else {
                $errors['database'] = "Registration failed: " . mysqli_error($conn);
                $response['success'] = false;
            }

            mysqli_stmt_close($stmt);
        } else {
            $errors['database'] = "Database error: " . mysqli_error($conn);
            $response['success'] = false;
        }
    } else {
        $response['success'] = false;
    }
    
    $response['errors'] = $errors;
}

echo json_encode($response);
mysqli_close($conn);
?>