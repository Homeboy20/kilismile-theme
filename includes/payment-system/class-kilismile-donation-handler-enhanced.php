<?php
/**
 * Enhanced Donation Handler
 * 
 * Extends the base donation handler with advanced features like
 * queue management, analytics, and comprehensive error handling.
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Donation Handler Class
 */
class KiliSmile_Donation_Handler_Enhanced extends KiliSmile_Donation_Handler {
    
    /**
     * Queue manager instance
     */
    protected $queue_manager;
    
    /**
     * Analytics tracker instance
     */
    protected $analytics;
    
    /**
     * Cache manager
     */
    protected $cache;
    
    /**
     * Enhanced constructor
     */
    public function __construct() {
        parent::__construct();
        
        // Initialize enhanced components
        $this->init_enhanced_components();
        
        // Register enhanced hooks
        $this->register_enhanced_hooks();
        
        // Schedule background tasks
        $this->schedule_background_tasks();
    }
    
    /**
     * Initialize enhanced components
     */
    protected function init_enhanced_components() {
        // Initialize queue manager for background processing
        $this->queue_manager = new KiliSmile_Queue_Manager();
        
        // Initialize analytics tracker
        $this->analytics = new KiliSmile_Analytics_Tracker();
        
        // Initialize cache manager
        $this->cache = new KiliSmile_Cache_Manager();
        
        // Load enhanced gateways
        $this->load_enhanced_gateways();
    }
    
