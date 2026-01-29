<?php
/**
 * Database diagnostic tool
 */

// Include WordPress bootstrap
require_once('../../../wp-load.php');

echo "<h1>Database Diagnostic</h1>";

// Check if database class exists
if (class_exists('KiliSmile_Donation_Database')) {
    echo "<p>✅ Database class exists</p>";
    
    try {
        $db = new KiliSmile_Donation_Database();
        echo "<p>✅ Database class instantiated successfully</p>";
        
        // Check if tables exist
        global $wpdb;
        $donations_table = $wpdb->prefix . 'kilismile_donations';
        
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
            DB_NAME,
            $donations_table
        ));
        
        if ($table_exists) {
            echo "<p>✅ Donations table exists: $donations_table</p>";
            
            // Check table structure
            $columns = $wpdb->get_results("DESCRIBE $donations_table");
            echo "<h3>Table Structure:</h3>";
            echo "<ul>";
            foreach ($columns as $column) {
                echo "<li>{$column->Field} ({$column->Type})</li>";
            }
            echo "</ul>";
            
        } else {
            echo "<p>❌ Donations table does NOT exist: $donations_table</p>";
            echo "<p><strong>This is likely the cause of the error!</strong></p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p>❌ Database class does NOT exist</p>";
}

// Test database connection
global $wpdb;
echo "<h2>WordPress Database Connection</h2>";
echo "<p>Database Name: " . DB_NAME . "</p>";
echo "<p>Table Prefix: " . $wpdb->prefix . "</p>";

// Test simple query
$result = $wpdb->get_var("SELECT VERSION()");
if ($result) {
    echo "<p>✅ Database connection working: MySQL " . $result . "</p>";
} else {
    echo "<p>❌ Database connection failed</p>";
}

?>

<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 40px;
    background: #f9f9f9;
}
h1, h2, h3 {
    color: #333;
}
</style>

