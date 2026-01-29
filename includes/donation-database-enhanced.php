<?php
/**
 * Enhanced Donation Database with Additional Fields
 * 
 * Extends the donation database to support enhanced collection fields
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Donation Database Class
 */
class KiliSmile_Donation_Database_Enhanced extends KiliSmile_Donation_Database {
    
    /**
     * Create enhanced database tables
     */
    public function create_tables() {
        // Call parent to create base tables
        parent::create_tables();
        
        // Add enhanced columns to donations table
        $this->add_enhanced_columns();
    }
    
    /**
     * Add enhanced columns to donations table
     */
    private function add_enhanced_columns() {
        global $wpdb;
        
        $table_name = $this->donations_table;
        $charset_collate = $this->wpdb->get_charset_collate();
        
        // Enhanced address fields
        $enhanced_columns = array(
            'address_line1' => 'varchar(100) DEFAULT NULL',
            'address_line2' => 'varchar(100) DEFAULT NULL',
            'city' => 'varchar(50) DEFAULT NULL',
            'state_province' => 'varchar(50) DEFAULT NULL',
            'postal_code' => 'varchar(20) DEFAULT NULL',
            'donation_campaign' => 'varchar(100) DEFAULT NULL',
            'is_tribute' => 'tinyint(1) DEFAULT 0',
            'tribute_type' => 'varchar(50) DEFAULT NULL',
            'tribute_name' => 'varchar(100) DEFAULT NULL',
            'tribute_message' => 'text DEFAULT NULL',
            'notify_tribute' => 'tinyint(1) DEFAULT 0',
            'tribute_notification_name' => 'varchar(100) DEFAULT NULL',
            'tribute_notification_email' => 'varchar(100) DEFAULT NULL',
            'employer_organization' => 'varchar(100) DEFAULT NULL',
            'employer_match' => 'tinyint(1) DEFAULT 0',
            'newsletter_subscribe' => 'tinyint(1) DEFAULT 1',
            'receive_updates' => 'tinyint(1) DEFAULT 1',
            'communication_preference' => 'varchar(20) DEFAULT NULL'
        );
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        foreach ($enhanced_columns as $column => $definition) {
            // Check if column exists
            $column_exists = $wpdb->get_results($wpdb->prepare(
                "SHOW COLUMNS FROM {$table_name} LIKE %s",
                $column
            ));
            
            if (empty($column_exists)) {
                $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN {$column} {$definition}");
            }
        }
        
        // Add indexes for commonly queried fields
        $indexes = array('donation_campaign', 'is_tribute', 'country', 'city');
        foreach ($indexes as $index_column) {
            $index_name = 'idx_' . $index_column;
            $index_exists = $wpdb->get_results($wpdb->prepare(
                "SHOW INDEX FROM {$table_name} WHERE Key_name = %s",
                $index_name
            ));
            
            if (empty($index_exists)) {
                $wpdb->query("ALTER TABLE {$table_name} ADD INDEX {$index_name} ({$index_column})");
            }
        }
    }
    
