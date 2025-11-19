// Check WebP support
function checkWebP(callback) {
    const webP = new Image();
    webP.onload = webP.onerror = function () {
        callback(webP.height === 2);
    };
    webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
}

// Initialize progressive image loading
function initProgressiveImages() {
    // Check WebP support first
    checkWebP(function(hasWebP) {
        const images = document.querySelectorAll('img.lazyload');
        
        images.forEach(img => {
            // Get parent picture element if it exists
            const picture = img.parentElement.tagName === 'PICTURE' ? img.parentElement : null;
            
            // Create IntersectionObserver for lazy loading
            const observer = new IntersectionObserver(
                (entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            
                            // If WebP is supported and we have a picture element, let the browser handle it
                            if (hasWebP && picture) {
                                img.src = img.dataset.src;
                            } else {
                                // If WebP is not supported, use the fallback jpg/png
                                img.src = img.dataset.src;
                            }
                            
                            // Add loading animation
                            img.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            
                            img.onload = function() {
                                img.classList.add('loaded');
                                
                                // Remove placeholder after image is loaded
                                const placeholder = img.parentElement.querySelector('.placeholder');
                                if (placeholder) {
                                    placeholder.style.opacity = '0';
                                    setTimeout(() => placeholder.remove(), 300);
                                }
                            };
                            
                            observer.unobserve(img);
                        }
                    });
                },
                {
                    rootMargin: '50px 0px', // Start loading images 50px before they enter the viewport
                    threshold: 0.01
                }
            );
            
            observer.observe(img);
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProgressiveImages);
} else {
    initProgressiveImages();
}

// Reinitialize on dynamic content changes
document.addEventListener('contentChanged', initProgressiveImages);
