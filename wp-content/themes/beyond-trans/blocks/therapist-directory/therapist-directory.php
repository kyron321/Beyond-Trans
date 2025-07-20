<?php

/**
 * Therapist Directory Block
 * 
 * Displays a filterable directory of therapists with specialty, country, and region selects
 */

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$specialties = get_terms([
    'taxonomy' => 'specialty',
    'hide_empty' => true,
]);

$countries_with_therapists = [];
$all_countries = get_terms([
    'taxonomy' => 'country',
    'hide_empty' => false,
]);

foreach ($all_countries as $country) {
    $therapist_check = new WP_Query([
        'post_type' => 'therapist',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'tax_query' => [
            [
                'taxonomy' => 'country',
                'field' => 'term_id',
                'terms' => $country->term_id,
            ]
        ]
    ]);
    
    if ($therapist_check->have_posts()) {
        $countries_with_therapists[] = $country;
    }
    wp_reset_postdata();
}

$countries = $countries_with_therapists;

$current_specialties = isset($_GET['specialty']) ? array_map('sanitize_text_field', (array)$_GET['specialty']) : [];
$current_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
$current_region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
$current_search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

$regions = [];
if (!empty($current_country)) {
    $country_term = get_term_by('slug', $current_country, 'country');
    if ($country_term && !is_wp_error($country_term))
        $all_regions = get_terms([
            'taxonomy' => 'region-state-province',
            'hide_empty' => false,
        ]);
        
        foreach ($all_regions as $region) {
            $linked_country = get_field('linked_country', 'region-state-province_' . $region->term_id);
            
            if ($linked_country) {
                $linked_country_id = null;
                if (is_object($linked_country) && isset($linked_country->term_id)) {
                    $linked_country_id = $linked_country->term_id;
                } elseif (is_array($linked_country) && !empty($linked_country)) {
                    $first_country = reset($linked_country);
                    $linked_country_id = is_object($first_country) ? $first_country->term_id : $first_country;
                } elseif (is_numeric($linked_country)) {
                    $linked_country_id = $linked_country;
                }
                
                if ($linked_country_id == $country_term->term_id) {
                    $therapist_check = new WP_Query([
                        'post_type' => 'therapist',
                        'posts_per_page' => 1,
                        'fields' => 'ids',
                        'tax_query' => [
                            'relation' => 'AND',
                            [
                                'taxonomy' => 'country',
                                'field' => 'term_id',
                                'terms' => $country_term->term_id,
                            ],
                            [
                                'taxonomy' => 'region-state-province',
                                'field' => 'term_id',
                                'terms' => $region->term_id,
                            ]
                        ]
                    ]);
                    
                    if ($therapist_check->have_posts()) {
                        $regions[] = $region;
                    }
                    wp_reset_postdata();
                }
            }
        }
    }


