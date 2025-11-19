// components/footer.js

document.addEventListener('DOMContentLoaded', function() {
    // Create a function to load and insert the footer
    function loadFooter() {
        fetch('/components/footer.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('footer-placeholder').innerHTML = data;
                
                // Reinitialize any scripts that the footer needs
                initializeFooterScripts();
            })
            .catch(error => console.error('Error loading footer:', error));
    }

    // Initialize footer-specific scripts
    function initializeFooterScripts() {
        // WhatsApp float button behavior
        var whatsappIcon = document.getElementById("whatsapp-icon");
        if (whatsappIcon) {
            whatsappIcon.style.display = "none";
            
            window.addEventListener('scroll', function() {
                var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                var scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                var scrollPercentTop = (scrollTop / scrollHeight) * 100;

                if (scrollPercentTop > 20) {
                    whatsappIcon.style.display = "block";
                    whatsappIcon.classList.add("show");
                } else {
                    whatsappIcon.classList.remove("show");
                    setTimeout(function() {
                        whatsappIcon.style.display = "none";
                    }, 500);
                }
            });
        }
    }

    // Load the footer when the page loads
    loadFooter();
});