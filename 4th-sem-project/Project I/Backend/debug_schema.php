<?php
include 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Schema Inspector</h1>";

$table = 'customer_registration';
echo "<h2>Table: $table</h2>";

$result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");

if (!$result) {
    echo "Error showing columns: " . mysqli_error($conn);
} else {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $key => $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Also check just one row to see keys
echo "<h2>Sample Row Data</h2>";
$res2 = mysqli_query($conn, "SELECT * FROM `$table` LIMIT 1");
if ($row = mysqli_fetch_assoc($res2)) {
    echo "<pre>" . print_r(array_keys($row), true) . "</pre>";
} else {
    echo "Table is empty.";
}
?>
