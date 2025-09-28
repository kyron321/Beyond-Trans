/**
 * Simple Vertical Timeline Navigation
 * Basic year navigation and scroll functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize year navigation
    initYearNavigation();
    
    // Initialize scroll progress
    initScrollProgress();
    
    // Initialize back to top button
    initBackToTop();
});

function initYearNavigation() {
    const yearButtons = document.querySelectorAll('.timeline__year-btn');
    const timelineItems = document.querySelectorAll('.timeline__item');
    
    yearButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const year = this.getAttribute('data-year');
            
            // Update active button
            yearButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Find first timeline item with this year
            let targetItem = null;
            for (let item of timelineItems) {
                if (item.getAttribute('data-year') === year) {
                    targetItem = item;
                    break;
                }
            }
            
            // Scroll to the item
            if (targetItem) {
                const rect = targetItem.getBoundingClientRect();
                const scrollTop = window.pageYOffset + rect.top - (window.innerHeight / 2) + (rect.height / 2);
                
                window.scrollTo({
                    top: scrollTop,
                    behavior: 'smooth'
                });
                
            }
        });
    });
}

function initScrollProgress() {
    const progressBar = document.querySelector('.timeline__line-progress');
    if (!progressBar) return;
    
    function updateProgress() {
        const timeline = document.querySelector('.timeline');
        if (!timeline) return;
        
        const timelineHeight = timeline.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrolled = window.pageYOffset;
        const timelineTop = timeline.offsetTop;
        
        const start = timelineTop - windowHeight;
        const end = timelineTop + timelineHeight;
        const progress = Math.max(0, Math.min(1, (scrolled - start) / (end - start)));
        
        progressBar.style.height = `${progress * 100}%`;
    }
    
    window.addEventListener('scroll', updateProgress);
    updateProgress();
}

// Simple fade-in animation
function initFadeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const fadeElements = document.querySelectorAll('.timeline__card');
    fadeElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Initialize fade animations
document.addEventListener('DOMContentLoaded', function() {
    initFadeAnimations();
});

function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    const timeline = document.querySelector('.timeline');
    if (!backToTopBtn || !timeline) return;
    
    // Get timeline position
    const timelineRect = timeline.getBoundingClientRect();
    const timelineTop = timeline.offsetTop;
    
    // Show/hide button based on scroll position relative to timeline
    function toggleBackToTop() {
        const scrollTop = window.pageYOffset;
        const timelineStart = timelineTop;
        const timelineEnd = timelineTop + timeline.offsetHeight;
        
        // Show button when scrolled past timeline start and within timeline
        if (scrollTop > timelineStart + 200 && scrollTop < timelineEnd - 200) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    }
    
    // Scroll to top of timeline when clicked
    backToTopBtn.addEventListener('click', function() {
        timeline.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
    
    // Listen for scroll events
    window.addEventListener('scroll', toggleBackToTop);
    
    // Initial check
    toggleBackToTop();
}