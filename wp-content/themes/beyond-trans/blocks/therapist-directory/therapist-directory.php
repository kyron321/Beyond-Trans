<?php

/**
 * Therapist Directory Block
 * 
 * Displays a filterable directory of therapists with specialty filters
 */
// Get all specialties
$specialties = get_terms([
    'taxonomy' => 'specialty',
    'hide_empty' => true,
]);

// Get current specialty filter if set
$current_specialty = isset($_GET['specialty']) ? sanitize_text_field($_GET['specialty']) : '';

// Define default query args
$args = [
    'post_type' => 'therapist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];

// Add taxonomy filter if specialty is selected
if (!empty($current_specialty)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'specialty',
            'field' => 'slug',
            'terms' => $current_specialty,
        ]
    ];
}

// Run the query
$therapists = new WP_Query($args);
?>

<section class="block therapist-directory bg-light-yellow">
    <div class="container">
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

        <!-- Therapists Grid -->
        <?php if ($therapists->have_posts()): ?>
            <div class="therapist-directory__grid">
                <?php while ($therapists->have_posts()): $therapists->the_post();
                    // Get therapist details
                    $title = get_the_title();
                    $photo = get_the_post_thumbnail_url(get_the_ID());
                    $bio = get_field('bio', get_the_ID());
                    $therapist_specialties = wp_get_post_terms(get_the_ID(), 'specialty', ['fields' => 'names']);
                    $location = get_field('location', get_the_ID());
                    $contact_info = get_field('contact_info', get_the_ID());
                ?>
                    <div class="therapist-card">
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

                            <?php if ($contact_info): ?>
                                <div class="therapist-card__contact">
                                    <?php
                                    if (is_array($contact_info)) {
                                        foreach ($contact_info as $contact_item) {
                                            if (is_string($contact_item)) {
                                                echo '<div>' . wp_kses_post($contact_item) . '</div>';
                                            }
                                        }
                                    } else {
                                        echo wp_kses_post($contact_info);
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

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