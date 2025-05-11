<?php
// Get ACF fields
$image = get_field('image');
$title = get_field('title');
$sub_title = get_field('sub_title');

// Get block position
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';
?>

<section class="block banner-internal">
    <?php if ($image): ?>
        <div class="banner-internal__image">
            <img
                src="<?= $image['url']; ?>"
                alt="<?= get_alt_text($image); ?>"
                loading="<?= $loading_type; ?>">
        </div>
    <?php endif; ?>

    <div class="banner-internal__content container">
        <?php if ($title): ?>
            <h1 class="banner-internal__content__title fade-in"><?= $title; ?></h1>
        <?php endif; ?>

        <?php if ($sub_title): ?>
            <p class="banner-internal__content__subtitle fade-in"><?= $sub_title; ?></p>
        <?php endif; ?>
    </div>
</section>