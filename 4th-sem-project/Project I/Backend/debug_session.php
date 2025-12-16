<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use the SAME configuration as other scripts
session_set_cookie_params(0, '/');
session_start();

header('Content-Type: text/html');
echo "<h1>Session Diagnostic</h1>";
echo "<hr>";

echo "<h2>1. Session Metadata</h2>";
echo "<strong>Session ID:</strong> " . session_id() . "<br>";
echo "<strong>Session Name:</strong> " . session_name() . "<br>";
echo "<strong>Session Save Path:</strong> " . session_save_path() . "<br>";
echo "<strong>Cookie Params:</strong> <pre>" . print_r(session_get_cookie_params(), true) . "</pre>";

echo "<h2>2. Session Data (\$_SESSION)</h2>";
if (empty($_SESSION)) {
    echo "<p style='color:red;'>SESSION IS EMPTY. You are not logged in according to this script.</p>";
} else {
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

echo "<h2>3. Cookies Received (\$_COOKIE)</h2>";
echo "<pre>" . print_r($_COOKIE, true) . "</pre>";

echo "<hr>";
echo "<p><em>Compare 'Session ID' here with the one in your browser dev tools (Application -> Cookies). They must match.</em></p>";
?>
