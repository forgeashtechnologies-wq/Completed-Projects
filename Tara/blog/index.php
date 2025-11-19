<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start with PHP code execution, no output
$pageTitle = "Blog - Tara's Dental & Aesthetic Center";
$error_message = null;

// Include configuration after any output buffering
require_once('../admin/config.php');

try {
    // Get database connection
    $db = getDBConnection();

    // Pagination setup
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 6;
    $offset = ($page - 1) * $per_page;
    $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blog - Tara's Dental & Aesthetic Center</title>
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
    <link rel="shortcut icon" href="../images/tara's dental logo.png" type="image/x-icon">
    <style>
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

        /* Blog Card Styles */
        .blog-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }

        .blog-content {
            padding: 25px;
        }

        .blog-content h2 {
            font-size: 1.4em;
            margin: 15px 0;
            color: #333;
            font-weight: 600;
        }

        .blog-content h2 a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .blog-content h2 a:hover {
            color: #4e9525;
        }

        .blog-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Sidebar Styles */
        .sidebar-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }

        .sidebar-box h3 {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }

        .sidebar-box h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: #4e9525;
        }

        .categories {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .categories li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .categories li:last-child {
            border-bottom: none;
        }

        .categories a {
            color: #666;
            font-size: 0.95em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none;
            padding: 2px 0;
        }

        .categories a:hover {
            color: #4e9525;
            text-decoration: none;
            padding-left: 5px;
        }

        .categories .post-count {
            color: #999;
            font-size: 0.9em;
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" 
                    aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="../index.html" class="nav-link">Home</a></li>
                    <li class="nav-item dropdown">
                        <a href="../about.html" class="nav-link" onclick="window.location.href='../about.html';">
                            About us
                            <span class="dropdown-toggle" id="aboutDropdown" role="button" data-toggle="dropdown" 
                                  aria-haspopup="true" aria-expanded="false"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <a class="dropdown-item" href="../gallery.html">Our Gallery</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="../services.html" class="nav-link">Services</a></li>
                    <li class="nav-item"><a href="../doctors.html" class="nav-link">Our Team</a></li>
                    <li class="nav-item active"><a href="index.php" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="../contact.html" class="nav-link">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <!-- Hero Section -->
    <section class="home-slider owl-carousel">
        <div class="slider-item bread-item" style="background-image: url('../images/landing page 2.webp');" 
             data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container" data-scrollax-parent="true">
                <div class="row slider-text align-items-end">
                    <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                        <p class="breadcrumbs" data-scrollax=" properties: { translateY: '70%', opacity: 1.6}">
                            <span class="mr-2"><a href="../index.html">Home</a></span>
                            <span>Blog</span>
                        </p>
                        <h1 class="mb-3" data-scrollax=" properties: { translateY: '70%', opacity: .9}">Our Blog</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Content -->
    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="blog-grid">
                        <?php
                            // Database queries are now at the top of the file

                            // Query to get posts with categories
                            if ($category_id) {
                                $query = "SELECT DISTINCT p.*, c.name as category_name, c.id as category_id
                                          FROM blog_posts p 
                                          LEFT JOIN post_categories pc ON p.id = pc.post_id 
                                          LEFT JOIN blog_categories c ON pc.category_id = c.id 
                                          WHERE pc.category_id = ?
                                          ORDER BY p.created_at DESC 
                                          LIMIT ? OFFSET ?";
                                $stmt = $db->prepare($query);
                                $stmt->execute([$category_id, $per_page, $offset]);
                            } else {
                                $query = "SELECT DISTINCT p.*, c.name as category_name, c.id as category_id
                                          FROM blog_posts p 
                                          LEFT JOIN post_categories pc ON p.id = pc.post_id 
                                          LEFT JOIN blog_categories c ON pc.category_id = c.id 
                                          ORDER BY p.created_at DESC 
                                          LIMIT ? OFFSET ?";
                                $stmt = $db->prepare($query);
                                $stmt->execute([$per_page, $offset]);
                            }
                            
                            // Fetch and organize posts with categories
                            $posts = [];
                            $temp_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($temp_posts as $row) {
                                $post_id = $row['id'];
                                if (!isset($posts[$post_id])) {
                                    $posts[$post_id] = [
                                        'id' => $row['id'],
                                        'title' => $row['title'],
                                        'content' => $row['content'],
                                        'author' => $row['author'],
                                        'created_at' => $row['created_at'],
                                        'image_url' => $row['image_url'],
                                        'categories' => []
                                    ];
                                }
                                if ($row['category_name']) {
                                    $posts[$post_id]['categories'][] = [
                                        'id' => $row['category_id'],
                                        'name' => $row['category_name']
                                    ];
                                }
                            }
                            $posts = array_values($posts);

                            // Get total posts for pagination
                            if ($category_id) {
                                $count_stmt = $db->prepare("SELECT COUNT(DISTINCT p.id) 
                                                           FROM blog_posts p 
                                                           JOIN post_categories pc ON p.id = pc.post_id 
                                                           WHERE pc.category_id = ?");
                                $count_stmt->execute([$category_id]);
                            } else {
                                $count_stmt = $db->query("SELECT COUNT(*) FROM blog_posts");
                            }
                            $total_posts = $count_stmt->fetchColumn();
                            $total_pages = ceil($total_posts / $per_page);

                            // Get categories with post count
                            $categories_query = "SELECT c.*, COUNT(DISTINCT pc.post_id) as post_count 
                                                FROM blog_categories c 
                                                LEFT JOIN post_categories pc ON c.id = pc.category_id 
                                                GROUP BY c.id 
                                                ORDER BY c.name";
                            $categories = $db->query($categories_query)->fetchAll(PDO::FETCH_ASSOC);

                        } catch (Exception $e) {
                            error_log("Database error: " . $e->getMessage());
                            $error_message = "An error occurred while fetching the data.";
                            $posts = [];
                            $categories = [];
                            $total_pages = 0;
                        }
                        ?>
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <div class="blog-card">
                                    <div class="blog-content">
                                        <?php if (!empty($post['categories'])): ?>
                                            <?php foreach ($post['categories'] as $index => $category): ?>
                                                <a href="?category=<?php echo $category['id']; ?>" class="category-tag">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <h2><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></a></h2>
                                        <div class="blog-meta">
                                            <span><i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                        </div>
                                        <p><?php echo substr(strip_tags($post['content']), 0, 150) . '...'; ?></p>
                                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
                                        <div class="share-buttons">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" 
                                               class="share-btn" target="_blank" rel="noopener">
                                                <span class="icon-facebook"></span>
                                            </a>
                                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&text=<?php echo urlencode(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>" 
                                               class="share-btn" target="_blank" rel="noopener">
                                                <span class="icon-twitter"></span>
                                            </a>
                                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" 
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-gray-600">
                                <p>No posts found.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="row mt-5">
                            <div class="col text-center">
                                <div class="block-27">
                                    <ul>
                                        <?php if ($page > 1): ?>
                                            <li><a href="?page=<?php echo ($page-1); ?><?php echo isset($_GET['category']) ? '&category='.$_GET['category'] : ''; ?>">&lt;</a></li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li <?php echo ($i == $page) ? 'class="active"' : ''; ?>>
                                                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category='.$_GET['category'] : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li><a href="?page=<?php echo ($page+1); ?><?php echo isset($_GET['category']) ? '&category='.$_GET['category'] : ''; ?>">&gt;</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4 sidebar">
                    <div class="sidebar-box">
                        <h3>Categories</h3>
                        <ul class="categories">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="?category=<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <?php if ($category['post_count'] > 0): ?>
                                            <span class="post-count">(<?php echo $category['post_count']; ?>)</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#F96D00" /></svg></div>

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
    <script src="../js/google-map.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>