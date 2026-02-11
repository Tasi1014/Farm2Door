<?php
include 'connection.php';

$phoneNumbers = ['9803901467', '9813132648', '9823782211'];
$tables = [
    'customer_registration' => 'customer_id',
    'farmer_registration' => 'farmer_id'
];

foreach ($tables as $table => $idField) {
    echo "--- Updating $table ---\n";
    $query = "SELECT $idField FROM $table ORDER BY $idField ASC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row[$idField];
            $phone = $phoneNumbers[$count % 3];
            
            $updateQuery = "UPDATE $table SET Phone = ? WHERE $idField = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $phone, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Updated ID $id with $phone\n";
            } else {
                echo "Failed to update ID $id\n";
            }
            mysqli_stmt_close($stmt);
            $count++;
        }
    } else {
        echo "Error fetching records from $table: " . mysqli_error($conn) . "\n";
    }
    echo "\n";
}

mysqli_close($conn);
?>
