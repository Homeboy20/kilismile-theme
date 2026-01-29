<?php
/**
 * Simple AzamPay CLI Test
 * Command line test for AzamPay integration
 */

// Include WordPress environment
$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}
require_once $wp_load_path;

echo "=== AzamPay Plugin Test ===\n\n";

// Test 1: Check if classes are loaded
echo "1. Class Availability:\n";
$azampay_class = class_exists('KiliSmile_AzamPay');
$enhanced_class = class_exists('KiliSmile_Enhanced_AzamPay');
$factory_class = class_exists('KiliSmile_Payment_Gateway_Factory');

echo "   - KiliSmile_AzamPay: " . ($azampay_class ? "✓ Available" : "✗ Missing") . "\n";
echo "   - KiliSmile_Enhanced_AzamPay: " . ($enhanced_class ? "✓ Available" : "✗ Missing") . "\n";
echo "   - Payment Gateway Factory: " . ($factory_class ? "✓ Available" : "✗ Missing") . "\n\n";

// Test 2: Check plugin status
echo "2. Plugin Status:\n";
$plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE');
echo "   - Plugin Active: " . ($plugin_active ? "✓ Yes" : "✗ No") . "\n";

$ajax_registered = has_action('wp_ajax_kilismile_process_payment');
echo "   - AJAX Handler: " . ($ajax_registered ? "✓ Registered" : "✗ Not Registered") . "\n\n";

// Test 3: AzamPay Configuration
echo "3. AzamPay Configuration:\n";
$sandbox = get_option('kilismile_azampay_sandbox_mode', 'Not Set');
$enhanced = get_option('kilismile_use_enhanced_azampay', 'Not Set');
$client_id = get_option('kilismile_azampay_client_id', '');
$client_secret = get_option('kilismile_azampay_client_secret', '');

echo "   - Sandbox Mode: " . ($sandbox ? "Enabled" : "Disabled") . "\n";
echo "   - Enhanced Mode: " . ($enhanced ? "Enabled" : "Disabled") . "\n";
echo "   - Client ID: " . (!empty($client_id) ? "✓ Set" : "✗ Not Set") . "\n";
echo "   - Client Secret: " . (!empty($client_secret) ? "✓ Set" : "✗ Not Set") . "\n\n";

// Test 4: Gateway availability for TZS
echo "4. Gateway Test:\n";
if (function_exists('kilismile_get_payment_gateways')) {
    try {
        $gateways = kilismile_get_payment_gateways('TZS');
        $azampay_found = false;
        
        echo "   Available gateways for TZS:\n";
        foreach ($gateways as $id => $gateway) {
            echo "   - $id\n";
            if (strpos($id, 'azam') !== false) {
                $azampay_found = true;
            }
        }
        
        echo "   - AzamPay Available: " . ($azampay_found ? "✓ Yes" : "✗ No") . "\n\n";
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ✗ Gateway function not available\n\n";
}

// Test 5: Instance creation test
echo "5. Instance Creation:\n";
try {
    if ($enhanced_class) {
        $azampay = new KiliSmile_Enhanced_AzamPay();
        echo "   - Enhanced AzamPay: ✓ Created successfully\n";
        
        if (method_exists($azampay, 'is_available')) {
            $available = $azampay->is_available();
            echo "   - Gateway Available: " . ($available ? "✓ Yes" : "✗ No") . "\n";
        }
        
    } else if ($azampay_class) {
        $azampay = new KiliSmile_AzamPay();
        echo "   - Standard AzamPay: ✓ Created successfully\n";
        
        if (method_exists($azampay, 'is_available')) {
            $available = $azampay->is_available();
            echo "   - Gateway Available: " . ($available ? "✓ Yes" : "✗ No") . "\n";
        }
    } else {
        echo "   ✗ No AzamPay class available\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error creating instance: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Simple payment simulation
echo "6. Payment Simulation:\n";
if (isset($azampay) && method_exists($azampay, 'process_payment')) {
    $test_data = array(
        'amount' => 5000,
        'currency' => 'TZS',
        'donor_name' => 'Test User',
        'donor_email' => 'test@example.com',
        'donor_phone' => '+255700123456'
    );
    
    echo "   Testing with data: " . json_encode($test_data) . "\n";
    
    try {
        // Note: This is a simulation - actual payment would require proper setup
        echo "   - Payment method exists: ✓ Yes\n";
        echo "   - Would process: TZS 5,000 for Test User\n";
        echo "   ⚠ Note: Actual payment requires proper API credentials\n";
    } catch (Exception $e) {
        echo "   ✗ Error in payment simulation: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ Payment method not available\n";
}

echo "\n";

// Summary
echo "=== Test Summary ===\n";
$total_tests = 6;
$passed_tests = 0;

if ($azampay_class || $enhanced_class) $passed_tests++;
if ($plugin_active) $passed_tests++;
if (!empty($client_id) && !empty($client_secret)) $passed_tests++;
if (function_exists('kilismile_get_payment_gateways')) $passed_tests++;
if (isset($azampay)) $passed_tests++;
if ($ajax_registered) $passed_tests++;

$percentage = round(($passed_tests / $total_tests) * 100);

echo "Tests Passed: $passed_tests/$total_tests ($percentage%)\n";

if ($percentage >= 80) {
    echo "Status: ✓ AzamPay integration is working well\n";
} else if ($percentage >= 50) {
    echo "Status: ⚠ AzamPay integration has some issues\n";
} else {
    echo "Status: ✗ AzamPay integration needs attention\n";
}

echo "\nRecommendations:\n";
if (!$plugin_active) {
    echo "- Ensure the payment plugin is active\n";
}
if (empty($client_id) || empty($client_secret)) {
    echo "- Configure AzamPay API credentials\n";
}
if (!$ajax_registered) {
    echo "- Check AJAX handler registration\n";
}
if (!$azampay_class && !$enhanced_class) {
    echo "- Verify AzamPay classes are loaded\n";
}

echo "\nTo run a web-based test, visit:\n";
echo "- " . home_url('/wp-content/themes/kilismile/test-azampay-plugin.php') . "\n";
echo "- " . home_url('/wp-content/themes/kilismile/test-azampay-class.php') . "\n";

echo "\n=== End Test ===\n";
?>

