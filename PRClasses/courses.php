<?php
// Define the document root path for absolute includes
$root_path = $_SERVER['DOCUMENT_ROOT'];

include $root_path . '/includes/header.php'; 

// Get category from URL parameter
$category = isset($_GET['category']) ? cleanInput($_GET['category']) : "CMA Inter";

// Validate category
if ($category != "CMA Inter" && $category != "CMA Final") {
    $category = "CMA Inter";
}

// Function to get courses by category
function getCoursesByCategory($pdo, $category) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE category = ? ORDER BY id DESC");
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

// Get courses for the selected category
$courses = getCoursesByCategory($pdo, $category);
?>

<section class="page-header bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 text-center">
            <h1 class="h2 fw-bold mb-0" style="text-align: left !important;"><?php echo $category; ?> Courses</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white opacity-75">Home</a></li>
                        <li class="breadcrumb-item"><a href="courses.php" class="text-white opacity-75">Courses</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $category; ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button id="scan-for-payment-btn" class="btn btn-warning btn-sm">
                    <i class="fas fa-qrcode me-1"></i> Scan for Payment
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Courses Section with Clean Styling -->
<section class="courses-section py-4">
    <div class="container">
        <!-- Simple Category Tabs -->
        <div class="category-tabs mb-4">
            <div class="nav nav-pills">
                <a class="nav-link <?php echo ($category == 'CMA Inter') ? 'active' : ''; ?>" href="courses.php?category=CMA+Inter">
                    CMA Inter
                </a>
                <a class="nav-link <?php echo ($category == 'CMA Final') ? 'active' : ''; ?>" href="courses.php?category=CMA+Final">
                    CMA Final
                </a>
            </div>
        </div>
        
        <!-- Simple Category Description -->
        <div class="category-description mb-4">
            <p class="text-muted mb-0">
                <?php if($category == "CMA Inter"): ?>
                Our CMA Intermediate courses build a strong foundation in cost and management accounting with expert guidance.
                <?php else: ?>
                Our CMA Final courses provide in-depth knowledge and advanced concepts for professional excellence.
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Single Clean Offer Banner -->
        <div class="discount-banner mb-4 text-center">
            <span class="badge bg-primary px-3 py-2">Limited-time offer: 50% discount on all courses</span>
        </div>
        
        <?php if(empty($courses)): ?>
        <div class="alert alert-light text-center py-4 border">
            <p class="mb-0">No courses available for <?php echo $category; ?> at the moment. Please check back later or contact us for more information.</p>
        </div>
        <?php else: ?>
        
        <!-- Clean Course Grid Layout -->
        <div class="row">
            <?php foreach($courses as $course): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 course-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><?php echo $course['title']; ?></h5>
                        <?php if($course['enrollment_status'] == 'Open'): ?>
                        <span class="badge bg-success status-badge">
                            Open for Enrollment
                        </span>
                        <?php else: ?>
                        <span class="badge bg-secondary status-badge">
                            Enrollment Closed
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body pt-3">
                        <div class="course-content">
                            <p class="card-text mb-3"><?php echo $course['description']; ?></p>
                            
                            <?php if(!empty($course['schedule'])): ?>
                            <div class="course-schedule mb-3">
                                <p class="mb-1 fw-medium"><i class="fas fa-calendar-alt me-2 text-primary"></i> Schedule:</p>
                                <div class="schedule-details">
                                    <?php echo nl2br($course['schedule']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($course['limited_seats']): ?>
                            <p class="text-muted small"><i class="fas fa-user-friends me-1"></i> Only <?php echo $course['seats_available']; ?> seats remaining</p>
                            <?php endif; ?>
                            
                            <div class="price-section mt-3">
                                <?php if($course['discount_percentage'] > 0): ?>
                                <div class="d-flex align-items-baseline">
                                    <s class="text-muted me-2">₹<?php echo number_format($course['fees']); ?></s>
                                    <span class="text-primary fs-4 fw-bold">₹<?php echo number_format($course['fees'] - ($course['fees'] * $course['discount_percentage'] / 100)); ?></span>
                                </div>
                                <?php else: ?>
                                <p class="text-primary fs-4 fw-bold mb-0">₹<?php echo number_format($course['fees']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="share-dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleShareOptions(this)">
                                    <i class="fas fa-share-alt me-1"></i> Share
                                </button>
                                <div class="share-options">
                                    <a href="https://wa.me/?text=Check%20out%20this%20course%20at%20PR%20Classes:%20<?php echo urlencode($course['title']); ?>%20-%20<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank">
                                        <i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank">
                                        <i class="fab fa-facebook me-2 text-primary"></i> Facebook
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($course['title']); ?>" target="_blank">
                                        <i class="fab fa-linkedin me-2 text-info"></i> LinkedIn
                                    </a>
                                </div>
                            </div>
                            
                            <?php if($course['enrollment_status'] == 'Open'): ?>
                            <a href="https://wa.me/919042796696?text=I'm%20interested%20in%20the%20<?php echo urlencode($course['title']); ?>%20course" class="btn btn-primary btn-sm">
                                <i class="fab fa-whatsapp me-1"></i> Join Now
                            </a>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>Enrollment Closed</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Simple Call to Action -->
<section class="cta-section py-4">
    <div class="container">
        <div class="text-center">
            <p class="mb-2">Need a customized learning program?</p>
            <a href="contact.php" class="btn btn-outline-primary btn-sm">
                Contact Us
            </a>
        </div>
    </div>
</section>

<!-- Add page-specific JavaScript -->
<script>
    // Function to toggle share options for mobile compatibility
    function toggleShareOptions(button) {
        const shareOptions = button.nextElementSibling;
        if (shareOptions.style.display === 'flex') {
            shareOptions.style.display = 'none';
        } else {
            // Close all other open share options first
            document.querySelectorAll('.share-options').forEach(el => {
                el.style.display = 'none';
            });
            shareOptions.style.display = 'flex';
        }
        
        // Close share options when clicking outside
        document.addEventListener('click', function closeShareOptions(e) {
            if (!e.target.closest('.share-dropdown')) {
                shareOptions.style.display = 'none';
                document.removeEventListener('click', closeShareOptions);
            }
        });
    }
</script>

<!-- Add page-specific CSS -->
<style>
    /* Mobile-friendly styles for courses page */
    @media (max-width: 768px) {
        .category-tabs .nav {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }
        
        .category-tabs .nav-link {
            white-space: nowrap;
            margin-right: 0.5rem;
        }
    }
    
    /* Share dropdown styles for mobile */
    .share-dropdown {
        position: relative;
    }
    
    .share-options {
        display: none;
        position: absolute;
        left: 0;
        top: 100%;
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        flex-direction: column;
        min-width: 150px;
        z-index: 100;
        margin-top: 0.5rem;
    }
    
    .share-options a {
        padding: 8px 12px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }
    
    .share-options a:hover {
        background: #f8f9fa;
    }
</style>
            <!-- QR Code Floater -->
            <div class="qr-code-floater">
                <div class="close-btn">&times;</div>
                <img src="images/QRCode.png" alt="Scan QR Code">
                <p>Scan for Payment</p>
            </div>
            
<?php include $root_path . '/includes/footer.php'; ?>