    /**
     * Load enhanced payment gateways
     */
    protected function load_enhanced_gateways() {
        $enhanced_gateways = [
            'selcom_enhanced' => 'class-kilismile-selcom-gateway-enhanced.php',
            'paypal_enhanced' => 'class-kilismile-paypal-gateway-enhanced.php',
            'mobile_money_enhanced' => 'class-kilismile-mobile-money-gateway-enhanced.php'
        ];
        
        foreach ($enhanced_gateways as $gateway_id => $file) {
            $file_path = dirname(__FILE__) . '/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Register enhanced hooks
     */
    protected function register_enhanced_hooks() {
        // Enhanced donation processing
        add_action('wp_ajax_kilismile_process_donation_enhanced', [$this, 'ajax_process_donation_enhanced']);
        add_action('wp_ajax_nopriv_kilismile_process_donation_enhanced', [$this, 'ajax_process_donation_enhanced']);
        
        // Background processing
        add_action('kilismile_process_donation_queue', [$this, 'process_donation_queue']);
        
        // Analytics tracking
        add_action('kilismile_donation_completed', [$this, 'track_donation_completion'], 10, 2);
        add_action('kilismile_donation_failed', [$this, 'track_donation_failure'], 10, 2);
        
        // Automated follow-ups
        add_action('kilismile_schedule_donor_followup', [$this, 'schedule_donor_followup'], 10, 2);
        
        // Health monitoring
        add_action('wp_ajax_kilismile_donation_health', [$this, 'ajax_health_status']);
        
        // Cache management
        add_action('kilismile_clear_donation_cache', [$this, 'clear_donation_cache']);
    }
    
    /**
     * Schedule background tasks
     */
    protected function schedule_background_tasks() {
        // Schedule queue processing if not already scheduled
        if (!wp_next_scheduled('kilismile_process_donation_queue')) {
            wp_schedule_event(time(), 'every_minute', 'kilismile_process_donation_queue');
        }
        
        // Schedule daily analytics cleanup
        if (!wp_next_scheduled('kilismile_cleanup_analytics')) {
            wp_schedule_event(time(), 'daily', 'kilismile_cleanup_analytics');
        }
        
        // Schedule weekly donation reports
        if (!wp_next_scheduled('kilismile_weekly_donation_report')) {
            wp_schedule_event(time(), 'weekly', 'kilismile_weekly_donation_report');
        }
    }
    
    /**
     * Enhanced donation processing with queue support
     */
    public function ajax_process_donation_enhanced() {
        try {
            // Verify nonce
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_donation_nonce')) {
                throw new Exception('Security verification failed');
            }
            
            // Rate limiting check
            if (!$this->check_rate_limit()) {
                throw new Exception('Too many requests. Please try again later.');
            }
            
            // Sanitize and validate input
            $donation_data = $this->sanitize_donation_data($_POST);
            $validation_result = $this->validate_donation_data_enhanced($donation_data);
            
            if (!$validation_result['valid']) {
                throw new Exception($validation_result['message']);
            }
            
            // Check for duplicate submissions
            $duplicate_check = $this->check_duplicate_donation($donation_data);
            if ($duplicate_check['is_duplicate']) {
                wp_send_json_success([
                    'message' => 'Donation already processed',
                    'donation_id' => $duplicate_check['donation_id'],
                    'redirect' => $duplicate_check['redirect_url']
                ]);
                return;
            }
            
            // Create donation record
            $donation_id = $this->create_donation_record_enhanced($donation_data);
            
            // Track donation attempt
            $this->analytics->track_event('donation_attempted', [
                'donation_id' => $donation_id,
                'amount' => $donation_data['amount'],
                'currency' => $donation_data['currency'],
                'gateway' => $donation_data['payment_method'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip_address' => $this->get_client_ip()
            ]);
            
            // Process payment based on queue settings
            if ($this->should_use_queue($donation_data)) {
                $this->queue_donation_processing($donation_id, $donation_data);
                
                wp_send_json_success([
                    'message' => 'Donation queued for processing',
                    'donation_id' => $donation_id,
                    'status' => 'queued'
                ]);
            } else {
                $payment_result = $this->process_payment_enhanced($donation_id, $donation_data);
                
                if ($payment_result['success']) {
                    wp_send_json_success([
                        'message' => 'Payment initiated successfully',
                        'donation_id' => $donation_id,
                        'redirect' => $payment_result['redirect'],
                        'transaction_id' => $payment_result['transaction_id']
                    ]);
                } else {
                    throw new Exception($payment_result['message']);
                }
            }
            
        } catch (Exception $e) {
            error_log("Enhanced donation processing error: " . $e->getMessage());
            
            // Track failure
            if (isset($donation_id)) {
                $this->analytics->track_event('donation_failed', [
                    'donation_id' => $donation_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            wp_send_json_error([
                'message' => $e->getMessage(),
                'error_code' => 'PROCESSING_ERROR'
            ]);
        }
    }
    
    /**
     * Enhanced donation data validation
     */
    protected function validate_donation_data_enhanced($data) {
        // Basic validation from parent class
        $basic_validation = $this->validate_donation_data($data);
        if (!$basic_validation['valid']) {
            return $basic_validation;
        }
        
        // Enhanced validations
        
        // Email validation with domain checking
        if (!$this->is_valid_email_domain($data['email'])) {
            return [
                'valid' => false,
                'message' => 'Email domain not allowed'
            ];
        }
        
        // Phone number validation if provided
        if (!empty($data['phone']) && !$this->is_valid_phone($data['phone'])) {
            return [
                'valid' => false,
                'message' => 'Invalid phone number format'
            ];
        }
        
        // Amount validation with fraud detection
        if ($this->is_suspicious_amount($data['amount'], $data['currency'])) {
            return [
                'valid' => false,
                'message' => 'Amount flagged for review'
            ];
        }
        
        // IP-based validation
        if ($this->is_blocked_ip($this->get_client_ip())) {
            return [
                'valid' => false,
                'message' => 'Access denied from this location'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Check if email domain is valid
     */
    protected function is_valid_email_domain($email) {
        $domain = substr(strrchr($email, "@"), 1);
        
        // Check against blocked domains
        $blocked_domains = $this->get_blocked_domains();
        if (in_array($domain, $blocked_domains)) {
            return false;
        }
        
        // Check if domain has MX record
        return checkdnsrr($domain, 'MX');
    }
    
    /**
     * Validate phone number format
     */
    protected function is_valid_phone($phone) {
        // Remove non-numeric characters
        $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Basic validation - adjust patterns as needed
        $patterns = [
            '/^\+255[67][0-9]{8}$/',  // Tanzania mobile
            '/^0[67][0-9]{8}$/',      // Tanzania mobile (local format)
            '/^\+[1-9][0-9]{7,14}$/'  // International format
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $clean_phone)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for suspicious amounts
     */
    protected function is_suspicious_amount($amount, $currency) {
        $limits = $this->get_fraud_detection_limits();
        
        // Check maximum single donation
        if ($amount > $limits['max_single_donation'][$currency]) {
            return true;
        }
        
        // Check for round numbers (potential testing)
        if ($amount >= 1000 && $amount % 1000 == 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if IP is blocked
     */
    protected function is_blocked_ip($ip) {
        $blocked_ips = get_option('kilismile_blocked_ips', []);
        return in_array($ip, $blocked_ips);
    }
    
    /**
     * Get client IP address
     */
    protected function get_client_ip() {
        $ip_headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Check for duplicate donations
     */
    protected function check_duplicate_donation($data) {
        $cache_key = 'donation_' . md5(serialize($data));
        $cached_result = $this->cache->get($cache_key);
        
        if ($cached_result) {
            return [
                'is_duplicate' => true,
                'donation_id' => $cached_result['donation_id'],
                'redirect_url' => $cached_result['redirect_url']
            ];
        }
        
        return ['is_duplicate' => false];
    }
    
    /**
     * Create enhanced donation record with additional metadata
     */
    protected function create_donation_record_enhanced($data) {
        // Enhance data with additional fields
        $enhanced_data = array_merge($data, [
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'source_url' => $_SERVER['HTTP_REFERER'] ?? '',
            'created_via' => 'enhanced_handler',
            'session_id' => session_id() ?: wp_generate_uuid4(),
            'browser_fingerprint' => $this->generate_browser_fingerprint(),
            'metadata' => [
                'form_version' => '2.0.0',
                'processing_time' => 0,
                'validation_score' => $this->calculate_validation_score($data)
            ]
        ]);
        
        $donation_id = KiliSmile_Donation_DB::insert_donation($enhanced_data);
        
        // Cache the donation for duplicate detection
        $cache_key = 'donation_' . md5(serialize($data));
        $this->cache->set($cache_key, [
            'donation_id' => $donation_id,
            'redirect_url' => home_url('/donation-success/?id=' . $donation_id)
        ], 300); // 5 minutes
        
        return $donation_id;
    }
    
    /**
     * Generate browser fingerprint for fraud detection
     */
    protected function generate_browser_fingerprint() {
        $components = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            $_SERVER['HTTP_ACCEPT'] ?? ''
        ];
        
        return hash('sha256', implode('|', $components));
    }
    
    /**
     * Calculate validation score for fraud detection
     */
    protected function calculate_validation_score($data) {
        $score = 100;
        
        // Deduct points for suspicious patterns
        if (empty($data['phone'])) $score -= 10;
        if (empty($data['address'])) $score -= 5;
        if ($this->is_disposable_email($data['email'])) $score -= 20;
        if ($this->is_suspicious_name($data['first_name'], $data['last_name'])) $score -= 15;
        
        return max(0, $score);
    }
    
    /**
     * Check if email is from disposable provider
     */
    protected function is_disposable_email($email) {
        $domain = substr(strrchr($email, "@"), 1);
        $disposable_domains = $this->get_disposable_email_domains();
        return in_array($domain, $disposable_domains);
    }
    
    /**
     * Check for suspicious name patterns
     */
    protected function is_suspicious_name($first_name, $last_name) {
        $suspicious_patterns = [
            '/^test/i',
            '/^temp/i',
            '/^fake/i',
            '/^demo/i',
            '/[0-9]{3,}/',
            '/^(.)\1{2,}$/' // Repeated characters
        ];
        
        $full_name = trim($first_name . ' ' . $last_name);
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $full_name)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if donation should use queue processing
     */
    protected function should_use_queue($data) {
        // Use queue for large amounts or during high traffic
        $queue_threshold = get_option('kilismile_queue_threshold', 100000);
        
        if ($data['amount'] >= $queue_threshold) {
            return true;
        }
        
        // Check current server load
        if ($this->is_high_traffic_period()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Queue donation for background processing
     */
    protected function queue_donation_processing($donation_id, $data) {
        $this->queue_manager->add_job('process_donation', [
            'donation_id' => $donation_id,
            'data' => $data,
            'priority' => $this->get_processing_priority($data),
            'scheduled_for' => time() + 5 // Process in 5 seconds
        ]);
    }
    
    /**
     * Get processing priority based on donation data
     */
    protected function get_processing_priority($data) {
        // Higher priority for larger amounts
        if ($data['amount'] >= 1000000) return 'high';
        if ($data['amount'] >= 100000) return 'medium';
        return 'normal';
    }
    
    /**
     * Enhanced payment processing with advanced error handling
     */
    protected function process_payment_enhanced($donation_id, $data) {
        $start_time = microtime(true);
        
        try {
            // Get enhanced gateway
            $gateway = $this->get_enhanced_gateway($data['payment_method']);
            if (!$gateway) {
                throw new Exception('Payment gateway not available');
            }
            
            // Pre-process hooks
            do_action('kilismile_before_payment_processing', $donation_id, $data);
            
            // Process payment
            $result = $gateway->process_payment($donation_id, $data);
            
            // Post-process hooks
            do_action('kilismile_after_payment_processing', $donation_id, $data, $result);
            
            // Update processing time
            $processing_time = microtime(true) - $start_time;
            $this->update_donation_metadata($donation_id, [
                'processing_time' => $processing_time
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            // Enhanced error handling
            $this->handle_payment_error($donation_id, $e, [
                'gateway' => $data['payment_method'],
                'amount' => $data['amount'],
                'processing_time' => microtime(true) - $start_time
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get enhanced payment gateway
     */
    protected function get_enhanced_gateway($gateway_id) {
        $enhanced_gateways = [
            'selcom' => 'KiliSmile_Selcom_Gateway_Enhanced',
            'paypal' => 'KiliSmile_PayPal_Gateway_Enhanced',
            'mobile_money' => 'KiliSmile_Mobile_Money_Gateway_Enhanced'
        ];
        
        $class_name = $enhanced_gateways[$gateway_id] ?? null;
        
        if ($class_name && class_exists($class_name)) {
            return new $class_name();
        }
        
        // Fall back to standard gateway
        return parent::get_payment_gateway($gateway_id);
    }
    
    /**
     * Handle payment errors with enhanced logging
     */
    protected function handle_payment_error($donation_id, $exception, $context = []) {
        $error_data = [
            'donation_id' => $donation_id,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'stack_trace' => $exception->getTraceAsString(),
            'context' => $context,
            'timestamp' => current_time('mysql'),
            'ip_address' => $this->get_client_ip()
        ];
        
        // Log to database
        KiliSmile_Donation_DB::log_error($error_data);
        
        // Update donation status
        KiliSmile_Donation_DB::update_donation($donation_id, [
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
        
        // Fire action for additional error handling
        do_action('kilismile_payment_error', $error_data);
        
        // Send admin notification for critical errors
        if ($this->is_critical_error($exception)) {
            $this->send_admin_error_notification($error_data);
        }
    }
    
    /**
     * Check if error is critical
     */
    protected function is_critical_error($exception) {
        $critical_patterns = [
            'database',
            'connection',
            'timeout',
            'server error'
        ];
        
        $message = strtolower($exception->getMessage());
        
        foreach ($critical_patterns as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Process donation queue
     */
    public function process_donation_queue() {
        $jobs = $this->queue_manager->get_pending_jobs('process_donation', 5);
        
        foreach ($jobs as $job) {
            try {
                $this->queue_manager->mark_job_processing($job->id);
                
                $result = $this->process_payment_enhanced(
                    $job->data['donation_id'],
                    $job->data['data']
                );
                
                if ($result['success']) {
                    $this->queue_manager->mark_job_completed($job->id);
                } else {
                    $this->queue_manager->mark_job_failed($job->id, $result['message']);
                }
                
            } catch (Exception $e) {
                $this->queue_manager->mark_job_failed($job->id, $e->getMessage());
            }
        }
    }
    
    /**
     * Track donation completion
     */
    public function track_donation_completion($donation_id, $transaction_data) {
        $this->analytics->track_event('donation_completed', [
            'donation_id' => $donation_id,
            'amount' => $transaction_data['amount'],
            'currency' => $transaction_data['currency'],
            'gateway' => $transaction_data['payment_method'],
            'completion_time' => current_time('mysql')
        ]);
        
        // Schedule follow-up actions
        $this->schedule_donor_followup($donation_id, 'completion');
    }
    
    /**
     * Track donation failure
     */
    public function track_donation_failure($donation_id, $error_data) {
        $this->analytics->track_event('donation_failed', [
            'donation_id' => $donation_id,
            'error' => $error_data['error_message'],
            'gateway' => $error_data['context']['gateway'] ?? 'unknown',
            'failure_time' => current_time('mysql')
        ]);
    }
    
    /**
     * Schedule donor follow-up actions
     */
    public function schedule_donor_followup($donation_id, $trigger) {
        $followup_actions = [
            'completion' => [
                'thank_you_email' => '+1 hour',
                'receipt_email' => '+24 hours',
                'impact_report' => '+7 days'
            ],
            'abandonment' => [
                'reminder_email' => '+1 hour',
                'alternative_payment' => '+24 hours'
            ]
        ];
        
        if (!isset($followup_actions[$trigger])) {
            return;
        }
        
        foreach ($followup_actions[$trigger] as $action => $delay) {
            wp_schedule_single_event(
                strtotime($delay),
                'kilismile_donor_followup',
                [$donation_id, $action]
            );
        }
    }
    
    /**
     * AJAX health status endpoint
     */
    public function ajax_health_status() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $health_data = [
            'donation_handler' => [
                'status' => 'healthy',
                'queue_size' => $this->queue_manager->get_queue_size(),
                'processing_rate' => $this->analytics->get_processing_rate(),
                'error_rate' => $this->analytics->get_error_rate_24h()
            ],
            'gateways' => $this->get_gateway_health_status(),
            'database' => $this->check_database_health(),
            'cache' => $this->cache->get_health_status()
        ];
        
        wp_send_json($health_data);
    }
    
    /**
     * Get gateway health status
     */
    protected function get_gateway_health_status() {
        $gateways = ['selcom', 'paypal', 'mobile_money'];
        $status = [];
        
        foreach ($gateways as $gateway_id) {
            $gateway = $this->get_enhanced_gateway($gateway_id);
            if ($gateway && method_exists($gateway, 'health_check')) {
                $status[$gateway_id] = $gateway->health_check();
            }
        }
        
        return $status;
    }
    
    /**
     * Check database health
     */
    protected function check_database_health() {
        try {
            global $wpdb;
            
            // Test basic connectivity
            $result = $wpdb->get_var("SELECT 1");
            
            if ($result != 1) {
                throw new Exception('Database connectivity test failed');
            }
            
            // Check table health
            $tables_status = $this->check_donation_tables();
            
            return [
                'status' => 'healthy',
                'connectivity' => 'ok',
                'tables' => $tables_status
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check donation tables status
     */
    protected function check_donation_tables() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'kilismile_donations',
            $wpdb->prefix . 'kilismile_transactions',
            $wpdb->prefix . 'kilismile_donation_meta'
        ];
        
        $status = [];
        
        foreach ($tables as $table) {
            try {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
                $status[str_replace($wpdb->prefix, '', $table)] = [
                    'exists' => true,
                    'record_count' => (int)$count
                ];
            } catch (Exception $e) {
                $status[str_replace($wpdb->prefix, '', $table)] = [
                    'exists' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $status;
    }
    
    /**
     * Update donation metadata
     */
    protected function update_donation_metadata($donation_id, $metadata) {
        $existing_meta = get_post_meta($donation_id, 'kilismile_donation_meta', true) ?: [];
        $updated_meta = array_merge($existing_meta, $metadata);
        update_post_meta($donation_id, 'kilismile_donation_meta', $updated_meta);
    }
    
    /**
     * Rate limiting check
     */
    protected function check_rate_limit() {
        $ip = $this->get_client_ip();
        $rate_limit_key = 'rate_limit_' . md5($ip);
        $current_count = $this->cache->get($rate_limit_key, 0);
        
        $max_requests = 10; // Max 10 requests per minute
        
        if ($current_count >= $max_requests) {
            return false;
        }
        
        $this->cache->set($rate_limit_key, $current_count + 1, 60);
        return true;
    }
    
    /**
     * Check if it's a high traffic period
     */
    protected function is_high_traffic_period() {
        $concurrent_users = $this->analytics->get_concurrent_users();
        return $concurrent_users > 50; // Threshold for high traffic
    }
    
    /**
     * Get helper data methods
     */
    protected function get_blocked_domains() {
        return get_option('kilismile_blocked_domains', [
            'tempmail.com',
            '10minutemail.com',
            'guerrillamail.com'
        ]);
    }
    
    protected function get_disposable_email_domains() {
        return get_option('kilismile_disposable_domains', [
            '10minutemail.com',
            'tempmail.com',
            'throwaway.email'
        ]);
    }
    
    protected function get_fraud_detection_limits() {
        return [
            'max_single_donation' => [
                'TZS' => 10000000, // 10M TZS
                'USD' => 5000      // $5000 USD
            ]
        ];
    }
    
    /**
     * Send admin error notification
     */
    protected function send_admin_error_notification($error_data) {
        $admin_email = get_option('admin_email');
        $subject = 'Critical Donation System Error - ' . get_bloginfo('name');
        
        $message = "A critical error occurred in the donation system:\n\n";
        $message .= "Error: " . $error_data['error_message'] . "\n";
        $message .= "Donation ID: " . $error_data['donation_id'] . "\n";
        $message .= "Time: " . $error_data['timestamp'] . "\n";
        $message .= "IP: " . $error_data['ip_address'] . "\n\n";
        $message .= "Please check the system immediately.";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Clear donation cache
     */
    public function clear_donation_cache() {
        $this->cache->flush_group('donations');
    }
}

/**
 * Queue Manager Class
 */
class KiliSmile_Queue_Manager {
    
    protected $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'kilismile_queue';
        
        // Create table if not exists
        $this->create_queue_table();
    }
    
    protected function create_queue_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_type varchar(50) NOT NULL,
            data longtext NOT NULL,
            priority varchar(20) DEFAULT 'normal',
            status varchar(20) DEFAULT 'pending',
            scheduled_for datetime NOT NULL,
            attempts int(11) DEFAULT 0,
            max_attempts int(11) DEFAULT 3,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY job_type (job_type),
            KEY scheduled_for (scheduled_for)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function add_job($job_type, $data) {
        global $wpdb;
        
        return $wpdb->insert(
            $this->table_name,
            [
                'job_type' => $job_type,
                'data' => json_encode($data),
                'priority' => $data['priority'] ?? 'normal',
                'scheduled_for' => date('Y-m-d H:i:s', $data['scheduled_for'] ?? time()),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]
        );
    }
    
    public function get_pending_jobs($job_type, $limit = 10) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
             WHERE job_type = %s 
             AND status = 'pending' 
             AND scheduled_for <= NOW() 
             ORDER BY priority = 'high' DESC, priority = 'medium' DESC, created_at ASC 
             LIMIT %d",
            $job_type,
            $limit
        ));
    }
    
    public function mark_job_processing($job_id) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            [
                'status' => 'processing',
                'updated_at' => current_time('mysql')
            ],
            ['id' => $job_id]
        );
    }
    
    public function mark_job_completed($job_id) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            [
                'status' => 'completed',
                'updated_at' => current_time('mysql')
            ],
            ['id' => $job_id]
        );
    }
    
    public function mark_job_failed($job_id, $error_message = '') {
        global $wpdb;
        
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $job_id
        ));
        
        if (!$job) return false;
        
        $attempts = $job->attempts + 1;
        $status = ($attempts >= $job->max_attempts) ? 'failed' : 'pending';
        
        return $wpdb->update(
            $this->table_name,
            [
                'status' => $status,
                'attempts' => $attempts,
                'updated_at' => current_time('mysql')
            ],
            ['id' => $job_id]
        );
    }
    
    public function get_queue_size($job_type = null) {
        global $wpdb;
        
        $where = "status = 'pending'";
        $params = [];
        
        if ($job_type) {
            $where .= " AND job_type = %s";
            $params[] = $job_type;
        }
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}",
            $params
        ));
    }
}

/**
 * Analytics Tracker Class
 */
class KiliSmile_Analytics_Tracker {
    
    protected $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'kilismile_analytics';
        
        $this->create_analytics_table();
    }
    
    protected function create_analytics_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            session_id varchar(100),
            user_id bigint(20),
            ip_address varchar(45),
            user_agent text,
            timestamp datetime NOT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY timestamp (timestamp),
            KEY session_id (session_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function track_event($event_type, $data = []) {
        global $wpdb;
        
        return $wpdb->insert(
            $this->table_name,
            [
                'event_type' => $event_type,
                'event_data' => json_encode($data),
                'session_id' => session_id() ?: wp_generate_uuid4(),
                'user_id' => get_current_user_id(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'timestamp' => current_time('mysql')
            ]
        );
    }
    
    public function get_processing_rate() {
        global $wpdb;
        
        $completed = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} 
             WHERE event_type = 'donation_completed' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        ));
        
        $attempted = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} 
             WHERE event_type = 'donation_attempted' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        ));
        
        return $attempted > 0 ? ($completed / $attempted) * 100 : 0;
    }
    
    public function get_error_rate_24h() {
        global $wpdb;
        
        $failed = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} 
             WHERE event_type = 'donation_failed' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        ));
        
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} 
             WHERE event_type IN ('donation_completed', 'donation_failed') 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        ));
        
        return $total > 0 ? ($failed / $total) * 100 : 0;
    }
    
    public function get_concurrent_users() {
        global $wpdb;
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM {$this->table_name} 
             WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)"
        ));
    }
}

