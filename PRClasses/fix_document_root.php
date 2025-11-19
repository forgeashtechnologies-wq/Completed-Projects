<?php
// fix_document_root.php - Fix document root path issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PR Classes Document Root Path Fix</h1>";

// Current document root
$current_root = $_SERVER['DOCUMENT_ROOT'];
echo "<p>Current DOCUMENT_ROOT: $current_root</p>";

// Actual project root directory
$actual_root = dirname(__FILE__);
echo "<p>Actual project root: $actual_root</p>";

// Check if they match
if ($current_root === $actual_root) {
    echo "<p style='color:green'>✓ Document root is correctly set</p>";
} else {
    echo "<p style='color:red'>✗ Document root is incorrectly set</p>";
    echo "<p>This is likely causing issues with file inclusions like the footer.</p>";
    
    echo "<h2>Solution</h2>";
    echo "<p>Replace all instances of:</p>";
    echo "<pre>\$root_path = \$_SERVER['DOCUMENT_ROOT'];</pre>";
    echo "<p>with:</p>";
    echo "<pre>\$root_path = dirname(__FILE__);</pre>";
    echo "<p>Or for files in subdirectories:</p>";
    echo "<pre>\$root_path = dirname(dirname(__FILE__)); // Go up one level</pre>";
    
    echo "<h2>Quick Fix for Index and Course Pages</h2>";
    echo "<p>The following will update the root path in the main pages:</p>";
    
    // Fix index.php
    $index_file = $actual_root . '/index.php';
    if (file_exists($index_file)) {
        $index_content = file_get_contents($index_file);
        $index_content = str_replace("$root_path = $_SERVER['DOCUMENT_ROOT'];", "$root_path = dirname(__FILE__);", $index_content);
        if (file_put_contents($index_file, $index_content)) {
            echo "<p style='color:green'>✓ Fixed index.php</p>";
        } else {
            echo "<p style='color:red'>✗ Could not update index.php</p>";
        }
    }
    
    // Fix courses_details.php
    $courses_file = $actual_root . '/courses_details.php';
    if (file_exists($courses_file)) {
        $courses_content = file_get_contents($courses_file);
        $courses_content = str_replace("$root_path = $_SERVER['DOCUMENT_ROOT'];", "$root_path = dirname(__FILE__);", $courses_content);
        if (file_put_contents($courses_file, $courses_content)) {
            echo "<p style='color:green'>✓ Fixed courses_details.php</p>";
        } else {
            echo "<p style='color:red'>✗ Could not update courses_details.php</p>";
        }
    }
    
    // Fix contact.php
    $contact_file = $actual_root . '/contact.php';
    if (file_exists($contact_file)) {
        $contact_content = file_get_contents($contact_file);
        $contact_content = str_replace("$root_path = $_SERVER['DOCUMENT_ROOT'];", "$root_path = dirname(__FILE__);", $contact_content);
        if (file_put_contents($contact_file, $contact_content)) {
            echo "<p style='color:green'>✓ Fixed contact.php</p>";
        } else {
            echo "<p style='color:red'>✗ Could not update contact.php</p>";
        }
    }
    
    // Fix header.php
    $header_file = $actual_root . '/includes/header.php';
    if (file_exists($header_file)) {
        $header_content = file_get_contents($header_file);
        $header_content = str_replace("$root_path = $_SERVER['DOCUMENT_ROOT'];", "$root_path = dirname(dirname(__FILE__));", $header_content);
        if (file_put_contents($header_file, $header_content)) {
            echo "<p style='color:green'>✓ Fixed includes/header.php</p>";
        } else {
            echo "<p style='color:red'>✗ Could not update includes/header.php</p>";
        }
    }
}

echo "<h2>Testing Footer Inclusion</h2>";
$footer_path = $actual_root . '/includes/footer.php';
if (file_exists($footer_path)) {
    echo "<p style='color:green'>✓ Footer file exists at: $footer_path</p>";
} else {
    echo "<p style='color:red'>✗ Footer file not found at: $footer_path</p>";
}

echo "<p><a href='index.php'>Return to homepage</a> to test if the fix worked.</p>";
?>