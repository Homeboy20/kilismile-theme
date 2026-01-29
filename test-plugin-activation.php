<?php
/**
 * Test AzamPay Plugin Activation
 */

// Set up WordPress environment
define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../../../wp-config.php');

echo "<h1>Testing AzamPay Plugin Activation</h1>\n";

// Check if plugin file exists
$plugin_file = WP_PLUGIN_DIR . '/azampay-payment-gateway/azampay-payment-gateway.php';
if (!file_exists($plugin_file)) {
    echo "<p style='color: red;'>❌ Plugin file not found: $plugin_file</p>\n";
    exit;
}

echo "<p style='color: green;'>✅ Plugin file found</p>\n";

// Try to include the plugin file to check for syntax errors
echo "<h2>Checking Plugin Syntax</h2>\n";

// Capture any output/errors
ob_start();
$error_level = error_reporting(E_ALL);

try {
    include_once $plugin_file;
    echo "<p style='color: green;'>✅ Plugin file loaded successfully</p>\n";
    
    // Check if the main class exists
    if (class_exists('AzamPay_Gateway_Plugin')) {
        echo "<p style='color: green;'>✅ Main plugin class exists</p>\n";
        
        // Try to get instance
        $instance = AzamPay_Gateway_Plugin::get_instance();
        if ($instance) {
            echo "<p style='color: green;'>✅ Plugin instance created successfully</p>\n";
        }
        
        // Test static activation method
        if (method_exists('AzamPay_Gateway_Plugin', 'activate')) {
            echo "<p style='color: green;'>✅ Static activate method exists</p>\n";
        } else {
            echo "<p style='color: red;'>❌ Static activate method missing</p>\n";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Main plugin class not found</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading plugin: " . $e->getMessage() . "</p>\n";
} catch (ParseError $e) {
    echo "<p style='color: red;'>❌ Parse error in plugin: " . $e->getMessage() . "</p>\n";
}

$output = ob_get_clean();
error_reporting($error_level);

echo $output;

echo "<h2>Plugin Status Summary</h2>\n";
echo "<p>If all checks pass, the plugin should activate without errors.</p>\n";
echo "<p><strong>Next step:</strong> Try activating the plugin through WordPress admin.</p>\n";
?>

