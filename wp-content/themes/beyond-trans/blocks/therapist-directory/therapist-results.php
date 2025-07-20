<?php
/**
 * Therapist Directory Results
 */

if ($therapists->have_posts()): ?>
    <div class="therapist-directory__grid">
        <?php while ($therapists->have_posts()): $therapists->the_post();
            $title = get_the_title();
            $photo = get_the_post_thumbnail_url(get_the_ID());
            $bio = get_field('description', get_the_ID());
            $therapist_specialties = wp_get_post_terms(get_the_ID(), 'specialty', ['fields' => 'names']);

            $contact_info = get_field('contact_info', get_the_ID());
            $location = get_field('location', get_the_ID());
        ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="therapist-card-link">
                <div class="therapist-card">
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