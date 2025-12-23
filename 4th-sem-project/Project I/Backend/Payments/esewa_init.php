<?php
session_start();
require "../connection.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    if (!isset($_SESSION['customer_id'])) {
        throw new Exception("Please login to proceed.");
    }
    // New Flow: Check session for pending order
    if (!isset($_SESSION['pending_order'])) {
        throw new Exception("No pending order found. Please try checkout again.");
    }

    $pending = $_SESSION['pending_order'];
    
    // Safety check: ensure it belongs to the logged in user
    if ($pending['customer_id'] !== $_SESSION['customer_id']) {
        throw new Exception("Unauthorized order access.");
    }

    // Check expiration (e.g., 30 minutes)
    if (time() - $pending['created_at'] > 1800) {
        unset($_SESSION['pending_order']);
        throw new Exception("Order session expired. Please try again.");
    }

    $total_amount = $pending['total_amount'];
    $transaction_uuid = $pending['transaction_uuid'];
    $product_code = "EPAYTEST";
    $secret_key = "8gBm/:&EnhH.1/q"; 

    $total_amount_str = strval($total_amount);

    // Data for signature: total_amount,transaction_uuid,product_code
    $message = "total_amount=$total_amount_str,transaction_uuid=$transaction_uuid,product_code=$product_code";
    
    // Generate HMAC-SHA256 Signature
    $signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

    $response['success'] = true;
    $response['esewa_data'] = [
        'amount' => $total_amount_str,
        'tax_amount' => 0,
        'total_amount' => $total_amount_str,
        'transaction_uuid' => $transaction_uuid,
        'product_code' => $product_code,
        'product_service_charge' => 0,
        'product_delivery_charge' => 0,
        'signed_field_names' => "total_amount,transaction_uuid,product_code",
        'signature' => $signature,
        'success_url' => "http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Backend/Payments/esewa_success.php",
        'failure_url' => "http://localhost/PROJECT_Farm2Door/4th-sem-project/Project%20I/Backend/Payments/esewa_failure.php"
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($conn);
?>
