<?php
/**
 * Custom error handler for PR Classes website
 */

// Prevent multiple inclusions
if (defined('ERROR_HANDLER_INCLUDED')) {
    return;
}
define('ERROR_HANDLER_INCLUDED', true);

// Set error reporting level
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to users
ini_set('log_errors', 1); // Log errors

// Create logs directory if it doesn't exist
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . '/php_errors.log');

/**
 * Custom error handler
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('[Y-m-d H:i:s]') . " Error [$errno]: $errstr in $errfile on line $errline";
    error_log($error_message);
    
    // Don't execute PHP's internal error handler
    return true;
}

/**
 * Custom exception handler
 */
function customExceptionHandler($exception) {
    $error_message = date('[Y-m-d H:i:s]') . " Exception: " . $exception->getMessage() . 
                     " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($error_message);
    
    // Check if this is an admin page
    $current_path = $_SERVER['PHP_SELF'] ?? '';
    if (strpos($current_path, '/admin/') !== false) {
        // Redirect to admin error page
        header('Location: /admin/error.php?error=exception');
        exit;
    } else {
        // For front-end, show a user-friendly error
        if (!headers_sent()) {
            http_response_code(500);
            include __DIR__ . '/../error.php';
        } else {
            echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;'>";
            echo "<h3>Something went wrong</h3>";
            echo "<p>We're sorry, but there was an error processing your request. Please try again later.</p>";
            echo "</div>";
        }
    }
    
    exit;
}

/**
 * Shutdown function to catch fatal errors
 */
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error_message = date('[Y-m-d H:i:s]') . " Fatal Error: [" . $error['type'] . "] " . 
                         $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
        error_log($error_message);
        
        // If headers not sent, redirect to error page
        if (!headers_sent()) {
            http_response_code(500);
            
            // Check if this is an admin page
            $current_path = $_SERVER['PHP_SELF'] ?? '';
            if (strpos($current_path, '/admin/') !== false) {
                header('Location: /admin/error.php?error=fatal');
            } else {
                include __DIR__ . '/../error.php';
            }
        } else {
            echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;'>";
            echo "<h3>Something went wrong</h3>";
            echo "<p>We're sorry, but there was an error processing your request. Please try again later.</p>";
            echo "</div>";
        }
    }
}

// Set the custom error handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
register_shutdown_function('shutdownHandler');
?> 