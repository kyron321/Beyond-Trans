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

add_action('acf/init', function () {
    if (function_exists('acf_register_block_type')) {
        acf_register_block_type(array(
            'name'              => 'banner',
            'title'             => __('Banner'),
            'description'       => __('A custom banner block that uses ACF fields.'),
            'render_template'   => get_template_directory() . '/blocks/banner/banner.php',
            'category'          => 'Beyond Trans',
            'icon'              => 'format-image',
            'keywords'          => array('banner'),
            'enqueue_style'     => get_template_directory_uri() . '/blocks/banner/banner.css',
            'supports'          => array(
                'anchor' => true,
                'align'  => true,
                'jsx'    => true,
                'color'  => true,
            ),
        ));
    }
});
