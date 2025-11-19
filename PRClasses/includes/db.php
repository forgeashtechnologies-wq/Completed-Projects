<?php
/**
 * Database connection with enhanced error logging for PR Classes Website
 */

// Prevent direct access
if (!defined('INCLUDED_FROM_INDEX')) {
    die('Direct access to this file is not allowed.');
}

// Database credentials
$db_host = 'localhost';
$db_name = 'prclasses_db'; // Update with your actual database name
$db_user = 'root';         // Update with your MySQL username
$db_pass = '';             // Update with your MySQL password

// Fallback to SQLite if MySQL connection fails
$use_sqlite_fallback = true; // Set to false to disable SQLite fallback
$sqlite_db_path = __DIR__ . '/../database/database.sqlite';

// Logging configuration
define('DB_LOG_ENABLED', true);          // Master switch for database logging
define('DB_LOG_QUERIES', true);          // Log all queries (can generate large logs)
define('DB_LOG_QUERY_PARAMS', true);     // Log query parameters (may contain sensitive data)
define('DB_LOG_EXECUTION_TIME', true);   // Log query execution time
define('DB_LOG_PATH', __DIR__ . '/../logs/database.log');
define('DB_ERROR_LOG_PATH', __DIR__ . '/../logs/database_errors.log');

// Ensure log directory exists
$log_dir = dirname(DB_LOG_PATH);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

/**
 * Log database-related message
 * 
 * @param string $message Message to log
 * @param string $level Log level (ERROR, WARNING, INFO, DEBUG)
 * @param array $context Additional context information
 * @return void
 */
function db_log($message, $level = 'INFO', $context = []) {
    if (!DB_LOG_ENABLED) {
        return;
    }
    
    $log_file = ($level === 'ERROR') ? DB_ERROR_LOG_PATH : DB_LOG_PATH;
    
    // Format timestamp
    $timestamp = date('Y-m-d H:i:s');
    
    // Generate unique query ID for tracking
    $query_id = 'q_' . substr(md5(uniqid(mt_rand(), true)), 0, 12);
    
    // Format context as JSON
    $context_json = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    
    // Format log entry
    $log_entry = "[{$timestamp}] [{$level}] {$query_id} {$message}{$context_json}\n";
    
    // Write to log file
    error_log($log_entry, 3, $log_file);
    
    // Also write to PHP error log for critical errors
    if ($level === 'ERROR') {
        error_log("Database {$level}: {$message}");
    }
    
    return $query_id;
}

