<?php
// Stripe API Configuration
// 1. Try loading from local ignored file (for development)
$secrets_file = __DIR__ . '/stripe_secrets.php';
if (file_exists($secrets_file)) {
    require_once $secrets_file;
} else {
    // 2. Use Environment Variables (for production)
    if (!defined('STRIPE_SECRET_KEY')) define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY'));
    if (!defined('STRIPE_PUBLISHABLE_KEY')) define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY'));
}

// Ensure the Composer autoloader is included
// Adjust path if vendor is in a different location relative to this file
require_once __DIR__ . '/../../../../vendor/autoload.php';
?>
