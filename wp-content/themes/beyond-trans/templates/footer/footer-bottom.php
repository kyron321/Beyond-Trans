<div class="footer__bottom">
    <p class="footer__bottom__copyright">
        &copy; <?= date('Y'); ?> Beyond Trans. All rights reserved.
    </p>
    <menu class="footer__bottom__menu">
        <?php wp_nav_menu(array(
            'theme_location' => 'policy-menu',
            'container' => false,
            'items_wrap' => '<ul>%3$s</ul>',
        )); ?>
    </menu>
</div>