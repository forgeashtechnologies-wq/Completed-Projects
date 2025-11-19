<?php
require_once '../includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve marksheet
    if (isset($_POST['approve_marksheet'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE marksheets SET status = 'Approved' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Marksheet approved successfully!");
        } else {
            display_error_message("Failed to approve marksheet.");
        }
    }
    
    // Reject marksheet
    if (isset($_POST['reject_marksheet'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE marksheets SET status = 'Rejected' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Marksheet rejected successfully!");
        } else {
            display_error_message("Failed to reject marksheet.");
        }
    }
    
    // Delete marksheet
    if (isset($_POST['delete_marksheet'])) {
        $id = (int) $_POST['id'];
        
        // Get marksheet details to delete the file
        $stmt = $pdo->prepare("SELECT marksheet_path, image_path FROM marksheets WHERE id = ?");
        $stmt->execute([$id]);
        $marksheet = $stmt->fetch();
        
        // Delete the marksheet record
        $stmt = $pdo->prepare("DELETE FROM marksheets WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // Delete the marksheet file if it exists
            if ($marksheet && !empty($marksheet['marksheet_path']) && file_exists('../' . $marksheet['marksheet_path'])) {
                unlink('../' . $marksheet['marksheet_path']);
            }
            
            // Delete the profile image if it exists
            if ($marksheet && !empty($marksheet['image_path']) && file_exists('../' . $marksheet['image_path'])) {
                unlink('../' . $marksheet['image_path']);
            }
            
            display_success_message("Marksheet deleted successfully!");
        } else {
            display_error_message("Failed to delete marksheet.");
        }
    }
}

// Get marksheets based on status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "SELECT * FROM marksheets";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '" . sanitize($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->query($query);
$marksheets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheets Management - <?php echo SITE_NAME; ?></title>
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
        .marksheet-list {
            width: 100%;
            border-collapse: collapse;
        }
        .marksheet-list th, .marksheet-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .marksheet-list th {
            background-color: #f2f2f2;
        }
        .marksheet-list tr:hover {
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
            max-width: 800px;
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
        .marksheet-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 60px;">
        <div class="header">
            <h2>Marksheets Management</h2>
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
        
        <?php if (count($marksheets) > 0): ?>
            <table class="marksheet-list">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Course</th>
                        <th>Marksheet</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($marksheets as $marksheet): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($marksheet['name']); ?></td>
                            <td><?php echo htmlspecialchars($marksheet['subject']); ?></td>
                            <td><?php echo htmlspecialchars($marksheet['course']); ?></td>
                            <td>
                                <a href="#" onclick="viewMarksheet(<?php echo $marksheet['id']; ?>)">
                                    View Marksheet
                                </a>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($marksheet['status']); ?>">
                                    <?php echo htmlspecialchars($marksheet['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($marksheet['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($marksheet['status'] === 'Pending'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $marksheet['id']; ?>">
                                        <button type="submit" name="approve_marksheet" class="btn btn-primary">Approve</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $marksheet['id']; ?>">
                                        <button type="submit" name="reject_marksheet" class="btn btn-warning">Reject</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this marksheet?');">
                                    <input type="hidden" name="id" value="<?php echo $marksheet['id']; ?>">
                                    <button type="submit" name="delete_marksheet" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No marksheets found.</p>
        <?php endif; ?>
    </div>
    
    <!-- Marksheet View Modal -->
    <div id="marksheetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Marksheet Details</h3>
            <div id="marksheetDetails"></div>
        </div>
    </div>
    
    <script>
        // Get the modal
        var modal = document.getElementById("marksheetModal");
        
        // Function to view marksheet details
        function viewMarksheet(id) {
            // In a real application, you would fetch the marksheet details via AJAX
            // For simplicity, we'll use the data already on the page
            <?php echo "var marksheets = " . json_encode($marksheets) . ";\n"; ?>
            
            var marksheet = marksheets.find(function(m) { return m.id === id; });
            
            if (marksheet) {
                var detailsHtml = '<div style="margin-bottom: 15px;">' +
                    '<p><strong>Name:</strong> ' + marksheet.name + '</p>' +
                    '<p><strong>Registration No:</strong> ' + marksheet.registration_no + '</p>' +
                    '<p><strong>Subject:</strong> ' + marksheet.subject + '</p>' +
                    '<p><strong>Course:</strong> ' + marksheet.course + '</p>' +
                    '<p><strong>Mode:</strong> ' + marksheet.mode + '</p>' +
                    '<p><strong>Year:</strong> ' + marksheet.year + '</p>' +
                    '<p><strong>Mobile:</strong> ' + marksheet.mobile + '</p>';
                
                if (marksheet.marksheet_path) {
                    detailsHtml += '<p><strong>Marksheet:</strong></p>' +
                        '<img src="../' + marksheet.marksheet_path + '" alt="Marksheet" class="marksheet-image">';
                }
                
                if (marksheet.image_path) {
                    detailsHtml += '<p><strong>Profile Picture:</strong></p>' +
                        '<img src="../' + marksheet.image_path + '" alt="Profile Picture" style="max-width: 100px; max-height: 100px;">';
                }
                
                detailsHtml += '</div>';
                
                document.getElementById("marksheetDetails").innerHTML = detailsHtml;
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