<?php
/**
 * Simple test script to verify all donation system classes load correctly
 */

// Set up basic WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../../');
}

// Load the donation system files in the correct order
require_once 'includes/donation-database.php';
require_once 'includes/payment-gateways-modern.php';
require_once 'includes/donation-email-handler.php';
require_once 'includes/donation-system-modern.php';

echo "Testing class loading...\n\n";

// Test each class
$classes_to_test = array(
    'KiliSmile_Donation_Database',
    'KiliSmile_Payment_Gateway_Factory',
    'KiliSmile_Donation_Email_Handler',
    'KiliSmile_Modern_Donation_System'
);

foreach ($classes_to_test as $class_name) {
    if (class_exists($class_name)) {
        echo "✅ {$class_name} - FOUND\n";
    } else {
        echo "❌ {$class_name} - NOT FOUND\n";
    }
}

echo "\nTesting singleton instantiation...\n";
try {
    // This should work without errors now
    $donation_system = KiliSmile_Modern_Donation_System::get_instance();
    echo "✅ Modern Donation System initialized successfully!\n";
} catch (Exception $e) {
    echo "❌ Error initializing system: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
?>


