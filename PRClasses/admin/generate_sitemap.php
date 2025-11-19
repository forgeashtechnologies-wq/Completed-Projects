<?php
/**
 * Sitemap generator
 * 
 * This script generates a sitemap.xml file for search engines.
 * It should be run periodically via cron job.
 */

// Include configuration
require_once __DIR__ . '/../includes/config.php';

// Base URL
$base_url = SITE_URL;

// Start XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Add static pages
$static_pages = [
    '' => '1.0', // Homepage
    'about.php' => '0.8',
    'courses.php' => '0.9',
    'success-stories.php' => '0.8',
    'contact.php' => '0.7',
    'faq.php' => '0.6',
    'privacy-policy.php' => '0.5',
    'terms-of-service.php' => '0.5'
];

foreach ($static_pages as $page => $priority) {
    $url = $base_url . $page;
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . $url . '</loc>' . PHP_EOL;
    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

// Add dynamic pages from database
try {
    // Get courses
    $stmt = $pdo->query("SELECT id, title FROM courses WHERE status = 'active'");
    $courses = $stmt->fetchAll();
    
    foreach ($courses as $course) {
        $url = $base_url . 'course.php?id=' . $course['id'];
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $url . '</loc>' . PHP_EOL;
        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.8</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
    
    // Get testimonials
    $stmt = $pdo->query("SELECT id FROM testimonials WHERE status = 'approved'");
    $testimonials = $stmt->fetchAll();
    
    foreach ($testimonials as $testimonial) {
        $url = $base_url . 'success-stories.php?section=testimonials&id=' . $testimonial['id'];
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . $url . '</loc>' . PHP_EOL;
        $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.6</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
} catch (PDOException $e) {
    error_log('Sitemap generation error: ' . $e->getMessage());
}

// End XML
$xml .= '</urlset>';

// Write to file
file_put_contents(__DIR__ . '/../sitemap.xml', $xml);

echo "Sitemap generated successfully.\n"; 