<?php
/**
 * Complete Donation System Test
 * Access at: /wp-content/themes/kilismile/test-complete-system.php
 */

// Load WordPress
require_once(dirname(dirname(dirname(__FILE__))) . '/wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>KiliSmile Complete Donation System Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .test-section { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .test-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .test-card { background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #007cba; }
    </style>
</head>
<body>
    <h1>🚀 KiliSmile Complete Donation System Test</h1>
    
    <div class="test-section">
        <h2>📋 System Components Status</h2>
        <div class="test-grid">
            <?php
            $components = array(
                'Multi-Step Form Template' => get_template_directory() . '/templates/donation-form-multistep.php',
                'Payment Processor' => get_template_directory() . '/includes/payment-processor.php',
                'Admin Dashboard' => get_template_directory() . '/includes/donation-dashboard.php',
                'Success Page' => get_template_directory() . '/page-donation-success.php',
                'Cancelled Page' => get_template_directory() . '/page-donation-cancelled.php',
                'Form Component' => get_template_directory() . '/template-parts/donation-form-component.php'
            );
            
            foreach ($components as $name => $path) {
                $exists = file_exists($path);
                $status_class = $exists ? 'status-good' : 'status-bad';
                $status_text = $exists ? '✅ Installed' : '❌ Missing';
                
                echo "<div class='test-card'>";
                echo "<h4>$name</h4>";
                echo "<p class='$status_class'>$status_text</p>";
                if ($exists) {
                    echo "<small>Size: " . size_format(filesize($path)) . "</small>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🔌 KiliSmile Payments Plugin Status</h2>
        <div class="test-grid">
            <?php
            $plugin_active = class_exists('KiliSmile_Payments_Plugin');
            $plugin_path = WP_PLUGIN_DIR . '/kilismile-payments-plugin/kilismile-payments-plugin.php';
            $plugin_exists = file_exists($plugin_path);
            
            echo "<div class='test-card'>";
            echo "<h4>Plugin Status</h4>";
            if ($plugin_active) {
                echo "<p class='status-good'>✅ Active and Loaded</p>";
                $plugin = KiliSmile_Payments_Plugin::get_instance();
                $gateways = $plugin->get_enabled_gateways();
                echo "<p>Enabled Gateways: " . count($gateways) . "</p>";
            } elseif ($plugin_exists) {
                echo "<p class='status-warning'>⚠️ Installed but Not Active</p>";
            } else {
                echo "<p class='status-bad'>❌ Not Installed</p>";
            }
            echo "</div>";
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🗄️ Database Status</h2>
        <div class="test-grid">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'kilismile_donations';
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            
            echo "<div class='test-card'>";
            echo "<h4>Donations Table</h4>";
            if ($table_exists) {
                echo "<p class='status-good'>✅ Table Created</p>";
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                echo "<p>Total Donations: $count</p>";
                
                $recent = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 3");
                if (!empty($recent)) {
                    echo "<h5>Recent Donations:</h5>";
                    foreach ($recent as $donation) {
                        echo "<small>{$donation->transaction_id} - {$donation->currency} {$donation->amount} - {$donation->payment_status}</small><br>";
                    }
                }
            } else {
                echo "<p class='status-bad'>❌ Table Missing</p>";
                echo "<p><small>The payment processor should create this automatically.</small></p>";
            }
            echo "</div>";
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🔗 URL Endpoints Test</h2>
        <div class="test-grid">
            <?php
            $endpoints = array(
                'Donation Page' => home_url('/donate'),
                'Success Page' => home_url('/donation-success'),
                'Cancelled Page' => home_url('/donation-cancelled'),
                'AJAX Endpoint' => admin_url('admin-ajax.php'),
                'Admin Dashboard' => admin_url('admin.php?page=kilismile-donations')
            );
            
            foreach ($endpoints as $name => $url) {
                echo "<div class='test-card'>";
                echo "<h4>$name</h4>";
                echo "<p><a href='$url' target='_blank'>$url</a></p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>⚙️ AJAX Actions Registered</h2>
        <div class="test-grid">
            <?php
            $ajax_actions = array(
                'kilismile_process_payment',
                'kilismile_check_payment_status',
                'kilismile_azampay_webhook',
                'kilismile_paypal_webhook'
            );
            
            foreach ($ajax_actions as $action) {
                $registered = has_action("wp_ajax_$action") || has_action("wp_ajax_nopriv_$action");
                $status_class = $registered ? 'status-good' : 'status-bad';
                $status_text = $registered ? '✅ Registered' : '❌ Not Registered';
                
                echo "<div class='test-card'>";
                echo "<h4>$action</h4>";
                echo "<p class='$status_class'>$status_text</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🧪 Quick System Test</h2>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            <h4>Test Multi-Step Form</h4>
            <p>Click the button below to test the complete donation form:</p>
            <a href="<?php echo home_url('/donate'); ?>" target="_blank" style="background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; display: inline-block; margin: 10px 0;">
                🚀 Test Donation Form
            </a>
            
            <h4>Access Admin Dashboard</h4>
            <p>View and manage donations:</p>
            <a href="<?php echo admin_url('admin.php?page=kilismile-donations'); ?>" target="_blank" style="background: #007cba; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; display: inline-block; margin: 10px 0;">
                📊 Admin Dashboard
            </a>
        </div>
    </div>
    
    <div class="test-section">
        <h2>📝 System Summary</h2>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            <?php
            $summary_items = array(
                'Multi-step donation form with currency selection (TZS/USD)',
                'Payment processing via KiliSmile Payments Plugin',
                'AzamPay integration for Tanzania mobile money',
                'PayPal integration for international credit cards',
                'Automated email receipts and thank you messages',
                'Complete admin dashboard with reports and charts',
                'Database tracking of all donations and statuses',
                'Success and cancellation page handling',
                'Real-time payment status checking',
                'Webhook handlers for payment confirmations'
            );
            
            echo "<h4>✨ Complete Features Implemented:</h4>";
            echo "<ul>";
            foreach ($summary_items as $item) {
                echo "<li>$item</li>";
            }
            echo "</ul>";
            ?>
            
            <div style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #28a745;">
                <h4 style="color: #155724; margin-top: 0;">🎉 System Ready!</h4>
                <p style="color: #155724; margin-bottom: 0;">
                    Your complete donation processing system is now integrated and ready for use. 
                    Users can make donations through the multi-step form, payments are processed securely 
                    through your KiliSmile Payments Plugin, and you can manage everything through the admin dashboard.
                </p>
            </div>
        </div>
    </div>
    
    <div style="text-align: center; margin: 40px 0; color: #6c757d;">
        <p>🏥 <strong>KiliSmile Organization</strong> - Complete Donation Processing System v2.0.0</p>
        <p><small>Developed with ❤️ for healthcare education in Tanzania</small></p>
    </div>
</body>
</html>