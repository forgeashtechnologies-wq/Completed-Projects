<?php
// Save as test.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['test'] = 'Hello World';
    echo "Session set!";
} else {
    echo "Current session data: ";
    var_dump($_SESSION);
}
?>
<form method="POST">
    <button type="submit">Set Session</button>
</form>