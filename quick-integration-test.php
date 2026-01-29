<?php
/**
 * Quick Payment Integration Test
 * Tests if the payment integration is working after fixing the fatal error
 */

// Simple test without WordPress loading issues
echo "<h1>Payment Integration Status Check</h1>\n";

// Check if we're in WordPress context
if (defined('ABSPATH')) {
    echo "✅ WordPress context available\n\n";
    
    // Test plugin loading
    $plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE');
    echo "Plugin Active: " . ($plugin_active ? "✅ YES" : "❌ NO") . "\n";
    
    // Test key classes
    $classes = [
        'KiliSmile_Payment_Gateway_Factory' => class_exists('KiliSmile_Payment_Gateway_Factory'),
        'KiliSmile_Payment_Processor' => class_exists('KiliSmile_Payment_Processor'),
        'KiliSmile_Payment_Plugin_Bridge' => class_exists('KiliSmile_Payment_Plugin_Bridge')
    ];
    
    echo "\nKey Classes:\n";
    foreach ($classes as $class => $exists) {
        echo "- $class: " . ($exists ? "✅" : "❌") . "\n";
    }
    
    // Test AJAX hooks
    $ajax_registered = has_action('wp_ajax_kilismile_process_payment');
    echo "\nAJAX Hook Registered: " . ($ajax_registered ? "✅ YES" : "❌ NO") . "\n";
    
    // Test gateway function
    if (function_exists('kilismile_get_payment_gateways')) {
        echo "Gateway Function: ✅ Available\n";
        
        try {
            $gateways = kilismile_get_payment_gateways();
            echo "Gateway Count: " . count($gateways) . "\n";
        } catch (Exception $e) {
            echo "Gateway Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Gateway Function: ❌ Not Available\n";
    }
    
    echo "\n" . str_repeat("=", 40) . "\n";
    echo "OVERALL STATUS: ";
    
    if ($plugin_active && $ajax_registered && class_exists('KiliSmile_Payment_Processor')) {
        echo "✅ WORKING\n";
        echo "The payment integration is functioning correctly!\n";
    } else {
        echo "❌ ISSUES DETECTED\n";
        echo "Some components may need attention.\n";
    }
    
} else {
    echo "❌ Not in WordPress context - run this through the web interface\n";
}

