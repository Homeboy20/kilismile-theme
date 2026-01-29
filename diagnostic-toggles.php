<?php
/**
 * Simple Diagnostic Script for Payment Toggle Issue
 * This doesn't require WordPress to be fully loaded
 */

echo "<h1>Payment Toggle Diagnostic</h1>";

// Check if we can access WordPress options directly from the database
$host = 'localhost';
$username = 'root';  // Common Local by Flywheel username
$password = 'root';  // Common Local by Flywheel password
$database = 'local';  // Common Local by Flywheel database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get WordPress table prefix
    $prefix_query = $pdo->query("SHOW TABLES LIKE '%options'");
    $tables = $prefix_query->fetchAll();
    
    if (empty($tables)) {
        echo "<p style='color: red;'>No WordPress options table found. Database connection issue.</p>";
        exit;
    }
    
    $options_table = $tables[0][0]; // Get the actual table name
    echo "<p>Found options table: <strong>$options_table</strong></p>";
    
    // Check payment method settings
    $payment_options = [
        'kilismile_paypal_enabled',
        'kilismile_stripe_enabled', 
        'kilismile_mpesa_enabled',
        'kilismile_tigo_pesa_enabled',
        'kilismile_airtel_money_enabled',
        'kilismile_selcom_enabled',
        'kilismile_azam_pay_enabled',
        'kilismile_local_bank_enabled',
        'kilismile_wire_transfer_enabled'
    ];
    
    echo "<h2>Current Payment Method Settings:</h2>";
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th style='padding: 10px; background: #f0f0f0;'>Option Name</th><th style='padding: 10px; background: #f0f0f0;'>Value</th><th style='padding: 10px; background: #f0f0f0;'>Status</th></tr>";
    
    foreach ($payment_options as $option) {
        $stmt = $pdo->prepare("SELECT option_value FROM $options_table WHERE option_name = ?");
        $stmt->execute([$option]);
        $result = $stmt->fetch();
        
        $value = $result ? $result['option_value'] : 'Not Set';
        $status = ($value == '1') ? 'ENABLED' : (($value == '0') ? 'DISABLED' : 'NOT SET');
        $color = ($value == '1') ? 'green' : (($value == '0') ? 'red' : 'orange');
        
        echo "<tr>";
        echo "<td style='padding: 10px;'>$option</td>";
        echo "<td style='padding: 10px;'>$value</td>";
        echo "<td style='padding: 10px; color: $color; font-weight: bold;'>$status</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>Diagnostic Information:</h2>";
    echo "<ul>";
    echo "<li>If values show as 'Not Set', the payment methods haven't been configured yet</li>";
    echo "<li>If values are '1', the payment method is enabled</li>";
    echo "<li>If values are '0', the payment method is disabled</li>";
    echo "<li>The toggle should change between '1' and '0' when you click it in admin</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database settings. Common settings for Local by Flywheel:</p>";
    echo "<ul>";
    echo "<li>Host: localhost</li>";
    echo "<li>Username: root</li>";
    echo "<li>Password: root</li>";
    echo "<li>Database: local</li>";
    echo "</ul>";
}
?>


