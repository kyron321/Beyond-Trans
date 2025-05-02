<?php
$website_logo = get_field('website_logo', 'option');
$cta_1 = get_field('cta_1', 'option');
$cta_2 = get_field('cta_2', 'option');
?>

<nav class="main-nav">
    <div class="main-nav container">
        <div class="main-nav__logo">
            <a href="/">
                <?php $svg_code = $website_logo ? get_svg_by_post_id($website_logo) : ''; ?>
                <?php if ($svg_code): ?>
                    <?= $svg_code; ?>
                <?php endif; ?>
            </a>
        </div>
        <button class="main-nav__toggle" aria-label="Toggle Navigation" aria-expanded="false">
            <span class="main-nav__toggle-icon"></span>
        </button>
        <div class="main-nav__menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header-menu',
                'items_wrap' => '<ul class="main-nav__menu__list">%3$s</ul>',
                'container' => false,
                'depth' => 2, // Allow one level of dropdown
                'walker' => new Main_Nav_Walker(),
            ));
            ?>
            <div class="main-nav__menu__ctas">
                <?php if ($cta_1): ?>
                    <a href="<?= esc_url($cta_1['url']); ?>" class="btn btn-transparent"><?= esc_html($cta_1['title']); ?></a>
                <?php endif; ?>
                <?php if ($cta_2): ?>
                    <a href="<?= esc_url($cta_2['url']); ?>" class="btn btn-secondary"><?= esc_html($cta_2['title']); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>