<?php
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
    <title><?php echo htmlspecialchars($post['title']); ?> - Tara's Dental & Aesthetic Center</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
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

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="../index.html" class="nav-link">Home</a></li>
                    <li class="nav-item dropdown">
                        <a href="../about.html" class="nav-link dropdown-toggle" id="aboutDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            About us
                        </a>
                        <div class="dropdown-menu" aria-labelledby="aboutDropdown" 
                            style="backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8); 
                                   box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border: none; border-radius: 10px; transition: all 0.3s ease;">
                            <a class="dropdown-item" href="../gallery.html">Our Gallery</a>
                            <a class="dropdown-item" href="../doctors.html">Our team</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="../services.html" class="nav-link dropdown-toggle" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Services
                        </a>
                        <div class="dropdown-menu" aria-labelledby="servicesDropdown" 
                            style="backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8); 
                                   box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border: none; border-radius: 10px; transition: all 0.3s ease;">
                            <a class="dropdown-item" href="../services.html">Dental</a>
                            <a class="dropdown-item" href="#">Aesthetic</a>
                        </div>
                    </li>
                    <li class="nav-item active"><a href="index.php" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="../contact.html" class="nav-link">Contact</a></li>
                    <li class="nav-item cta"><a href="../contact.html" class="nav-link"><span>Make an Appointment</span></a></li>
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
                            <span><?php echo htmlspecialchars($post['title']); ?></span>
                        </p>
                        <h1 class="mb-3" data-scrollax=" properties: { translateY: '70%', opacity: .9}">
                            <?php echo htmlspecialchars($post['title']); ?>
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
                    <?php if ($post['image_url']): ?>
                        <p class="post-featured-image">
                            <img src="../<?php echo htmlspecialchars($post['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                 class="img-fluid">
                        </p>
                    <?php endif; ?>

                    <div class="post-content">
                        <div class="meta-info-container">
                            <!-- Author and Date -->
                            <div class="meta-primary d-flex align-items-center">
                                <div class="meta-item">
                                    <span class="icon-user"></span>
                                    <?php echo htmlspecialchars($post['author']); ?>
                                </div>
                                <div class="meta-item">
                                    <span class="icon-calendar"></span>
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </div>
                                
                                <?php if ($post['categories']): ?>
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
                                <span class="share-label">Share:</span>
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-facebook"></span>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-twitter"></span>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       class="share-btn" target="_blank" rel="noopener">
                                        <span class="icon-linkedin"></span>
                                    </a>
                                    <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
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
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-twitter"></span>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-linkedin"></span>
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                   class="share-btn" target="_blank" rel="noopener">
                                    <span class="icon-whatsapp"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4 sidebar ftco-animate">
                    <!-- Categories -->
                    <div class="sidebar-box">
                        <h3 class="sidebar-heading">Categories</h3>
                        <ul class="categories">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="index.php?category=<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?> 
                                        <span>(<?php echo $category['post_count']; ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Blog Posts -->
                    <div class="sidebar-box ftco-animate">
                        <h3 class="sidebar-heading">Recent Posts</h3>
                        <?php foreach ($recent_posts as $recent): ?>
                            <div class="block-21 mb-4 d-flex">
                                <?php if ($recent['image_url']): ?>
                                    <a class="blog-img mr-4" 
                                       style="background-image: url('../<?php echo htmlspecialchars($recent['image_url']); ?>');">
                                    </a>
                                <?php endif; ?>
                                <div class="text">
                                    <h3 class="heading">
                                        <a href="post.php?id=<?php echo $recent['id']; ?>">
                                            <?php echo htmlspecialchars($recent['title']); ?>
                                        </a>
                                    </h3>
                                    <div class="meta">
                                        <div>
                                            <a href="#">
                                                <span class="icon-calendar"></span> 
                                                <?php echo date('M d, Y', strtotime($recent['created_at'])); ?>
                                            </a>
                                        </div>
                                        <div>
                                            <a href="#">
                                                <span class="icon-person"></span> 
                                                <?php echo htmlspecialchars($recent['author']); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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

    <style>
    
    /* Single Post Styles */
    
    .post-featured-image {
        margin-bottom: 2em;
        border-radius: 4px;
        overflow: hidden;
    }
    .post-featured-image img {
        width: 100%;
        height: auto;
    }
    .meta-info {
        font-size: 0.95em;
        color: #666;
        line-height: 1;
    }
    .meta-info > div {
        display: flex;
        align-items: center;
    }
    .meta-info .icon-user,
    .meta-info .icon-calendar,
    .meta-info .icon-folder {
        font-size: 1.1em;
        color: #4e9525;
    }
    .category-link {
        color: #666;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .category-link:hover {
        color: #4e9525;
    }
    .post-body {
        font-size: 1.1em;
        line-height: 1.8;
        color: #333;
    }
    .post-body p {
        margin-bottom: 1.5em;
    }
    .post-body h2, .post-body h3 {
        margin: 1.5em 0 1em;
        color: #333;
    }

.social-share {
    margin-top: 2rem;
}

.social-share h3 {
    margin-bottom: 1rem;
}

.social-share .ftco-social {
    display: flex;
    gap: 1rem;
    padding: 0;
    margin: 0;
    list-style: none;
}

.social-share .ftco-social li {
    margin: 0;
    padding: 0;
}

.social-share .social-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 50px;
    border-radius: 50%;
    background: #f5f5f5;
    color: #000;
    transition: all 0.3s ease;
}

