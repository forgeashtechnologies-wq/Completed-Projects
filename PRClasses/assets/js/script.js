/**
 * PR Classes - Main JavaScript File
 * 
 * This file contains all the common JavaScript functionality used across the website.
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Navbar scroll behavior
    var header = document.querySelector('.header');
    var headerHeight = header ? header.offsetHeight : 0;
    
    // Add padding to body to account for fixed header
    if (headerHeight > 0) {
        document.body.style.paddingTop = headerHeight + 'px';
    }
    
    // Change navbar background on scroll
    window.addEventListener('scroll', function() {
        if (header) {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });
    
    // Handle mobile navigation
    var navbarToggler = document.querySelector('.navbar-toggler');
    var navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target) && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
            }
        });
        
        // Close mobile menu when clicking on a link
        var navLinks = navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    navbarCollapse.classList.remove('show');
                }
            });
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]:not([data-bs-toggle])').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                
                // Calculate position accounting for header height
                var headerOffset = header ? header.offsetHeight : 0;
                var elementPosition = target.getBoundingClientRect().top;
                var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Smooth scrolling for anchor links with smooth-scroll class
    const smoothScrollLinks = document.querySelectorAll('a.smooth-scroll');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Handle active state in navigation based on scroll position
    function setActiveNavLink() {
        var scrollPosition = window.scrollY;
        
        // Get all sections that have an ID
        document.querySelectorAll('section[id]').forEach(section => {
            var sectionTop = section.offsetTop - headerHeight - 100; // Offset for better UX
            var sectionHeight = section.offsetHeight;
            var sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                // Remove active class from all nav links
                document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Add active class to corresponding nav link
                var correspondingNavLink = document.querySelector(`.navbar-nav .nav-link[href="#${sectionId}"]`);
                if (correspondingNavLink) {
                    correspondingNavLink.classList.add('active');
                }
            }
        });
    }
    
    // Call the function on scroll
    window.addEventListener('scroll', setActiveNavLink);
    
    // Call it also on page load
    setActiveNavLink();
    
    // Handle form submissions with validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Don't validate if the form has a skip-validation class
            if (form.classList.contains('skip-validation')) {
                return;
            }
            
            // If the form has required fields, check validation
            if (form.querySelector('[required]')) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Add was-validated class to show validation feedback
                    form.classList.add('was-validated');
                    
                    // Scroll to the first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            }
        });
    });
    
    // Initialize lightbox if present
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'fadeDuration': 200,
            'imageFadeDuration': 300,
            'positionFromTop': 100
        });
    }
    
    // WhatsApp integration
    const whatsappButtons = document.querySelectorAll('[data-whatsapp-message]');
    const whatsappNumber = '919042796696'; // Default WhatsApp number
    
    whatsappButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get custom number if specified
            const number = button.getAttribute('data-whatsapp-number') || whatsappNumber;
            
            // Get message
            const message = button.getAttribute('data-whatsapp-message') || '';
            
            // Open WhatsApp with number and message
            window.open(`https://wa.me/${number}?text=${encodeURIComponent(message)}`, '_blank');
        });
    });
    
    // Counter animation for statistics
    const counters = document.querySelectorAll('.counter');
    
    if (counters.length > 0) {
        const isElementInViewport = function(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        };
        
        const animateCounter = function() {
            counters.forEach(counter => {
                if (isElementInViewport(counter) && !counter.classList.contains('animated')) {
                    counter.classList.add('animated');
                    
                    const target = parseInt(counter.getAttribute('data-count'), 10);
                    const duration = 2000; // Animation duration in milliseconds
                    const step = Math.ceil(target / (duration / 16)); // 60fps approx.
                    
                    let current = 0;
                    const timer = setInterval(function() {
                        current += step;
                        
                        if (current >= target) {
                            counter.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            counter.textContent = current.toLocaleString();
                        }
                    }, 16);
                }
            });
        };
        
        // Run animation when scrolling
        window.addEventListener('scroll', animateCounter);
        
        // Also run on page load
        animateCounter();
    }
    
    // Handle back to top button
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Show countdown timer for discount offers
    const countdownTimer = document.querySelector('.countdown-timer');
    if (countdownTimer) {
        // Set the date we're counting down to (3 days from now)
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 3);
        
        // Update the count down every 1 second
        const x = setInterval(function() {
            // Get today's date and time
            const now = new Date().getTime();
            
            // Find the distance between now and the count down date
            const distance = countDownDate - now;
            
            // Time calculations for days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the result
            countdownTimer.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            
            // If the count down is finished, display expired message
            if (distance < 0) {
                clearInterval(x);
                countdownTimer.innerHTML = "EXPIRED";
            }
        }, 1000);
    }
    
    // Handle tabs with URL params
    function handleUrlTabs() {
        // Get tab parameter from URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        const sectionParam = urlParams.get('section');
        
        // Activate tab based on URL parameter if it exists
        if (tabParam) {
            const tabEl = document.querySelector(`.nav-link[data-bs-target="#${tabParam}"], .nav-link[href="#${tabParam}"]`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
        
        // Activate section tab if parameter exists
        if (sectionParam) {
            const sectionEl = document.querySelector(`.nav-link[data-section="${sectionParam}"], .nav-link[href="#${sectionParam}"]`);
            if (sectionEl) {
                const tab = new bootstrap.Tab(sectionEl);
                tab.show();
            }
        }
    }
    
    // Call the function on page load
    handleUrlTabs();
    
    // Update URL when tabs change
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"], [data-bs-toggle="pill"]');
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
            const targetSection = e.target.getAttribute('data-section');
            
            // Create URL with new parameters
            const url = new URL(window.location);
            
            if (targetId && targetId.startsWith('#')) {
                url.searchParams.set('tab', targetId.substring(1));
            }
            
            if (targetSection) {
                url.searchParams.set('section', targetSection);
            }
            
            // Update URL without refreshing the page
            window.history.replaceState({}, '', url);
        });
    });
    
    // Course sharing functionality
    setupShareButtons();
    
    // Setup enrollment button
    setupEnrollmentButtons();
});

function setupShareButtons() {
    const shareContainers = document.querySelectorAll('.share-container');
    
    shareContainers.forEach(container => {
        const courseTitle = container.dataset.courseTitle;
        const courseUrl = window.location.href;
        
        // Setup WhatsApp sharing
        const whatsappBtn = container.querySelector('.share-whatsapp');
        if (whatsappBtn) {
            const whatsappText = `Check out this course at PR Classes: ${courseTitle} - ${courseUrl}`;
            whatsappBtn.href = `https://wa.me/?text=${encodeURIComponent(whatsappText)}`;
        }
        
        // Setup Facebook sharing
        const facebookBtn = container.querySelector('.share-facebook');
        if (facebookBtn) {
            facebookBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(courseUrl)}`;
        }
        
        // Setup LinkedIn sharing
        const linkedinBtn = container.querySelector('.share-linkedin');
        if (linkedinBtn) {
            linkedinBtn.href = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(courseUrl)}`;
        }
        
        // Setup copy link functionality
        const copyLinkBtn = container.querySelector('.share-link');
        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', function(e) {
                e.preventDefault();
                copyToClipboard(courseUrl);
                alert('Link copied to clipboard!');
            });
        }
    });
}

function setupEnrollmentButtons() {
    const enrollButtons = document.querySelectorAll('.enroll-button');
    
    enrollButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            window.location.href = 'https://forms.gle/8EokfgsFmqqFGqJ49';
        });
    });
}

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}