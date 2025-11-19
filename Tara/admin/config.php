<?php
// Start session at the very beginning with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    // Only use secure cookies in production environment
    if (!strstr($_SERVER['HTTP_HOST'], 'localhost') && !strstr($_SERVER['HTTP_HOST'], '127.0.0.1') && !strstr($_SERVER['HTTP_HOST'], '0.0.0.0')) {
        ini_set('session.cookie_secure', '1');
    } else {
        ini_set('session.cookie_secure', '0');
    }
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '3600');
    ini_set('error_log', __DIR__ . '/error.log');
    session_start();
}

// Force HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    // Skip HTTPS redirection for localhost development
    if (!strstr($_SERVER['HTTP_HOST'], 'localhost') && !strstr($_SERVER['HTTP_HOST'], '127.0.0.1') && !strstr($_SERVER['HTTP_HOST'], '0.0.0.0')) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Site Configuration
define('SITE_URL', 'https://tarasdental.in');
define('IMAGES_PATH', '../images/blog/');

// Admin Login Credentials
define('ADMIN_USERNAME', 'tarasmdentistry');
define('ADMIN_PASSWORD', '$2y$12$Hp/ylNDW9eptXW0EEJvQgu2WCy8JypWkwc5KJtGW2sQFPbJJj/MYa');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour timeout
define('SECURE_COOKIE', true);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u218412549_admin');
define('DB_PASS', 'Admin@Tara\'s123');
define('DB_NAME', 'u218412549_Tarablog');

// Modern Database Connection (PDO)
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed. Please try again later.");
    }
}

// Security functions
function isAdminLoggedIn() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

function verifyPassword($password) {
    if (empty($password) || !defined('ADMIN_PASSWORD')) {
        error_log("Password verification failed - empty password or ADMIN_PASSWORD not defined");
        return false;
    }
    return password_verify($password, ADMIN_PASSWORD);
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: index.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Generate a token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Only validate on POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token'])) {
            error_log('CSRF token missing in POST data');
            return false;
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            error_log('CSRF token missing in session');
            return false;
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            error_log('CSRF token validation failed');
            return false;
        }
    }
    
    return $_SESSION['csrf_token'];
}

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Ensure secure headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.ckeditor.com https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; img-src \'self\' data: https:; font-src \'self\' https:;');