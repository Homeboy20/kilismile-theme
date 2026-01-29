<?php
/**
 * Simple diagnostic for payment issues
 */

// Include WordPress bootstrap
require_once('../../../wp-load.php');

echo "<h1>Payment System Diagnostic</h1>";

// Check AzamPay settings
echo "<h2>AzamPay Configuration Status</h2>";

$azampay_settings = array(
    'app_name' => get_option('kilismile_azampay_app_name', ''),
    'client_id' => get_option('kilismile_azampay_client_id', ''),
    'client_secret' => get_option('kilismile_azampay_client_secret', ''),
    'sandbox' => get_option('kilismile_azampay_sandbox', true)
);

$has_credentials = !empty($azampay_settings['app_name']) && 
                   !empty($azampay_settings['client_id']) && 
                   !empty($azampay_settings['client_secret']);

if ($has_credentials) {
    echo "<p>✅ AzamPay credentials are configured</p>";
    echo "<ul>";
    echo "<li>App Name: " . esc_html($azampay_settings['app_name']) . "</li>";
    echo "<li>Client ID: " . esc_html(substr($azampay_settings['client_id'], 0, 10)) . "...</li>";
    echo "<li>Sandbox Mode: " . ($azampay_settings['sandbox'] ? 'Yes' : 'No') . "</li>";
    echo "</ul>";
} else {
    echo "<p>❌ AzamPay credentials are NOT configured</p>";
    echo "<p><strong>SOLUTION:</strong> Go to <a href='" . admin_url('admin.php?page=kilismile-payment-settings') . "'>Payment Settings</a> and configure your AzamPay credentials.</p>";
    
    echo "<h3>Required AzamPay Settings:</h3>";
    echo "<ul>";
    echo "<li>App Name: " . (empty($azampay_settings['app_name']) ? '❌ Not set' : '✅ Set') . "</li>";
    echo "<li>Client ID: " . (empty($azampay_settings['client_id']) ? '❌ Not set' : '✅ Set') . "</li>";
    echo "<li>Client Secret: " . (empty($azampay_settings['client_secret']) ? '❌ Not set' : '✅ Set') . "</li>";
    echo "</ul>";
}

// Check WordPress AJAX
echo "<h2>WordPress AJAX Configuration</h2>";

if (defined('DOING_AJAX')) {
    echo "<p>✅ WordPress AJAX is available</p>";
} else {
    echo "<p>ℹ️ Not in AJAX context (normal for this test)</p>";
}

// Test if action is registered
global $wp_filter;
if (isset($wp_filter['wp_ajax_kilismile_process_payment'])) {
    echo "<p>✅ AJAX action 'kilismile_process_payment' is registered</p>";
} else {
    echo "<p>❌ AJAX action 'kilismile_process_payment' is NOT registered</p>";
}

if (isset($wp_filter['wp_ajax_nopriv_kilismile_process_payment'])) {
    echo "<p>✅ AJAX action 'kilismile_process_payment' is registered for non-logged users</p>";
} else {
    echo "<p>❌ AJAX action 'kilismile_process_payment' is NOT registered for non-logged users</p>";
}

// Check current admin URL
echo "<h2>WordPress Configuration</h2>";
echo "<p>Admin AJAX URL: " . admin_url('admin-ajax.php') . "</p>";
echo "<p>Site URL: " . site_url() . "</p>";
echo "<p>Home URL: " . home_url() . "</p>";

// Provide quick fix
if (!$has_credentials) {
    echo "<h2>Quick Fix Instructions</h2>";
    echo "<ol>";
    echo "<li>Go to your WordPress admin dashboard</li>";
    echo "<li>Navigate to <strong>KiliSmile Settings > Payment Settings</strong></li>";
    echo "<li>Fill in your AzamPay credentials:</li>";
    echo "<ul>";
    echo "<li>App Name: (Your AzamPay application name)</li>";
    echo "<li>Client ID: (Your AzamPay client ID)</li>";
    echo "<li>Client Secret: (Your AzamPay client secret)</li>";
    echo "<li>Enable Sandbox mode for testing</li>";
    echo "</ul>";
    echo "<li>Save the settings</li>";
    echo "<li>Test the donation form again</li>";
    echo "</ol>";
    
    echo "<h3>For Testing (Sandbox):</h3>";
    echo "<p>You can use AzamPay's sandbox credentials for testing. Visit the <a href='https://developers.azampay.co.tz/' target='_blank'>AzamPay Developer Portal</a> to get test credentials.</p>";
}

?>

<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 40px;
    background: #f9f9f9;
}
h1, h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
}
ul, ol {
    margin: 15px 0 15px 20px;
}
a {
    color: #0073aa;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>

