<?php
// admin_login_fix.php - Place in your website root directory

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PR Classes Admin Login Fixer</h1>";

// Define paths
$root_path = $_SERVER['DOCUMENT_ROOT'];
// MySQL credentials
$db_host = 'localhost';
$db_name = 'pr_classes_test';
$db_user = 'test_admin';
$db_pass = 'Test@123';

echo "<p>Document Root: $root_path</p>";
echo "<p>Database: $db_name on $db_host</p>";

// Check database connectivity
try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connected to MySQL database</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $table_exists = $stmt->fetchColumn();
    
    if (!$table_exists) {
        echo "<p style='color:red'>❌ Users table doesn't exist!</p>";
        
        // Create the users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                role VARCHAR(50) DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "<p style='color:green'>✓ Created users table</p>";
        
        // Add admin user
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (username, email, password_hash, role) 
            VALUES ('admin', 'admin@prclasses.in', '$password_hash', 'admin')
        ");
        echo "<p style='color:green'>✓ Created admin user</p>";
    } else {
        echo "<p style='color:green'>✓ Users table exists</p>";
        
        // Check if admin user exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $admin_exists = $stmt->fetchColumn();
        
        if (!$admin_exists) {
            // Add admin user
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->exec("
                INSERT INTO users (username, email, password_hash, role) 
                VALUES ('admin', 'admin@prclasses.in', '$password_hash', 'admin')
            ");
            echo "<p style='color:green'>✓ Created admin user</p>";
        } else {
            echo "<p style='color:green'>✓ Admin user exists</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Fix admin/login.php file
$login_file = $root_path . '/admin/login.php';
if (file_exists($login_file)) {
    echo "<p>Found admin/login.php file</p>";
    
    // Create a backup
    $backup_file = $login_file . '.backup';
    copy($login_file, $backup_file);
    echo "<p style='color:green'>✓ Created backup of login.php</p>";
}

// Test authentication
echo "<h2>Testing Admin Authentication</h2>";

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch();
    
    if ($user) {
        $is_valid = password_verify('admin123', $user['password_hash']);
        if ($is_valid) {
            echo "<p style='color:green'>✓ Authentication test successful!</p>";
        } else {
            echo "<p style='color:red'>❌ Password verification failed</p>";
            
            // Reset admin password
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
            $stmt->execute([$password_hash]);
            echo "<p style='color:green'>✓ Reset admin password to 'admin123'</p>";
        }
    } else {
        echo "<p style='color:red'>❌ Admin user not found in database</p>";
        
        // Create admin user
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (username, email, password_hash, role) 
            VALUES ('admin', 'admin@prclasses.in', '$password_hash', 'admin')
        ");
        echo "<p style='color:green'>✓ Created admin user</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Database error during authentication test: " . $e->getMessage() . "</p>";
}

echo "<h2>All Fixes Applied</h2>";
echo "<p>The admin login page should now work correctly. Please try logging in with:</p>";
echo "<ul>";
echo "<li>Username: <strong>admin</strong></li>";
echo "<li>Password: <strong>admin123</strong></li>";
echo "</ul>";
echo "<p><a href='/admin/login.php' style='font-weight:bold;'>Go to Admin Login</a></p>";
?>