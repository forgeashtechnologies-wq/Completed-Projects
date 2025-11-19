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
    
    // Test query
    $result = $pdo->query("SHOW TABLES");
    echo "<h4>Available Tables:</h4>";
    echo "<ul>";
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
    
} catch(Exception $e) {
    echo "PDO Connection Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

try {
    // Try MySQLi connection
    echo "<h3>Testing MySQLi Connection:</h3>";
    $mysqli = getLegacyDBConnection();
    echo "MySQLi Connection successful!<br>";
    
    // Test query
    $result = $mysqli->query("SHOW TABLES");
    echo "<h4>Available Tables:</h4>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
    
} catch(Exception $e) {
    echo "MySQLi Connection Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}