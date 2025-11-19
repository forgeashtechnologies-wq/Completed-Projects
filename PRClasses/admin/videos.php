<?php
require_once '../includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve video
    if (isset($_POST['approve_video'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE videos SET status = 'Approved' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Video approved successfully!");
        } else {
            display_error_message("Failed to approve video.");
        }
    }
    
    // Reject video
    if (isset($_POST['reject_video'])) {
        $id = (int) $_POST['id'];
        
        $stmt = $pdo->prepare("UPDATE videos SET status = 'Rejected' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            display_success_message("Video rejected successfully!");
        } else {
            display_error_message("Failed to reject video.");
        }
    }
    
    // Delete video
    if (isset($_POST['delete_video'])) {
        $id = (int) $_POST['id'];
        
        // Get video details to delete the profile image if exists
        $stmt = $pdo->prepare("SELECT image_path FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        $video = $stmt->fetch();
        
        // Delete the video record
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // Delete the profile image if it exists
            if ($video && !empty($video['image_path']) && file_exists('../' . $video['image_path'])) {
                unlink('../' . $video['image_path']);
            }
            
            display_success_message("Video deleted successfully!");
        } else {
            display_error_message("Failed to delete video.");
        }
    }
    
    // Update YouTube URL
    if (isset($_POST['update_youtube_url'])) {
        $id = (int) $_POST['id'];
        $youtube_url = sanitize($_POST['youtube_url']);
        
        $stmt = $pdo->prepare("UPDATE videos SET youtube_url = ? WHERE id = ?");
        
        if ($stmt->execute([$youtube_url, $id])) {
            display_success_message("YouTube URL updated successfully!");
        } else {
            display_error_message("Failed to update YouTube URL.");
        }
    }
}

// Get videos based on status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$query = "SELECT * FROM videos";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '" . sanitize($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->query($query);
$videos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Videos Management - <?php echo SITE_NAME; ?></title>
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
        .video-list {
            width: 100%;
            border-collapse: collapse;
        }
        .video-list th, .video-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .video-list th {
            background-color: #f2f2f2;
        }
        .video-list tr:hover {
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
        .youtube-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            margin-top: 10px;
        }
        .youtube-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Feedback Videos Management</h2>
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
        
        <?php if (count($videos) > 0): ?>
            <table class="video-list">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Course</th>
                        <th>YouTube URL</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($video['name']); ?></td>
                            <td><?php echo htmlspecialchars($video['subject']); ?></td>
                            <td><?php echo htmlspecialchars($video['course']); ?></td>
                            <td>
                                <?php if (!empty($video['youtube_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($video['youtube_url']); ?>" target="_blank">
                                        View Video
                                    </a>
                                <?php else: ?>
                                    <a href="#" onclick="addYouTubeUrl(<?php echo $video['id']; ?>, '<?php echo htmlspecialchars($video['name']); ?>')">
                                        Add YouTube URL
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($video['status']); ?>">
                                    <?php echo htmlspecialchars($video['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="#" onclick="viewVideoDetails(<?php echo $video['id']; ?>)" class="btn btn-primary">Details</a>
                                
                                <?php if ($video['status'] === 'Pending'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                                        <button type="submit" name="approve_video" class="btn btn-primary">Approve</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                                        <button type="submit" name="reject_video" class="btn btn-warning">Reject</button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                    <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                                    <button type="submit" name="delete_video" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No videos found.</p>
        <?php endif; ?>
    </div>
    
    <!-- Video Details Modal -->
    <div id="videoDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('videoDetailsModal')">&times;</span>
            <h3>Video Details</h3>
            <div id="videoDetails"></div>
        </div>
    </div>
    
    <!-- Add YouTube URL Modal -->
    <div id="youtubeUrlModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('youtubeUrlModal')">&times;</span>
            <h3>Add YouTube URL for <span id="videoName"></span></h3>
            <form method="post" action="" id="youtubeUrlForm">
                <input type="hidden" name="id" id="videoId">
                <div style="margin-bottom: 15px;">
                    <label for="youtube_url" style="display: block; margin-bottom: 5px; font-weight: bold;">YouTube URL:</label>
                    <input type="url" id="youtube_url" name="youtube_url" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Enter the full YouTube URL (e.g., https://www.youtube.com/watch?v=XXXXXXXXXXX)</p>
                </div>
                <button type="submit" name="update_youtube_url" class="btn">Save</button>
            </form>
        </div>
    </div>
    
    <script>
        // Function to view video details
        function viewVideoDetails(id) {
            // In a real application, you would fetch the video details via AJAX
            // For simplicity, we'll use the data already on the page
            <?php echo "var videos = " . json_encode($videos) . ";\n"; ?>
            
            var video = videos.find(function(v) { return v.id === id; });
            
            if (video) {
                var videoDetails = document.getElementById('videoDetails');
                videoDetails.innerHTML = `
                    <p><strong>Name:</strong> ${video.name}</p>
                    <p><strong>Subject:</strong> ${video.subject}</p>
                    <p><strong>Course:</strong> ${video.course}</p>
                    <p><strong>YouTube URL:</strong> ${video.youtube_url}</p>
                    <p><strong>Status:</strong> ${video.status}</p>
                    <p><strong>Date:</strong> ${video.created_at}</p>
                `;
                
                var modal = document.getElementById('videoDetailsModal');
                modal.style.display = 'block';
            }
        }
        
        // Function to add YouTube URL
        function addYouTubeUrl(id, name) {
            var videoId = document.getElementById('videoId');
            var videoName = document.getElementById('videoName');
            var youtubeUrlForm = document.getElementById('youtubeUrlForm');
            
            videoId.value = id;
            videoName.textContent = name;
            
            var modal = document.getElementById('youtubeUrlModal');
            modal.style.display = 'block';
        }
        
        // Function to close modal
        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.style.display = 'none';
        }
    </script>
</body>
</html>