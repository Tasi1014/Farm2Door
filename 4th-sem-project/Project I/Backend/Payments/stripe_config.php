<?php
// Stripe API Configuration
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_placeholder_for_git');
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_placeholder_for_git'); // Add if needed later, but we mostly need secret for backend

// Ensure the Composer autoloader is included
// Adjust path if vendor is in a different location relative to this file
require_once __DIR__ . '/../../../../vendor/autoload.php';
?>
