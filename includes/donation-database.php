<?php
/**
 * Modern Donation Database Handler
 * 
 * Handles all database operations for the donation system
 * with proper security, indexing, and performance optimization.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donation Database Handler Class
 */
class KiliSmile_Donation_Database {
    
    private $wpdb;
    private $donations_table;
    private $logs_table;
    private $meta_table;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // Table names with WordPress prefix
        $this->donations_table = $this->wpdb->prefix . 'donations';
        $this->logs_table = $this->wpdb->prefix . 'donation_logs';
        $this->meta_table = $this->wpdb->prefix . 'donation_meta';
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        // Donations table
        $donations_sql = "CREATE TABLE {$this->donations_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL DEFAULT 'USD',
            status varchar(20) NOT NULL DEFAULT 'pending',
            payment_method varchar(50) NOT NULL,
            gateway_transaction_id varchar(100) DEFAULT NULL,
            recurring tinyint(1) NOT NULL DEFAULT 0,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) DEFAULT NULL,
            anonymous tinyint(1) NOT NULL DEFAULT 0,
            purpose varchar(100) DEFAULT 'general',
            message text DEFAULT NULL,
            country varchar(2) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY donation_id (donation_id),
            KEY status (status),
            KEY payment_method (payment_method),
            KEY currency (currency),
            KEY created_at (created_at),
            KEY email (email)
        ) $charset_collate;";
        
        // Donation logs table
        $logs_sql = "CREATE TABLE {$this->logs_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL,
            event_type varchar(50) NOT NULL,
            event_data longtext DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY event_type (event_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Donation meta table
        $meta_sql = "CREATE TABLE {$this->meta_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            donation_id varchar(50) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY meta_key (meta_key)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($donations_sql);
        dbDelta($logs_sql);
        dbDelta($meta_sql);
    }
    
    /**
     * Create a new donation
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
            'phone' => $donation_data['phone'] ?? '',
            'anonymous' => $donation_data['anonymous'] ? 1 : 0,
            'purpose' => $donation_data['purpose'] ?? 'general',
            'message' => $donation_data['message'] ?? '',
            'country' => $donation_data['country'] ?? '',
            'ip_address' => $donation_data['ip_address'] ?? '',
            'user_agent' => $donation_data['user_agent'] ?? '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
        
        $formats = array(
            '%s', '%f', '%s', '%s', '%s', '%d', 
            '%s', '%s', '%s', '%s', '%d', '%s', 
            '%s', '%s', '%s', '%s', '%s', '%s'
        );
        
        $result = $this->wpdb->insert($this->donations_table, $data, $formats);
        
        if ($result) {
            $this->log_event($donation_data['donation_id'], 'donation_created', $data);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a single donation by ID
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
            $this->log_event($donation_id, 'status_updated', array(
                'old_status' => $this->get_donation($donation_id)['status'] ?? 'unknown',
                'new_status' => $status,
                'gateway_transaction_id' => $gateway_transaction_id
            ));
            return true;
        }
        
        return false;
    }
    
    /**
     * Update donation with arbitrary data
     */
    public function update_donation($donation_id, $data) {
        $formats = array();
        
        // Determine formats based on data types
        foreach ($data as $key => $value) {
            if (in_array($key, array('amount'))) {
                $formats[] = '%f';
            } elseif (in_array($key, array('anonymous'))) {
                $formats[] = '%d';
            } else {
                $formats[] = '%s';
            }
        }
        
        $result = $this->wpdb->update(
            $this->donations_table,
            $data,
            array('donation_id' => $donation_id),
            $formats,
            array('%s')
        );
        
        if ($result !== false) {
            $this->log_event($donation_id, 'donation_updated', array(
                'updated_fields' => array_keys($data)
            ));
            return true;
        }
        
        return false;
    }
    
    /**
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
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        // Apply filters
        if (!empty($args['status'])) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if (!empty($args['currency'])) {
            $where_conditions[] = 'currency = %s';
            $where_values[] = $args['currency'];
        }
        
        if (!empty($args['payment_method'])) {
            $where_conditions[] = 'payment_method = %s';
            $where_values[] = $args['payment_method'];
        }
        
        if (!empty($args['date_from'])) {
            $where_conditions[] = 'DATE(created_at) >= %s';
            $where_values[] = $args['date_from'];
        }
        
        if (!empty($args['date_to'])) {
            $where_conditions[] = 'DATE(created_at) <= %s';
            $where_values[] = $args['date_to'];
        }
        
        if (!empty($args['search'])) {
            $where_conditions[] = '(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR donation_id LIKE %s)';
            $search_term = '%' . $args['search'] . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        $where_sql = implode(' AND ', $where_conditions);
        $order_sql = sprintf('ORDER BY %s %s', $args['order_by'], $args['order']);
        $limit_sql = sprintf('LIMIT %d OFFSET %d', $args['limit'], $args['offset']);
        
        if (!empty($where_values)) {
            $sql = $this->wpdb->prepare(
                "SELECT * FROM {$this->donations_table} WHERE {$where_sql} {$order_sql} {$limit_sql}",
                $where_values
            );
        } else {
            $sql = "SELECT * FROM {$this->donations_table} WHERE {$where_sql} {$order_sql} {$limit_sql}";
        }
        
        return $this->wpdb->get_results($sql);
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
    
    /**
     * Log donation events
     */
    public function log_event($donation_id, $event_type, $event_data = array()) {
        $data = array(
            'donation_id' => $donation_id,
            'event_type' => $event_type,
            'event_data' => is_array($event_data) ? json_encode($event_data) : $event_data,
            'created_at' => current_time('mysql')
        );
        
        $formats = array('%s', '%s', '%s', '%s');
        
        return $this->wpdb->insert($this->logs_table, $data, $formats);
    }
    
    /**
     * Get donation logs
     */
    public function get_donation_logs($donation_id, $limit = 50) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->logs_table} WHERE donation_id = %s ORDER BY created_at DESC LIMIT %d",
            $donation_id,
            $limit
        );
        
        $logs = $this->wpdb->get_results($sql);
        
        // Decode JSON event data
        foreach ($logs as $log) {
            $log->event_data = json_decode($log->event_data, true);
        }
        
        return $logs;
    }
    
    /**
     * Add donation meta
     */
    public function add_donation_meta($donation_id, $meta_key, $meta_value) {
        $data = array(
            'donation_id' => $donation_id,
            'meta_key' => $meta_key,
            'meta_value' => is_array($meta_value) ? json_encode($meta_value) : $meta_value
        );
        
        return $this->wpdb->insert($this->meta_table, $data, array('%s', '%s', '%s'));
    }
    
    /**
     * Get donation meta
     */
    public function get_donation_meta($donation_id, $meta_key = null) {
        if ($meta_key) {
            $sql = $this->wpdb->prepare(
                "SELECT meta_value FROM {$this->meta_table} WHERE donation_id = %s AND meta_key = %s",
                $donation_id,
                $meta_key
            );
            
            $value = $this->wpdb->get_var($sql);

            if ($value === null || $value === '') {
                return $value;
            }

            // Try to decode JSON
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        } else {
            $sql = $this->wpdb->prepare(
                "SELECT meta_key, meta_value FROM {$this->meta_table} WHERE donation_id = %s",
                $donation_id
            );
            
            $results = $this->wpdb->get_results($sql);
            $meta = array();
            
            foreach ($results as $row) {
                if ($row->meta_value === null || $row->meta_value === '') {
                    $meta[$row->meta_key] = $row->meta_value;
                    continue;
                }

                $decoded = json_decode($row->meta_value, true);
                $meta[$row->meta_key] = $decoded !== null ? $decoded : $row->meta_value;
            }
            
            return $meta;
        }
    }
    
    /**
     * Update donation meta
     */
    public function update_donation_meta($donation_id, $meta_key, $meta_value) {
        // Check if meta exists
        $existing = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT id FROM {$this->meta_table} WHERE donation_id = %s AND meta_key = %s",
            $donation_id,
            $meta_key
        ));
        
        $value = is_array($meta_value) ? json_encode($meta_value) : $meta_value;
        
        if ($existing) {
            return $this->wpdb->update(
                $this->meta_table,
                array('meta_value' => $value),
                array('donation_id' => $donation_id, 'meta_key' => $meta_key),
                array('%s'),
                array('%s', '%s')
            );
        } else {
            return $this->add_donation_meta($donation_id, $meta_key, $meta_value);
        }
    }
    
    /**
     * Delete donation and all related data
     */
    public function delete_donation($donation_id) {
        // Start transaction
        $this->wpdb->query('START TRANSACTION');
        
        try {
            // Delete meta
            $this->wpdb->delete($this->meta_table, array('donation_id' => $donation_id), array('%s'));
            
            // Delete logs
            $this->wpdb->delete($this->logs_table, array('donation_id' => $donation_id), array('%s'));
            
            // Delete donation
            $result = $this->wpdb->delete($this->donations_table, array('donation_id' => $donation_id), array('%s'));
            
            if ($result) {
                $this->wpdb->query('COMMIT');
                return true;
            } else {
                $this->wpdb->query('ROLLBACK');
                return false;
            }
        } catch (Exception $e) {
            $this->wpdb->query('ROLLBACK');
            return false;
        }
    }
}


