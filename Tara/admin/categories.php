<?php
require_once 'config.php';

// Check admin login
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDBConnection();
$error = '';
$success = '';

// Handle category creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'create') {
            $name = sanitizeInput($_POST['name']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            $stmt = $db->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
            $success = "Category created successfully!";
        }
        elseif ($_POST['action'] === 'delete' && isset($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            
            // First, remove category associations
            $stmt = $db->prepare("DELETE FROM post_categories WHERE category_id = ?");
            $stmt->execute([$category_id]);
            
            // Then delete the category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $success = "Category deleted successfully!";
        }
        elseif ($_POST['action'] === 'edit' && isset($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            $name = sanitizeInput($_POST['name']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            $stmt = $db->prepare("UPDATE blog_categories SET name = ?, slug = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $category_id]);
            $success = "Category updated successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all categories
$stmt = $db->query("SELECT c.*, COUNT(pc.post_id) as post_count 
                    FROM blog_categories c 
                    LEFT JOIN post_categories pc ON c.id = pc.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - <?php echo SITE_URL; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Categories</h1>
            <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Back to Dashboard</a>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Add New Category Form -->
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-bold mb-4">Add New Category</h2>
            <form method="POST" class="flex gap-4 items-end">
                <input type="hidden" name="action" value="create">
                <div class="flex-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Category Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="text" id="name" name="name" required>
                </div>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Add Category
                </button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($categories as $category): ?>
                        <tr id="category-row-<?php echo $category['id']; ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="category-name"><?php echo htmlspecialchars($category['name']); ?></span>
                                <form class="hidden category-edit-form" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>"
                                           class="shadow border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($category['slug']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $category['post_count']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button onclick="toggleEdit(<?php echo $category['id']; ?>)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4 edit-btn">
                                    Edit
                                </button>
                                <form method="POST" class="inline delete-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this category? This will remove it from all posts.')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function toggleEdit(categoryId) {
        const row = document.getElementById(`category-row-${categoryId}`);
        const nameSpan = row.querySelector('.category-name');
        const editForm = row.querySelector('.category-edit-form');
        const editBtn = row.querySelector('.edit-btn');

        if (editForm.classList.contains('hidden')) {
            // Show edit form
            nameSpan.classList.add('hidden');
            editForm.classList.remove('hidden');
            editBtn.textContent = 'Save';
            
            // Focus on input
            const input = editForm.querySelector('input[name="name"]');
            input.focus();
            input.select();
            
            // Submit form when pressing Enter
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    editForm.submit();
                }
            });
        } else {
            // Submit the form
            editForm.submit();
        }
    }

    // Add event listeners to all delete forms
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this category? This will remove it from all posts.')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>