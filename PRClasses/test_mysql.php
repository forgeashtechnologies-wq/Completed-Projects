<?php
// Test MySQL connection after removing SQLite fallback

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>MySQL Connection Test</h1>";

// MySQL credentials
$db_host = 'localhost';
$db_name = 'pr_classes_test';
$db_user = 'test_admin';
$db_pass = 'Test@123';

echo "<p>Attempting to connect to MySQL database:</p>";
echo "<ul>";
echo "<li>Host: {$db_host}</li>";
echo "<li>Database: {$db_name}</li>";
echo "<li>User: {$db_user}</li>";
echo "</ul>";

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "<p style='color:green;font-weight:bold;'>✓ MySQL connection successful!</p>";
    
    // Test query execution
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Found " . count($tables) . " tables in the database:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;font-weight:bold;'>✗ MySQL connection failed: " . $e->getMessage() . "</p>";
}

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>";
?>