<?php
// Save this as session-check.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Session Status: " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);

echo "\nPHP Info for Session:\n";
phpinfo(INFO_VARIABLES);