<?php
// Save this as debug.php in your admin directory
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

try {
    // Test database connection
    $db = getDBConnection();
    echo "Database connection successful!\n";
    
    // Check if tables exist
    $tables = ['blog_posts', 'blog_categories', 'post_categories'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table $table exists\n";
        } else {
            echo "Table $table does not exist!\n";
        }
    }
    
    // Check if image upload directory exists and is writable
    $upload_dir = IMAGES_PATH;
    if (!file_exists($upload_dir)) {
        echo "Warning: Upload directory does not exist\n";
        if (!mkdir($upload_dir, 0755, true)) {
            echo "Error: Failed to create upload directory\n";
        } else {
            echo "Created upload directory successfully\n";
        }
    } else {
        echo "Upload directory exists\n";
        if (is_writable($upload_dir)) {
            echo "Upload directory is writable\n";
        } else {
            echo "Warning: Upload directory is not writable\n";
        }
    }
    
    // Test session
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "Session is active\n";
        print_r($_SESSION);
    } else {
        echo "Warning: Session is not active\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}