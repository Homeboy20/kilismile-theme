<?php
/**
 * Quick Class Loading Test
 * Run this to verify the payment classes can be loaded
 */

// Simulate WordPress environment
define('ABSPATH', true);

// Include the files in correct order
require_once 'includes/donation-database.php';
require_once 'includes/azampay-integration.php';
require_once 'includes/paypal-integration.php';
require_once 'includes/payment-processor.php';

echo "<h1>Class Loading Test</h1>";

// Test class definitions
$classes = array(
    'KiliSmile_Donation_Database',
    'KiliSmile_AzamPay', 
    'KiliSmile_PayPal',
    'KiliSmile_Payment_Processor'
);

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✅ {$class} - Class defined<br>";
    } else {
        echo "❌ {$class} - Class not found<br>";
    }
}

// Test instantiation
echo "<h2>Instantiation Test</h2>";

try {
    $database = new KiliSmile_Donation_Database();
    echo "✅ KiliSmile_Donation_Database - Instantiated<br>";
} catch (Exception $e) {
    echo "❌ KiliSmile_Donation_Database - Error: " . $e->getMessage() . "<br>";
}

try {
    $azampay = new KiliSmile_AzamPay();
    echo "✅ KiliSmile_AzamPay - Instantiated<br>";
} catch (Exception $e) {
    echo "❌ KiliSmile_AzamPay - Error: " . $e->getMessage() . "<br>";
}

try {
    $paypal = new KiliSmile_PayPal();
    echo "✅ KiliSmile_PayPal - Instantiated<br>";
} catch (Exception $e) {
    echo "❌ KiliSmile_PayPal - Error: " . $e->getMessage() . "<br>";
}

try {
    $processor = new KiliSmile_Payment_Processor();
    echo "✅ KiliSmile_Payment_Processor - Instantiated<br>";
} catch (Exception $e) {
    echo "❌ KiliSmile_Payment_Processor - Error: " . $e->getMessage() . "<br>";
}

echo "<p>If all tests show ✅, the class loading issue is fixed!</p>";
?>

