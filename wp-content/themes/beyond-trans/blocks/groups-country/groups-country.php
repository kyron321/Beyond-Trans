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
$block_classes = ['block', 'groups-country'];

if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <div class="groups-country__header">
            <div class="groups-country__icon">
                <?php echo get_svg_by_post_id($icon_id); ?>
            </div>
            <?php if ($block_heading) : ?>
                <h2><?php echo esc_html($block_heading); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($block_subheading) : ?>
            <div class="groups-country__subheading fade-in"><?php echo wp_kses_post($block_subheading); ?></div>
        <?php endif; ?>

        <?php if ($single_text) : ?>
            <div class="groups-country__items">
                <?php foreach ($single_text as $item) :
                    $country = $item['country'] ?? null;
                    $group_type = $item['group_type'];
                    $group_name = $item['group_name'];
                    $group_description = $item['group_description'];
                    $link = $item['title_link'];
                    $flag_acf_data = $item['flag_icon'] ?? null;
                    $flag_id = null;
                    if (is_array($flag_acf_data) && !empty($flag_acf_data) && isset($flag_acf_data[0])) {
                        $flag_id = $flag_acf_data[0];
                    }
                ?>
                    <div class="groups-country__item">
                        <div class="groups-country__item-header">
                            <div class="groups-country__flag">
                                <?php if ($flag_id) : ?>
                                    <?php echo get_svg_by_post_id($flag_id); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($country) : ?>
                                <h3><?php echo esc_html($country); ?></h3>
                            <?php endif; ?>
                        </div>

                        <?php if ($group_type) : ?>
                            <p><?php echo esc_html($group_type); ?></p>
                        <?php endif; ?>
                        <?php if ($group_name) : ?>
                            <?php if ($link && $link['url']) : ?>
                                <h3><a href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?: '_self'); ?>"><?php echo esc_html($group_name); ?></a></h3>
                            <?php else : ?>
                                <p><?php echo esc_html($group_name); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($group_description) : ?>
                            <p><?php echo esc_html($group_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>