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

/**
 * Create therapist post from Ninja Forms submission
 */
function create_therapist_from_ninja_form($form_data)
{
    // Only process submissions from the therapist form (form ID 7)
    if ($form_data['form_id'] != 7) {
        return $form_data;
    }

    // Initialize variables
    $name = '';
    $description = '';
    $company = '';
    $website = '';
    $location = '';
    $country = '';
    $email = '';
    $specialties = array();
    $image_url = '';

    // Extract data from the form submission based on actual field IDs/keys
    if (isset($form_data['fields_by_key']['name_1746732593811']) && !empty($form_data['fields_by_key']['name_1746732593811']['value'])) {
        $name = sanitize_text_field($form_data['fields_by_key']['name_1746732593811']['value']);
    }

    if (isset($form_data['fields_by_key']['about_you_1746732750587']) && !empty($form_data['fields_by_key']['about_you_1746732750587']['value'])) {
        $description = sanitize_textarea_field($form_data['fields_by_key']['about_you_1746732750587']['value']);
    }

    if (isset($form_data['fields_by_key']['company_1746732640040']) && !empty($form_data['fields_by_key']['company_1746732640040']['value'])) {
        $company = sanitize_text_field($form_data['fields_by_key']['company_1746732640040']['value']);
    }

    if (isset($form_data['fields_by_key']['website_1746732727998']) && !empty($form_data['fields_by_key']['website_1746732727998']['value'])) {
        $website = esc_url_raw($form_data['fields_by_key']['website_1746732727998']['value']);
        // Make sure website has a proper protocol
        if (!empty($website) && !preg_match("~^(?:f|ht)tps?://~i", $website)) {
            $website = "https://" . $website;
        }
    }

    if (isset($form_data['fields_by_key']['region_state_province_1746732697327']) && !empty($form_data['fields_by_key']['region_state_province_1746732697327']['value'])) {
        $location = sanitize_text_field($form_data['fields_by_key']['region_state_province_1746732697327']['value']);
    }

    if (isset($form_data['fields_by_key']['country_1746733998322']) && !empty($form_data['fields_by_key']['country_1746733998322']['value'])) {
        $country = sanitize_text_field($form_data['fields_by_key']['country_1746733998322']['value']);
    }

    if (isset($form_data['fields_by_key']['email_1746732710335']) && !empty($form_data['fields_by_key']['email_1746732710335']['value'])) {
        $email = sanitize_email($form_data['fields_by_key']['email_1746732710335']['value']);
    }

    // Get specialties (multiple checkboxes)
    if (isset($form_data['fields_by_key']['areas_of_speciality_1746734103584']) && !empty($form_data['fields_by_key']['areas_of_speciality_1746734103584']['value'])) {
        $specialties = (array) $form_data['fields_by_key']['areas_of_speciality_1746734103584']['value'];
    }

    // Get uploaded image if available
    if (
        isset($form_data['fields_by_key']['file_upload_1746732113358']) &&
        !empty($form_data['fields_by_key']['file_upload_1746732113358']['value'])
    ) {

        if (is_array($form_data['fields_by_key']['file_upload_1746732113358']['value'])) {
            // Get the first image URL if it's an array
            $image_url = reset($form_data['fields_by_key']['file_upload_1746732113358']['value']);
        } else {
            $image_url = $form_data['fields_by_key']['file_upload_1746732113358']['value'];
        }
    }

    // Create location string combining region and country
    $full_location = $location;
    if (!empty($country)) {
        $full_location = !empty($location) ? $location . ', ' . $country : $country;
    }

    // Create post data
    $therapist_data = array(
        'post_title'    => $name,
        'post_content'  => '', // We'll store description in ACF
        'post_status'   => 'pending', // Set as pending for review
        'post_type'     => 'therapist',
    );

    // Insert the post
    $post_id = wp_insert_post($therapist_data);

    // Add custom fields if the post was created successfully
    if ($post_id && !is_wp_error($post_id)) {
        // Save the ACF fields
        update_field('description', $description, $post_id);

        // Create the contact_info group
        $contact_info = array();

        if (!empty($company)) {
            $contact_info['company'] = $company;
        }

        if (!empty($website)) {
            $contact_info['website'] = $website;
        }

        if (!empty($full_location)) {
            $contact_info['location'] = $full_location;
        }

        if (!empty($email)) {
            $contact_info['email'] = $email;
        }

        update_field('contact_info', $contact_info, $post_id);

        // Save specialties as taxonomy terms
        if (!empty($specialties)) {
            wp_set_object_terms($post_id, $specialties, 'specialty');
        }

        // Also save region and country as their own taxonomies
        if (!empty($location)) {
            wp_set_object_terms($post_id, $location, 'region-state-province');
        }

        if (!empty($country)) {
            wp_set_object_terms($post_id, $country, 'country');
        }

        // Set the featured image if uploaded
        if (!empty($image_url)) {
            // Check if the image URL corresponds to an attachment ID
            $attachment_id = attachment_url_to_postid($image_url);

            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        // Add submission source metadata
        add_post_meta($post_id, '_submitted_via', 'Therapist submission form');
    }

    return $form_data;
}
add_action('ninja_forms_after_submission', 'create_therapist_from_ninja_form');


/**
 * Replace Ninja Forms email with therapist email on submission
 */
function replace_ninja_forms_to_email_with_therapist_email($form_data)
{
    // get hidden field value named therapist_id
    $therapist_id = isset($form_data['fields'][60]['value']) ? $form_data['fields'][60]['value'] : '';

    // using therapist_id, get the email address of the therapist from acf
    $therapist_email = '';
    if (!empty($therapist_id)) {
        // Get ACF fields for this therapist
        $fields = get_fields($therapist_id);

        // Extract email from contact_info group
        if (isset($fields['contact_info']['email']) && !empty($fields['contact_info']['email'])) {
            $therapist_email = trim($fields['contact_info']['email']);
        }
    }

    // Modify the email field value, set to the actual therapist email
    $form_data['fields'][59]['value'] = $therapist_email;

    return $form_data;
}
add_filter('ninja_forms_submit_data', 'replace_ninja_forms_to_email_with_therapist_email', 10, 1);

/** 
 * Log form data from ninja forms to debug.log
 */
function log_ninja_forms_data($form_data)
{
    // Check if the form ID is 6 or 7
    if ($form_data['form_id'] == 6 || $form_data['form_id'] == 7) {
        // Log the form data
        error_log(print_r($form_data, true));
    }
}
add_action('ninja_forms_after_submission', 'log_ninja_forms_data');
add_action('ninja_forms_after_display', 'log_ninja_forms_data');
