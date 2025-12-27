<?php
include 'connection.php';
$table = 'payments';
$result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
echo "Table: $table\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
