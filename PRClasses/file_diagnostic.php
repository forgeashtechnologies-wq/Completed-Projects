<?php
// file_diagnostic.php - Check file structure and permissions
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PR Classes Website File Structure Diagnostic</h1>";

$root_path = $_SERVER['DOCUMENT_ROOT'];
echo "<p>Document Root: $root_path</p>";

// Function to check directory and permissions
function check_directory($path, $required = true) {
    if (is_dir($path)) {
        echo "<p style='color:green'>✓ Directory exists: $path</p>";
        if (is_writable($path)) {
            echo "<p style='color:green'>✓ Directory is writable</p>";
        } else {
            echo "<p style='color:red'>✗ Directory is not writable</p>";
            echo "<p>Command to fix: <code>chmod 755 $path</code></p>";
        }
    } else {
        if ($required) {
            echo "<p style='color:red'>✗ Required directory missing: $path</p>";
            echo "<p>Command to create: <code>mkdir -p $path</code></p>";
        } else {
            echo "<p style='color:orange'>! Optional directory missing: $path</p>";
        }
    }
}

// Function to check file and permissions
function check_file($path, $required = true) {
    if (file_exists($path)) {
        echo "<p style='color:green'>✓ File exists: $path</p>";
        if (is_readable($path)) {
            echo "<p style='color:green'>✓ File is readable</p>";
        } else {
            echo "<p style='color:red'>✗ File is not readable</p>";
            echo "<p>Command to fix: <code>chmod 644 $path</code></p>";
        }
        if (is_writable($path)) {
            echo "<p style='color:green'>✓ File is writable</p>";
        } else {
            echo "<p style='color:orange'>! File is not writable (may be needed for some operations)</p>";
            echo "<p>Command to fix: <code>chmod 664 $path</code></p>";
        }
    } else {
        if ($required) {
            echo "<p style='color:red'>✗ Required file missing: $path</p>";
        } else {
            echo "<p style='color:orange'>! Optional file missing: $path</p>";
        }
    }
}

echo "<h2>Key Directories</h2>";
check_directory($root_path . '/includes');
check_directory($root_path . '/admin');
check_directory($root_path . '/database');
check_directory($root_path . '/logs');
check_directory($root_path . '/images', false);

echo "<h2>Critical Files</h2>";
check_file($root_path . '/includes/config.php');
check_file($root_path . '/includes/functions.php');
check_file($root_path . '/includes/schema_sqlite.php');
check_file($root_path . '/admin/login.php');
check_file($root_path . '/database/database.sqlite', false);

echo "<h2>Fix Missing Files</h2>";
echo "<p>Click the buttons below to create any missing required files:</p>";

// Create site.webmanifest
echo "<button onclick=\"location.href='create_webmanifest.php'\">Create site.webmanifest</button>";

// Create error.php if missing
if (!file_exists($root_path . '/error.php')) {
    echo "<button onclick=\"createErrorPage()\">Create error.php</button>";
    
    echo "<script>
    function createErrorPage() {
        fetch('create_error_page.php')
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
    </script>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Run <a href='fix_database.php'>fix_database.php</a> to ensure the database is properly set up</li>";
echo "<li>Run <a href='admin_login_fix.php'>admin_login_fix.php</a> to fix admin login issues</li>";
echo "<li>Try the debug login page: <a href='admin/debug_login.php'>admin/debug_login.php</a></li>";
echo "</ol>";
?> 