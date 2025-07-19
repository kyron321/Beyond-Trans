/**
 * Therapist Directory functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const therapistDirectory = document.querySelector('.therapist-directory');
    
    if (!therapistDirectory) return;
    
    // Get filter elements
    const filterForm = document.getElementById('therapist-filter-form');
    const resultsContainer = document.getElementById('therapist-results');
    const countrySelect = document.getElementById('country-filter');
    const regionSelect = document.getElementById('region-filter');
    const checkboxes = filterForm.querySelectorAll('.therapist-directory__checkbox');
    const filterSelects = filterForm.querySelectorAll('.filter-select');
    const clearButton = document.getElementById('clear-filters');
    
    // Function to get form data as URL params
    function getFormDataAsParams() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        // Handle array values (checkboxes)
        for (let [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                params.append(key.slice(0, -2) + '[]', value);
            } else {
                params.append(key, value);
            }
        }
        
        return params;
    }
    
    // Function to fetch and update results
    async function updateResults() {
        // Show loading state
        resultsContainer.classList.add('is-loading');
        resultsContainer.style.opacity = '0.5';
        
        try {
            // Get current form data as URL params
            const params = getFormDataAsParams();
            const url = `${window.location.pathname}?${params.toString()}`;
            
            // Fetch the page with filters
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            // Parse the response
            const html = await response.text();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract the results section
            const newResults = tempDiv.querySelector('#therapist-results');
            
            if (newResults) {
                // Fade out current results
                resultsContainer.style.transition = 'opacity 0.3s ease';
                resultsContainer.style.opacity = '0';
                
                setTimeout(() => {
                    // Replace the content
                    resultsContainer.innerHTML = newResults.innerHTML;
                    
                    // Re-initialize animations for new cards
                    initializeCardAnimations();
                    
                    // Re-initialize click handlers for new cards
                    initializeTherapistCardHandlers();
                    
                    // Fade in new results
                    resultsContainer.style.opacity = '1';
                    resultsContainer.classList.remove('is-loading');
                }, 300);
            }
            
            // Update the URL without reloading the page
            window.history.pushState({}, '', url);
            
        } catch (error) {
            console.error('Error fetching results:', error);
            resultsContainer.classList.remove('is-loading');
            resultsContainer.style.opacity = '1';
        }
    }
    
    // Function to initialize card animations
    function initializeCardAnimations() {
        const therapistCards = resultsContainer.querySelectorAll('.therapist-card');
        
        if ('IntersectionObserver' in window) {
            const appearOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };
            
            const appearOnScroll = new IntersectionObserver(
                function(entries, appearOnScroll) {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        
                        entry.target.classList.add('is-visible');
                        appearOnScroll.unobserve(entry.target);
                    });
                }, 
                appearOptions
            );
            
            therapistCards.forEach(card => {
                card.classList.add('fade-in');
                appearOnScroll.observe(card);
            });
        }
    }
    
    // Function to initialize therapist card click handlers
    function initializeTherapistCardHandlers() {
        const therapistCards = resultsContainer.querySelectorAll('.therapist-card[data-permalink]');
        
        therapistCards.forEach(function(card) {
            // Skip if already initialized
            if (card.dataset.clickInitialized) {
                return;
            }
            
            // Mark as initialized
            card.dataset.clickInitialized = 'true';
            
            // Add pointer cursor style
            card.style.cursor = 'pointer';
            
            // Add click handler
            card.addEventListener('click', function(e) {
                // Don't navigate if clicking on a link or button inside the card
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                
                const permalink = this.getAttribute('data-permalink');
                if (permalink) {
                    window.location.href = permalink;
                }
            });
            
            // Add hover effect
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Function to handle country change and update regions
    async function handleCountryChange() {
        const countryValue = countrySelect.value;
        const currentRegionValue = regionSelect.value;
        
        if (!countryValue) {
            // If country is cleared, disable region select
            regionSelect.value = '';
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
        } else {
            // Show loading state on region select
            regionSelect.disabled = true;
            const originalHTML = regionSelect.innerHTML;
            regionSelect.innerHTML = '<option value="">Loading regions...</option>';
            
            // Fetch the page with just the country filter to get regions
            const params = new URLSearchParams({ country: countryValue });
            const url = `${window.location.pathname}?${params.toString()}`;
            
            try {
                const response = await fetch(url);
                const html = await response.text();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extract the region select options
                const newRegionSelect = tempDiv.querySelector('#region-filter');
                if (newRegionSelect) {
                    regionSelect.innerHTML = newRegionSelect.innerHTML;
                    regionSelect.disabled = false;
                    
                    // Try to restore the previously selected region if it still exists
                    if (currentRegionValue) {
                        const optionExists = Array.from(regionSelect.options).some(
                            option => option.value === currentRegionValue
                        );
                        if (optionExists) {
                            regionSelect.value = currentRegionValue;
                        }
                    }
                } else {
                    regionSelect.innerHTML = originalHTML;
                    regionSelect.disabled = false;
                }
            } catch (error) {
                console.error('Error fetching regions:', error);
                regionSelect.innerHTML = '<option value="">Error loading regions</option>';
                regionSelect.disabled = false;
            }
        }
        
        // Update results after country change
        updateResults();
    }
    
    // Add event listeners for checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateResults);
    });
    
    // Add event listeners for select elements
    countrySelect.addEventListener('change', handleCountryChange);
    regionSelect.addEventListener('change', updateResults);
    
    // Initialize card animations and click handlers on page load
    initializeCardAnimations();
    initializeTherapistCardHandlers();
    
    // Prevent form submission on Enter key
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        updateResults();
    });
    
    // Handle clear filters button
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            // Uncheck all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset select elements
            countrySelect.value = '';
            regionSelect.value = '';
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
            
            // Update results
            updateResults();
        });
    }
});