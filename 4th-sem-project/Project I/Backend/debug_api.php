<?php
// Test Script to check get_all_products output
$url = "http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Backend/get_all_products.php";
$response = file_get_contents($url);
echo "Response Check:\n";
echo "--------------------------------------------------\n";
echo $response;
echo "\n--------------------------------------------------\n";
?>
