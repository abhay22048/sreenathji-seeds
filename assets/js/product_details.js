// Initialize AOS animations
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate on Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease',
        once: true,
        offset: 100,
    });
    
    // Product Gallery Functionality
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Update main image
            const imgSrc = this.getAttribute('data-img');
            mainImage.src = imgSrc;
            
            // Update active state
            thumbnails.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
   
    
    // Tab System
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to current button and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Product Carousel
    const productCarousel = document.querySelector('.product-carousel');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    let scrollAmount = 0;
    const scrollStep = 310; // Width of product card + margin
    
    nextBtn.addEventListener('click', function() {
        scrollAmount += scrollStep;
        const maxScroll = productCarousel.scrollWidth - productCarousel.clientWidth;
        
        if (scrollAmount > maxScroll) {
            scrollAmount = maxScroll;
        }
        
        productCarousel.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });
    
    prevBtn.addEventListener('click', function() {
        scrollAmount -= scrollStep;
        
        if (scrollAmount < 0) {
            scrollAmount = 0;
        }
        
        productCarousel.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

   
    
    // Click on indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            currentTestimonial = index;
            showTestimonial(currentTestimonial);
        });
    });
    
    // Back to top button
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Form submission
    const inquiryForm = document.querySelector('.inquiry-form');
    
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            
            // Here you would typically send the form data to a server
            // For now, let's just show a success message
            alert('Thank you for your inquiry! Our team will contact you shortly.');
            inquiryForm.reset();
        });
    }
});
// Growing Guide Section JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Tab System for Growing Guide
    const tabButtons = document.querySelectorAll('.growing-guide .tab-btn');
    const tabPanes = document.querySelectorAll('.growing-guide .tab-pane');
    
    // Initialize tab functionality
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to current button and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Add smooth transition effect
            document.querySelector('.tab-content').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.tab-content').style.opacity = '1';
            }, 150);
        });
    });
    
    // Timeline animation for planting tab
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    function animateTimeline() {
        if (isElementInViewport(document.querySelector('.timeline'))) {
            timelineItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('appear');
                }, index * 300);
            });
        }
    }
    
    // Care items animation
    const careItems = document.querySelectorAll('.care-item');
    
    function animateCareItems() {
        if (isElementInViewport(document.querySelector('.care-container'))) {
            careItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('appear');
                }, index * 200);
            });
        }
    }
    
    // Yield meter animation
    function animateYieldMeter() {
        const yieldMeter = document.querySelector('.yield-progress');
        if (yieldMeter && isElementInViewport(yieldMeter)) {
            yieldMeter.style.width = '0%';
            setTimeout(() => {
                yieldMeter.style.transition = 'width 1.5s ease-in-out';
                yieldMeter.style.width = '85%';
            }, 300);
        }
    }
    
    // Storage methods animation
    const storageMethods = document.querySelectorAll('.storage-method');
    
    function animateStorageMethods() {
        if (isElementInViewport(document.querySelector('.storage-methods'))) {
            storageMethods.forEach((method, index) => {
                setTimeout(() => {
                    method.classList.add('appear');
                }, index * 300);
            });
        }
    }
    
    // Helper function to check if element is in viewport
    function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Run animations when tab changes
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Run specific animations based on the tab
            switch(tabId) {
                case 'planting':
                    setTimeout(animateTimeline, 200);
                    break;
                case 'care':
                    setTimeout(animateCareItems, 200);
                    break;
                case 'harvesting':
                    setTimeout(animateYieldMeter, 200);
                    break;
                case 'storage':
                    setTimeout(animateStorageMethods, 200);
                    break;
            }
        });
    });
    
    // Run animations on scroll
    window.addEventListener('scroll', function() {
        const activeTab = document.querySelector('.tab-pane.active');
        if (activeTab) {
            const tabId = activeTab.id;
            
            switch(tabId) {
                case 'planting':
                    animateTimeline();
                    break;
                case 'care':
                    animateCareItems();
                    break;
                case 'harvesting':
                    animateYieldMeter();
                    break;
                case 'storage':
                    animateStorageMethods();
                    break;
            }
        }
    });
    
    // Initialize animations for the first visible tab on page load
    setTimeout(() => {
        animateTimeline();
    }, 500);
    
    // Interactive elements for the growing guide
    
    // Add hover effects for care items
    careItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.classList.add('hover');
        });
        
        item.addEventListener('mouseleave', function() {
            this.classList.remove('hover');
        });
    });
    
    // Add click to expand more information on timeline items
    timelineItems.forEach(item => {
        item.addEventListener('click', function() {
            // Toggle expanded class
            this.classList.toggle('expanded');
            
            // If there's a hidden additional information section, show it
            const additionalInfo = this.querySelector('.additional-info');
            if (additionalInfo) {
                if (this.classList.contains('expanded')) {
                    additionalInfo.style.maxHeight = additionalInfo.scrollHeight + 'px';
                } else {
                    additionalInfo.style.maxHeight = '0';
                }
            }
        });
    });
    
    // Add tooltips for complex terms
    const complexTerms = document.querySelectorAll('.complex-term');
    complexTerms.forEach(term => {
        term.addEventListener('mouseenter', function() {
            const tooltip = this.querySelector('.tooltip');
            if (tooltip) {
                tooltip.style.display = 'block';
                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);
            }
        });
        
        term.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.tooltip');
            if (tooltip) {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    tooltip.style.display = 'none';
                }, 300);
            }
        });
    });
});


