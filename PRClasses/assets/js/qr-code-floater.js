/**
 * QR Code Floater Script
 * Shows a floating QR code when the user scrolls down the page
 * Enhanced version with improved animations and user experience
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the QR code floater element
    const qrCodeFloater = document.querySelector('.qr-code-floater');
    
    // If QR code floater doesn't exist, exit
    if (!qrCodeFloater) return;
    
    // Variables for scroll tracking
    let scrollTimer;
    let hasShown = false;
    let isAnimating = false;
    const scrollThreshold = 100; // Reduced threshold - show after scrolling just 100px
    const delayBeforeShow = 500; // Reduced delay - show after 500ms
    
    // Function to show the QR code floater with enhanced animation
    function showQRCodeFloater() {
        // Force show the QR code floater regardless of previous state
        isAnimating = true;
        
        // Add visible class to start animation
        qrCodeFloater.classList.add('visible');
        console.log('QR Code Floater: Adding visible class');
        
        // Add subtle animation to QR code image
        const qrImage = qrCodeFloater.querySelector('img');
        if (qrImage) {
            console.log('QR Code Floater: Image found, animating');
            setTimeout(() => {
                qrImage.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    qrImage.style.transform = 'scale(1)';
                }, 300);
            }, 400);
        } else {
            console.log('QR Code Floater: Image not found');
        }
        
        hasShown = true;
        isAnimating = false;
    }
    
    // Function to hide the QR code floater
    function hideQRCodeFloater() {
        if (!isAnimating) {
            isAnimating = true;
            qrCodeFloater.classList.remove('visible');
            setTimeout(() => {
                isAnimating = false;
            }, 400); // Match transition duration
        }
    }
    
    // Add click event to close button
    const closeBtn = qrCodeFloater.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            hideQRCodeFloater();
            
            // Set a cookie to remember that user closed the QR code floater
            // This will prevent it from showing again for 7 days
            const expiryDate = new Date();
            expiryDate.setDate(expiryDate.getDate() + 7);
            document.cookie = 'qrCodeFloaterClosed=true; expires=' + expiryDate.toUTCString() + '; path=/';
        });
    }
    
    // Check if user has previously closed the QR code floater
    function hasClosedQRCodeFloater() {
        // For debugging - always return false to ensure QR code shows
        return false;
        // Original code: return document.cookie.indexOf('qrCodeFloaterClosed=true') !== -1;
    }
    
    // Handle scroll event with improved behavior
    window.addEventListener('scroll', function() {
        // If user has closed the QR code floater before, don't show it
        if (hasClosedQRCodeFloater()) return;
        
        // Clear the previous timer
        clearTimeout(scrollTimer);
        
        // Set a new timer to show the QR code floater after scrolling stops
        scrollTimer = setTimeout(function() {
            if (window.scrollY > scrollThreshold) {
                // Add delay before showing for better UX
                setTimeout(() => {
                    showQRCodeFloater();
                }, hasShown ? 0 : delayBeforeShow);
            } else {
                hideQRCodeFloater();
                hasShown = false;
            }
        }, 300);
    });
    
    // Add hover effect to QR code image
    const qrImage = qrCodeFloater.querySelector('img');
    if (qrImage) {
        qrImage.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        qrImage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    // Force show the QR code floater after a short delay
    console.log('QR Code Floater: Script loaded, will show after delay');
    setTimeout(function() {
        console.log('QR Code Floater: Showing after page load');
        showQRCodeFloater();
    }, 2000);
});