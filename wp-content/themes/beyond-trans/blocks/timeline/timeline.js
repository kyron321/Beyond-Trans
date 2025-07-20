/**
 * Interactive Timeline Block JavaScript
 * Handles horizontal scrolling, mobile interactions, progress tracking, and accessibility
 */

function initializeTimeline(timelineElement) {
    const timeline = new TimelineController(timelineElement);
    timeline.init();
}

class TimelineController {
    constructor(element) {
        this.timeline = element;
        this.container = element.querySelector('.timeline__container');
        this.track = element.querySelector('.timeline__track');
        this.items = element.querySelectorAll('.timeline__item');
        this.prevBtn = element.querySelector('.timeline__nav--prev');
        this.nextBtn = element.querySelector('.timeline__nav--next');
        this.progressFill = element.querySelector('.timeline__progress__fill');
        this.progressCurrent = element.querySelector('.timeline__progress__current');
        this.progressTotal = element.querySelector('.timeline__progress__total');
        this.filters = element.querySelectorAll('.timeline__filter');
        
        // State
        this.currentIndex = 0;
        this.totalItems = this.items.length;
        this.isScrolling = false;
        this.autoScrollInterval = null;
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.isDragging = false;
        this.itemWidth = 344; // 320px + 24px gap
        this.isMobile = window.innerWidth <= 768;
        
        // Configuration
        this.autoScroll = this.container.dataset.autoScroll === 'true';
        this.scrollSpeed = parseInt(this.container.dataset.scrollSpeed) || 3000;
        
        // Bind methods
        this.handleResize = this.handleResize.bind(this);
        this.handleKeydown = this.handleKeydown.bind(this);
        this.handlePrevClick = this.handlePrevClick.bind(this);
        this.handleNextClick = this.handleNextClick.bind(this);
        this.handleFilterClick = this.handleFilterClick.bind(this);
        this.handleTouchStart = this.handleTouchStart.bind(this);
        this.handleTouchMove = this.handleTouchMove.bind(this);
        this.handleTouchEnd = this.handleTouchEnd.bind(this);
        this.handleToggleClick = this.handleToggleClick.bind(this);
    }
    
    init() {
        if (!this.items.length) return;
        
        this.setupEventListeners();
        this.updateProgressTotal();
        this.updateNavigation();
        this.updateProgress();
        this.setupMobileToggles();
        this.handleResize();
        
        if (this.autoScroll && !this.isMobile) {
            this.startAutoScroll();
        }
        
        // Initialize fade-in animations
        this.initializeFadeInAnimations();
    }
    
    setupEventListeners() {
        // Navigation buttons
        if (this.prevBtn) this.prevBtn.addEventListener('click', this.handlePrevClick);
        if (this.nextBtn) this.nextBtn.addEventListener('click', this.handleNextClick);
        
        // Filter buttons
        this.filters.forEach(filter => {
            filter.addEventListener('click', this.handleFilterClick);
        });
        
        // Touch events for mobile
        if (this.container) {
            this.container.addEventListener('touchstart', this.handleTouchStart, { passive: false });
            this.container.addEventListener('touchmove', this.handleTouchMove, { passive: false });
            this.container.addEventListener('touchend', this.handleTouchEnd);
        }
        
        // Mouse events for desktop dragging
        if (this.container && !this.isMobile) {
            this.container.addEventListener('mousedown', this.handleMouseDown.bind(this));
            this.container.addEventListener('mousemove', this.handleMouseMove.bind(this));
            this.container.addEventListener('mouseup', this.handleMouseUp.bind(this));
            this.container.addEventListener('mouseleave', this.handleMouseUp.bind(this));
        }
        
        // Keyboard navigation
        this.timeline.addEventListener('keydown', this.handleKeydown);
        this.timeline.setAttribute('tabindex', '0');
        
        // Window resize
        window.addEventListener('resize', this.handleResize);
        
        // Auto-scroll pause on hover/focus
        if (this.timeline) {
            this.timeline.addEventListener('mouseenter', () => this.pauseAutoScroll());
            this.timeline.addEventListener('mouseleave', () => this.resumeAutoScroll());
            this.timeline.addEventListener('focusin', () => this.pauseAutoScroll());
            this.timeline.addEventListener('focusout', () => this.resumeAutoScroll());
        }
    }
    
    handlePrevClick() {
        this.goToPrevious();
    }
    
    handleNextClick() {
        this.goToNext();
    }
    
