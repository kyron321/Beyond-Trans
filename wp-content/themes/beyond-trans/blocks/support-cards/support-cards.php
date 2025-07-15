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
$block_classes = ['block', 'support-cards'];
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

<section id="support-cards-section" class="<?php echo esc_attr(implode(' ', $block_classes)); ?>">
    <div class="container">
        
        <div class="support-cards__header">
            <?php if ($block_heading): ?>
                <div class="support-cards__heading fade-in">
                    <h3><?php echo $block_heading; ?></h3>
                </div>
            <?php endif; ?>
            <?php if ($block_subheading): ?>
                <div class="support-cards__subheading fade-in">
                    <p><?php echo $block_subheading; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="support-cards__switch-toggle-wrapper fade-in">
        <div class="support-cards__switch-toggle" role="group" aria-label="Toggle group">
            <input type="radio" id="toggle-recovery-support" name="support-cards-toggle" class="switch-toggle__input" value="recovery-support" checked>
            <label for="toggle-recovery-support" class="switch-toggle__label">Recovery Support</label>
            <input type="radio" id="toggle-families" name="support-cards-toggle" class="switch-toggle__input" value="families">
            <label for="toggle-families" class="switch-toggle__label">Family Support</label>
            <span class="switch-toggle__slider"></span>
        </div>
        </div>

        <div class="support-cards__container">
            <?php foreach ($cards as $card_data):
                // Access the card group data
                $card = $card_data['card'];
                $image = isset($card['image']) ? $card['image'] : null;
                $time_tag = isset($card['time_tag']) ? $card['time_tag'] : null;
                $heading = isset($card['heading']) ? $card['heading'] : '';
                $time = isset($card['time']) ? $card['time'] : '';
                $text = isset($card['text']) ? $card['text'] : '';
                $facilitator = isset($card['facilitator']) ? $card['facilitator'] : null;
                $cta = isset($card['cta']) ? $card['cta'] : null;
                $type = isset($card['type']) ? $card['type'] : null;
            ?>
                <div class="support-cards__card fade-in" data-type="<?php echo esc_attr(strtolower($type)); ?>">
                    <?php if ($image): ?>
                        <div class="support-cards__card-image">
                            <?php if ($cta && !empty($cta['url'])): ?>
                                <a href="<?php echo esc_url($cta['url']); ?>" class="support-cards__card-image-link" target="<?php echo esc_attr($cta['target'] ?: '_self'); ?>">
                                    <img
                                        src="<?php echo esc_url($image['url']); ?>"
                                        alt="<?php echo get_alt_text($image); ?>"
                                        loading="<?php echo esc_attr($loading_type); ?>">
                                </a>
                            <?php else: ?>
                                <img
                                    src="<?php echo esc_url($image['url']); ?>"
                                    alt="<?php echo get_alt_text($image); ?>"
                                    loading="<?php echo esc_attr($loading_type); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="support-cards__card-content">
                        <?php if ($time_tag): ?>
                            <div class="support-cards__card-time-tag">
                                <?php echo esc_html($time_tag); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($heading): ?>
                            <h5 class="support-cards__card-heading"><?php echo esc_html($heading); ?></h5>
                        <?php endif; ?>

                        <?php if ($time): ?>
                            <div class="support-cards__card-time">
                                <?php 
                                // Check if time contains commas (multiple time zones)
                                if (strpos($time, ',') !== false) {
                                    // Split by comma and display each timezone on a separate line
                                    $timezones = explode(',', $time);
                                    foreach ($timezones as $timezone) {
                                        echo '<div class="support-cards__card-timezone">' . esc_html(trim($timezone)) . '</div>';
                                    }
                                } else {
                                    // Single time zone, display normally
                                    echo esc_html($time);
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($text): ?>
                            <div class="support-cards__card-text">
                                <?php echo wpautop(esc_html($text)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($type): ?>
                            <div class="support-cards__card-facilitator-label">
                              
                                <?php 
                                // Set facilitator based on card type
                                $facilitator_text = '';
                                if (strtolower($type) === 'recovery-support') {
                                    $facilitator_text = 'Therapist-Facilitated';
                                } elseif (strtolower($type) === 'families') {
                                    $facilitator_text = 'Peer-Facilitated';
                                }
                                echo esc_html($facilitator_text);
                                ?>
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

        <div class="support-cards__footer-cta fade-in">
            <a id="support-cards-cta" href="/apply-for-support/" class="btn btn-primary">Apply For Support</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recoveryToggle = document.getElementById('toggle-recovery-support');
    const familiesToggle = document.getElementById('toggle-families');
    const ctaLink = document.getElementById('support-cards-cta');

    if (!recoveryToggle || !familiesToggle || !ctaLink) {
        return;
    }

    const content = {
        recovery: {
            href: '/apply-for-support/',
            text: 'Apply For Recovery Support'
        },
        family: {
            href: '/apply-for-family-support/',
            text: 'Apply for Family Support'
        }
    };

    function updateButton() {
        if (familiesToggle.checked) {
            ctaLink.href = content.family.href;
            ctaLink.textContent = content.family.text;
        } else {
            ctaLink.href = content.recovery.href;
            ctaLink.textContent = content.recovery.text;
        }
    }

    recoveryToggle.addEventListener('change', updateButton);
    familiesToggle.addEventListener('change', updateButton);

    // Set initial state
    updateButton();
});
</script>