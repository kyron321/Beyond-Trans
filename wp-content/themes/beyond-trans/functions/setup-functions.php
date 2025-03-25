<?php
// Define theme version
define('THEME_VERSION', '1.0.0');

// Add theme support for post thumbnails
add_theme_support('post-thumbnails');

function remove_comments()
{
    // Remove comments from admin menu
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });

    // Remove comments from admin bar
    add_action('admin_bar_menu', function ($wp_admin_bar) {
        $wp_admin_bar->remove_node('comments');
    }, 999);

    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // Remove comments support from post types
    add_action('init', function () {
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    });
}
remove_comments();

function add_admin_menu_separator()
{
    // Add a custom separator after Pages and before Media
    add_action('admin_menu', function () {
        global $menu;

        // Set position to be between Pages (20) and Media (10)
        // Finding a position between existing menu items
        $position = 15;

        // Add the separator
        $menu[$position] = array(
            0 => '',
            1 => 'read',
            2 => 'separator' . $position,
            3 => '',
            4 => 'wp-menu-separator'
        );
    });
}
add_admin_menu_separator();
