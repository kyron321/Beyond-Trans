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

$countries = get_terms([
    'taxonomy' => 'country',
    'hide_empty' => true,
]);

// Get current filters if set
$current_specialty = isset($_GET['specialty']) ? sanitize_text_field($_GET['specialty']) : '';
$current_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
$current_region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';

// Get regions for the selected country (if any)
$regions = [];
if (!empty($current_country)) {
    // Get the country term ID
    $country_term = get_term_by('slug', $current_country, 'country');
    if ($country_term && !is_wp_error($country_term)) {
        // Get regions linked to this country using ACF relationship
        $regions = get_terms([
            'taxonomy' => 'region-state-province',
            'hide_empty' => true,
            'meta_query' => [
                [
                    'key' => 'linked_country',
                    'value' => $country_term->term_id,
                    'compare' => '='
                ]
            ]
        ]);
    }
}

// Define default query args
$args = [
    'post_type' => 'therapist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];

// Build tax query for filters
$tax_queries = [];

if (!empty($current_specialty)) {
    $tax_queries[] = [
        'taxonomy' => 'specialty',
        'field' => 'slug',
        'terms' => $current_specialty,
    ];
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
        <!-- Filter Section -->
        <div class="therapist-directory__filters-container">
            <form class="therapist-directory__filter-form fade-in" method="get" action="<?php echo esc_url($current_url); ?>">
                <div class="therapist-directory__filter-selects">

                    <!-- Specialty Filters -->
                    <?php if (!empty($specialties) && !is_wp_error($specialties)): ?>
                        <div class="therapist-directory__filters">
                            <div class="therapist-directory__filter-label">Filter by specialty:</div>
                            <div class="therapist-directory__filter-buttons">
                                <a href="<?php echo esc_url(remove_query_arg('specialty')); ?>"
                                    class="btn btn-primary <?php echo empty($current_specialty) ? 'active' : ''; ?>">
                                    All
                                </a>

                                <?php foreach ($specialties as $specialty): ?>
                                    <a href="<?php echo esc_url(add_query_arg('specialty', $specialty->slug)); ?>"
                                        class="btn btn-primary <?php echo $current_specialty === $specialty->slug ? 'active' : ''; ?>">
                                        <?php echo esc_html($specialty->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Country Select & Region select container -->
                    <div class="therapist-directory__filter-selects">
                        <div class="therapist-directory__filter-label">Filter by location & Region / State / Province</div>
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
                            <div class="therapist-directory__filter-submit">
                                <a href="<?php echo esc_url($current_url); ?>" class="btn btn-secondary">Clear Filters</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Therapists Grid -->
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
                    <div class="therapist-card fade-in">
                        <?php if ($photo): ?>
                            <div class="therapist-card__image">
                                <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="therapist-card__content">
                            <h5 class="therapist-card__name"><?php echo esc_html($title); ?></h5>

                            <?php if (!empty($therapist_specialties)): ?>
                                <p class="therapist-card__specialties">
                                    <?php echo esc_html(implode(', ', $therapist_specialties)); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($location): ?>
                                <div class="therapist-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path>
                                        <circle cx="12" cy="9" r="2.5"></circle>
                                    </svg>
                                    <?php echo esc_html($location); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($bio): ?>
                                <div class="therapist-card__bio">
                                    <?php echo wp_trim_words($bio, 20, '...'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="therapist-card__contact">
                                <?php if (!empty($contact_info['company'])): ?>
                                    <div class="therapist-card__contact-item">
                                        <strong>Company:</strong> <?php echo esc_html($contact_info['company']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($contact_info['website'])): ?>
                                    <div class="therapist-card__contact-item">
                                        <a href="<?php echo esc_url($contact_info['website']); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html($contact_info['website']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($contact_info['location'])): ?>
                                    <div class="therapist-card__contact-item">
                                        <?php echo esc_html($contact_info['location']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($contact_info['contact']) && is_array($contact_info['contact'])): ?>
                                    <div class="therapist-card__contact-item">
                                        <a href="<?php echo esc_url($contact_info['contact']['url']); ?>"
                                            target="<?php echo esc_attr($contact_info['contact']['target'] ?: '_self'); ?>">
                                            <?php echo esc_html($contact_info['contact']['title']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-underline">View Profile</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="therapist-directory__no-results">
                <p>No therapists found matching your criteria.</p>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>