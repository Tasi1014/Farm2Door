<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>System Diagnostics</h1>";

// 1. Check Connection
if (!file_exists('connection.php')) {
    die("CRITICAL: connection.php not found!");
}
include 'connection.php';
echo "<p>Database connection included.</p>";

if (!$conn) {
    die("CRITICAL: \$conn is null or false. " . mysqli_connect_error());
}
echo "<p>Database connection object exists.</p>";

// 2. Check Table existance
$tables = ['products', 'farmer_registration', 'customer_registration', 'cart'];
foreach ($tables as $t) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$t'");
    if (mysqli_num_rows($check) > 0) {
        $count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM $t"))[0];
        echo "<p>Table <strong>$t</strong> exists. Rows: <strong>$count</strong></p>";
    } else {
        echo "<p style='color:red'>Table <strong>$t</strong> DOES NOT EXIST!</p>";
    }
}

// 3. Test Query (The one we use)
echo "<h2>Testing Product Query</h2>";
$sql = "SELECT p.*, f.firstName, f.lastName 
        FROM products p 
        LEFT JOIN farmer_registration f ON p.farmer_id = f.farmer_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    echo "<p>Query Successful. Returned " . count($rows) . " rows.</p>";
    echo "<pre>" . print_r(array_slice($rows, 0, 2), true) . "</pre>"; // Show first 2
} else {
    echo "<p style='color:red'>Query Failed: " . mysqli_error($conn) . "</p>";
}

// 4. Test JSON Output (Emulate get_all_products)
echo "<h2>Testing JSON Emulation</h2>";
$json = json_encode(['success' => true, 'products' => $rows]);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "<p>JSON Encoding successful.</p>";
} else {
    echo "<p style='color:red'>JSON Error: " . json_last_error_msg() . "</p>";
}

mysqli_close($conn);
?>
