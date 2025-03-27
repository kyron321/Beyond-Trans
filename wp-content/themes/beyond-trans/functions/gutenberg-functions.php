<?php

/**
 * Filters the list of allowed block types in the editor.
 */
function allowedBlocks()
{
    $allowed_blocks = array();

    $blocks_directory = new DirectoryIterator(get_template_directory() . '/blocks/');
    foreach ($blocks_directory as $item) {
        if ($item->isDir() && !$item->isDot() && file_exists($item->getPathname() . '/block.json')) {
            // Read the block.json file
            $block_json = json_decode(file_get_contents($item->getPathname() . '/block.json'), true);

            // Add the block name to the allowed blocks
            if (isset($block_json['name'])) {
                $allowed_blocks[] = $block_json['name'];
            }
        }
    }

    return $allowed_blocks;
}
add_filter('allowed_block_types_all', 'allowedBlocks');
