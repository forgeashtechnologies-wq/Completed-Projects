<?php
session_start();

// Use absolute path to config.php
$config_path = realpath(dirname(__FILE__) . '/../admin/config.php');
if (!$config_path || !file_exists($config_path)) {
    die("Error: Could not find config.php at: " . dirname(__FILE__) . '/../admin/config.php');
}
require_once($config_path);

// Check if user is admin
if (!isAdminLoggedIn()) {
    header('Location: ../admin/login.php');
    exit;
}

// Include CloudBlogPoster to get available topics
require_once('cloud_blog_poster.php');
$poster = new CloudBlogPoster();
$available_topics = array_keys($poster->getTopics());

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Blog Posts - Tara's Dental & Aesthetic Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .output { 
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Blog Posts</h1>
        <form id="generateForm" class="mb-4">
            <div class="mb-3">
                <label for="openai_key" class="form-label">OpenAI API Key</label>
                <input type="password" class="form-control" id="openai_key" name="openai_key" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Select Topics to Generate</label>
                <?php foreach ($available_topics as $topic): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="topics[]" value="<?php echo htmlspecialchars($topic); ?>" id="topic_<?php echo htmlspecialchars($topic); ?>">
                    <label class="form-check-label" for="topic_<?php echo htmlspecialchars($topic); ?>">
                        <?php echo htmlspecialchars($topic); ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary">Generate Posts</button>
        </form>
        
        <div id="output" class="output"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#generateForm').on('submit', function(e) {
                e.preventDefault();
                
                $('#output').html('Starting blog post generation...\n');
                
                $.ajax({
                    url: 'process_posts.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#output').append(response);
                    },
                    error: function(xhr, status, error) {
                        $('#output').append('Error: ' + error + '\n');
                    }
                });
            });
        });
    </script>
</body>
</html>
