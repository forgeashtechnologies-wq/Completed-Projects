// Debug: Script loading confirmation
console.log('Gallery script loaded successfully');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    // Fallback for missing elements
    function safeQuerySelector(selector, context = document) {
        const element = context.querySelector(selector);
        if (!element) {
            console.warn(`Element not found: ${selector}`);
            return null;
        }
        return element;
    }

    function safeQuerySelectorAll(selector, context = document) {
        const elements = context.querySelectorAll(selector);
        if (elements.length === 0) {
            console.warn(`No elements found: ${selector}`);
        }
        return elements;
    }

    // Safe image loading
    function handleImageLoad(img) {
        if (!img) {
            console.warn('Attempted to handle load for null image');
            return;
        }

        // Ensure img is an image element
        if (!(img instanceof HTMLImageElement)) {
            console.warn('Not an image element', img);
            return;
        }

        try {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
            });
            
            // If image is already loaded
            if (img.complete) {
                img.classList.add('loaded');
            }
        } catch (error) {
            console.error('Error handling image load:', error);
        }
    }

    // Initialize thumbnails and handle image loading
    function initializeGallery() {
        const amenityImages = safeQuerySelectorAll('.amenity-image');
        
        if (amenityImages.length === 0) {
            console.warn('No amenity images found to initialize gallery');
            return;
        }

        amenityImages.forEach(amenityImage => {
            const thumbs = safeQuerySelectorAll('.preview-thumb', amenityImage);
            const mainImage = safeQuerySelector('.main-image img', amenityImage);

            // Skip if main image is missing
            if (!mainImage) {
                console.warn('Main image not found in amenity image');
                return;
            }

            handleImageLoad(mainImage);

            // Collect all image sources, including main image and thumbnails
            const allSources = [
                mainImage.src,
                ...[...thumbs].map(thumb => thumb.getAttribute('data-src') || '')
                    .filter(src => src.trim() !== '')
            ];

            // Main image click opens modal
            mainImage.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(allSources, 0);
            });

            thumbs.forEach((thumb, index) => {
                const thumbImg = safeQuerySelector('img', thumb);
                if (thumbImg) {
                    handleImageLoad(thumbImg);
                }

                thumb.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const src = this.getAttribute('data-src');
                    
                    if (!e.ctrlKey && !e.metaKey) {
                        if (mainImage && src) {
                            mainImage.classList.remove('loaded');
                            mainImage.src = src;
                            handleImageLoad(mainImage);
                            
                            thumbs.forEach(t => t.classList.remove('active'));
                            this.classList.add('active');
                        }
                    } else {
                        openModal(allSources, index + 1);
                    }
                });

                thumb.addEventListener('dblclick', function(e) {
                    e.preventDefault();
                    openModal(allSources, index + 1);
                });
            });
        });
    }

    // Get modal elements with fallback
    const modal = safeQuerySelector('#galleryModal');
    const modalImage = modal ? safeQuerySelector('.modal-image', modal) : null;
    const closeBtn = modal ? safeQuerySelector('.modal-close', modal) : null;
    const prevBtn = modal ? safeQuerySelector('.modal-prev', modal) : null;
    const nextBtn = modal ? safeQuerySelector('.modal-next', modal) : null;

    // Validate modal elements
    if (!modal || !modalImage || !closeBtn || !prevBtn || !nextBtn) {
        console.error('One or more modal elements are missing');
        return;
    }

    let currentGallery = [];
    let currentIndex = 0;

    function openModal(gallery, index) {
        // Remove duplicates while preserving order
        currentGallery = [...new Set(gallery)].filter(src => src.trim() !== '');
        
        if (currentGallery.length === 0) {
            console.warn('No images to display in gallery');
            return;
        }

        currentIndex = Math.min(index, currentGallery.length - 1);
        
        updateModalImage();
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        console.log('Modal opened', {
            galleryLength: currentGallery.length,
            currentIndex: currentIndex
        });
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        console.log('Modal closed');
    }

    function updateModalImage() {
        if (!modalImage || !currentGallery[currentIndex]) {
            console.warn('Cannot update modal image');
            return;
        }

        modalImage.classList.remove('loaded');
        modalImage.src = currentGallery[currentIndex];
        handleImageLoad(modalImage);
        
        console.log('Image updated', {
            currentIndex: currentIndex,
            totalImages: currentGallery.length
        });
    }

    function nextImage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        currentIndex = (currentIndex + 1) % currentGallery.length;
        updateModalImage();
        console.log('Next image clicked');
    }

    function prevImage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
        updateModalImage();
        console.log('Previous image clicked');
    }

    // Initialize modal controls
    function initializeModalControls() {
        // Close button
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        });

        // Navigation buttons
        prevBtn.addEventListener('click', prevImage);
        nextBtn.addEventListener('click', nextImage);

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (modal.style.display === 'block') {
                switch(e.key) {
                    case 'ArrowRight':
                        e.preventDefault();
                        nextImage();
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        prevImage();
                        break;
                    case 'Escape':
                        e.preventDefault();
                        closeModal();
                        break;
                }
            }
        });

        // Click outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Prevent image dragging
        modalImage.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    }

    // Initialize everything
    try {
        initializeGallery();
        initializeModalControls();
    } catch (error) {
        console.error('Error initializing gallery:', error);
    }
});
