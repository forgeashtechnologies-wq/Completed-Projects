<?php
require_once '../includes/config.php';

echo "<h1>Admin Login Helper</h1>";

try {
    // Get all admin users
    $stmt = $pdo->query("SELECT id, username, role FROM users WHERE role = 'admin'");
    $admins = $stmt->fetchAll();
    
    echo "<h2>Available Admin Accounts</h2>";
    echo "<ul>";
    foreach ($admins as $admin) {
        echo "<li>ID: {$admin['id']}, Username: <strong>{$admin['username']}</strong></li>";
    }
    echo "</ul>";
    
    echo "<h2>Login Instructions</h2>";
    echo "<p>Please use the exact username as shown above (case-sensitive) with your password.</p>";
    echo "<p>If you need to reset your password, use the <a href='reset_admin.php'>reset admin</a> script.</p>";
    
    // Create a direct login form with the correct username
    if (count($admins) > 0) {
        $first_admin = $admins[0]['username'];
        echo "<h2>Quick Login Form</h2>";
        echo "<form action='login.php' method='post'>";
        echo "<input type='hidden' name='username' value='{$first_admin}'>";
        echo "<p>Username: <strong>{$first_admin}</strong> (pre-filled)</p>";
        echo "<p>Password: <input type='password' name='password' required></p>";
        echo "<p><button type='submit'>Login</button></p>";
        echo "</form>";
    }
    
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?> 