<?php
// Set page specific variables
$page_title = "Home";

// Define the document root path for absolute includes
$root_path = dirname(__FILE__);

include $root_path . '/includes/header.php';

// Get featured courses (fallback to empty array if query fails)
try {
    $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY id DESC LIMIT 4");
    $stmt->execute();
    $courses = $stmt->fetchAll();
} catch (Exception $e) {
    $courses = [];
}

// Get testimonials (fallback to empty array if query fails)
try {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE status = 'Approved' ORDER BY id DESC LIMIT 3");
    $stmt->execute();
    $testimonials = $stmt->fetchAll();
} catch (Exception $e) {
    $testimonials = [];
}
?>
<!-- Hero Section -->
<section class="hero-section" style="position: relative; min-height: 10vh; display: flex; align-items: center; background-color: #FFD700; overflow: hidden; margin-top: -1px;">
    <div class="hero-background" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
        <img src="assets/images/slider1.jpg" alt="" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
        <div class="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)); z-index: 1;"></div>
    </div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="hero-content text-center" style="padding: 2rem 0;">
            <div class="hero-text">
                <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">Welcome to PR Classes</h1>
                <p class="lead text-white mb-4 animate__animated animate__fadeInUp" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);">Your journey towards Professional Revolution begins here</p>
                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="#about" class="btn btn-primary btn-lg me-3 shadow-sm smooth-scroll" style="padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 30px; transition: all 0.3s ease;">Discover More</a>
                    <a href="#featured-courses" class="btn btn-outline-light btn-lg smooth-scroll" style="padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 30px; transition: all 0.3s ease;">Explore Courses</a>
                </div>
            </div>
        </div>
    </div>
</section>
  <!-- Announcement Bar for Special Offers -->
  <div class="announcement-bar bg-primary text-white py-2 d-none d-lg-block marquee">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 small"><i class="fas fa-tag me-1"></i> Flat 50% Discount on all courses - Limited time offer!</p>
            </div>
        </div>
    </div>
    <style>
        .marquee {
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
        }
        .marquee p {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-100%, 0); }
        }
    </style>
