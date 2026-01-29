<?php
/**
 * Create WordPress page for debug logs
 */

// Include WordPress
$wp_config_path = 'c:\Users\yusuf\Local Sites\kilismile\app\public\wp-config.php';
if (file_exists($wp_config_path)) {
    require_once $wp_config_path;
} else {
    die("❌ WordPress configuration not found at: $wp_config_path\n");
}

// Check if page already exists
$page_slug = 'debug-logs';
$page = get_page_by_path($page_slug);

if (!$page) {
    // Create the page
    $page_data = array(
        'post_title'    => 'Donation Debug Logs',
        'post_content'  => '<!-- This page displays donation form debug logs -->',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => $page_slug,
        'page_template' => 'page-debug-logs.php'
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', 'page-debug-logs.php');
        
        echo "✅ Debug logs page created successfully!\n";
        echo "🔗 URL: " . home_url('/debug-logs/') . "\n";
        echo "📋 Page ID: " . $page_id . "\n";
        echo "🔧 Template: page-debug-logs.php\n";
    } else {
        echo "❌ Failed to create debug logs page\n";
    }
} else {
    echo "ℹ️ Debug logs page already exists\n";
    echo "🔗 URL: " . home_url('/debug-logs/') . "\n";
    echo "📋 Page ID: " . $page->ID . "\n";
    
    // Update template if needed
    $current_template = get_post_meta($page->ID, '_wp_page_template', true);
    if ($current_template !== 'page-debug-logs.php') {
        update_post_meta($page->ID, '_wp_page_template', 'page-debug-logs.php');
        echo "🔧 Template updated to: page-debug-logs.php\n";
    }
}
?>

