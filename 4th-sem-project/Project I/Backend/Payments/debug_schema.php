<?php
include '../connection.php';
$res = mysqli_query($conn, "SHOW COLUMNS FROM payments LIKE 'payment_status'");
$row = mysqli_fetch_assoc($res);
print_r($row);
?>
