<?php
// Define root path for absolute includes
$root_path = $_SERVER['DOCUMENT_ROOT'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $root_path . '/includes/config.php';
require_once $root_path . '/includes/functions.php';

// Set page title
$page_title = 'Success Stories';
$page_specific_css = 'success-stories';

// Get current section from URL parameter
$current_section = isset($_GET['section']) ? $_GET['section'] : 'testimonials';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle testimonial submission
    if (isset($_POST['submit_testimonial'])) {
        try {
            // Validate inputs
            $name = cleanInput($_POST['name'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $course = cleanInput($_POST['course'] ?? '');
            $testimonial = cleanInput($_POST['testimonial'] ?? '');
            $rating = intval($_POST['rating'] ?? 5);
            
            // Basic validation
            if (empty($name) || empty($email) || empty($testimonial)) {
                throw new Exception("Please fill all required fields");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Please enter a valid email address");
            }
            
            // Limit rating between 1-5
            $rating = max(1, min(5, $rating));
            
            // Set status to pending for moderation
            $status = 'pending';
            
            // Debugging: Log values before insertion
            error_log("Inserting testimonial with values: " . json_encode([$name, $email, $course, $testimonial, $rating, $status]));
            
            // Prepare and execute the query
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, email, course, testimonial, rating, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$name, $email, $course, $testimonial, $rating, $status]);
            
            if ($result) {
                $success_message = "Thank you for your testimonial! It will be reviewed and published soon.";
            } else {
                throw new Exception("Failed to submit testimonial. Please try again.");
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
    
    // Handle marksheet submission
    if (isset($_POST['submit_marksheet'])) {
        $name = cleanInput($_POST['name']);
        $registration_no = cleanInput($_POST['registration_no']);
        $subject = cleanInput($_POST['subject']);
        $course = cleanInput($_POST['course']);
        $year = cleanInput($_POST['year']);
        $mobile = cleanInput($_POST['mobile']);
        $email = cleanInput($_POST['email']);
        $marksheet_path = '';
        $image_path = '';
        
        // Handle marksheet upload
        if (isset($_FILES['marksheet_image']) && $_FILES['marksheet_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/marksheets/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['marksheet_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['marksheet_image']['tmp_name'], $target_file)) {
                $marksheet_path = $target_file;
            } else {
                $error_message = "Failed to upload marksheet image.";
            }
        } else {
            $error_message = "Marksheet image is required.";
        }
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profiles/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
        
        if (!isset($error_message) && !empty($marksheet_path)) {
            // Debugging: Log values before insertion
            error_log("Inserting marksheet with values: " . json_encode([$name, $registration_no, $subject, $course, $year, $mobile, $email, $marksheet_path, $image_path]));
            
            // Insert marksheet into database
            $stmt = $pdo->prepare("INSERT INTO marksheets (name, registration_no, subject, course, year, mobile, email, marksheet_path, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $registration_no, $subject, $course, $year, $mobile, $email, $marksheet_path, $image_path])) {
                $success_message = "Your marksheet has been submitted successfully and is pending approval.";
            } else {
                $error_message = "Failed to submit marksheet. Please try again.";
            }
        }
    }
    
    // Handle video feedback submission
    if (isset($_POST['submit_video'])) {
        $name = cleanInput($_POST['name']);
        $registration_no = cleanInput($_POST['registration_no']);
        $subject = cleanInput($_POST['subject']);
        $course = cleanInput($_POST['course']);
        $year = cleanInput($_POST['year']);
        $mobile = cleanInput($_POST['mobile']);
        $email = cleanInput($_POST['email']);
        $image_path = '';
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profiles/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
        
        // Debugging: Log values before insertion
        error_log("Inserting video with values: " . json_encode([$name, $registration_no, $subject, $course, $year, $mobile, $email, $image_path]));
        
        // Insert video into database
        $stmt = $pdo->prepare("INSERT INTO videos (name, registration_no, subject, course, year, mobile, email, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $registration_no, $subject, $course, $year, $mobile, $email, $image_path])) {
            // Generate WhatsApp message with video submission details
            $whatsapp_message = "New Video Feedback Submission:\n";
            $whatsapp_message .= "Name: $name\n";
            $whatsapp_message .= "Registration No: $registration_no\n";
            $whatsapp_message .= "Subject: $subject\n";
            $whatsapp_message .= "Course: $course\n";
            $whatsapp_message .= "Year: $year\n";
            $whatsapp_message .= "Mobile: $mobile\n";
            $whatsapp_message .= "Email: $email\n";
            
            // Encode message for WhatsApp URL
            $encoded_message = urlencode($whatsapp_message);
            $whatsapp_url = "https://wa.me/919042796696?text=$encoded_message";
            
            // Redirect to WhatsApp
            header("Location: $whatsapp_url");
            exit();
        } else {
            $error_message = "Failed to submit video feedback. Please try again.";
        }
    }
}

// Get approved testimonials
$stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'Approved' ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll();

// Get approved marksheets
$stmt = $pdo->query("SELECT * FROM marksheets WHERE status = 'Approved' ORDER BY created_at DESC");
$marksheets = $stmt->fetchAll();

// Get approved videos
$stmt = $pdo->query("SELECT * FROM videos WHERE status = 'Approved' AND youtube_url IS NOT NULL ORDER BY created_at DESC");
$videos = $stmt->fetchAll();

// Include header
include $root_path . '/includes/header.php';
?>

<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="fw-bold mb-2">Success Stories</h1>
                <p class="lead">Discover how our students are achieving excellence in their CMA journey.</p>
            </div>
        </div>
    </div>
</section>

<div class="container mt-5">
    <!-- Section Navigation Tabs -->
    <div class="row">
        <div class="col-md-10 mx-auto">
            <ul class="nav nav-pills nav-fill mb-5 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_section === 'testimonials' ? 'active' : ''; ?>" href="?section=testimonials">
                        <i class="fas fa-comment-dots me-2"></i>Testimonials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_section === 'marksheets' ? 'active' : ''; ?>" href="?section=marksheets">
                        <i class="fas fa-file-alt me-2"></i>Marksheets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_section === 'videos' ? 'active' : ''; ?>" href="?section=videos">
                        <i class="fas fa-video me-2"></i>Feedback Videos
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <?php if ($current_section === 'testimonials'): ?>
        <!-- Testimonials Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="testimonials-container">
                    <?php if (count($testimonials) > 0): ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar">
                                        <span><?php echo substr($testimonial['name'], 0, 1); ?></span>
                                    </div>
                                    <div class="testimonial-meta">
                                        <h5 class="testimonial-name"><?php echo $testimonial['name']; ?></h5>
                                        <p class="testimonial-course"><?php echo $testimonial['course']; ?></p>
                                        <div class="testimonial-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'active' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    <p><?php echo $testimonial['testimonial']; ?></p>
                                </div>
                                <div class="testimonial-footer">
                                    <span class="testimonial-date"><?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No testimonials available yet. Be the first to share your experience!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-container">
                    <h4 class="form-title">
                        <i class="fas fa-comment-dots me-2"></i>Share Your Experience
                    </h4>
                    <form action="success-stories.php?section=testimonials" method="post" class="testimonial-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" name="course">
                        </div>
                        <div class="mb-3">
                            <label for="testimonial" class="form-label">Your Testimonial <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="testimonial" name="testimonial" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                                    <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <button type="submit" name="submit_testimonial" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Submit Testimonial
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif ($current_section === 'marksheets'): ?>
        <!-- Marksheets Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="marksheets-container">
                    <?php if (count($marksheets) > 0): ?>
                        <div class="row">
                            <?php foreach ($marksheets as $marksheet): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="marksheet-card">
                                        <div class="marksheet-header">
                                            <div class="marksheet-avatar">
                                                <?php if (!empty($marksheet['image_path'])): ?>
                                                    <img src="<?php echo $marksheet['image_path']; ?>" alt="<?php echo $marksheet['name']; ?>">
                                                <?php else: ?>
                                                    <span><?php echo substr($marksheet['name'], 0, 1); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="marksheet-meta">
                                                <h5 class="marksheet-name"><?php echo $marksheet['name']; ?></h5>
                                                <p class="marksheet-course"><?php echo $marksheet['course']; ?> - <?php echo $marksheet['subject']; ?></p>
                                                <p class="marksheet-year"><?php echo $marksheet['year']; ?></p>
                                            </div>
                                        </div>
                                        <div class="marksheet-content">
                                            <a href="<?php echo $marksheet['marksheet_path']; ?>" class="marksheet-link" target="_blank">
                                                <i class="fas fa-file-alt me-2"></i>View Marksheet
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No marksheets available yet. Be the first to share your success!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-container">
                    <h4 class="form-title">
                        <i class="fas fa-upload me-2"></i>Share Your Success
                    </h4>
                    <form action="success-stories.php?section=marksheets" method="post" enctype="multipart/form-data" class="marksheet-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="registration_no" class="form-label">Registration No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="registration_no" name="registration_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course" name="course" required>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="year" name="year" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="marksheet_image" class="form-label">Marksheet Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="marksheet_image" name="marksheet_image" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Your Photo</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                        <button type="submit" name="submit_marksheet" class="btn btn-primary w-100">
                            <i class="fas fa-upload me-2"></i>Submit Marksheet
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif ($current_section === 'videos'): ?>
        <!-- Videos Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="videos-container">
                    <?php if (count($videos) > 0): ?>
                        <div class="row">
                            <?php foreach ($videos as $video): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="video-card">
                                        <div class="video-header">
                                            <div class="video-avatar">
                                                <?php if (!empty($video['image_path'])): ?>
                                                    <img src="<?php echo $video['image_path']; ?>" alt="<?php echo $video['name']; ?>">
                                                <?php else: ?>
                                                    <span><?php echo substr($video['name'], 0, 1); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="video-meta">
                                                <h5 class="video-name"><?php echo $video['name']; ?></h5>
                                                <p class="video-course"><?php echo $video['course']; ?> - <?php echo $video['subject']; ?></p>
                                                <p class="video-year"><?php echo $video['year']; ?></p>
                                            </div>
                                        </div>
                                        <div class="video-content">
                                            <div class="video-embed">
                                                <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?php echo getYoutubeVideoId($video['youtube_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No feedback videos available yet. Be the first to share your experience!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-container">
                    <h4 class="form-title">
                        <i class="fas fa-video me-2"></i>Share Your Video Feedback
                    </h4>
                    <form action="success-stories.php?section=videos" method="post" enctype="multipart/form-data" class="video-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="registration_no" class="form-label">Registration No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="registration_no" name="registration_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course" name="course" required>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="year" name="year" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Your Photo</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                        <p class="text-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>After submitting, you will be redirected to WhatsApp to share your video.
                        </p>
                        <button type="submit" name="submit_video" class="btn btn-primary w-100">
                            <i class="fas fa-video me-2"></i>Submit Video Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
</div>

<?php
// Helper function to extract YouTube video ID from URL
function getYoutubeVideoId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    if (preg_match($pattern, $url, $match)) {
        return $match[1];
    }
    return false;
}

// Include footer
include $root_path . '/includes/footer.php';
?>
