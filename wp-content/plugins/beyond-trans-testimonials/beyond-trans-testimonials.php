<?php

/**
 * Plugin Name: Beyond Trans Testimonials
 * Plugin URI: https://kyronsmith.com
 * Description: A simple plugin to manage testimonials.
 * Version: 1.0.0
 * Author: Kyron Smith
 * Author URI: https://kyronsmith.com
 * Text Domain: beyond-trans-testimonials
 */

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Register the Testimonials post type
 */
function beyond_trans_register_testimonials_post_type()
{
    $labels = array(
        'name'               => _x('Testimonials', 'post type general name', 'beyond-trans-testimonials'),
        'singular_name'      => _x('Testimonial', 'post type singular name', 'beyond-trans-testimonials'),
        'menu_name'          => _x('Testimonials', 'admin menu', 'beyond-trans-testimonials'),
        'name_admin_bar'     => _x('Testimonial', 'add new on admin bar', 'beyond-trans-testimonials'),
        'add_new'            => _x('Add New', 'testimonial', 'beyond-trans-testimonials'),
        'add_new_item'       => __('Add New Testimonial', 'beyond-trans-testimonials'),
        'new_item'           => __('New Testimonial', 'beyond-trans-testimonials'),
        'edit_item'          => __('Edit Testimonial', 'beyond-trans-testimonials'),
        'view_item'          => __('View Testimonial', 'beyond-trans-testimonials'),
        'all_items'          => __('All Testimonials', 'beyond-trans-testimonials'),
        'search_items'       => __('Search Testimonials', 'beyond-trans-testimonials'),
        'parent_item_colon'  => __('Parent Testimonials:', 'beyond-trans-testimonials'),
        'not_found'          => __('No testimonials found.', 'beyond-trans-testimonials'),
        'not_found_in_trash' => __('No testimonials found in Trash.', 'beyond-trans-testimonials')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Testimonials for your organization', 'beyond-trans-testimonials'),
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 30,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => array('title')
    );

    register_post_type('testimonial', $args);
}

/**
 * Register the Testimonial Type taxonomy
 */
function beyond_trans_register_testimonial_type_taxonomy()
{
    $labels = array(
        'name'              => _x('Testimonial Types', 'taxonomy general name', 'beyond-trans-testimonials'),
        'singular_name'     => _x('Testimonial Type', 'taxonomy singular name', 'beyond-trans-testimonials'),
        'search_items'      => __('Search Testimonial Types', 'beyond-trans-testimonials'),
        'all_items'         => __('All Testimonial Types', 'beyond-trans-testimonials'),
        'parent_item'       => __('Parent Testimonial Type', 'beyond-trans-testimonials'),
        'parent_item_colon' => __('Parent Testimonial Type:', 'beyond-trans-testimonials'),
        'edit_item'         => __('Edit Testimonial Type', 'beyond-trans-testimonials'),
        'update_item'       => __('Update Testimonial Type', 'beyond-trans-testimonials'),
        'add_new_item'      => __('Add New Testimonial Type', 'beyond-trans-testimonials'),
        'new_item_name'     => __('New Testimonial Type Name', 'beyond-trans-testimonials'),
        'menu_name'         => __('Testimonial Types', 'beyond-trans-testimonials'),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'rewrite'           => false,
    );

    register_taxonomy('testimonial_type', array('testimonial'), $args);
}

/**
 * Add pending testimonial count to admin menu
 */
function beyond_trans_pending_testimonials_count()
{
    global $menu;

    // Count pending testimonials
    $pending_count = wp_count_posts('testimonial')->pending;

    // Only proceed if there are pending testimonials
    if ($pending_count > 0) {
        // Find the testimonial menu item
        foreach ($menu as $key => $value) {
            if ($value[2] == 'edit.php?post_type=testimonial') {
                // Add the pending count to the menu label
                $menu[$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $pending_count . '">' .
                    '<span class="pending-count">' . $pending_count . '</span>' .
                    '</span>';
                break;
            }
        }
    }
}
add_action('admin_menu', 'beyond_trans_pending_testimonials_count');

/**
 * Add admin notice for pending testimonials on the testimonials list page
 */
function beyond_trans_pending_testimonials_notice()
{
    $screen = get_current_screen();

    // Only show on testimonials screen
    if ($screen->post_type !== 'testimonial') {
        return;
    }

    // Count pending testimonials
    $pending_count = wp_count_posts('testimonial')->pending;

    // Only show if there are pending testimonials
    if ($pending_count > 0) {
        $url = admin_url('edit.php?post_status=pending&post_type=testimonial');

        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>';
        printf(
            _n(
                'There is %s pending testimonial that needs review.',
                'There are %s pending testimonials that need review.',
                $pending_count,
                'beyond-trans-testimonials'
            ),
            '<a href="' . esc_url($url) . '">' . $pending_count . '</a>'
        );
        echo '</strong></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'beyond_trans_pending_testimonials_notice');

/**
 * Prevent this plugin from being deactivated
 */
function beyond_trans_testimonials_prevent_deactivation($actions, $plugin_file, $plugin_data, $context)
{
    // Get the basename of the current plugin file
    $this_plugin = plugin_basename(__FILE__);

    // Check if we're working with this plugin
    if ($plugin_file == $this_plugin) {
        // Remove the deactivate link
        if (isset($actions['deactivate'])) {
            unset($actions['deactivate']);
        }

        // Add a notice explaining why it can't be deactivated
        $actions['required'] = __('Required Plugin');
    }

    return $actions;
}

add_filter('plugin_action_links', 'beyond_trans_testimonials_prevent_deactivation', 10, 4);

// Register custom post type and taxonomy on 'init'
add_action('init', function () {
    beyond_trans_register_testimonials_post_type();
    beyond_trans_register_testimonial_type_taxonomy();
});
