<?php
// test-db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Try PDO connection
    echo "<h3>Testing PDO Connection:</h3>";
    $pdo = getDBConnection();
    echo "PDO Connection successful!<br>";
    
    // Display current database details (without sensitive info)
    echo "<h4>Database Configuration:</h4>";
    echo "Host: " . DB_HOST . "<br>";
    echo "Database: " . DB_NAME . "<br>";
    
    // Test tables
    $requiredTables = ['blog_posts', 'blog_categories', 'post_categories'];
    
    echo "<h4>Checking Required Tables:</h4>";
    foreach ($requiredTables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "✓ Table '$table' exists<br>";
            
            // Get row count for each table
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "  - Row count: $count<br>";
            
            // Show table structure
            echo "  - Structure:<br>";
            $structure = $pdo->query("DESCRIBE $table");
            echo "<pre>";
            while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
                echo "    " . $row['Field'] . " (" . $row['Type'] . ")<br>";
            }
            echo "</pre>";
        } else {
            echo "✗ Table '$table' does not exist!<br>";
        }
    }
    
    // Test a sample query
    echo "<h4>Testing Sample Query:</h4>";
    $stmt = $pdo->query("SELECT * FROM blog_posts LIMIT 1");
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($post) {
        echo "Successfully retrieved a sample post (ID: " . $post['id'] . ")<br>";
    } else {
        echo "No posts found in the database<br>";
    }
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>Database Error:</h3>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
    echo "<p>Error Message: " . $e->getMessage() . "</p>";
    
    // Log the error
    error_log("Database connection error in test-db.php: " . $e->getMessage());
} catch(Exception $e) {
    echo "<h3 style='color: red;'>General Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    // Log the error
    error_log("General error in test-db.php: " . $e->getMessage());
}

// Display PHP Info
echo "<h3>PHP Configuration:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    line-height: 1.6;
}
h2, h3, h4 {
    color: #333;
    margin-top: 20px;
}
pre {
    background: #f5f5f5;
    padding: 10px;
    border-radius: 4px;
}
</style>