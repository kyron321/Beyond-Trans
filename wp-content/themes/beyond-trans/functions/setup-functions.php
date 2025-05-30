<?php
// Define theme version
define('THEME_VERSION', '1.0.1');

// Enqueue theme styles
function theme_styles()
{
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/dist/css/style.min.css', [], THEME_VERSION);
}
add_action('wp_enqueue_scripts', 'theme_styles');

// Enqueue admin exclusive styles
function admin_styles()
{
    global $pagenow;
    if (is_user_logged_in() || $pagenow === 'wp-login.php') {
        wp_enqueue_style('admin-style', get_template_directory_uri() . '/dist/css/admin.min.css', [], THEME_VERSION);
    }
}
add_action('admin_enqueue_scripts', 'admin_styles');
add_action('login_enqueue_scripts', 'admin_styles');

//Enqueue theme scripts
function theme_scripts()
{
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/dist/js/main.min.js', [], THEME_VERSION, true);
}
add_action('wp_enqueue_scripts', 'theme_scripts');

// Enqueue theme fonts
function theme_fonts()
{
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap', [], null);
}
add_action('wp_enqueue_scripts', 'theme_fonts');

// Define JQuery
function define_jquery()
{
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', get_template_directory_uri() . '/assets/vendor/jquery-3.7.1.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'define_jquery');

// Add theme support for post thumbnails
add_theme_support('post-thumbnails');
// Add theme support for title tag (for Yoast and WP SEO plugins)
add_theme_support('title-tag');

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

// Remove block editor default styles
function bt_remove_block_editor_styles()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'bt_remove_block_editor_styles');

// Register Slick Slider
function theme_enqueue_scripts()
{
    wp_enqueue_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1');
    wp_enqueue_style('slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array('slick'), '1.8.1');
    wp_enqueue_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
add_action('admin_enqueue_scripts', 'theme_enqueue_scripts');