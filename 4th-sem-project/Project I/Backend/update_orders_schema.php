<?php
include 'connection.php';

$queries = [
    // 1. Update orders table status ENUM
    "ALTER TABLE orders MODIFY COLUMN order_status ENUM('Pending', 'Processing', 'Dispatched', 'Received', 'Ready for Pickup', 'Fulfilled', 'Cancelled', 'Rejected') DEFAULT 'Pending'",
    
    // 2. Add rejection_reason and cancellation_reason columns if not exist
    "ALTER TABLE orders ADD COLUMN IF NOT EXISTS rejection_reason TEXT AFTER shipping_notes",
    "ALTER TABLE orders ADD COLUMN IF NOT EXISTS cancellation_reason TEXT AFTER rejection_reason",

    // 3. Create order_status_logs table
    "CREATE TABLE IF NOT EXISTS order_status_logs (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        old_status VARCHAR(50),
        new_status VARCHAR(50) NOT NULL,
        actor_type ENUM('Farmer', 'Admin', 'Customer') NOT NULL,
        actor_id INT,
        rejection_reason TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
    )"
];

foreach ($queries as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Success: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
?>
