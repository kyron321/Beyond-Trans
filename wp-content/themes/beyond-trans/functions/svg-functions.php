<?php
// Register a custom post type for SVGs
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

    register_post_type('svg_library', $args);
}
add_action('init', 'register_svg_post_type');

// Add a custom column to the SVG post type
function add_svg_columns($columns)
{
    $columns['svg_preview'] = 'Preview'; // Ensure the key matches
    return $columns;
}
add_filter('manage_svg_library_posts_columns', 'add_svg_columns');

// Populate the custom column
function add_svg_column_content($column, $post_id)
{
    if ($column === 'svg_preview') {
        // Get the SVG code from the ACF field
        $svg_code = get_field('full_svg_code', $post_id); // Use the ACF field name

        if ($svg_code) {
            // Display the SVG code directly in the admin column
            echo '<div>';
            echo $svg_code; // Render the SVG
            echo '</div>';
        } else {
            echo 'No SVG code found';
        }
    }
}
add_action('manage_svg_library_posts_custom_column', 'add_svg_column_content', 10, 2);

// Add a meta box to display the SVG preview on the single SVG editor page
function add_svg_preview_meta_box()
{
    add_meta_box(
        'svg_preview_meta_box', // Meta box ID
        'SVG Preview',          // Meta box title
        'render_svg_preview_meta_box', // Callback function
        'svg_library',          // Post type
        'side',                 // Context (side, normal, advanced)
        'low'                  // Priority
    );
}
add_action('add_meta_boxes', 'add_svg_preview_meta_box');

// Render the SVG preview in the meta box
function render_svg_preview_meta_box($post)
{
    // Get the SVG code from the ACF field
    $svg_code = get_field('full_svg_code', $post->ID); // Use the ACF field name

    if ($svg_code) {
        // Display the SVG preview
        echo '<div style="border: 1px solid #ddd; padding: 10px; text-align: center;">';
        echo '<div style="max-width: 100%; max-height: 200px; overflow: auto;">';
        echo $svg_code; // Render the SVG
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>No SVG code found.</p>';
    }
}
