<?php
include 'connection.php';

// Create Cart Table
$sql = "CREATE TABLE IF NOT EXISTS `cart` (
    `cart_id` INT(11) NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cart_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE
    -- We assume customer_registration has a primary key. We will add FK constraint later if needed to avoid errors if table name differs
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "Cart table created successfully (or already exists).";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
