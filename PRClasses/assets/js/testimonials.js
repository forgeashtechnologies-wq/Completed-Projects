// Testimonial Slider Implementation

document.addEventListener('DOMContentLoaded', function() {
    // Get testimonial elements
    const testimonialsTrack = document.querySelector('.testimonials-track');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    const testimonialDots = document.querySelectorAll('.testimonial-dot');
    
    if (!testimonialsTrack || testimonialCards.length === 0) return;
    
    let currentIndex = 0;
    let startX, moveX, initialPosition, finalPosition;
    let isAnimating = false;
    
    // Set up automatic sliding
    let autoSlideInterval = setInterval(nextSlide, 5000);
    
    // Initialize testimonial width and position
    function initTestimonials() {
        const cardWidth = testimonialCards[0].offsetWidth;
        const trackWidth = cardWidth * testimonialCards.length;
        
        // Set track width
        testimonialsTrack.style.width = `${trackWidth}px`;
        
        // Position each card
        testimonialCards.forEach((card, index) => {
            card.style.left = `${index * cardWidth}px`;
        });
    }
    
    // Move to specific slide
    function goToSlide(index) {
        if (isAnimating) return;
        isAnimating = true;
        
        // Clear auto slide interval and restart it
        clearInterval(autoSlideInterval);
        
        const cardWidth = testimonialCards[0].offsetWidth;
        const newPosition = -index * cardWidth;
        
        // Update current index
        currentIndex = index;
        
        // Animate the track
        testimonialsTrack.style.transition = 'transform 0.5s ease-in-out';
        testimonialsTrack.style.transform = `translateX(${newPosition}px)`;
        
        // Update dots
        updateDots();
        
        // Reset animation flag after transition
        setTimeout(() => {
            isAnimating = false;
            autoSlideInterval = setInterval(nextSlide, 5000);
        }, 500);
    }
    
    // Go to next slide
    function nextSlide() {
        let nextIndex = currentIndex + 1;
        if (nextIndex >= testimonialCards.length) {
            nextIndex = 0;
        }
        goToSlide(nextIndex);
    }
    
    // Go to previous slide
    function prevSlide() {
        let prevIndex = currentIndex - 1;
        if (prevIndex < 0) {
            prevIndex = testimonialCards.length - 1;
        }
        goToSlide(prevIndex);
    }
    
    // Update dot indicators
    function updateDots() {
        testimonialDots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    // Add click event to dots
    testimonialDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goToSlide(index);
        });
    });
    
    // Add touch events for mobile swipe
    testimonialsTrack.addEventListener('touchstart', handleTouchStart, false);
    testimonialsTrack.addEventListener('touchmove', handleTouchMove, false);
    testimonialsTrack.addEventListener('touchend', handleTouchEnd, false);
    
    // Handle touch start
    function handleTouchStart(e) {
        startX = e.touches[0].clientX;
        initialPosition = -currentIndex * testimonialCards[0].offsetWidth;
        
        // Clear auto slide during touch
        clearInterval(autoSlideInterval);
    }
    
    // Handle touch move
    function handleTouchMove(e) {
        if (!startX) return;
        
        moveX = e.touches[0].clientX;
        const diff = moveX - startX;
        
        // Move the track with finger
        finalPosition = initialPosition + diff;
        testimonialsTrack.style.transition = 'none';
        testimonialsTrack.style.transform = `translateX(${finalPosition}px)`;
    }
    
    // Handle touch end
    function handleTouchEnd() {
        if (!startX || !moveX) {
            startX = null;
            moveX = null;
            return;
        }
        
        const diff = moveX - startX;
        const threshold = testimonialCards[0].offsetWidth / 3;
        
        if (diff > threshold) {
            // Swipe right - go to previous slide
            prevSlide();
        } else if (diff < -threshold) {
            // Swipe left - go to next slide
            nextSlide();
        } else {
            // Return to current slide
            goToSlide(currentIndex);
        }
        
        // Reset touch values
        startX = null;
        moveX = null;
        
        // Restart auto slide
        autoSlideInterval = setInterval(nextSlide, 5000);
    }
    
    // Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
    
    // Initialize on load
    initTestimonials();
    
    // Handle window resize
    window.addEventListener('resize', () => {
        initTestimonials();
        goToSlide(currentIndex);
    });
    
    // Initialize testimonial form submission
    const testimonialForm = document.getElementById('testimonial-form');
    const successMessage = document.getElementById('testimonial-success-message');
    
    if (testimonialForm && successMessage) {
        testimonialForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect form data
            const formData = new FormData(testimonialForm);
            
            // Send data to server
            fetch('submit-testimonial.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    successMessage.classList.add('show');
                    testimonialForm.reset();
                    
                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        successMessage.classList.remove('show');
                    }, 5000);
                } else {
                    alert(data.message || 'Error submitting testimonial. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }
});