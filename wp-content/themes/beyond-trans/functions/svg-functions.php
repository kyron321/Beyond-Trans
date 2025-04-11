<?php

// Register SVG post type
function register_svg_post_type()
{
    $labels = array(
        'name'               => 'SVGs',
        'singular_name'      => 'SVG',
        'menu_name'          => 'SVGs',
        'add_new'            => 'Add New SVG',
        'add_new_item'       => 'Add New SVG',
        'edit_item'          => 'Edit SVG',
        'new_item'           => 'New SVG',
        'view_item'          => 'View SVG',
        'search_items'       => 'Search SVGs',
        'not_found'          => 'No SVGs found',
        'not_found_in_trash' => 'No SVGs found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 10,
        'menu_icon'          => 'dashicons-format-image',
        'supports'           => array('title'),
        'has_archive'        => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    register_post_type('svg', $args);
}
add_action('init', 'register_svg_post_type');

// Register categories for the SVG post type
function add_categories_to_svg_post_type()
{
    register_taxonomy_for_object_type('category', 'svg');
}
add_action('init', 'add_categories_to_svg_post_type');

// Add custom fields to SVG post type
function add_svg_columns($columns)
{
    $columns['svg_preview'] = 'Preview';
    return $columns;
}
add_filter('manage_svg_posts_columns', 'add_svg_columns');

// Display SVG preview in the admin column
function add_svg_column_content($column, $post_id)
{
    if ($column === 'svg_preview') {
        $svg_code = get_field('full_svg_code', $post_id);

        if ($svg_code) {
            echo '<div>';
            echo $svg_code;
            echo '</div>';
        } else {
            echo 'No SVG code found';
        }
    }
}
add_action('manage_svg_posts_custom_column', 'add_svg_column_content', 10, 2);

// Add custom meta box to SVG post type
function add_svg_preview_meta_box()
{
    add_meta_box(
        'svg_preview_meta_box',
        'SVG Preview',
        'render_svg_preview_meta_box',
        'svg',
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'add_svg_preview_meta_box');

// Render the SVG preview meta box
function render_svg_preview_meta_box($post)
{
    $svg_code = get_field('full_svg_code', $post->ID);

    if ($svg_code) {
        echo '<div style="border: 1px solid #ddd; padding: 10px; text-align: center;">';
        echo '<div style="max-width: 100%; max-height: 200px; overflow: auto;">';
        echo $svg_code;
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>No SVG code found.</p>';
    }
}
