document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS animation library
    AOS.init({
        duration: 800,
        easing: 'ease',
        once: true,
        offset: 100
    });

    // Category Filter Functionality
    const categoryButtons = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const category = this.getAttribute('data-category');
            
            // Filter products
            filterProducts(category);
        });
    });
    
    function filterProducts(category) {
        productCards.forEach(card => {
            // First, remove any animation classes
            card.classList.remove('scale-in', 'fade-out');
            
            // If 'all' category is selected or card matches the category
            if (category === 'all' || card.classList.contains(category)) {
                // Show the card with animation
                card.classList.remove('hidden');
                card.classList.add('scale-in');
            } else {
                // Hide the card with animation
                card.classList.add('fade-out');
                
                // After animation completes, hide the element
                setTimeout(() => {
                    card.classList.add('hidden');
                }, 500);
            }
        });
    }
    
    // Load More Products Functionality
    const loadMoreBtn = document.querySelector('.load-more-btn');
    let currentItems = 12; // Initial number of items to show
    let increment = 6; // Number of items to load each time
    
    // Hide products beyond the initial count
    function hideExtraProducts() {
        const visibleCategory = document.querySelector('.category-btn.active').getAttribute('data-category');
        
        let visibleProducts = visibleCategory === 'all' 
            ? document.querySelectorAll('.product-card') 
            : document.querySelectorAll(`.product-card.${visibleCategory}`);
        
        // Convert NodeList to Array to use array methods
        visibleProducts = Array.from(visibleProducts);
        
        // Hide products beyond the currentItems count
        visibleProducts.forEach((product, index) => {
            if (index >= currentItems) {
                product.classList.add('hidden');
            }
        });
        
        // Hide the load more button if all products are shown
        if (visibleProducts.length <= currentItems) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-block';
        }
    }
    
    // Call initially to set up the page
    hideExtraProducts();
    
    // Load more products when the button is clicked
    loadMoreBtn.addEventListener('click', function() {
        currentItems += increment;
        
        const visibleCategory = document.querySelector('.category-btn.active').getAttribute('data-category');
        
        let visibleProducts = visibleCategory === 'all' 
            ? document.querySelectorAll('.product-card') 
            : document.querySelectorAll(`.product-card.${visibleCategory}`);
        
        // Convert NodeList to Array
        visibleProducts = Array.from(visibleProducts);
        
        // Show more products with a slight delay for each
        visibleProducts.forEach((product, index) => {
            if (index < currentItems && product.classList.contains('hidden')) {
                setTimeout(() => {
                    product.classList.remove('hidden');
                    product.classList.add('scale-in');
                }, (index % increment) * 100); // Staggered animation
            }
        });
        
        // Hide the load more button if all products are shown
        if (visibleProducts.length <= currentItems) {
            loadMoreBtn.style.display = 'none';
        }
    });
    
    // Reset current items when changing categories
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentItems = 12; // Reset to initial count
            setTimeout(hideExtraProducts, 600); // Wait for filter animation to complete
        });
    });
    
    // Add hover effect for product images
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('.product-image img');
            img.style.transform = 'scale(1.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('.product-image img');
            img.style.transform = 'scale(1)';
        });
    });
    
    // Add product badges dynamically (example: new products, sale items)
    // function addProductBadges() {
    //     // Example: Add "New" badge to the first two products
    //     const newProducts = document.querySelectorAll('.product-card:nth-child(-n+2)');
    //     newProducts.forEach(product => {
    //         const badge = document.createElement('div');
    //         badge.className = 'product-badge';
    //         badge.textContent = 'New';
    //         product.appendChild(badge);
    //     });
        
    //     // Example: Add "Sale" badge to some products
    //     const saleProducts = document.querySelectorAll('.product-card:nth-child(5), .product-card:nth-child(10), .product-card:nth-child(15)');
    //     saleProducts.forEach(product => {
    //         const badge = document.createElement('div');
    //         badge.className = 'product-badge';
    //         badge.textContent = 'Sale';
    //         badge.style.backgroundColor = '#ff6b6b';
    //         product.appendChild(badge);
    //     });
    // }
    
    // Call to add badges
    addProductBadges();
    
    // Scroll to top when filter buttons are clicked
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productsSection = document.querySelector('.products-display');
            window.scrollTo({
                top: productsSection.offsetTop - 100,
                behavior: 'smooth'
            });
        });
    });
});