<?php
// Add support for ACF JSON
add_filter('acf/settings/save_json', 'save_acf_json_to_theme');
function save_acf_json_to_theme($path)
{
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
}

add_filter('acf/settings/load_json', 'load_acf_json_from_theme');
function load_acf_json_from_theme($paths)
{
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
}
