<?php
include 'connection.php';
$tables = ['customer_registration', 'farmer_registration'];

foreach ($tables as $table) {
    echo "--- $table ---\n";
    $query = "SELECT * FROM $table LIMIT 10";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: " . ($row['customer_id'] ?? $row['farmer_id']) . " | Name: " . $row['firstName'] . " | Phone: " . $row['Phone'] . "\n";
        }
    }
}
mysqli_close($conn);
?>
