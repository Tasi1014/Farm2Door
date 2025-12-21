<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// 1. Check Auth (Farmer only)
if (!isset($_SESSION['farmer_id'])) {
    $response['message'] = 'Unauthorized. Please login as a farmer.';
    echo json_encode($response);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

// Ensure Table Exists
$createTableSql = "CREATE TABLE IF NOT EXISTS `products` (
    `product_id` INT(11) NOT NULL AUTO_INCREMENT,
    `farmer_id` INT(11) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock_quantity` INT(11) NOT NULL,
    `description` TEXT,
    `image` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`product_id`),
    FOREIGN KEY (`farmer_id`) REFERENCES `farmer_registration`(`farmer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!mysqli_query($conn, $createTableSql)) {
    $response['message'] = "Table creation failed: " . mysqli_error($conn);
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get Input
    $name = isset($_POST['productName']) ? trim($_POST['productName']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    //Validation
    if (empty($name)) $response['errors']['name'] = "Product name is required.";
    if (empty($price) || !is_numeric($price)) $response['errors']['price'] = "Valid price is required.";
    if (empty($quantity) || !is_numeric($quantity)) $response['errors']['quantity'] = "Valid quantity is required.";
    
    //Handle Image Upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Create unique filename to prevent overwrites
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            // Directory relative to this script
            // Script is in Backend/, Images are in Project I/Images/
            // So path is ../Images/products/
            $uploadFileDir = '../Images/products/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Store relative path for frontend use (e.g. "../Images/products/filename.jpg")
                // Or just store filename and prepend path in frontend. 
                // Let's store the filename for flexibility, or the relative path from root.
                // Storing filename 'products/filename.jpg' seems clean if we assume Images/ base.
                // Let's store just the filename as requested by user "with the name of that image in the database".
                $imagePath = $newFileName; 
            } else {
                $response['errors']['image'] = "Error moving uploaded file.";
            }
        } else {
            $response['errors']['image'] = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    } else {
         $response['errors']['image'] = "Product image is required.";
    }

    // Check if product with same name exists for this farmer
    if (empty($response['errors'])) {
        $checkSql = "SELECT product_id FROM products WHERE farmer_id = ? AND name = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "is", $farmer_id, $name);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $response['errors']['name'] = "You have already added a product with this name.";
        }
        mysqli_stmt_close($checkStmt);
    }

    if (empty($response['errors'])) {
        // Insert into Database
        $sql = "INSERT INTO `products` (farmer_id, name, category, price, stock_quantity, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issdiss", $farmer_id, $name, $category, $price, $quantity, $description, $imagePath);
            
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Product added successfully!";
            } else {
                $response['message'] = "Database error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = "Database error: " . mysqli_error($conn);
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
