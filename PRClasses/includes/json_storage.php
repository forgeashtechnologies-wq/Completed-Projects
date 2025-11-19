<?php
/**
 * JSON Storage functions for the PR Classes website
 * This file provides functions to store and retrieve data from JSON files
 * instead of using a database.
 */

// Prevent multiple inclusions
if (defined('JSON_STORAGE_INCLUDED')) {
    return;
}
define('JSON_STORAGE_INCLUDED', true);

// Define JSON file paths
define('COURSES_JSON_FILE', __DIR__ . '/../database/courses.json');

/**
 * Get all courses from JSON file
 * 
 * @param string $orderBy Field to order by (default: null)
 * @return array Array of courses
 */
function get_all_courses($orderBy = null) {
    if (!file_exists(COURSES_JSON_FILE)) {
        file_put_contents(COURSES_JSON_FILE, '[]');
        return [];
    }
    
    $courses = json_decode(file_get_contents(COURSES_JSON_FILE), true);
    
    // Sort courses if orderBy is specified
    if ($orderBy) {
        usort($courses, function($a, $b) use ($orderBy) {
            return $a[$orderBy] <=> $b[$orderBy];
        });
    }
    
    return $courses;
}

/**
 * Get a course by ID
 * 
 * @param int $id Course ID
 * @return array|null Course data or null if not found
 */
function get_course_by_id($id) {
    $courses = get_all_courses();
    
    foreach ($courses as $course) {
        if ($course['id'] == $id) {
            return $course;
        }
    }
    
    return null;
}

/**
 * Add a new course
 * 
 * @param array $course_data Course data
 * @return bool True if successful, false otherwise
 */
function add_course($course_data) {
    $courses = get_all_courses();
    
    // Generate a new ID
    $max_id = 0;
    foreach ($courses as $course) {
        if ($course['id'] > $max_id) {
            $max_id = $course['id'];
        }
    }
    
    $course_data['id'] = $max_id + 1;
    $course_data['created_at'] = date('Y-m-d H:i:s');
    $course_data['updated_at'] = date('Y-m-d H:i:s');
    
    $courses[] = $course_data;
    
    return file_put_contents(COURSES_JSON_FILE, json_encode($courses, JSON_PRETTY_PRINT));
}

/**
 * Update an existing course
 * 
 * @param int $id Course ID
 * @param array $course_data Updated course data
 * @return bool True if successful, false otherwise
 */
function update_course($id, $course_data) {
    $courses = get_all_courses();
    $updated = false;
    
    foreach ($courses as $key => $course) {
        if ($course['id'] == $id) {
            $course_data['id'] = $id; // Ensure ID remains the same
            $course_data['created_at'] = $course['created_at']; // Preserve creation date
            $course_data['updated_at'] = date('Y-m-d H:i:s'); // Update modification date
            
            $courses[$key] = $course_data;
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        return file_put_contents(COURSES_JSON_FILE, json_encode($courses, JSON_PRETTY_PRINT));
    }
    
    return false;
}

/**
 * Delete a course
 * 
 * @param int $id Course ID
 * @return bool True if successful, false otherwise
 */
function delete_course($id) {
    $courses = get_all_courses();
    $deleted = false;
    
    foreach ($courses as $key => $course) {
        if ($course['id'] == $id) {
            unset($courses[$key]);
            $deleted = true;
            break;
        }
    }
    
    if ($deleted) {
        // Re-index array to ensure sequential keys
        $courses = array_values($courses);
        return file_put_contents(COURSES_JSON_FILE, json_encode($courses, JSON_PRETTY_PRINT));
    }
    
    return false;
}