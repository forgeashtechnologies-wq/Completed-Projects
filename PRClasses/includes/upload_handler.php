<?php
/**
 * Secure file upload handler
 */

/**
 * Upload a file securely
 * 
 * @param array $file $_FILES array element
 * @param string $destination Directory to upload to
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array Result with status and message
 */
function uploadFile($file, $destination, $allowed_types = [], $max_size = 5242880) {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return [
            'success' => false,
            'message' => $error_messages[$file['error']] ?? 'Unknown upload error'
        ];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return [
            'success' => false,
            'message' => 'File size exceeds the maximum limit of ' . formatBytes($max_size)
        ];
    }
    
    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    if (!empty($allowed_types) && !in_array($mime_type, $allowed_types)) {
        return [
            'success' => false,
            'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types)
        ];
    }
    
    // Create destination directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Generate a unique filename
    $filename = bin2hex(random_bytes(8)) . '_' . time() . '_' . basename($file['name']);
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $filename); // Remove special characters
    $filepath = $destination . '/' . $filename;
    
    // Move the file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Set proper permissions
        chmod($filepath, 0644);
        
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'filename' => $filename,
            'filepath' => $filepath
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ];
    }
} 