/**
 * Cache Manager Class
 */
class KiliSmile_Cache_Manager {
    
    protected $prefix = 'kilismile_';
    
    public function get($key, $default = null) {
        $cached = wp_cache_get($this->prefix . $key, 'kilismile');
        return $cached !== false ? $cached : $default;
    }
    
    public function set($key, $value, $expiration = 3600) {
        return wp_cache_set($this->prefix . $key, $value, 'kilismile', $expiration);
    }
    
    public function delete($key) {
        return wp_cache_delete($this->prefix . $key, 'kilismile');
    }
    
    public function flush_group($group = 'kilismile') {
        return wp_cache_flush_group($group);
    }
    
    public function get_health_status() {
        $test_key = 'cache_test_' . time();
        $test_value = 'test_data';
        
        // Test write
        $write_success = $this->set($test_key, $test_value, 60);
        
        // Test read
        $read_value = $this->get($test_key);
        $read_success = ($read_value === $test_value);
        
        // Cleanup
        $this->delete($test_key);
        
        return [
            'status' => ($write_success && $read_success) ? 'healthy' : 'unhealthy',
            'write_test' => $write_success ? 'pass' : 'fail',
            'read_test' => $read_success ? 'pass' : 'fail'
        ];
    }
}

// Initialize enhanced donation handler
add_action('init', function() {
    if (class_exists('KiliSmile_Donation_Handler')) {
        new KiliSmile_Donation_Handler_Enhanced();
    }
});


