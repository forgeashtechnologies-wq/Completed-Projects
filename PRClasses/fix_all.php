<?php
// updated_fix.php - Place this in your website root directory

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Updated PR Classes Website Fixer</h1>";

// Define the site root path
$root_path = $_SERVER['DOCUMENT_ROOT'];
echo "<p>Document Root: $root_path</p>";

// Step 1: Fix Database Connection with correct parameters
echo "<h2>Step 1: Database Connection Test</h2>";

// Hostinger often uses different database host than localhost
$db_host = 'localhost'; // Try this first
$db_hosts_to_try = ['localhost', '127.0.0.1', 'mysql.hostinger.com'];
$db_name = 'u218412549_prclasses';
$db_user = 'u218412549_prclasses';
$db_pass = 'PR@ashwin123'; // This might be incorrect

// Display form if not submitted
if (!isset($_POST['submit'])) {
    echo '
    <form method="post" action="">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Database Host:</label>
            <input type="text" name="db_host" value="'.$db_host.'" style="width: 300px; padding: 5px;">
            <span>Try: localhost, 127.0.0.1, or mysql.hostinger.com</span>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Database Name:</label>
            <input type="text" name="db_name" value="'.$db_name.'" style="width: 300px; padding: 5px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Database Username:</label>
            <input type="text" name="db_user" value="'.$db_user.'" style="width: 300px; padding: 5px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Database Password:</label>
            <input type="password" name="db_pass" value="" style="width: 300px; padding: 5px;">
            <span>Enter the correct password for your database user</span>
        </div>
        <div>
            <input type="submit" name="submit" value="Test Connection & Fix Website" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
        </div>
    </form>';
    exit;
}

// Get connection parameters from form
$db_host = $_POST['db_host'] ?? 'localhost';
$db_name = $_POST['db_name'] ?? 'u218412549_prclasses';
$db_user = $_POST['db_user'] ?? 'u218412549_prclasses';
$db_pass = $_POST['db_pass'] ?? '';

echo "<p>Testing connection to: <strong>$db_host</strong></p>";
echo "<p>Database: <strong>$db_name</strong></p>";
echo "<p>Username: <strong>$db_user</strong></p>";

