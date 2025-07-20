/**
 * Therapist Directory functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const therapistDirectory = document.querySelector('.therapist-directory');
    
    if (!therapistDirectory) return;
    
    const filterForm = document.getElementById('therapist-filter-form');
    const resultsContainer = document.getElementById('therapist-results');
    const countrySelect = document.getElementById('country-filter');
    const regionSelect = document.getElementById('region-filter');
    const checkboxes = filterForm.querySelectorAll('.therapist-directory__checkbox');
    const clearButton = document.getElementById('clear-filters');
    const submitButton = document.querySelector('.therapist-directory__submit-btn');
    
    if (submitButton) {
        submitButton.style.display = 'none';
    }
    
    function getFormDataAsParams() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                params.append(key.slice(0, -2) + '[]', value);
            } else {
                params.append(key, value);
            }
        }
        
        return params;
    }
    
    async function updateResults() {
        resultsContainer.classList.add('is-loading');
        resultsContainer.style.opacity = '0.5';
        
        try {
            const params = getFormDataAsParams();
            const url = `${window.location.pathname}?${params.toString()}`;
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const html = await response.text();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            const newResults = tempDiv.querySelector('#therapist-results');
            
            if (newResults) {
                resultsContainer.style.transition = 'opacity 0.3s ease';
                resultsContainer.style.opacity = '0';
                
                setTimeout(() => {
                    resultsContainer.innerHTML = newResults.innerHTML;
                    
                    initializeCardAnimations();
                                        
                    resultsContainer.style.opacity = '1';
                    resultsContainer.classList.remove('is-loading');
                }, 300);
            }
            
            window.history.pushState({}, '', url);
            
        } catch (error) {
            console.error('Error fetching results:', error);
            resultsContainer.classList.remove('is-loading');
            resultsContainer.style.opacity = '1';
        }
    }
    
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
    
    async function handleCountryChange() {
        const countryValue = countrySelect.value;
        const currentRegionValue = regionSelect.value;
        
        if (!countryValue) {
            regionSelect.value = '';
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
        } else {
            regionSelect.disabled = true;
            const originalHTML = regionSelect.innerHTML;
            regionSelect.innerHTML = '<option value="">Loading regions...</option>';
            
            const params = new URLSearchParams({ country: countryValue });
            const url = `${window.location.pathname}?${params.toString()}`;
            
            try {
                const response = await fetch(url);
                const html = await response.text();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                const newRegionSelect = tempDiv.querySelector('#region-filter');
                if (newRegionSelect) {
                    regionSelect.innerHTML = newRegionSelect.innerHTML;
                    regionSelect.disabled = false;
                    
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
        
        updateResults();
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateResults);
    });
    
    countrySelect.addEventListener('change', handleCountryChange);
    regionSelect.addEventListener('change', updateResults);
    
    const initialCards = resultsContainer.querySelectorAll('.therapist-card');
    initialCards.forEach(card => {
        card.classList.add('fade-in');
    });
    
    initializeCardAnimations();
    
    filterForm.addEventListener('submit', function(e) {
        if (typeof fetch !== 'undefined') {
            e.preventDefault();
            updateResults();
        }
    });
    
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            countrySelect.value = '';
            regionSelect.value = '';
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">Region/State/Province</option>';
            
            updateResults();
        });
    }
});