<?php
include 'connection.php';

// Create Search History Table
// We'll use INT for user_id to match customer_registration(id)
$sql = "CREATE TABLE IF NOT EXISTS `search_history` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `search_query` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `customer_registration`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "<h1>Search History table created successfully!</h1>";
} else {
    echo "<h1>Error creating table: " . mysqli_error($conn) . "</h1>";
}

mysqli_close($conn);
?>
<br>
<a href="../Frontend/Home/index.html">Go Back to Home</a>
