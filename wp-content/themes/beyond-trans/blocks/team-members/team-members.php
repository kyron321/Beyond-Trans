<?php
// Get ACF fields
$subheading = get_field('subheading');
$heading = get_field('heading');
$text = get_field('text');
$team_members_selection = get_field('team_members');

// If no specific team members are selected, get all published team members
if (empty($team_members_selection)) {
    $args = array(
        'post_type' => 'team_member',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    );
    $team_members_query = new WP_Query($args);
    $team_members = $team_members_query->posts;
} else {
    $team_members = $team_members_selection;
}
?>

<section class="block team-members">
    <div class="container">
        <?php if ($subheading || $heading || $text): ?>
            <div class="team-members__header">
                <?php if ($subheading): ?>
                    <p class="team-members__subheading"><?= $subheading; ?></p>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <h2 class="team-members__heading"><?= $heading; ?></h2>
                <?php endif; ?>

                <?php if ($text): ?>
                    <p class="team-members__text"><?= $text; ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($team_members): ?>
            <div class="team-members__grid">
                <?php foreach ($team_members as $member): ?>
                    <?php
                    $member_id = $member->ID;
                    $title = get_the_title($member_id);
                    $role = get_field('experience__role', $member_id);
                    $description = get_field('description', $member_id);
                    $socials = get_field('socials', $member_id);
                    $image = get_the_post_thumbnail_url($member_id, 'medium_large');
                    ?>

                    <div class="team-member">
                        <?php if ($image): ?>
                            <div class="team-member__image">
                                <img src="<?= $image; ?>" alt="<?= $title; ?>">
                            </div>

                        <?php else: ?>
                            <div class="team-member__image">
                                <img src="<?= get_template_directory_uri(); ?>/dist/images/placeholder.webp" alt="Placeholder">
                            </div>
                        <?php endif; ?>

                        <div class="team-member__content">
                            <h5 class="team-member__name"><?= $title; ?></h5>

                            <?php if ($role): ?>
                                <p class="team-member__role"><?= $role; ?></p>
                            <?php endif; ?>

                            <?php if ($description): ?>
                                <p class="team-member__description">
                                    <?= $description; ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($socials): ?>
                                <div class="team-member__socials">
                                    <?php if (!empty($socials['linkedin'])): ?>
                                        <a href="<?= $socials['linkedin']; ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="inherit" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 3C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19ZM18.5 18.5V13.2C18.5 12.3354 18.1565 11.5062 17.5452 10.8948C16.9338 10.2835 16.1046 9.94 15.24 9.94C14.39 9.94 13.4 10.46 12.92 11.24V10.13H10.13V18.5H12.92V13.57C12.92 12.8 13.54 12.17 14.31 12.17C14.6813 12.17 15.0374 12.3175 15.2999 12.5801C15.5625 12.8426 15.71 13.1987 15.71 13.57V18.5H18.5ZM6.88 8.56C7.32556 8.56 7.75288 8.383 8.06794 8.06794C8.383 7.75288 8.56 7.32556 8.56 6.88C8.56 5.95 7.81 5.19 6.88 5.19C6.43178 5.19 6.00193 5.36805 5.68499 5.68499C5.36805 6.00193 5.19 6.43178 5.19 6.88C5.19 7.81 5.95 8.56 6.88 8.56ZM8.27 18.5V10.13H5.5V18.5H8.27Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($socials['x'])): ?>
                                        <a href="<?= $socials['x']; ?>" target="_blank" rel="noopener noreferrer" aria-label="X">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="inherit" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M18.244 2.25H21.552L14.325 10.51L22.827 21.75H16.17L10.956 14.933L4.99 21.75H1.68L9.41 12.915L1.254 2.25H8.08L12.793 8.481L18.244 2.25ZM17.083 19.77H18.916L7.084 4.126H5.117L17.083 19.77Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($socials['website'])): ?>
                                        <a href="<?= $socials['website']; ?>" target="_blank" rel="noopener noreferrer" aria-label="Website">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="inherit" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM4 12C4 11.39 4.08 10.79 4.21 10.22L8.99 15V16C8.99 17.1 9.89 18 10.99 18V19.93C7.06 19.43 4 16.07 4 12ZM17.89 17.4C17.63 16.59 16.89 16 16 16H15V13C15 12.45 14.55 12 14 12H8V10H10C10.55 10 11 9.55 11 9V7H13C14.1 7 15 6.1 15 5V4.59C17.93 5.78 20 8.65 20 12C20 14.08 19.2 15.97 17.89 17.4Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>