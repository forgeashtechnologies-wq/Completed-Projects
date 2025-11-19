// Payment QR code modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const scanBtns = document.querySelectorAll('.scan-btn');
    const modal = document.getElementById('scanModal');
    const closeModal = document.querySelector('.close-modal');
    
    scanBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            // You can use courseId to show different QR codes for different courses
            modal.style.display = 'block';
        });
    });
    
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
}); 