<?php
require_once 'config.php';

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Function to create thumbnail
function createThumbnail($source, $destination, $width = 300) {
    list($src_width, $src_height, $src_type) = getimagesize($source);
    
    // Calculate new height maintaining aspect ratio
    $height = floor($src_height * ($width / $src_width));
    
    // Create new image resource
    $thumb = imagecreatetruecolor($width, $height);
    
    // Create source image resource based on file type
    switch ($src_type) {
        case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($source);
            // Preserve transparency
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            break;
        case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $src_img = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    // Resize image
    imagecopyresampled($thumb, $src_img, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
    
    // Save thumbnail
    $path_parts = pathinfo($destination);
    $extension = strtolower($path_parts['extension']);
    
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($thumb, $destination, 85);
            break;
        case 'png':
            imagepng($thumb, $destination, 8);
            break;
        case 'gif':
            imagegif($thumb, $destination);
            break;
        case 'webp':
            imagewebp($thumb, $destination, 85);
            break;
    }
    
    // Free up memory
    imagedestroy($src_img);
    imagedestroy($thumb);
    
    return true;
}

// Handle file upload
try {
    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    if (!in_array($file['type'], $allowed_types) || !in_array($extension, $allowed_extensions)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }
    
    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File is too large. Maximum size is 5MB.');
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = IMAGES_PATH;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Create thumbnails directory if it doesn't exist
    $thumbs_dir = $upload_dir . 'thumbnails/';
    if (!file_exists($thumbs_dir)) {
        mkdir($thumbs_dir, 0755, true);
    }
    
    // Generate safe filename with timestamp to prevent caching issues
    $filename = uniqid() . '_' . time() . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
    $upload_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Create thumbnail
        $thumb_filename = 'thumb_' . $filename;
        $thumb_path = $thumbs_dir . $thumb_filename;
        createThumbnail($upload_path, $thumb_path);
        
        // Return success response with image URLs
        echo json_encode([
            'location' => '../images/blog/' . $filename,
            'thumbnail' => '../images/blog/thumbnails/' . $thumb_filename
        ]);
    } else {
        throw new Exception('Failed to move uploaded file');
    }
    
} catch (Exception $e) {
    error_log('Upload error: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}