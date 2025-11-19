<?php
/**
 * Session handler
 * 
 * This file handles session management and security.
 */

// Set session cookie parameters
$session_lifetime = 3600; // 1 hour
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$httponly = true;
$samesite = 'Lax';

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
]);

// Start session
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    // Session expired, destroy it
    session_unset();
    session_destroy();
    
    // Redirect to login page if needed
    if (!in_array($_SERVER['PHP_SELF'], ['/login.php', '/index.php'])) {
        header('Location: login.php?expired=1');
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time(); 