// Create connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Connection established
    define('DB_CONNECTION_SUCCESS', true);
    db_log("Database connection established successfully", 'INFO', ['host' => $db_host, 'database' => $db_name]);
    
} catch(PDOException $e) {
    // Log error but don't display to user
    $error_message = "Database Connection Error: " . $e->getMessage();
    db_log($error_message, 'ERROR', [
        'host' => $db_host,
        'database' => $db_name,
        'error_code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Try SQLite fallback if enabled
    if ($use_sqlite_fallback) {
        try {
            db_log("Attempting SQLite fallback connection", 'INFO', ['path' => $sqlite_db_path]);
            $pdo = new PDO("sqlite:" . $sqlite_db_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            define('DB_CONNECTION_SUCCESS', true);
            define('USING_SQLITE_FALLBACK', true);
            db_log("SQLite fallback connection established successfully", 'INFO', ['path' => $sqlite_db_path]);
        } catch (PDOException $sqlite_e) {
            db_log("SQLite fallback connection failed", 'ERROR', [
                'path' => $sqlite_db_path,
                'error_code' => $sqlite_e->getCode(),
                'message' => $sqlite_e->getMessage(),
                'trace' => $sqlite_e->getTraceAsString()
            ]);
            define('DB_CONNECTION_SUCCESS', false);
        }
    } else {
        define('DB_CONNECTION_SUCCESS', false);
    }
}

/**
 * Execute a query safely with detailed logging
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for prepared statement
 * @return PDOStatement|false PDO statement object or false on failure
 */
function db_query($sql, $params = []) {
    global $pdo;
    
    if (!isset($pdo) || !DB_CONNECTION_SUCCESS) {
        db_log("Database query attempted but connection is not available", 'ERROR', ['sql' => $sql]);
        return false;
    }
    
    // Handle SHOW TABLES command for SQLite which doesn't support it
    if (defined('USING_SQLITE_FALLBACK') && USING_SQLITE_FALLBACK && stripos($sql, 'SHOW TABLES') !== false) {
        // Convert to SQLite compatible query
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
        db_log("Converted 'SHOW TABLES' to SQLite compatible query", 'INFO');
    }
    
    $query_id = DB_LOG_QUERIES ? db_log("Executing query", 'INFO', [
        'sql' => $sql,
        'params' => DB_LOG_QUERY_PARAMS ? $params : '[hidden]',
        'operation' => strtoupper(substr(trim($sql), 0, strpos(trim($sql).' ', ' ')))
    ]) : null;
    
    $start_time = microtime(true);
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if (DB_LOG_EXECUTION_TIME) {
            $execution_time = microtime(true) - $start_time;
            db_log("Query {$query_id} completed in {$execution_time} seconds", 'INFO', [
                'row_count' => $stmt->rowCount(),
                'execution_time' => $execution_time
            ]);
        }
        
        return $stmt;
    } catch (PDOException $e) {
        $error_message = "Query {$query_id} failed: [{$e->getCode()}] {$e->getMessage()}";
        db_log($error_message, 'ERROR', [
            'sql' => $sql,
            'params' => $params,
            'operation' => strtoupper(substr(trim($sql), 0, strpos(trim($sql).' ', ' '))),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Get a single record with detailed logging
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for prepared statement
 * @return array|false Single record or false on failure
 */
function db_get_row($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $result = $stmt ? $stmt->fetch() : false;
    
    if (DB_LOG_QUERIES) {
        db_log("Fetched single row", 'INFO', [
            'sql' => $sql,
            'success' => ($result !== false),
            'found' => ($result !== false)
        ]);
    }
    
    return $result;
}

/**
 * Get multiple records with detailed logging
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for prepared statement
 * @return array Array of records (empty array if no results or on failure)
 */
function db_get_rows($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $results = $stmt ? $stmt->fetchAll() : [];
    
    if (DB_LOG_QUERIES) {
        db_log("Fetched multiple rows", 'INFO', [
            'sql' => $sql,
            'success' => ($stmt !== false),
            'count' => count($results)
        ]);
    }
    
    return $results;
}

/**
 * Insert a record with detailed logging
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false Last insert ID or false on failure
 */
function db_insert($table, $data) {
    global $pdo;
    
    if (!isset($pdo) || !DB_CONNECTION_SUCCESS) {
        db_log("Insert attempted but connection is not available", 'ERROR', ['table' => $table]);
        return false;
    }
    
    try {
        // Build columns and placeholders
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $query_id = DB_LOG_QUERIES ? db_log("Executing insert", 'INFO', [
            'table' => $table,
            'columns' => array_keys($data),
            'values' => DB_LOG_QUERY_PARAMS ? array_values($data) : '[hidden]'
        ]) : null;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $last_id = $pdo->lastInsertId();
        
        db_log("Insert {$query_id} completed successfully", 'INFO', [
            'table' => $table,
            'last_insert_id' => $last_id
        ]);
        
        return $last_id;
    } catch (PDOException $e) {
        $error_message = "Insert {$query_id} failed: [{$e->getCode()}] {$e->getMessage()}";
        db_log($error_message, 'ERROR', [
            'table' => $table,
            'data' => $data,
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Update a record with detailed logging
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value to update
 * @param string $where Where clause (without 'WHERE')
 * @param array $params Parameters for where clause
 * @return bool Success or failure
 */
function db_update($table, $data, $where, $params = []) {
    global $pdo;
    
    if (!isset($pdo) || !DB_CONNECTION_SUCCESS) {
        db_log("Update attempted but connection is not available", 'ERROR', ['table' => $table]);
        return false;
    }
    
    try {
        // Build SET clause
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $set_clause = implode(', ', $set);
        
        $sql = "UPDATE $table SET $set_clause WHERE $where";
        
        $query_id = DB_LOG_QUERIES ? db_log("Executing update", 'INFO', [
            'table' => $table,
            'set' => array_keys($data),
            'where' => $where,
            'values' => DB_LOG_QUERY_PARAMS ? array_merge(array_values($data), $params) : '[hidden]'
        ]) : null;
        
        // Combine data values with where params
        $execute_params = array_merge(array_values($data), $params);
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($execute_params);
        $affected_rows = $stmt->rowCount();
        
        db_log("Update {$query_id} completed successfully", 'INFO', [
            'table' => $table,
            'affected_rows' => $affected_rows
        ]);
        
        return true;
    } catch (PDOException $e) {
        $error_message = "Update {$query_id} failed: [{$e->getCode()}] {$e->getMessage()}";
        db_log($error_message, 'ERROR', [
            'table' => $table,
            'data' => $data,
            'where' => $where,
            'params' => $params,
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Delete a record with detailed logging
 * 
 * @param string $table Table name
 * @param string $where Where clause (without 'WHERE')
 * @param array $params Parameters for where clause
 * @return bool Success or failure
 */
function db_delete($table, $where, $params = []) {
    global $pdo;
    
    if (!isset($pdo) || !DB_CONNECTION_SUCCESS) {
        db_log("Delete attempted but connection is not available", 'ERROR', ['table' => $table]);
        return false;
    }
    
    try {
        $sql = "DELETE FROM $table WHERE $where";
        
        $query_id = DB_LOG_QUERIES ? db_log("Executing delete", 'INFO', [
            'table' => $table,
            'where' => $where,
            'params' => DB_LOG_QUERY_PARAMS ? $params : '[hidden]'
        ]) : null;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $affected_rows = $stmt->rowCount();
        
        db_log("Delete {$query_id} completed successfully", 'INFO', [
            'table' => $table,
            'affected_rows' => $affected_rows
        ]);
        
        return true;
    } catch (PDOException $e) {
        $error_message = "Delete {$query_id} failed: [{$e->getCode()}] {$e->getMessage()}";
        db_log($error_message, 'ERROR', [
            'table' => $table,
            'where' => $where,
            'params' => $params,
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}