// Product Gallery JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const gallery = document.querySelector('.product-gallery');
    
    // Variables for zoom functionality
    let isZoomed = false;
    let zoomLevel = 1.5;
    
    // Initialize the gallery
    function initGallery() {
        // Set up thumbnail click handlers
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image with smooth transition
                fadeOutMainImage(() => {
                    const imgSrc = this.getAttribute('data-img');
                    mainImage.src = imgSrc;
                    fadeInMainImage();
                    
                    // Update active state
                    thumbnails.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
        
        // Add image zoom functionality
        setupZoom();
        
        // Add keyboard navigation
        setupKeyboardNavigation();
        
        // Add swipe functionality for mobile
        setupSwipeNavigation();
        
        // Preload images for smoother transitions
        preloadImages();
    }
    
    // Fade out the main image
    function fadeOutMainImage(callback) {
        mainImage.style.opacity = '0';
        setTimeout(() => {
            if (callback) callback();
        }, 300);
    }
    
    // Fade in the main image
    function fadeInMainImage() {
        setTimeout(() => {
            mainImage.style.opacity = '1';
        }, 50);
    }
    
    // Set up image zoom functionality
    function setupZoom() {
        const mainImageContainer = document.querySelector('.main-image');
        
        // Create zoom overlay
        const zoomOverlay = document.createElement('div');
        zoomOverlay.classList.add('zoom-overlay');
        zoomOverlay.style.position = 'absolute';
        zoomOverlay.style.top = '0';
        zoomOverlay.style.left = '0';
        zoomOverlay.style.right = '0';
        zoomOverlay.style.bottom = '0';
        zoomOverlay.style.display = 'none';
        zoomOverlay.style.overflow = 'hidden';
        zoomOverlay.style.cursor = 'zoom-out';
        
        // Create zoom image
        const zoomImage = document.createElement('img');
        zoomImage.style.position = 'absolute';
        zoomImage.style.transformOrigin = '0 0';
        zoomOverlay.appendChild(zoomImage);
        
        // Add zoom overlay to container
        mainImageContainer.style.position = 'relative';
        mainImageContainer.appendChild(zoomOverlay);
        
        // Toggle zoom on main image click
        mainImage.style.cursor = 'zoom-in';
        mainImage.addEventListener('click', function() {
            if (!isZoomed) {
                zoomIn();
            }
        });
        
        // Close zoom on overlay click
        zoomOverlay.addEventListener('click', function() {
            if (isZoomed) {
                zoomOut();
            }
        });
        
        // Move zoomed image with mouse
        zoomOverlay.addEventListener('mousemove', function(e) {
            if (isZoomed) {
                const rect = zoomOverlay.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const percentX = x / rect.width;
                const percentY = y / rect.height;
                
                // Calculate position based on zoom level
                const posX = Math.max(0, Math.min(100, percentX * 100));
                const posY = Math.max(0, Math.min(100, percentY * 100));
                
                zoomImage.style.transform = `translate(-${posX}%, -${posY}%) scale(${zoomLevel})`;
            }
        });
        
        // Functions to zoom in and out
        function zoomIn() {
            const currentSrc = mainImage.src;
            zoomImage.src = currentSrc;
            zoomImage.style.width = `${zoomLevel * 100}%`;
            zoomImage.style.height = `${zoomLevel * 100}%`;
            
            // Show overlay
            zoomOverlay.style.display = 'block';
            setTimeout(() => {
                zoomOverlay.style.opacity = '1';
            }, 10);
            
            isZoomed = true;
            mainImage.style.opacity = '0';
        }
        
        function zoomOut() {
            zoomOverlay.style.opacity = '0';
            setTimeout(() => {
                zoomOverlay.style.display = 'none';
            }, 300);
            
            isZoomed = false;
            mainImage.style.opacity = '1';
        }
    }
    
    // Setup keyboard navigation
    function setupKeyboardNavigation() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                navigateGallery('prev');
            } else if (e.key === 'ArrowRight') {
                navigateGallery('next');
            } else if (e.key === 'Escape' && isZoomed) {
                // Close zoom if open
                const zoomOverlay = document.querySelector('.zoom-overlay');
                if (zoomOverlay) {
                    zoomOverlay.click();
                }
            }
        });
    }
    
    // Navigate gallery with arrow keys or buttons
    function navigateGallery(direction) {
        const activeThumbnail = document.querySelector('.thumbnail.active');
        if (!activeThumbnail) return;
        
        let targetThumbnail;
        
        if (direction === 'next') {
            targetThumbnail = activeThumbnail.nextElementSibling || thumbnails[0];
        } else {
            targetThumbnail = activeThumbnail.previousElementSibling || thumbnails[thumbnails.length - 1];
        }
        
        if (targetThumbnail) {
            targetThumbnail.click();
        }
    }
    
    // Setup swipe navigation for mobile
    function setupSwipeNavigation() {
        let touchStartX = 0;
        let touchEndX = 0;
        
        const mainImageContainer = document.querySelector('.main-image');
        
        mainImageContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        mainImageContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left, go to next image
                navigateGallery('next');
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right, go to previous image
                navigateGallery('prev');
            }
        }
    }
    
    // Preload images for smoother transitions
    function preloadImages() {
        thumbnails.forEach(thumbnail => {
            const imgSrc = thumbnail.getAttribute('data-img');
            const preloadImage = new Image();
            preloadImage.src = imgSrc;
        });
    }
    
    // Add navigation buttons to the gallery
    function addGalleryNavButtons() {
        const navPrev = document.createElement('button');
        navPrev.classList.add('gallery-nav', 'prev');
        navPrev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        
        const navNext = document.createElement('button');
        navNext.classList.add('gallery-nav', 'next');
        navNext.innerHTML = '<i class="fas fa-chevron-right"></i>';
        
        const mainImageContainer = document.querySelector('.main-image');
        mainImageContainer.appendChild(navPrev);
        mainImageContainer.appendChild(navNext);
        
        navPrev.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering zoom
            navigateGallery('prev');
        });
        
        navNext.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering zoom
            navigateGallery('next');
        });
    }
    
    // Initialize everything
    if (gallery && mainImage && thumbnails.length > 0) {
        // Add navigation buttons
        addGalleryNavButtons();
        
        // Set up main functionality
        initGallery();
        
        // Add smooth transition to main image
        mainImage.style.transition = 'opacity 0.3s ease';
    }
});