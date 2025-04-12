<?php
// Get column one content
$column_one = get_field('column_one');
$subheading = isset($column_one['subheading']) ? $column_one['subheading'] : '';
$heading = isset($column_one['heading']) ? $column_one['heading'] : '';
$text = isset($column_one['text']) ? $column_one['text'] : '';
$key_points = isset($column_one['key_points']) ? $column_one['key_points'] : [];
$cta_one = isset($column_one['cta_one']) ? $column_one['cta_one'] : '';
$cta_two = isset($column_one['cta_two']) ? $column_one['cta_two'] : '';

// Get column two content
$column_two = get_field('column_two');
$image = isset($column_two['image']) ? $column_two['image'] : '';

// Get background color class
$background_color = get_field('block_background_colour');
$bg_class = bt_get_background_color($background_color);
$text_class = bt_get_text_color_for_background($background_color);

// Get flip columns option
$flip_columns = get_field('flip_columns');

// Set block classes
$block_classes = ['block', 'split-content-text-media'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}

// Determine button class for CTA two based on background color
$cta_two_class = 'btn btn-underline';
if ($bg_class === 'bg-light-yellow' || $bg_class === 'bg-grey') {
    $cta_two_class = 'btn btn-underline-black';
}

// Get block position
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="split-content-text-media__inner<?php echo $flip_columns ? ' split-content-text-media__inner--flipped' : ''; ?>">
            <div class="split-content-text-media__col split-content-text-media__col--text">
                <?php if ($subheading): ?>
                    <p class="split-content-text-media__subheading"><?php echo esc_html($subheading); ?></p>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <h2 class="split-content-text-media__heading"><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>

                <?php if ($text): ?>
                    <div class="split-content-text-media__text">
                        <?php echo wpautop(esc_html($text)); ?>
                    </div>
                <?php endif; ?>

                <?php if ($key_points && count($key_points) > 0): ?>
                    <ul class="split-content-text-media__key-points">
                        <?php foreach ($key_points as $key_point):
                            $icon_id = isset($key_point['key_point_icon']) ? $key_point['key_point_icon'] : '';
                            $point_text = isset($key_point['key_point_text']) ? $key_point['key_point_text'] : '';
                        ?>
                            <li class="split-content-text-media__key-point">
                                <?php if ($icon_id): ?>
                                    <div class="split-content-text-media__key-point-icon">
                                        <?php echo get_svg_by_post_id($icon_id); ?>
                                    </div>
                                <?php endif; ?>
                                <span class="split-content-text-media__key-point-text"><?php echo esc_html($point_text); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($cta_one || $cta_two): ?>
                    <div class="split-content-text-media__ctas">
                        <?php if ($cta_one): ?>
                            <a href="<?php echo esc_url($cta_one['url']); ?>"
                                class="btn btn-secondary"
                                target="<?php echo esc_attr($cta_one['target'] ?: '_self'); ?>">
                                <?php echo esc_html($cta_one['title']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($cta_two): ?>
                            <a href="<?php echo esc_url($cta_two['url']); ?>"
                                class="<?php echo esc_attr($cta_two_class); ?>"
                                target="<?php echo esc_attr($cta_two['target'] ?: '_self'); ?>">
                                <?php echo esc_html($cta_two['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="split-content-text-media__col split-content-text-media__col--media">
                <?php if ($image): ?>
                    <div class="split-content-text-media__image">
                        <img src="<?php echo esc_url($image['url']); ?>"
                            alt="<?php echo get_alt_text($image); ?>"
                            loading="<?php echo esc_attr($loading_type); ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>