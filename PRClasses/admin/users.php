<?php
require_once '../includes/functions.php';

// Check if admin is logged in
check_admin_login();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new user
    if (isset($_POST['add_user'])) {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate input
        if (empty($username) || empty($password) || empty($confirm_password)) {
            display_error_message("All fields are required.");
        } else if ($password !== $confirm_password) {
            display_error_message("Passwords do not match.");
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user_exists = $stmt->fetch();
            
            if ($user_exists) {
                display_error_message("Username already exists.");
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                
                if ($stmt->execute([$username, $password_hash])) {
                    display_success_message("User added successfully!");
                } else {
                    display_error_message("Failed to add user.");
                }
            }
        }
    }
    
    // Change password
    if (isset($_POST['change_password'])) {
        $user_id = (int) $_POST['user_id'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];
        
        // Validate input
        if (empty($new_password) || empty($confirm_new_password)) {
            display_error_message("All fields are required.");
        } else if ($new_password !== $confirm_new_password) {
            display_error_message("Passwords do not match.");
        } else {
            // Hash new password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update user password
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            
            if ($stmt->execute([$password_hash, $user_id])) {
                display_success_message("Password changed successfully!");
            } else {
                display_error_message("Failed to change password.");
            }
        }
    }
    
    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = (int) $_POST['user_id'];
        $admin_id = $_SESSION['admin_id'];
        
        // Prevent admin from deleting their own account
        if ($user_id === $admin_id) {
            display_error_message("You cannot delete your own account.");
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            
            if ($stmt->execute([$user_id])) {
                display_success_message("User deleted successfully!");
            } else {
                display_error_message("Failed to delete user.");
            }
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY username");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - <?php echo SITE_NAME; ?></title>
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
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .user-list {
            width: 100%;
            border-collapse: collapse;
        }
        .user-list th, .user-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-list th {
            background-color: #f2f2f2;
        }
        .user-list tr:hover {
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
            max-width: 500px;
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
    <div class="container">
        <div class="header">
            <h2>User Management</h2>
            <a href="index.php" class="btn">Back to Dashboard</a>
        </div>
        
        <?php if ($success_message = get_success_message()): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message = get_error_message()): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h3>Add New User</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="add_user" class="btn">Add User</button>
            </form>
        </div>
        
        <h3>Existing Users</h3>
        <?php if (count($users) > 0): ?>
            <table class="user-list">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="action-buttons">
                                <button class="btn btn-primary" onclick="openChangePasswordModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">Change Password</button>
                                
                                <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                                    <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
    
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Change Password for <span id="modalUsername"></span></h3>
            <form method="post" action="">
                <input type="hidden" id="modalUserId" name="user_id">
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                </div>
                
                <button type="submit" name="change_password" class="btn">Change Password</button>
            </form>
        </div>
    </div>
    
    <script>
        // Get the modal
        var modal = document.getElementById("changePasswordModal");
        
        // Function to open the change password modal
        function openChangePasswordModal(userId, username) {
            document.getElementById("modalUserId").value = userId;
            document.getElementById("modalUsername").textContent = username;
            modal.style.display = "block";
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
</body>
</html>