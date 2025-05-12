<?php
// Get ACF fields
$image = get_field('image');
$title = get_field('title');
$sub_title = get_field('sub_title');
$cta = get_field('cta');
$cta_two = get_field('cta_two');

// Get block position 
$position = isset($block['attrs']['position']) ? $block['attrs']['position'] : null;
$is_first_block = ($position === 0);
$loading_type = $is_first_block ? 'eager' : 'lazy';
?>

<section class="block banner">
    <?php if ($image): ?>
        <div class="banner__image">
            <img
                src="<?= $image['url']; ?>"
                alt="<?= get_alt_text($image); ?>"
                loading="<?= $loading_type; ?>">
            </img>
        </div>
    <?php endif; ?>

    <div class="banner__content container">
        <?php if ($title): ?>
            <h1 class="banner__content__title fade-in"><?= $title; ?></h1>
        <?php endif; ?>

        <?php if ($sub_title): ?>
            <p class="banner__content__subtitle fade-in"><?= $sub_title; ?></p>
        <?php endif; ?>

        <div class="banner__content__ctas fade-in">
            <?php if ($cta): ?>
                <a href="#grid-cards-section" class="btn btn-secondary"
   <?= $cta['target'] ? 'target="' . esc_attr($cta['target']) . '"' : ''; ?>>
   <?= $cta['title']; ?>
</a>
            <?php endif; ?>

            <?php if ($cta_two): ?>
                <a href="<?= $cta_two['url']; ?>" class="btn btn-white"
                <?= $cta_two['target'] ? 'target="' . esc_attr($cta_two['target']) . '"' : ''; ?>>
                <?= $cta_two['title']; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>