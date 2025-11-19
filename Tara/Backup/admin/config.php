<?php
// Start session at the very beginning with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '3600');
    session_start();
}

// Force HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}

// Database configuration - CORRECTED VALUES
define('SITE_URL', 'https://tarasdental.in');
define('IMAGES_PATH', '../images/blog/');

// Admin Login Credentials
define('ADMIN_USERNAME', 'tarasmdentistry');
define('ADMIN_PASSWORD', '$2y$12$Hp/ylNDW9eptXW0EEJvQgu2WCy8JypWkwc5KJtGW2sQFPbJJj/MYa');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour timeout
define('SECURE_COOKIE', true);

// Database configuration - CORRECTED VALUES
define('DB_HOST', 'localhost');
define('DB_USER', 'u218412549_admin');
define('DB_PASS', 'Admin@Tara\'s123');
define('DB_NAME', 'u218412549_Tarablog');

// Modern Database Connection (PDO)
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check configuration.");
    }
}

// Legacy Database Connection (mysqli) - if needed
function getLegacyDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        throw new Exception("Database connection failed. Please check configuration.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Security functions
function isAdminLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            error_log('CSRF token validation failed');
            http_response_code(403);
            die('Security check failed. Please try again.');
        }
    }
    return $_SESSION['csrf_token'];
}

function generateCSRFToken() {
    return validateCSRFToken();
}

function verifyPassword($password) {
    return password_verify($password, ADMIN_PASSWORD);
}

function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: index.php?error=timeout');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Setup error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Ensure secure headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.ckeditor.com https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; img-src \'self\' data: https:; font-src \'self\' https:;');

// Register shutdown function to ensure proper session handling
register_shutdown_function(function() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
});