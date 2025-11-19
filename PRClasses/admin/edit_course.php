<?php
require_once '../includes/functions.php';
require_once '../includes/json_storage.php';

// Check if admin is logged in
check_admin_login();

// Check if course ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$course_id = (int) $_GET['id'];

// Get course details
$course = get_course_by_id($course_id);

// If course not found, redirect back to courses page
if (!$course) {
    display_error_message("Course not found.");
    header("Location: courses.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $fees = (float) $_POST['fees'];
    $discount_percentage = (int) $_POST['discount_percentage'];
    $schedule = sanitize($_POST['schedule']);
    $enrollment_status = sanitize($_POST['enrollment_status']);
    $limited_seats = (int) $_POST['limited_seats'];
    $seats_available = (int) $_POST['seats_available'];
    
    $course_data = [
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'fees' => $fees,
        'discount_percentage' => $discount_percentage,
        'schedule' => $schedule,
        'enrollment_status' => $enrollment_status,
        'limited_seats' => $limited_seats,
        'seats_available' => $seats_available
    ];
    
    if (update_course($course_id, $course_data)) {
        display_success_message("Course updated successfully!");
        header("Location: courses.php");
        exit();
    } else {
        display_error_message("Failed to update course.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h2 {
            margin: 0;
        }
        .btn {
            padding: 8px 16px;