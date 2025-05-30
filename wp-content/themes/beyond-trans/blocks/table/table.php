<?php

/**
 * Block: Table
 * 
 * This block displays data in a responsive table layout.
 */

// Get the table field
$table = get_field('table');

// Get content fields
$subheading = get_field('subheading');
$heading = get_field('heading');
$text = get_field('text');

// Get background color
$background_colour = get_field('background_colour');
$bg_class = bt_get_background_color($background_colour);
$text_class = bt_get_text_color_for_background($background_colour);

// Set block classes
$block_classes = ['block', 'table-block'];
if (!empty($bg_class)) {
    $block_classes[] = $bg_class;
}
if (!empty($text_class)) {
    $block_classes[] = $text_class;
}

// Calculate column count for the table
$column_count = 0;
if (!empty($table) && is_array($table)) {
    if (!empty($table['header'])) {
        $column_count = count($table['header']);
    } elseif (!empty($table['body']) && !empty($table['body'][0])) {
        $column_count = count($table['body'][0]);
    }
}

// Check if any content exists
if (!empty($subheading) || !empty($heading) || !empty($text) || (!empty($table) && is_array($table))) :
?>

    <section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
        <div class="container">
            <?php if (!empty($subheading) || !empty($heading) || !empty($text)) : ?>
                <div class="table-block__content">
                    <?php if (!empty($subheading)) : ?>
                        <p class="table-block__subheading fade-in"><?php echo esc_html($subheading); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($heading)) : ?>
                        <h2 class="table-block__heading fade-in"><?php echo esc_html($heading); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($text)) : ?>
                        <div class="table-block__text fade-in">
                            <?php echo wpautop(esc_html($text)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($table) && is_array($table)) : ?>
                <div class="table-block__wrapper">
                    <?php if (!empty($table['caption'])) : ?>
                        <h3 class="table-block__caption fade-in"><?php echo esc_html($table['caption']); ?></h3>
                    <?php endif; ?>

                    <div class="table-block__responsive">
                        <table class="table-block__table col-count-<?php echo esc_attr($column_count); ?> fade-in">
                            <?php if (!empty($table['header'])) : ?>
                                <thead>
                                    <tr>
                                        <?php foreach ($table['header'] as $th) : ?>
                                            <th><?php echo wp_kses_post($th['c']); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                            <?php endif; ?>

                            <tbody>
                                <?php foreach ($table['body'] as $tr_index => $tr) : ?>
                                    <tr>
                                        <?php foreach ($tr as $td_index => $td) :
                                            // Get the corresponding header text, strip tags for cleaner data attribute
                                            $header_label = '';
                                            if (!empty($table['header'][$td_index]['c'])) {
                                                $header_label = esc_attr(strip_tags($table['header'][$td_index]['c']));
                                            }
                                        ?>
                                            <td data-label="<?php echo $header_label; ?>"><?php echo wp_kses_post($td['c']); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php else : ?>
    <section class="block table-block table-block--empty">
        <div class="container">
            <p class="table-block__empty-message">Please add content or table data in the block settings.</p>
        </div>
    </section>
<?php endif; ?>