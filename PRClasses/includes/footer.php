<!-- Footer -->
<footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Logo and About Section -->
                <div class="footer-logo-section">
                    <a href="index.php" class="footer-logo">
                        <span class="logo-icon">PR</span>
                        <div class="logo-text">
                            <span class="logo-large">Classes</span>
                        </div>
                    </a>
                    <p class="footer-tagline">Empowering students through excellence in Cost and Management Accounting education since 2018. Join us on your journey to becoming a successful CMA professional.</p>
                    
                    <!-- Social Icons -->
                    <div class="social-icons">
                        <a href="#" class="social-icon" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="success-stories.php">Success Stories</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <!-- Our Courses -->
                <div class="footer-column">
                    <h3>Our Courses</h3>
                    <ul class="footer-links">
                        <li><a href="courses.php?category=CMA+Inter">CMA Inter Courses</a></li>
                        <li><a href="courses.php?category=CMA+Final">CMA Final Courses</a></li>
                        <li><a href="courses.php?type=comprehensive">Comprehensive Courses</a></li>
                        <li><a href="courses.php?type=crash">Crash Courses</a></li>
                        <li><a href="enroll.php">Enroll Now</a></li>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Chennai, India</span>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <div class="whatsapp-contact">
                                <span>+91 90427 96696</span>
                                <a href="https://wa.me/919042796696" class="whatsapp-button" aria-label="Chat on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:prclasses.in@gmail.com">prclasses.in@gmail.com</a>
                        </div>
                        
                        <!-- Get a Callback Form -->
                        <!-- 
                        <div class="footer-form-container">
                            <h4 class="footer-form-title">Get a Callback</h4>
                            <form class="footer-form" id="footer-callback-form">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                                <input type="tel" class="form-control" placeholder="Phone Number" required>
                                <button type="submit" class="btn btn-primary">Request Callback</button>
                            </form>
                        </div>
                        -->
                    </div>
                </div>
                
            </div>
            

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
                    <p>Developed by <a href="https://www.ashwinai.in" target="_blank">Ashwins Tech Solutions</a></p>
                </div>
            </div>
        </div>
        
        <!-- Back to Top Button -->
        <div class="back-to-top">
            <i class="fas fa-arrow-up"></i>
        </div>
    </footer>
    
    <!-- CSS Reference -->
    <link rel="stylesheet" href="assets/css/hero.css">
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="assets/js/script.js"></script>
    
    <!-- Footer Form Handling -->
    <script>
        // Callback Form Handling
        document.addEventListener('DOMContentLoaded', function() {
            const callbackForm = document.getElementById('footer-callback-form');
            if (callbackForm) {
                callbackForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const name = this.querySelector('input[type="text"]').value;
                    const phone = this.querySelector('input[type="tel"]').value;
                    
                    // Here you would typically send data to your server via AJAX
                    // For now, we'll just show a success message and reset the form
                    
                    // Create a success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success mt-3';
                    successMessage.textContent = 'Thank you! We will call you back shortly.';
                    
                    // Insert the message after the form
                    callbackForm.after(successMessage);
                    
                    // Reset the form
                    callbackForm.reset();
                    
                    // Remove the message after 5 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 5000);
                });
            }
        });
    </script>
    
    <?php if(isset($page_specific_js)): ?>
    <script src="assets/js/<?php echo $page_specific_js; ?>.js"></script>
    <?php endif; ?>
    
    <!-- QR Code Floater Script -->
    <script src="assets/js/qr-code-floater.js"></script>
    
    <!-- WhatsApp Button Style -->
    <style>
    /* Enhanced WhatsApp Button */
    .whatsapp-contact {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .whatsapp-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #004d00;
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .whatsapp-button:hover {
        background-color: #128C7E;
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .whatsapp-button i {
        font-size: 18px;
    }

    /* Optional: Animated pulse effect */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    .whatsapp-button.with-pulse {
        animation: pulse 2s infinite;
    }
    </style>
</body>
</html>