<?php
/**
 * Default values and functions
 */

if (!function_exists('getDefaultMode')) {
    /**
     * Get default mode value for database tables
     * 
     * @return string Default mode value
     */
    function getDefaultMode() {
        return 'public'; // Default mode for all content
    }
}

if (!function_exists('getDefaultStatus')) {
    /**
     * Get default status value for user submissions
     * 
     * @return string Default status value
     */
    function getDefaultStatus() {
        return 'pending'; // Default status for all user submissions
    }
}

if (!function_exists('getDefaultYear')) {
    /**
     * Get default year value
     * 
     * @return string Current year
     */
    function getDefaultYear() {
        return date('Y');
    }
}

// Add this file to your includes in config.php 