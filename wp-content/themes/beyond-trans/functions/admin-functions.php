<?php

/**
 * Replace Login Page website logo
 */

function custom_login_logo()
{
    $logo_url = get_theme_file_uri() . '/assets/images/logo.png';
    if ($logo_url) :
?>
        <style type="text/css">
            #login h1 a {
                background-image: url('<?php echo esc_url($logo_url); ?>');
                background-size: contain;
                background-repeat: no-repeat;
                width: 100%;
                height: 80px;
            }
        </style>
<?php
    endif;
}
add_action('login_enqueue_scripts', 'custom_login_logo');
