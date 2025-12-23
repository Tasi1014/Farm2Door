<?php
include 'connection.php';

echo "<h2>Database Schema Update</h2>";

$updates = [
    "ALTER TABLE farmer_registration ADD COLUMN status ENUM('active', 'blocked') DEFAULT 'active'",
    "ALTER TABLE customer_registration ADD COLUMN status ENUM('active', 'blocked') DEFAULT 'active'"
];

foreach ($updates as $sql) {
    echo "<p>Executing: <code>$sql</code> ... ";
    try {
        if (mysqli_query($conn, $sql)) {
            echo "<span style='color: green;'>Success</span>";
        } else {
            echo "<span style='color: red;'>Failed</span> (Error: " . mysqli_error($conn) . ")";
        }
    } catch (Exception $e) {
        echo "<span style='color: orange;'>Skipped/Error</span> (Likely already exists or: " . $e->getMessage() . ")";
    }
    echo "</p>";
}

echo "<hr><p>Update process complete. You can now delete this file or keep it for records.</p>";
mysqli_close($conn);
?>
