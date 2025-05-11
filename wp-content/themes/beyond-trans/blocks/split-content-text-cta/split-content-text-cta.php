<?php
// Get column one content
$column_one = get_field('column_one');
$subheading = isset($column_one['subheading']) ? $column_one['subheading'] : '';
$heading = isset($column_one['heading']) ? $column_one['heading'] : '';
$text = isset($column_one['text']) ? $column_one['text'] : '';

// Get column two content
$column_two = get_field('column_two');
$column_text = isset($column_two['text']) ? $column_two['text'] : '';
$cta_one = isset($column_two['cta_one']) ? $column_two['cta_one'] : '';
$cta_two = isset($column_two['cta_two']) ? $column_two['cta_two'] : '';

// Get flip columns option
$flip_columns = get_field('flip_columns');

// Get background color
$background_colour = get_field('background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'split-content-text-cta'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}

// Determine button class for CTA two based on background color
$cta_two_class = 'btn btn-underline';
if ($bg_class === 'bg-light-yellow' || $bg_class === 'bg-grey') {
    $cta_two_class = 'btn btn-primary';
}

// Get block position
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="split-content-text-cta__inner<?php echo $flip_columns ? ' split-content-text-cta__inner--flipped' : ''; ?>">
            <div class="split-content-text-cta__col split-content-text-cta__col--text">
                <?php if ($subheading): ?>
                    <p class="split-content-text-cta__subheading fade-in"><?php echo esc_html($subheading); ?></p>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <h2 class="split-content-text-cta__heading fade-in"><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>

                <?php if ($text): ?>
                    <div class="split-content-text-cta__text fade-in">
                        <?php echo wpautop(esc_html($text)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="split-content-text-cta__col split-content-text-cta__col--cta">
                <?php if ($column_text): ?>
                    <p class="split-content-text-cta__column-text fade-in">
                        <?php echo esc_html($column_text); ?>
                    </p>
                <?php endif; ?>

                <?php if ($cta_one || $cta_two): ?>
                    <div class="split-content-text-cta__ctas fade-in">
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
        </div>
    </div>
</section>