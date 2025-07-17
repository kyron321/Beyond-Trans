<?php
// Get ACF fields
$block_heading = get_field('block_heading');
$block_subheading = get_field('block_subheading');
$single_text = get_field('single_text');

// Process subheading to add target="_blank" to any links
if ($block_subheading) {
    $block_subheading = preg_replace('/<a\s+([^>]*?)>/i', '<a $1 target="_blank">', $block_subheading);
}

// Get background color class
$background_colour = get_field('block_background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'youtube-videos', 'grid-layout'];

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

<section class="<?php echo implode(' ', $block_classes); ?>">
    <div class="container">
        <?php if ($block_heading) : ?>
            <h2 class="fade-in"><?php echo $block_heading; ?></h2>
        <?php endif; ?>

        <?php if ($block_subheading) : ?>
            <div class="youtube-videos__subheading fade-in"><?php echo $block_subheading; ?></div>
        <?php endif; ?>

        <?php if ($single_text) : ?>
            <div class="youtube-videos__items">
                <?php foreach ($single_text as $item) :
                    $url = $item['url'];
                    $video_label = $item['video_label'];
                ?>
                    <div class="youtube-videos__item fade-in">
                       <iframe src="https://www.youtube.com/embed/<?php echo $url; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="<?php echo $loading_type; ?>"></iframe>
                       <div class="youtube-videos__item-label"><?php echo $video_label; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>