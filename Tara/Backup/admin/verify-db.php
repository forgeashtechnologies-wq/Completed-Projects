<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Verification Test</h2>";

// Define credentials
$db_host = 'localhost';
$db_user = 'u218412549_admin';
$db_pass = 'Admin@Tara\'s123';
$db_name = 'u218412549_Tarablog';

// Test direct mysqli connection
echo "<h3>Testing MySQLi Connection:</h3>";
try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    echo "✅ MySQLi connection successful!<br>";
    
    // Test query
    $result = $mysqli->query("SHOW TABLES");
    echo "<h4>Available Tables:</h4>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "❌ MySQLi Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test PDO connection
echo "<h3>Testing PDO Connection:</h3>";
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ PDO connection successful!<br>";
    
    // Test query
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<strong>Connection Info:</strong><br>";
    echo "Database Name: " . $db_name . "<br>";
    echo "Number of tables: " . count($tables) . "<br>";
} catch (PDOException $e) {
    echo "❌ PDO Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<h3>Environment Information:</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension is loaded<br>";
} else {
    echo "❌ MySQLi extension is NOT loaded<br>";
}
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded<br>";
} else {
    echo "❌ PDO MySQL extension is NOT loaded<br>";
}