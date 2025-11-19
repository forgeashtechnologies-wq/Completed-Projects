<?php
/**
 * Common functions for the PR Classes website
 */

// Prevent multiple inclusions
if (defined('FUNCTIONS_INCLUDED')) {
    return;
}
define('FUNCTIONS_INCLUDED', true);

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the config file to access constants and database connection
require_once __DIR__ . '/config.php';

/**
 * Sanitize input data
 * 
 * @param mixed $data Data to sanitize
 * @return string Sanitized data
 */
if (!function_exists('cleanInput')) {
    function cleanInput($data) {
        if ($data === null) {
            return '';
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Validate email format
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

/**
 * Validate phone number format (basic validation)
 * 
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function isValidPhone($phone) {
    // Remove any non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if it's a valid length (adjust for your country's phone format)
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Generate a random string
 * 
 * @param int $length Length of the random string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }
}

/**
 * Check if user is admin
 * 
 * @return bool True if admin, false otherwise
 */
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isLoggedIn() && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
    }
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Format date to a readable format
 * 
 * @param string $date Date to format
 * @return string Formatted date
 */
if (!function_exists('formatDate')) {
    function formatDate($date) {
        if (empty($date)) return 'N/A';
        return date('F j, Y', strtotime($date));
    }
}

/**
 * Get default mode value for database tables
 * 
 * @return string Default mode value
 */
if (!function_exists('getDefaultMode')) {
    function getDefaultMode() {
        return 'public'; // Default mode for all content
    }
}

/**
 * Format currency values
 * 
 * @param float $amount The amount to format
 * @param string $currency The currency symbol (default: ₹)
 * @return string Formatted currency string
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currency = '₹') {
        return $currency . number_format($amount, 0, '.', ',');
    }
}

/**
 * Calculate discounted price
 * 
 * @param float $price Original price
 * @param float $discountPercentage Discount percentage
 * @return float Discounted price
 */
if (!function_exists('calculateDiscountedPrice')) {
    function calculateDiscountedPrice($price, $discountPercentage) {
        return $price - ($price * $discountPercentage / 100);
    }
}

/**
 * Truncate text to a specific length
 * 
 * @param string $text Text to truncate
 * @param int $length Length to truncate to
 * @return string Truncated text
 */
if (!function_exists('truncateText')) {
    function truncateText($text, $length = 100) {
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . '...';
    }
}

/**
 * Check if admin is logged in and redirect if not
 * 
 * @return void
 */
function check_admin_login() {
    if (!isset($_SESSION['admin_username'])) {
        // Use absolute path for redirection to avoid path issues
        header('Location: ' . SITE_URL . 'admin/login.php');
        exit;
    }
}

/**
 * Validate login credentials
 * 
 * @param string $username Username
 * @param string $password Password
 * @param PDO $pdo Database connection
 * @return array|bool User data if valid, false otherwise
 */
function validateLogin($username, $password, $pdo) {
    if (!$pdo) return false;
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        return false;
    }
    
    try {
        // Check what driver we're using
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        if ($driver === 'mysql') {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE LOWER(username) = LOWER(?)");
        } else {
            // SQLite is case-sensitive, so we need a different approach
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? COLLATE NOCASE");
        }
        
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Login validation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Display success message in session
 * 
 * @param string $message Message to display
 * @return void
 */
if (!function_exists('display_success_message')) {
    function display_success_message($message) {
        $_SESSION['success_message'] = $message;
    }
}

/**
 * Display error message in session
 * 
 * @param string $message Message to display
 * @return void
 */
if (!function_exists('display_error_message')) {
    function display_error_message($message) {
        $_SESSION['error_message'] = $message;
    }
}

/**
 * Get success message from session
 * 
 * @return string Success message
 */
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

/**
 * Get error message from session
 * 
 * @return string Error message
 */
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

/**
 * Safe database query execution
 * 
 * @param PDO $pdo Database connection
 * @param string $query SQL query
 * @param array $params Query parameters
 * @return PDOStatement|bool Statement if successful, false otherwise
 */
function executeQuery($pdo, $query, $params = []) {
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query execution error: " . $e->getMessage());
        error_log("Query: " . $query);
        error_log("Params: " . print_r($params, true));
        return false;
    }
}

/**
 * Insert testimonial with proper mode
 * 
 * @param array $data Testimonial data
 * @return bool True if successful, false otherwise
 */
function insert_testimonial($data) {
    global $pdo;
    
    // IMPORTANT: Always set a default mode if it's not present
    if (!isset($data['mode']) || empty($data['mode'])) {
        $data['mode'] = 'public'; // Hardcode the default directly
    }
    
    // Insert with all required fields including mode
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    try {
        $stmt = $pdo->prepare("INSERT INTO testimonials ({$columns}) VALUES ({$placeholders})");
        
        error_log('Inserting testimonial with values: ' . json_encode(array_values($data)));
        $result = $stmt->execute(array_values($data));
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error inserting testimonial: " . $e->getMessage());
        return false;
    }
}

/**
 * Insert marksheet with proper mode
 * 
 * @param array $data Marksheet data
 * @return bool True if successful, false otherwise
 */
function insert_marksheet($data) {
    global $pdo;
    
    // IMPORTANT: Always set a default mode if it's not present
    if (!isset($data['mode']) || empty($data['mode'])) {
        $data['mode'] = 'public'; // Hardcode the default directly
    }
    
    // Insert with all required fields including mode
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    try {
        $stmt = $pdo->prepare("INSERT INTO marksheets ({$columns}) VALUES ({$placeholders})");
        $result = $stmt->execute(array_values($data));
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error inserting marksheet: " . $e->getMessage());
        return false;
    }
}

/**
 * Insert video with proper mode
 * 
 * @param array $data Video data
 * @return bool True if successful, false otherwise
 */
function insert_video($data) {
    global $pdo;
    
    // IMPORTANT: Always set a default mode if it's not present
    if (!isset($data['mode']) || empty($data['mode'])) {
        $data['mode'] = 'public'; // Hardcode the default directly
    }
    
    // Insert with all required fields including mode
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    try {
        $stmt = $pdo->prepare("INSERT INTO videos ({$columns}) VALUES ({$placeholders})");
        $result = $stmt->execute(array_values($data));
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error inserting video: " . $e->getMessage());
        return false;
    }
}

/**
 * Get approved testimonials with database-agnostic query
 * 
 * @return array Testimonials
 */
function getApprovedTestimonials() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'Approved' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching testimonials: " . $e->getMessage());
        return [];
    }
}

