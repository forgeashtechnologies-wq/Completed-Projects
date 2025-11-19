<?php
/**
 * Health check script
 * 
 * This script checks the health of the application and returns a JSON response.
 * It can be used by monitoring tools to check if the application is running properly.
 */

// Set content type to JSON
header('Content-Type: application/json');

// Start time
$start_time = microtime(true);

// Health status
$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check database connection
try {
    require_once __DIR__ . '/../includes/config.php';
    
    $stmt = $pdo->query('SELECT 1');
    $result = $stmt->fetch();
    
    $health['checks']['database'] = [
        'status' => 'ok',
        'message' => 'Database connection successful'
    ];
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['checks']['database'] = [
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
}

// Check file system
$required_dirs = [
    __DIR__ . '/../logs',
    __DIR__ . '/../uploads',
    __DIR__ . '/../cache'
];

foreach ($required_dirs as $dir) {
    $dir_name = basename($dir);
    
    if (!is_dir($dir)) {
        $health['status'] = 'warning';
        $health['checks']['filesystem_' . $dir_name] = [
            'status' => 'warning',
            'message' => "Directory $dir_name does not exist"
        ];
        continue;
    }
    
    if (!is_writable($dir)) {
        $health['status'] = 'warning';
        $health['checks']['filesystem_' . $dir_name] = [
            'status' => 'warning',
            'message' => "Directory $dir_name is not writable"
        ];
        continue;
    }
    
    $health['checks']['filesystem_' . $dir_name] = [
        'status' => 'ok',
        'message' => "Directory $dir_name is writable"
    ];
}

// Check PHP version
$required_php_version = '7.4.0';
$current_php_version = PHP_VERSION;

if (version_compare($current_php_version, $required_php_version, '<')) {
    $health['status'] = 'warning';
    $health['checks']['php_version'] = [
        'status' => 'warning',
        'message' => "PHP version $current_php_version is below the required version $required_php_version"
    ];
} else {
    $health['checks']['php_version'] = [
        'status' => 'ok',
        'message' => "PHP version $current_php_version meets requirements"
    ];
}

// Add execution time
$health['execution_time'] = round((microtime(true) - $start_time) * 1000, 2) . 'ms';

// Output health status
echo json_encode($health, JSON_PRETTY_PRINT); 