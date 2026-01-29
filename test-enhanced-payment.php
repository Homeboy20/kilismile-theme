<?php
/**
 * Enhanced Payment System Test
 * Access via: /wp-content/themes/kilismile/test-enhanced-payment.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('Access denied. Administrator privileges required.');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Enhanced Payment System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-result { padding: 15px; margin: 10px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>';

echo '<h1>🧪 Enhanced Payment System Test</h1>';

// Test 1: Check if plugin class exists
echo '<h2>Test 1: Plugin Class Detection</h2>';
if (class_exists('KiliSmile_Payments_Plugin')) {
    echo '<div class="test-result success">✅ KiliSmile_Payments_Plugin class found</div>';
    
    $plugin = KiliSmile_Payments_Plugin::get_instance();
    echo '<div class="test-result info">Plugin instance created successfully</div>';
    
    if (method_exists($plugin, 'get_available_gateways')) {
        $gateways = $plugin->get_available_gateways();
        echo '<div class="test-result info">Available gateways: ' . count($gateways) . '</div>';
        if (!empty($gateways)) {
            echo '<div class="code">' . print_r($gateways, true) . '</div>';
        }
    }
} else {
    echo '<div class="test-result error">❌ KiliSmile_Payments_Plugin class not found</div>';
}

// Test 2: Check constants
echo '<h2>Test 2: Plugin Constants</h2>';
$constants = ['KILISMILE_PAYMENTS_ACTIVE', 'KILISMILE_PAYMENTS_PLUGIN_DIR', 'KILISMILE_PAYMENTS_PLUGIN_URL', 'KILISMILE_PAYMENTS_VERSION'];
foreach ($constants as $constant) {
    if (defined($constant)) {
        echo '<div class="test-result success">✅ ' . $constant . ' = ' . constant($constant) . '</div>';
    } else {
        echo '<div class="test-result error">❌ ' . $constant . ' not defined</div>';
    }
}

// Test 3: Check template files
echo '<h2>Test 3: Template Files</h2>';
$templates = [
    'Enhanced Payment Form' => '/kilismile-payments-plugin/templates/forms/payment-form.php',
    'Payment Success' => '/kilismile-payments-plugin/templates/forms/payment-success.php',
    'Payment Error' => '/kilismile-payments-plugin/templates/forms/payment-error.php',
    'Donation Form Component' => '/template-parts/donation-form-component.php'
];

foreach ($templates as $name => $path) {
    $full_path = get_template_directory() . $path;
    if (file_exists($full_path)) {
        echo '<div class="test-result success">✅ ' . $name . '</div>';
        echo '<div class="code">' . $full_path . '</div>';
    } else {
        echo '<div class="test-result error">❌ ' . $name . ' not found</div>';
        echo '<div class="code">' . $full_path . '</div>';
    }
}

// Test 4: Check asset files
echo '<h2>Test 4: Asset Files</h2>';
$assets = [
    'Frontend CSS' => '/kilismile-payments-plugin/assets/css/frontend.css',
    'Frontend JS' => '/kilismile-payments-plugin/assets/js/frontend.js',
    'Admin CSS' => '/kilismile-payments-plugin/assets/css/admin.css',
    'Admin JS' => '/kilismile-payments-plugin/assets/js/admin.js'
];

foreach ($assets as $name => $path) {
    $full_path = get_template_directory() . $path;
    if (file_exists($full_path)) {
        echo '<div class="test-result success">✅ ' . $name . '</div>';
    } else {
        echo '<div class="test-result error">❌ ' . $name . ' not found</div>';
    }
    echo '<div class="code">' . $full_path . '</div>';
}

// Test 5: Test donation form component
echo '<h2>Test 5: Donation Form Component Test</h2>';
echo '<div class="test-result info">Testing donation form component output...</div>';

ob_start();
try {
    include get_template_directory() . '/template-parts/donation-form-component.php';
    $form_output = ob_get_clean();
    
    if (strpos($form_output, 'Enhanced Payment System Loaded Successfully') !== false) {
        echo '<div class="test-result success">✅ Enhanced payment form loaded successfully</div>';
    } elseif (strpos($form_output, 'Enhanced Payment System Active') !== false) {
        echo '<div class="test-result success">✅ Enhanced payment system active</div>';
    } else {
        echo '<div class="test-result error">❌ Legacy payment form being used</div>';
    }
    
    // Show first 500 characters of output
    echo '<div class="code">' . htmlspecialchars(substr($form_output, 0, 500)) . '...</div>';
    
} catch (Exception $e) {
    ob_end_clean();
    echo '<div class="test-result error">❌ Error loading form: ' . $e->getMessage() . '</div>';
}

// Test 6: AJAX endpoints
echo '<h2>Test 6: AJAX Endpoints</h2>';
$ajax_actions = [
    'kilismile_process_payment',
    'kilismile_check_payment_status'
];

foreach ($ajax_actions as $action) {
    if (has_action('wp_ajax_' . $action) || has_action('wp_ajax_nopriv_' . $action)) {
        echo '<div class="test-result success">✅ AJAX action: ' . $action . '</div>';
    } else {
        echo '<div class="test-result error">❌ AJAX action not registered: ' . $action . '</div>';
    }
}

echo '<h2>✨ Test Complete</h2>';
echo '<p><a href="' . admin_url() . '">← Back to WordPress Admin</a></p>';
echo '</body></html>';