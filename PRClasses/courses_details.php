<?php
// Set page specific variables
$page_title = "Course Details";

// Define root path for absolute includes
$root_path = dirname(__FILE__);
include $root_path . '/includes/header.php';
?>

<section class="page-header bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 fw-bold mb-0">Courses</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white opacity-75">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Course Details</li>
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

<!-- Announcement Bar for Special Offers -->
<div class="announcement-bar bg-warning text-dark py-2">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <p class="mb-0 fw-bold"><i class="fas fa-tag me-1"></i> Flat 50% Discount on all courses - Limited time offer! Hurry Limited Seats Only!!!</p>
        </div>
    </div>
</div>

<section class="courses-section py-5">
    <div class="container">
        <!-- Simple Category Tabs -->
        <div class="category-tabs mb-4">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link active" href="#cma-inter" data-bs-toggle="tab">CMA Inter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cma-final" data-bs-toggle="tab">CMA Final</a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content">
            <!-- CMA Inter Courses -->
            <div class="tab-pane fade show active" id="cma-inter">
                <h2 class="mb-4">CMA Inter Courses</h2>
                
                <!-- Course 1: Crash Course -->
                <div class="course-card mb-5 shadow-sm">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h4 mb-0">Online Live CRASH Course for CMA Inter Paper 8: Cost Accounting</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <ul class="list-unstyled course-meta">
                                        <li class="mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i> <strong>Begins:</strong> May 2025 for June exam</li>
                                        <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> <strong>Timing:</strong> 6.00 to 8.00 AM</li>
                                        <li class="mb-2"><i class="fas fa-calendar-day text-primary me-2"></i> <strong>Days:</strong> Saturday and Sunday</li>
                                    </ul>
                                    
                                    <div class="price-section my-4">
                                        <h4>
                                            <span class="text-decoration-line-through text-muted me-2">₹6000/-</span>
                                            <span class="text-success fw-bold">₹3000/- Only</span>
                                            <span class="badge bg-danger ms-2">50% Discount</span>
                                        </h4>
                                    </div>
                                    
                                    <div class="alert alert-warning d-inline-block">
                                        <i class="fas fa-exclamation-circle me-2"></i> Hurry Limited Seats Only!!!
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="course-benefits bg-light p-3 rounded">
                                        <h5 class="mb-3">Course Benefits:</h5>
                                        <ul class="course-benefits-list">
                                            <li>10 classes covering full concepts and solving important problems for all chapters</li>
                                            <li>Recorded video access taken during this crash course will be provided for one year with UNLIMITED views</li>
                                            <li>Complete pdf questions and solved problem notes taken during COMPREHENSIVE class will be provided</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex mt-3">
                            <a href="https://forms.gle/8EokfgsFmqqFGqJ49" class="btn btn-primary me-2">
    <i class="fab fa-google me-1"></i> Join Now
</a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-share-alt me-1"></i> Share to your friends
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="shareDropdown1">
                                        <li><a class="dropdown-item" href="https://wa.me/?text=Check%20out%20this%20CMA%20Inter%20Crash%20Course%20at%20PR%20Classes%20-%20https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp</a></li>
                                        <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-facebook text-primary me-2"></i> Facebook</a></li>
                                        <li><a class="dropdown-item" href="mailto:?subject=CMA%20Inter%20Course%20at%20PR%20Classes&body=Check%20out%20this%20course:%20https://prclasses.in/courses_details.php" target="_blank"><i class="fas fa-envelope text-dark me-2"></i> Email</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course 2: Comprehensive Course -->
                <div class="course-card mb-5 shadow-sm">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h4 mb-0">Online Live COMPREHENSIVE Course for CMA Inter Paper 8: Cost Accounting</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <ul class="list-unstyled course-meta">
                                        <li class="mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i> <strong>Begins:</strong> July 2025 for Dec exam</li>
                                        <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> <strong>Timing:</strong> 6.00 to 8.00 AM</li>
                                        <li class="mb-2"><i class="fas fa-calendar-day text-primary me-2"></i> <strong>Days:</strong> Wednesday and Saturday</li>
                                    </ul>
                                    
                                    <div class="price-section my-4">
                                        <h4>
                                            <span class="text-decoration-line-through text-muted me-2">₹4000/-</span>
                                            <span class="text-success fw-bold">₹2000/- Only</span>
                                            <span class="badge bg-danger ms-2">50% Discount</span>
                                        </h4>
                                    </div>
                                    
                                    <div class="alert alert-warning d-inline-block">
                                        <i class="fas fa-exclamation-circle me-2"></i> Hurry Limited Seats Only!!!
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="course-benefits bg-light p-3 rounded">
                                        <h5 class="mb-3">Course Benefits:</h5>
                                        <ul class="course-benefits-list">
                                            <li>30 classes covering full concepts and solving majority problems for all chapters</li>
                                            <li>Recorded video access taken during this comprehensive course will be provided for one year with UNLIMITED views</li>
                                            <li>Pdf questions and solved problem notes taken during this comprehensive course will be shared</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex mt-3">
                            <a href="https://forms.gle/8EokfgsFmqqFGqJ49" class="btn btn-primary me-2">
                  <i class="fab fa-google me-1"></i> Join Now
