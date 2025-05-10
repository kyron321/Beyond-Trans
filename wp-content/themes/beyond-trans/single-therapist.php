<?php

/**
 * The template for displaying single Therapist posts
 */

get_header();

echo render_block([
    'blockName' => 'acf/banner-internal',
    'attrs' => [
        'data' => [
            'title'     => 'Therapist Profile',
            'sub_title' => 'Find out how this therapist works and whether they might be the right fit for you.',
            'image'     =>  [
                'url' => '/wp-content/themes/beyond-trans/assets/images/therapy-session.webp'
            ]
        ],
    ]
]);
?>



<main id="primary" class="site-main therapist-profile">

    <?php
    while (have_posts()) : the_post();
        $therapist_id = get_the_ID();
        $name = get_the_title();
        $photo_url = get_the_post_thumbnail_url($therapist_id, 'large');
        $fields = get_fields($therapist_id);
        $bio = isset($fields['description']) ? $fields['description'] : '';
        $website = isset($fields['contact_info']['website']) ? trim($fields['contact_info']['website']) : '';
        $location_string = isset($fields['contact_info']['location']) ? $fields['contact_info']['location'] : '';
        $email = isset($fields['contact_info']['email']) ? trim($fields['contact_info']['email']) : '';
        $contact_form_link = '#contact-form-section';
        $specialties_terms = get_the_terms($therapist_id, 'specialty');
    ?>
        <section class="therapist-profile__details">
            <div class="container">
                <div class="therapist-profile__content-wrapper">
                    <div class="therapist-profile__image-column">
                        <?php if ($photo_url): ?>
                            <div class="therapist-profile__image-wrapper">
                                <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($name); ?>" class="therapist-profile__image">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="therapist-profile__info-column">
                        <h1 class="therapist-profile__name"><?php echo esc_html($name); ?></h1>
                        <?php if ($bio): ?>
                            <div class="therapist-profile__bio"><?php echo wp_kses_post($bio); ?></div>
                        <?php endif; ?>
                        <div class="therapist-profile__contact-details">
                            <?php if ($website): ?>
                                <p class="therapist-profile__contact-item">
                                    <svg class="therapist-profile__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                    <a href="<?php echo esc_url((strpos($website, 'http') !== 0 ? 'https://' : '') . $website); ?>" target="_blank" rel="noopener noreferrer" class="therapist-profile__link"><?php echo esc_html(parse_url((strpos($website, 'http') !== 0 ? 'https://' : '') . $website, PHP_URL_HOST)); ?></a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($location_string)): ?>
                                <p class="therapist-profile__contact-item">
                                    <svg class="therapist-profile__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    Located in <?php echo esc_html($location_string); ?>
                                </p>
                            <?php endif; ?>
                            <?php
                            if ($specialties_terms && !is_wp_error($specialties_terms) && !empty($specialties_terms)): ?>
                                <div class="therapist-profile__specialties">
                                    <strong class="therapist-profile__specialties-label">Specialties:</strong>
                                    <?php foreach ($specialties_terms as $term): ?>
                                        <span class="therapist-profile__specialty-tag">
                                            <?php echo esc_html($term->name); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section id="contact-form-section" class="contact-form-section">
            <div class="container contact-form-section__container">
                <h2 class="contact-form-section__title">Send Message</h2>
                <p class="contact-form-section__description">
                    Use this form to contact <?php echo esc_html($name); ?> directly. Your message will go straight to them and will be kept completely confidential.
                </p>
                <div class="contact-form-section__form-wrapper">
                    <?php
                    if (function_exists('Ninja_Forms')) {
                        echo do_shortcode('[ninja_form id=8]');
                    }
                    ?>
                </div>
            </div>
        </section>
    <?php
    endwhile;
    ?>
</main>

<?php get_footer(); ?>