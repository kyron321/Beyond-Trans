document.addEventListener('DOMContentLoaded', function() {
    // Find the consent button
    const consentButton = document.querySelector('.consent-button');
    
    if (consentButton) {
        const returnPageId = consentButton.dataset.returnPage;
        
        consentButton.addEventListener('click', function(e) {
            if (returnPageId) {
                // Prevent the default navigation
                e.preventDefault();
                
                // Set a cookie for this specific page (expires in 30 days)
                document.cookie = `page_consent_${returnPageId}=1; max-age=2592000; path=/`;
                
                // Get the site URL from wp_localize_script data or fallback to current origin
                const siteUrl = (window.btVars && window.btVars.siteUrl) ? window.btVars.siteUrl : window.location.origin;
                                
                // Navigate to the original page
                window.location.href = `${siteUrl}?page_id=${returnPageId}`;
            }
        });
    }
});