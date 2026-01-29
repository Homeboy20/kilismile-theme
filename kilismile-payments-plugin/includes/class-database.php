<?php
/**
 * Database Manager Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database Manager
 * 
 * Handles all database operations for the payment plugin including:
 * - Transaction storage and retrieval
 * - Payment settings management
 * - Gateway configurations
 * - Transaction logs and audit trails
 * - Database migrations and updates
 */
class KiliSmile_Payments_Database {
    
    /**
     * Plugin version for database migrations
     */
    const DB_VERSION = '1.0.0';
    
    /**
     * Table names
     */
    private $tables = array();
    
    /**
     * WordPress database object
     */
    private $wpdb;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // Define table names
        $this->tables = array(
            'transactions' => $this->wpdb->prefix . 'kilismile_transactions',
            'transaction_meta' => $this->wpdb->prefix . 'kilismile_transaction_meta',
            'payment_logs' => $this->wpdb->prefix . 'kilismile_payment_logs',
            'gateway_settings' => $this->wpdb->prefix . 'kilismile_gateway_settings'
        );
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        $this->create_transactions_table();
        $this->create_transaction_meta_table();
        $this->create_payment_logs_table();
        $this->create_gateway_settings_table();
        
        // Update database version
        update_option('kilismile_payments_db_version', self::DB_VERSION);
    }
    
    /**
     * Create transactions table
     */
    private function create_transactions_table() {
        $table_name = $this->tables['transactions'];
        
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            gateway varchar(50) NOT NULL,
            transaction_type enum('donation', 'subscription', 'corporate') NOT NULL DEFAULT 'donation',
            reference_id varchar(100) NOT NULL,
            gateway_transaction_id varchar(255) DEFAULT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL DEFAULT 'USD',
            original_amount decimal(10,2) DEFAULT NULL,
            original_currency varchar(3) DEFAULT NULL,
            status enum('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
            donor_name varchar(255) NOT NULL,
            donor_email varchar(255) NOT NULL,
            donor_phone varchar(20) DEFAULT NULL,
            donor_address text DEFAULT NULL,
            payment_method varchar(50) DEFAULT NULL,
            recurring tinyint(1) NOT NULL DEFAULT 0,
            recurring_interval enum('monthly', 'quarterly', 'yearly') DEFAULT NULL,
            parent_transaction_id bigint(20) UNSIGNED DEFAULT NULL,
            notes text DEFAULT NULL,
            gateway_response longtext DEFAULT NULL,
            webhook_data longtext DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            completed_at datetime DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_gateway (gateway),
            KEY idx_status (status),
            KEY idx_reference_id (reference_id),
            KEY idx_gateway_transaction_id (gateway_transaction_id),
            KEY idx_donor_email (donor_email),
            KEY idx_created_at (created_at),
            KEY idx_parent_transaction (parent_transaction_id),
            KEY idx_recurring (recurring),
            CONSTRAINT fk_parent_transaction FOREIGN KEY (parent_transaction_id) REFERENCES $table_name (id) ON DELETE SET NULL
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create transaction meta table
     */
    private function create_transaction_meta_table() {
        $table_name = $this->tables['transaction_meta'];
        
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            transaction_id bigint(20) UNSIGNED NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext DEFAULT NULL,
            PRIMARY KEY (meta_id),
            KEY idx_transaction_id (transaction_id),
            KEY idx_meta_key (meta_key(191)),
            CONSTRAINT fk_transaction_meta FOREIGN KEY (transaction_id) REFERENCES {$this->tables['transactions']} (id) ON DELETE CASCADE
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create payment logs table
     */
    private function create_payment_logs_table() {
        $table_name = $this->tables['payment_logs'];
        
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            transaction_id bigint(20) UNSIGNED DEFAULT NULL,
            gateway varchar(50) NOT NULL,
            level enum('debug', 'info', 'warning', 'error', 'critical') NOT NULL DEFAULT 'info',
            message text NOT NULL,
            context longtext DEFAULT NULL,
            created_at datetime NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_id bigint(20) UNSIGNED DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_transaction_id (transaction_id),
            KEY idx_gateway (gateway),
            KEY idx_level (level),
            KEY idx_created_at (created_at),
            KEY idx_user_id (user_id),
            CONSTRAINT fk_log_transaction FOREIGN KEY (transaction_id) REFERENCES {$this->tables['transactions']} (id) ON DELETE SET NULL
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Create gateway settings table
     */
    private function create_gateway_settings_table() {
        $table_name = $this->tables['gateway_settings'];
        
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            gateway varchar(50) NOT NULL,
            setting_key varchar(255) NOT NULL,
            setting_value longtext DEFAULT NULL,
            is_encrypted tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_gateway_setting (gateway, setting_key),
            KEY idx_gateway (gateway),
            KEY idx_setting_key (setting_key(191))
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Save transaction
     */
    public function save_transaction($data) {
        $defaults = array(
            'gateway' => '',
            'transaction_type' => 'donation',
            'reference_id' => '',
            'amount' => 0,
            'currency' => 'USD',
            'status' => 'pending',
            'donor_name' => '',
            'donor_email' => '',
            'recurring' => 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '')
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Generate reference ID if not provided
        if (empty($data['reference_id'])) {
            $data['reference_id'] = $this->generate_reference_id($data['gateway']);
        }
        
        // Serialize complex data
        if (isset($data['gateway_response']) && is_array($data['gateway_response'])) {
            $data['gateway_response'] = maybe_serialize($data['gateway_response']);
        }
        
        if (isset($data['webhook_data']) && is_array($data['webhook_data'])) {
            $data['webhook_data'] = maybe_serialize($data['webhook_data']);
        }
        
        $result = $this->wpdb->insert(
            $this->tables['transactions'],
            $data,
            array(
                '%s', // gateway
                '%s', // transaction_type
                '%s', // reference_id
                '%s', // gateway_transaction_id
                '%f', // amount
                '%s', // currency
                '%f', // original_amount
                '%s', // original_currency
                '%s', // status
                '%s', // donor_name
                '%s', // donor_email
                '%s', // donor_phone
                '%s', // donor_address
                '%s', // payment_method
                '%d', // recurring
                '%s', // recurring_interval
                '%d', // parent_transaction_id
                '%s', // notes
                '%s', // gateway_response
                '%s', // webhook_data
                '%s', // created_at
                '%s', // updated_at
                '%s', // completed_at
                '%s', // ip_address
                '%s'  // user_agent
            )
        );
        
        if ($result === false) {
            return false;
        }
        
        return $this->wpdb->insert_id;
    }
    
    /**
     * Update transaction
     */
    public function update_transaction($transaction_id, $data) {
        $data['updated_at'] = current_time('mysql');
        
        // Set completed_at if status is completed
        if (isset($data['status']) && $data['status'] === 'completed' && !isset($data['completed_at'])) {
            $data['completed_at'] = current_time('mysql');
        }
        
        // Serialize complex data
        if (isset($data['gateway_response']) && is_array($data['gateway_response'])) {
            $data['gateway_response'] = maybe_serialize($data['gateway_response']);
        }
        
        if (isset($data['webhook_data']) && is_array($data['webhook_data'])) {
            $data['webhook_data'] = maybe_serialize($data['webhook_data']);
        }
        
        $result = $this->wpdb->update(
            $this->tables['transactions'],
            $data,
            array('id' => $transaction_id),
            null,
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Get transaction by ID
     */
    public function get_transaction($transaction_id) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->tables['transactions']} WHERE id = %d",
            $transaction_id
        );
        
        $transaction = $this->wpdb->get_row($sql, ARRAY_A);
        
        if ($transaction) {
            // Unserialize complex data
            if (!empty($transaction['gateway_response'])) {
                $transaction['gateway_response'] = maybe_unserialize($transaction['gateway_response']);
            }
            
            if (!empty($transaction['webhook_data'])) {
                $transaction['webhook_data'] = maybe_unserialize($transaction['webhook_data']);
            }
        }
        
        return $transaction;
    }
    
    /**
     * Get transaction by reference ID
     */
    public function get_transaction_by_reference($reference_id) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->tables['transactions']} WHERE reference_id = %s",
            $reference_id
        );
        
        $transaction = $this->wpdb->get_row($sql, ARRAY_A);
        
        if ($transaction) {
            // Unserialize complex data
            if (!empty($transaction['gateway_response'])) {
                $transaction['gateway_response'] = maybe_unserialize($transaction['gateway_response']);
            }
            
            if (!empty($transaction['webhook_data'])) {
                $transaction['webhook_data'] = maybe_unserialize($transaction['webhook_data']);
            }
        }
        
        return $transaction;
    }
    
    /**
     * Get transaction by gateway transaction ID
     */
    public function get_transaction_by_gateway_id($gateway_transaction_id) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->tables['transactions']} WHERE gateway_transaction_id = %s",
            $gateway_transaction_id
        );
        
        return $this->wpdb->get_row($sql, ARRAY_A);
    }
    
    /**
     * Get transactions with pagination
     */
    public function get_transactions($args = array()) {
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'gateway' => '',
            'status' => '',
            'donor_email' => '',
            'start_date' => '',
            'end_date' => '',
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        if (!empty($args['gateway'])) {
            $where_conditions[] = 'gateway = %s';
            $where_values[] = $args['gateway'];
        }
        
        if (!empty($args['status'])) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if (!empty($args['donor_email'])) {
            $where_conditions[] = 'donor_email = %s';
            $where_values[] = $args['donor_email'];
        }
        
        if (!empty($args['start_date'])) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if (!empty($args['end_date'])) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['end_date'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Build query
        $sql = "SELECT * FROM {$this->tables['transactions']} WHERE {$where_clause}";
        
        // Add ordering
        $allowed_order_by = array('id', 'created_at', 'amount', 'status', 'gateway');
        $order_by = in_array($args['order_by'], $allowed_order_by) ? $args['order_by'] : 'created_at';
        $order = in_array(strtoupper($args['order']), array('ASC', 'DESC')) ? $args['order'] : 'DESC';
        
        $sql .= " ORDER BY {$order_by} {$order}";
        
        // Add pagination
        if ($args['limit'] > 0) {
            $sql .= " LIMIT {$args['offset']}, {$args['limit']}";
        }
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Get transaction count
     */
    public function get_transaction_count($args = array()) {
        $defaults = array(
            'gateway' => '',
            'status' => '',
            'donor_email' => '',
            'start_date' => '',
            'end_date' => ''
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        if (!empty($args['gateway'])) {
            $where_conditions[] = 'gateway = %s';
            $where_values[] = $args['gateway'];
        }
        
        if (!empty($args['status'])) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if (!empty($args['donor_email'])) {
            $where_conditions[] = 'donor_email = %s';
            $where_values[] = $args['donor_email'];
        }
        
        if (!empty($args['start_date'])) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if (!empty($args['end_date'])) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['end_date'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "SELECT COUNT(*) FROM {$this->tables['transactions']} WHERE {$where_clause}";
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        return intval($this->wpdb->get_var($sql));
    }
    
    /**
     * Add transaction meta
     */
    public function add_transaction_meta($transaction_id, $meta_key, $meta_value) {
        return $this->wpdb->insert(
            $this->tables['transaction_meta'],
            array(
                'transaction_id' => $transaction_id,
                'meta_key' => $meta_key,
                'meta_value' => maybe_serialize($meta_value)
            ),
            array('%d', '%s', '%s')
        );
    }
    
    /**
     * Get transaction meta
     */
    public function get_transaction_meta($transaction_id, $meta_key = '') {
        if (!empty($meta_key)) {
            $sql = $this->wpdb->prepare(
                "SELECT meta_value FROM {$this->tables['transaction_meta']} WHERE transaction_id = %d AND meta_key = %s",
                $transaction_id,
                $meta_key
            );
            
            $value = $this->wpdb->get_var($sql);
            return maybe_unserialize($value);
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT meta_key, meta_value FROM {$this->tables['transaction_meta']} WHERE transaction_id = %d",
                $transaction_id
            );
            
            $results = $this->wpdb->get_results($sql);
            $meta = array();
            
            foreach ($results as $row) {
                $meta[$row->meta_key] = maybe_unserialize($row->meta_value);
            }
            
            return $meta;
        }
    }
    
    /**
     * Update transaction meta
     */
    public function update_transaction_meta($transaction_id, $meta_key, $meta_value) {
        $existing = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT meta_id FROM {$this->tables['transaction_meta']} WHERE transaction_id = %d AND meta_key = %s",
            $transaction_id,
            $meta_key
        ));
        
        if ($existing) {
            return $this->wpdb->update(
                $this->tables['transaction_meta'],
                array('meta_value' => maybe_serialize($meta_value)),
                array('transaction_id' => $transaction_id, 'meta_key' => $meta_key),
                array('%s'),
                array('%d', '%s')
            );
        } else {
            return $this->add_transaction_meta($transaction_id, $meta_key, $meta_value);
        }
    }
    
    /**
     * Delete transaction meta
     */
    public function delete_transaction_meta($transaction_id, $meta_key = '') {
        if (!empty($meta_key)) {
            return $this->wpdb->delete(
                $this->tables['transaction_meta'],
                array('transaction_id' => $transaction_id, 'meta_key' => $meta_key),
                array('%d', '%s')
            );
        } else {
            return $this->wpdb->delete(
                $this->tables['transaction_meta'],
                array('transaction_id' => $transaction_id),
                array('%d')
            );
        }
    }
    
    /**
     * Add payment log
     */
    public function add_log($data) {
        $defaults = array(
            'gateway' => '',
            'level' => 'info',
            'message' => '',
            'context' => null,
            'created_at' => current_time('mysql'),
            'ip_address' => $this->get_client_ip(),
            'user_id' => get_current_user_id()
        );
        
        $data = wp_parse_args($data, $defaults);
        
        if (is_array($data['context']) || is_object($data['context'])) {
            $data['context'] = maybe_serialize($data['context']);
        }
        
        return $this->wpdb->insert(
            $this->tables['payment_logs'],
            $data,
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );
    }
    
    /**
     * Get payment logs
     */
    public function get_logs($args = array()) {
        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'gateway' => '',
            'level' => '',
            'transaction_id' => '',
            'start_date' => '',
            'end_date' => '',
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        if (!empty($args['gateway'])) {
            $where_conditions[] = 'gateway = %s';
            $where_values[] = $args['gateway'];
        }
        
        if (!empty($args['level'])) {
            $where_conditions[] = 'level = %s';
            $where_values[] = $args['level'];
        }
        
        if (!empty($args['transaction_id'])) {
            $where_conditions[] = 'transaction_id = %d';
            $where_values[] = $args['transaction_id'];
        }
        
        if (!empty($args['start_date'])) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if (!empty($args['end_date'])) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['end_date'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "SELECT * FROM {$this->tables['payment_logs']} WHERE {$where_clause}";
        
        // Add ordering
        $allowed_order_by = array('id', 'created_at', 'level', 'gateway');
        $order_by = in_array($args['order_by'], $allowed_order_by) ? $args['order_by'] : 'created_at';
        $order = in_array(strtoupper($args['order']), array('ASC', 'DESC')) ? $args['order'] : 'DESC';
        
        $sql .= " ORDER BY {$order_by} {$order}";
        
        // Add pagination
        if ($args['limit'] > 0) {
            $sql .= " LIMIT {$args['offset']}, {$args['limit']}";
        }
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        $logs = $this->wpdb->get_results($sql, ARRAY_A);
        
        // Unserialize context data
        foreach ($logs as &$log) {
            if (!empty($log['context'])) {
                $log['context'] = maybe_unserialize($log['context']);
            }
        }
        
        return $logs;
    }
    
    /**
     * Save gateway setting
     */
    public function save_gateway_setting($gateway, $setting_key, $setting_value, $is_encrypted = false) {
        $existing = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT id FROM {$this->tables['gateway_settings']} WHERE gateway = %s AND setting_key = %s",
            $gateway,
            $setting_key
        ));
        
        $data = array(
            'gateway' => $gateway,
            'setting_key' => $setting_key,
            'setting_value' => maybe_serialize($setting_value),
            'is_encrypted' => $is_encrypted ? 1 : 0,
            'updated_at' => current_time('mysql')
        );
        
        if ($existing) {
            return $this->wpdb->update(
                $this->tables['gateway_settings'],
                $data,
                array('id' => $existing),
                array('%s', '%s', '%s', '%d', '%s'),
                array('%d')
            );
        } else {
            $data['created_at'] = current_time('mysql');
            return $this->wpdb->insert(
                $this->tables['gateway_settings'],
                $data,
                array('%s', '%s', '%s', '%d', '%s', '%s')
            );
        }
    }
    
    /**
     * Get gateway setting
     */
    public function get_gateway_setting($gateway, $setting_key = '', $default = null) {
        if (!empty($setting_key)) {
            $sql = $this->wpdb->prepare(
                "SELECT setting_value FROM {$this->tables['gateway_settings']} WHERE gateway = %s AND setting_key = %s",
                $gateway,
                $setting_key
            );
            
            $value = $this->wpdb->get_var($sql);
            
            if ($value === null) {
                return $default;
            }
            
            return maybe_unserialize($value);
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT setting_key, setting_value FROM {$this->tables['gateway_settings']} WHERE gateway = %s",
                $gateway
            );
            
            $results = $this->wpdb->get_results($sql);
            $settings = array();
            
            foreach ($results as $row) {
                $settings[$row->setting_key] = maybe_unserialize($row->setting_value);
            }
            
            return $settings;
        }
    }
    
    /**
     * Delete gateway setting
     */
    public function delete_gateway_setting($gateway, $setting_key = '') {
        if (!empty($setting_key)) {
            return $this->wpdb->delete(
                $this->tables['gateway_settings'],
                array('gateway' => $gateway, 'setting_key' => $setting_key),
                array('%s', '%s')
            );
        } else {
            return $this->wpdb->delete(
                $this->tables['gateway_settings'],
                array('gateway' => $gateway),
                array('%s')
            );
        }
    }
    
    /**
     * Generate unique reference ID
     */
    public function generate_reference_id($gateway = '') {
        $prefix = strtoupper(substr($gateway, 0, 3));
        if (empty($prefix)) {
            $prefix = 'KS';
        }
        
        return $prefix . '_' . time() . '_' . wp_rand(1000, 9999);
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = sanitize_text_field($_SERVER[$key]);
                
                // Handle comma-separated IPs
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return '127.0.0.1';
    }
    
    /**
     * Get statistics
     */
    public function get_statistics($gateway = '', $period = '30_days') {
        $where_conditions = array('1=1');
        $where_values = array();
        
        if (!empty($gateway)) {
            $where_conditions[] = 'gateway = %s';
            $where_values[] = $gateway;
        }
        
        // Add period filter
        switch ($period) {
            case '7_days':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case '30_days':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
            case '90_days':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)';
                break;
            case '1_year':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
                break;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "
            SELECT 
                COUNT(*) as total_transactions,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful_transactions,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_transactions,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
                AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as average_amount,
                MIN(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as min_amount,
                MAX(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as max_amount
            FROM {$this->tables['transactions']} 
            WHERE {$where_clause}
        ";
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare($sql, $where_values);
        }
        
        $stats = $this->wpdb->get_row($sql, ARRAY_A);
        
        // Calculate success rate
        if ($stats['total_transactions'] > 0) {
            $stats['success_rate'] = ($stats['successful_transactions'] / $stats['total_transactions']) * 100;
        } else {
            $stats['success_rate'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Drop all tables (for uninstall)
     */
    public function drop_tables() {
        $tables = array_reverse($this->tables); // Drop in reverse order due to foreign keys
        
        foreach ($tables as $table) {
            $this->wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
        
        delete_option('kilismile_payments_db_version');
    }
    
    /**
     * Get table names
     */
    public function get_tables() {
        return $this->tables;
    }
}

