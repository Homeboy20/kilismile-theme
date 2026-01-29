<?php
define('WP_USE_THEMES', false);
require_once('../../../../wp-config.php');
require_once('../../../../wp-load.php');

// Find the donation page
$donation_page = get_page_by_path('donate');
if (!$donation_page) {
    $donation_page = get_page_by_path('donation');
}
if (!$donation_page) {
    $donation_page = get_page_by_path('donations');
}

if ($donation_page) {
    echo 'Donation page found: ' . get_permalink($donation_page->ID) . PHP_EOL;
    echo 'Page slug: ' . $donation_page->post_name . PHP_EOL;
    echo 'Page title: ' . $donation_page->post_title . PHP_EOL;
} else {
    echo 'No donation page found. Searching for pages with donation template...' . PHP_EOL;
    
    $pages = get_pages();
    foreach ($pages as $page) {
        $template = get_page_template_slug($page->ID);
        if (strpos($template, 'donation') !== false || $page->post_name === 'donate') {
            echo 'Page with donation template: ' . get_permalink($page->ID) . ' (Template: ' . $template . ')' . PHP_EOL;
        }
    }
}

// Also check current URL that works
echo 'Testing current donation URL: ' . home_url('/donate') . PHP_EOL;
echo 'Home URL: ' . home_url() . PHP_EOL;
?>


