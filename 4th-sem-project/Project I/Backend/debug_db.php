<?php
$host = '127.0.0.1'; // Using IP instead of localhost
$username = "root";
$password = "";
$dbname = "farm2door";

echo "Trying to connect to $host...\n";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully!\n";

$res = mysqli_query($conn, "DESCRIBE payments");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

mysqli_close($conn);
?>
