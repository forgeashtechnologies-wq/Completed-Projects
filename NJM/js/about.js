document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary elements
    const reviewsContainer = document.querySelector('.reviews-container');
    const prevButton = document.querySelector('.prev-btn');
    const nextButton = document.querySelector('.next-btn');
    const reviewCards = document.querySelectorAll('.review-card');
    
    let currentIndex = 0;
    
    // Function to update carousel position
    function updateCarousel() {
        // Get the width of a single card including margin
        const cardWidth = reviewCards[0].offsetWidth + 16; // 16px for margin-right
        
        // Calculate how many cards to show based on screen width
        const cardsToShow = window.innerWidth < 768 ? 1 : 
                           window.innerWidth < 1200 ? 2 : 3;
        
        // Update carousel position
        reviewsContainer.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        
        // Update button states
        prevButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex >= reviewCards.length - cardsToShow;
        
        // Add visual feedback for disabled state
        prevButton.style.opacity = prevButton.disabled ? '0.5' : '1';
        nextButton.style.opacity = nextButton.disabled ? '0.5' : '1';
    }
    
    // Event listeners for navigation buttons
    prevButton.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
    
    nextButton.addEventListener('click', () => {
        const cardsToShow = window.innerWidth < 768 ? 1 : 
                           window.innerWidth < 1200 ? 2 : 3;
        if (currentIndex < reviewCards.length - cardsToShow) {
            currentIndex++;
            updateCarousel();
        }
    });
    
    // Handle touch events for swipe
    let touchStartX = 0;
    let touchEndX = 0;
    
    reviewsContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, false);
    
    reviewsContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        const swipeThreshold = 50; // minimum distance for swipe
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0 && currentIndex < reviewCards.length - 1) {
                // Swiped left
                currentIndex++;
                updateCarousel();
            } else if (diff < 0 && currentIndex > 0) {
                // Swiped right
                currentIndex--;
                updateCarousel();
            }
        }
    }
    
    // Update on window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Reset position when screen size changes
            currentIndex = 0;
            updateCarousel();
        }, 250);
    });
    
    // Initial setup
    updateCarousel();
});