<!-- About Section with ID for smooth scrolling -->
<section id="about" class="about-section py-5 bg-light">
    <div class="container">
        <div class="section-heading text-center mb-5">
            <h2 class="display-5 fw-bold">About PR Classes</h2>
            <div class="heading-underline mx-auto"></div>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image text-center">
                    <img src="assets/images/about/teacher.jpg" alt="PR Classes" class="img-fluid rounded-3 shadow-lg" style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="about-content ps-lg-4">
                    <h3 class="mb-3 text-primary">Welcome to my teaching hub</h3>
                    <p class="lead mb-3">Where passion meets expertise.</p>
                    <p>With a background in Engineering from the prestigious College of Engineering, Guindy Anna University, and as a qualified Cost and Management Accountant (CMA), my journey has equipped me with a robust blend of technical and financial acumen.</p>
                    
                    <p>Over the past 15 years, I've honed my skills in financial planning, appraisal, controlling, and costing, while working with various multinational companies.</p>
                    
                    <p>Driven by my love for teaching, I have dedicated the last 5+ years to sharing my knowledge and mentoring students in cost and management accounting. My teaching journey began at CMA Institute SIRC Chennai, where I have had the privilege of guiding 1000 plus aspiring professionals towards their career goals.</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <div class="mission-vision-card h-100 p-4 bg-white shadow-sm rounded-3 border-start border-primary border-4">
                    <div class="card-icon text-primary mb-3">
                        <i class="fas fa-bullseye fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-3">Our Mission</h3>
                    <p>To simplify complex concepts and instill confidence in learners as they navigate the dynamic world of finance. Whether you're preparing for exams, seeking career growth, or building a solid foundation in finance, we are here to support you every step of the way.</p>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="mission-vision-card h-100 p-4 bg-white shadow-sm rounded-3 border-start border-primary border-4">
                    <div class="card-icon text-primary mb-3">
                        <i class="fas fa-eye fa-3x"></i>
                    </div>
                    <h3 class="h4 mb-3">Our Vision</h3>
                    <p>To bridge the gap between academics and industry by creating a dynamic learning environment. We aim to empower students with strong conceptual clarity and practical insights for thriving careers in finance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<section class="featured-courses py-5" id="featured-courses">
    <div class="container">
        <div class="section-heading text-center mb-5">
            <h2>Our Featured Courses</h2>
            <p>Comprehensive and crash courses designed for your success</p>
        </div>
        
        <div class="row">
            <?php if(empty($courses)): ?>
                <div class="col-12 text-center">
                </div>
            <?php else: ?>
                <?php foreach($courses as $course): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($course['title']); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="badge bg-<?php echo ($course['enrollment_status'] == 'Open') ? 'success' : 'danger'; ?> mb-3">
                                <?php echo htmlspecialchars($course['enrollment_status']); ?> for Enrollment
                            </div>
                            
                            <p class="card-text"><?php echo substr(htmlspecialchars($course['description']), 0, 150); ?>...</p>
                            
                            <div class="course-details mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <p><i class="fas fa-graduation-cap me-2"></i> <?php echo htmlspecialchars($course['category']); ?></p>
                                    </div>
                                    <div class="col-6">
                                        <?php if(isset($course['limited_seats']) && $course['limited_seats']): ?>
                                        <p class="text-danger"><i class="fas fa-user-friends me-2"></i> Only <?php echo htmlspecialchars($course['seats_available']); ?> seats left!</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="price-section mb-3">
                                <?php if(isset($course['discount_percentage']) && $course['discount_percentage'] > 0): ?>
                                <p>
                                    <span class="original-price">₹<?php echo number_format($course['fees']); ?></span>
                                    <span class="discount-price text-success">₹<?php echo number_format($course['fees'] - ($course['fees'] * $course['discount_percentage'] / 100)); ?></span>
                                    <span class="discount-badge bg-warning text-dark"><?php echo $course['discount_percentage']; ?>% OFF</span>
                                </p>
                                <?php else: ?>
                                <p class="current-price">₹<?php echo number_format($course['fees']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between">
                                <a href="courses.php?category=<?php echo urlencode($course['category']); ?>" class="btn btn-outline-primary">View Details</a>
                                <?php if($course['enrollment_status'] == 'Open'): ?>
                                <a href="https://wa.me/919042796696?text=I'm%20interested%20in%20the%20<?php echo urlencode($course['title']); ?>%20course" class="btn btn-success">
                                    <i class="fab fa-whatsapp me-1"></i> Join Now
                                </a>
                                <?php else: ?>
                                <button class="btn btn-secondary" disabled>Enrollment Closed</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="courses_details.php" class="btn btn-primary btn-lg">View All Courses</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us py-5">
    <div class="container">
        <div class="section-heading text-center mb-5">
            <h2>Why Choose PR Classes?</h2>
            <p>We are dedicated to helping you achieve excellence in your CMA journey</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center p-4 h-100 bg-white shadow-sm rounded">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-trophy fa-3x text-primary"></i>
                    </div>
                    <h4>Proven Results</h4>
                    <p>Our students consistently achieve top ranks in CMA exams with our comprehensive guidance.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center p-4 h-100 bg-white shadow-sm rounded">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary"></i>
                    </div>
                    <h4>Expert Faculty</h4>
                    <p>Learn from experienced professionals who simplify complex concepts for better understanding.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center p-4 h-100 bg-white shadow-sm rounded">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-book fa-3x text-primary"></i>
                    </div>
                    <h4>Comprehensive Study Material</h4>
                    <p>Get access to meticulously prepared study materials that cover the entire syllabus effectively.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="qr-code-floater">
<img src="./images/QRcode.png" class="qr-code" alt="QR Code" />
    <button class="close-btn">&times;</button>
    <p>Scan this QR code to make a payment.</p>
</div>

<!-- Add smooth scrolling script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to all links with the smooth-scroll class
    const smoothScrollLinks = document.querySelectorAll('a.smooth-scroll, a[href^="#"]');
    
    for (const link of smoothScrollLinks) {
        link.addEventListener('click', function(e) {
            // Only apply to internal links starting with #
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // Add offset for fixed header if needed
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    }
});
</script>

<?php include $root_path . '/includes/footer.php'; ?>