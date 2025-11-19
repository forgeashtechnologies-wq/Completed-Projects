<?php
// Define root path for absolute includes
$root_path = $_SERVER['DOCUMENT_ROOT'];
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Get admin username
$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .dashboard-header h2 {
            margin: 0;
        }
        .dashboard-header .admin-info {
            display: flex;
            align-items: center;
        }
        .dashboard-header .admin-info span {
            margin-right: 10px;
        }
        .dashboard-header .logout-btn {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .dashboard-menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .menu-item {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .menu-item:hover {
            background-color: #e9e9e9;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .menu-item a {
            text-decoration: none;
            color: #333;
        }
        .menu-item h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .menu-item p {
            margin: 0;
            color: #666;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-item {
            padding: 15px;
            background-color: #f0f8ff;
            border-radius: 5px;
            text-align: center;
        }
        .stat-item h4 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #333;
        }
        .stat-item .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
            <div class="admin-info">
                <span>Welcome, <?php echo htmlspecialchars($admin_username); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="dashboard-menu">
            <div class="menu-item">
                <a href="/admin/courses.php">
                    <h3>Course Management</h3>
                    <p>Add, edit, or delete courses</p>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="/admin/testimonials.php">
                    <h3>Testimonials</h3>
                    <p>Manage student testimonials</p>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="/admin/marksheets.php">
                    <h3>Marksheets</h3>
                    <p>Manage student marksheets</p>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="/admin/videos.php">
                    <h3>Feedback Videos</h3>
                    <p>Manage student feedback videos</p>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="/admin/gallery.php">
                    <h3>Gallery</h3>
                    <p>Manage photos and videos</p>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="/admin/users.php">
                    <h3>User Management</h3>
                    <p>Manage admin users</p>
                </a>
            </div>
        </div>
        
        <h3>Quick Stats</h3>
        <div class="stats-container">
            <?php
            // Get pending testimonials count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM testimonials WHERE status = 'Pending'");
            $pending_testimonials = $stmt->fetch()['count'];
            
            // Get pending marksheets count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM marksheets WHERE status = 'Pending'");
            $pending_marksheets = $stmt->fetch()['count'];
            
            // Get pending videos count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM videos WHERE status = 'Pending'");
            $pending_videos = $stmt->fetch()['count'];
            
            // Get total courses count
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
            $total_courses = $stmt->fetch()['count'];
            ?>
            
            <div class="stat-item">
                <h4>Pending Testimonials</h4>
                <div class="stat-value"><?php echo $pending_testimonials; ?></div>
            </div>
            
            <div class="stat-item">
                <h4>Pending Marksheets</h4>
                <div class="stat-value"><?php echo $pending_marksheets; ?></div>
            </div>
            
            <div class="stat-item">
                <h4>Pending Videos</h4>
                <div class="stat-value"><?php echo $pending_videos; ?></div>
            </div>
            
            <div class="stat-item">
                <h4>Total Courses</h4>
                <div class="stat-value"><?php echo $total_courses; ?></div>
            </div>
        </div>
    </div>
    
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