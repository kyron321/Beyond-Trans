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
