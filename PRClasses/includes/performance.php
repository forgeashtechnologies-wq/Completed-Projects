<?php
/**
 * Performance monitoring functions
 */

// Start time tracking
$GLOBALS['performance_start'] = microtime(true);
$GLOBALS['performance_queries'] = [];
$GLOBALS['performance_memory_start'] = memory_get_usage();

/**
 * Log database query for performance monitoring
 * 
 * @param string $query SQL query
 * @param float $time Time taken in seconds
 * @return void
 */
function logQuery($query, $time) {
    $GLOBALS['performance_queries'][] = [
        'query' => $query,
        'time' => $time
    ];
}

/**
 * Get performance statistics
 * 
 * @return array Performance statistics
 */
function getPerformanceStats() {
    $end_time = microtime(true);
    $execution_time = $end_time - $GLOBALS['performance_start'];
    
    $memory_usage = memory_get_usage() - $GLOBALS['performance_memory_start'];
    $memory_peak = memory_get_peak_usage();
    
    $query_count = count($GLOBALS['performance_queries']);
    $query_time = 0;
    
    foreach ($GLOBALS['performance_queries'] as $query) {
        $query_time += $query['time'];
    }
    
    return [
        'execution_time' => round($execution_time * 1000, 2), // in milliseconds
        'memory_usage' => formatBytes($memory_usage),
        'memory_peak' => formatBytes($memory_peak),
        'query_count' => $query_count,
        'query_time' => round($query_time * 1000, 2), // in milliseconds
        'queries' => $GLOBALS['performance_queries']
    ];
}

/**
 * Format bytes to human-readable format
 * 
 * @param int $bytes Bytes to format
 * @param int $precision Decimal precision
 * @return string Formatted bytes
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Display performance debug information
 * 
 * @param bool $include_queries Whether to include query details
 * @return string HTML with performance information
 */
function displayPerformanceInfo($include_queries = false) {
    $stats = getPerformanceStats();
    
    $html = '<div class="performance-debug" style="position:fixed;bottom:0;right:0;background:#f8f9fa;padding:10px;border:1px solid #ddd;font-size:12px;z-index:9999;">';
    $html .= '<h4 style="margin:0 0 5px 0;font-size:14px;">Performance</h4>';
    $html .= '<ul style="margin:0;padding:0 0 0 15px;">';
    $html .= '<li>Execution: ' . $stats['execution_time'] . 'ms</li>';
    $html .= '<li>Memory: ' . $stats['memory_usage'] . ' (Peak: ' . $stats['memory_peak'] . ')</li>';
    $html .= '<li>Queries: ' . $stats['query_count'] . ' (' . $stats['query_time'] . 'ms)</li>';
    $html .= '</ul>';
    
    if ($include_queries && !empty($stats['queries'])) {
        $html .= '<h5 style="margin:5px 0;font-size:13px;">Queries</h5>';
        $html .= '<ul style="margin:0;padding:0 0 0 15px;">';
        
        foreach ($stats['queries'] as $query) {
            $html .= '<li>';
            $html .= '<span style="color:#777;">' . round($query['time'] * 1000, 2) . 'ms</span> ';
            $html .= '<span style="color:#333;">' . htmlspecialchars(substr($query['query'], 0, 100)) . (strlen($query['query']) > 100 ? '...' : '') . '</span>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
    }
    
    $html .= '</div>';
    
    return $html;
}

// Register shutdown function to log performance data
register_shutdown_function(function() {
    $stats = getPerformanceStats();
    $log_file = __DIR__ . '/../logs/performance.log';
    
    $log_entry = date('[Y-m-d H:i:s]') . ' ' . 
                 $_SERVER['REQUEST_URI'] . ' ' .
                 'Time: ' . $stats['execution_time'] . 'ms ' .
                 'Memory: ' . $stats['memory_usage'] . ' ' .
                 'Queries: ' . $stats['query_count'] . ' (' . $stats['query_time'] . 'ms)' . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}); 