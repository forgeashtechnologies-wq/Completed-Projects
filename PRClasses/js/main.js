// Mobile menu improvements
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const dropdownLinks = document.querySelectorAll('.has-dropdown > a');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Handle dropdown menus on mobile
    if (dropdownLinks) {
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Only prevent default on mobile
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    const dropdown = this.nextElementSibling;
                    
                    // Close all other dropdowns
                    dropdownLinks.forEach(otherLink => {
                        if (otherLink !== link) {
                            otherLink.nextElementSibling.classList.remove('show');
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle('show');
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.has-dropdown')) {
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
    }
}); 