.social-share .social-button:hover {
    background: #32c5d2;
    color: #fff;
}

.post-meta {
    margin: 1rem 0;
    gap: 2rem;
    align-items: center;
    flex-wrap: wrap;
}

.author-meta, .date-meta, .social-meta, .category-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.post-social {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.75rem;
}

.social-meta-button {
    color: #666;
    transition: color 0.3s ease;
    font-size: 1.1em;
    line-height: 1;
    display: flex;
    align-items: center;
}

.social-meta-button:hover {
    color: #4e9525;
    text-decoration: none;
}

.meta-text {
    color: #666;
}


@media (max-width: 768px) {
    .meta-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .meta-info > div {
        margin-bottom: 10px;
    }
    
    .share-post-top {
        margin-top: 5px;
    }
}
    
    /* Post Content Layout */
.post-content {
    margin-bottom: 3rem;
}

/* Meta Information */
.meta-info-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.meta-primary {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.9em;
}

.meta-item span[class^="icon-"] {
    color: #4e9525;
    font-size: 1.1em;
}

/* Category Links */
.category-link {
    color: #666;
    transition: color 0.3s ease;
    text-decoration: none;
}

.category-link:hover {
    color: #4e9525;
}

/* Share Section */
.meta-share {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.share-label {
    font-size: 0.9em;
    color: #666;
}

.share-buttons {
    display: flex;
    gap: 0.75rem;
}

.share-btn {
    color: #666;
    font-size: 1.1em;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.share-btn:hover {
    color: #4e9525;
    text-decoration: none;
}

/* Post Content */
.post-body {
    font-size: 1.1em;
    line-height: 1.8;
    color: #333;
}

.post-body p {
    margin-bottom: 1.5em;
}

.post-body h2,
.post-body h3 {
    color: #333;
    margin: 1.5em 0 1em;
}

/* Featured Image */
.post-featured-image {
    margin-bottom: 2rem;
    border-radius: 8px;
    overflow: hidden;
}

.post-featured-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

/* Bottom Share Section */
.share-post-bottom {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.share-post-bottom h5 {
    color: #666;
    margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .meta-info-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .meta-primary {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .meta-share {
        width: 100%;
        margin-top: 1rem;
        justify-content: flex-start;
    }
}

    /* Sidebar Styles */
    
    .sidebar-box {
        margin-bottom: 40px;
        padding: 25px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .sidebar-heading {
        font-size: 20px;
        margin-bottom: 20px;
        color: #333;
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .meta-info {
            flex-direction: column;
        }
        .meta-info > div {
            margin-bottom: 10px;
        }
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
</body>
</html>