<?php
require_once 'config.php';

// Check admin login
if (!isAdminLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'error' => 'Unknown action'];

try {
    $db = getDBConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['action'] === 'create' && !empty($_POST['category_name'])) {
            $name = sanitizeInput($_POST['category_name']);
            if (strlen($name) < 2) {
                throw new Exception('Category name must be at least 2 characters long');
            }
            
            // Check if category already exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM blog_categories WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Category already exists');
            }
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            $stmt = $db->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
            $response = ['success' => true];
        }
        elseif ($_POST['action'] === 'delete' && isset($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            
            $db->beginTransaction();
            
            // First, remove category associations
            $stmt = $db->prepare("DELETE FROM post_categories WHERE category_id = ?");
            $stmt->execute([$category_id]);
            
            // Then delete the category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$category_id]);
            
            $db->commit();
            $response = ['success' => true];
        }
        elseif ($_POST['action'] === 'edit' && isset($_POST['category_id']) && isset($_POST['name'])) {
            $category_id = intval($_POST['category_id']);
            $name = sanitizeInput($_POST['name']);
            
            if (strlen($name) < 2) {
                throw new Exception('Category name must be at least 2 characters long');
            }
            
            // Check if new name already exists for different category
            $stmt = $db->prepare("SELECT COUNT(*) FROM blog_categories WHERE name = ? AND id != ?");
            $stmt->execute([$name, $category_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Category name already exists');
            }
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            $stmt = $db->prepare("UPDATE blog_categories SET name = ?, slug = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $category_id]);
            $response = ['success' => true];
        }
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $response = ['success' => false, 'error' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);