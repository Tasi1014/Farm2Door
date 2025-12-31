<?php
// Stripe API Configuration
// 1. Try loading from local ignored file (for development)
$secrets_file = __DIR__ . '/stripe_secrets.php';
if (file_exists($secrets_file)) {
    require_once $secrets_file;
}
require_once __DIR__ . '/../../../../vendor/autoload.php';
?>
