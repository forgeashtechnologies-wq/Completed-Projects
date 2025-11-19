<?php
session_start();

// Use absolute path to config.php
$config_path = realpath(dirname(__FILE__) . '/../admin/config.php');
if (!$config_path || !file_exists($config_path)) {
    die("Error: Could not find config.php at: " . dirname(__FILE__) . '/../admin/config.php');
}
require_once($config_path);

// Log the config path
error_log("Loading config from: " . $config_path);

// Check if user is admin
if (!isAdminLoggedIn()) {
    die("Unauthorized access");
}

// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');

// Verify database constants
$required_constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($required_constants as $constant) {
    if (!defined($constant)) {
        $error = "Error: Required constant $constant is not defined";
        error_log($error);
        die($error);
    }
}

// Log database configuration (sanitized)
error_log("Database Host: " . DB_HOST);
error_log("Database Name: " . DB_NAME);
error_log("Database User: " . DB_USER);

// Set OpenAI API key
if (empty($_POST['openai_key'])) {
    die("Error: OpenAI API key is required");
}
putenv('OPENAI_API_KEY=' . $_POST['openai_key']);

// Disable output buffering
if (ob_get_level()) ob_end_clean();
ob_implicit_flush(true);

try {
    require_once('cloud_blog_poster.php');
    $poster = new CloudBlogPoster();
    
    // Filter topics if specified
    if (!empty($_POST['topics'])) {
        $selected_topics = $_POST['topics'];
        $all_topics = $poster->getTopics();
        error_log("Selected topics: " . print_r($selected_topics, true));
        error_log("Available topics: " . print_r(array_keys($all_topics), true));
        $poster->setTopics(array_intersect_key($all_topics, array_flip($selected_topics)));
    }
    
    $results = $poster->generatePosts();
    
    // Output results
    foreach ($results as $topic => $result) {
        if ($result['status'] === 'success') {
            echo "✓ Successfully created {$result['title']} (ID: {$result['post_id']})\n";
        } else {
            echo "✗ Failed to create post for {$topic}: {$result['message']}\n";
        }
    }
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString();
    error_log($error_message);
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
