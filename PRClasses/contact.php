<?php
// Set page title
$page_title = "Contact Us";
$page_specific_css = "contact";

// Define the document root path for absolute includes
$root_path = dirname(__FILE__);

include $root_path . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with us for any queries or assistance</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <!-- Get In Touch Section -->
        <div class="section-block mb-5">
            <h2 class="section-heading">Get In Touch</h2>
            
            <!-- Phone Number -->
            <div class="contact-item">
                <div class="icon-circle">
                    <i class="fas fa-phone-alt text-primary"></i>
                </div>
                <div class="contact-info">
                    <h3 class="info-title">Phone Number</h3>
                    <p class="info-text"><a href="tel:+919042796696" class="text-decoration-none text-muted">+91 9042796696</a></p>
                </div>
            </div>
            
            <!-- Email Address -->
            <div class="contact-item">
                <div class="icon-circle">
                    <i class="fas fa-envelope text-primary"></i>
                </div>
                <div class="contact-info">
                    <h3 class="info-title">Email Address</h3>
                    <p class="info-text"><a href="mailto:prclasses.in@gmail.com" class="text-decoration-none text-muted">prclasses.in@gmail.com</a></p>
                </div>
            </div>
            
            <!-- Working Hours -->
            <div class="contact-item">
                <div class="icon-circle">
                    <i class="fas fa-clock text-primary"></i>
                </div>
                <div class="contact-info">
                    <h3 class="info-title">Working Hours</h3>
                    <p class="info-text"><a href="#" class="text-decoration-none text-muted">Monday - Saturday: 9:00 AM - 8:00 PM</a></p>
                </div>
            </div>
        </div>
        
        <!-- Connect with Us Section -->
        <div class="section-block">
            <h2 class="section-heading">Join our Community</h2>
            
            <p class="section-desc">Follow us on social media for updates, events, and educational content. Join our growing community!</p>
            
            <!-- Social Media Links -->
            <div class="social-links">
                <!--  <a href="https://www.facebook.com/prclasses.in" target="_blank" class="social-link-pill">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </a>
                <a href="https://www.instagram.com/prclasses.in" target="_blank" class="social-link-pill">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </a>
                <a href="https://www.youtube.com/@prclasses" target="_blank" class="social-link-pill">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                </a>-->
                <a href="https://whatsapp.com/channel/0029Vb9M53IIHphIBISs291w" target="_blank" class="social-link-pill">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="https://t.me/prclasses" target="_blank" class="social-link-pill">
                    <i class="fab fa-telegram"></i>
                    <span>Telegram</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Page JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                event.preventDefault();
                
                // Show success message for contact form
                if (form.id === 'contact-form') {
                    const formResponse = document.getElementById('form-response');
                    formResponse.classList.remove('d-none');
                    formResponse.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> Your message has been sent successfully! We will get back to you soon.</div>';
                    form.reset();
                    
                    // Scroll to response
                    formResponse.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Show success message for callback form
                if (form.id === 'callback-form') {
                    // Create temporary alert
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success mt-3';
                    alert.innerHTML = '<i class="fas fa-check-circle me-2"></i> Your callback request has been received. We\'ll call you at your preferred time.';
                    form.appendChild(alert);
                    form.reset();
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                }
            }
            
            form.classList.add('was-validated');
        }, false);
    });
});
</script>

<!-- Contact Page Specific CSS -->
<style>
/* Main section styles */
.section-heading {
    position: relative;
    font-weight: 600;
    color: #333;
    font-size: 2.25rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #5f6caf;
    display: inline-block;
}

.section-desc {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 600px;
}

.section-block {
    margin-bottom: 4rem;
}

/* Contact item styles */
.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}

.icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.5rem;
    border: 1px solid #eee;
    flex-shrink: 0;
}

.icon-circle i {
    font-size: 1.5rem;
    color: #5f6caf;
}

.info-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #333;
}

.info-text {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 0;
    word-break: break-word;
}

.info-text a {
    color: #666;
    transition: color 0.3s;
}

.info-text a:hover {
    color: #5f6caf;
    text-decoration: none;
}

/* Social link styles */
.social-links {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-link-pill {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    color: #555;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    border: 1px solid #eee;
    font-weight: 500;
    min-width: 160px;
}

.social-link-pill i {
    font-size: 1.25rem;
    margin-right: 0.75rem;
}

.social-link-pill:hover {
    background-color: #5f6caf;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(95, 108, 175, 0.2);
    text-decoration: none;
    border-color: #5f6caf;
}

/* Responsive styles */
@media (max-width: 768px) {
    .contact-item {
        flex-direction: column;
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .icon-circle {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .social-link-pill {
        width: calc(50% - 1rem);
        justify-content: center;
        min-width: auto;
        padding: 0.75rem 1rem;
    }
    
    .section-heading {
        text-align: center;
        display: block;
        font-size: 1.75rem;
    }
    
    .section-desc {
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        font-size: 1rem;
    }
    
    .info-title {
        font-size: 1.1rem;
    }
    
    .info-text {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .social-link-pill {
        width: 100%;
        justify-content: flex-start;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
    }
    
    .icon-circle i {
        font-size: 1.25rem;
    }
    
    .section-heading {
        font-size: 1.5rem;
    }
    
    .section-block {
        margin-bottom: 2.5rem;
    }
}
</style>

<link rel="stylesheet" href="/assets/css/hero.css">

<?php include $root_path . '/includes/footer.php'; ?>