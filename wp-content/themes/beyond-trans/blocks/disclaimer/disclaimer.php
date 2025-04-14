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

// Set block classes
$block_classes = ['block', 'disclaimer'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}

// Get block position
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);

// Generate a unique ID for the cta_one button
$cta_one_id = 'cta-cookie-' . uniqid();

// Add the script to set cookie when CTA one is clicked
if ($cta_one) {
    add_action('wp_footer', function () use ($cta_one_id, $cta_one) {
?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctaButton = document.getElementById('<?php echo esc_js($cta_one_id); ?>');
                if (ctaButton) {
                    ctaButton.addEventListener('click', function(e) {
                        // Set cookie that expires in 30 days
                        const expiryDate = new Date();
                        expiryDate.setDate(expiryDate.getDate() + 30);
                        document.cookie = "disclaimer_accepted=1; expires=" + expiryDate.toUTCString() + "; path=/; SameSite=Strict";
                    });
                }
            });
        </script>
<?php
    });
}
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?> bg-primary text-light">
    <div class="container">
        <div class="disclaimer__inner">
            <div class="disclaimer__content">
                <?php if ($subheading): ?>
                    <div class="disclaimer__subheading">
                        <p><?php echo $subheading; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <div class="disclaimer__heading">
                        <h2><?php echo $heading; ?></h2>
                    </div>
                <?php endif; ?>

                <?php if ($text): ?>
                    <div class="disclaimer__text">
                        <?php echo $text; ?>
                    </div>
                <?php endif; ?>

                <?php if ($cta_one || $cta_two): ?>
                    <div class="disclaimer__ctas">
                        <?php if ($cta_one): ?>
                            <a href="<?php echo esc_url($cta_one['url']); ?>" class="btn btn-secondary"
                                target="<?php echo $cta_one['target'] ?: '_self'; ?>"
                                id="<?php echo esc_attr($cta_one_id); ?>">
                                <?php echo esc_html($cta_one['title']); ?>
                            </a>
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