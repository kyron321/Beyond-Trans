<?php

/**
 * Block: Disclaimer
 * 
 * Displays disclaimer content with heading, subheading, text and CTAs.
 */

// Get block content
$block_content = get_field('block_content');
$subheading = isset($block_content['subheading']) ? $block_content['subheading'] : '';
$heading = isset($block_content['heading']) ? $block_content['heading'] : '';
$text = isset($block_content['text']) ? $block_content['text'] : '';
$cta_one = isset($block_content['cta_one']) ? $block_content['cta_one'] : '';
$cta_two = isset($block_content['cta_two']) ? $block_content['cta_two'] : '';


$cookie_page_id = '';
if (isset($_GET['return_page']) && !empty($_GET['return_page'])) {
    $cookie_page_id = intval($_GET['return_page']);
} elseif (!empty($cta_one['url'])) {
   
    $cookie_page_id = url_to_postid($cta_one['url']);
    
  
    if (empty($cookie_page_id) && strpos($cta_one['url'], 'therapist-directory') !== false) {
        $cookie_page_id = '592';
    }
}




$block_classes = ['block', 'disclaimer'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}


$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?> bg-primary text-light">
    <div class="container">
        <div class="disclaimer__inner">
            <div class="disclaimer__content">
                <?php if ($subheading): ?>
                    <div class="disclaimer__subheading fade-in">
                        <p><?php echo $subheading; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <div class="disclaimer__heading fade-in">
                        <h2><?php echo $heading; ?></h2>
                    </div>
                <?php endif; ?>

                <?php if ($text): ?>
                    <div class="disclaimer__text fade-in">
                        <?php echo $text; ?>
                    </div>
                <?php endif; ?>

                <?php if ($cta_one || $cta_two): ?>
                    <div class="disclaimer__ctas fade-in">
                        <?php if ($cta_one): ?>
                            <button type="button"
                                class="btn btn-secondary consent-button"
                                style="border: none; cursor: pointer; position: relative; z-index: 10;"
                                data-target-url="<?php echo esc_url($cta_one['url']); ?>"
                                data-page-id="<?php echo $cookie_page_id; ?>">
                                <?php echo esc_html($cta_one['title']); ?>
                            </button>
                        <?php endif; ?>

                        <?php if ($cta_two): ?>
                            <a href="<?php echo esc_url($cta_two['url']); ?>" class="btn btn-white"
                                target="<?php echo $cta_two['target'] ?: '_self'; ?>">
                                <?php echo esc_html($cta_two['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Disclaimer JS loaded');
    

    const consentButton = document.querySelector('.consent-button');
    console.log('Consent button found:', consentButton);
    
    if (consentButton) {
        const targetUrl = consentButton.dataset.targetUrl;
        const pageId = consentButton.dataset.pageId;
        console.log('Target URL:', targetUrl);
        console.log('Page ID for cookie:', pageId);
        
        consentButton.addEventListener('click', function(e) {
            console.log('Button clicked!');
            
            if (targetUrl) {
                
                e.preventDefault();
                
                let cookiePageId = pageId;
                
                
                if (!cookiePageId && targetUrl.includes('therapist-directory')) {
                    cookiePageId = '592';
                    console.log('Using fallback page ID 592 for therapist directory');
                }
                
                if (cookiePageId) {
                    console.log('Setting cookie for page:', cookiePageId);
                    
                   
                    const domain = window.location.hostname;
                    const isSecure = window.location.protocol === 'https:';
                    
                    let cookieString = `page_consent_${cookiePageId}=1; max-age=2592000; path=/`;
                
               
                if (domain) {
                    cookieString += `; domain=${domain}`;
                }
                
              
                if (isSecure) {
                    cookieString += `; secure`;
                }
                
                console.log('Setting cookie:', cookieString);
                
             
                document.cookie = cookieString;
               
                setTimeout(() => {
                    console.log('Redirecting to:', targetUrl);
                    
                   
                    window.location.href = targetUrl;
                }, 100); 
                }
            } else {
                console.log('Missing target URL:', targetUrl);
            }
        });
        
        console.log('Click event listener added');
    } else {
        console.log('No consent button found on page');
    }
});
</script>   