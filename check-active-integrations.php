<?php
/**
 * Check Active Payment Integrations
 * This file shows which payment gateways are currently active and configured
 */

$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}
require_once $wp_load_path;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Active Payment Integrations</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .gateway { margin: 20px 0; padding: 20px; border-radius: 8px; border-left: 5px solid #ccc; }
        .active { border-left-color: #28a745; background: #d4edda; }
        .inactive { border-left-color: #dc3545; background: #f8d7da; }
        .partial { border-left-color: #ffc107; background: #fff3cd; }
        .status { font-weight: bold; margin-bottom: 10px; }
        .details { margin-left: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 20px 0; }
        h1, h2 { color: #333; }
        .summary { background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 Active Payment Integrations Status</h1>
        
        <?php
        // Check plugin status
        $plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE');
        $plugin_loaded = class_exists('KiliSmile_Payment_Processor');
        
        echo '<div class="summary">';
        echo '<h2>Payment System Overview</h2>';
        echo '<p><strong>Payment Plugin:</strong> ' . ($plugin_active ? '<span class="success">✅ Active</span>' : '<span class="error">❌ Not Active</span>') . '</p>';
        echo '<p><strong>Payment Classes:</strong> ' . ($plugin_loaded ? '<span class="success">✅ Loaded</span>' : '<span class="error">❌ Not Loaded</span>') . '</p>';
        echo '</div>';
        
        // PayPal Gateway Check
        $paypal_enabled = get_option('kilismile_paypal_enabled', false);
        $paypal_client_id = get_option('kilismile_paypal_client_id', '');
        $paypal_client_secret = get_option('kilismile_paypal_client_secret', '');
        $paypal_sandbox = get_option('kilismile_paypal_sandbox', true);
        
        $paypal_configured = !empty($paypal_client_id) && !empty($paypal_client_secret);
        
        if ($paypal_enabled && $paypal_configured) {
            $paypal_status = 'active';
            $paypal_message = 'Active and Configured';
        } elseif ($paypal_enabled && !$paypal_configured) {
            $paypal_status = 'partial';
            $paypal_message = 'Enabled but Missing Credentials';
        } else {
            $paypal_status = 'inactive';
            $paypal_message = 'Disabled';
        }
        
        echo '<div class="gateway ' . $paypal_status . '">';
        echo '<div class="status">💳 PayPal Gateway: ' . $paypal_message . '</div>';
        echo '<div class="details">';
        echo '• Enabled: ' . ($paypal_enabled ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . '<br>';
        echo '• Client ID: ' . (!empty($paypal_client_id) ? '<span class="success">Configured</span>' : '<span class="error">Missing</span>') . '<br>';
        echo '• Client Secret: ' . (!empty($paypal_client_secret) ? '<span class="success">Configured</span>' : '<span class="error">Missing</span>') . '<br>';
        echo '• Environment: <span class="info">' . ($paypal_sandbox ? 'Sandbox (Test)' : 'Live (Production)') . '</span><br>';
        echo '</div>';
        echo '</div>';
        
        // AzamPay Gateway Check (Theme Integration)
        $azampay_enabled = get_option('kilismile_azampay_enabled', false);
        $azampay_client_id = get_option('kilismile_azampay_client_id', '');
        $azampay_client_secret = get_option('kilismile_azampay_client_secret', '');
        $azampay_app_name = get_option('kilismile_azampay_app_name', '');
        $azampay_sandbox = get_option('kilismile_azampay_sandbox_mode', true);
        $enhanced_azampay = get_option('kilismile_use_enhanced_azampay', false);
        
        $azampay_configured = !empty($azampay_client_id) && !empty($azampay_client_secret);
        
        if ($azampay_enabled && $azampay_configured) {
            $azampay_status = 'active';
            $azampay_message = 'Active and Configured';
        } elseif ($azampay_enabled && !$azampay_configured) {
            $azampay_status = 'partial';
            $azampay_message = 'Enabled but Missing Credentials';
        } else {
            $azampay_status = 'inactive';
            $azampay_message = 'Disabled';
        }
        
        echo '<div class="gateway ' . $azampay_status . '">';
        echo '<div class="status">📱 AzamPay Gateway (Theme): ' . $azampay_message . '</div>';
        echo '<div class="details">';
        echo '• Enabled: ' . ($azampay_enabled ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . '<br>';
        echo '• Client ID: ' . (!empty($azampay_client_id) ? '<span class="success">Configured</span>' : '<span class="error">Missing</span>') . '<br>';
        echo '• Client Secret: ' . (!empty($azampay_client_secret) ? '<span class="success">Configured</span>' : '<span class="error">Missing</span>') . '<br>';
        echo '• App Name: ' . (!empty($azampay_app_name) ? '<span class="success">' . esc_html($azampay_app_name) . '</span>' : '<span class="warning">Not Set</span>') . '<br>';
        echo '• Environment: <span class="info">' . ($azampay_sandbox ? 'Sandbox (Test)' : 'Live (Production)') . '</span><br>';
        echo '• Enhanced Mode: ' . ($enhanced_azampay ? '<span class="success">Enabled</span>' : '<span class="info">Disabled</span>') . '<br>';
        echo '</div>';
        echo '</div>';
        
        // AzamPay Plugin Check
        $azampay_plugin_active = class_exists('AzamPay_Gateway');
        $azampay_plugin_options = get_option('azampay_gateway_options', array());
        
        if ($azampay_plugin_active) {
            $plugin_sandbox = isset($azampay_plugin_options['sandbox_mode']) ? $azampay_plugin_options['sandbox_mode'] : true;
            $plugin_client_id = isset($azampay_plugin_options['client_id']) ? $azampay_plugin_options['client_id'] : '';
            $plugin_configured = !empty($plugin_client_id);
            
            if ($plugin_configured) {
                $plugin_status = 'active';
                $plugin_message = 'Active and Configured';
            } else {
                $plugin_status = 'partial';
                $plugin_message = 'Active but Not Configured';
            }
            
            echo '<div class="gateway ' . $plugin_status . '">';
            echo '<div class="status">🔌 AzamPay Plugin: ' . $plugin_message . '</div>';
            echo '<div class="details">';
            echo '• Plugin Active: <span class="success">Yes</span><br>';
            echo '• Client ID: ' . (!empty($plugin_client_id) ? '<span class="success">Configured</span>' : '<span class="error">Missing</span>') . '<br>';
            echo '• Environment: <span class="info">' . ($plugin_sandbox ? 'Sandbox (Test)' : 'Live (Production)') . '</span><br>';
            echo '• Admin URL: <a href="' . admin_url('admin.php?page=azampay-settings') . '" target="_blank">Configure Plugin</a><br>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="gateway inactive">';
            echo '<div class="status">🔌 AzamPay Plugin: Not Active</div>';
            echo '<div class="details">';
            echo '• Plugin Status: <span class="error">Not Installed/Activated</span><br>';
            echo '• Location: <code>wp-content/plugins/azampay-payment-gateway/</code><br>';
            echo '</div>';
            echo '</div>';
        }
        
        // Other Gateways Check
        $selcom_enabled = get_option('kilismile_selcom_enabled', false);
        $stripe_enabled = get_option('kilismile_stripe_enabled', false);
        
        if ($selcom_enabled || $stripe_enabled) {
            echo '<div class="gateway partial">';
            echo '<div class="status">🏪 Other Gateways</div>';
            echo '<div class="details">';
            echo '• Selcom: ' . ($selcom_enabled ? '<span class="warning">Enabled</span>' : '<span class="info">Disabled</span>') . '<br>';
            echo '• Stripe: ' . ($stripe_enabled ? '<span class="warning">Enabled</span>' : '<span class="info">Disabled</span>') . '<br>';
            echo '</div>';
            echo '</div>';
        }
        
        // Summary and Recommendations
        echo '<div class="info">';
        echo '<h2>📋 Summary & Recommendations</h2>';
        
        $active_gateways = 0;
        if ($paypal_enabled && $paypal_configured) $active_gateways++;
        if ($azampay_enabled && $azampay_configured) $active_gateways++;
        if ($azampay_plugin_active && $plugin_configured) $active_gateways++;
        
        echo '<p><strong>Active Payment Gateways:</strong> ' . $active_gateways . '</p>';
        
        if ($active_gateways == 0) {
            echo '<p><span class="error">⚠️ No payment gateways are fully configured!</span></p>';
            echo '<p><strong>Action needed:</strong> Configure at least one payment gateway to accept donations.</p>';
        } elseif ($active_gateways == 1) {
            echo '<p><span class="success">✅ One payment gateway is active.</span></p>';
            echo '<p><strong>Recommendation:</strong> Consider adding a second gateway for backup and user choice.</p>';
        } else {
            echo '<p><span class="success">✅ Multiple payment gateways are active - excellent redundancy!</span></p>';
        }
        
        // Show which integration is recommended
        if ($azampay_plugin_active && $azampay_enabled) {
            echo '<div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin-top: 15px;">';
            echo '<p><strong>⚠️ Notice:</strong> You have both AzamPay theme integration and plugin active.</p>';
            echo '<p><strong>Recommendation:</strong> Use the plugin for better updates and support, disable theme integration.</p>';
            echo '</div>';
        }
        
        echo '</div>';
        ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Theme Settings</a>
            <?php if ($azampay_plugin_active): ?>
            <a href="<?php echo admin_url('admin.php?page=azampay-settings'); ?>" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">AzamPay Plugin Settings</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

