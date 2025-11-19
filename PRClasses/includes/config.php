<?php
// Define INCLUDED_FROM_INDEX to allow config.php to be included
define('INCLUDED_FROM_INDEX', true);

// Enable error reporting for development
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1); // Log errors instead
error_log("Config file loaded");

// Create logs directory if it doesn't exist
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . '/php_errors.log'); // Set path for error log



// MySQL credentials
$db_host = 'localhost';
$db_name = 'pr_classes_test';
$db_user = 'test_admin';
$db_pass = 'Test@123';

// Site Configuration
define('SITE_NAME', 'PR Classes');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// MySQL connection
try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    error_log("MySQL connection successful");
    
    // Create tables if they don't exist
    if (file_exists(__DIR__ . '/schema_mysql.php')) {
        include_once __DIR__ . '/schema_mysql.php';
    } else {
        error_log("schema_mysql.php file not found");
    }
    
} catch (PDOException $e) {
    error_log("MySQL connection error: " . $e->getMessage());
    
    // Log the error but don't expose details to users
    error_log("Database connection failed completely. " . $e->getMessage());
    
    // For regular pages, continue with a null PDO to avoid breaking the site
    $pdo = null;
}

// For backward compatibility with mysqli code
class MySQLConnection {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function query($sql) {
        if (!$this->pdo) return null;
        try {
            $stmt = $this->pdo->query($sql);
            return new MySQLResult($stmt);
        } catch (Exception $e) {
            error_log("MySQL query error: " . $e->getMessage());
            return null;
        }
    }
    
    public function prepare($sql) {
        return $this->pdo ? $this->pdo->prepare($sql) : null;
    }
    
    public function real_escape_string($string) {
        return str_replace("'", "''", $string); // Basic escaping for compatibility
    }
    
    public function set_charset($charset) {
        if ($this->pdo) {
            try {
                $this->pdo->exec("SET NAMES $charset");
            } catch (Exception $e) {
                error_log("Set charset error: " . $e->getMessage());
            }
        }
        return true;
    }
    
    public function __get($name) {
        if ($name === 'connect_error') {
            return null; // No error if we got this far
        }
        return null;
    }
}

class MySQLResult {
    private $stmt;
    public $num_rows = 0;
    private $rows = null;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
        if ($stmt) {
            try {
                // Calculate num_rows immediately and store it
                $this->rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->num_rows = count($this->rows);
                // Reset the statement cursor for future fetch operations
                $this->stmt->execute();
            } catch (Exception $e) {
                error_log("MySQLResult error: " . $e->getMessage());
                $this->rows = [];
                $this->num_rows = 0;
            }
        }
    }
    
    public function fetch_assoc() {
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_ASSOC) : null;
    }
    
    public function fetch_all($mode = null) {
        return $this->rows ?: ($this->stmt ? $this->stmt->fetchAll($mode ?: PDO::FETCH_ASSOC) : []);
    }
    
    public function num_rows() {
        return $this->num_rows;
    }
}

$conn = new MySQLConnection($pdo);

// Helper Functions

// Sanitize input
if (!function_exists('sanitize')) {
    function sanitize($data) {
        if ($data === null) {
            return '';
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Generate random string
if (!function_exists('generate_random_string')) {
    function generate_random_string($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $random_string;
    }
}

// Format date
if (!function_exists('format_date')) {
    function format_date($date) {
        return date('F j, Y', strtotime($date));
    }
}

// Display success message
if (!function_exists('display_success_message')) {
    function display_success_message($message) {
        $_SESSION['success_message'] = $message;
    }
}

// Display error message
if (!function_exists('display_error_message')) {
    function display_error_message($message) {
        $_SESSION['error_message'] = $message;
    }
}

// Get success message
if (!function_exists('get_success_message')) {
    function get_success_message() {
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            return $message;
        }
        return '';
    }
}

// Get error message
if (!function_exists('get_error_message')) {
    function get_error_message() {
        if (isset($_SESSION['error_message'])) {
            $message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            return $message;
        }
        return '';
    }
}
?>