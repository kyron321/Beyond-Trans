<?php
// Get field values
$title = get_field('title');
$subtitle = get_field('subtitle');
$form_id = get_field('form_id');

$block_classes = ['block', 'form', 'bg-light-yellow'];

// Check if a form has been selected
$has_form = !empty($form_id) && $form_id !== 'select-form';
?>

<section class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        <?php if (!empty($title) || !empty($subtitle)): ?>
            <div class="form-header text-center">
                <?php if (!empty($title)): ?>
                    <h2 class="form-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($subtitle)): ?>
                    <p class="form-subtitle">
                        <?php echo wp_kses_post($subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="form-content">
            <?php if ($has_form): ?>
                <?php echo do_shortcode('[ninja_form id="' . esc_attr($form_id) . '"]'); ?>
            <?php elseif (is_admin()): ?>
                <div class="form-placeholder">
                    <p><?php esc_html_e('Please select a form from the block settings.', 'beyond-trans'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>