<?php

/**
 * Plugin Name: Beyond Trans Disclaimers
 * Plugin URI: https://kyronsmith.com
 * Description: A plugin to manage disclaimer pages.
 * Version: 1.0.0
 * Author: Kyron Smith
 * Author URI: https://kyronsmith.com
 * Text Domain: beyond-trans-disclaimers
 */

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Register the Disclaimers post type
 */
function beyond_trans_register_disclaimers_post_type()
{
    $labels = array(
        'name'               => _x('Disclaimers', 'post type general name', 'beyond-trans-disclaimers'),
        'singular_name'      => _x('Disclaimer', 'post type singular name', 'beyond-trans-disclaimers'),
        'menu_name'          => _x('Disclaimers', 'admin menu', 'beyond-trans-disclaimers'),
        'name_admin_bar'     => _x('Disclaimer', 'add new on admin bar', 'beyond-trans-disclaimers'),
        'add_new'            => _x('Add New', 'disclaimer', 'beyond-trans-disclaimers'),
        'add_new_item'       => __('Add New Disclaimer', 'beyond-trans-disclaimers'),
        'new_item'           => __('New Disclaimer', 'beyond-trans-disclaimers'),
        'edit_item'          => __('Edit Disclaimer', 'beyond-trans-disclaimers'),
        'view_item'          => __('View Disclaimer', 'beyond-trans-disclaimers'),
        'all_items'          => __('All Disclaimers', 'beyond-trans-disclaimers'),
        'search_items'       => __('Search Disclaimers', 'beyond-trans-disclaimers'),
        'parent_item_colon'  => __('Parent Disclaimers:', 'beyond-trans-disclaimers'),
        'not_found'          => __('No disclaimers found.', 'beyond-trans-disclaimers'),
        'not_found_in_trash' => __('No disclaimers found in Trash.', 'beyond-trans-disclaimers')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Disclaimer pages', 'beyond-trans-disclaimers'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'disclaimer'),
        'capability_type'    => 'page',
        'has_archive'        => false,
        'hierarchical'       => true,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-shield',
        'supports'           => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'page-attributes',
            'custom-fields',
            'revisions',
            'template'
        ),
        'show_in_rest'       => true,
        'template_lock'      => false
    );

    register_post_type('disclaimer', $args);
}

/**
 * Add theme support for full site editing features
 */
function beyond_trans_disclaimers_theme_support()
{
    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for full site editing
    add_theme_support('block-templates');

    // Add template support for the disclaimer post type
    add_theme_support('block-template-parts');
}

// Make it so that this plugin doesn't show updates
add_filter('site_transient_update_plugins', 'beyond_trans_disclaimers_remove_update_notification');
function beyond_trans_disclaimers_remove_update_notification($value)
{
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}

// Hook into the 'init' action
add_action('init', 'beyond_trans_register_disclaimers_post_type');
add_action('after_setup_theme', 'beyond_trans_disclaimers_theme_support');
