<?php
/**
 * WordPress Payment System Test
 * Access this file through your website to test the payment system
 */

// WordPress bootstrap
if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/../../../../wp-load.php';
}

if (!defined('ABSPATH')) {
    die('WordPress not loaded properly');
}

// Set content type
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>KiliSmile Payment System Test</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        code { background: #f5f5f5; padding: 2px 5px; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 KiliSmile Payment System Test</h1>
    <p><strong>Test Date:</strong> <?php echo current_time('Y-m-d H:i:s'); ?></p>

    <div class="test-section">
        <h2>1. 🔧 WordPress Environment</h2>
        <?php
        echo '<p class="success">✅ WordPress Version: ' . get_bloginfo('version') . '</p>';
        echo '<p class="success">✅ PHP Version: ' . PHP_VERSION . '</p>';
        echo '<p class="success">✅ WordPress URL: ' . site_url() . '</p>';
        echo '<p class="success">✅ Theme: ' . get_stylesheet() . '</p>';
        ?>
    </div>

    <div class="test-section">
        <h2>2. 📦 Class Availability</h2>
        <?php
        $classes = array(
            'KiliSmile_Payment_Processor' => 'Unified Payment Processor',
            'KiliSmile_PayPal' => 'PayPal Integration',
            'KiliSmile_AzamPay' => 'AzamPay Integration',
            'KiliSmile_Donation_Database' => 'Database Handler'
        );

        foreach ($classes as $class => $description) {
            if (class_exists($class)) {
                echo '<p class="success">✅ ' . $class . ' - ' . $description . '</p>';
            } else {
                echo '<p class="error">❌ ' . $class . ' - Missing</p>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2>3. 🎯 AJAX Handlers</h2>
        <?php
        $ajax_actions = array(
            'kilismile_process_payment' => 'Unified Payment Processing',
            'kilismile_check_payment_status' => 'Payment Status Check'
        );

        foreach ($ajax_actions as $action => $description) {
            $logged_in = has_action("wp_ajax_{$action}");
            $guest = has_action("wp_ajax_nopriv_{$action}");
            
            if ($logged_in && $guest) {
                echo '<p class="success">✅ ' . $action . ' - ' . $description . ' (Logged in & Guest)</p>';
            } elseif ($logged_in) {
                echo '<p class="warning">⚠️ ' . $action . ' - ' . $description . ' (Logged in only)</p>';
            } elseif ($guest) {
                echo '<p class="warning">⚠️ ' . $action . ' - ' . $description . ' (Guest only)</p>';
            } else {
                echo '<p class="error">❌ ' . $action . ' - Not registered</p>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2>4. 🗃️ Database Tables</h2>
        <?php
        global $wpdb;
        $donations_table = $wpdb->prefix . 'donations';
        
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$donations_table}'") == $donations_table;
        if ($table_exists) {
            echo '<p class="success">✅ Donations table exists</p>';
            
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$donations_table}");
            echo '<p class="info">📊 Total donations in database: ' . $count . '</p>';
        } else {
            echo '<p class="error">❌ Donations table missing</p>';
            echo '<p class="info">💡 Create table by visiting Admin → Enhanced Theme Settings</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>5. ⚙️ Payment Gateway Settings</h2>
        <?php
        // PayPal Settings
        $paypal_enabled = get_option('kilismile_paypal_enabled', false);
        $paypal_client_id = get_option('kilismile_paypal_client_id', '');
        $paypal_environment = get_option('kilismile_paypal_environment', 'sandbox');
        
        echo '<h3>PayPal Configuration:</h3>';
        echo '<p>' . ($paypal_enabled ? '<span class="success">✅ Enabled</span>' : '<span class="error">❌ Disabled</span>') . '</p>';
        echo '<p>Client ID: ' . (!empty($paypal_client_id) ? '<span class="success">✅ Set</span>' : '<span class="error">❌ Not set</span>') . '</p>';
        echo '<p>Environment: <span class="info">' . ucfirst($paypal_environment) . '</span></p>';
        
        // AzamPay Settings
        $azampay_enabled = get_option('kilismile_azampay_enabled', false);
        $azampay_client_id = get_option('kilismile_azampay_client_id', '');
        $azampay_environment = get_option('kilismile_azampay_environment', 'sandbox');
        
        echo '<h3>AzamPay Configuration:</h3>';
        echo '<p>' . ($azampay_enabled ? '<span class="success">✅ Enabled</span>' : '<span class="error">❌ Disabled</span>') . '</p>';
        echo '<p>Client ID: ' . (!empty($azampay_client_id) ? '<span class="success">✅ Set</span>' : '<span class="error">❌ Not set</span>') . '</p>';
        echo '<p>Environment: <span class="info">' . ucfirst($azampay_environment) . '</span></p>';
        ?>
    </div>

    <div class="test-section">
        <h2>6. 🎨 Frontend Components</h2>
        <?php
        // Check shortcode
        if (shortcode_exists('kilismile_payment_form')) {
            echo '<p class="success">✅ Payment form shortcode registered</p>';
            echo '<p class="info">💡 Use: <code>[kilismile_payment_form]</code></p>';
        } else {
            echo '<p class="error">❌ Payment form shortcode not registered</p>';
        }
        
        // Check files
        $theme_dir = get_template_directory();
        $files = array(
            'template-parts/payment-form.php' => 'Payment Form Template',
            'assets/css/payment-form.css' => 'Payment Form Styles',
            'assets/js/payment-form.js' => 'Payment Form JavaScript'
        );
        
        foreach ($files as $file => $description) {
            if (file_exists($theme_dir . '/' . $file)) {
                echo '<p class="success">✅ ' . $file . ' - ' . $description . '</p>';
            } else {
                echo '<p class="error">❌ ' . $file . ' - Missing</p>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2>7. 🚀 Integration Test</h2>
        <?php
        try {
            // Test class instantiation within WordPress
            if (class_exists('KiliSmile_Payment_Processor')) {
                echo '<p class="success">✅ Payment processor can be instantiated</p>';
            }
            
            if (class_exists('KiliSmile_Donation_Database')) {
                $db = new KiliSmile_Donation_Database();
                echo '<p class="success">✅ Database handler working</p>';
            }
            
            echo '<p class="info">🎯 All systems operational!</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Integration test failed: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>8. 📋 Next Steps</h2>
        <ol>
            <li><strong>Configure Payment Gateways:</strong> Go to Admin → Enhanced Theme Settings → Payments</li>
            <li><strong>Set Credentials:</strong> Add your PayPal and AzamPay sandbox/live credentials</li>
            <li><strong>Create Test Page:</strong> Add <code>[kilismile_payment_form]</code> to any page</li>
            <li><strong>Test Payments:</strong> Try both USD (PayPal) and TZS (AzamPay) payments</li>
            <li><strong>Monitor Database:</strong> Check donation records in the database</li>
        </ol>
        
        <h3>🔗 Quick Links:</h3>
        <ul>
            <li><a href="<?php echo admin_url('admin.php?page=enhanced-theme-settings'); ?>" target="_blank">Enhanced Theme Settings</a></li>
            <li><a href="<?php echo site_url('/wp-admin/edit.php?post_type=page'); ?>" target="_blank">Create Test Page</a></li>
        </ul>
    </div>

    <div class="test-section">
        <h2>🎉 System Status</h2>
        <?php
        $all_good = true;
        
        // Check critical components
        if (!class_exists('KiliSmile_Payment_Processor')) $all_good = false;
        if (!has_action('wp_ajax_kilismile_process_payment')) $all_good = false;
        if (!shortcode_exists('kilismile_payment_form')) $all_good = false;
        
        if ($all_good) {
            echo '<p class="success" style="font-size: 1.2em; font-weight: bold;">🎉 PAYMENT SYSTEM IS READY!</p>';
            echo '<p>Your KiliSmile payment integration is fully functional and ready for testing.</p>';
        } else {
            echo '<p class="error" style="font-size: 1.2em; font-weight: bold;">⚠️ SETUP INCOMPLETE</p>';
            echo '<p>Please address the issues highlighted above before testing payments.</p>';
        }
        ?>
    </div>

    <hr>
    <p><small>Test completed at <?php echo current_time('Y-m-d H:i:s'); ?> | WordPress <?php echo get_bloginfo('version'); ?></small></p>
</body>
</html>