</a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-share-alt me-1"></i> Share to your friends
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="shareDropdown2">
                                        <li><a class="dropdown-item" href="https://wa.me/?text=Check%20out%20this%20CMA%20Inter%20Comprehensive%20Course%20at%20PR%20Classes%20-%20https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp</a></li>
                                        <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-facebook text-primary me-2"></i> Facebook</a></li>
                                        <li><a class="dropdown-item" href="mailto:?subject=CMA%20Inter%20Course%20at%20PR%20Classes&body=Check%20out%20this%20course:%20https://prclasses.in/courses_details.php" target="_blank"><i class="fas fa-envelope text-dark me-2"></i> Email</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CMA Final Courses -->
            <div class="tab-pane fade" id="cma-final">
                <h2 class="mb-4">CMA Final Courses</h2>
                
                <!-- Course 3: CMA Final Comprehensive -->
                <div class="course-card mb-5 shadow-sm">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h4 mb-0">Online Live COMPREHENSIVE Course for CMA Final Paper 14: Strategic Financial Management</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <ul class="list-unstyled course-meta">
                                        <li class="mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i> <strong>Begins:</strong> July 2025 for Dec exam</li>
                                        <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> <strong>Timing:</strong> 6.00 to 8.00 AM</li>
                                        <li class="mb-2"><i class="fas fa-calendar-day text-primary me-2"></i> <strong>Days:</strong> Friday and Sunday</li>
                                    </ul>
                                    
                                    <div class="price-section my-4">
                                        <h4>
                                            <span class="text-decoration-line-through text-muted me-2">₹8000/-</span>
                                            <span class="text-success fw-bold">₹4000/- Only</span>
                                            <span class="badge bg-danger ms-2">50% Discount</span>
                                        </h4>
                                    </div>
                                    
                                    <div class="alert alert-warning d-inline-block">
                                        <i class="fas fa-exclamation-circle me-2"></i> Hurry Limited Seats Only!!!
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="course-benefits bg-light p-3 rounded">
                                        <h5 class="mb-3">Course Benefits:</h5>
                                        <ul class="course-benefits-list">
                                            <li>30 classes covering full concepts and solving majority problems for all chapters</li>
                                            <li>Recorded video access taken during this comprehensive course will be provided for one year with UNLIMITED views</li>
                                            <li>Pdf questions and solved problem notes taken during this comprehensive course will be shared</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex mt-3">
                            <a href="https://forms.gle/8EokfgsFmqqFGqJ49" class="btn btn-primary me-2">
    <i class="fab fa-google me-1"></i> Join Now
</a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown3" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-share-alt me-1"></i> Share to your friends
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="shareDropdown3">
                                        <li><a class="dropdown-item" href="https://wa.me/?text=Check%20out%20this%20CMA%20Final%20Comprehensive%20Course%20at%20PR%20Classes%20-%20https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp</a></li>
                                        <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=https://prclasses.in/courses_details.php" target="_blank"><i class="fab fa-facebook text-primary me-2"></i> Facebook</a></li>
                                        <li><a class="dropdown-item" href="mailto:?subject=CMA%20Final%20Course%20at%20PR%20Classes&body=Check%20out%20this%20course:%20https://prclasses.in/courses_details.php" target="_blank"><i class="fas fa-envelope text-dark me-2"></i> Email</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="cta-section py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h3 class="mb-3">Need more information about our courses?</h3>
            <p class="lead mb-4">Contact us for personalized guidance and to discuss your learning needs</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="contact.php" class="btn btn-primary">
                    <i class="fas fa-envelope me-2"></i> Contact Us
                </a>
                <a href="https://wa.me/919042796696?text=Hello,%20I%20want%20to%20know%20more%20about%20your%20courses" class="btn btn-success">
                    <i class="fab fa-whatsapp me-2"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<div class="qr-code-floater">
<img src="./images/QRcode.png" class="qr-code" alt="QR Code" />
    <button class="close-btn">&times;</button>
    <p>Scan this QR code to make a payment.</p>
</div>

<style>
    .course-benefits-list {
        padding-left: 20px;
    }
    .course-benefits-list li {
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .course-meta i {
        width: 20px;
        text-align: center;
    }
    .announcement-bar {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { background-color: #ffc107; }
        50% { background-color: #ff9800; }
        100% { background-color: #ffc107; }
    }
</style>

<!-- Include the element-styles.js file -->
<script src="assets/js/element-styles.js"></script>

<script>
async function fix() {
  await setElementStyles($0.parentElement, {
    display: 'flex',
    justifyContent: 'flex-start',
    padding: '0', // Adjust padding as needed
  });
  await setElementStyles($0, {
    textAlign: 'left', // Or remove this line entirely as flexbox will handle alignment
  });
}
fix();
</script>

<?php include $root_path . '/includes/footer.php'; ?>