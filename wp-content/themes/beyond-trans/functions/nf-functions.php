<?php

/**
 * Create testimonial post from Ninja Forms submission
 */
function create_testimonial_from_ninja_form($form_data)
{
    // Only process submissions from the testimonial form
    if ($form_data['form_id'] != 6) {
        return $form_data;
    }

    $name = 'Anonymous';
    $testimonial = '';
    $testimonial_type = '';
    $affiliation = '';
    $star_rating = 5;

    // Extract data from the form submission
    if (isset($form_data['fields'][37]) && !empty($form_data['fields'][37]['value'])) {
        $name = sanitize_text_field($form_data['fields'][37]['value']);
    }

    if (isset($form_data['fields'][40]) && !empty($form_data['fields'][40]['value'])) {
        $testimonial = sanitize_textarea_field($form_data['fields'][40]['value']);
    }

    if (isset($form_data['fields'][39]) && !empty($form_data['fields'][39]['value'])) {
        $testimonial_type = sanitize_text_field($form_data['fields'][39]['value']);
    }

    // Create post data
    $testimonial_data = array(
        'post_title'    => $name,
        'post_content'  => $testimonial,
        'post_status'   => 'pending',
        'post_type'     => 'testimonial',
    );

    // Insert the post
    $post_id = wp_insert_post($testimonial_data);

    // Add custom fields if the post was created successfully
    if ($post_id && !is_wp_error($post_id)) {
        // Save the testimonial as a taxonomy term
        if (!empty($testimonial_type)) {
            wp_set_object_terms($post_id, $testimonial_type, 'testimonial_type');
        }

        // Save the ACF fields
        update_field('quote', $testimonial, $post_id);
        update_field('affiliation', $affiliation, $post_id);
        update_field('star_rating', $star_rating, $post_id);

        // Add submission source metadata
        add_post_meta($post_id, '_submitted_via', 'User submission form');
    }

    return $form_data;
}
add_action('ninja_forms_after_submission', 'create_testimonial_from_ninja_form');
