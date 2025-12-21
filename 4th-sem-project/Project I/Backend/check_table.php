<?php
require "connection.php";
$res = mysqli_query($conn, "SHOW TABLES LIKE 'order_status_logs'");
if (mysqli_num_rows($res) > 0) {
    echo "Table 'order_status_logs' exists.\n";
    $res = mysqli_query($conn, "DESCRIBE order_status_logs");
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
} else {
    echo "Table 'order_status_logs' DOES NOT exist.\n";
}
mysqli_close($conn);
?>
