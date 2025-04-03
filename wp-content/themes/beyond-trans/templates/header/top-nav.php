<?php
$genspect_related_websites = get_field('genspect_related_websites', 'option');
?>

<?php if ($genspect_related_websites): ?>
    <nav class="top-nav container">
        <?php foreach ($genspect_related_websites as $website):
            $url = isset($website['website']['url']) ? esc_url($website['website']['url']) : '';
            $title = isset($website['website']['title']) ? esc_html($website['website']['title']) : '';
            $website_icon = isset($website['website_icon']) ? $website['website_icon'] : '';
            $svg_code = $website_icon ? get_svg_by_post_id($website_icon) : '';
        ?>
            <a href="<?php echo $url; ?>" target="_blank" class="top-nav__related-website">
                <?php if ($svg_code): ?>
                    <span class="top-nav__related-website__website-icon"><?php echo $svg_code; ?></span>
                <?php endif; ?>
                <span class="top-nav__related-website__website-title"><?php echo $title; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>