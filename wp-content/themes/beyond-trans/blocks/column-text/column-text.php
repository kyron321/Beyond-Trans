<?php
// Get ACF fields
$block_heading = get_field('block_heading');
$block_subheading = get_field('block_subheading');
$single_text = get_field('single_text');

// Get background color class
$background_colour = get_field('block_background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'column-text'];

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
        <?php if ($block_heading) : ?>
            <h2><?php echo esc_html($block_heading); ?></h2>
        <?php endif; ?>

        <?php if ($block_subheading) : ?>
            <div class="column-text__subheading fade-in"><?php echo wp_kses_post($block_subheading); ?></div>
        <?php endif; ?>

        <?php if ($single_text) : ?>
            <div class="column-text__items">
                <?php foreach ($single_text as $item) :
                    $title = $item['title'];
                    $subtitle = $item['subtitle'];
                    $link = $item['title_link'];
                ?>
                    <div class="column-text__item fade-in">
                        <?php if ($title) : ?>
                            <?php if ($link && $link['url']) : ?>
                                <h3><a href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?: '_self'); ?>"><?php echo esc_html($title); ?></a></h3>
                            <?php else : ?>
                                <h3><?php echo esc_html($title); ?></h3>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($subtitle) : ?>
                            <p><?php echo esc_html($subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>