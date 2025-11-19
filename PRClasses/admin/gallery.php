<?php
require_once '../includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new gallery item
    if (isset($_POST['add_gallery_item'])) {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $type = sanitize($_POST['type']);
        $path = '';
        
        // Handle file upload for image type
        if ($type === 'image') {
            // Check if file was uploaded without errors
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $upload_dir = '../uploads/gallery/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $file_extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $target_file = $upload_dir . $filename;
                
                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                    $path = 'uploads/gallery/' . $filename;
                } else {
                    display_error_message("Failed to upload image.");
                }
            } else {
                display_error_message("Please select an image to upload.");
            }
        } else if ($type === 'video') {
            // For video type, just store the YouTube URL
            $path = sanitize($_POST['youtube_url']);
        }
        
        // Insert gallery item into database
        if (!empty($path)) {
            $stmt = $pdo->prepare("INSERT INTO gallery (title, description, type, path) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$title, $description, $type, $path])) {
                display_success_message("Gallery item added successfully!");
            } else {
                display_error_message("Failed to add gallery item.");
            }
        }
    }
    
    // Delete gallery item
    if (isset($_POST['delete_gallery_item'])) {
        $id = (int) $_POST['id'];
        
        // Get gallery item details to delete the file if it's an image
        $stmt = $pdo->prepare("SELECT type, path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $gallery_item = $stmt->fetch();
        
        // Delete the gallery item record
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // Delete the image file if it exists and is an image type
            if ($gallery_item && $gallery_item['type'] === 'image' && !empty($gallery_item['path']) && file_exists('../' . $gallery_item['path'])) {
                unlink('../' . $gallery_item['path']);
            }
            
            display_success_message("Gallery item deleted successfully!");
        } else {
            display_error_message("Failed to delete gallery item.");
        }
    }
}

// Get gallery items based on type filter
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';

$query = "SELECT * FROM gallery";
if ($type_filter !== 'all') {
    $query .= " WHERE type = '" . sanitize($type_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->query($query);
$gallery_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h2 {
            margin: 0;
        }
        .btn {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-primary {
            background-color: #2196F3;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .form-container {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
        }
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container a {
            margin-right: 10px;
            padding: 5px 10px;
            text-decoration: none;
            color: #333;
            border-radius: 3px;
        }
        .filter-container a.active {
            background-color: #4CAF50;
            color: white;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .gallery-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        .gallery-item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-item-video {
            width: 100%;
            height: 200px;
        }
        .gallery-item-info {
            padding: 10px;
        }
        .gallery-item-title {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .gallery-item-description {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .gallery-item-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .success-message {
            padding: 10px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error-message {
            padding: 10px;
            background-color: #f2dede;
            color: #a94442;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .type-image {
            background-color: #2196F3;
            color: white;
        }
        .type-video {
            background-color: #f44336;
            color: white;
        }
        #youtube-url-container {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Gallery Management</h2>
            <a href="index.php" class="btn">Back to Dashboard</a>
        </div>
        
        <?php if ($success_message = get_success_message()): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message = get_error_message()): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h3>Add New Gallery Item</h3>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required onchange="toggleFileUpload()">
                        <option value="image">Image</option>
                        <option value="video">YouTube Video</option>
                    </select>
                </div>
                
                <div class="form-group" id="image-file-container">
                    <label for="image_file">Image File</label>
                    <input type="file" id="image_file" name="image_file" accept="image/*">
                </div>
                
                <div class="form-group" id="youtube-url-container">
                    <label for="youtube_url">YouTube URL</label>
                    <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX">
                </div>
                
                <button type="submit" name="add_gallery_item" class="btn">Add Gallery Item</button>
            </form>
        </div>
        
        <div class="filter-container">
            <strong>Filter by type:</strong>
            <a href="?type=all" class="<?php echo $type_filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="?type=image" class="<?php echo $type_filter === 'image' ? 'active' : ''; ?>">Images</a>
            <a href="?type=video" class="<?php echo $type_filter === 'video' ? 'active' : ''; ?>">Videos</a>
        </div>
        
        <?php if (count($gallery_items) > 0): ?>
            <div class="gallery-grid">
                <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-item">
                        <?php if ($item['type'] === 'image'): ?>
                            <img src="../<?php echo htmlspecialchars($item['path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="gallery-item-image">
                        <?php else: ?>
                            <?php 
                            // Extract video ID from YouTube URL
                            $video_id = '';
                            $url = $item['path'];
                            
                            if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $url, $matches)) {
                                $video_id = $matches[1];
                            } else if (preg_match('/youtu\.be\/([\w-]+)/', $url, $matches)) {
                                $video_id = $matches[1];
                            }
                            ?>
                            
                            <?php if ($video_id): ?>
                                <iframe class="gallery-item-video" src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="gallery-item-video" style="display: flex; align-items: center; justify-content: center; background-color: #f5f5f5;">
                                    <p>Invalid YouTube URL</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="gallery-item-info">
                            <span class="type-badge type-<?php echo $item['type']; ?>">
                                <?php echo ucfirst($item['type']); ?>
                            </span>
                            <h3 class="gallery-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="gallery-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                        
                        <div class="gallery-item-actions">
                            <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this gallery item?');">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_gallery_item" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No gallery items found.</p>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleFileUpload() {
            const type = document.getElementById('type').value;
            const imageContainer = document.getElementById('image-file-container');
            const youtubeContainer = document.getElementById('youtube-url-container');
            
            if (type === 'image') {
                imageContainer.style.display = 'block';
                youtubeContainer.style.display = 'none';
            } else {
                imageContainer.style.display = 'none';
                youtubeContainer.style.display = 'block';
            }
        }
    </script>
</body>
</html>