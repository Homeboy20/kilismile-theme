<?php
/**
 * Modern Donation Database Handler
 * 
 * Handles all database operations for the donation system
 * with proper security, indexing, and performance optimization.
 *
 * @package KiliSmile
 * @    /**
     * Get donations with pagination and filters
     */
    public function get_donations($limit = null, $offset = null, $filters = null) {
        // Handle different calling styles
        if (is_array($limit)) {
            // Called with array of args (original style)
            $args = $limit;
        } else {
            // Called with individual parameters (admin style)
            $args = array(
                'limit' => $limit ?: 50,
                'offset' => $offset ?: 0
            );
            
            if (is_array($filters)) {
                $args = array_merge($args, $filters);
            }
        }
        
        $defaults = array(
            'status' => null,
            'currency' => null,
            'payment_method' => null,
            'date_from' => null,
            'date_to' => null,
            'search' => null,
            'limit' => 50,
            'offset' => 0,
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donation Database Handler
 */
class KiliSmile_Donation_Database {
    
    private $wpdb;
    private $donations_table;
    private $donation_logs_table;
    private $donation_meta_table;
    
    public function __construct() {
        global $wpdb;
        
        $this->wpdb = $wpdb;
        $this->donations_table = $wpdb->prefix . 'kilismile_donations';
        $this->donation_logs_table = $wpdb->prefix . 'kilismile_donation_logs';
        $this->donation_meta_table = $wpdb->prefix . 'kilismile_donation_meta';
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        // Main donations table
        $donations_sql = "CREATE TABLE {$this->donations_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL UNIQUE,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_method varchar(50) NOT NULL,
            gateway_transaction_id varchar(100),
            recurring tinyint(1) NOT NULL DEFAULT 0,
            recurring_interval varchar(20),
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20),
            country varchar(2),
            anonymous tinyint(1) NOT NULL DEFAULT 0,
            purpose varchar(100),
            message text,
            ip_address varchar(45),
            user_agent text,
            referrer_url text,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            completed_at datetime NULL,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY status (status),
            KEY email (email),
            KEY payment_method (payment_method),
            KEY currency (currency),
            KEY created_at (created_at),
            KEY amount (amount)
        ) {$charset_collate};";
        
        // Donation logs table for tracking events
        $logs_sql = "CREATE TABLE {$this->donation_logs_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY event_type (event_type),
            KEY created_at (created_at),
            FOREIGN KEY (donation_id) REFERENCES {$this->donations_table}(donation_id) ON DELETE CASCADE
        ) {$charset_collate};";
        
        // Donation meta table for additional flexible data
        $meta_sql = "CREATE TABLE {$this->donation_meta_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY meta_key (meta_key),
            FOREIGN KEY (donation_id) REFERENCES {$this->donations_table}(donation_id) ON DELETE CASCADE
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($donations_sql);
        dbDelta($logs_sql);
        dbDelta($meta_sql);
        
        // Create version option to track database schema
        update_option('kilismile_donation_db_version', '2.0.0');
    }
    
    /**
     * Create a new donation record
     */
    public function create_donation($donation_data) {
        $data = array(
            'donation_id' => $donation_data['donation_id'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'status' => 'pending',
            'payment_method' => $donation_data['payment_method'],
            'recurring' => $donation_data['recurring'] ? 1 : 0,
            'first_name' => $donation_data['first_name'],
            'last_name' => $donation_data['last_name'],
            'email' => $donation_data['email'],
            'phone' => $donation_data['phone'],
            'country' => $donation_data['country'] ?? null,
            'anonymous' => $donation_data['anonymous'] ? 1 : 0,
            'purpose' => $donation_data['purpose'],
            'message' => $donation_data['message'],
            'ip_address' => $donation_data['ip_address'],
            'user_agent' => $donation_data['user_agent'],
            'created_at' => current_time('mysql')
        );
        
        $formats = array(
            '%s', '%f', '%s', '%s', '%s', '%d',
            '%s', '%s', '%s', '%s', '%s', '%d',
            '%s', '%s', '%s', '%s', '%s', '%s'
        );
        
        $result = $this->wpdb->insert($this->donations_table, $data, $formats);
        
        if ($result === false) {
            error_log('Database error creating donation: ' . $this->wpdb->last_error);
            return false;
        }
        
        // Log the creation event
        $this->log_event($donation_data['donation_id'], 'donation_created', array(
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'payment_method' => $donation_data['payment_method']
        ));
        
        return true;
    }
    
    /**
     * Get donation by ID
     */
    public function get_donation($donation_id) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->donations_table} WHERE donation_id = %s",
            $donation_id
        );
        
        return $this->wpdb->get_row($sql, ARRAY_A);
    }
    
    /**
     * Update donation status
     */
    public function update_donation_status($donation_id, $status, $gateway_transaction_id = null) {
        $data = array(
            'status' => $status,
            'updated_at' => current_time('mysql')
        );
        
        $formats = array('%s', '%s');
        
        if ($gateway_transaction_id) {
            $data['gateway_transaction_id'] = $gateway_transaction_id;
            $formats[] = '%s';
        }
        
        if ($status === 'completed') {
            $data['completed_at'] = current_time('mysql');
            $formats[] = '%s';
        }
        
        $result = $this->wpdb->update(
            $this->donations_table,
            $data,
            array('donation_id' => $donation_id),
            $formats,
            array('%s')
        );
        
        if ($result !== false) {
            // Log status change
            $this->log_event($donation_id, 'status_changed', array(
                'new_status' => $status,
                'gateway_transaction_id' => $gateway_transaction_id
            ));
        }
        
        return $result !== false;
    }
    
    /**
     * Get donations by criteria
     */
    public function get_donations($args = array()) {
        $defaults = array(
            'status' => null,
            'currency' => null,
            'payment_method' => null,
            'date_from' => null,
            'date_to' => null,
            'limit' => 50,
            'offset' => 0,
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        if ($args['status']) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if ($args['currency']) {
            $where_conditions[] = 'currency = %s';
            $where_values[] = $args['currency'];
        }
        
        if ($args['payment_method']) {
            $where_conditions[] = 'payment_method = %s';
            $where_values[] = $args['payment_method'];
        }
        
        if ($args['date_from']) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['date_from'];
        }
        
        if ($args['date_to']) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['date_to'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "SELECT * FROM {$this->donations_table} 
                WHERE {$where_clause} 
                ORDER BY {$args['order_by']} {$args['order']} 
                LIMIT %d OFFSET %d";
        
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];
        
        $prepared_sql = $this->wpdb->prepare($sql, $where_values);
        
        return $this->wpdb->get_results($prepared_sql, ARRAY_A);
    }
    
    /**
     * Get donation statistics
     */
    public function get_donation_stats($period = '30days') {
        $date_condition = '';
        
        switch ($period) {
            case '24hours':
                $date_condition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
            case '7days':
                $date_condition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '30days':
                $date_condition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case '90days':
                $date_condition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            case 'year':
                $date_condition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
            default:
                $date_condition = '';
        }
        
        // Total donations and amount by currency
        $sql = "SELECT 
                    currency,
                    status,
                    COUNT(*) as count,
                    SUM(amount) as total_amount,
                    AVG(amount) as avg_amount,
                    MIN(amount) as min_amount,
                    MAX(amount) as max_amount
                FROM {$this->donations_table} 
                {$date_condition}
                GROUP BY currency, status";
        
        $results = $this->wpdb->get_results($sql, ARRAY_A);
        
        // Payment method breakdown
        $payment_sql = "SELECT 
                           payment_method,
                           currency,
                           COUNT(*) as count,
                           SUM(amount) as total_amount
                       FROM {$this->donations_table} 
                       {$date_condition}
                       GROUP BY payment_method, currency";
        
        $payment_results = $this->wpdb->get_results($payment_sql, ARRAY_A);
        
        // Daily breakdown for charts
        $daily_sql = "SELECT 
                         DATE(created_at) as date,
                         currency,
                         COUNT(*) as count,
                         SUM(amount) as total_amount
                     FROM {$this->donations_table} 
                     {$date_condition}
                     GROUP BY DATE(created_at), currency
                     ORDER BY date DESC
                     LIMIT 30";
        
        $daily_results = $this->wpdb->get_results($daily_sql, ARRAY_A);
        
        return array(
            'summary' => $results,
            'payment_methods' => $payment_results,
            'daily_breakdown' => $daily_results
        );
    }
    
    /**
     * Log donation event
     */
    public function log_event($donation_id, $event_type, $event_data = array()) {
        $data = array(
            'donation_id' => $donation_id,
            'event_type' => $event_type,
            'event_data' => wp_json_encode($event_data),
            'created_at' => current_time('mysql')
        );
        
        return $this->wpdb->insert(
            $this->donation_logs_table,
            $data,
            array('%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get donation events/logs
     */
    public function get_donation_logs($donation_id, $limit = 50) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->donation_logs_table} 
             WHERE donation_id = %s 
             ORDER BY created_at DESC 
             LIMIT %d",
            $donation_id,
            $limit
        );
        
        $logs = $this->wpdb->get_results($sql, ARRAY_A);
        
        // Decode event data
        foreach ($logs as &$log) {
            $log['event_data'] = json_decode($log['event_data'], true);
        }
        
        return $logs;
    }
    
    /**
     * Add donation meta
     */
    public function add_donation_meta($donation_id, $meta_key, $meta_value) {
        return $this->wpdb->insert(
            $this->donation_meta_table,
            array(
                'donation_id' => $donation_id,
                'meta_key' => $meta_key,
                'meta_value' => is_array($meta_value) ? wp_json_encode($meta_value) : $meta_value
            ),
            array('%s', '%s', '%s')
        );
    }
    
    /**
     * Get donation meta
     */
    public function get_donation_meta($donation_id, $meta_key = null) {
        if ($meta_key) {
            $sql = $this->wpdb->prepare(
                "SELECT meta_value FROM {$this->donation_meta_table} 
                 WHERE donation_id = %s AND meta_key = %s 
                 LIMIT 1",
                $donation_id,
                $meta_key
            );
            
            $result = $this->wpdb->get_var($sql);
            
            // Try to decode JSON
            $decoded = json_decode($result, true);
            return $decoded !== null ? $decoded : $result;
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT meta_key, meta_value FROM {$this->donation_meta_table} 
                 WHERE donation_id = %s",
                $donation_id
            );
            
            $results = $this->wpdb->get_results($sql, ARRAY_A);
            $meta = array();
            
            foreach ($results as $row) {
                $decoded = json_decode($row['meta_value'], true);
                $meta[$row['meta_key']] = $decoded !== null ? $decoded : $row['meta_value'];
            }
            
            return $meta;
        }
    }
    
    /**
     * Update donation meta
     */
    public function update_donation_meta($donation_id, $meta_key, $meta_value) {
        $existing = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT id FROM {$this->donation_meta_table} 
             WHERE donation_id = %s AND meta_key = %s",
            $donation_id,
            $meta_key
        ));
        
        if ($existing) {
            return $this->wpdb->update(
                $this->donation_meta_table,
                array('meta_value' => is_array($meta_value) ? wp_json_encode($meta_value) : $meta_value),
                array('id' => $existing),
                array('%s'),
                array('%d')
            );
        } else {
            return $this->add_donation_meta($donation_id, $meta_key, $meta_value);
        }
    }
    
    /**
     * Delete donation and related data
     */
    public function delete_donation($donation_id) {
        // Start transaction
        $this->wpdb->query('START TRANSACTION');
        
        try {
            // Delete meta
            $this->wpdb->delete(
                $this->donation_meta_table,
                array('donation_id' => $donation_id),
                array('%s')
            );
            
            // Delete logs
            $this->wpdb->delete(
                $this->donation_logs_table,
                array('donation_id' => $donation_id),
                array('%s')
            );
            
            // Delete main donation record
            $result = $this->wpdb->delete(
                $this->donations_table,
                array('donation_id' => $donation_id),
                array('%s')
            );
            
            if ($result === false) {
                throw new Exception('Failed to delete donation');
            }
            
            $this->wpdb->query('COMMIT');
            return true;
            
        } catch (Exception $e) {
            $this->wpdb->query('ROLLBACK');
            error_log('Error deleting donation: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get recurring donations
     */
    public function get_recurring_donations($status = 'active') {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->donations_table} 
             WHERE recurring = 1 AND status = %s 
             ORDER BY created_at DESC",
            $status
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Search donations
     */
    public function search_donations($search_term, $limit = 20) {
        $search_term = '%' . $this->wpdb->esc_like($search_term) . '%';
        
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->donations_table} 
             WHERE donation_id LIKE %s 
                OR email LIKE %s 
                OR CONCAT(first_name, ' ', last_name) LIKE %s
                OR gateway_transaction_id LIKE %s
             ORDER BY created_at DESC 
             LIMIT %d",
            $search_term,
            $search_term,
            $search_term,
            $search_term,
            $limit
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Export donations to CSV
     */
    public function export_donations_csv($args = array()) {
        $donations = $this->get_donations(array_merge($args, array('limit' => 999999)));
        
        $filename = 'donations_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = wp_upload_dir()['path'] . '/' . $filename;
        
        $file = fopen($filepath, 'w');
        
        // CSV headers
        fputcsv($file, array(
            'Donation ID',
            'Amount',
            'Currency',
            'Status',
            'Payment Method',
            'Transaction ID',
            'Recurring',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Anonymous',
            'Purpose',
            'Created At',
            'Completed At'
        ));
        
        // Data rows
        foreach ($donations as $donation) {
            fputcsv($file, array(
                $donation['donation_id'],
                $donation['amount'],
                $donation['currency'],
                $donation['status'],
                $donation['payment_method'],
                $donation['gateway_transaction_id'],
                $donation['recurring'] ? 'Yes' : 'No',
                $donation['first_name'],
                $donation['last_name'],
                $donation['email'],
                $donation['phone'],
                $donation['anonymous'] ? 'Yes' : 'No',
                $donation['purpose'],
                $donation['created_at'],
                $donation['completed_at']
            ));
        }
        
        fclose($file);
        
        return array(
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => wp_upload_dir()['url'] . '/' . $filename,
            'count' => count($donations)
        );
    }
    
    /**
     * Count donations with filters
     */
    public function count_donations($filters = array()) {
        $where_clauses = array('1=1');
        $where_values = array();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $where_clauses[] = "(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR donation_id LIKE %s)";
            $search_term = '%' . $filters['search'] . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        if (!empty($filters['status'])) {
            $where_clauses[] = "status = %s";
            $where_values[] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where_clauses[] = "DATE(created_at) >= %s";
            $where_values[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where_clauses[] = "DATE(created_at) <= %s";
            $where_values[] = $filters['date_to'];
        }
        
        $where_sql = implode(' AND ', $where_clauses);
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare("SELECT COUNT(*) FROM {$this->donations_table} WHERE {$where_sql}", $where_values);
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->donations_table} WHERE {$where_sql}";
        }
        
        return (int) $this->wpdb->get_var($sql);
    }
    
    /**
     * Get donation statistics for admin dashboard
     */
    public function get_donation_statistics() {
        $stats = array();
        
        // Total donations
        $stats['total_donations'] = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->donations_table}");
        
        // Total amounts by currency
        $stats['total_amount_usd'] = $this->wpdb->get_var("SELECT SUM(amount) FROM {$this->donations_table} WHERE currency = 'USD' AND status = 'completed'") ?: 0;
        $stats['total_amount_tzs'] = $this->wpdb->get_var("SELECT SUM(amount) FROM {$this->donations_table} WHERE currency = 'TZS' AND status = 'completed'") ?: 0;
        
        // This month's donations
        $stats['monthly_count'] = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->donations_table} WHERE MONTH(created_at) = %d AND YEAR(created_at) = %d",
                date('m'),
                date('Y')
            )
        );
        
        // This month's amount
        $stats['monthly_amount_usd'] = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->donations_table} WHERE currency = 'USD' AND status = 'completed' AND MONTH(created_at) = %d AND YEAR(created_at) = %d",
                date('m'),
                date('Y')
            )
        ) ?: 0;
        
        $stats['monthly_amount_tzs'] = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->donations_table} WHERE currency = 'TZS' AND status = 'completed' AND MONTH(created_at) = %d AND YEAR(created_at) = %d",
                date('m'),
                date('Y')
            )
        ) ?: 0;
        
        return $stats;
    }
    
    /**
     * Get analytics data for charts
     */
    public function get_analytics_data() {
        $analytics = array();
        
        // Monthly donation data (last 12 months)
        $monthly_data = $this->wpdb->get_results("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count,
                SUM(CASE WHEN currency = 'USD' AND status = 'completed' THEN amount ELSE 0 END) as amount_usd,
                SUM(CASE WHEN currency = 'TZS' AND status = 'completed' THEN amount ELSE 0 END) as amount_tzs
            FROM {$this->donations_table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        
        $analytics['monthly_data'] = array_map(function($row) {
            return array(
                'month' => $row->month,
                'count' => (int) $row->count,
                'amount' => (float) $row->amount_usd + ((float) $row->amount_tzs / 2500) // Convert TZS to USD for chart
            );
        }, $monthly_data);
        
        // Payment methods distribution
        $payment_methods = $this->wpdb->get_results("
            SELECT payment_method as method, COUNT(*) as count
            FROM {$this->donations_table}
            GROUP BY payment_method
            ORDER BY count DESC
        ");
        
        $analytics['payment_methods'] = array_map(function($row) {
            return array(
                'method' => ucfirst($row->method),
                'count' => (int) $row->count
            );
        }, $payment_methods);
        
        // Currency distribution
        $currencies = $this->wpdb->get_results("
            SELECT 
                currency,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as amount
            FROM {$this->donations_table}
            GROUP BY currency
        ");
        
        $analytics['currencies'] = array_map(function($row) {
            return array(
                'currency' => $row->currency,
                'count' => (int) $row->count,
                'amount' => (float) $row->amount
            );
        }, $currencies);
        
        // Status distribution
        $statuses = $this->wpdb->get_results("
            SELECT status, COUNT(*) as count
            FROM {$this->donations_table}
            GROUP BY status
        ");
        
        $analytics['statuses'] = array_map(function($row) {
            return array(
                'status' => ucfirst($row->status),
                'count' => (int) $row->count
            );
        }, $statuses);
        
        return $analytics;
    }
}


