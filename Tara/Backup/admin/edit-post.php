<?php
require_once 'config.php';

// Check admin login
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDBConnection();
$isNewPost = true;
$post = [
    'title' => '',
    'content' => '',
    'author' => '',
    'image_url' => '',
];
$currentCategories = [];

// Get the post ID from URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Only fetch post if we have an ID and it's not a new post
if ($post_id > 0) {
    // Fetch post details
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $fetchedPost = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fetchedPost) {
        $isNewPost = false;
        $post = $fetchedPost;
        
        // Fetch current post categories
        $stmt = $db->prepare("
            SELECT category_id 
            FROM post_categories 
            WHERE post_id = ?
        ");
        $stmt->execute([$post_id]);
        $currentCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Fetch all categories
$stmt = $db->query("SELECT * FROM blog_categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Sanitize input
        $title = sanitizeInput($_POST['title']);
        $content = $_POST['content']; // TinyMCE content
        $author = sanitizeInput($_POST['author']);
        $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
        
        // Handle image upload
        $image_url = $isNewPost ? null : $post['image_url'];
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $upload_dir = IMAGES_PATH;
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($file_ext, $allowed_types)) {
                $new_filename = uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if (!$isNewPost && $post['image_url'] && file_exists('../' . $post['image_url'])) {
                        unlink('../' . $post['image_url']);
                    }
                    $image_url = 'images/blog/' . $new_filename;
                }
            }
        }

        if ($isNewPost) {
            // Insert new post
            $stmt = $db->prepare("
                INSERT INTO blog_posts (title, content, author, image_url, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$title, $content, $author, $image_url]);
            $post_id = $db->lastInsertId();
        } else {
            // Update existing post
            $stmt = $db->prepare("
                UPDATE blog_posts 
                SET title = ?, content = ?, author = ?, image_url = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $author, $image_url, $post_id]);
        }
        
        // Handle categories
        if (!$isNewPost) {
            // Remove existing categories for updates
            $stmt = $db->prepare("DELETE FROM post_categories WHERE post_id = ?");
            $stmt->execute([$post_id]);
        }
        
        // Add categories
        if (!empty($selectedCategories)) {
            $stmt = $db->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
            foreach ($selectedCategories as $category_id) {
                $stmt->execute([$post_id, $category_id]);
            }
        }

        $db->commit();
        header('Location: edit-post.php?id=' . $post_id . '&status=success');
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error " . ($isNewPost ? "creating" : "updating") . " post: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isNewPost ? 'Create New' : 'Edit'; ?> Blog Post - <?php echo SITE_URL; ?></title>
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/vphzsdeorpvxsxbatqv5ye6vz4kuxr1rowc1x8hg7p62zza2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Your existing styles remain unchanged */
        .container { max-width: 1200px; margin: 2rem auto; padding: 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-control { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        .select2-container { width: 100% !important; }
        .btn-primary { background-color: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        .btn-secondary { background-color: #6c757d; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-left: 1rem; }
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $isNewPost ? 'Create New' : 'Edit'; ?> Blog Post</h1>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success">
                Post <?php echo $isNewPost ? 'created' : 'updated'; ?> successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="categories">Categories</label>
                <select id="categories" name="categories[]" class="form-control" multiple>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"
                                <?php echo in_array($category['id'], $currentCategories) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" class="form-control" 
                       value="<?php echo htmlspecialchars($post['author']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control">
                    <?php echo htmlspecialchars($post['content']); ?>
                </textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Featured Image</label>
                <?php if (!$isNewPost && $post['image_url']): ?>
                    <div class="current-image">
                        <img src="../<?php echo htmlspecialchars($post['image_url']); ?>" 
                             alt="Current featured image" style="max-width: 200px; margin: 10px 0;">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" class="form-control" 
                       accept="image/jpeg,image/png,image/webp">
                <?php if (!$isNewPost): ?>
                    <small class="form-text text-muted">Leave empty to keep current image</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">
                    <?php echo $isNewPost ? 'Create' : 'Update'; ?> Post
                </button>
                <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <script>
        // Initialize Select2 for categories
        $(document).ready(function() {
            $('#categories').select2({
                placeholder: 'Select categories',
                allowClear: true
            });
        });

        // Initialize TinyMCE with your existing configuration
        tinymce.init({
    selector: '#content',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 500,
    menubar: true,
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    
    // Updated image upload settings
    images_upload_handler: function (blobInfo, progress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload.php');

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = function() {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                const json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                resolve(json.location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        });
    },
    
    // Additional settings
    image_dimensions: false,
    object_resizing: true,
    resize_img_proportional: true,
    
    // Content styling
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
});
    </script>
</body>
</html>