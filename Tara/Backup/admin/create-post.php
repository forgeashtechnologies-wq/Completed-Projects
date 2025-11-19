<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDBConnection();
$error = '';
$success = '';

// Fetch all categories
$stmt = $db->query("SELECT * FROM blog_categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Sanitize input
        $title = sanitizeInput($_POST['title']);
        $content = $_POST['content']; // TinyMCE content
        $author = sanitizeInput($_POST['author']);
        $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
        
        // Handle image upload
        $image_url = null;
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
                    $image_url = 'images/blog/' . $new_filename;
                }
            }
        }

        // Insert post
        $stmt = $db->prepare("
            INSERT INTO blog_posts (title, content, author, image_url, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$title, $content, $author, $image_url]);
        $post_id = $db->lastInsertId();
        
        // Insert categories
        if (!empty($selectedCategories)) {
            $stmt = $db->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
            foreach ($selectedCategories as $category_id) {
                $stmt->execute([$post_id, $category_id]);
            }
        }

        $db->commit();
        $success = "Post created successfully!";
        header("Location: edit-post.php?id=" . $post_id . "&status=created");
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error creating post: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post - <?php echo SITE_URL; ?></title>
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/vphzsdeorpvxsxbatqv5ye6vz4kuxr1rowc1x8hg7p62zza2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Select2 for better category selection -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Your existing styles remain the same */
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
        <h1>Create New Post</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="categories">Categories</label>
                <select id="categories" name="categories[]" class="form-control" multiple>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Featured Image</label>
                <input type="file" id="image" name="image" class="form-control" 
                       accept="image/jpeg,image/png,image/webp">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">Create Post</button>
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

        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinydrive advtable advcode editimage tableofcontents footnotes mergetags autocorrect typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 500,
            menubar: true,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            
            // Image upload settings
            images_upload_url: 'upload.php',
            images_upload_base_path: '/images/blog',
            automatic_uploads: true,
            
            // Table settings
            table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
            
            // Code settings
            codesample_languages: [
                { text: 'HTML/XML', value: 'markup' },
                { text: 'JavaScript', value: 'javascript' },
                { text: 'CSS', value: 'css' },
                { text: 'PHP', value: 'php' },
                { text: 'Python', value: 'python' }
            ],
            
            // Content styling
            style_formats: [
                { title: 'Headers', items: [
                    { title: 'Header 1', format: 'h1' },
                    { title: 'Header 2', format: 'h2' },
                    { title: 'Header 3', format: 'h3' }
                ]},
                { title: 'Inline', items: [
                    { title: 'Bold', format: 'bold' },
                    { title: 'Italic', format: 'italic' }
                ]},
                { title: 'Blocks', items: [
                    { title: 'Blockquote', format: 'blockquote' },
                    { title: 'Code Block', format: 'code' }
                ]}
            ]
        });
    </script>
</body>
</html>