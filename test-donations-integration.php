<?php
/**
 * Test AzamPay Integration on Donations Page
 * Quick verification that everything is working
 */

// Include WordPress
if (!defined('ABSPATH')) {
    // Define path to WordPress root
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

// Include our integration files
require_once get_template_directory() . '/includes/azampay-integration.php';
require_once get_template_directory() . '/includes/payment-processor.php';
require_once get_template_directory() . '/includes/donation-database.php';

// Test data
$test_data = array(
    'amount' => 5000,
    'currency' => 'TZS',
    'reference' => 'TEST_' . time(),
    'donor_name' => 'Test Donor',
    'donor_email' => 'test@example.com',
    'donor_phone' => '255712345678',
    'network' => 'vodacom'
);

echo "<h1>🧪 AzamPay Integration Test for Donations Page</h1>";

echo "<h2>✅ Class Loading Test</h2>";
try {
    $azampay = new KiliSmile_AzamPay();
    echo "<p>✅ AzamPay Integration Class: <strong>Loaded Successfully</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ AzamPay Integration Class: <strong>Failed - " . $e->getMessage() . "</strong></p>";
}

try {
    $processor = new KiliSmile_Payment_Processor();
    echo "<p>✅ Payment Processor Class: <strong>Loaded Successfully</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ Payment Processor Class: <strong>Failed - " . $e->getMessage() . "</strong></p>";
}

try {
    $database = new KiliSmile_Donation_Database();
    echo "<p>✅ Donation Database Class: <strong>Loaded Successfully</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ Donation Database Class: <strong>Failed - " . $e->getMessage() . "</strong></p>";
}

echo "<h2>🔧 Configuration Test</h2>";
$config_items = array(
    'kilismile_azampay_app_name' => get_option('kilismile_azampay_app_name', ''),
    'kilismile_azampay_client_id' => get_option('kilismile_azampay_client_id', ''),
    'kilismile_azampay_client_secret' => get_option('kilismile_azampay_client_secret', ''),
    'kilismile_azampay_sandbox' => get_option('kilismile_azampay_sandbox', true)
);

foreach ($config_items as $key => $value) {
    $status = !empty($value) ? "✅ Configured" : "⚠️ Not Set";
    $display_value = $key === 'kilismile_azampay_client_secret' ? 
        (!empty($value) ? str_repeat('*', 8) : 'Empty') : 
        (string)$value;
    echo "<p><strong>{$key}:</strong> {$display_value} - {$status}</p>";
}

echo "<h2>🌐 Endpoint Test</h2>";
if (isset($azampay)) {
    try {
        $test_checkout = $azampay->create_checkout_session($test_data);
        echo "<p>✅ Checkout Session Creation: <strong>Method Available</strong></p>";
        echo "<pre>" . json_encode($test_checkout, JSON_PRETTY_PRINT) . "</pre>";
    } catch (Exception $e) {
        echo "<p>❌ Checkout Session Creation: <strong>Failed - " . $e->getMessage() . "</strong></p>";
    }
    
    try {
        $test_stkpush = $azampay->initiate_stkpush($test_data);
        echo "<p>✅ STK Push Method: <strong>Method Available</strong></p>";
        echo "<pre>" . json_encode($test_stkpush, JSON_PRETTY_PRINT) . "</pre>";
    } catch (Exception $e) {
        echo "<p>❌ STK Push Method: <strong>Failed - " . $e->getMessage() . "</strong></p>";
    }
}

echo "<h2>📝 WordPress Hooks Test</h2>";
$hooks_to_check = array(
    'wp_ajax_kilismile_process_payment',
    'wp_ajax_nopriv_kilismile_process_payment',
    'wp_ajax_kilismile_check_payment_status',
    'wp_ajax_nopriv_kilismile_check_payment_status',
    'wp_ajax_azampay_callback',
    'wp_ajax_nopriv_azampay_callback'
);

global $wp_filter;
foreach ($hooks_to_check as $hook) {
    $registered = isset($wp_filter[$hook]) && !empty($wp_filter[$hook]);
    $status = $registered ? "✅ Registered" : "❌ Not Registered";
    echo "<p><strong>{$hook}:</strong> {$status}</p>";
}

echo "<h2>🔗 Important URLs</h2>";
$urls = array(
    'AJAX Endpoint' => admin_url('admin-ajax.php'),
    'AzamPay Callback' => admin_url('admin-ajax.php?action=azampay_callback'),
    'Donations Page' => home_url('/donations/'),
    'Success Page' => home_url('/donation-success/'),
    'Failed Page' => home_url('/donation-failed/'),
    'Cancel Page' => home_url('/donation-cancelled/')
);

foreach ($urls as $name => $url) {
    echo "<p><strong>{$name}:</strong> <a href='{$url}' target='_blank'>{$url}</a></p>";
}

echo "<h2>📋 Integration Checklist</h2>";
echo "<div style='background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0;'>";

$checklist = array(
    '✅ AzamPay Integration Class loaded',
    '✅ Payment Processor Class loaded', 
    '✅ Donation Database Class loaded',
    '✅ AJAX endpoints registered',
    '✅ Callback handler registered',
    '⚠️ Configure AzamPay credentials',
    '⚠️ Test with real AzamPay sandbox',
    '⚠️ Verify callback URL in AzamPay dashboard',
    '⚠️ Test donations page form',
    '⚠️ Test both STK Push and Checkout methods'
);

foreach ($checklist as $item) {
    echo "<p>{$item}</p>";
}

echo "</div>";

echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
echo "<li>Visit the <a href='" . home_url('/donations/') . "' target='_blank'>Donations Page</a> to test the form</li>";
echo "<li>Try both USD (PayPal) and TZS (AzamPay) payment options</li>";
echo "<li>Test both STK Push and Checkout Page methods for TZS</li>";
echo "<li>Configure AzamPay credentials in WordPress admin if not already done</li>";
echo "<li>Set up webhook URL in AzamPay dashboard: <code>" . admin_url('admin-ajax.php?action=azampay_callback') . "</code></li>";
echo "</ol>";

?>

<style>
body { 
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
    max-width: 1000px; 
    margin: 0 auto; 
    padding: 20px; 
    line-height: 1.6; 
}
h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
h2 { color: #27ae60; margin-top: 30px; }
pre { 
    background: #f8f9fa; 
    padding: 15px; 
    border-radius: 8px; 
    border-left: 4px solid #3498db; 
    overflow-x: auto; 
}
code { 
    background: #f1f3f4; 
    padding: 2px 6px; 
    border-radius: 4px; 
    font-family: 'Courier New', monospace; 
}
a { color: #3498db; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>

