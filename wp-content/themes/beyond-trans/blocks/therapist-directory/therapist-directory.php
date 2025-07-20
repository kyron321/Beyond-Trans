<?php

/**
 * Therapist Directory Block
 * 
 * Displays a filterable directory of therapists with specialty, country, and region selects
 */
// Get all taxonomies for filtering
$specialties = get_terms([
    'taxonomy' => 'specialty',
    'hide_empty' => true,
]);

// Get all countries that have therapists
$countries_with_therapists = [];
$all_countries = get_terms([
    'taxonomy' => 'country',
    'hide_empty' => false,
]);

// Check each country for therapists
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

// Get current filters if set
$current_specialties = isset($_GET['specialty']) ? array_map('sanitize_text_field', (array)$_GET['specialty']) : [];
$current_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
$current_region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
$current_search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Get regions for the selected country (if any)
$regions = [];
if (!empty($current_country)) {
    // Get the country term ID
    $country_term = get_term_by('slug', $current_country, 'country');
    if ($country_term && !is_wp_error($country_term)) {
        // Get all regions and filter by linked country
        $all_regions = get_terms([
            'taxonomy' => 'region-state-province',
            'hide_empty' => false,
        ]);
        
        foreach ($all_regions as $region) {
            $linked_country = get_field('linked_country', 'region-state-province_' . $region->term_id);
            
            // Check if this region is linked to the selected country
            if ($linked_country) {
                // Handle different return formats
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
                    // Check if this region has any therapists
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
}

// Define default query args
$args = [
    'post_type' => 'therapist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];

// Add name search if provided
if (!empty($current_search)) {
    $args['s'] = $current_search;
}

// Build tax query for filters
$tax_queries = [];

// Handle specialties with AND logic - each selected specialty must be present
if (!empty($current_specialties)) {
    // Create a separate tax query for each specialty to ensure AND logic
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

// Add tax query to main args if any filters are active
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

// Run the query
$therapists = new WP_Query($args);

// Get current URL without query parameters for form action
$current_url = strtok($_SERVER["REQUEST_URI"], '?');
?>

<section class="block therapist-directory bg-light-yellow">
    <div class="container">
        <!-- Mobile Filter Button -->
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
            <!-- Filter Sidebar (Desktop) / Modal (Mobile) -->
            <aside class="therapist-directory__filters-sidebar" id="filters-sidebar">
                <div class="therapist-directory__filters-container">
                    <!-- Mobile Close Button -->
                    <button class="therapist-directory__mobile-close" id="mobile-filter-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <h3 class="therapist-directory__filters-title">Filter Therapists</h3>
                    <form id="therapist-filter-form" class="therapist-directory__filter-form" method="get" action="<?php echo esc_url($current_url); ?>">
                <div class="therapist-directory__filter-selects">

                    <!-- Country Select & Region select container -->
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

                    <!-- Name Search -->
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
                       <!-- Specialty Filters -->
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
                                <a href="<?php echo esc_url($current_url); ?>" class="btn btn-secondary">Clear All Filters</a>
                            </div>
                    </div>
                </form>
                <!-- Mobile View Results Button -->
                <div class="therapist-directory__mobile-view-results">
                    <button type="button" class="btn btn-primary" id="mobile-view-results">
                        View <span id="results-count"><?php echo $therapists->found_posts; ?></span> Results
                    </button>
                </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="therapist-directory__main-content">
                <!-- Therapists Grid -->
                <div id="therapist-results">
        <?php if ($therapists->have_posts()): ?>
            <div class="therapist-directory__grid">
                <?php while ($therapists->have_posts()): $therapists->the_post();
                    // Get therapist details
                    $title = get_the_title();
                    $photo = get_the_post_thumbnail_url(get_the_ID());
                    $bio = get_field('description', get_the_ID());
                    $therapist_specialties = wp_get_post_terms(get_the_ID(), 'specialty', ['fields' => 'names']);

                    // Get contact info fields
                    $contact_info = get_field('contact_info', get_the_ID());
                    $location = get_field('location', get_the_ID());
                ?>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="therapist-card-link">
                        <div class="therapist-card fade-in">
                            <div class="therapist-card__image">
                                    <?php if ($photo && !empty($photo)): ?>
                                        <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" onerror="this.src='https://beyond-trans-k.local/wp-content/uploads/2025/07/placeholder.png'; this.onerror=null;">
                                    <?php else: ?>
                                        <img src="https://beyond-trans-k.local/wp-content/uploads/2025/07/placeholder.png" alt="<?php echo esc_attr($title); ?>" loading="lazy" onerror="this.style.display='none';">
                                    <?php endif; ?>
                                </div>
                                <div class="therapist-card__content" style="display: flex; flex-direction: column; height: 100%;">
                                    <h5 class="therapist-card__name"><?php echo esc_html($title); ?></h5>

                                    <?php if (!empty($therapist_specialties)): ?>
                                        <p class="therapist-card__specialties">
                                            <?php echo esc_html(implode(', ', $therapist_specialties)); ?>
                                        </p>
                                    <?php endif; ?>
                                  
                                    <?php if ($location): ?>
                                        <div class="therapist-card__location">
                                            <?php echo esc_html($location); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($bio): ?>
                                        <div class="therapist-card__bio">
                                            <?php echo wp_trim_words($bio, 10, '...'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="therapist-card__contact">
                                        <?php if (!empty($contact_info['company'])): ?>
                                            <div class="therapist-card__contact-items">
                                                <strong>Company:</strong> <?php echo esc_html($contact_info['company']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($contact_info['website'])): ?>
                                            <div class="therapist-card__contact-item">
                                                <svg class="therapist-profile__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                                </svg>
                                                <?php echo esc_html($contact_info['website']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($contact_info['location'])): ?>
                                            <div class="therapist-card__contact-item">
                                                <svg class="therapist-profile__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <?php echo esc_html($contact_info['location']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($contact_info['contact']) && is_array($contact_info['contact'])): ?>
                                            <div class="therapist-card__contact-item">
                                                <?php echo esc_html($contact_info['contact']['title']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="therapist-directory__no-results">
                <p>No therapists found matching your criteria.</p>
            </div>
        <?php endif; ?>
                </div>
            </main>
            </div>

        <?php wp_reset_postdata(); ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile filter modal functionality
    const filterToggle = document.getElementById('mobile-filter-toggle');
    const filterClose = document.getElementById('mobile-filter-close');
    const filtersSidebar = document.getElementById('filters-sidebar');
    const body = document.body;
    
    if (filterToggle && filterClose && filtersSidebar) {
        // Open modal
        filterToggle.addEventListener('click', function() {
            filtersSidebar.classList.add('is-open');
            body.classList.add('modal-open');
        });
        
        // Close modal
        filterClose.addEventListener('click', function() {
            filtersSidebar.classList.remove('is-open');
            body.classList.remove('modal-open');
        });
        
        // Close on outside click
        filtersSidebar.addEventListener('click', function(e) {
            if (e.target === filtersSidebar) {
                filtersSidebar.classList.remove('is-open');
                body.classList.remove('modal-open');
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filtersSidebar.classList.contains('is-open')) {
                filtersSidebar.classList.remove('is-open');
                body.classList.remove('modal-open');
            }
        });
    }
});
</script>