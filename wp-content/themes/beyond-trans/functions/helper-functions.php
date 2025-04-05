<?php
// Function to get alt text or fallback to filename
function get_alt_text($image)
{
    // If alt text exists and is not empty, use it
    if (isset($image['alt']) && !empty($image['alt'])) {
        return $image['alt'];
    }

    // Otherwise, extract filename from URL and clean it up
    $filename = basename($image['url']);

    // Remove file extension
    $filename = preg_replace('/\.[^.]+$/', '', $filename);

    // Replace hyphens and underscores with spaces
    $filename = str_replace(['-', '_'], ' ', $filename);

    // Capitalize words
    return ucwords($filename);
}
