<?php
/**
 * Plugin Status Check - Browser Version
 * Access this via browser to check plugin status
 */

// Set up WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-config.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>AzamPay Plugin Status</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .code { background: #f8f9fa; padding: 10px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔌 AzamPay Plugin Status Check</h1>
    
    <?php
    // Check if plugin file exists
    $plugin_file = WP_PLUGIN_DIR . '/azampay-payment-gateway/azampay-payment-gateway.php';
    echo "<h2>1. Plugin File Check</h2>";
    
    if (file_exists($plugin_file)) {
        echo "<div class='status success'>✅ Plugin file exists</div>";
        echo "<div class='code'>Path: $plugin_file</div>";
    } else {
        echo "<div class='status error'>❌ Plugin file not found</div>";
        echo "<div class='code'>Expected path: $plugin_file</div>";
        exit;
    }
    
    // Check if plugin is listed in WordPress
    echo "<h2>2. WordPress Plugin Detection</h2>";
    
    if (!function_exists('get_plugins')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    $all_plugins = get_plugins();
    $plugin_key = 'azampay-payment-gateway/azampay-payment-gateway.php';
    
    if (isset($all_plugins[$plugin_key])) {
        echo "<div class='status success'>✅ Plugin detected by WordPress</div>";
        echo "<div class='code'>";
        echo "Name: " . $all_plugins[$plugin_key]['Name'] . "<br>";
        echo "Version: " . $all_plugins[$plugin_key]['Version'] . "<br>";
        echo "Description: " . $all_plugins[$plugin_key]['Description'];
        echo "</div>";
    } else {
        echo "<div class='status warning'>⚠️ Plugin not detected by WordPress</div>";
        echo "<div class='code'>Available plugins:<br>";
        foreach ($all_plugins as $key => $plugin) {
            echo "- $key: {$plugin['Name']}<br>";
        }
        echo "</div>";
    }
    
    // Check if plugin is active
    echo "<h2>3. Plugin Activation Status</h2>";
    
    if (is_plugin_active($plugin_key)) {
        echo "<div class='status success'>✅ Plugin is active</div>";
    } else {
        echo "<div class='status warning'>⚠️ Plugin is not active</div>";
        
        // Try to activate it
        echo "<h3>Attempting Activation</h3>";
        
        $result = activate_plugin($plugin_key);
        
        if (is_wp_error($result)) {
            echo "<div class='status error'>❌ Activation failed</div>";
            echo "<div class='code'>Error: " . $result->get_error_message() . "</div>";
        } else {
            echo "<div class='status success'>✅ Plugin activated successfully!</div>";
        }
    }
    
    // Check if plugin class is available
    echo "<h2>4. Plugin Class Check</h2>";
    
    if (class_exists('AzamPay_Gateway_Plugin')) {
        echo "<div class='status success'>✅ Main plugin class available</div>";
        
        // Test getting instance
        try {
            $instance = AzamPay_Gateway_Plugin::get_instance();
            echo "<div class='status success'>✅ Plugin instance created</div>";
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Failed to create instance: " . $e->getMessage() . "</div>";
        }
        
    } else {
        echo "<div class='status warning'>⚠️ Main plugin class not loaded</div>";
    }
    
    // Test plugin settings
    echo "<h2>5. Plugin Settings</h2>";
    
    $options = get_option('azampay_gateway_options', false);
    if ($options) {
        echo "<div class='status success'>✅ Plugin options exist</div>";
        echo "<div class='code'>";
        echo "App Name: " . ($options['app_name'] ?? 'Not set') . "<br>";
        echo "Environment: " . ($options['sandbox_mode'] ? 'Sandbox' : 'Live') . "<br>";
        echo "STK Push: " . ($options['enable_stkpush'] ? 'Enabled' : 'Disabled') . "<br>";
        echo "Checkout: " . ($options['enable_checkout'] ? 'Enabled' : 'Disabled');
        echo "</div>";
    } else {
        echo "<div class='status warning'>⚠️ Plugin options not found</div>";
    }
    
    // Next steps
    echo "<h2>📋 Next Steps</h2>";
    
    if (is_plugin_active($plugin_key)) {
        echo "<p>✅ Plugin is active! You can now:</p>";
        echo "<ol>";
        echo "<li>Go to <a href='" . admin_url('admin.php?page=azampay-settings') . "'>AzamPay Settings</a></li>";
        echo "<li>Configure your API credentials</li>";
        echo "<li>Test the donation form on your <a href='" . home_url('/donate') . "'>donation page</a></li>";
        echo "</ol>";
    } else {
        echo "<p>⚠️ Plugin needs to be activated manually:</p>";
        echo "<ol>";
        echo "<li>Go to <a href='" . admin_url('plugins.php') . "'>WordPress Admin → Plugins</a></li>";
        echo "<li>Find 'AzamPay Payment Gateway' and click 'Activate'</li>";
        echo "<li>If activation fails, check the error message</li>";
        echo "</ol>";
    }
    ?>
    
    <hr>
    <p><em>Status checked at <?php echo date('Y-m-d H:i:s'); ?></em></p>
</body>
</html>

