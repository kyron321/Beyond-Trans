<?php

// Filter the list of allowed block types in the editor
function allowedBlocks()
{
    $allowed_blocks = array();

    $blocks_directory = new DirectoryIterator(get_template_directory() . '/blocks/');
    foreach ($blocks_directory as $item) {
        // add to allowed blocks field blocks
        if ($item->isDir() && !$item->isDot()) {
            $block_name = $item->getFilename();
            $allowed_blocks[] = 'acf/' . $block_name;
        }
    }

    return $allowed_blocks;
}
add_filter('allowed_block_types_all', 'allowedBlocks');

// Remove the "Advanced" tab by disabling custom class name support
function remove_the_class_anchor($metadata)
{
    $metadata['supports']['customClassName'] = false;
    $metadata['supports']['anchor'] = false;
    return $metadata;
}
add_filter('block_type_metadata', 'remove_the_class_anchor');

// Automatically enqueue block-specific JavaScript
function enqueue_block_specific_assets()
{
    global $post;

    // Determine if we're in the admin editor or on the frontend
    $is_editor = is_admin() && function_exists('get_current_screen') && get_current_screen()->is_block_editor;

    // Check if we're on a singular page or in the block editor
    if (is_singular() || $is_editor) {
        // Get the post content (for frontend or editor preview)
        $post_content = $is_editor && isset($post->post_content) ? $post->post_content : get_the_content();

        // Get all blocks used in the current post
        if (has_blocks($post_content)) {
            $blocks = parse_blocks($post_content);

            foreach ($blocks as $block) {
                // Check if the block is an ACF block
                if (strpos($block['blockName'], 'acf/') === 0) {
                    $block_name = str_replace('acf/', '', $block['blockName']);
                    $script_path = get_template_directory() . "/blocks/{$block_name}/{$block_name}.js";

                    // Check if the block-specific JS file exists
                    if (file_exists($script_path)) {
                        wp_enqueue_script(
                            "{$block_name}-block-script",
                            get_template_directory_uri() . "/blocks/{$block_name}/{$block_name}.js",
                            array('jquery'),
                            '1.0.0',
                            true
                        );
                    }
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_block_specific_assets');
add_action('enqueue_block_editor_assets', 'enqueue_block_specific_assets');
