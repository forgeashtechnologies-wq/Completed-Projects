<?php
/**
 * Session Check Utility
 * This file is for debugging purposes only and should not be accessible in production
 */

// Verify this is running in a development environment
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    http_response_code(403);
    die('Access Denied');
}

// Basic security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Optimize error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to safely display session data
function displaySessionData($data, $level = 0) {
    $output = '';
    foreach ($data as $key => $value) {
        $indent = str_repeat('  ', $level);
        if (is_array($value)) {
            $output .= $indent . htmlspecialchars($key) . ":\n";
            $output .= displaySessionData($value, $level + 1);
        } else {
            $output .= $indent . htmlspecialchars($key) . ': ' . htmlspecialchars($value) . "\n";
        }
    }
    return $output;
}

// Cache control headers for development
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Session Debug Information</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .debug-section {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2d3748;
            margin-top: 0;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="debug-section">
        <h2>Session Status</h2>
        <pre><?php
            $status = session_status();
            $statusText = match($status) {
                PHP_SESSION_DISABLED => 'Sessions are disabled',
                PHP_SESSION_NONE => 'Sessions are enabled but none exists',
                PHP_SESSION_ACTIVE => 'Sessions are enabled and one exists',
                default => 'Unknown status'
            };
            echo "Status Code: $status\n";
            echo "Status: $statusText\n";
            echo "Session ID: " . session_id() . "\n";
            echo "Session Name: " . session_name() . "\n";
            echo "Session Cookie Parameters:\n";
            print_r(session_get_cookie_params());
        ?></pre>
    </div>

    <div class="debug-section">
        <h2>Session Data</h2>
        <pre><?php
            if (empty($_SESSION)) {
                echo "No session data available.";
            } else {
                echo displaySessionData($_SESSION);
            }
        ?></pre>
    </div>

    <div class="debug-section">
        <h2>Session Configuration</h2>
        <pre><?php
            $sessionConfig = [
                'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
                'session.cookie_httponly' => ini_get('session.cookie_httponly'),
                'session.cookie_secure' => ini_get('session.cookie_secure'),
                'session.cookie_samesite' => ini_get('session.cookie_samesite'),
                'session.use_strict_mode' => ini_get('session.use_strict_mode'),
                'session.use_cookies' => ini_get('session.use_cookies'),
                'session.use_only_cookies' => ini_get('session.use_only_cookies'),
                'session.cache_limiter' => ini_get('session.cache_limiter'),
                'session.cache_expire' => ini_get('session.cache_expire'),
                'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
            ];
            foreach ($sessionConfig as $key => $value) {
                echo htmlspecialchars($key) . ': ' . htmlspecialchars($value) . "\n";
            }
        ?></pre>
    </div>

    <script>
        // Auto-refresh the page every 30 seconds
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>