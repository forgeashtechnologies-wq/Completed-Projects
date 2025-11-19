<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';

// Create a new admin user or reset existing one
$username = 'admin';
$email = 'admin@prclasses.in';
$password = 'Admin@pr123';
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
        $result = $stmt->execute([$password_hash, $username]);
        echo "Admin password reset successfully!";
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $result = $stmt->execute([$username, $email, $password_hash]);
        echo "New admin user created successfully!";
    }
    
    echo "<p>Username: $username</p>";
    echo "<p>Password: $password</p>";
    echo "<p>You can now <a href='login.php'>login</a> with these credentials.</p>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 