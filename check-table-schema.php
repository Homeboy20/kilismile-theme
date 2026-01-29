<?php
/**
 * Check AzamPay Table Schema
 * 
 * This script shows the current database table structure
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    wp_die('You do not have permission to access this page.');
}

echo "<h1>AzamPay Database Schema Check</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 20px;'>";

global $wpdb;

$transactions_table = $wpdb->prefix . 'azampay_transactions';
$logs_table = $wpdb->prefix . 'azampay_logs';

// Check if tables exist
$transactions_exists = $wpdb->get_var("SHOW TABLES LIKE '{$transactions_table}'");
$logs_exists = $wpdb->get_var("SHOW TABLES LIKE '{$logs_table}'");

echo "<h2>Table Existence</h2>";
echo "<p>Transactions table exists: " . ($transactions_exists ? '✓ Yes' : '✗ No') . "</p>";
echo "<p>Logs table exists: " . ($logs_exists ? '✓ Yes' : '✗ No') . "</p>";

// Show transactions table structure if it exists
if ($transactions_exists) {
    echo "<h2>Transactions Table Structure</h2>";
    $structure = $wpdb->get_results("DESCRIBE {$transactions_table}");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f5f5f5;'><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Null</th><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>Default</th><th style='padding: 8px;'>Extra</th></tr>";
    foreach ($structure as $field) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Field) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Type) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Null) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Key) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Default) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Extra) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show indexes
    echo "<h3>Transactions Table Indexes</h3>";
    $indexes = $wpdb->get_results("SHOW INDEX FROM {$transactions_table}");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f5f5f5;'><th style='padding: 8px;'>Key Name</th><th style='padding: 8px;'>Column</th><th style='padding: 8px;'>Unique</th></tr>";
    foreach ($indexes as $index) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . esc_html($index->Key_name) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($index->Column_name) . "</td>";
        echo "<td style='padding: 8px;'>" . ($index->Non_unique ? 'No' : 'Yes') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Show logs table structure if it exists
if ($logs_exists) {
    echo "<h2>Logs Table Structure</h2>";
    $structure = $wpdb->get_results("DESCRIBE {$logs_table}");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f5f5f5;'><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Null</th><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>Default</th><th style='padding: 8px;'>Extra</th></tr>";
    foreach ($structure as $field) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Field) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Type) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Null) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Key) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Default) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($field->Extra) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show indexes
    echo "<h3>Logs Table Indexes</h3>";
    $indexes = $wpdb->get_results("SHOW INDEX FROM {$logs_table}");
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f5f5f5;'><th style='padding: 8px;'>Key Name</th><th style='padding: 8px;'>Column</th><th style='padding: 8px;'>Unique</th></tr>";
    foreach ($indexes as $index) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . esc_html($index->Key_name) . "</td>";
        echo "<td style='padding: 8px;'>" . esc_html($index->Column_name) . "</td>";
        echo "<td style='padding: 8px;'>" . ($index->Non_unique ? 'No' : 'Yes') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// Add navigation links
echo "<p style='margin: 20px;'>";
echo "<a href='" . admin_url('admin.php?page=azampay-status') . "'>← Back to AzamPay Status</a> | ";
echo "<a href='" . home_url('/wp-content/themes/kilismile/force-create-tables.php') . "'>Force Create Tables</a>";
echo "</p>";
?>

