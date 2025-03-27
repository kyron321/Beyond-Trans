<?php
// Get ACF fields
$image = get_field('image');
$title = get_field('title');
$sub_title = get_field('sub_title');
$cta = get_field('cta');
$cta_two = get_field('cta_two');
?>

<section class="banner-block">
    <?php if ($image): ?>
        <div class="banner-image">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
        </div>
    <?php endif; ?>

    <div class="banner-content">
        <?php if ($title): ?>
            <h1 class="banner-title"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>

        <?php if ($sub_title): ?>
            <p class="banner-sub-title"><?php echo esc_html($sub_title); ?></p>
        <?php endif; ?>

        <div class="banner-cta">
            <?php if ($cta): ?>
                <a href="<?php echo esc_url($cta['url']); ?>" class="btn btn-primary"
                    target="<?php echo esc_attr($cta['target']); ?>">
                    <?php echo esc_html($cta['title']); ?>
                </a>
            <?php endif; ?>

            <?php if ($cta_two): ?>
                <a href="<?php echo esc_url($cta_two['url']); ?>" class="btn btn-secondary"
                    target="<?php echo esc_attr($cta_two['target']); ?>">
                    <?php echo esc_html($cta_two['title']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>