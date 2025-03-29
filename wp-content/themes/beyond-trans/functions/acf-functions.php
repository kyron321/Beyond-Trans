<?php
// Create ACF-JSON directory if it doesn't exist
function ensure_acf_json_directory_exists()
{
    $acf_json_path = get_stylesheet_directory() . '/acf-json';

    if (!file_exists($acf_json_path)) {
        wp_mkdir_p($acf_json_path);
    }
}
add_action('acf/init', 'ensure_acf_json_directory_exists');

// Save ACF JSON to theme
add_filter('acf/settings/save_json', 'save_acf_json_to_theme');
function save_acf_json_to_theme($path)
{
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
}

// Load ACF JSON from theme
add_filter('acf/settings/load_json', 'load_acf_json_from_theme');
function load_acf_json_from_theme($paths)
{
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
}

// Register ACF blocks
function register_acf_blocks()
{
    register_block_type(get_template_directory() . '/blocks/banner');
    register_block_type(get_template_directory() . '/blocks/banner-tile');
}
add_action('init', 'register_acf_blocks');

// Register a website options page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Website Options',
        'menu_title' => 'Website Options',
        'menu_slug'  => 'website-options',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ));
}