/**
 * Get approved marksheets with database-agnostic query
 * 
 * @return array Marksheets
 */
function getApprovedMarksheets() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM marksheets WHERE status = 'Approved' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching marksheets: " . $e->getMessage());
        return [];
    }
}

/**
 * Get approved videos with database-agnostic query
 * 
 * @return array Videos
 */
function getApprovedVideos() {
    global $pdo;
    
    try {
        // Note: We use LOWER() to make the query case-insensitive in both MySQL and SQLite
        $stmt = $pdo->query("SELECT * FROM videos WHERE LOWER(status) = 'approved' ORDER BY id DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching videos: " . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to extract YouTube video ID from URL
 * 
 * @param string $url YouTube URL
 * @return string|bool Video ID or false if not found
 */
if (!function_exists('getYoutubeVideoId')) {
    function getYoutubeVideoId($url) {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $url, $match)) {
            return $match[1];
        }
        return false;
    }
}

/**
 * Process login form submission
 * 
 * @return array Result with success status and message
 */
function processLogin() {
    global $pdo;
    
    $result = [
        'success' => false,
        'message' => ''
    ];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = cleanInput($_POST['username'] ?? '');
        $password = cleanInput($_POST['password'] ?? '');
        
        // Debugging output
        error_log('Username: ' . $username);
        
        if (empty($username) || empty($password)) {
            $result['message'] = "Please enter both username and password";
            error_log('Login failed: Empty username or password');
        } else {
            try {
                $user = validateLogin($username, $password, $pdo);
                
                if ($user) {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    error_log('Login successful for user: ' . $username);
                    
                    $result['success'] = true;
                    $result['message'] = "Login successful";
                } else {
                    $result['message'] = "Invalid username or password";
                    error_log('Login failed for user: ' . $username . ' - Invalid credentials');
                }
            } catch (PDOException $e) {
                $result['message'] = "An error occurred during login";
                error_log('Login error: ' . $e->getMessage());
            }
        }
    }
    
    return $result;
}

// Update other potentially duplicated functions the same way:
if (!function_exists('sanitize')) {
    function sanitize($data) {
        if ($data === null) {
            return '';
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
?>