<?php
/**
 * Cron job script to run scheduled tasks
 * 
 * This script should be run via cron job, e.g.:
 * 0 0 * * * php /path/to/admin/cron.php > /dev/null 2>&1
 */

// Set maximum execution time to 10 minutes
ini_set('max_execution_time', 600);

// Include configuration
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/logger.php';

// Create logger
$logger = new Logger(__DIR__ . '/../logs/cron.log');
$logger->info('Starting cron job');

// Run database backup (weekly on Sunday)
if (date('w') == 0) {
    $logger->info('Running weekly database backup');
    include __DIR__ . '/backup.php';
}

// Generate sitemap (daily)
$logger->info('Generating sitemap');
include __DIR__ . '/generate_sitemap.php';

// Clean up old logs (keep last 30 days)
$logger->info('Cleaning up old logs');
$log_files = glob(__DIR__ . '/../logs/*.log');
$cutoff = strtotime('-30 days');

foreach ($log_files as $log_file) {
    // Skip if file is less than 30 days old
    if (filemtime($log_file) > $cutoff) {
        continue;
    }
    
    // Archive old log file
    $archive_dir = __DIR__ . '/../logs/archive';
    if (!is_dir($archive_dir)) {
        mkdir($archive_dir, 0755, true);
    }
    
    $archive_file = $archive_dir . '/' . basename($log_file) . '.' . date('Y-m-d', filemtime($log_file)) . '.gz';
    
    // Compress and move to archive
    $data = file_get_contents($log_file);
    $gzdata = gzencode($data, 9);
    file_put_contents($archive_file, $gzdata);
    
    // Clear original log file
    file_put_contents($log_file, '');
    
    $logger->info('Archived log file: ' . basename($log_file));
}

// Clean up temporary files
$temp_files = glob(__DIR__ . '/../temp/*');
foreach ($temp_files as $temp_file) {
    if (is_file($temp_file) && filemtime($temp_file) < $cutoff) {
        unlink($temp_file);
        $logger->info('Deleted old temp file: ' . basename($temp_file));
    }
}

$logger->info('Cron job completed successfully'); 