<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = (int)$_GET['id'];
    
    try {
        $conn = getDBConnection();
        
        // First, get the image URL if exists
        $stmt = $conn->prepare("SELECT image_url FROM blog_posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete the post
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$postId]);
        
        // Delete associated image if exists
        if ($post && $post['image_url']) {
            $imagePath = IMAGES_PATH . basename($post['image_url']);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        header('Location: dashboard.php?message=Post deleted successfully');
    } catch (PDOException $e) {
        error_log("Delete failed: " . $e->getMessage());
        header('Location: dashboard.php?error=Failed to delete post');
    }
} else {
    header('Location: dashboard.php?error=Invalid post ID');
}
exit();
?>