/**
 * PR Classes - Header & Footer JavaScript
 * Handles all functionality related to header and footer components
 */

document.addEventListener('DOMContentLoaded', function() {
    // Header scroll behavior
    handleHeaderScroll();
    
    // Mobile menu improvements
    enhanceMobileMenu();
    
    // Dropdown menu accessibility
    improveDropdownAccessibility();
    
    // Footer callback form
    setupCallbackForm();
    
    // Back to top button
    setupBackToTop();
    
    // Smooth scrolling for anchor links
    enableSmoothScrolling();
});

/**
 * Handle header appearance changes on scroll
 */
function handleHeaderScroll() {
    const header = document.querySelector('.header');
    if (!header) return;
    
    // Check initial scroll position
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    }
    
    // Add scroll event listener
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

/**
 * Enhance mobile menu behavior
 */
function enhanceMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (!navbarToggler || !navbarCollapse) return;
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInside = navbarToggler.contains(event.target) || navbarCollapse.contains(event.target);
        
        if (!isClickInside && navbarCollapse.classList.contains('show')) {
            // Use Bootstrap's collapse API to hide the menu
            const bsCollapse = new bootstrap.Collapse(navbarCollapse);
            bsCollapse.hide();
        }
    });
    
    // Make dropdown toggles work on first click for mobile
    const dropdownToggles = document.querySelectorAll('.navbar-nav .dropdown-toggle');
    
    if (window.innerWidth < 992) {
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                // Don't follow the href
                e.preventDefault();
                
                // Get the next sibling which should be the dropdown menu
                const dropdownMenu = this.nextElementSibling;
                
                // Toggle the show class
                if (dropdownMenu) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        });
    }
}

/**
 * Improve dropdown accessibility
 */
function improveDropdownAccessibility() {
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    
    dropdownItems.forEach(item => {
        // Add keyboard focus styles
        item.addEventListener('focus', function() {
            this.parentElement.classList.add('focus-within');
        });
        
        item.addEventListener('blur', function() {
            this.parentElement.classList.remove('focus-within');
        });
    });
}

/**
 * Set up the footer callback form
 */
function setupCallbackForm() {
    const callbackForm = document.getElementById('footer-callback-form');
    if (!callbackForm) return;
    
    callbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const name = this.querySelector('input[type="text"]').value;
        const phone = this.querySelector('input[type="tel"]').value;
        
        // Show success message
        showCallbackSuccess(callbackForm);
        
        // Optional: Send data to server
        // sendCallbackData(name, phone);
        
        // Reset form
        callbackForm.reset();
    });
}

/**
 * Show success message after callback form submission
 */
function showCallbackSuccess(form) {
    // Create success message element
    const successMessage = document.createElement('div');
    successMessage.className = 'alert alert-success mt-3';
    successMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i> Thank you! We will call you back shortly.';
    
    // Insert after form
    form.parentNode.insertBefore(successMessage, form.nextSibling);
    
    // Remove after 5 seconds
    setTimeout(() => {
        successMessage.remove();
    }, 5000);
}

/**
 * Set up back to top button functionality
 */
function setupBackToTop() {
    const backToTopButton = document.querySelector('.back-to-top');
    if (!backToTopButton) return;
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });
    
    // Scroll to top when clicked
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Enable smooth scrolling for anchor links
 */
function enableSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]:not([data-bs-toggle])');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Get the target element
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                // Get header height for offset
                const headerHeight = document.querySelector('.header').offsetHeight;
                
                // Calculate position
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = targetPosition - headerHeight - 20; // Extra 20px padding
                
                // Scroll smoothly
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}
