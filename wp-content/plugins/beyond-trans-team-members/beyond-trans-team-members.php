<?php

/**
 * Plugin Name: Beyond Trans Team Members
 * Plugin URI: https://kyronsmith.com
 * Description: A simple plugin to manage team members.
 * Version: 1.0.0
 * Author: Kyron Smith
 * Author URI: https://kyronsmith.com
 * Text Domain: beyond-trans-team-members
 */

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Register the Team Members post type
 */
function beyond_trans_register_team_members_post_type()
{
    $labels = array(
        'name'               => _x('Team Members', 'post type general name', 'beyond-trans-team-members'),
        'singular_name'      => _x('Team Member', 'post type singular name', 'beyond-trans-team-members'),
        'menu_name'          => _x('Team Members', 'admin menu', 'beyond-trans-team-members'),
        'name_admin_bar'     => _x('Team Member', 'add new on admin bar', 'beyond-trans-team-members'),
        'add_new'            => _x('Add New', 'team member', 'beyond-trans-team-members'),
        'add_new_item'       => __('Add New Team Member', 'beyond-trans-team-members'),
        'new_item'           => __('New Team Member', 'beyond-trans-team-members'),
        'edit_item'          => __('Edit Team Member', 'beyond-trans-team-members'),
        'view_item'          => __('View Team Member', 'beyond-trans-team-members'),
        'all_items'          => __('All Team Members', 'beyond-trans-team-members'),
        'search_items'       => __('Search Team Members', 'beyond-trans-team-members'),
        'parent_item_colon'  => __('Parent Team Members:', 'beyond-trans-team-members'),
        'not_found'          => __('No team members found.', 'beyond-trans-team-members'),
        'not_found_in_trash' => __('No team members found in Trash.', 'beyond-trans-team-members')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Team members for your organization', 'beyond-trans-team-members'),
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'thumbnail',)
    );

    register_post_type('team_member', $args);
}

// Make it so that this plugin doesnt show updates
add_filter('site_transient_update_plugins', 'remove_update_notification');
function remove_update_notification($value)
{
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}

/**
 * Prevent this plugin from being deactivated
 */
function beyond_trans_tm_prevent_deactivation($actions, $plugin_file, $plugin_data, $context)
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

// Hook into the 'init' action
add_action('init', 'beyond_trans_register_team_members_post_type');
add_filter('plugin_action_links', 'beyond_trans_tm_prevent_deactivation', 10, 4);
