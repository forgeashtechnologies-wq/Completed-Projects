<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

echo "<h1>Database Update Tool</h1>";

// Function to safely add column if it doesn't exist
function addColumnIfNotExists($pdo, $table, $column, $definition) {
    try {
        // Check if column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM $table LIKE ?");
        $stmt->execute([$column]);
        $exists = $stmt->fetch();
        
        if (!$exists) {
            // Add the column
            $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
            echo "<p>Added column '$column' to table '$table'</p>";
        } else {
            echo "<p>Column '$column' already exists in table '$table'</p>";
        }
        
        return true;
    } catch (PDOException $e) {
        echo "<p>Error adding column '$column' to table '$table': " . $e->getMessage() . "</p>";
        return false;
    }
}

// Add missing columns to tables
if ($pdo) {
    // Add youtube_url column to videos table
    addColumnIfNotExists($pdo, 'videos', 'youtube_url', 'VARCHAR(255)');
    
    // Add mode column to videos, testimonials, and marksheets tables if missing
    addColumnIfNotExists($pdo, 'videos', 'mode', "VARCHAR(50) NOT NULL DEFAULT 'online'");
    addColumnIfNotExists($pdo, 'testimonials', 'mode', "VARCHAR(50) NOT NULL DEFAULT 'online'");
    addColumnIfNotExists($pdo, 'marksheets', 'mode', "VARCHAR(50) NOT NULL DEFAULT 'online'");
    
    echo "<p>Database update completed.</p>";
    echo "<p><a href='index.php'>Return to Admin Dashboard</a></p>";
} else {
    echo "<p>Database connection failed. Cannot update database structure.</p>";
}
?> 