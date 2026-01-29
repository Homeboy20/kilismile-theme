<?php
/**
 * Emergency Plugin Activator
 * Use this if normal activation fails
 */

// WARNING: Only use this as a last resort
// This bypasses normal WordPress activation process

define('WP_USE_THEMES', false);
require_once('../../../wp-config.php');

echo "🚨 Emergency Plugin Activation\n";
echo "==============================\n\n";

// Force activate the plugin
$plugin_file = 'azampay-payment-gateway/azampay-payment-gateway.php';

// Get current active plugins
$active_plugins = get_option('active_plugins', array());

// Add our plugin if not already active
if (!in_array($plugin_file, $active_plugins)) {
    $active_plugins[] = $plugin_file;
    update_option('active_plugins', $active_plugins);
    echo "✅ Plugin added to active plugins list\n";
} else {
    echo "ℹ️ Plugin already in active plugins list\n";
}

// Try to load the plugin manually
$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
if (file_exists($plugin_path)) {
    include_once $plugin_path;
    echo "✅ Plugin file loaded\n";
    
    // Call activation if class exists
    if (class_exists('AzamPay_Gateway_Plugin')) {
        try {
            AzamPay_Gateway_Plugin::activate();
            echo "✅ Plugin activation method called\n";
        } catch (Exception $e) {
            echo "❌ Activation failed: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "❌ Plugin file not found\n";
}

echo "\n📋 Next Steps:\n";
echo "1. Refresh your WordPress admin\n";
echo "2. Check if plugin appears as active\n";
echo "3. Configure settings in AzamPay Settings\n";
echo "4. Test the donation form\n";

echo "\nDone.\n";
?>

