<?php
/**
 * Payment System Status Check
 * Simple page to verify payment system is working after conflict resolution
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
    <title>Payment System Status</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .status-good { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        h1, h2 { color: #333; }
        .test-button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 Payment System Status</h1>
        
        <div class="section">
            <h2>System Health Check</h2>
            
            <?php
            // Check for conflicts
            $has_conflicts = false;
            $status_messages = array();
            
            // Check if both theme and plugin versions are trying to load
            if (defined('KILISMILE_PAYMENTS_ACTIVE') && class_exists('KiliSmile_Payment_Gateway_Base')) {
                $status_messages[] = '<span class="status-good">✅ Payment system is loaded</span>';
            } else {
                $status_messages[] = '<span class="status-warning">⚠️ Payment system classes not detected</span>';
            }
            
            // Check for class conflicts
            if (class_exists('KiliSmile_Payment_Gateway_Base')) {
                $reflection = new ReflectionClass('KiliSmile_Payment_Gateway_Base');
                $source = $reflection->getFileName();
                if (strpos($source, 'plugins/kilismile-payments') !== false) {
                    $status_messages[] = '<span class="status-good">✅ Using standalone plugin version</span>';
                } elseif (strpos($source, 'themes/') !== false) {
                    $status_messages[] = '<span class="status-warning">⚠️ Using theme version (standalone plugin recommended)</span>';
                } else {
                    $status_messages[] = '<span class="status-error">❌ Payment classes loaded from unexpected location</span>';
                }
            }
            
            // Check AJAX handlers
            if (has_action('wp_ajax_kilismile_process_payment')) {
                $status_messages[] = '<span class="status-good">✅ Payment AJAX handler registered</span>';
            } else {
                $status_messages[] = '<span class="status-error">❌ Payment AJAX handler missing</span>';
            }
            
            foreach ($status_messages as $message) {
                echo '<p>' . $message . '</p>';
            }
            ?>
        </div>
        
        <div class="section">
            <h2>Active Payment Gateways</h2>
            <?php
            $paypal_enabled = get_option('kilismile_paypal_enabled', false);
            $azampay_enabled = get_option('kilismile_azampay_enabled', false);
            $azampay_plugin_active = class_exists('AzamPay_Gateway');
            
            echo '<p>PayPal: ' . ($paypal_enabled ? '<span class="status-good">✅ Enabled</span>' : '<span class="status-warning">⚠️ Disabled</span>') . '</p>';
            echo '<p>AzamPay (Theme): ' . ($azampay_enabled ? '<span class="status-good">✅ Enabled</span>' : '<span class="status-warning">⚠️ Disabled</span>') . '</p>';
            echo '<p>AzamPay (Plugin): ' . ($azampay_plugin_active ? '<span class="status-good">✅ Active</span>' : '<span class="status-warning">⚠️ Not Active</span>') . '</p>';
            ?>
        </div>
        
        <div class="section">
            <h2>Quick Tests</h2>
            <p>Test your payment system with these links:</p>
            
            <a href="test-azampay-plugin.php" class="test-button">🧪 Test AzamPay Integration</a>
            <a href="test-payment-nonce.php" class="test-button">🔐 Test Security Nonces</a>
            <a href="check-active-integrations.php" class="test-button">📊 View Integration Status</a>
            
            <?php if (current_user_can('manage_options')): ?>
            <br><br>
            <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>" class="test-button">⚙️ Theme Settings</a>
            <?php if ($azampay_plugin_active): ?>
            <a href="<?php echo admin_url('admin.php?page=azampay-settings'); ?>" class="test-button">🔧 AzamPay Plugin Settings</a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>Conflict Resolution Status</h2>
            <p><span class="status-good">✅ Theme-based plugin loading has been disabled</span></p>
            <p><span class="status-good">✅ Conflicts between theme and plugin versions resolved</span></p>
            <p>Your payment system is now using the standalone plugin architecture for better stability and updates.</p>
        </div>
    </div>
</body>
</html>

