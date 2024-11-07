<?php 


// if( get_option( 'wptravel-blocks-migration-status' ) == true ){
//     return;
// }

// Renaming block name slug from 'wptravel' to 'wp-travel-blocks'
function wptravel_blocks_replace_content_in_files($directory, $search, $replace) {
    // Create an iterator for the directory
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        // Only modify PHP and HTML files
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'html'])) {
            $filePath = $file->getPathname();
            $fileContent = file_get_contents($filePath);

            // Replace the specified content
            $newContent = str_replace($search, $replace, $fileContent);

            // Write the new content back to the file if changes were made
            if ($fileContent !== $newContent) {
                file_put_contents($filePath, $newContent);
            }
        }
    }
}

// Get the active theme directory
$child_theme_directory = get_template_directory(); // For parent theme
$theme_directory = get_stylesheet_directory(); // For child theme

// Define the directories to search
$directories = [
    "$child_theme_directory/parts",
    "$child_theme_directory/patterns",
    "$child_theme_directory/templates",
    "$theme_directory/parts",
    "$theme_directory/patterns",
    "$theme_directory/templates",
];

// Define the search and replace strings
$replacements = [
    'wp:wptravel/' => 'wp:wp-travel-blocks/',
    'wp:wp-travel-block/' => 'wp:wp-travel-blocks/',
];

// Loop through each directory and perform the replacements
foreach ($directories as $directory) {
    if (is_dir($directory)) {
        foreach ($replacements as $search => $replace) {
            wptravel_blocks_replace_content_in_files($directory, $search, $replace);
        }
    }
}

global $wpdb;

$sql = "
    UPDATE $wpdb->posts
    SET post_content = REPLACE(REPLACE(post_content, 'wp:wptravel/', 'wp:wp-travel-blocks/'), 'wp:wp-travel-block/', 'wp:wp-travel-blocks/')
";

$result = $wpdb->query($sql);

// Check if the replacements were successful
if ($result !== false) {
    // Set the migration status option to true
    update_option('wptravel-blocks-migration-status', true);
}
