<?php

/**
 * Plugin Name: Beyond Trans Therapists
 * Plugin URI: https://kyronsmith.com
 * Description: A plugin to manage therapist profiles.
 * Version: 1.0.0
 * Author: Kyron Smith
 * Author URI: https://kyronsmith.com
 * Text Domain: beyond-trans-therapists
 */

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Register the Therapists post type
 */
function beyond_trans_register_therapists_post_type()
{
    $labels = array(
        'name'               => _x('Therapists', 'post type general name', 'beyond-trans-therapists'),
        'singular_name'      => _x('Therapist', 'post type singular name', 'beyond-trans-therapists'),
        'menu_name'          => _x('Therapists', 'admin menu', 'beyond-trans-therapists'),
        'name_admin_bar'     => _x('Therapist', 'add new on admin bar', 'beyond-trans-therapists'),
        'add_new'            => _x('Add New', 'therapist', 'beyond-trans-therapists'),
        'add_new_item'       => __('Add New Therapist', 'beyond-trans-therapists'),
        'new_item'           => __('New Therapist', 'beyond-trans-therapists'),
        'edit_item'          => __('Edit Therapist', 'beyond-trans-therapists'),
        'view_item'          => __('View Therapist', 'beyond-trans-therapists'),
        'all_items'          => __('All Therapists', 'beyond-trans-therapists'),
        'search_items'       => __('Search Therapists', 'beyond-trans-therapists'),
        'parent_item_colon'  => __('Parent Therapists:', 'beyond-trans-therapists'),
        'not_found'          => __('No therapists found.', 'beyond-trans-therapists'),
        'not_found_in_trash' => __('No therapists found in Trash.', 'beyond-trans-therapists')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Therapists profiles', 'beyond-trans-therapists'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'therapist'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-businessman',
        'supports'           => array('title', 'thumbnail',)
    );

    register_post_type('therapist', $args);
}

/**
 * Register Specialty taxonomy for Therapists
 */
function beyond_trans_register_specialty_taxonomy()
{
    $labels = array(
        'name'              => _x('Specialties', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Specialty', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Specialties', 'beyond-trans-therapists'),
        'all_items'         => __('All Specialties', 'beyond-trans-therapists'),
        'parent_item'       => __('Parent Specialty', 'beyond-trans-therapists'),
        'parent_item_colon' => __('Parent Specialty:', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Specialty', 'beyond-trans-therapists'),
        'update_item'       => __('Update Specialty', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Specialty', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Specialty Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Specialties', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => false,
        'rewrite'           => false,
    );

    register_taxonomy('specialty', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_specialty_taxonomy');

/**
 * Register Countries taxonomy for Therapists
 */
function beyond_trans_register_countries_taxonomy()
{
    $labels = array(
        'name'              => _x('Countries', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Country', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Countries', 'beyond-trans-therapists'),
        'all_items'         => __('All Countries', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Country', 'beyond-trans-therapists'),
        'update_item'       => __('Update Country', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Country', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Country Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Countries', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'country'),
    );

    register_taxonomy('country', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_countries_taxonomy');

/**
 * Register Regions/States/Provinces taxonomy for Therapists
 */
function beyond_trans_register_regions_taxonomy()
{
    $labels = array(
        'name'              => _x('Regions/States/Provinces', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Region/State/Province', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Regions/States/Provinces', 'beyond-trans-therapists'),
        'all_items'         => __('All Regions/States/Provinces', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Region/State/Province', 'beyond-trans-therapists'),
        'update_item'       => __('Update Region/State/Province', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Region/State/Province', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Region/State/Province Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Regions, States & Provinces', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'region'),
        'description'       => false,
    );

    register_taxonomy('region-state-province', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_regions_taxonomy');

// Make it so that this plugin doesn't show updates
add_filter('site_transient_update_plugins', 'beyond_trans_remove_update_notification');
function beyond_trans_remove_update_notification($value)
{
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}

/**
 * Prevent this plugin from being deactivated
 */

function beyond_trans_therapists_prevent_deactivation($actions, $plugin_file, $plugin_data, $context)
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

add_filter('plugin_action_links', 'beyond_trans_therapists_prevent_deactivation', 10, 4);

// Hook into the 'init' action
add_action('init', 'beyond_trans_register_therapists_post_type');
