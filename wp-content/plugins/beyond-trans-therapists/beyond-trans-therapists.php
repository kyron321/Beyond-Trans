<?php

/**
 * Plugin Name: Beyond Trans Therapists
 * Plugin URI: https://kyronsmith.com
 * Description: A plugin to manage therapist profiles.
 * Version: 1.0.0
 * Author: Kyron Smith
 * Author URI: https://kyronsmith.com
 * Text Domain: beyond-trans-therapists
 */

// If this file is called directly, abort.
defined('ABSPATH') || exit;

/**
 * Register the Therapists post type
 */
function beyond_trans_register_therapists_post_type()
{
    $labels = array(
        'name'               => _x('Therapists', 'post type general name', 'beyond-trans-therapists'),
        'singular_name'      => _x('Therapist', 'post type singular name', 'beyond-trans-therapists'),
        'menu_name'          => _x('Therapists', 'admin menu', 'beyond-trans-therapists'),
        'name_admin_bar'     => _x('Therapist', 'add new on admin bar', 'beyond-trans-therapists'),
        'add_new'            => _x('Add New', 'therapist', 'beyond-trans-therapists'),
        'add_new_item'       => __('Add New Therapist', 'beyond-trans-therapists'),
        'new_item'           => __('New Therapist', 'beyond-trans-therapists'),
        'edit_item'          => __('Edit Therapist', 'beyond-trans-therapists'),
        'view_item'          => __('View Therapist', 'beyond-trans-therapists'),
        'all_items'          => __('All Therapists', 'beyond-trans-therapists'),
        'search_items'       => __('Search Therapists', 'beyond-trans-therapists'),
        'parent_item_colon'  => __('Parent Therapists:', 'beyond-trans-therapists'),
        'not_found'          => __('No therapists found.', 'beyond-trans-therapists'),
        'not_found_in_trash' => __('No therapists found in Trash.', 'beyond-trans-therapists')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Therapists profiles', 'beyond-trans-therapists'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'therapist'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-businessman',
        'supports'           => array('title', 'thumbnail',)
    );

    register_post_type('therapist', $args);
}

/**
 * Register Specialty taxonomy for Therapists
 */
