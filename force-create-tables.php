<?php
/**
 * Force Create AzamPay Database Tables
 * Access this via browser: yoursite.local/wp-content/themes/kilismile/force-create-tables.php
 */

// Bootstrap WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-config.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Create AzamPay Tables</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🗃️ Force Create AzamPay Database Tables</h1>
    
    <?php
    global $wpdb;
    
    echo "<h2>Current Database Status</h2>";
    
    // Check if tables exist
    $transactions_table = $wpdb->prefix . 'azampay_transactions';
    $logs_table = $wpdb->prefix . 'azampay_logs';
    
    $transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '$transactions_table'");
    $logs_exists = $wpdb->get_var("SHOW TABLES LIKE '$logs_table'");
    
    echo "<p><strong>$transactions_table:</strong> " . ($transactions_exists ? "<span class='success'>EXISTS</span>" : "<span class='error'>MISSING</span>") . "</p>";
    echo "<p><strong>$logs_table:</strong> " . ($logs_exists ? "<span class='success'>EXISTS</span>" : "<span class='error'>MISSING</span>") . "</p>";
    
    if (!$transactions_exists || !$logs_exists) {
        echo "<h2>Creating Missing Tables</h2>";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create transactions table
        if (!$transactions_exists) {
            echo "<p class='info'>Creating transactions table...</p>";
            
            $sql = "CREATE TABLE $transactions_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                reference varchar(100) NOT NULL UNIQUE,
                amount decimal(10,2) NOT NULL,
                currency varchar(3) NOT NULL DEFAULT 'TZS',
                donor_name varchar(255) NOT NULL,
                donor_email varchar(255) NOT NULL,
                phone varchar(20) NOT NULL,
                purpose varchar(255) DEFAULT 'healthcare',
                payment_method varchar(50) DEFAULT 'stkpush',
                anonymous tinyint(1) DEFAULT 0,
                notes text,
                status varchar(20) DEFAULT 'pending',
                gateway_transaction_id varchar(255),
                gateway_response longtext,
                callback_data longtext,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY reference (reference),
                KEY status (status),
                KEY created_at (created_at),
                KEY donor_email (donor_email)
            ) $charset_collate;";
            
            $result = dbDelta($sql);
            
            // Verify creation
            $transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '$transactions_table'");
            if ($transactions_exists) {
                echo "<p class='success'>✅ Transactions table created successfully!</p>";
            } else {
                echo "<p class='error'>❌ Failed to create transactions table</p>";
                echo "<pre>SQL: $sql</pre>";
            }
        }
        
        // Create logs table
        if (!$logs_exists) {
            echo "<p class='info'>Creating logs table...</p>";
            
            $sql = "CREATE TABLE $logs_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                level varchar(20) NOT NULL DEFAULT 'info',
                message text NOT NULL,
                context longtext,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY level (level),
                KEY created_at (created_at)
            ) $charset_collate;";
            
            $result = dbDelta($sql);
            
            // Verify creation
            $logs_exists = $wpdb->get_var("SHOW TABLES LIKE '$logs_table'");
            if ($logs_exists) {
                echo "<p class='success'>✅ Logs table created successfully!</p>";
            } else {
                echo "<p class='error'>❌ Failed to create logs table</p>";
                echo "<pre>SQL: $sql</pre>";
            }
        }
        
    } else {
        echo "<p class='success'>✅ All tables already exist!</p>";
    }
    
    echo "<h2>Final Status Check</h2>";
    
    // Final verification
    $transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '$transactions_table'");
    $logs_exists = $wpdb->get_var("SHOW TABLES LIKE '$logs_table'");
    
    echo "<p><strong>$transactions_table:</strong> " . ($transactions_exists ? "<span class='success'>EXISTS ✅</span>" : "<span class='error'>MISSING ❌</span>") . "</p>";
    echo "<p><strong>$logs_table:</strong> " . ($logs_exists ? "<span class='success'>EXISTS ✅</span>" : "<span class='error'>MISSING ❌</span>") . "</p>";
    
    if ($transactions_exists && $logs_exists) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Success!</h3>";
        echo "<p>All database tables have been created successfully. You can now:</p>";
        echo "<ul>";
        echo "<li>Access the AzamPay admin pages without errors</li>";
        echo "<li>View transactions and logs</li>";
        echo "<li>Use the donation forms</li>";
        echo "</ul>";
        echo "<p><a href='" . admin_url('admin.php?page=azampay-settings') . "'>Go to AzamPay Settings →</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>❌ Tables Still Missing</h3>";
        echo "<p>Some tables could not be created. This might be due to:</p>";
        echo "<ul>";
        echo "<li>Database permissions</li>";
        echo "<li>MySQL version compatibility</li>";
        echo "<li>WordPress database configuration</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    // Show database info for debugging
    echo "<h2>Debug Information</h2>";
    echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Table Prefix:</strong> " . $wpdb->prefix . "</p>";
    echo "<p><strong>MySQL Version:</strong> " . $wpdb->db_version() . "</p>";
    echo "<p><strong>WordPress Version:</strong> " . get_bloginfo('version') . "</p>";
    ?>
    
</body>
</html>

