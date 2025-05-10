<?php
// Get acf fields
$image_rows = get_field('images');
$heading = get_field('heading');
$subheading = get_field('subheading');

// Get block position for image loading type
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';

// Default empty alt text
$default_alt = get_bloginfo('name') . ' - Image';

// Set block classes
$block_classes = ['block', 'portrait-image-slider'];
if (!empty($block['className'])) {
    $block_classes[] = $block['className'];
}
if (!empty($block['align'])) {
    $block_classes[] = 'align' . $block['align'];
}

// Create id attribute
$id = 'portrait-image-slider-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Enqueue slick slider assets
wp_enqueue_style('slick');
wp_enqueue_style('slick-theme');
wp_enqueue_script('slick');
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?> bg-light-yellow">
    <div class="container">
        <div class="portrait-image-slider__header">
            <?php if (!empty($heading)): ?>
                <div class="portrait-image-slider__heading fade-in">
                    <h2><?php echo esc_html($heading); ?></h2>
                </div>
            <?php endif; ?>
            <?php if (!empty($subheading)): ?>
                <div class="portrait-image-slider__subheading fade-in">
                    <p><?php echo esc_html($subheading); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="portrait-image-slider__wrapper fade-in" id="<?php echo esc_attr($id); ?>">
            <div class="portrait-image-slider__container">
                <?php
                if ($image_rows && count($image_rows) > 0):
                    foreach ($image_rows as $row):
                        $image = $row['image'];
                        if (!$image) continue;

                        $alt_text = !empty($image['alt']) ? $image['alt'] : $default_alt;
                ?>
                        <div class="portrait-image-slider__slide">
                            <img
                                src="<?php echo esc_url($image['url']); ?>"
                                alt="<?php echo esc_attr($alt_text); ?>"
                                loading="<?php echo esc_attr($loading_type); ?>">
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</section>