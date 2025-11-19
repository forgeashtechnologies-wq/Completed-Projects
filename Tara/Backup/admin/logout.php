<?php
// Start session
session_start();

// Log the logout
if (isset($_SESSION['admin_logged_in'])) {
    error_log("Admin logged out successfully");
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear authentication cookie if exists
if (isset($_COOKIE['admin_auth'])) {
    setcookie('admin_auth', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: index.php?logout=1');
exit;