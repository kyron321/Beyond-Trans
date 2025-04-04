<footer class="footer">
    <div class="container">
        <div class="footer__main">
            <div class="footer__main__inner">
                <?php get_template_part('templates/footer/footer-left'); ?>
                <?php get_template_part('templates/footer/footer-right'); ?>
            </div>
        </div>
        <?php get_template_part('templates/footer/footer-bottom'); ?>
    </div>
</footer>
</body>

<?php wp_footer(); ?>