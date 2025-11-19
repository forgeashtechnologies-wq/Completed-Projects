// Carousel functionality for PR Classes website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the carousel
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.carousel-item');
    const indicators = carousel.querySelectorAll('.carousel-indicators button');
    const prevBtn = carousel.querySelector('.carousel-control-prev');
    const nextBtn = carousel.querySelector('.carousel-control-next');
    
    let currentSlide = 0;
    let interval = null;
    const intervalTime = 5000; // 5 seconds between slides
    
    // Function to show a specific slide
    function showSlide(index) {
        // Remove active class from all slides and indicators
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => {
            indicator.classList.remove('active');
            indicator.setAttribute('aria-current', 'false');
        });
        
        // Add active class to current slide and indicator
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        indicators[index].setAttribute('aria-current', 'true');
        
        // Update current slide index
        currentSlide = index;
    }
    
    // Function to show next slide
    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slides.length) next = 0;
        showSlide(next);
    }
    
    // Function to show previous slide
    function prevSlide() {
        let prev = currentSlide - 1;
        if (prev < 0) prev = slides.length - 1;
        showSlide(prev);
    }
    
    // Set up event listeners for controls
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            prevSlide();
            resetInterval();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            nextSlide();
            resetInterval();
        });
    }
    
    // Set up event listeners for indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function(e) {
            e.preventDefault();
            showSlide(index);
            resetInterval();
        });
    });
    
    // Function to start automatic sliding
    function startInterval() {
        interval = setInterval(nextSlide, intervalTime);
    }
    
    // Function to reset interval
    function resetInterval() {
        clearInterval(interval);
        startInterval();
    }
    
    // Start automatic sliding
    startInterval();
    
    // Pause carousel on hover
    carousel.addEventListener('mouseenter', function() {
        clearInterval(interval);
    });
    
    // Resume carousel on mouse leave
    carousel.addEventListener('mouseleave', function() {
        startInterval();
    });
});