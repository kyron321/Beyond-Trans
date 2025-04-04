<?php
$social_links = get_field('socials', 'option');
?>

<div class="footer__main__inner__right">
    <menu class="footer__main__inner__right__menu__one">
        <?php
        $menu_one = wp_get_nav_menu_object(get_nav_menu_locations()['footer-menu-one']);
        ?>
        <?php if ($menu_one): ?>
            <h6><?= esc_html($menu_one->name); ?></h6>
        <?php endif; ?>
        <?php wp_nav_menu(array(
            'theme_location' => 'footer-menu-one',
        )); ?>
    </menu>
    <menu class="footer__main__inner__right__menu__two">
        <?php
        $menu_two = wp_get_nav_menu_object(get_nav_menu_locations()['footer-menu-two']);
        ?>
        <?php if ($menu_two): ?>
            <h6><?= esc_html($menu_two->name); ?></h6>
        <?php endif; ?>
        <?php wp_nav_menu(array(
            'theme_location' => 'footer-menu-two',
        )); ?>
    </menu>
    <menu class="footer__main__inner__right__menu__socials">
        <h6>Follow Us</h6>
        <?php if ($social_links): ?>
            <ul>
                <?php foreach ($social_links as $item): ?>
                    <?php
                    $social = isset($item['social']) ? $item['social'] : null;
                    $social_icon = isset($item['social_icon']) ? $item['social_icon'] : null;
                    if ($social):
                    ?>
                        <li>
                            <a href="<?= esc_url($social['url']); ?>" target="<?= esc_attr($social['target'] ?: '_blank'); ?>" class="footer__social-link">
                                <?php if ($social_icon): ?>
                                    <?= get_svg_by_post_id($social_icon); ?>
                                <?php endif; ?>
                                <?php if ($social['title']): ?>
                                    <?= esc_html($social['title']); ?>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </menu>
</div>