<?php
require_once 'config.php';

error_log("Starting dashboard.php");
try {
    $pdo = getDBConnection();
    error_log("Database connection successful");
    
    // Test query
    $test = $pdo->query("SELECT 1");
    error_log("Test query successful");
} catch(Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database connection test failed: " . $e->getMessage());
}
// Check admin login status
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Check session timeout
checkSessionTimeout();

// Generate CSRF token for forms
$csrf_token = generateCSRFToken();

try {
    // Get database connection using PDO
    $pdo = getDBConnection();
    
    // Handle post deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        if (isset($_GET['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            header('Location: dashboard.php');
            exit;
        } else {
            die('Invalid security token');
        }
    }

    // Fetch all blog posts
    $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
    
} catch(Exception $e) {
    error_log("Error in dashboard: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin Dashboard - Taras Dental</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Blog Admin Dashboard</h1>
            <div class="space-x-4">
                <a href="edit-post.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add New Post</a>
                <a href="categories.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Manage Categories</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>

        <?php if (!empty($posts)): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated At</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($post['author']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('Y-m-d H:i:s', strtotime($post['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('Y-m-d H:i:s', strtotime($post['updated_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <a href="dashboard.php?delete=<?php echo $post['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" 
                                       class="text-red-600 hover:text-red-900 delete-post"
                                       data-post-title="<?php echo htmlspecialchars($post['title']); ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-gray-500">No posts found. 
                <a href="edit-post.php" class="text-blue-500 hover:text-blue-600">Create your first post</a>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced delete confirmation
            document.querySelectorAll('.delete-post').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const postTitle = link.getAttribute('data-post-title');
                    if (confirm(`Are you sure you want to delete the post "${postTitle}"? This action cannot be undone.`)) {
                        window.location.href = link.href;
                    }
                });
            });
        });
    </script>
</body>
</html>