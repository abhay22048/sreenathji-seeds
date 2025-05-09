// DOM Elements
document.addEventListener('DOMContentLoaded', function() {
    // Page Loader
    const loader = document.querySelector('.loader');
    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
        }, 2000);
    });

    // Mobile Navigation
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
    });
    
    // Close mobile menu when clicking on links
    const navItems = document.querySelectorAll('.nav-links li a');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        });
    });

    // Header scroll effect
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Active Navigation Link
    const sections = document.querySelectorAll('section');
    const navLi = document.querySelectorAll('.nav-links li a');
    
    window.addEventListener('scroll', () => {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });
        
        navLi.forEach(li => {
            li.classList.remove('active');
            if (li.getAttribute('href') === `#${current}`) {
                li.classList.add('active');
            }
        });
    });

    // Stats Counter Animation
    const statItems = document.querySelectorAll('.stat-item');
    
    const options = {
        threshold: 0.5
    };
    
    const statObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, options);
    
    statItems.forEach(item => {
        statObserver.observe(item);
    });
    
    function animateCounter(element) {
        const counter = element.querySelector('.stat-number');
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = Math.ceil(target / (duration / 30));
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = current;
            }
        }, 30);
    }

    // 3D Canvas Animation for Hero Section
    initThreeJS();

    // Contact Form Validation
 
  


});

// ThreeJS Animation
function initThreeJS() {
    const container = document.getElementById('canvas-container');
    if (!container) return;

    // Scene setup
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(renderer.domElement);
    
    // Camera position
    camera.position.z = 15;
    
    // Create Seeds (particles)
    const seedCount = 500;
    const seedGeometry = new THREE.SphereGeometry(0.1, 8, 8);
    const seedMaterials = [
        new THREE.MeshBasicMaterial({ color: 0x19972b }), // Primary color
        new THREE.MeshBasicMaterial({ color: 0x034616 }), // Secondary color
        new THREE.MeshBasicMaterial({ color: 0xf8b500 })  // Accent color
    ];
    
    const seeds = new THREE.Group();
    
    for (let i = 0; i < seedCount; i++) {
        const seed = new THREE.Mesh(
            seedGeometry,
            seedMaterials[Math.floor(Math.random() * seedMaterials.length)]
        );
        
        // Position seeds in a spherical formation
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        const radius = 10 + Math.random() * 5;
        
        seed.position.x = radius * Math.sin(phi) * Math.cos(theta);
        seed.position.y = radius * Math.sin(phi) * Math.sin(theta);
        seed.position.z = radius * Math.cos(phi);
        
        // Add velocity for animation
        seed.userData = {
            velocity: new THREE.Vector3(
                (Math.random() - 0.5) * 0.01,
                (Math.random() - 0.5) * 0.01,
                (Math.random() - 0.5) * 0.01
            ),
            initialPosition: seed.position.clone()
        };
        
        seeds.add(seed);
    }
    
    scene.add(seeds);
    
    // Add stylized plant model
    const stemGeometry = new THREE.CylinderGeometry(0.05, 0.1, 3, 8);
    const stemMaterial = new THREE.MeshBasicMaterial({ color: 0x19972b });
    const stem = new THREE.Mesh(stemGeometry, stemMaterial);
    stem.position.set(0, -3, 0);
    
    const leafGeometry = new THREE.SphereGeometry(1, 8, 4);
    const leafMaterial = new THREE.MeshBasicMaterial({ color: 0x19972b });
    
    const leaf1 = new THREE.Mesh(leafGeometry, leafMaterial);
    leaf1.scale.set(0.8, 0.2, 0.5);
    leaf1.position.set(0.8, -2, 0);
    leaf1.rotation.z = Math.PI / 4;
    
    const leaf2 = new THREE.Mesh(leafGeometry, leafMaterial);
    leaf2.scale.set(0.8, 0.2, 0.5);
    leaf2.position.set(-0.8, -1.5, 0);
    leaf2.rotation.z = -Math.PI / 4;
    
    const plant = new THREE.Group();
    plant.add(stem);
    plant.add(leaf1);
    plant.add(leaf2);
    plant.scale.set(0.6, 0.6, 0.6);
    plant.position.set(4, -3, 3);
    
    scene.add(plant);
    
    // Animation
    function animate() {
        requestAnimationFrame(animate);
        
        // Rotate seeds group
        seeds.rotation.y += 0.001;
        
        // Animate individual seeds
        seeds.children.forEach(seed => {
            // Apply velocity
            seed.position.add(seed.userData.velocity);
            
            // Calculate distance from original position
            const distance = seed.position.distanceTo(seed.userData.initialPosition);
            
            // If seed goes too far, reset its position
            if (distance > 2) {
                const direction = seed.position.clone().sub(seed.userData.initialPosition).normalize();
                seed.userData.velocity.sub(direction.multiplyScalar(0.001));
            }
            
            // Random rotation for dynamic effect
            seed.rotation.x += 0.01;
            seed.rotation.y += 0.01;
        });
        
        // Gentle rotation of plant
        plant.rotation.y += 0.005;
        
        renderer.render(scene, camera);
    }
    
    // Handle window resize
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
    
    // Start animation
    animate();
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});

