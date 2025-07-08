<?php
//Register function files		
$functions = array_diff(scandir(get_template_directory() . '/functions'), array('.', '..', '.DS_Store'));
foreach ($functions as $function) {
    include('functions/' . $function);
}

/**
 * One-time function to add missing countries and their regions
 * Call this function once to populate Wales, Ireland, and New Zealand
 */
function add_missing_countries_and_regions() {
    $results = array();
    
    // Define countries and their regions
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
        // Create or get the country term
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
            
            // Create the region term
            $region = wp_insert_term($region_name, 'region-state-province', array(
                'slug' => $region_slug
            ));
            
            if (!is_wp_error($region)) {
                $region_id = $region['term_id'];
                
                // Use the same method that works for other countries
                // Try updating with the field key first
                $field_updated = update_field('field_6818dab8555ce', $country_id, 'region-state-province_' . $region_id);
                
                if (!$field_updated) {
                    // Fallback to term meta
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

// Uncomment the line below and visit any page on your site to run this function once
// Then comment it back out
// add_action('init', function() { echo add_missing_countries_and_regions(); exit; });
