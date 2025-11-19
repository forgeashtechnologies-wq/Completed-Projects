<?php
/**
 * Database backup script
 * 
 * This script creates a backup of the database and saves it to the backups directory.
 * It should be run periodically via cron job.
 */

// Set maximum execution time to 5 minutes
ini_set('max_execution_time', 300);

// Include configuration
require_once __DIR__ . '/../includes/config.php';

// Create backups directory if it doesn't exist
$backup_dir = __DIR__ . '/../backups';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Generate backup filename with date
$date = date('Y-m-d_H-i-s');
$backup_file = $backup_dir . '/backup_' . $date . '.sql';

// Get database credentials from config
$db_host = $db_host ?? 'localhost';
$db_name = $db_name ?? 'prclasses_db';
$db_user = $db_user ?? 'root';
$db_pass = $db_pass ?? '';

// Command to create backup
$command = "mysqldump --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name} > {$backup_file}";

// Execute backup command
exec($command, $output, $return_var);

// Check if backup was successful
if ($return_var === 0) {
    // Compress the backup file
    $zip_file = $backup_file . '.zip';
    $zip = new ZipArchive();
    
    if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backup_file, basename($backup_file));
        $zip->close();
        
        // Remove the uncompressed SQL file
        unlink($backup_file);
        
        echo "Backup created successfully: " . basename($zip_file) . "\n";
        
        // Delete old backups (keep only last 10)
        $backups = glob($backup_dir . '/backup_*.zip');
        if (count($backups) > 10) {
            // Sort by filename (which includes date)
            sort($backups);
            
            // Delete oldest backups
            $to_delete = count($backups) - 10;
            for ($i = 0; $i < $to_delete; $i++) {
                unlink($backups[$i]);
                echo "Deleted old backup: " . basename($backups[$i]) . "\n";
            }
        }
    } else {
        echo "Failed to create zip file.\n";
    }
} else {
    echo "Backup failed with error code: " . $return_var . "\n";
} 