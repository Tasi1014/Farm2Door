<?php
session_start();
// eSewa failure script
// Clear pending order to prevent stale session
if (isset($_SESSION['pending_order'])) {
    unset($_SESSION['pending_order']);
}
header("Location: ../../Frontend/Cart/checkout.html?error=Payment was cancelled or failed");
exit;
?>
