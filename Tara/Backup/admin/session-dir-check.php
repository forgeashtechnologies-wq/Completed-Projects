<?php
// Save as session-dir-check.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get session save path
$sessionPath = session_save_path();
echo "Session save path: " . $sessionPath . "\n";

// Check if directory exists
echo "Directory exists: " . (file_exists($sessionPath) ? 'Yes' : 'No') . "\n";

// Check directory permissions
echo "Directory permissions: " . substr(sprintf('%o', fileperms($sessionPath)), -4) . "\n";

// Check if directory is writable
echo "Directory writable: " . (is_writable($sessionPath) ? 'Yes' : 'No') . "\n";

// Get current PHP user
echo "PHP process user: " . get_current_user() . "\n";

// Check session configuration
echo "\nSession Configuration:\n";
echo "session.save_handler: " . ini_get('session.save_handler') . "\n";
echo "session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";
echo "session.use_strict_mode: " . ini_get('session.use_strict_mode') . "\n";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";