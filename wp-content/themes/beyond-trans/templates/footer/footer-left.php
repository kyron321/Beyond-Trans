<?php
$website_logo = get_field('website_logo', 'option');
?>

<div class="footer__main__inner__left">
    <a class="footer__main__inner__left__logo" href="/" aria-label="Home">
        <?php $svg_code = $website_logo ? get_svg_by_post_id($website_logo) : ''; ?>
        <?php if ($svg_code): ?>
            <?= $svg_code; ?>
        <?php endif; ?>
    </a>
    <div class="footer__main__inner__left__newsletter">
        <p class="footer__main__inner__left__newsletter__title">
            Get the latest news on therapy services, peer support, and upcoming events.
        </p>
        <div class="footer__main__inner__left__newsletter__form">
            <form id="newsletter-form" action="<?php echo esc_url(home_url('/')); ?>" method="post">
                <input type="email" name="email" placeholder="Enter your email address" required>
                <button type="submit" class="btn btn-transparent">Join</button>
            </form>
        </div>
        <p class="footer__main__inner__left__newsletter__privacy">
            By subscribing, you consent to our Privacy Policy and agree to receive updates.
        </p>
    </div>
</div>