<?php
/**
 * Manual Database Table Creator
 * Run this to manually create the AzamPay plugin tables
 */

define('WP_USE_THEMES', false);
require_once('../../../wp-config.php');

echo "🗃️ Manual AzamPay Database Table Creation\n";
echo "=========================================\n\n";

// Include the database class
require_once WP_PLUGIN_DIR . '/azampay-payment-gateway/includes/class-azampay-database.php';

// Get database instance
$db = AzamPay_Database::get_instance();

try {
    echo "Creating database tables...\n";
    $db->create_tables();
    echo "✅ Database tables created successfully!\n\n";
    
    // Verify tables exist
    global $wpdb;
    
    $transactions_table = $wpdb->prefix . 'azampay_transactions';
    $logs_table = $wpdb->prefix . 'azampay_logs';
    
    echo "Verifying table creation:\n";
    
    $transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '$transactions_table'");
    if ($transactions_exists) {
        echo "✅ $transactions_table table exists\n";
        
        // Show table structure
        $columns = $wpdb->get_results("DESCRIBE $transactions_table");
        echo "   Columns: " . count($columns) . "\n";
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "❌ $transactions_table table NOT created\n";
    }
    
    echo "\n";
    
    $logs_exists = $wpdb->get_var("SHOW TABLES LIKE '$logs_table'");
    if ($logs_exists) {
        echo "✅ $logs_table table exists\n";
        
        // Show table structure
        $columns = $wpdb->get_results("DESCRIBE $logs_table");
        echo "   Columns: " . count($columns) . "\n";
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "❌ $logs_table table NOT created\n";
    }
    
    echo "\n📋 Next Steps:\n";
    echo "1. Tables should now be available for the plugin\n";
    echo "2. Check the WordPress admin AzamPay pages\n";
    echo "3. Look for any remaining errors in the log\n";
    
} catch (Exception $e) {
    echo "❌ Error creating tables: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>

<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 40px;
    background: #f9f9f9;
}
h1 {
    color: #333;
}
a {
    color: #0073aa;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>

