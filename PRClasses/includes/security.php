<?php
/**
 * Security functions for the PR Classes website
 */

// Prevent multiple inclusions
if (defined('SECURITY_INCLUDED')) {
    return;
}
define('SECURITY_INCLUDED', true);

/**
 * Generate CSRF token
 */
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Verify CSRF token
 */
if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }
}

/**
 * Check if request is AJAX
 */
if (!function_exists('isAjaxRequest')) {
    function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

/**
 * Redirect to login if not authenticated
 */
if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
}

/**
 * Redirect to error page if not admin
 */
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isAdmin()) {
            header('Location: /admin/error.php?error=permission');
            exit;
        }
    }
}

/**
 * Sanitize file name
 */
if (!function_exists('sanitizeFileName')) {
    function sanitizeFileName($filename) {
        // Remove any path information
        $filename = basename($filename);
        
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);
        
        // Remove any non-alphanumeric characters except dots, underscores and hyphens
        $filename = preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $filename);
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
}

/**
 * Validate file upload
 */
if (!function_exists('validateFileUpload')) {
    function validateFileUpload($file, $allowed_types = [], $max_size = 5242880) {
        // Check if file was uploaded without errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'File upload error: ' . uploadErrorMessage($file['error'])
            ];
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            return [
                'success' => false,
                'message' => 'File is too large. Maximum size is ' . formatFileSize($max_size)
            ];
        }
        
        // Check file type if allowed types are specified
        if (!empty($allowed_types)) {
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($file_info, $file['tmp_name']);
            finfo_close($file_info);
            
            if (!in_array($mime_type, $allowed_types)) {
                return [
                    'success' => false,
                    'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types)
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'File is valid'
        ];
    }
}

/**
 * Get upload error message
 */
if (!function_exists('uploadErrorMessage')) {
    function uploadErrorMessage($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
}

/**
 * Format file size
 */
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

/**
 * Sanitize output for HTML display
 * 
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function htmlSafe($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Get client IP address
 * 
 * @return string Client IP address
 */
function getClientIp() {
    $ip = '0.0.0.0';
    
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Log security event
 * 
 * @param string $event Event description
 * @param string $level Event level (info, warning, error)
 * @return void
 */
function logSecurityEvent($event, $level = 'info') {
    $log_file = __DIR__ . '/../logs/security.log';
    $ip = getClientIp();
    $time = date('Y-m-d H:i:s');
    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
    
    $log_entry = "[$time] [$level] [$ip] [User:$user] $event" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

/**
 * Check if user has admin role
 * 
 * @return bool True if admin, false otherwise
 */
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

/**
 * Add CSRF token to form
 * 
 * @return string HTML input field with CSRF token
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}