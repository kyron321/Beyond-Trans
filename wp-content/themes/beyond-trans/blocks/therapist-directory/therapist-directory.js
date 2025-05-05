/**
 * Therapist Directory functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    const therapistDirectory = document.querySelector('.therapist-directory');
    
    if (!therapistDirectory) return;
    
    // Add smooth scroll to directory when filter is clicked
    const filterButtons = therapistDirectory.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        // Save the href for later navigation
        const targetHref = this.getAttribute('href');
        
        // Optionally, add loading state
        const therapistGrid = therapistDirectory.querySelector('.therapist-directory__grid');
        if (therapistGrid) {
          therapistGrid.classList.add('is-loading');
        }
        
        // Apply active state immediately for better UX
        filterButtons.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        // Optional: scroll to the directory section smoothly before loading new results
        e.preventDefault();
        
        const scrollTarget = therapistDirectory.getBoundingClientRect().top + window.pageYOffset - 100;
        
        window.scrollTo({
          top: scrollTarget,
          behavior: 'smooth'
        });
        
        // Navigate to the filter URL after a short delay
        setTimeout(() => {
          window.location.href = targetHref;
        }, 500);
      });
    });
    
    // Optional: Add animation when cards appear
    const therapistCards = therapistDirectory.querySelectorAll('.therapist-card');
    
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

    // Get filter elements
    const countrySelect = document.getElementById('country-filter');
    const regionContainer = document.getElementById('region-filter-container');
    const regionSelect = document.getElementById('region-filter');

    // Set up automatic form submission on select change
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.id === 'country-filter' && this.value === '') {
                // If country is cleared, also clear region
                regionSelect.value = '';
                regionSelect.disabled = true;
                regionContainer.style.display = 'none';
            }

            // Submit the form on any select change
            this.form.submit();
        });
      });
  });