// Test connection
$connection_successful = false;

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5, // 5 second timeout
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    echo "<p style='color:green;font-weight:bold;'>✓ MySQL connection successful!</p>";
    $connection_successful = true;
    
    // Update config.php with correct database info if connection successful
    $config_path = $root_path . '/includes/config.php';
    if (file_exists($config_path)) {
        $config_content = file_get_contents($config_path);
        
        // First, add a backup comment showing original values
        if (strpos($config_content, '// ORIGINAL DB CONFIG BACKUP') === false) {
            $config_content = preg_replace(
                '/(\/\/ Database configuration)/i',
                "// ORIGINAL DB CONFIG BACKUP\n// \$db_host = '$db_host';\n// \$db_name = '$db_name';\n// \$db_user = '$db_user';\n// \$db_pass = '[ORIGINAL PASSWORD]';\n\n$1",
                $config_content
            );
        }
        
        // Update database credentials
        $config_content = preg_replace(
            '/\$db_host\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
            "\$db_host = '$db_host';",
            $config_content
        );
        $config_content = preg_replace(
            '/\$db_name\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
            "\$db_name = '$db_name';",
            $config_content
        );
        $config_content = preg_replace(
            '/\$db_user\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
            "\$db_user = '$db_user';",
            $config_content
        );
        $config_content = preg_replace(
            '/\$db_pass\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
            "\$db_pass = '$db_pass';",
            $config_content
        );
        
        file_put_contents($config_path, $config_content);
        echo "<p style='color:green;'>✓ Updated database credentials in config.php</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;font-weight:bold;'>✗ MySQL connection failed: " . $e->getMessage() . "</p>";
    
    // Try alternative hosts automatically if specified host fails
    if ($db_host === 'localhost') {
        echo "<p>Trying alternative database hosts...</p>";
        
        foreach (['127.0.0.1', 'mysql.hostinger.com'] as $alt_host) {
            echo "<p>Testing: <strong>$alt_host</strong>...</p>";
            
            try {
                $dsn = "mysql:host=$alt_host;dbname=$db_name;charset=utf8mb4";
                $pdo = new PDO($dsn, $db_user, $db_pass, $options);
                
                echo "<p style='color:green;font-weight:bold;'>✓ MySQL connection successful with host: $alt_host!</p>";
                
                // Update config.php with successful host
                if (file_exists($config_path)) {
                    $config_content = file_get_contents($config_path);
                    $config_content = preg_replace(
                        '/\$db_host\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
                        "\$db_host = '$alt_host';", 
                        $config_content
                    );
                    file_put_contents($config_path, $config_content);
                    echo "<p style='color:green;'>✓ Updated database host to $alt_host in config.php</p>";
                }
                
                $db_host = $alt_host;
                $connection_successful = true;
                break;
            } catch (PDOException $e) {
                echo "<p style='color:red;'>✗ Connection to $alt_host failed: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // No SQLite fallback - MySQL connection is required
    if (!$connection_successful) {
        echo "<p style='color:red;font-weight:bold;'>MySQL connection is required for this application to work.</p>";
        echo "<p>Please check your database credentials and try again.</p>";
        echo "<p>Make sure the MySQL server is running and the database exists.</p>";
        echo "<p><a href='fix_all.php' style='color:blue;'>Try again</a></p>";
        exit;
    }
}
    }
}

// Step 2: Fix Function Redeclaration
echo "<h2>Step 2: Function Redeclaration Fix</h2>";

$functions_path = $root_path . '/includes/functions.php';
if (file_exists($functions_path)) {
    $functions_content = file_get_contents($functions_path);
    $modified = false;
    
    // Find and fix getDefaultMode() function
    if (preg_match('/function\s+getDefaultMode\s*\(\s*\)\s*\{/', $functions_content) && 
        strpos($functions_content, 'if (!function_exists(\'getDefaultMode\'))') === false) {
        
        $functions_content = preg_replace(
            '/function\s+getDefaultMode\s*\(\s*\)\s*\{([^}]+)\}/',
            'if (!function_exists(\'getDefaultMode\')) {
    function getDefaultMode() {$1}
}',
            $functions_content
        );
        
        $modified = true;
    }
    
    // Add function_exists checks to other common functions
    $functions_to_check = [
        'formatCurrency',
        'calculateDiscountedPrice',
        'truncateText',
        'formatDate',
        'isValidEmail',
        'getYoutubeVideoId'
    ];
    
    foreach ($functions_to_check as $func) {
        if (preg_match('/function\s+' . $func . '\s*\(/', $functions_content) && 
            strpos($functions_content, 'if (!function_exists(\'' . $func . '\'))') === false) {
            
            $functions_content = preg_replace(
                '/function\s+' . $func . '\s*\(([^{]+)\{/',
                'if (!function_exists(\'' . $func . '\')) {
    function ' . $func . '($1{',
                $functions_content
            );
            
            // Add closing brace at the end of the function
            $functions_content = preg_replace(
                '/(function\s+' . $func . '\s*\([^{]+\{(?:[^{}]++|(?R))*+\})\s*(?=function|\?>|$)/',
                '$1
}',
                $functions_content
            );
            
            $modified = true;
        }
    }
    
    if ($modified) {
        file_put_contents($functions_path, $functions_content);
        echo "<p style='color:green;'>✓ Fixed function redeclaration issues</p>";
    } else {
        echo "<p>No function redeclaration issues found or they were already fixed</p>";
    }
} else {
    echo "<p style='color:red;'>✗ functions.php not found at: $functions_path</p>";
}

// Step 3: Fix Include Paths
echo "<h2>Step 3: Fix Include Paths</h2>";

// Process PHP files in key directories
$directories = [
    '/includes',
    '/admin',
    '/' // Root directory
];

$processed_files = 0;
$modified_files = 0;

foreach ($directories as $dir) {
    $full_dir = $root_path . $dir;
    
    if (!is_dir($full_dir)) {
        echo "<p>Directory not found: $dir</p>";
        continue;
    }
    
    echo "<p>Processing directory: $dir</p>";
    
    $files = glob($full_dir . '/*.php');
    
    foreach ($files as $file) {
        if (basename($file) == 'updated_fix.php') continue; // Skip this script
        
        $processed_files++;
        $file_contents = file_get_contents($file);
        $original_contents = $file_contents;
        
        // Fix include paths
        $patterns = [
            '/require_once\s+[\'"]includes\/([^\'"]*)[\'"]/i',
            '/include\s+[\'"]includes\/([^\'"]*)[\'"]/i',
            '/require\s+[\'"]includes\/([^\'"]*)[\'"]/i',
            '/include_once\s+[\'"]includes\/([^\'"]*)[\'"]/i',
        ];
        
        $replacements = [
            'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/includes/$1\'',
            'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/includes/$1\'',
            'require $_SERVER[\'DOCUMENT_ROOT\'] . \'/includes/$1\'',
            'include_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/includes/$1\'',
        ];
        
        // Replace relative paths with absolute paths using $_SERVER['DOCUMENT_ROOT']
        $file_contents = preg_replace($patterns, $replacements, $file_contents);
        
        // Check if file was modified
        if ($file_contents !== $original_contents) {
            file_put_contents($file, $file_contents);
            $modified_files++;
            echo "<p style='color:green;'>✓ Fixed file: " . basename($file) . "</p>";
        }
    }
}

echo "<h3>Path Fix Summary</h3>";
echo "<p>Processed $processed_files files</p>";
echo "<p>Modified $modified_files files</p>";

// Final step: Set proper permissions for key directories
echo "<h2>Step 4: Set Proper Permissions</h2>";

$directories_to_check = [
    '/logs',
    '/uploads',
    '/uploads/gallery',
    '/uploads/profiles',
    '/uploads/marksheets',
    '/database'
];

foreach ($directories_to_check as $dir) {
    $full_dir = $root_path . $dir;
    
    if (!is_dir($full_dir)) {
        mkdir($full_dir, 0755, true);
        echo "<p style='color:green;'>✓ Created directory: $dir</p>";
    }
    
    if (!is_writable($full_dir)) {
        chmod($full_dir, 0755);
        echo "<p style='color:green;'>✓ Set permissions for: $dir</p>";
    } else {
        echo "<p>Directory $dir already has correct permissions</p>";
    }
}

// Final message
echo "<h2>Fix Process Complete!</h2>";
echo "<p>The website should now be functioning correctly.</p>";
echo "<p>If you still encounter issues, please check the error logs in the /logs directory.</p>";
echo "<p><a href='/' style='font-weight:bold;'>Go to Homepage</a></p>";
?>