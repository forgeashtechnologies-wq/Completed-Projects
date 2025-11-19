<?php
/**
 * SQLite to MySQL Migration Script
 * This script migrates data from SQLite to MySQL database
 */

// Include necessary files
require_once '../includes/config.php';

// Set up page
$page_title = "Database Migration";
include_once '../includes/admin_auth.php';

// Function to get SQLite connection
function getSQLiteConnection() {
    try {
        $sqlite_pdo = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
        $sqlite_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlite_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $sqlite_pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Function to get MySQL connection
function getMySQLConnection() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    try {
        $mysql_pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
        $mysql_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $mysql_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $mysql_pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Function to migrate a table
function migrateTable($sqlite_pdo, $mysql_pdo, $table_name) {
    $results = [];
    
    try {
        // Check if table exists in SQLite
        $stmt = $sqlite_pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table_name}'");
        if ($stmt->fetchColumn() === false) {
            $results[] = "Table {$table_name} does not exist in SQLite database.";
            return $results;
        }
        
        // Get all data from SQLite table
        $stmt = $sqlite_pdo->query("SELECT * FROM {$table_name}");
        $rows = $stmt->fetchAll();
        
        if (empty($rows)) {
            $results[] = "No data found in SQLite {$table_name} table.";
            return $results;
        }
        
        // Get column names
        $columns = array_keys($rows[0]);
        
        // Begin transaction in MySQL
        $mysql_pdo->beginTransaction();
        
        // Clear existing data in MySQL table
        $mysql_pdo->exec("TRUNCATE TABLE {$table_name}");
        
        // Prepare insert statement
        $placeholders = implode(", ", array_fill(0, count($columns), "?"));
        $column_names = implode(", ", $columns);
        $stmt = $mysql_pdo->prepare("INSERT INTO {$table_name} ({$column_names}) VALUES ({$placeholders})");
        
        // Insert each row
        $count = 0;
        foreach ($rows as $row) {
            $stmt->execute(array_values($row));
            $count++;
        }
        
        // Commit transaction
        $mysql_pdo->commit();
        
        $results[] = "Successfully migrated {$count} rows from SQLite to MySQL for table {$table_name}.";
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($mysql_pdo->inTransaction()) {
            $mysql_pdo->rollBack();
        }
        $results[] = "Error migrating table {$table_name}: " . $e->getMessage();
    }
    
    return $results;
}

// Function to migrate JSON data to MySQL
function migrateJsonToMySQL($mysql_pdo, $json_file, $table_name) {
    $results = [];
    
    try {
        if (!file_exists($json_file)) {
            $results[] = "JSON file {$json_file} does not exist.";
            return $results;
        }
        
        $json_data = json_decode(file_get_contents($json_file), true);
        
        if (empty($json_data)) {
            $results[] = "No data found in JSON file {$json_file}.";
            return $results;
        }
        
        // Begin transaction
        $mysql_pdo->beginTransaction();
        
        // Get column names from first item
        $columns = array_keys($json_data[0]);
        
        // Prepare insert statement
        $placeholders = implode(", ", array_fill(0, count($columns), "?"));
        $column_names = implode(", ", $columns);
        $stmt = $mysql_pdo->prepare("INSERT INTO {$table_name} ({$column_names}) VALUES ({$placeholders})");
        
        // Insert each row
        $count = 0;
        foreach ($json_data as $item) {
            $stmt->execute(array_values($item));
            $count++;
        }
        
        // Commit transaction
        $mysql_pdo->commit();
        
        $results[] = "Successfully migrated {$count} items from JSON to MySQL for table {$table_name}.";
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($mysql_pdo->inTransaction()) {
            $mysql_pdo->rollBack();
        }
        $results[] = "Error migrating JSON to table {$table_name}: " . $e->getMessage();
    }
    
    return $results;
}

// Process migration if form submitted
$migration_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['migrate'])) {
    $sqlite_pdo = getSQLiteConnection();
    $mysql_pdo = getMySQLConnection();
    
    if (!$sqlite_pdo) {
        $migration_results[] = "Error: Could not connect to SQLite database.";
    } else if (!$mysql_pdo) {
        $migration_results[] = "Error: Could not connect to MySQL database.";
    } else {
        // Migrate tables
        $tables = ['users', 'testimonials', 'marksheets', 'videos', 'gallery'];
        
        foreach ($tables as $table) {
            $results = migrateTable($sqlite_pdo, $mysql_pdo, $table);
            $migration_results = array_merge($migration_results, $results);
        }
        
        // Migrate courses from JSON if it exists
        $courses_json = __DIR__ . '/../database/courses.json';
        if (file_exists($courses_json)) {
            $results = migrateJsonToMySQL($mysql_pdo, $courses_json, 'courses');
            $migration_results = array_merge($migration_results, $results);
        } else {
            $migration_results[] = "Courses JSON file not found. Skipping courses migration.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQLite to MySQL Migration</h1>
        <p>This tool will migrate all data from your SQLite database to MySQL.</p>
        
        <div class="alert alert-warning">
            <strong>Warning:</strong> This process will overwrite any existing data in the MySQL database. Make sure you have a backup before proceeding.
        </div>
        
        <?php if (!empty($migration_results)): ?>
            <h2>Migration Results</h2>
            <?php foreach ($migration_results as $result): ?>
                <?php 
                $class = 'alert-info';
                if (strpos($result, 'Error') !== false) {
                    $class = 'alert-danger';
                } else if (strpos($result, 'Successfully') !== false) {
                    $class = 'alert-success';
                } else if (strpos($result, 'No data') !== false) {
                    $class = 'alert-warning';
                }
                ?>
                <div class="alert <?php echo $class; ?>">
                    <?php echo $result; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form method="post" action="">
            <button type="submit" name="migrate" class="btn btn-primary" onclick="return confirm('Are you sure you want to migrate the database? This will overwrite existing MySQL data.');">Start Migration</button>
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>