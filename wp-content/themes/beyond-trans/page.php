<?php
// Template Name: Page
$disclaimer_page = get_field('disclaimer_page', get_the_ID());
$current_page = get_queried_object_id();

check_therapist_directory_consent($disclaimer_page, $current_page);
get_header();
?>

<main>
    <section>
        <?php the_content(); ?>
    </section>
</main>

<?php
get_footer();
?>