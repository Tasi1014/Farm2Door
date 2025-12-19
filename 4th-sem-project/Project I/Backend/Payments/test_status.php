<?php
include '../connection.php';
$statuses = ['Completed', 'Success', 'Paid', 'Pending', 'COMPLETE'];
foreach ($statuses as $status) {
    $sql = "INSERT INTO payments (order_id, payment_method, payment_status, amount_paid, transaction_id) 
            VALUES (0, 'TEST', '$status', 0.00, 'TEST')";
    if (mysqli_query($conn, $sql)) {
        echo "Status '$status' is VALID\n";
        // Clean up
        mysqli_query($conn, "DELETE FROM payments WHERE transaction_id = 'TEST'");
    } else {
        echo "Status '$status' is INVALID: " . mysqli_error($conn) . "\n";
    }
}
?>
