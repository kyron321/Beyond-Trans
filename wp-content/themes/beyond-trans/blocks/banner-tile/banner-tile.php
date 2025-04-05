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

<section class="banner-tile">
    <?php if ($image): ?>
        <div class="banner-tile__image">
            <img
                src="<?= $image['url']; ?>"
                alt="<?= get_alt_text($image); ?>"
                loading="<?= $loading_type; ?>">
            </img>
        </div>
    <?php endif; ?>

    <div class="banner-tile__content">
        <?php if ($title): ?>
            <h1 class="banner-tile__content__title"><?= $title; ?></h1>
        <?php endif; ?>

        <?php if ($sub_title): ?>
            <p class="banner-tile__content__subtitle"><?= $sub_title; ?></p>
        <?php endif; ?>

        <div class="banner-tile__content__ctas">
            <?php if ($cta): ?>
                <a href="<?= $cta['url']; ?>" class="btn btn-primary"
                    target="<?= $cta['target'] ?: '_blank'; ?>">
                    <?= $cta['title']; ?>
                </a>
            <?php endif; ?>

            <?php if ($cta_two): ?>
                <a href="<?= $cta_two['url']; ?>" class="btn btn-secondary"
                    target="<?= $cta_two['target'] ?: '_blank'; ?>">
                    <?= $cta_two['title']; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>