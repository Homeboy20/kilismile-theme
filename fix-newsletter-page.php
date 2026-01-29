<?php
/**
 * Fix Newsletter Page for Kilismile
 * 
 * This script creates the newsletter page if it doesn't exist
 */

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
require_once($wp_load_path);

// Check if the newsletter page exists
$newsletter_page = get_page_by_path('newsletter');

if (!$newsletter_page) {
    // Create the newsletter page
    $page_id = wp_insert_post(array(
        'post_title'    => 'Newsletter',
        'post_content'  => 'Subscribe to our newsletter to stay updated with our latest programs, success stories, and ways to get involved.',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => 'newsletter',
        'post_author'   => 1,
        'comment_status' => 'closed',
        'ping_status'   => 'closed'
    ));

    if ($page_id && !is_wp_error($page_id)) {
        // Set page template
        update_post_meta($page_id, '_wp_page_template', 'page-newsletter.php');
        
        echo "Successfully created the newsletter page with ID: $page_id\n";
    } else {
        echo "Error creating newsletter page: " . ($page_id ? $page_id->get_error_message() : "Unknown error") . "\n";
    }
} else {
    echo "Newsletter page already exists with ID: " . $newsletter_page->ID . "\n";
    
    // Ensure template is set correctly
    $template = get_post_meta($newsletter_page->ID, '_wp_page_template', true);
    
    if ($template !== 'page-newsletter.php') {
        update_post_meta($newsletter_page->ID, '_wp_page_template', 'page-newsletter.php');
        echo "Updated page template to 'page-newsletter.php'\n";
    } else {
        echo "Page template is already set correctly\n";
    }
}


