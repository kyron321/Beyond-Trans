<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo get_bloginfo('description'); ?>">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" />
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php get_template_part('template-parts/common/favicon'); ?>
    <?php wp_head(); ?>
    <?php echo '<!-- Header loaded -->'; ?>
</head>

<body <?php body_class(); ?>>

    <?php get_template_part('templates/header/top-nav'); ?>
    <header class="header">
        <?php get_template_part('templates/header/main-nav'); ?>
    </header>