<?php
/**
 * Create AJAX Test Page
 * Run this once to create the test page
 */

// Include WordPress
if (!defined('ABSPATH')) {
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

// Check if page already exists
$test_page = get_page_by_path('ajax-test');

if (!$test_page) {
    // Create the page
    $page_data = array(
        'post_title'    => 'AJAX Test',
        'post_content'  => 'This page uses the Simple AJAX Test template to diagnose payment system issues.',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => 'ajax-test'
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', 'page-ajax-test.php');
        echo "✅ AJAX Test page created successfully! Visit: " . home_url('/ajax-test/');
    } else {
        echo "❌ Failed to create AJAX Test page.";
    }
} else {
    // Update existing page to use the template
    update_post_meta($test_page->ID, '_wp_page_template', 'page-ajax-test.php');
    echo "✅ AJAX Test page already exists and template updated! Visit: " . home_url('/ajax-test/');
}

// Redirect to the test page
echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/ajax-test/') . '"; }, 2000);</script>';
?>

