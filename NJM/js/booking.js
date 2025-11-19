document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-form');
    
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect form data
            const formData = {
                eventType: document.getElementById('event-type').value,
                guestCount: document.getElementById('guest-count').value,
                venuePreference: document.getElementById('venue-preference').value,
                eventDate: document.getElementById('event-date').value,
                eventTime: document.getElementById('event-time').value,
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                additionalRequirements: document.getElementById('additional-requirements').value
            };
            
            // Validate required fields
            const requiredFields = ['eventType', 'guestCount', 'venuePreference', 'eventDate', 'eventTime', 'name', 'phone'];
            const missingFields = requiredFields.filter(field => !formData[field]);
            
            if (missingFields.length > 0) {
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }
            
            // Prepare WhatsApp message
            const message = `
Jay Mahal Event Booking Inquiry:

Event Type: ${formData.eventType}
Number of Guests: ${formData.guestCount}
Venue Preference: ${formData.venuePreference}
Event Date: ${formData.eventDate}
Event Time: ${formData.eventTime}
Name: ${formData.name}
Phone: ${formData.phone}
Email: ${formData.email || 'Not provided'}

Additional Requirements:
${formData.additionalRequirements || 'None'}
            `.trim();
            
            // Encode message for WhatsApp
            const encodedMessage = encodeURIComponent(message);
            
            // Replace with actual WhatsApp number
            const phoneNumber = '+919876543210'; // Replace with actual contact number
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
            
            // Open WhatsApp
            window.open(whatsappUrl, '_blank');
        });
    }
});