    handleFilterClick(event) {
        const button = event.target;
        const year = button.dataset.year;
        
        // Update active filter
        this.filters.forEach(f => f.classList.remove('timeline__filter--active'));
        button.classList.add('timeline__filter--active');
        
        // Filter items
        this.filterItemsByYear(year);
    }
    
    handleKeydown(event) {
        switch (event.key) {
            case 'ArrowLeft':
                event.preventDefault();
                this.goToPrevious();
                break;
            case 'ArrowRight':
                event.preventDefault();
                this.goToNext();
                break;
            case 'Home':
                event.preventDefault();
                this.goToIndex(0);
                break;
            case 'End':
                event.preventDefault();
                this.goToIndex(this.totalItems - 1);
                break;
        }
    }
    
    handleTouchStart(event) {
        if (this.isMobile) return; // Let mobile use vertical scrolling
        
        this.touchStartX = event.touches[0].clientX;
        this.touchStartY = event.touches[0].clientY;
        this.isDragging = true;
        this.pauseAutoScroll();
    }
    
    handleTouchMove(event) {
        if (!this.isDragging || this.isMobile) return;
        
        const touchX = event.touches[0].clientX;
        const touchY = event.touches[0].clientY;
        const deltaX = this.touchStartX - touchX;
        const deltaY = this.touchStartY - touchY;
        
        // Prevent vertical scrolling if horizontal movement is significant
        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            event.preventDefault();
        }
    }
    
    handleTouchEnd(event) {
        if (!this.isDragging || this.isMobile) return;
        
        const touchEndX = event.changedTouches[0].clientX;
        const deltaX = this.touchStartX - touchEndX;
        const threshold = 50;
        
        if (Math.abs(deltaX) > threshold) {
            if (deltaX > 0) {
                this.goToNext();
            } else {
                this.goToPrevious();
            }
        }
        
        this.isDragging = false;
        this.resumeAutoScroll();
    }
    
    handleMouseDown(event) {
        this.isDragging = true;
        this.touchStartX = event.clientX;
        this.pauseAutoScroll();
        event.preventDefault();
    }
    
    handleMouseMove(event) {
        if (!this.isDragging) return;
        event.preventDefault();
    }
    
    handleMouseUp(event) {
        if (!this.isDragging) return;
        
        const deltaX = this.touchStartX - event.clientX;
        const threshold = 50;
        
        if (Math.abs(deltaX) > threshold) {
            if (deltaX > 0) {
                this.goToNext();
            } else {
                this.goToPrevious();
            }
        }
        
        this.isDragging = false;
        this.resumeAutoScroll();
    }
    
    handleToggleClick(event) {
        const toggle = event.target.closest('.timeline__item__toggle');
        const item = toggle.closest('.timeline__item');
        const details = item.querySelector('.timeline__item__details');
        
        toggle.classList.toggle('expanded');
        details.classList.toggle('expanded');
        
        // Update toggle button icon
        const svg = toggle.querySelector('svg');
        if (toggle.classList.contains('expanded')) {
            toggle.setAttribute('aria-label', 'Collapse details');
        } else {
            toggle.setAttribute('aria-label', 'Expand details');
        }
    }
    
    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        if (wasMobile !== this.isMobile) {
            this.updateLayout();
        }
        
        if (!this.isMobile) {
            this.updateItemWidth();
            this.updateScrollPosition();
        }
    }
    
    goToPrevious() {
        if (this.currentIndex > 0) {
            this.goToIndex(this.currentIndex - 1);
        }
    }
    
    goToNext() {
        if (this.currentIndex < this.totalItems - 1) {
            this.goToIndex(this.currentIndex + 1);
        }
    }
    
    goToIndex(index) {
        if (index < 0 || index >= this.totalItems || this.isScrolling) return;
        
        this.currentIndex = index;
        this.updateScrollPosition();
        this.updateNavigation();
        this.updateProgress();
        this.announceCurrentItem();
    }
    
    updateScrollPosition() {
        if (this.isMobile || !this.track) return;
        
        this.isScrolling = true;
        const scrollPosition = -this.currentIndex * this.itemWidth;
        
        this.track.style.transform = `translateX(${scrollPosition}px)`;
        
        // Reset scrolling flag after animation
        setTimeout(() => {
            this.isScrolling = false;
        }, 500);
    }
    
    updateNavigation() {
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentIndex === 0;
        }
        
        if (this.nextBtn) {
            this.nextBtn.disabled = this.currentIndex === this.totalItems - 1;
        }
    }
    
    updateProgress() {
        const progress = ((this.currentIndex + 1) / this.totalItems) * 100;
        
        if (this.progressFill) {
            this.progressFill.style.width = `${progress}%`;
        }
        
        if (this.progressCurrent) {
            this.progressCurrent.textContent = this.currentIndex + 1;
        }
    }
    
    updateProgressTotal() {
        if (this.progressTotal) {
            this.progressTotal.textContent = this.totalItems;
        }
    }
    
    updateItemWidth() {
        if (this.items.length > 0 && !this.isMobile) {
            const firstItem = this.items[0];
            const itemRect = firstItem.getBoundingClientRect();
            const gap = 24; // From CSS
            this.itemWidth = itemRect.width + gap;
        }
    }
    
    filterItemsByYear(year) {
        this.items.forEach(item => {
            const itemYear = item.dataset.year;
            const shouldShow = year === 'all' || itemYear === year;
            
            item.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Update visible items and reset position
        const visibleItems = Array.from(this.items).filter(item => 
            item.style.display !== 'none'
        );
        
        this.totalItems = visibleItems.length;
        this.currentIndex = 0;
        this.updateScrollPosition();
        this.updateNavigation();
        this.updateProgress();
        this.updateProgressTotal();
    }
    
    setupMobileToggles() {
        const toggleButtons = this.timeline.querySelectorAll('.timeline__item__toggle');
        
        toggleButtons.forEach(toggle => {
            toggle.addEventListener('click', this.handleToggleClick);
        });
    }
    
    startAutoScroll() {
        if (!this.autoScroll || this.isMobile) return;
        
        this.autoScrollInterval = setInterval(() => {
            if (this.currentIndex < this.totalItems - 1) {
                this.goToNext();
            } else {
                this.goToIndex(0); // Loop back to start
            }
        }, this.scrollSpeed);
    }
    
    pauseAutoScroll() {
        if (this.autoScrollInterval) {
            clearInterval(this.autoScrollInterval);
        }
    }
    
    resumeAutoScroll() {
        if (this.autoScroll && !this.isMobile) {
            this.startAutoScroll();
        }
    }
    
    updateLayout() {
        if (this.isMobile) {
            // Reset desktop transforms for mobile
            if (this.track) {
                this.track.style.transform = 'none';
            }
            this.pauseAutoScroll();
        } else {
            // Re-enable desktop features
            this.updateItemWidth();
            this.updateScrollPosition();
            this.resumeAutoScroll();
        }
    }
    
    announceCurrentItem() {
        const currentItem = this.items[this.currentIndex];
        if (!currentItem) return;
        
        const titleElement = currentItem.querySelector('.timeline__item__title');
        const yearElement = currentItem.querySelector('.timeline__item__year');
        const title = titleElement ? titleElement.textContent : null;
        const year = yearElement ? yearElement.textContent : null;
        
        if (title && year) {
            const announcement = `${title}, ${year}. Item ${this.currentIndex + 1} of ${this.totalItems}`;
            this.announceToScreenReader(announcement);
        }
    }
    
    announceToScreenReader(message) {
        // Create temporary element for screen reader announcement
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.style.position = 'absolute';
        announcement.style.left = '-10000px';
        announcement.style.width = '1px';
        announcement.style.height = '1px';
        announcement.style.overflow = 'hidden';
        
        document.body.appendChild(announcement);
        announcement.textContent = message;
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }
    
    initializeFadeInAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);
        
        const fadeElements = this.timeline.querySelectorAll('.fade-in');
        fadeElements.forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    }
    
    destroy() {
        // Clean up event listeners
        window.removeEventListener('resize', this.handleResize);
        this.pauseAutoScroll();
        
        // Remove all event listeners
        if (this.prevBtn) this.prevBtn.removeEventListener('click', this.handlePrevClick);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this.handleNextClick);
        
        this.filters.forEach(filter => {
            filter.removeEventListener('click', this.handleFilterClick);
        });
        
        const toggleButtons = this.timeline.querySelectorAll('.timeline__item__toggle');
        toggleButtons.forEach(toggle => {
            toggle.removeEventListener('click', this.handleToggleClick);
        });
    }
}

// Initialize all timelines on page load
document.addEventListener('DOMContentLoaded', function() {
    const timelines = document.querySelectorAll('.timeline');
    timelines.forEach(timeline => {
        initializeTimeline(timeline);
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initializeTimeline, TimelineController };
} 