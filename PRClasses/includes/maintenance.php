<?php
/**
 * Maintenance mode handler
 */

// Check if maintenance mode is enabled
$maintenance_file = __DIR__ . '/../.maintenance';
$is_maintenance = file_exists($maintenance_file);

// Allow admin IPs to bypass maintenance mode
$allowed_ips = [
    '127.0.0.1',
    '::1',
    // Add your IP address here
];

$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';

// If in maintenance mode and not an allowed IP, show maintenance page
if ($is_maintenance && !in_array($client_ip, $allowed_ips)) {
    // Get maintenance details
    $maintenance_details = json_decode(file_get_contents($maintenance_file), true);
    $end_time = $maintenance_details['end_time'] ?? null;
    $message = $maintenance_details['message'] ?? 'We are currently performing scheduled maintenance. Please check back soon.';
    
    // Set response code
    http_response_code(503);
    header('Retry-After: 3600');
    
    // Display maintenance page
    include __DIR__ . '/../maintenance.php';
    exit;
} 