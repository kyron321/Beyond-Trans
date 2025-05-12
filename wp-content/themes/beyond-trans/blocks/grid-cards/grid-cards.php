<?php
// Get ACF fields
$block_heading = get_field('block_heading');
$block_subheading = get_field('block_subheading');
$cards = get_field('cards');

// Get background color class
$background_colour = get_field('block_background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Determine button class based on background color
$cta_class = 'btn btn-underline';
if ($bg_class === 'bg-light-yellow' || $bg_class === 'bg-grey') {
    $cta_class = 'btn btn-underline-black';
}

// Set block classes
$block_classes = ['block', 'grid-cards'];
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

// Check if we have cards to display
if (!$cards) {
    return;
}
?>

<section id="grid-cards-section" class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="grid-cards__header">
            <?php if ($block_heading): ?>
                <div class="grid-cards__heading fade-in">
                    <h3><?php echo $block_heading; ?></h3>
                </div>
            <?php endif; ?>
            <?php if ($block_subheading): ?>
                <div class="grid-cards__subheading fade-in">
                    <p><?php echo $block_subheading; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid-cards__container">
            <?php foreach ($cards as $card_data):
                // Access the card group data
                $card = $card_data['card'];
                $image = isset($card['image']) ? $card['image'] : null;
                $heading = isset($card['heading']) ? $card['heading'] : '';
                $text = isset($card['text']) ? $card['text'] : '';
                $cta = isset($card['cta']) ? $card['cta'] : null;
            ?>
                <div class="grid-cards__card fade-in">
                    <?php if ($image): ?>
                        <div class="grid-cards__card-image">
                            <a href="<?php echo esc_url($cta['url']); ?>" class="grid-cards__card-image-link" target="<?php echo esc_attr($cta['target'] ?: '_self'); ?>">
                                <img
                                    src="<?php echo esc_url($image['url']); ?>"
                                    alt="<?php echo get_alt_text($image); ?>"
                                    loading="<?php echo esc_attr($loading_type); ?>">
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="grid-cards__card-content">
                        <?php if ($heading): ?>
                            <h5 class="grid-cards__card-heading"><?php echo esc_html($heading); ?></h5>
                        <?php endif; ?>

                        <?php if ($text): ?>
                            <div class="grid-cards__card-text">
                                <?php echo wpautop(esc_html($text)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($cta): ?>
                            <a href="<?php echo esc_url($cta['url']); ?>"
                                class="<?php echo esc_attr($cta_class); ?>"
                                target="<?php echo esc_attr($cta['target'] ?: '_self'); ?>">
                                <?php echo esc_html($cta['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>