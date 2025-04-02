<?php
$genspect_related_websites = get_field('genspect_related_websites', 'option');
?>

<?php if ($genspect_related_websites): ?>
<nav class="container">
    <?php foreach ($genspect_related_websites as $website):
            $url = isset($website['website']['url']) ? $website['website']['url'] : '';
            $title = isset($website['website']['title']) ? $website['website']['title'] : '';
            $website_icon = isset($website['website_icon']) ? $website['website_icon'] : '';

            // Get the full SVG code using the ID
            $svg_code = $website_icon ? get_field('full_svg_code', $website_icon) : '';
        ?>
    <a href="<?php echo $url; ?>" target="_blank" class="related-website">
        <span class="website-icon"><?php echo $svg_code; ?></span>
        <span class="website-title"><?php echo $title; ?></span>
    </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>