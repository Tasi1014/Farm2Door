<?php
include 'connection.php';
$tables = ['orders', 'order_items', 'payments', 'products'];
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $res = mysqli_query($conn, "SHOW CREATE TABLE `$table`") or die(mysqli_error($conn));
    $row = mysqli_fetch_array($res);
    echo $row[1] . "\n\n";
}
?>
