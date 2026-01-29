<?php
/**
 * Payment System Test Script
 * Run this to test if the payment integration is working
 */

// WordPress bootstrap
require_once dirname(__FILE__) . '/../../../../wp-load.php';

if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

// Test output
echo "<h1>KiliSmile Payment System Test</h1>";

// Test 1: Check if classes exist
echo "<h2>1. Class Availability Test</h2>";
$classes = array(
    'KiliSmile_Payment_Processor',
    'KiliSmile_PayPal_Integration', 
    'KiliSmile_AzamPay_Integration',
    'KiliSmile_Donation_Database'
);

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✅ {$class} - Available<br>";
    } else {
        echo "❌ {$class} - Missing<br>";
    }
}

// Test 2: Check AJAX handlers
echo "<h2>2. AJAX Handler Registration Test</h2>";
$actions = array(
    'kilismile_process_payment',
    'kilismile_check_payment_status'
);

foreach ($actions as $action) {
    if (has_action("wp_ajax_{$action}")) {
        echo "✅ wp_ajax_{$action} - Registered<br>";
    } else {
        echo "❌ wp_ajax_{$action} - Not registered<br>";
    }
    
    if (has_action("wp_ajax_nopriv_{$action}")) {
        echo "✅ wp_ajax_nopriv_{$action} - Registered<br>";
    } else {
        echo "❌ wp_ajax_nopriv_{$action} - Not registered<br>";
    }
}

// Test 3: Database tables
echo "<h2>3. Database Table Test</h2>";
global $wpdb;
$donations_table = $wpdb->prefix . 'donations';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$donations_table}'") == $donations_table;
if ($table_exists) {
    echo "✅ Donations table exists<br>";
    
    // Check table structure
    $columns = $wpdb->get_results("DESCRIBE {$donations_table}");
    echo "<details><summary>Table structure</summary>";
    echo "<pre>";
    foreach ($columns as $column) {
        echo "{$column->Field} - {$column->Type}<br>";
    }
    echo "</pre></details>";
} else {
    echo "❌ Donations table missing<br>";
    echo "Run the database creation script or activate the theme to create tables.<br>";
}

// Test 4: Settings
echo "<h2>4. Payment Gateway Settings Test</h2>";
$paypal_enabled = get_option('kilismile_paypal_enabled', false);
$azampay_enabled = get_option('kilismile_azampay_enabled', false);

echo ($paypal_enabled ? "✅" : "❌") . " PayPal Enabled: " . ($paypal_enabled ? 'Yes' : 'No') . "<br>";
echo ($azampay_enabled ? "✅" : "❌") . " AzamPay Enabled: " . ($azampay_enabled ? 'Yes' : 'No') . "<br>";

// PayPal credentials check
$paypal_client_id = get_option('kilismile_paypal_client_id', '');
$paypal_client_secret = get_option('kilismile_paypal_client_secret', '');
echo (!empty($paypal_client_id) ? "✅" : "⚠️") . " PayPal Client ID: " . (!empty($paypal_client_id) ? 'Set' : 'Not set') . "<br>";
echo (!empty($paypal_client_secret) ? "✅" : "⚠️") . " PayPal Client Secret: " . (!empty($paypal_client_secret) ? 'Set' : 'Not set') . "<br>";

// AzamPay credentials check  
$azampay_client_id = get_option('kilismile_azampay_client_id', '');
$azampay_client_secret = get_option('kilismile_azampay_client_secret', '');
echo (!empty($azampay_client_id) ? "✅" : "⚠️") . " AzamPay Client ID: " . (!empty($azampay_client_id) ? 'Set' : 'Not set') . "<br>";
echo (!empty($azampay_client_secret) ? "✅" : "⚠️") . " AzamPay Client Secret: " . (!empty($azampay_client_secret) ? 'Set' : 'Not set') . "<br>";

// Test 5: Simulate payment processing
echo "<h2>5. Payment Processing Test</h2>";

if (class_exists('KiliSmile_Payment_Processor')) {
    echo "🔄 Testing payment processor initialization...<br>";
    try {
        $processor = new KiliSmile_Payment_Processor();
        echo "✅ Payment processor initialized successfully<br>";
    } catch (Exception $e) {
        echo "❌ Payment processor initialization failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Cannot test payment processor - class not found<br>";
}

// Test 6: Shortcode test
echo "<h2>6. Shortcode Test</h2>";
if (shortcode_exists('kilismile_payment_form')) {
    echo "✅ Payment form shortcode registered<br>";
    echo "<p>You can use: <code>[kilismile_payment_form]</code></p>";
} else {
    echo "❌ Payment form shortcode not registered<br>";
}

// Test 7: File existence
echo "<h2>7. File Existence Test</h2>";
$required_files = array(
    'includes/payment-processor.php',
    'includes/azampay-integration.php', 
    'includes/paypal-integration.php',
    'includes/payment-form-handler.php',
    'template-parts/payment-form.php',
    'assets/css/payment-form.css',
    'assets/js/payment-form.js'
);

foreach ($required_files as $file) {
    $full_path = get_template_directory() . '/' . $file;
    if (file_exists($full_path)) {
        echo "✅ {$file}<br>";
    } else {
        echo "❌ {$file} - Missing<br>";
    }
}

echo "<h2>Summary</h2>";
echo "<p>If all tests show ✅, your payment system is ready to use!</p>";
echo "<p>If you see ❌ or ⚠️, please address those issues before testing payments.</p>";
echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Configure payment gateway credentials in Admin → Enhanced Theme Settings → Payments</li>";
echo "<li>Create a test page with the payment form shortcode</li>";
echo "<li>Test with sandbox/test credentials first</li>";
echo "<li>Test both USD (PayPal) and TZS (AzamPay) payments</li>";
echo "</ol>";
?>