function beyond_trans_register_specialty_taxonomy()
{
    $labels = array(
        'name'              => _x('Specialties', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Specialty', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Specialties', 'beyond-trans-therapists'),
        'all_items'         => __('All Specialties', 'beyond-trans-therapists'),
        'parent_item'       => __('Parent Specialty', 'beyond-trans-therapists'),
        'parent_item_colon' => __('Parent Specialty:', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Specialty', 'beyond-trans-therapists'),
        'update_item'       => __('Update Specialty', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Specialty', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Specialty Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Specialties', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => false,
        'rewrite'           => false,
    );

    register_taxonomy('specialty', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_specialty_taxonomy');

/**
 * Register Countries taxonomy for Therapists
 */
function beyond_trans_register_countries_taxonomy()
{
    $labels = array(
        'name'              => _x('Countries', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Country', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Countries', 'beyond-trans-therapists'),
        'all_items'         => __('All Countries', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Country', 'beyond-trans-therapists'),
        'update_item'       => __('Update Country', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Country', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Country Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Countries', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'country'),
    );

    register_taxonomy('country', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_countries_taxonomy');

/**
 * Register Regions/States/Provinces taxonomy for Therapists
 */
function beyond_trans_register_regions_taxonomy()
{
    $labels = array(
        'name'              => _x('Regions/States/Provinces', 'taxonomy general name', 'beyond-trans-therapists'),
        'singular_name'     => _x('Region/State/Province', 'taxonomy singular name', 'beyond-trans-therapists'),
        'search_items'      => __('Search Regions/States/Provinces', 'beyond-trans-therapists'),
        'all_items'         => __('All Regions/States/Provinces', 'beyond-trans-therapists'),
        'edit_item'         => __('Edit Region/State/Province', 'beyond-trans-therapists'),
        'update_item'       => __('Update Region/State/Province', 'beyond-trans-therapists'),
        'add_new_item'      => __('Add New Region/State/Province', 'beyond-trans-therapists'),
        'new_item_name'     => __('New Region/State/Province Name', 'beyond-trans-therapists'),
        'menu_name'         => __('Regions, States & Provinces', 'beyond-trans-therapists'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'region'),
        'description'       => false,
    );

    register_taxonomy('region-state-province', array('therapist'), $args);
}
add_action('init', 'beyond_trans_register_regions_taxonomy');

/**
 * Add pending therapist count to admin menu
 */
function beyond_trans_pending_therapists_count()
{
    global $menu;

    // Count pending therapists
    $pending_count = wp_count_posts('therapist')->pending;

    // Only proceed if there are pending therapists
    if ($pending_count > 0) {
        // Find the therapist menu item
        foreach ($menu as $key => $value) {
            if ($value[2] == 'edit.php?post_type=therapist') {
                // Add the pending count to the menu label
                $menu[$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $pending_count . '">' .
                    '<span class="pending-count">' . $pending_count . '</span>' .
                    '</span>';
                break;
            }
        }
    }
}
add_action('admin_menu', 'beyond_trans_pending_therapists_count');


/**
 * Export Regions/States/Provinces taxonomy terms to a text file
 */
function beyond_trans_export_regions_to_file($filter_country_id = null)
{
    // Check if user has permission to export
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'beyond-trans-therapists'));
    }

    // Get all terms from the taxonomy
    $terms = get_terms([
        'taxonomy' => 'region-state-province',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) {
        wp_die(__('Error retrieving terms: ', 'beyond-trans-therapists') . $terms->get_error_message());
    }

    // Get all countries for mapping term_id to name
    $countries = get_terms([
        'taxonomy' => 'country',
        'hide_empty' => false,
    ]);
    $country_map = array();
    foreach ($countries as $country) {
        $country_map[$country->term_id] = $country->name;
    }

    // Create the content for the text file
    $content = "Label, Value, Calc Value\n"; // Header line
    $counter = 1;

    foreach ($terms as $term) {
        $label = $term->name;
        $value = $term->slug;
        $calc_value = $counter++;
        $linked_country_id = get_field('linked_country', 'region-state-province_' . $term->term_id);
        $linked_country_name = '';
        if ($linked_country_id) {
            // If ACF returns an array of IDs, get the first one
            if (is_array($linked_country_id)) {
                $linked_country_id = reset($linked_country_id);
            }
            $linked_country_name = isset($country_map[$linked_country_id]) ? $country_map[$linked_country_id] : $linked_country_id;
        }
        // If filtering, skip if not matching
        if ($filter_country_id && $linked_country_id != $filter_country_id) {
            continue;
        }
        $content .= "$label, $value, $calc_value\n";
    }

    // Set the file path in the WordPress uploads directory
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/regions-export-' . date('Y-m-d') . '.txt';

    // Write content to file
    $file = fopen($file_path, 'w');
    if ($file === false) {
        wp_die(__('Unable to create file for export.', 'beyond-trans-therapists'));
    }

    fwrite($file, $content);
    fclose($file);

    // Provide a download link
    $file_url = $upload_dir['baseurl'] . '/regions-export-' . date('Y-m-d') . '.txt';

    echo '<div class="notice notice-success is-dismissible"><p>';
    echo __('Export successful! ', 'beyond-trans-therapists');
    echo '<a href="' . esc_url($file_url) . '" download>' . __('Download the file', 'beyond-trans-therapists') . '</a>';
    echo '</p></div>';
}

/**
 * Add export page to the WordPress admin menu
 */
function beyond_trans_add_export_page()
{
    add_submenu_page(
        'edit.php?post_type=therapist',  // Parent slug
        __('Export Regions', 'beyond-trans-therapists'),  // Page title
        __('Export Regions', 'beyond-trans-therapists'),  // Menu title
        'manage_options',  // Capability
        'export-regions',  // Menu slug
        'beyond_trans_export_regions_page'  // Function
    );
}
add_action('admin_menu', 'beyond_trans_add_export_page');

/**
 * Display the export page content
 */
function beyond_trans_export_regions_page()
{
    // Get all countries for button generation
    $countries = get_terms([
        'taxonomy' => 'country',
        'hide_empty' => false,
    ]);
?>
    <div class="wrap">
        <h1><?php echo esc_html__('Export Regions/States/Provinces', 'beyond-trans-therapists'); ?></h1>
        <p><?php echo esc_html__('Click a button below to export all region terms, or just those for a specific country, to a text file in the format: Label, Value, Calc Value, Linked Country', 'beyond-trans-therapists'); ?></p>

        <form method="post" style="margin-bottom: 1em;">
            <?php wp_nonce_field('beyond_trans_export_regions_nonce', 'beyond_trans_nonce'); ?>
            <input type="submit" name="beyond_trans_export_regions" class="button button-primary" value="<?php echo esc_attr__('Export All Regions', 'beyond-trans-therapists'); ?>" />
        </form>
        <?php foreach ($countries as $country): ?>
            <form method="post" style="display:inline-block; margin-right: 0.5em;">
                <?php wp_nonce_field('beyond_trans_export_regions_nonce', 'beyond_trans_nonce'); ?>
                <input type="hidden" name="linked_country_id" value="<?php echo esc_attr($country->term_id); ?>" />
                <input type="submit" name="beyond_trans_export_regions_by_country" class="button button-secondary" value="<?php echo esc_attr__('Export for ', 'beyond-trans-therapists') . esc_html($country->name); ?>" />
            </form>
        <?php endforeach; ?>
    </div>
<?php

    // Process export if form is submitted
    if (isset($_POST['beyond_trans_export_regions']) && check_admin_referer('beyond_trans_export_regions_nonce', 'beyond_trans_nonce')) {
        beyond_trans_export_regions_to_file();
    }
    if (isset($_POST['beyond_trans_export_regions_by_country']) && check_admin_referer('beyond_trans_export_regions_nonce', 'beyond_trans_nonce')) {
        $linked_country_id = isset($_POST['linked_country_id']) ? intval($_POST['linked_country_id']) : 0;
        if ($linked_country_id) {
            beyond_trans_export_regions_to_file($linked_country_id);
        }
    }
}


/**
 * Add admin notice for pending therapists on the therapists list page
 */
function beyond_trans_pending_therapists_notice()
{
    $screen = get_current_screen();

    // Only show on therapists screen
    if ($screen->post_type !== 'therapist') {
        return;
    }

    // Count pending therapists
    $pending_count = wp_count_posts('therapist')->pending;

    // Only show if there are pending therapists
    if ($pending_count > 0) {
        $url = admin_url('edit.php?post_status=pending&post_type=therapist');

        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>';
        printf(
            _n(
                'There is %s pending therapist profile that needs review.',
                'There are %s pending therapist profiles that need review.',
                $pending_count,
                'beyond-trans-therapists'
            ),
            '<a href="' . esc_url($url) . '">' . $pending_count . '</a>'
        );
        echo '</strong></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'beyond_trans_pending_therapists_notice');


// Make it so that this plugin doesn't show updates
add_filter('site_transient_update_plugins', 'beyond_trans_remove_update_notification');
function beyond_trans_remove_update_notification($value)
{
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}

/**
 * Prevent this plugin from being deactivated
 */

function beyond_trans_therapists_prevent_deactivation($actions, $plugin_file, $plugin_data, $context)
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

add_filter('plugin_action_links', 'beyond_trans_therapists_prevent_deactivation', 10, 4);

// Hook into the 'init' action
add_action('init', 'beyond_trans_register_therapists_post_type');
