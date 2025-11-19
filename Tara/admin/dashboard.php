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

// Initialize variables
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

try {
    // Get database connection using PDO
    $pdo = getDBConnection();
    
    // Base query
    $query = "FROM blog_posts p";
    $countQuery = "SELECT COUNT(*) FROM blog_posts p";
    $params = [];
    $whereConditions = [];
    
    // Add category filter if selected
    if ($category_id > 0) {
        $query .= " JOIN post_categories pc ON p.id = pc.post_id";
        $countQuery .= " JOIN post_categories pc ON p.id = pc.post_id";
        $whereConditions[] = "pc.category_id = ?";
        $params[] = $category_id;
    }
    
    // Add search condition if provided
    if (!empty($search)) {
        $whereConditions[] = "(p.title LIKE ? OR p.content LIKE ? OR p.author LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
        $countQuery .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Get total count for pagination
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total_posts = $stmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
    // Fetch posts with pagination
    $query = "SELECT p.* " . $query . " ORDER BY p.created_at DESC LIMIT $offset, $per_page";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
    
    // Fetch all categories for filter dropdown
    $stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Blog Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">Manage your blog content</p>
            </div>
            <div class="space-x-3">
                <a href="edit-post.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New Post
                </a>
                <a href="categories.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-tags mr-2"></i> Manage Categories
                </a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded inline-flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Search and Filter Section -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <form method="GET" action="dashboard.php" class="flex flex-wrap gap-4">
                <!-- Search Box -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Posts</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search by title, content or author"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="w-full md:w-auto">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Category</label>
                    <select id="category_id" name="category_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                                (<?php echo isset($category['post_count']) ? $category['post_count'] : '0'; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-redo mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Posts Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <i class="fas fa-file-alt text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Posts</p>
                        <p class="text-2xl font-bold"><?php echo $total_posts; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-tags text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Categories</p>
                        <p class="text-2xl font-bold"><?php echo count($categories); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <i class="fas fa-clock text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Latest Update</p>
                        <p class="text-lg font-bold">
                            <?php 
                            echo !empty($posts) ? date('M d, Y', strtotime($posts[0]['updated_at'])) : 'No posts yet'; 
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($posts)): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($posts as $post): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                            <?php if (!empty($post['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="" class="h-10 w-10 rounded-md object-cover">
                                            <?php else: ?>
                                                <i class="fas fa-file-alt text-gray-400"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </div>
                                            <?php 
                                            // Get post categories
                                            $stmt = $pdo->prepare("SELECT c.name FROM blog_categories c 
                                                                  JOIN post_categories pc ON c.id = pc.category_id 
                                                                  WHERE pc.post_id = ?");
                                            $stmt->execute([$post['id']]);
                                            $postCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                            if (!empty($postCategories)): ?>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <?php foreach ($postCategories as $catName): ?>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"><?php echo htmlspecialchars($catName); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-user text-gray-400 mr-2"></i>
                                        <?php echo htmlspecialchars($post['author']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                        </span>
                                        <span class="block text-xs text-gray-400">
                                            <?php echo date('h:i A', strtotime($post['created_at'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-edit text-gray-400 mr-2"></i>
                                            <?php echo date('M d, Y', strtotime($post['updated_at'])); ?>
                                        </span>
                                        <span class="block text-xs text-gray-400">
                                            <?php echo date('h:i A', strtotime($post['updated_at'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" 
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-900 mr-3">
                                       <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="#" onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars(addslashes($post['title'])); ?>'); return false;"
                                       class="inline-flex items-center text-red-600 hover:text-red-900">
                                       <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination Controls -->
                <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                            <span class="font-medium"><?php echo min($offset + $per_page, $total_posts); ?></span> of 
                            <span class="font-medium"><?php echo $total_posts; ?></span> results
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category_id > 0 ? '&category_id=' . $category_id : ''; ?>" 
                                   class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                   <i class="fas fa-chevron-left mr-1"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category_id > 0 ? '&category_id=' . $category_id : ''; ?>" 
                                   class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                   Next <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-md rounded-lg p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-file-alt text-5xl"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-700 mb-2">No posts found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first blog post</p>
                <a href="edit-post.php" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                    <i class="fas fa-plus mr-2"></i> Create New Post
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmDelete(postId, postTitle) {
            if (confirm('Are you sure you want to delete "' + postTitle + '"? This action cannot be undone.')) {
                window.location.href = 'delete-post.php?id=' + postId;
            }
        }
        
        // Add event listeners for filter changes
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const searchInput = document.getElementById('search');
            
            // Auto-submit on category change
            categorySelect.addEventListener('change', function() {
                if (this.value !== '0' || searchInput.value.trim() !== '') {
                    this.form.submit();
                }
            });
        });
    </script>
</body>
</html>