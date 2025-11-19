/**
 * PR Classes - Courses Page JavaScript
 * Minimal JS functionality for the courses.php page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize card hover effects
    initCardHoverEffects();
    
    // Initialize share functionality
    initShareButtons();
    
    // Initialize scan for payment button
    initScanForPaymentButton();
});

/**
 * Subtle hover effects for course cards
 */
function initCardHoverEffects() {
    const courseCards = document.querySelectorAll('.course-card');
    
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.08)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.03)';
        });
    });
}

/**
 * Initialize share buttons with Web Share API when available
 */
function initShareButtons() {
    const shareButtons = document.querySelectorAll('.btn-outline-secondary');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Try to use the Web Share API if available
            if (navigator.share) {
                e.preventDefault();
                
                const url = this.getAttribute('href').split('text=')[1];
                const decodedText = decodeURIComponent(url);
                const shareText = decodedText.split(' - ')[0];
                const shareUrl = decodedText.split(' - ')[1];
                
                navigator.share({
                    title: 'PR Classes Course',
                    text: shareText,
                    url: shareUrl
                }).then(() => {
                    console.log('Share successful');
                }).catch((error) => {
                    console.log('Share failed:', error);
                });
            }
        });
    });
}

/**
 * Initialize scan for payment button functionality
 * Only shows QR code when button is clicked
 */
function initScanForPaymentButton() {
    // Get the scan button and QR code floater elements
    const scanButton = document.getElementById('scan-for-payment-btn');
    const qrCodeFloater = document.querySelector('.qr-code-floater');
    
    // Only proceed if both elements exist (we're on the courses page)
    if (scanButton && qrCodeFloater) {
        // Hide the QR code floater by default (it should only show when button is clicked)
        qrCodeFloater.classList.remove('visible');
        
        // Add click event to scan button
        scanButton.addEventListener('click', function(e) {
            e.preventDefault();
            qrCodeFloater.classList.add('visible');
        });
        
        // Add click event to close button
        const closeBtn = qrCodeFloater.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                qrCodeFloater.classList.remove('visible');
            });
        }
        
        // Prevent the automatic showing of QR code on scroll
        // by overriding the scroll event listener
        window.removeEventListener('scroll', window.qrCodeScrollHandler);
    }
}