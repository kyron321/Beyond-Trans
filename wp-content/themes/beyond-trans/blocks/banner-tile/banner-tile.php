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
?>

<section class="block banner-tile">
    <?php if ($image): ?>
        <div class="banner-tile__image">
            <img
                src="<?= $image['url']; ?>"
                alt="<?= get_alt_text($image); ?>"
                loading="eager">
            </img>
        </div>
    <?php endif; ?>

    <div class="banner-tile__content">
        <div class="banner-tile__content__inner container">
            <?php if ($title): ?>
                <h1 class="banner-tile__content__title fade-in"><?= $title; ?></h1>
            <?php endif; ?>

            <?php if ($sub_title): ?>
                <p class="banner-tile__content__subtitle fade-in"><?= $sub_title; ?></p>
            <?php endif; ?>

            <div class="banner-tile__content__ctas fade-in">
                <?php if ($cta): ?>
                    <a href="<?= $cta['url']; ?>" class="btn btn-primary"
                        target="<?= $cta['target'] ?: '_blank'; ?>">
                        <?= $cta['title']; ?>
                    </a>
                <?php endif; ?>

                <?php if ($cta_two): ?>
                    <a href="<?= $cta_two['url']; ?>" class="btn btn-white"
                        target="<?= $cta_two['target'] ?: '_blank'; ?>">
                        <?= $cta_two['title']; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>