<?php
/**
 * Test donation form submission
 * Debug what happens when the payment form is submitted
 */

// Include WordPress bootstrap
require_once('../../../wp-load.php');

echo "<h1>Donation Form Submission Test</h1>";

// Test if classes are loaded
if (class_exists('KiliSmile_Payment_Processor')) {
    echo "<p>✅ Payment Processor class is loaded</p>";
} else {
    echo "<p>❌ Payment Processor class NOT loaded</p>";
}

if (class_exists('KiliSmile_AzamPay')) {
    echo "<p>✅ AzamPay integration class is loaded</p>";
} else {
    echo "<p>❌ AzamPay integration class NOT loaded</p>";
}

// Test AJAX endpoints
echo "<h2>AJAX Endpoints Test</h2>";

// Simulate AJAX call data
$_POST = array(
    'action' => 'kilismile_process_payment',
    'amount' => 10000,
    'currency' => 'TZS',
    'recurring' => false,
    'donor_name' => 'Test User',
    'donor_email' => 'test@example.com',
    'donor_phone' => '0712345678',
    'anonymous' => false,
    'payment_gateway' => 'azampay',
    'use_checkout' => false,
    'mobile_network' => 'vodacom',
    'payment_phone' => '0712345678',
    'nonce' => wp_create_nonce('kilismile_payment_nonce')
);

echo "<p>Simulating payment form submission...</p>";
echo "<pre>POST data: " . print_r($_POST, true) . "</pre>";

// Check if processor can be instantiated
try {
    $processor = new KiliSmile_Payment_Processor();
    echo "<p>✅ Payment processor instantiated successfully</p>";
    
    // Check if AzamPay settings exist
    $settings = array(
        'app_name' => get_option('kilismile_azampay_app_name', 'NOT_SET'),
        'client_id' => get_option('kilismile_azampay_client_id', 'NOT_SET'),
        'client_secret' => get_option('kilismile_azampay_client_secret', 'NOT_SET'),
        'sandbox' => get_option('kilismile_azampay_sandbox', true)
    );
    
    echo "<h3>AzamPay Settings:</h3>";
    echo "<pre>";
    foreach ($settings as $key => $value) {
        if ($key === 'client_secret' && $value !== 'NOT_SET') {
            echo "$key: " . substr($value, 0, 10) . "..." . "\n";
        } else {
            echo "$key: $value\n";
        }
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Error instantiating payment processor: " . $e->getMessage() . "</p>";
}

// Test AzamPay integration directly
echo "<h2>AzamPay Integration Test</h2>";
try {
    $azampay = new KiliSmile_AzamPay();
    echo "<p>✅ AzamPay integration instantiated successfully</p>";
    
    // Test authentication
    echo "<p>Testing authentication...</p>";
    $auth_result = $azampay->authenticate();
    if ($auth_result) {
        echo "<p>✅ Authentication successful</p>";
    } else {
        echo "<p>❌ Authentication failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error with AzamPay integration: " . $e->getMessage() . "</p>";
}

?>