// Parallax effect for images
window.addEventListener('scroll', () => {
    const scrollPosition = window.pageYOffset;
    
    // About image parallax
    const aboutImage = document.querySelector('.about-image');
    if (aboutImage) {
        aboutImage.style.transform = `translateY(${scrollPosition * 0.03}px)`;
    }
    
    // Research animation parallax
    const labAnimation = document.querySelector('.lab-animation');
    if (labAnimation) {
        labAnimation.style.transform = `translateY(${scrollPosition * 0.02}px)`;
    }
});

// CSS Animation Triggers on Scroll
const animateOnScroll = () => {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementPosition < windowHeight - 100) {
            element.classList.add('animated');
        }
    });
};             

window.addEventListener('scroll', animateOnScroll);

// Initialize GSAP animations for text elements
gsap.registerPlugin(ScrollTrigger);

// Section headers animation
gsap.utils.toArray('.section-header').forEach(header => {
    gsap.from(header, {
        opacity: 0,
        y: 50,
        duration: 1,
        scrollTrigger: {
            trigger: header,
            start: "top 80%",
            toggleActions: "play none none none"
        }
    });
});

// About text animation
const aboutTextElements = document.querySelectorAll('.about-text p');
aboutTextElements.forEach((element, index) => {
    gsap.from(element, {
        opacity: 0,
        x: -50,
        duration: 0.8,
        delay: index * 0.2,
        scrollTrigger: {
            trigger: element,
            start: "top 80%",
            toggleActions: "play none none none"
        }
    });
});

// Research text animation
const researchTextElements = document.querySelectorAll('.research-text p');
researchTextElements.forEach((element, index) => {
    gsap.from(element, {
        opacity: 0,
        x: 50,
        duration: 0.8,
        delay: index * 0.2,
        scrollTrigger: {
            trigger: element,
            start: "top 80%",
            toggleActions: "play none none none"
        }
    });
});

// Product categories animation
gsap.from('.category', {
    opacity: 0,
    y: 50,
    duration: 0.8,
    stagger: 0.3,
    scrollTrigger: {
        trigger: '.product-categories',
        start: "top 80%",
        toggleActions: "play none none none"
    }
});

// Animate hero content
gsap.timeline()
    .from('.hero-content h1', { opacity: 0, y: 30, duration: 1, delay: 0.5 })
    .from('.hero-content p', { opacity: 0, y: 20, duration: 0.8 }, "-=0.4")
    .from('.hero-content .cta-button', { opacity: 0, y: 20, duration: 0.8 }, "-=0.4")
    .from('.scroll-indicator', { opacity: 0, y: 20, duration: 0.8 }, "-=0.4");

// Animate floating seeds in about section
gsap.to('.floating-seed', {
    y: -15,
    duration: 2,
    yoyo: true,
    repeat: -1,
    ease: "power1.inOut",
    stagger: {
        each: 0.5,
        from: "random"
    }
});

// Lab animation effects
gsap.to('.bubbles .bubble', {
    y: -20,
    duration: 2,
    repeat: -1,
    ease: "power1.inOut",
    stagger: {
        each: 0.3,
        from: "start"
    }
});

gsap.to('.plant-growth .plant', {
    y: -5,
    scale: 1.05,
    duration: 3,
    yoyo: true,
    repeat: -1,
    ease: "sine.inOut"
});

// Initialize floating elements for input labels
const inputGroups = document.querySelectorAll('.input-group');
inputGroups.forEach(group => {
    const input = group.querySelector('input, textarea');
    const label = group.querySelector('label');
    
    input.addEventListener('focus', () => {
        label.classList.add('active');
    });
    
    input.addEventListener('blur', () => {
        if (input.value === '') {
            label.classList.remove('active');
        }
    });
    
    // Check if input has value on page load
    if (input.value !== '') {
        label.classList.add('active');
    }
});

// Add hover effects for product categories
const categories = document.querySelectorAll('.category');
categories.forEach(category => {
    category.addEventListener('mouseenter', () => {
        gsap.to(category, {
            y: -10,
            duration: 0.3,
            ease: "power1.out"
        });
    });
    
    category.addEventListener('mouseleave', () => {
        gsap.to(category, {
            y: 0,
            duration: 0.3,
            ease: "power1.out"
        });
    });
});

// View all buttons functionality
const viewAllButtons = document.querySelectorAll('.view-all');
viewAllButtons.forEach(button => {
    button.addEventListener('click', function() {
        const category = this.closest('.category').querySelector('h3').textContent;
        alert(`View all ${category} will be implemented in the full version.`);
    });
});

// Product button functionality
const productButton = document.querySelector('.product-button');
if (productButton) {
    productButton.addEventListener('click', () => {
        alert('Product catalog page will be implemented in the full version.');
    });
}

// Learn more button functionality
const learnMoreButton = document.querySelector('.learn-more');
if (learnMoreButton) {
    learnMoreButton.addEventListener('click', () => {
        alert('Full about page will be implemented in the full version.');
    });
}

// Know more button functionality
const knowMoreButton = document.querySelector('.know-more');
if (knowMoreButton) {
    knowMoreButton.addEventListener('click', () => {
        alert('Full research page will be implemented in the full version.');
    });
}

// Initialize page animations on load
window.addEventListener('load', animateOnScroll);

// Preloader progress bar animation
const progressBar = document.querySelector('.progress');
if (progressBar) {
    let width = 0;
    const interval = setInterval(() => {
        if (width >= 100) {
            clearInterval(interval);
        } else {
            width++;
            progressBar.style.width = width + '%';
        }
    }, 20);
}