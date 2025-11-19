<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep this 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

error_log("Starting admin/index.php");




try {
    require_once 'config.php';
    error_log("Config file loaded");

    // Check if already logged in
    if (isAdminLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }

    $error = '';
    $csrf_token = validateCSRFToken();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Processing login attempt");

        if (!validateCSRFToken()) {
            $error = 'Security check failed. Please try again.';
            error_log("CSRF validation failed");
        } else {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $_POST['password'] ?? '';

            if ($username === ADMIN_USERNAME && verifyPassword($password)) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['last_activity'] = time();
                error_log("Admin login successful for user: " . $username);
                
                header('Location: dashboard.php');
                exit;
            } else {
                error_log("Failed login attempt for username: " . $username);
                $error = 'Invalid username or password';
            }
        }
    }
} catch (Exception $e) {
    error_log("Error in admin/index.php: " . $e->getMessage());
    $error = "An error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo htmlspecialchars(SITE_URL); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold text-center mb-6">Admin Login</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Login
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="../" class="text-sm text-indigo-600 hover:text-indigo-500">Return to Website</a>
            </div>
        </div>
    </div>
</body>
</html>