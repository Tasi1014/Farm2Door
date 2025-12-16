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
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customer_registration`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "<h1>Cart table created successfully!</h1>";
    echo "<p>You can now go back and try adding items to the cart.</p>";
} else {
    echo "<h1>Error creating table</h1>";
    echo "<p>" . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>
