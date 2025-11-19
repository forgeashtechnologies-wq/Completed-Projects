<?php
/**
 * Admin authentication functions
 */

// Prevent direct access
if (!defined('CONFIG_INCLUDED')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Check if admin is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_username']) && !empty($_SESSION['admin_username']);
}

/**
 * Redirect to admin login if not logged in
 * 
 * @return void
 */
function check_admin_login() {
    if (!is_admin_logged_in()) {
        // Use absolute path for redirection
        header('Location: ' . SITE_URL . 'admin/login.php');
        exit;
    }
}

/**
 * Authenticate admin user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return array Authentication result
 */
function authenticate_admin($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return [
                'success' => true,
                'user' => $user
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid username or password'
        ];
    } catch (PDOException $e) {
        error_log('Admin authentication error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred during authentication'
        ];
    }
}

/**
 * Log out admin user
 * 
 * @return void
 */
function admin_logout() {
    // Unset admin session variables
    if (isset($_SESSION['admin_id'])) unset($_SESSION['admin_id']);
    if (isset($_SESSION['admin_username'])) unset($_SESSION['admin_username']);
    
    // Optional: regenerate session ID for security
    session_regenerate_id(true);
}
?> 