<?php
// Database connection and queries
require_once '../admin/config.php';

// Get database connection
$db = getDBConnection();

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

// Query to get posts
if ($category_id) {
    $query = "SELECT DISTINCT p.*, GROUP_CONCAT(c.name) as categories 
              FROM blog_posts p 
              LEFT JOIN post_categories pc ON p.id = pc.post_id 
              LEFT JOIN blog_categories c ON pc.category_id = c.id 
              WHERE EXISTS (
                  SELECT 1 FROM post_categories 
                  WHERE post_id = p.id AND category_id = ?
              )
              GROUP BY p.id 
              ORDER BY p.created_at DESC 
              LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$category_id, $per_page, $offset]);
} else {
    $query = "SELECT p.*, GROUP_CONCAT(c.name) as categories 
              FROM blog_posts p 
              LEFT JOIN post_categories pc ON p.id = pc.post_id 
              LEFT JOIN blog_categories c ON pc.category_id = c.id 
              GROUP BY p.id 
              ORDER BY p.created_at DESC 
              LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$per_page, $offset]);
}
$posts = $stmt->fetchAll();

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
$categories_query = "SELECT c.*, COUNT(pc.post_id) as post_count 
                    FROM blog_categories c 
                    LEFT JOIN post_categories pc ON c.id = pc.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name";
$categories = $db->query($categories_query)->fetchAll();
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
  </head>
  <body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
      <div class="container">
        <a class="navbar-brand" href="../index.html"><span id="head-tara">Tara's </span><br> <span style="font-size: 0.9rem; font-weight: 400;">Dental & Aesthetic Center</span></a>
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

    <!-- Hero Section -->
    <section class="home-slider owl-carousel">
      <div class="slider-item bread-item" style="background-image: url('../images/landing page 2.webp');" data-stellar-background-ratio="0.5">
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
    <section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-12 ftco-animate">
                            <div class="blog-entry">
                                <?php if ($post['image_url']): ?>
                                    <a href="post.php?id=<?php echo $post['id']; ?>" 
                                       class="block-20" 
                                       style="background-image: url('<?php echo htmlspecialchars($post['image_url']); ?>');">
                                    </a>
                                <?php endif; ?>

                                <div class="text d-flex py-4">
                                    <div class="meta mb-3">
                                        <div>
                                            <a href="#"><?php echo date('M. d, Y', strtotime($post['created_at'])); ?></a>
                                        </div>
                                        <div>
                                            <a href="#"><?php echo htmlspecialchars($post['author']); ?></a>
                                        </div>
                                        <?php if ($post['categories']): ?>
                                            <div>
                                                <span class="icon-folder"></span>
                                                <?php foreach (explode(',', $post['categories']) as $category): ?>
                                                    <a href="?category=<?php echo urlencode($category); ?>" class="category-link">
                                                        <?php echo htmlspecialchars($category); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="desc pl-sm-3 pl-md-5">
                                        <h3 class="heading">
                                            <a href="post.php?id=<?php echo $post['id']; ?>">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h3>
                                        <p><?php echo substr(strip_tags($post['content']), 0, 200) . '...'; ?></p>
                                        <p>
                                            <a href="post.php?id=<?php echo $post['id']; ?>" 
                                               class="btn btn-primary btn-outline-primary">Read more</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="row mt-5">
                        <div class="col">
                            <div class="block-27">
                                <ul>
                                    <?php if ($page > 1): ?>
                                        <li>
                                            <a href="?page=<?php echo ($page-1); ?><?php echo $category_id ? '&category='.$category_id : ''; ?>">&lt;</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li <?php echo ($i == $page) ? 'class="active"' : ''; ?>>
                                            <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category='.$category_id : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li>
                                            <a href="?page=<?php echo ($page+1); ?><?php echo $category_id ? '&category='.$category_id : ''; ?>">&gt;</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4 sidebar ftco-animate">
                <!-- Categories -->
                <div class="sidebar-box">
                    <h3>Categories</h3>
                    <ul class="categories">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="?category=<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?> 
                                    <span>(<?php echo $category['post_count']; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Recent Blog Posts -->
                <div class="sidebar-box ftco-animate">
                    <h3>Recent Blog</h3>
                    <?php 
                    $recent_posts_stmt = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3");
                    $recent_posts = $recent_posts_stmt->fetchAll();
                    foreach ($recent_posts as $recent_post): 
                    ?>
                        <div class="block-21 mb-4 d-flex">
                            <?php if ($recent_post['image_url']): ?>
                                <a class="blog-img mr-4" 
                                   style="background-image: url('<?php echo htmlspecialchars($recent_post['image_url']); ?>');"></a>
                            <?php endif; ?>
                            <div class="text">
                                <h3 class="heading">
                                    <a href="post.php?id=<?php echo $recent_post['id']; ?>">
                                        <?php echo htmlspecialchars($recent_post['title']); ?>
                                    </a>
                                </h3>
                                <div class="meta">
                                    <div>
                                        <a href="#"><span class="icon-calendar"></span> 
                                            <?php echo date('M d, Y', strtotime($recent_post['created_at'])); ?>
                                        </a>
                                    </div>
                                    <div>
                                        <a href="#"><span class="icon-person"></span> 
                                            <?php echo htmlspecialchars($recent_post['author']); ?>
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
  </body>
</html>