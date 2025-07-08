/**
 * Therapist Directory functionality
 */
(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        fadeDelay: 300,
        loadingClass: 'is-loading',
        visibleClass: 'is-visible',
        fadeClass: 'fade-in',
        observerOptions: {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        }
    };
    
    // State management
    const state = {
        elements: {},
        isInitialized: false,
        searchTimeout: null
    };
    
    /**
     * Cache DOM elements
     */
    function cacheElements() {
        state.elements = {
            directory: document.querySelector('.therapist-directory'),
            filterForm: document.getElementById('therapist-filter-form'),
            resultsContainer: document.getElementById('therapist-results'),
            countrySelect: document.getElementById('country-filter'),
            regionSelect: document.getElementById('region-filter'),
            clearButton: document.getElementById('clear-filters'),
            mobileToggle: document.getElementById('mobile-filter-toggle'),
            mobileClose: document.getElementById('mobile-filter-close'),
            filtersSidebar: document.getElementById('filters-sidebar'),
            mobileViewResults: document.getElementById('mobile-view-results'),
            resultsCount: document.getElementById('results-count'),
            searchInput: document.getElementById('name-search')
        };
        
        // Get dynamic collections
        if (state.elements.filterForm) {
            state.elements.checkboxes = state.elements.filterForm.querySelectorAll('.therapist-directory__checkbox');
            state.elements.filterSelects = state.elements.filterForm.querySelectorAll('.filter-select');
        }
    }
    
    /**
     * Initialize intersection observer for card animations
     */
    function createCardObserver() {
        if (!('IntersectionObserver' in window)) return null;
        
        return new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(CONFIG.visibleClass);
                    observer.unobserve(entry.target);
                }
            });
        }, CONFIG.observerOptions);
    }
    
    /**
     * Initialize therapist cards (animations and click handlers)
     */
    function initializeCards() {
        const cards = state.elements.resultsContainer.querySelectorAll('.therapist-card');
        const observer = createCardObserver();
        
        cards.forEach(card => {
            // Animations
            if (observer) {
                card.classList.add(CONFIG.fadeClass);
                observer.observe(card);
            }
            
            // Click handlers
            const permalink = card.getAttribute('data-permalink');
            if (permalink) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', handleCardClick);
            }
        });
    }
    
    /**
     * Handle card click event
     */
    function handleCardClick(e) {
        // Don't trigger if clicking on an actual link or button
        if (!e.target.closest('a') && !e.target.closest('button')) {
            const permalink = this.getAttribute('data-permalink');
            if (permalink) window.location.href = permalink;
        }
    }
    
    /**
     * Get form data as URL parameters
     */
    function getFormParams() {
        const formData = new FormData(state.elements.filterForm);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            // Handle array values (checkboxes)
            const paramKey = key.endsWith('[]') ? key : key;
            params.append(paramKey, value);
        }
        
        return params;
    }
    
    /**
     * Update results with loading state
     */
    async function updateResults() {
        const { resultsContainer } = state.elements;
        
        // Show loading state
        resultsContainer.classList.add(CONFIG.loadingClass);
        resultsContainer.style.opacity = '0.5';
        
        try {
            const params = getFormParams();
            const url = `${window.location.pathname}?${params.toString()}`;
            
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const html = await response.text();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const newResults = tempDiv.querySelector('#therapist-results');
            
            if (newResults) {
                // Fade out
                resultsContainer.style.transition = 'opacity 0.3s ease';
                resultsContainer.style.opacity = '0';
                
                setTimeout(() => {
                    resultsContainer.innerHTML = newResults.innerHTML;
                    initializeCards();
                    
                    // Update results count
                    updateResultsCount();
                    
                    // Fade in
                    resultsContainer.style.opacity = '1';
                    resultsContainer.classList.remove(CONFIG.loadingClass);
                }, CONFIG.fadeDelay);
            }
            
            // Update URL
            window.history.pushState({}, '', url);
            
        } catch (error) {
            console.error('Error fetching results:', error);
            resultsContainer.classList.remove(CONFIG.loadingClass);
            resultsContainer.style.opacity = '1';
        }
    }
    
    /**
     * Update regions based on selected country
     */
    async function updateRegions() {
        const { countrySelect, regionSelect } = state.elements;
        const countryValue = countrySelect.value;
        
        if (!countryValue) {
            // Reset region select
            regionSelect.value = '';
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
            return;
        }
        
        // Show loading state
        regionSelect.disabled = true;
        regionSelect.innerHTML = '<option value="">Loading regions...</option>';
        
        try {
            const params = new URLSearchParams({ country: countryValue });
            const response = await fetch(`${window.location.pathname}?${params.toString()}`);
            const html = await response.text();
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const newRegionSelect = tempDiv.querySelector('#region-filter');
            if (newRegionSelect) {
                regionSelect.innerHTML = newRegionSelect.innerHTML;
                regionSelect.disabled = false;
            }
        } catch (error) {
            console.error('Error fetching regions:', error);
            regionSelect.innerHTML = '<option value="">Error loading regions</option>';
            regionSelect.disabled = false;
        }
    }
    
    /**
     * Update results count in mobile view
     */
    function updateResultsCount() {
        const cards = state.elements.resultsContainer.querySelectorAll('.therapist-card');
        if (state.elements.resultsCount) {
            state.elements.resultsCount.textContent = cards.length;
        }
    }
    
    /**
     * Clear all filters
     */
    function clearFilters() {
        const { checkboxes, countrySelect, regionSelect } = state.elements;
        
        // Clear checkboxes
        checkboxes.forEach(checkbox => checkbox.checked = false);
        
        // Clear selects
        countrySelect.value = '';
        regionSelect.value = '';
        regionSelect.disabled = true;
        regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
        
        // Clear search input
        if (state.elements.searchInput) {
            state.elements.searchInput.value = '';
        }
        
        updateResults();
    }
    
    /**
     * Mobile filter modal handlers
     */
    const mobileModal = {
        open() {
            state.elements.filtersSidebar.classList.add('is-open');
            document.body.classList.add('modal-open');
        },
        
        close() {
            state.elements.filtersSidebar.classList.remove('is-open');
            document.body.classList.remove('modal-open');
        },
        
        handleOutsideClick(e) {
            if (e.target === state.elements.filtersSidebar) {
                mobileModal.close();
            }
        },
        
        handleEscape(e) {
            if (e.key === 'Escape' && state.elements.filtersSidebar.classList.contains('is-open')) {
                mobileModal.close();
            }
        }
    };
    
    /**
     * Handle search input with debounce
     */
    function handleSearchInput() {
        // Clear existing timeout
        if (state.searchTimeout) {
            clearTimeout(state.searchTimeout);
        }
        
        // Set new timeout for 500ms delay
        state.searchTimeout = setTimeout(() => {
            updateResults();
        }, 500);
    }
    
    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        const { filterForm, checkboxes, countrySelect, regionSelect, clearButton, mobileToggle, mobileClose, filtersSidebar, mobileViewResults, searchInput } = state.elements;
        
        // Form and filter events
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                updateResults();
            });
        }
        
        // Checkbox change events
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateResults);
        });
        
        // Select change events
        if (countrySelect) {
            countrySelect.addEventListener('change', async () => {
                await updateRegions();
                updateResults();
            });
        }
        
        if (regionSelect) {
            regionSelect.addEventListener('change', updateResults);
        }
        
        // Clear button
        if (clearButton) {
            clearButton.addEventListener('click', clearFilters);
        }
        
        // Search input
        if (searchInput) {
            searchInput.addEventListener('input', handleSearchInput);
        }
        
        // Mobile modal events
        if (mobileToggle) {
            mobileToggle.addEventListener('click', mobileModal.open);
        }
        
        if (mobileClose) {
            mobileClose.addEventListener('click', mobileModal.close);
        }
        
        if (filtersSidebar) {
            filtersSidebar.addEventListener('click', mobileModal.handleOutsideClick);
            document.addEventListener('keydown', mobileModal.handleEscape);
        }
        
        // Mobile view results button
        if (mobileViewResults) {
            mobileViewResults.addEventListener('click', () => {
                mobileModal.close();
                // Scroll to results
                state.elements.resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    }
    
    /**
     * Initialize the therapist directory
     */
    function init() {
        cacheElements();
        
        if (!state.elements.directory) return;
        
        if (state.elements.resultsContainer) {
            initializeCards();
        }
        
        attachEventListeners();
        state.isInitialized = true;
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();