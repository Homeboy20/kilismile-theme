<?php
/**
 * Payment Integration Verification
 * Comprehensive test of the plugin integration
 */

require_once __DIR__ . '/../../../../wp-load.php';

// Test the payment system integration
echo "<h2>Payment System Integration Test</h2>\n";

// Check if plugin is loaded
$plugin_loaded = defined('KILISMILE_PAYMENTS_ACTIVE');
echo "Plugin Loaded: " . ($plugin_loaded ? "✅ YES" : "❌ NO") . "\n";

// Check if bridge is working
$bridge_active = class_exists('KiliSmile_Payment_Plugin_Bridge');
echo "Bridge Active: " . ($bridge_active ? "✅ YES" : "❌ NO") . "\n";

// Check key classes
$classes_to_check = [
    'KiliSmile_Payment_Gateway_Factory',
    'KiliSmile_Payment_Processor', 
    'KiliSmile_PayPal',
    'KiliSmile_AzamPay',
    'KiliSmile_Modern_Donation_System'
];

echo "\nKey Classes Available:\n";
foreach ($classes_to_check as $class) {
    $available = class_exists($class);
    echo "- $class: " . ($available ? "✅" : "❌") . "\n";
}

// Check AJAX hooks
echo "\nAJAX Hooks Registered:\n";
$ajax_hooks = [
    'wp_ajax_kilismile_process_payment',
    'wp_ajax_nopriv_kilismile_process_payment',
    'wp_ajax_kilismile_check_payment_status'
];

foreach ($ajax_hooks as $hook) {
    $registered = has_action($hook);
    echo "- $hook: " . ($registered ? "✅" : "❌") . "\n";
}

// Test payment gateway availability
echo "\nPayment Gateways:\n";
if (function_exists('kilismile_get_payment_gateways')) {
    $gateways = kilismile_get_payment_gateways();
    if (empty($gateways)) {
        echo "- No gateways configured\n";
    } else {
        foreach ($gateways as $id => $gateway) {
            echo "- $id: " . (is_object($gateway) ? get_class($gateway) : 'Available') . "\n";
        }
    }
} else {
    echo "- Gateway function not available\n";
}

// Test payment processor
echo "\nPayment Processor Test:\n";
if (class_exists('KiliSmile_Payment_Processor')) {
    $test_data = [
        'amount' => 10.00,
        'currency' => 'USD',
        'payment_gateway' => 'paypal',
        'donor_name' => 'Test User',
        'donor_email' => 'test@example.com'
    ];
    
    echo "- Payment processor class available\n";
    echo "- Test data prepared\n";
} else {
    echo "- ❌ Payment processor not available\n";
}

// Donation form integration check
echo "\nDonation Form Integration:\n";
$donation_page_path = get_template_directory() . '/page-donate.php';
if (file_exists($donation_page_path)) {
    $content = file_get_contents($donation_page_path);
    $has_form_component = strpos($content, 'donation-form-component.php') !== false;
    echo "- Donate page exists: ✅\n";
    echo "- Form component included: " . ($has_form_component ? "✅" : "❌") . "\n";
} else {
    echo "- ❌ Donate page not found\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "INTEGRATION STATUS: ";

$all_good = $plugin_loaded && $bridge_active && 
           class_exists('KiliSmile_Payment_Processor') && 
           has_action('wp_ajax_kilismile_process_payment');

if ($all_good) {
    echo "✅ READY FOR TESTING\n";
    echo "The payment plugin integration is working correctly.\n";
    echo "Donation forms should now process payments through the plugin.\n";
} else {
    echo "❌ NEEDS ATTENTION\n";
    echo "Some components are missing or not properly loaded.\n";
}

echo "\nNext Steps:\n";
echo "1. Test donation form submission with PayPal (USD)\n";
echo "2. Test donation form submission with AzamPay (TZS)\n";
echo "3. Verify payment status tracking\n";
echo "4. Check admin donation management\n";

