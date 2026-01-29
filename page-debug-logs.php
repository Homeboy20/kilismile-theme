<?php
/**
 * Template Name: Donation Debug Logs
 * 
 * A WordPress page template for viewing donation debug logs
 */

get_header();

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo '<div style="padding: 40px; text-align: center;">';
    echo '<h2>Access Denied</h2>';
    echo '<p>You need administrator privileges to view debug logs.</p>';
    echo '<p><a href="' . wp_login_url() . '">Login</a> | <a href="' . home_url() . '">Home</a></p>';
    echo '</div>';
    get_footer();
    exit;
}

// Include the debug page content
include get_template_directory() . '/admin-donation-debug.php';

get_footer();
?>

