<?php	
$functions = array_diff(scandir(get_template_directory() . '/functions'), array('.', '..', '.DS_Store'));
foreach ($functions as $function) {
    include('functions/' . $function);
}

function auto_redirect_disclaimer_with_consent() {
    // Only run on disclaimer post type pages
    if (!is_singular('disclaimer')) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    $content = $post->post_content;
    if (!has_blocks($content)) {
        return;
    }
    
    $blocks = parse_blocks($content);
    
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'acf/disclaimer') {
            $block_content = get_field('block_content', $post->ID);
            $cta_one = isset($block_content['cta_one']) ? $block_content['cta_one'] : '';
            
            if (!empty($cta_one['url'])) {
                // Get page ID for cookie - with fallback for therapist directory
                $cookie_page_id = url_to_postid($cta_one['url']);
                
                // Fallback for therapist directory if ID not found
                if (empty($cookie_page_id) && strpos($cta_one['url'], 'therapist-directory') !== false) {
                    $cookie_page_id = '592';
                }
                
                if (!empty($cookie_page_id)) {
                    $cookie_name = 'page_consent_' . $cookie_page_id;
                    
                    // Check if cookie exists and has the correct value
                    if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == '1') {
                        // Validate the URL before redirecting
                        if (filter_var($cta_one['url'], FILTER_VALIDATE_URL) || wp_http_validate_url($cta_one['url'])) {
                            wp_redirect($cta_one['url']);
                            exit;
                        }
                    }
                }
            }
            break; 
        }
    }
}
add_action('template_redirect', 'auto_redirect_disclaimer_with_consent');

function add_missing_countries_and_regions() {
    $results = array();
    
    $countries_data = array(
        'Wales' => array(
            'slug' => 'wales',
            'regions' => array(
                'Anglesey',
                'Bridgend',
                'Caerphilly',
                'Cardiff',
                'Carmarthenshire',
                'Ceredigion',
                'Conwy',
                'Denbighshire',
                'Flintshire',
                'Gwynedd',
                'Merthyr Tydfil',
                'Monmouthshire',
                'Neath Port Talbot',
                'Newport',
                'Pembrokeshire',
                'Powys',
                'Rhondda Cynon Taff',
                'Swansea',
                'Torfaen',
                'Vale of Glamorgan',
                'Wrexham'
            )
        ),
        'Ireland' => array(
            'slug' => 'ireland',
            'regions' => array(
                'Antrim',
                'Armagh',
                'Carlow',
                'Cavan',
                'Clare',
                'Cork',
                'Derry',
                'Donegal',
                'Down',
                'Dublin',
                'Fermanagh',
                'Galway',
                'Kerry',
                'Kildare',
                'Kilkenny',
                'Laois',
                'Leitrim',
                'Limerick',
                'Longford',
                'Louth',
                'Mayo',
                'Meath',
                'Monaghan',
                'Offaly',
                'Roscommon',
                'Sligo',
                'Tipperary',
                'Tyrone',
                'Waterford',
                'Westmeath',
                'Wexford',
                'Wicklow'
            )
        ),
        'New Zealand' => array(
            'slug' => 'new-zealand',
            'regions' => array(
                'Auckland',
                'Bay of Plenty',
                'Canterbury',
                'Gisborne',
                'Hawke\'s Bay',
                'Manawatu-Wanganui',
                'Marlborough',
                'Nelson',
                'Northland',
                'Otago',
                'Southland',
                'Taranaki',
                'Tasman',
                'Waikato',
                'Wellington',
                'West Coast'
            )
        )
    );
    
    foreach ($countries_data as $country_name => $country_info) {
        $country = wp_insert_term($country_name, 'country', array(
            'slug' => $country_info['slug']
        ));
        
        if (is_wp_error($country)) {
            // Country might already exist, try to get it
            $country_term = get_term_by('slug', $country_info['slug'], 'country');
            $country_id = $country_term ? $country_term->term_id : null;
        } else {
            $country_id = $country['term_id'];
        }
        
        if (!$country_id) {
            $results[] = "Error: Could not create or find $country_name";
            continue;
        }
        
        $created_regions = array();
        
        foreach ($country_info['regions'] as $region_name) {
            $region_slug = sanitize_title($region_name);
            
            $region = wp_insert_term($region_name, 'region-state-province', array(
                'slug' => $region_slug
            ));
            
            if (!is_wp_error($region)) {
                $region_id = $region['term_id'];
                
                $field_updated = update_field('field_6818dab8555ce', $country_id, 'region-state-province_' . $region_id);
                
                if (!$field_updated) {
                    update_term_meta($region_id, 'linked_country', $country_id);
                    update_term_meta($region_id, '_linked_country', 'field_6818dab8555ce');
                    $field_updated = true;
                }
                
                $created_regions[] = $region_name;
            }
        }
        
        $results[] = "$country_name (ID: $country_id) - Created " . count($created_regions) . " regions";
    }
    
    return implode('<br>', $results);
}
