<?php
include 'connection.php';
header('Content-Type: application/json');

$tables = ['orders', 'order_items', 'payments'];
$result_json = [];

foreach ($tables as $table) {
    try {
        $res = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
        if ($res) {
            $cols = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $cols[] = $row['Field'];
            }
            $result_json[$table] = $cols;
        } else {
            $result_json[$table] = "Error: " . mysqli_error($conn);
        }
    } catch (Exception $e) {
        $result_json[$table] = "Exception: " . $e->getMessage();
    }
}

echo json_encode($result_json, JSON_PRETTY_PRINT);
?>
