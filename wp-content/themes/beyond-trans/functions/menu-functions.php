<?php

// Add custom menu support
function register_custom_menus()
{
    register_nav_menus(array(
        'header-menu' => __('Header Menu'),
        'footer-menu-one'  => __('Footer Menu One'),
        'footer-menu-two'  => __('Footer Menu Two'),
        'footer-menu-three'  => __('Footer Menu Three'),
        'policy-menu'  => __('Policy Menu'),
    ));
}

add_action('init', 'register_custom_menus');
