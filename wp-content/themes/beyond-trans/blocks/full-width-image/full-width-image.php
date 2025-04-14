<?php

/**
 * Block: Full Width Image
 * 
 * Displays a full-width responsive image.
 */

// Get image field
$image = get_field('image');

// Get background color
$background_colour = get_field('background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'full-width-image'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}

// Get block position for image loading type
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';
?>


<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="full-width-image__wrapper">
            <?php if ($image): ?>
                <img
                    class="full-width-image__image"
                    src="<?php echo esc_url($image['url']); ?>"
                    alt="<?php echo get_alt_text($image); ?>"
                    width="<?php echo esc_attr($image['width']); ?>"
                    height="<?php echo esc_attr($image['height']); ?>"
                    loading="<?php echo esc_attr($loading_type); ?>" />
            <?php endif; ?>
        </div>
    </div>
</section>