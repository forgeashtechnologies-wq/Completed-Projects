<?php
// Save as test-login.php
require_once 'config.php';

echo "Current Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['last_activity'] = time();
    $_SESSION['test_value'] = 'test123';
    
    echo "Session data set!\n";
    echo "New session contents:\n";
    print_r($_SESSION);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
</head>
<body>
    <h1>Test Login Form</h1>
    <form method="POST">
        <button type="submit">Set Test Session Data</button>
    </form>
    
    <h2>Current Session Data:</h2>
    <pre><?php print_r($_SESSION); ?></pre>
</body>
</html>