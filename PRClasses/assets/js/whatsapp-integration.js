// WhatsApp Integration for PR Classes Website

document.addEventListener('DOMContentLoaded', function() {
    // WhatsApp configuration
    const whatsappNumber = '919042796696'; // Replace with your WhatsApp number
    const defaultMessage = 'Hello, I would like to inquire about your courses.'; // Default message
    
    // Initialize WhatsApp buttons
    initWhatsAppButtons();
    
    // Initialize floating WhatsApp button if it exists
    const floatingWhatsApp = document.querySelector('.floating-whatsapp');
    if (floatingWhatsApp) {
        updateWhatsAppLink(floatingWhatsApp, defaultMessage);
    }
    
    // Initialize WhatsApp integration for contact form
    initContactFormWhatsApp();
    
    // Initialize WhatsApp integration for course inquiry
    initCourseInquiryWhatsApp();
    
    /**
     * Initialize all WhatsApp buttons on the page
     */
    function initWhatsAppButtons() {
        const whatsappButtons = document.querySelectorAll('.btn-whatsapp');
        
        whatsappButtons.forEach(button => {
            // Get custom message if specified
            const customMessage = button.getAttribute('data-message') || defaultMessage;
            updateWhatsAppLink(button, customMessage);
            
            // Track WhatsApp button clicks
            button.addEventListener('click', function() {
                console.log('WhatsApp button clicked');
                // You can add analytics tracking here
            });
        });
    }
    
    /**
     * Update WhatsApp link with the correct number and message
     */
    function updateWhatsAppLink(element, message) {
        const encodedMessage = encodeURIComponent(message);
        const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
        
        if (element.tagName.toLowerCase() === 'a') {
            element.href = whatsappLink;
        } else {
            const linkElement = element.querySelector('a');
            if (linkElement) {
                linkElement.href = whatsappLink;
            }
        }
    }
    
    /**
     * Initialize WhatsApp integration for contact form
     */
    function initContactFormWhatsApp() {
        const contactForm = document.getElementById('contact-form');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const name = document.getElementById('name').value;
                const mobile = document.getElementById('mobile').value;
                const query = document.getElementById('query').value;
                
                // Validate form
                if (!name || !mobile || !query) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Create WhatsApp message
                const message = `Hello, I'm ${name}. ${query} (Mobile: ${mobile})`;
                
                // Submit form data via AJAX
                const formData = new FormData(contactForm);
                
                fetch('contact.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const formResponse = document.getElementById('form-response');
                        if (formResponse) {
                            formResponse.innerHTML = `<div class="success-message">${data.message}</div>`;
                            formResponse.style.display = 'block';
                        }
                        
                        // Reset form
                        contactForm.reset();
                        
                        // Open WhatsApp with pre-filled message
                        window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');
                    } else {
                        // Show error message
                        const formResponse = document.getElementById('form-response');
                        if (formResponse) {
                            formResponse.innerHTML = `<div class="error-message">${data.message}</div>`;
                            formResponse.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again later.');
                });
            });
        }
    }
    
    /**
     * Initialize WhatsApp integration for course inquiry
     */
    function initCourseInquiryWhatsApp() {
        const joinForm = document.getElementById('join-form');
        
        if (joinForm) {
            joinForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const course = document.getElementById('selected-course').value;
                const name = document.getElementById('join-name').value;
                const mobile = document.getElementById('join-mobile').value;
                const email = document.getElementById('join-email').value;
                
                // Validate form
                if (!course || !name || !mobile || !email) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Create WhatsApp message
                const message = `Hello, I'm ${name} and I'm interested in enrolling for the ${course}. My mobile number is ${mobile} and email is ${email}.`;
                
                // Show success message
                const successMessage = document.getElementById('success-message');
                if (successMessage) {
                    successMessage.style.display = 'block';
                }
                
                // Reset form
                joinForm.reset();
                
                // Open WhatsApp with pre-filled message
                window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');
            });
        }
    }
});