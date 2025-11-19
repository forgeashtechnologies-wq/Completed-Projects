<?php
require_once '../includes/functions.php';
require_once '../includes/json_storage.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new course
    if (isset($_POST['add_course'])) {
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
        
        if (add_course($course_data)) {
            display_success_message("Course added successfully!");
        } else {
            display_error_message("Failed to add course.");
        }
    }
    
    // Update existing course
    if (isset($_POST['update_course'])) {
        $id = (int) $_POST['id'];
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
        
        if (update_course($id, $course_data)) {
            display_success_message("Course updated successfully!");
        } else {
            display_error_message("Failed to update course.");
        }
    }
    
    // Delete course
    if (isset($_POST['delete_course'])) {
        $id = (int) $_POST['id'];
        
        if (delete_course($id)) {
            display_success_message("Course deleted successfully!");
        } else {
            display_error_message("Failed to delete course.");
        }
    }
}

// Get all courses
$courses = get_all_courses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 1200px;
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
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-primary {
            background-color: #2196F3;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .form-container {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
        }
        .course-list {
            width: 100%;
            border-collapse: collapse;
        }
        .course-list th, .course-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .course-list th {
            background-color: #f2f2f2;
        }
        .course-list tr:hover {
            background-color: #f5f5f5;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .success-message {
            padding: 10px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error-message {
            padding: 10px;
            background-color: #f2dede;
            color: #a94442;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Course Management</h2>
            <a href="index.php" class="btn">Back to Dashboard</a>
        </div>
        
        <?php if ($success_message = get_success_message()): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message = get_error_message()): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h3>Add New Course</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="title">Course Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="CMA Inter">CMA Inter</option>
                        <option value="CMA Final">CMA Final</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="fees">Fees</label>
                    <input type="number" id="fees" name="fees" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="discount_percentage">Discount Percentage</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" value="0">
                </div>
                
                <div class="form-group">
                    <label for="schedule">Schedule</label>
                    <input type="text" id="schedule" name="schedule">
                </div>
                
                <div class="form-group">
                    <label for="enrollment_status">Enrollment Status</label>
                    <select id="enrollment_status" name="enrollment_status">
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="limited_seats">Limited Seats</label>
                    <input type="number" id="limited_seats" name="limited_seats" min="0" value="0">
                </div>
                
                <div class="form-group">
                    <label for="seats_available">Seats Available</label>
                    <input type="number" id="seats_available" name="seats_available" min="0" value="0">
                </div>
                
                <button type="submit" name="add_course" class="btn">Add Course</button>
            </form>
        </div>
        
        <h3>Existing Courses</h3>
        <?php if (count($courses) > 0): ?>
            <table class="course-list">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Fees</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo htmlspecialchars($course['category']); ?></td>
                            <td>₹<?php echo number_format($course['fees'], 2); ?></td>
                            <td><?php echo $course['discount_percentage']; ?>%</td>
                            <td><?php echo htmlspecialchars($course['enrollment_status']); ?></td>
                            <td class="action-buttons">
                                <button class="btn btn-primary" onclick="editCourse(<?php echo $course['id']; ?>)">Edit</button>
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="delete_course" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No courses found. Add a course to get started.</p>
        <?php endif; ?>
    </div>
    
    <script>
        function editCourse(id) {
            // Fetch course details and populate edit form
            // This would typically be done with AJAX, but for simplicity, we'll use a form submission
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = 'edit_course.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>