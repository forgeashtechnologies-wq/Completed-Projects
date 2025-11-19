<?php
require_once '../includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve testimonial
    if (isset($_POST['approve_testimonial'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE testimonials SET status = 'Approved' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Testimonial approved successfully!");
        } else {
            display_error_message("Failed to approve testimonial.");
        }
    }
    
    // Reject testimonial
    if (isset($_POST['reject_testimonial'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE testimonials SET status = 'Rejected' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Testimonial rejected successfully!");
        } else {
            display_error_message("Failed to reject testimonial.");
        }
    }
    
    // Delete testimonial
    if (isset($_POST['delete_testimonial'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Testimonial deleted successfully!");
        } else {
            display_error_message("Failed to delete testimonial.");
        }
    }
}

// Get testimonials based on status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "SELECT * FROM testimonials";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '" . sanitize($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->query($query);
$testimonials = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials Management - <?php echo SITE_NAME; ?></title>
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
            display: inline-block;
            margin-right: 5px;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-primary {
            background-color: #2196F3;
        }
        .btn-warning {
            background-color: #ff9800;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container a {
            margin-right: 10px;
            padding: 5px 10px;
            text-decoration: none;
            color: #333;
            border-radius: 3px;
        }
        .filter-container a.active {
            background-color: #4CAF50;
            color: white;
        }
        .testimonial-list {
            width: 100%;
            border-collapse: collapse;
        }
        .testimonial-list th, .testimonial-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .testimonial-list th {
            background-color: #f2f2f2;
        }
        .testimonial-list tr:hover {
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
        .testimonial-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffeb3b;
            color: #333;
        }
        .status-approved {
            background-color: #4CAF50;
            color: white;
        }
        .status-rejected {
            background-color: #f44336;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 60px;">
        <div class="header">
            <h2>Testimonials Management</h2>
            <a href="index.php" class="btn">Back to Dashboard</a>
        </div>
        
        <?php if ($success_message = get_success_message()): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message = get_error_message()): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="filter-container">
            <strong>Filter by status:</strong>
            <a href="?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="?status=Pending" class="<?php echo $status_filter === 'Pending' ? 'active' : ''; ?>">Pending</a>
            <a href="?status=Approved" class="<?php echo $status_filter === 'Approved' ? 'active' : ''; ?>">Approved</a>
            <a href="?status=Rejected" class="<?php echo $status_filter === 'Rejected' ? 'active' : ''; ?>">Rejected</a>
        </div>
        
        <?php if (count($testimonials) > 0): ?>
            <table class="testimonial-list">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Course</th>
                        <th>Testimonial</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($testimonial['name']); ?></td>
                            <td><?php echo htmlspecialchars($testimonial['subject']); ?></td>
                            <td><?php echo htmlspecialchars($testimonial['course']); ?></td>
                            <td class="testimonial-content">
                                <a href="#" onclick="viewTestimonial(<?php echo $testimonial['id']; ?>)">
                                    <?php echo htmlspecialchars(substr($testimonial['content'], 0, 50) . (strlen($testimonial['content']) > 50 ? '...' : '')); ?>
                                </a>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($testimonial['status']); ?>">
                                    <?php echo htmlspecialchars($testimonial['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($testimonial['status'] === 'Pending'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                        <button type="submit" name="approve_testimonial" class="btn btn-primary">Approve</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                        <button type="submit" name="reject_testimonial" class="btn btn-warning">Reject</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                    <button type="submit" name="delete_testimonial" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No testimonials found.</p>
        <?php endif; ?>
    </div>
    
    <!-- Testimonial View Modal -->
    <div id="testimonialModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Testimonial Details</h3>
            <div id="testimonialDetails"></div>
        </div>
    </div>
    
    <script>
        // Get the modal
        var modal = document.getElementById("testimonialModal");
        
        // Function to view testimonial details
        function viewTestimonial(id) {
            // In a real application, you would fetch the testimonial details via AJAX
            // For simplicity, we'll use the data already on the page
            <?php echo "var testimonials = " . json_encode($testimonials) . ";\n"; ?>
            
            var testimonial = testimonials.find(function(t) { return t.id === id; });
            
            if (testimonial) {
                var detailsHtml = '<div style="margin-bottom: 15px;">' +
                    '<p><strong>Name:</strong> ' + testimonial.name + '</p>' +
                    '<p><strong>Registration No:</strong> ' + testimonial.registration_no + '</p>' +
                    '<p><strong>Subject:</strong> ' + testimonial.subject + '</p>' +
                    '<p><strong>Course:</strong> ' + testimonial.course + '</p>' +
                    '<p><strong>Mode:</strong> ' + testimonial.mode + '</p>' +
                    '<p><strong>Year:</strong> ' + testimonial.year + '</p>' +
                    '<p><strong>Mobile:</strong> ' + testimonial.mobile + '</p>' +
                    '<p><strong>Testimonial:</strong></p>' +
                    '<div style="background-color: #f9f9f9; padding: 10px; border-radius: 5px;">' + testimonial.content + '</div>';
                
                if (testimonial.image_path) {
                    detailsHtml += '<p><strong>Profile Picture:</strong></p>' +
                        '<img src="../' + testimonial.image_path + '" alt="Profile Picture" style="max-width: 100px; max-height: 100px;">';
                }
                
                detailsHtml += '</div>';
                
                document.getElementById("testimonialDetails").innerHTML = detailsHtml;
                modal.style.display = "block";
            }
            
            return false; // Prevent default link behavior
        }
        
        // Function to close the modal
        function closeModal() {
            modal.style.display = "none";
        }
        
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    
    <script>
        let timeout;
        const logoutTime = 5 * 60 * 1000; // 5 minutes

        function resetTimer() {
            clearTimeout(timeout);
            timeout = setTimeout(logout, logoutTime);
        }

        function logout() {
            // Clear session and redirect to login page
            window.location.href = '/admin/logout.php';
        }

        // Reset timer on mouse movement, key press, or click
        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onkeypress = resetTimer;
        window.onclick = resetTimer;
    </script>
</body>
</html>