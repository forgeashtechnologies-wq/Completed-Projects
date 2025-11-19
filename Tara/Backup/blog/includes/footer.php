<?php
// Footer Template for Blog System
?>
<footer class="ftco-footer ftco-bg-dark ftco-section">
      <div class="container">
          <div class="row mb-3">
              <!-- Social Media Section -->
              <div class="col-md-3">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Connect With Us</h2>
                    <div class="social-media-box">
                        <ul class="ftco-footer-social list-unstyled">
                            <li class="ftco-animate">
                                <a href="https://www.linkedin.com/company/tarasdentalandaestheticcenter/?viewAsMember=true" target="_blank">
                                    <span class="social-icon icon-linkedin"></span>
                                    <span class="social-text">LinkedIn</span>
                                </a>
                            </li>
                            <li class="ftco-animate">
                                <a href="https://www.facebook.com/profile.php?id=61556780617514" target="_blank">
                                    <span class="social-icon icon-facebook"></span>
                                    <span class="social-text">Facebook</span>
                                </a>
                            </li>
                            <li class="ftco-animate">
                                <a href="https://www.instagram.com/tarasdental_aesthetic?utm_source=qr&igsh=M3A3dWY0NmI0a2Vt" target="_blank">
                                    <span class="social-icon icon-instagram"></span>
                                    <span class="social-text">Instagram</span>
                                </a>
                            </li>
                            <li class="ftco-animate">
                                <a href="https://wa.me/916374076545" target="_blank">
                                    <span class="social-icon icon-whatsapp"></span>
                                    <span class="social-text">WhatsApp</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

              <!-- Quick Links Section -->
              <div class="col-md-2 quick-links-column">
                <div class="ftco-footer-widget mb-4 quick-links-widget">
                    <h2 class="ftco-heading-2">Quick Links</h2>
                    <ul class="list-unstyled footer-links">
                        <li><a href="../about.html" class="py-2 d-block">About</a></li>
                        <li><a href="index.php" class="py-2 d-block">Dental Tips & Advice</a></li>
                        <li><a href="../services.html" class="py-2 d-block">Our Treatments</a></li>            
                        <li><a href="../contact.html" class="py-2 d-block">Contact us</a></li>
                    </ul>
                </div>
            </div>

              <!-- Contact Information -->
              <div class="col-md-3">
                  <div class="ftco-footer-widget mb-4">
                      <h2 class="ftco-heading-2 mb-4">Reach Us At</h2>
                      <div class="block-23">
                          <ul class="list-unstyled contact-info">
                              <li class="contact-item">
                                  <span class="icon icon-map-marker"></span>
                                  <span class="text">
                                    <a href="https://maps.app.goo.gl/Be4FzxuCB7zxWHNv6" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="address-link">
                                        18A, 5th St, behind Federal Bank, Rajalakshmi Nagar, Velachery, Chennai, Tamil Nadu 600042
                                    </a>
                                </span>
                              </li>
                              <li class="contact-item">
                                  <a href="tel:+916374076545">
                                      <span class="icon icon-phone"></span>
                                      <span class="text">+91 63740 76545</span>
                                  </a>
                              </li>
                              <li class="contact-item">
                                  <a href="mailto:tarasmdentistry@gmail.com">
                                      <span class="icon icon-envelope"></span>
                                      <span class="text">tarasmdentistry@gmail.com</span>
                                  </a>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>

              <!-- Working Hours -->
              <div class="col-md-4">
                  <div class="ftco-footer-widget mb-4">
                      <h2 class="ftco-heading-2 mb-4">Working Hours</h2>
                      <div class="timing-box">
                          <ul class="list-unstyled contact-info">
                              <li class="timing-item">
                                  <span class="icon icon-clock-o"></span>
                                  <span class="text">
                                    <strong class="schedule-day">Mon – Sat:</strong>
                                    <span class="schedule-time">10:00 AM – 1:00 PM | 4:30 PM – 9:00 PM</span>
                                </span>
                              </li>
                              <li class="timing-item">
                                  <span class="icon icon-info-circle"></span>
                                  <span class="text">Need an appointment outside our hours? Just give us a call — we're here to help, even on Sundays!</span>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Copyright Section -->
          <div class="row">
              <div class="col-md-12">
                  <div class="copyright-section text-center">
                      <p class="copyright">
                          Copyright &copy; <script>document.write(new Date().getFullYear())</script> All rights reserved
                      </p>
                  </div>
              </div>
          </div>
      </div>
