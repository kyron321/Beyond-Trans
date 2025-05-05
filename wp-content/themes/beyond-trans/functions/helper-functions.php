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
        'light-yellow' => 'bg-light-yellow',
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

    // Define which backgrounds need light text - using slug values from ACF
    $dark_backgrounds = [
        'dark-green',
        'darker-green',
        'darkest-green'
    ];

    if (in_array($background_color, $dark_backgrounds)) {
        return 'text-light';
    }

    return 'text-dark';
}

/**
 * Check for consent cookie and redirect if needed
 * 
 * @param int|WP_Post $disclaimer_page The disclaimer page ID or post object
 * @param int $current_page The current page ID
 * @param string $cookie_prefix The prefix for the cookie name (default: 'page_consent_')
 * @return bool True if redirect occurred, false otherwise
 */
function check_therapist_directory_consent($disclaimer_page, $current_page = null, $cookie_prefix = 'page_consent_')
{
    // If no current page provided, get the current queried object
    if (is_null($current_page)) {
        $current_page = get_queried_object_id();
    }

    // Only proceed if a disclaimer page exists
    if (!empty($disclaimer_page) && $current_page != $disclaimer_page) {
        // Create a specific cookie name for this page
        $cookie_name = $cookie_prefix . $current_page;

        // Check if the consent cookie exists for this page
        if (!isset($_COOKIE[$cookie_name])) {
            // Get permalink for disclaimer page
            $redirect_url = is_numeric($disclaimer_page) ?
                get_permalink($disclaimer_page) :
                get_permalink($disclaimer_page->ID);

            // Add current page ID as a query parameter for return after consent
            $redirect_url = add_query_arg('return_page', $current_page, $redirect_url);

            // Redirect to the disclaimer page
            wp_redirect($redirect_url);
            exit;
        }
    }

    return false;
}

/**
 * Populate ACF select field with Ninja Form IDs
 */
function populate_ninja_forms_acf_field($field)
{
    // Check if the field name is 'form_id'
    if ($field['name'] === 'form_id') {

        // Reset the field's choices
        $field['choices'] = array();

        // Check if Ninja Forms is active
        if (function_exists('Ninja_Forms')) {
            // Get all available Ninja Forms
            $ninja_forms = Ninja_Forms()->form()->get_forms();

            // Add each form as an option
            if (!empty($ninja_forms) && is_array($ninja_forms)) {
                foreach ($ninja_forms as $form) {
                    $form_id = $form->get_id();
                    $form_title = $form->get_setting('title');

                    // Add to choices
                    $field['choices'][$form_id] = $form_title . ' (ID: ' . $form_id . ')';
                }
            }
        }
    }

    // Return the field
    return $field;
}
add_filter('acf/load_field/name=form_id', 'populate_ninja_forms_acf_field');
