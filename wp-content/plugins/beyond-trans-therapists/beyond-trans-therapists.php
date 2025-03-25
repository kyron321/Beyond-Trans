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

// Make it so that this plugin doesn't show updates
add_filter('site_transient_update_plugins', 'beyond_trans_remove_update_notification');
function beyond_trans_remove_update_notification($value)
{
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}

// Hook into the 'init' action
add_action('init', 'beyond_trans_register_therapists_post_type');