</footer>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/916374076545" class="whatsapp-icon" id="whatsapp-icon" aria-label="Chat on WhatsApp">
    <img src="../images/4-2-whatsapp-transparent.png" alt="WhatsApp Icon" width="50" height="50">
</a>

<style>
/* Footer Main Structure */
.ftco-footer {
    padding: 2em 0 0em 0;
}

.ftco-footer-widget {
    padding: 0;
    margin-bottom: 2em;
}

/* Section Headers */
.ftco-heading-2 {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 35px !important;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Quick Links */
.footer-links,
.list-unstyled {
    padding: 0;
    margin: 0;
    list-style: none;
}

.footer-links li,
.list-unstyled li {
    padding: 5px 0;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.footer-links li:last-child,
.list-unstyled li:last-child {
    margin-bottom: 0;
}

.footer-links a,
.list-unstyled li a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 16px;
    transition: all 0.3s ease;
    display: block;
    padding: 2px 0;
}

.footer-links a:hover,
.list-unstyled li a:hover {
    color: #fff;
    padding-left: 5px;
    text-decoration: none;
}

/* Social Media Icons */
.social-media-box {
    margin-top: 20px;
}

.ftco-footer-social {
    padding: 0;
    margin: 0;
}

.ftco-footer-social li {
    display: block;
    margin-bottom: 25px;
    opacity: 1;
}

.ftco-footer-social li:last-child {
    margin-bottom: 0;
}

.ftco-footer-social li a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease;
}

.ftco-footer-social li a:hover {
    color: #fff;
    padding-left: 5px;
    text-decoration: none;
}

.social-icon {
    margin-left: 2.5cap;
    font-size: 24px;
    width: 50px;
    min-width: 50px;
    display: inline-block;
    text-align: left;
    margin-right: 15px;
}

.social-text {
    margin-left: 5.5cap;
    font-size: 16px;
    font-weight: 400;
    letter-spacing: 0.5px;
    padding-left: 10px;
}

/* Contact and Timing Information */
.contact-info li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 15px;
    line-height: 1.6;
    transition: all 0.3s ease;
}

.contact-info li:hover {
    color: #fff;
    padding-left: 5px;
}

.contact-info li .icon {
    font-size: 18px;
    margin-right: 15px;
    min-width: 25px;
    color: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease;
    padding-top: 3px;
}

.contact-info li:hover .icon {
    color: #fff;
}

.quick-links-column {
    padding-right: 15px;
    padding-left: 0;
}

.quick-links-widget {
    margin-left: -25px;
}

/* Schedule display styles */
.schedule-day {
    display: block;
    text-align: left center;
    margin-bottom: 5px;
    color: #fff;
}

.schedule-time {
    display: block;
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
}

/* Whatsapp float button styles */
.whatsapp-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.whatsapp-icon.show {
    opacity: 1;
}

@media (max-width: 768px) {
    .quick-links-widget {
        margin-left: -10px;
    }
    .schedule-day {
        margin-bottom: 3px;
    }
    .schedule-time {
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var whatsappIcon = document.getElementById("whatsapp-icon");

    // Hide WhatsApp icon initially
    whatsappIcon.style.display = "none";

    window.addEventListener('scroll', function() {
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        var scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var scrollPercentTop = (scrollTop / scrollHeight) * 100;

        // Show WhatsApp icon when scrolled more than 20% down from the top
        if (scrollPercentTop > 20) {
            whatsappIcon.style.display = "block";
            whatsappIcon.classList.add("show");
        } else {
            whatsappIcon.classList.remove("show");
            setTimeout(function() {
                whatsappIcon.style.display = "none";
            }, 500); // Wait for fade-out before hiding
        }
    });
});
</script>