$args = [
    'post_type' => 'therapist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];

if (!empty($current_search)) {
    $args['s'] = $current_search;
}

$tax_queries = [];

if (!empty($current_specialties)) {
    foreach ($current_specialties as $specialty) {
        $tax_queries[] = [
            'taxonomy' => 'specialty',
            'field' => 'slug',
            'terms' => $specialty,
        ];
    }
}

if (!empty($current_country)) {
    $tax_queries[] = [
        'taxonomy' => 'country',
        'field' => 'slug',
        'terms' => $current_country,
    ];
}

if (!empty($current_region)) {
    $tax_queries[] = [
        'taxonomy' => 'region-state-province',
        'field' => 'slug',
        'terms' => $current_region,
    ];
}

if (!empty($tax_queries)) {
    if (count($tax_queries) > 1) {
        $args['tax_query'] = [
            'relation' => 'AND',
            ...$tax_queries
        ];
    } else {
        $args['tax_query'] = $tax_queries;
    }
}

$therapists = new WP_Query($args);

$current_url = strtok($_SERVER["REQUEST_URI"], '?');

if ($is_ajax) {
    echo '<div id="therapist-results">';
    include __DIR__ . '/therapist-results.php';
    echo '</div>';
    wp_die();
}

$seo_title_parts = [];
if (!empty($current_specialties)) {
    $specialty_names = [];
    foreach ($current_specialties as $specialty_slug) {
        $term = get_term_by('slug', $specialty_slug, 'specialty');
        if ($term) {
            $specialty_names[] = $term->name;
        }
    }
    if (!empty($specialty_names)) {
        $seo_title_parts[] = implode(' & ', $specialty_names);
    }
}

if (!empty($current_country)) {
    $country_term = get_term_by('slug', $current_country, 'country');
    if ($country_term) {
        $location_parts = [$country_term->name];
        if (!empty($current_region)) {
            $region_term = get_term_by('slug', $current_region, 'region-state-province');
            if ($region_term) {
                array_unshift($location_parts, $region_term->name);
            }
        }
        $seo_title_parts[] = 'in ' . implode(', ', $location_parts);
    }
}

if (!empty($seo_title_parts)) {
    $custom_title = 'Therapists ' . implode(' ', $seo_title_parts) . ' | ' . get_bloginfo('name');
    $custom_description = 'Find qualified therapists ' . implode(' ', $seo_title_parts) . '. Browse our directory of mental health professionals.';
    
    add_action('wp_head', function() use ($custom_title, $custom_description, $current_url) {
        echo '<meta property="og:title" content="' . esc_attr($custom_title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($custom_description) . '" />' . "\n";
        echo '<meta name="description" content="' . esc_attr($custom_description) . '" />' . "\n";
        
        $canonical_url = home_url($current_url);
        if (!empty($_GET)) {
            $canonical_url .= '?' . http_build_query($_GET);
        }
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }, 1);
    
    add_filter('document_title_parts', function($title_parts) use ($custom_title) {
        $title_parts['title'] = $custom_title;
        return $title_parts;
    }, 10);
}
?>

<section class="block therapist-directory bg-light-yellow">
    <div class="container">
        <div class="therapist-directory__mobile-filter-btn">
            <button class="btn btn-primary" id="mobile-filter-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
                Filter Therapists
            </button>
        </div>

        <div class="therapist-directory__layout">
            <aside class="therapist-directory__filters-sidebar" id="filters-sidebar">
                <div class="therapist-directory__filters-container">
                    <button class="therapist-directory__mobile-close" id="mobile-filter-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <h3 class="therapist-directory__filters-title">Filter Therapists</h3>
                    <form id="therapist-filter-form" class="therapist-directory__filter-form" method="get" action="<?php echo esc_url($current_url); ?>">
                <div class="therapist-directory__filter-selects">

                    <div class="therapist-directory__filter">
                        <div class="therapist-directory__filter-label">Filter by Location:</div>
                        <div class="therapist-directory__filter-selects-inner">
                            <?php if (!empty($countries) && !is_wp_error($countries)): ?>
                                <div class="therapist-directory__filter-select-group">
                                    <select name="country" id="country-filter" class="filter-select">
                                        <option value="">Country</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?php echo esc_attr($country->slug); ?>" <?php selected($current_country, $country->slug); ?>>
                                                <?php echo esc_html($country->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="therapist-directory__filter-select-group" id="region-filter-container">
                                <select name="region" id="region-filter" class="filter-select" <?php echo empty($current_country) ? 'disabled' : ''; ?>>
                                    <option value="">Region/State/Province</option>
                                    <?php if (!empty($regions) && !is_wp_error($regions)): ?>
                                        <?php foreach ($regions as $region): ?>
                                            <option value="<?php echo esc_attr($region->slug); ?>" <?php selected($current_region, $region->slug); ?>>
                                                <?php echo esc_html($region->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="therapist-directory__filter-search">
                        <div class="therapist-directory__filter-label">Search by Therapist Name:</div>
                        <div class="therapist-directory__search-input-group">
                            <input type="text" 
                                   name="search" 
                                   id="name-search" 
                                   class="therapist-directory__search-input"
                                   placeholder="Enter Therapist Name"
                                   value="<?php echo esc_attr($current_search); ?>">
                        </div>
                    </div>
                       <?php if (!empty($specialties) && !is_wp_error($specialties)): ?>
                        <div class="therapist-directory__filters">
                            <div class="therapist-directory__filter-label">Filter by Specialty:</div>
                            <div class="therapist-directory__filter-checkboxes">
                                <?php foreach ($specialties as $specialty): ?>
                                    <label class="therapist-directory__checkbox-label">
                                        <input type="checkbox" 
                                               name="specialty[]" 
                                               value="<?php echo esc_attr($specialty->slug); ?>"
                                               <?php echo in_array($specialty->slug, $current_specialties) ? 'checked' : ''; ?>
                                               class="therapist-directory__checkbox">
                                        <span class="therapist-directory__checkbox-text"><?php echo esc_html($specialty->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="therapist-directory__filter-submit">
                                <button type="submit" class="btn btn-primary therapist-directory__submit-btn">Apply Filters</button>
                                <a href="<?php echo esc_url($current_url); ?>" class="btn btn-secondary">Clear All Filters</a>
                            </div>
                    </div>
                </form>
                <div class="therapist-directory__mobile-view-results">
                    <button type="button" class="btn btn-primary" id="mobile-view-results">
                        View <span id="results-count"><?php echo $therapists->found_posts; ?></span> Results
                    </button>
                </div>
                </div>
            </aside>

            <main class="therapist-directory__main-content">
                <div id="therapist-results">
                    <?php include __DIR__ . '/therapist-results.php'; ?>
                </div>
            </main>
            </div>

        <?php wp_reset_postdata(); ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('mobile-filter-toggle');
    const filterClose = document.getElementById('mobile-filter-close');
    const filtersSidebar = document.getElementById('filters-sidebar');
    const body = document.body;
    
    if (filterToggle && filterClose && filtersSidebar) {
        filterToggle.addEventListener('click', function() {
            filtersSidebar.classList.add('is-open');
            body.classList.add('modal-open');
        });
        
        filterClose.addEventListener('click', function() {
            filtersSidebar.classList.remove('is-open');
            body.classList.remove('modal-open');
        });
        
        filtersSidebar.addEventListener('click', function(e) {
            if (e.target === filtersSidebar) {
                filtersSidebar.classList.remove('is-open');
                body.classList.remove('modal-open');
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filtersSidebar.classList.contains('is-open')) {
                filtersSidebar.classList.remove('is-open');
                body.classList.remove('modal-open');
            }
        });
    }
});
</script>