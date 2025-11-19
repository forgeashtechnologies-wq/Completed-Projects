<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start with PHP code execution, no output
$pageTitle = "Blog Post - Tara's Dental & Aesthetic Center";
$error_message = null;

// Include configuration after any output buffering
require_once '../admin/config.php';

// Get database connection
$db = getDBConnection();

// Get post ID
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch post with categories
$stmt = $db->prepare("
    SELECT p.*, GROUP_CONCAT(c.name) as categories 
    FROM blog_posts p 
    LEFT JOIN post_categories pc ON p.id = pc.post_id 
    LEFT JOIN blog_categories c ON pc.category_id = c.id 
    WHERE p.id = ?
    GROUP BY p.id
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit;
}

// Get recent posts for sidebar
$stmt = $db->prepare("
    SELECT * FROM blog_posts 
    WHERE id != ? 
    ORDER BY created_at DESC 
    LIMIT 3
");
$stmt->execute([$post_id]);
$recent_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for sidebar
$stmt = $db->query("
    SELECT c.*, COUNT(pc.post_id) as post_count 
    FROM blog_categories c 
    LEFT JOIN post_categories pc ON c.id = pc.category_id 
    GROUP BY c.id 
    ORDER BY c.name
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?> - Tara's Dental & Aesthetic Center</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta property="og:title" content="<?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?> - Tara's Dental & Aesthetic Center">
    <meta name="twitter:title" content="<?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?> - Tara's Dental & Aesthetic Center">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="../css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../css/ionicons.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.timepicker.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/icomoon.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="shortcut icon" href="../images/tara's dental logo.png" type="image/x-icon">
    <style>
    
    /* Single Post Styles */
    
    /* Navigation button color fix */
    .nav-item.active > a,
    .nav-item > a {
        color: #fff !important;
    }
        
    /* Post content image styles */
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
        
    /* Post styles */
    .post-body {
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
        
    /* Meta information styles */
    .meta-info-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .meta-primary {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95em;
        color: #666;
    }

    .meta-separator {
        color: #ddd;
        margin: 0 0.5rem;
    }

    .meta-primary span[class^="icon-"] {
        color: #4e9525;
        margin-right: 0.25rem;
    }

    .category-link {
        color: #666;
        transition: all 0.2s ease;
    }

    .category-link:hover {
        color: #4e9525;
        text-decoration: none;
    }

    /* Share Buttons Styles */
    .meta-share {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.75rem;
        margin-top: 0.5rem;
        border-top: 1px solid #f5f5f5;
    }

    .share-label {
        color: #666;
        font-size: 0.95em;
        font-weight: 500;
        margin-right: 0.5rem;
    }

    .share-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .share-btn {
        color: #666;
        font-size: 1.1em;
        transition: all 0.2s ease;
        padding: 5px;
        border-radius: 50%;
    }

    .share-btn:hover {
        color: #4e9525;
        text-decoration: none;
        background-color: #f0f7eb;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .meta-primary {
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .meta-separator {
            display: none;
        }
        
        .meta-share {
            width: 100%;
            justify-content: flex-start;
        }
    }
    
    /* Navigation button color fix */
    .nav-item.active > a,
    .nav-item > a {
        color: #fff !important;
    }

    /* Sidebar Styles */
    
    .sidebar-box {
        margin-bottom: 40px;
        padding: 25px;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .sidebar-box h3 {
        font-size: 20px;
        margin-bottom: 20px;
        color: #4e9525;
        font-weight: 600;
    }

    /* Categories */
    .categories {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .categories li {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .categories li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .categories li a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .categories li a span {
        color: #999;
        min-width: 30px;
        text-align: right;
        margin-left: 10px;
    }

    .categories li a:hover {
        color: #4e9525;
    }

    .categories li a:hover span {
        color: #4e9525;
    }

    /* Recent Blog Posts */
    .block-21 {
        display: flex;
        margin-bottom: 2rem;
        align-items: flex-start;
    }

    .block-21:last-child {
        margin-bottom: 0;
    }

    .block-21 .blog-img {
        width: 80px;
        height: 80px;
        min-width: 80px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 4px;
        margin-right: 20px;
    }

    .block-21 .text {
        flex: 1;
    }

    .block-21 .text .heading {
        font-size: 16px;
        margin: 0 0 10px 0;
        line-height: 1.4;
    }

    .block-21 .text .heading a {
        color: #666;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .block-21 .text .heading a:hover {
        color: #4e9525;
    }

    .block-21 .text .meta {
        margin-top: 0;
    }

    .block-21 .text .meta div {
        display: inline-block;
        margin-right: 15px;
        font-size: 14px;
        color: #999;
    }

    .block-21 .text .meta div:last-child {
        margin-right: 0;
    }

    .block-21 .text .meta div a {
        color: #999;
        text-decoration: none;
    }

    .block-21 .text .meta div a:hover {
        color: #4e9525;
    }

    .block-21 .text .meta div span {
        margin-right: 5px;
    }
    
    /* Social sharing styles */
    .share-post-top {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .share-post-top .social-meta-button,
    .share-post-bottom .social-meta-button {
        color: #666;
        transition: color 0.3s ease;
        font-size: 1.2em;
    }

    .share-post-top .social-meta-button:hover,
    .share-post-bottom .social-meta-button:hover {
        color: #4e9525;
    }

    .post-social {
        margin: 0;
        padding: 0;
    }

    .social-meta-button {
        text-decoration: none;
    }

    .share-post-bottom {
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    </style>
    <style>
    /* Modern Blog Styles */
    .bg-white {
        background-color: #ffffff;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .text-green-700 {
        color: #4e9525;
    }

    .text-green-800 {
        color: #3d7a1e;
    }

    .hover\:text-green-700:hover {
        color: #4e9525;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .text-gray-700 {
        color: #374151;
    }

    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }

    .space-y-4 > * + * {
        margin-top: 1rem;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .mb-8 {
        margin-bottom: 2rem;
    }

    .p-6 {
        padding: 1.5rem;
    }

    .pb-4 {
        padding-bottom: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-xl {
        font-size: 1.25rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-semibold {
        font-weight: 600;
    }

    .block {
        display: block;
    }

    .border-b {
        border-bottom-width: 1px;
        border-color: #e5e7eb;
    }

    .last\:border-b-0:last-child {
        border-bottom-width: 0;
    }

    .last\:pb-0:last-child {
        padding-bottom: 0;
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
    </style>
    <style>
    /* Additional styles for recent posts */
    .space-x-3 > * + * {
        margin-left: 0.75rem;
    }

    .space-x-4 > * + * {
        margin-left: 1rem;
    }

    .flex-shrink-0 {
        flex-shrink: 0;
    }

    .min-w-0 {
        min-width: 0;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .text-base {
        font-size: 1rem;
    }

    .blog-img {
        display: block;
        transition: transform 0.2s ease;
    }

    .blog-img:hover {
        transform: scale(1.05);
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="../index.html">
                <span id="head-tara">Tara's </span><br>
                <span style="font-size: 0.9rem; font-weight: 400;">Dental & Aesthetic Center</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>
     <div class="collapse navbar-collapse justify-content-end" id="ftco-nav">
          <ul class="navbar-nav">
            <li class="nav-item active"><a href="../index.html" class="nav-link">Home</a></li>
            <li class="nav-item active dropdown">
              <a href="../about.html" class="nav-link" onclick="window.location.href='../about.html';">
                About us
                <span class="dropdown-toggle" id="aboutDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
              </a>
              <div class="dropdown-menu" aria-labelledby="aboutDropdown">
                <a class="dropdown-item" href="../gallery.html">Our Gallery</a>
              </div>
            </li>
            <li class="nav-item"><a href="../services.html" class="nav-link">Services</a></li>
            <li class="nav-item"><a href="../doctors.html" class="nav-link">Our Team</a></li>
            <li class="nav-item"><a href="../blog/index.php" class="nav-link">Blog</a></li>
            <li class="nav-item"><a href="../contact.html" class="nav-link">Contact</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- END nav -->

    <!-- Blog Header -->
    <section class="home-slider owl-carousel">
        <div class="slider-item bread-item" style="background-image: url('../images/landing\ page\ 2.webp');" data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container" data-scrollax-parent="true">
                <div class="row slider-text align-items-end">
                    <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                        <p class="breadcrumbs" data-scrollax=" properties: { translateY: '70%', opacity: 1.6}">
                            <span class="mr-2"><a href="../index.html">Home</a></span>
                            <span class="mr-2"><a href="index.php">Blog</a></span>
                            <span><?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></span>
                        </p>
                        <h1 class="mb-3" data-scrollax=" properties: { translateY: '70%', opacity: .9}">
                            <?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Blog Content -->
    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 ftco-animate">
                    <div class="post-content">
                        <div class="meta-info-container">
                            <!-- Author and Date -->
                            <div class="meta-primary">
                                <div class="meta-item">
                                    <span class="icon-user"></span>
                                    <?php echo htmlspecialchars($post['author']); ?>
                                </div>
                                <div class="meta-separator">•</div>
                                <div class="meta-item">
                                    <span class="icon-calendar"></span>
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </div>
                                
                                <?php if ($post['categories']): ?>
                                    <div class="meta-separator">•</div>
                                    <div class="meta-item">
                                        <span class="icon-folder"></span>
                                        <?php foreach (explode(',', $post['categories']) as $category): ?>
                                            <a href="index.php?category=<?php echo urlencode($category); ?>" class="category-link">
                                                <?php echo htmlspecialchars($category); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Share Buttons -->
                            <div class="meta-share">
                                <span class="share-label">Share this post:</span>
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-facebook"></span>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-twitter"></span>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-linkedin"></span>
                                    </a>
                                    <a href="https://wa.me/?text=<?php echo urlencode(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-whatsapp"></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="post-body">
                            <?php echo $post['content']; ?>
                        </div>

                        <!-- Bottom Share Section -->
                        <div class="share-post-bottom">
                            <h5>Share this post:</h5>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-facebook"></span>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-twitter"></span>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-linkedin"></span>
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-whatsapp"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-semibold text-green-800 mb-4">Categories</h3>
                        <div class="space-y-3">
                            <?php foreach ($categories as $category): ?>
                                <div class="flex items-center justify-between group">
                                    <a href="index.php?category=<?php echo $category['id']; ?>" 
                                       class="text-gray-700 hover:text-green-700 flex-grow flex justify-between items-center">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <span class="text-gray-500 ml-2">(<?php echo $category['post_count']; ?>)</span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Recent Blog Posts -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-green-800 mb-4">Recent Blog</h3>
                        <div class="space-y-4">
                            <?php foreach ($recent_posts as $recent_post): ?>
                                <div class="border-b last:border-b-0 pb-4 last:pb-0">
                                    <a href="post.php?id=<?php echo htmlspecialchars($recent_post['id']); ?>" 
                                       class="text-gray-700 hover:text-green-700 font-medium block mb-2">
                                        <?php echo htmlspecialchars(html_entity_decode($recent_post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($recent_post['created_at'])); ?> • 
                                        <?php echo htmlspecialchars($recent_post['author']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen">
        <svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/>
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/>
        </svg>
    </div>

    <!-- Scripts -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.easing.1.3.js"></script>
    <script src="../js/jquery.waypoints.min.js"></script>
    <script src="../js/jquery.stellar.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../js/jquery.animateNumber.min.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/jquery.timepicker.min.js"></script>
    <script src="../js/scrollax.min.js"></script>
    <script src="../js/main.js"></script>

</body>
</html>