<?php

/**
 * Get alt text for an image
 */
function get_alt_text($image)
{
    // If alt text exists and is not empty, use it
    if (isset($image['alt']) && !empty($image['alt'])) {
        return $image['alt'];
    }

    // Otherwise, extract filename from URL and clean it up
    $filename = basename($image['url']);

    // Remove file extension
    $filename = preg_replace('/\.[^.]+$/', '', $filename);

    // Replace hyphens and underscores with spaces
    $filename = str_replace(['-', '_'], ' ', $filename);

    // Capitalize words
    return ucwords($filename);
}


/**
 * Get SVG code by post ID
 * 
 * @param int $post_id The post ID of the SVG
 * @param string $field_name The ACF field name to retrieve the SVG code from
 * @return string The SVG code or a fallback message
 */
function get_svg_by_post_id($post_id, $field_name = 'full_svg_code')
{
    // Get the SVG code from the specified ACF field
    $svg_code = get_field($field_name, $post_id);

    // Return the SVG code or a fallback message
    if ($svg_code) {
        return $svg_code;
    }

    return '<p>No SVG code found.</p>';
}


/**
 * Get background color class based on ACF selection
 * 
 * @param mixed $background_color The ACF background_colour field value
 * @return string CSS class
 */
function bt_get_background_color($background_color)
{
    if (is_array($background_color)) {
        // If it's an array with a value key, use that
        if (isset($background_color['value'])) {
            $background_color = $background_color['value'];
        }
        // If it's just an array, use the first element
        else if (!empty($background_color)) {
            $background_color = reset($background_color);
        }
        // Otherwise set to empty
        else {
            $background_color = '';
        }
    }

    // Define color mappings with CSS classes
    $color_map = [
        'dark-green' => 'bg-primary',
        'darker-green' => 'bg-primary-dark',
        'darkest-green' => 'bg-primary-darker',
        'off-grey' => 'bg-grey',
        'light-yellow' => 'bg-secondary'
    ];

    // If background color is not set or doesn't exist in our map, return empty string
    if (!$background_color || !isset($color_map[$background_color])) {
        return '';
    }

    // Return the CSS class
    return $color_map[$background_color];
}

/**
 * Get text color class that complements the background color
 * 
 * @param mixed $background_color The ACF background_colour field value
 * @return string CSS class for text color
 */
function bt_get_text_color_for_background($background_color)
{
    // Handle case when $background_color is an array (from ACF)
    if (is_array($background_color)) {
        // If it's an array with a value key, use that
        if (isset($background_color['value'])) {
            $background_color = $background_color['value'];
        }
        // If it's just an array, use the first element
        else if (!empty($background_color)) {
            $background_color = reset($background_color);
        }
        // Otherwise set to empty
        else {
            $background_color = '';
        }
    }

    // Define which backgrounds need light text
    $dark_backgrounds = [
        'Dark Green',
        'Darker Green',
        'Darkest Green'
    ];

    if (in_array($background_color, $dark_backgrounds)) {
        return 'text-light';
    }

    return 'text-dark';
}
