<?php
$page_title = "Admin Error";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Get error type
$error_type = isset($_GET['error']) ? $_GET['error'] : 'unknown';

// Error messages
$error_messages = [
    'db' => 'Database connection failed. Please check your database configuration.',
    'permission' => 'You do not have permission to access this page.',
    'exception' => 'An exception occurred while processing your request.',
    'fatal' => 'A fatal error occurred while processing your request.',
    'unknown' => 'An unknown error occurred.'
];

$error_message = $error_messages[$error_type] ?? $error_messages['unknown'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PR Classes Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
        }
        .error-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            margin-top: 20px;
        }
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="error-container text-center">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h1 class="mb-4">Admin Error</h1>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-3">
                            <i class="fas fa-home me-2"></i>Admin Dashboard
                        </a>
                        <a href="login.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 