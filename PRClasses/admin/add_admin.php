<?php
require_once '../includes/config.php';

// Set the new admin details
$new_username = 'newadmin';
$new_email = 'newadmin@prclasses.in';
$new_password = 'SecurePassword123';
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$new_username]);
    if ($stmt->fetch()) {
        echo "Username already exists!";
    } else {
        // Insert new admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $result = $stmt->execute([$new_username, $new_email, $password_hash]);
        
        if ($result) {
            echo "New admin user created successfully!";
            echo "<p>Username: $new_username</p>";
            echo "<p>Password: $new_password</p>";
            echo "<p>You can now <a href='login.php'>login</a> with these credentials.</p>";
        } else {
            echo "Failed to create new admin user.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 