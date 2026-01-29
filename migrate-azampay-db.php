<?php
/**
 * AzamPay Database Migration Script
 * 
 * This script migrates existing AzamPay tables to the new schema
 * Run this before reactivating the plugin
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    wp_die('You do not have permission to access this page.');
}

echo "<h1>AzamPay Database Migration</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";

global $wpdb;

$transactions_table = $wpdb->prefix . 'azampay_transactions';
$logs_table = $wpdb->prefix . 'azampay_logs';

try {
    echo "<h2>Starting Migration...</h2>";
    
    // Backup existing data if tables exist
    $transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '{$transactions_table}'");
    $logs_exists = $wpdb->get_var("SHOW TABLES LIKE '{$logs_table}'");
    
    $transaction_backup = array();
    $log_backup = array();
    
    if ($transactions_exists) {
        echo "<p>Backing up existing transaction data...</p>";
        $transaction_backup = $wpdb->get_results("SELECT * FROM {$transactions_table}", ARRAY_A);
        echo "<p>✓ Backed up " . count($transaction_backup) . " transactions</p>";
    }
    
    if ($logs_exists) {
        echo "<p>Backing up existing log data...</p>";
        $log_backup = $wpdb->get_results("SELECT * FROM {$logs_table}", ARRAY_A);
        echo "<p>✓ Backed up " . count($log_backup) . " log entries</p>";
    }
    
    // Drop existing tables
    echo "<h3>Dropping existing tables...</h3>";
    if ($transactions_exists) {
        $wpdb->query("DROP TABLE {$transactions_table}");
        echo "<p>✓ Dropped transactions table</p>";
    }
    
    if ($logs_exists) {
        $wpdb->query("DROP TABLE {$logs_table}");
        echo "<p>✓ Dropped logs table</p>";
    }
    
    // Create new tables with correct schema
    echo "<h3>Creating new tables...</h3>";
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Create transactions table with new schema
    $transactions_sql = "CREATE TABLE {$transactions_table} (
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
        KEY status (status),
        KEY created_at (created_at),
        KEY donor_email (donor_email)
    ) {$charset_collate}";
    
    $result1 = $wpdb->query($transactions_sql);
    if ($result1 !== false) {
        echo "<p style='color: green;'>✓ Created new transactions table</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create transactions table: " . $wpdb->last_error . "</p>";
    }
    
    // Create logs table
    $logs_sql = "CREATE TABLE {$logs_table} (
        id int(11) NOT NULL AUTO_INCREMENT,
        level varchar(20) NOT NULL DEFAULT 'info',
        message text NOT NULL,
        context longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY level (level),
        KEY created_at (created_at)
    ) {$charset_collate}";
    
    $result2 = $wpdb->query($logs_sql);
    if ($result2 !== false) {
        echo "<p style='color: green;'>✓ Created new logs table</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create logs table: " . $wpdb->last_error . "</p>";
    }
    
    // Restore compatible data
    echo "<h3>Restoring Data...</h3>";
    
    // Restore transaction data (map old fields to new fields)
    if (!empty($transaction_backup)) {
        $restored_count = 0;
        foreach ($transaction_backup as $transaction) {
            $insert_data = array(
                'reference' => isset($transaction['transaction_id']) ? $transaction['transaction_id'] : 
                              (isset($transaction['reference']) ? $transaction['reference'] : 'TXN_' . uniqid()),
                'amount' => $transaction['amount'] ?? 0,
                'currency' => $transaction['currency'] ?? 'TZS',
                'donor_name' => $transaction['donor_name'] ?? $transaction['customer_email'] ?? 'Unknown',
                'donor_email' => $transaction['donor_email'] ?? $transaction['customer_email'] ?? '',
                'phone' => $transaction['phone'] ?? $transaction['customer_phone'] ?? '',
                'purpose' => $transaction['purpose'] ?? 'healthcare',
                'payment_method' => $transaction['payment_method'] ?? 'stkpush',
                'anonymous' => 0,
                'notes' => '',
                'status' => $transaction['status'] ?? 'pending',
                'gateway_transaction_id' => $transaction['gateway_transaction_id'] ?? null,
                'gateway_response' => $transaction['gateway_response'] ?? null,
                'callback_data' => $transaction['callback_data'] ?? null,
                'created_at' => $transaction['created_at'] ?? current_time('mysql'),
                'updated_at' => $transaction['updated_at'] ?? current_time('mysql')
            );
            
            $inserted = $wpdb->insert($transactions_table, $insert_data);
            if ($inserted) {
                $restored_count++;
            }
        }
        echo "<p>✓ Restored {$restored_count} out of " . count($transaction_backup) . " transactions</p>";
    }
    
    // Restore log data
    if (!empty($log_backup)) {
        $restored_count = 0;
        foreach ($log_backup as $log) {
            $insert_data = array(
                'level' => $log['level'] ?? 'info',
                'message' => $log['message'] ?? '',
                'context' => $log['context'] ?? null,
                'created_at' => $log['created_at'] ?? current_time('mysql')
            );
            
            $inserted = $wpdb->insert($logs_table, $insert_data);
            if ($inserted) {
                $restored_count++;
            }
        }
        echo "<p>✓ Restored {$restored_count} out of " . count($log_backup) . " log entries</p>";
    }
    
    echo "<h2 style='color: green;'>Migration Completed Successfully!</h2>";
    echo "<p>You can now reactivate the AzamPay plugin without errors.</p>";
    
    // Verify final state
    echo "<h3>Final Verification:</h3>";
    $final_transactions = $wpdb->get_var("SHOW TABLES LIKE '{$transactions_table}'");
    $final_logs = $wpdb->get_var("SHOW TABLES LIKE '{$logs_table}'");
    echo "<p>Transactions table exists: " . ($final_transactions ? '✓ Yes' : '✗ No') . "</p>";
    echo "<p>Logs table exists: " . ($final_logs ? '✓ Yes' : '✗ No') . "</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Migration Failed!</h2>";
    echo "<p>Error: " . esc_html($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}

echo "</div>";

echo "<p style='margin: 20px;'>";
echo "<a href='" . admin_url('plugins.php') . "'>Go to Plugins Page</a> | ";
echo "<a href='" . admin_url('admin.php?page=azampay-status') . "'>AzamPay Status</a>";
echo "</p>";
?>