    /**
     * Enhanced create donation with all fields
     */
    public function create_donation_enhanced($donation_data) {
        // Base donation data
        $data = array(
            'donation_id' => $donation_data['donation_id'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'status' => 'pending',
            'payment_method' => $donation_data['payment_method'],
            'recurring' => isset($donation_data['recurring']) ? (int)$donation_data['recurring'] : 0,
            'first_name' => $donation_data['first_name'],
            'last_name' => $donation_data['last_name'],
            'email' => $donation_data['email'],
            'phone' => $donation_data['phone'] ?? '',
            'anonymous' => isset($donation_data['anonymous']) ? (int)$donation_data['anonymous'] : 0,
            'purpose' => $donation_data['purpose'] ?? 'general',
            'message' => $donation_data['message'] ?? $donation_data['donation_message'] ?? '',
            'country' => $donation_data['country'] ?? '',
            'ip_address' => $donation_data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $donation_data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
        
        // Enhanced address fields
        $enhanced_fields = array(
            'address_line1', 'address_line2', 'city', 'state_province', 'postal_code',
            'donation_campaign', 'is_tribute', 'tribute_type', 'tribute_name',
            'tribute_message', 'notify_tribute', 'tribute_notification_name',
            'tribute_notification_email', 'employer_organization', 'employer_match',
            'newsletter_subscribe', 'receive_updates', 'communication_preference'
        );
        
        foreach ($enhanced_fields as $field) {
            if (isset($donation_data[$field])) {
                if (in_array($field, array('is_tribute', 'notify_tribute', 'employer_match', 'newsletter_subscribe', 'receive_updates'))) {
                    $data[$field] = (int)$donation_data[$field];
                } else {
                    $data[$field] = sanitize_text_field($donation_data[$field]);
                }
            }
        }
        
        $formats = array();
        foreach ($data as $value) {
            if (is_int($value)) {
                $formats[] = '%d';
            } elseif (is_float($value)) {
                $formats[] = '%f';
            } else {
                $formats[] = '%s';
            }
        }
        
        $result = $this->wpdb->insert($this->donations_table, $data, $formats);
        
        if ($result) {
            $this->log_event($donation_data['donation_id'], 'donation_created_enhanced', $data);
            
            // Handle newsletter subscription
            if (!empty($data['newsletter_subscribe']) && !empty($data['email'])) {
                $this->subscribe_to_newsletter($data['email'], $data['first_name'], $data['last_name']);
            }
            
            // Handle tribute notification
            if (!empty($data['is_tribute']) && !empty($data['notify_tribute']) && !empty($data['tribute_notification_email'])) {
                $this->send_tribute_notification($donation_data);
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Subscribe to newsletter
     */
    private function subscribe_to_newsletter($email, $first_name, $last_name) {
        // Check if newsletter function exists
        if (function_exists('kilismile_subscribe_newsletter')) {
            kilismile_subscribe_newsletter(array(
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'source' => 'donation_form'
            ));
        }
    }
    
    /**
     * Send tribute notification email
     */
    private function send_tribute_notification($donation_data) {
        $to = $donation_data['tribute_notification_email'];
        $subject = sprintf(
            __('Tribute Donation Notification - %s', 'kilismile'),
            get_bloginfo('name')
        );
        
        $message = sprintf(
            __('Dear %s,

A donation has been made %s %s.

Donation Amount: %s %s
Donor: %s %s
Message: %s

Thank you for your support.

%s', 'kilismile'),
            $donation_data['tribute_notification_name'],
            $donation_data['tribute_type'] === 'memory' ? __('in memory of', 'kilismile') : __('in honor of', 'kilismile'),
            $donation_data['tribute_name'],
            $donation_data['currency'],
            number_format($donation_data['amount'], 2),
            $donation_data['first_name'],
            $donation_data['last_name'],
            $donation_data['tribute_message'] ?? '',
            get_bloginfo('name')
        );
        
        wp_mail($to, $subject, $message);
    }
    
    /**
     * Get donations with enhanced filters
     */
    public function get_donations_enhanced($args = array()) {
        $defaults = array(
            'status' => null,
            'currency' => null,
            'payment_method' => null,
            'date_from' => null,
            'date_to' => null,
            'search' => null,
            'campaign' => null,
            'is_tribute' => null,
            'country' => null,
            'city' => null,
            'limit' => 50,
            'offset' => 0,
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        // Apply all filters
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
        
        if (!empty($args['campaign'])) {
            $where_conditions[] = 'donation_campaign = %s';
            $where_values[] = $args['campaign'];
        }
        
        if (isset($args['is_tribute']) && $args['is_tribute'] !== null) {
            $where_conditions[] = 'is_tribute = %d';
            $where_values[] = (int)$args['is_tribute'];
        }
        
        if (!empty($args['country'])) {
            $where_conditions[] = 'country = %s';
            $where_values[] = $args['country'];
        }
        
        if (!empty($args['city'])) {
            $where_conditions[] = 'city = %s';
            $where_values[] = $args['city'];
        }
        
        if (!empty($args['search'])) {
            $where_conditions[] = '(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR donation_id LIKE %s OR phone LIKE %s)';
            $search_term = '%' . $args['search'] . '%';
            $where_values[] = $search_term;
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
     * Get enhanced statistics
     */
    public function get_enhanced_statistics() {
        $stats = parent::get_donation_statistics();
        
        // Tribute donations
        $stats['tribute_donations'] = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->donations_table} WHERE is_tribute = 1 AND status = 'completed'"
        );
        
        $stats['tribute_amount'] = $this->wpdb->get_var(
            "SELECT SUM(amount) FROM {$this->donations_table} WHERE is_tribute = 1 AND status = 'completed'"
        ) ?: 0;
        
        // Campaign statistics
        $stats['campaign_donations'] = $this->wpdb->get_results(
            "SELECT donation_campaign, COUNT(*) as count, SUM(amount) as total
             FROM {$this->donations_table}
             WHERE donation_campaign IS NOT NULL AND status = 'completed'
             GROUP BY donation_campaign
             ORDER BY total DESC"
        );
        
        // Geographic distribution
        $stats['by_country'] = $this->wpdb->get_results(
            "SELECT country, COUNT(*) as count, SUM(amount) as total
             FROM {$this->donations_table}
             WHERE country IS NOT NULL AND status = 'completed'
             GROUP BY country
             ORDER BY total DESC
             LIMIT 10"
        );
        
        // Employer matching
        $stats['employer_match_count'] = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->donations_table} WHERE employer_match = 1 AND status = 'completed'"
        );
        
        return $stats;
    }
}
