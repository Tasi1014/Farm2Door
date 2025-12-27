<?php
include 'connection.php';
$table = 'refunds';
$result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
echo "Table: $table\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
