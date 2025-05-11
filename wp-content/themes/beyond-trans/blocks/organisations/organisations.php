<?php
// Get ACF fields
$block_heading = get_field('block_heading');
$block_subheading = get_field('block_subheading');
$single_text = get_field('single_text');
$block_icon = get_field('block_icon');
$icon_id = $block_icon[0];

// Get background color class
$background_colour = get_field('block_background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'organisations'];

if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="organisations__header fade-in">
            <div class="organisations__icon">
                <?php echo get_svg_by_post_id($icon_id); ?>
            </div>
            <?php if ($block_heading) : ?>
                <h2><?php echo esc_html($block_heading); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($block_subheading) : ?>
            <div class="organisations__subheading fade-in"><?php echo wp_kses_post($block_subheading); ?></div>
        <?php endif; ?>

        <?php if ($single_text) : ?>
            <div class="organisations__items">
                <?php foreach ($single_text as $item) :
                    $title = $item['title'];
                    $subtitle = $item['subtitle'];
                    $link = $item['title_link'];
                ?>
                    <div class="organisations__item">
                        <?php if ($title) : ?>
                            <?php if ($link && $link['url']) : ?>
                                <h3 class="fade-in"><a href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?: '_self'); ?>"><?php echo esc_html($title); ?></a></h3>
                            <?php else : ?>
                                <h3 class="fade-in"><?php echo esc_html($title); ?></h3>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($subtitle) : ?>
                            <p class="fade-in"><?php echo esc_html($subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>