<?php
$website_logo = get_field('website_logo', 'option');
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
        <div class="main-nav__menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header-menu',
                'items_wrap' => '<ul class="main-nav__menu__list">%3$s</ul>',
            ));
            ?>
            <div class="main-nav__menu__ctas">
                <a href="<?= esc_url(home_url('/')); ?>donate" class="btn btn-secondary">Donate</a>
                <a href="<?= esc_url(home_url('/')); ?>join" class="btn btn-transparent">Contact</a>
            </div>
        </div>
    </div>
</nav>