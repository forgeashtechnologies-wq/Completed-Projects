<?php
// Define the document root path for absolute includes
$root_path = dirname(dirname(__FILE__));

// Include error handler first using absolute path
require_once $root_path . '/includes/error_handler.php';

// Include maintenance mode checker
require_once $root_path . '/includes/maintenance.php';

// Include configuration
require_once $root_path . '/includes/config.php';

// Include default values
require_once $root_path . '/includes/defaults.php';

// Include functions
require_once $root_path . '/includes/functions.php';

// Include security functions
require_once $root_path . '/includes/security.php';

// Include schema checker
if (file_exists($root_path . '/includes/schema_checker.php')) {
    require_once $root_path . '/includes/schema_checker.php';
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_category = isset($_GET['category']) ? $_GET['category'] : '';
$current_section = isset($_GET['section']) ? $_GET['section'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>PR Classes - Excellence in CMA Education</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="PR Classes offers comprehensive and crash courses for CMA Inter and Final students. Learn from experienced professionals with proven results.">
    <meta name="keywords" content="CMA coaching, Cost accounting, Financial management, Online classes, Chennai">
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <?php include $root_path . '/includes/favicon_links.php'; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/footer-fix.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/qr-code-floater.css">
    
    <!-- Add Animate.css library for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <?php if(isset($page_specific_css)): ?>
    <link rel="stylesheet" href="assets/css/<?php echo $page_specific_css; ?>.css">
    <?php endif; ?>
    
    <!-- Schema.org structured data for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "PR Classes",
      "description": "Excellence in CMA Education",
      "url": "https://prclasses.com",
      "telephone": "+919042796696",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Chennai",
        "addressRegion": "Tamil Nadu",
        "addressCountry": "IN"
      }
    }
    </script>
</head>
<body>
  
    
    <!-- Main Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo -->
                <a href="index.php" class="navbar-brand">
                    <span class="logo-icon">PR</span>
                    <div class="logo-text">
                        <span class="logo-large">Classes</span>
                    </div>
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                    
                    <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'about.php' || ($current_page == 'index.php' && isset($_GET['section']) && $_GET['section'] == 'about')) ? 'active' : ''; ?>" href="<?php echo ($current_page == 'index.php') ? '#about' : 'index.php#about'; ?>">About Us</a>
                        </li>
                        
                        <!-- Success Stories menu item hidden as requested
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($current_page == 'success-stories.php' || $current_page == 'testimonials.php' || $current_page == 'marksheets.php' || $current_page == 'videos.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Success Stories
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo ($current_page == 'success-stories.php' && $current_section == 'videos') ? 'active' : ''; ?>" href="success-stories.php?section=videos">Feedback Videos</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page == 'success-stories.php' && $current_section == 'testimonials') ? 'active' : ''; ?>" href="success-stories.php?section=testimonials">Testimonials</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page == 'success-stories.php' && $current_section == 'marksheets') ? 'active' : ''; ?>" href="success-stories.php?section=marksheets">Marksheets</a></li>
                            </ul>
                        </li>
                        -->
                        
                        <!-- Courses1 menu item hidden as requested
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($current_page == 'courses1.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Courses1
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item <?php echo ($current_page == 'courses1.php' && $current_category == 'CMA Inter') ? 'active' : ''; ?>" href="courses1.php?category=CMA+Inter">CMA Inter</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page == 'courses1.php' && $current_category == 'CMA Final') ? 'active' : ''; ?>" href="courses1.php?category=CMA+Final">CMA Final</a></li>
                            </ul>
                        </li>
                        -->
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'courses_details.php') ? 'active' : ''; ?>" href="courses_details.php">Courses</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                        </li>
                        
                        <!-- Primary CTA Button -->
                        <li class="nav-item">
                            <a href="https://forms.gle/8EokfgsFmqqFGqJ49" class="btn btn-primary">Enroll Now</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Header Spacing to prevent content from hiding behind fixed header -->
    <div class="header-spacing"></div>
    <?php if(isset($page_specific_js)): ?>
    <script src="assets/js/<?php echo $page_specific_js; ?>.js"></script>
    <?php endif; ?>

    <div class="social-channels">
        <!-- Removed WhatsApp and Telegram links -->
    </div>
</body>
</html>