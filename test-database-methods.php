<?php
// Test script for donation database class
require_once 'includes/donation-database.php';

try {
    $db = new KiliSmile_Donation_Database();
    echo "✓ KiliSmile_Donation_Database class loaded successfully\n";
    
    $methods = get_class_methods($db);
    $required_methods = ['count_donations', 'get_donation_statistics', 'get_analytics_data', 'get_donations'];
    
    foreach ($required_methods as $method) {
        if (in_array($method, $methods)) {
            echo "✓ Method {$method} exists\n";
        } else {
            echo "✗ Method {$method} missing\n";
        }
    }
    
    echo "\nAll methods: " . implode(', ', $methods) . "\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}


