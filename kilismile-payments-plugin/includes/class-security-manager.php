<?php
/**
 * KiliSmile Payments - Advanced Security System
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Security_Manager {
    
    private $rate_limit_table = 'kilismile_rate_limits';
    private $security_log_table = 'kilismile_security_logs';
    private $fraud_detection_enabled;
    private $rate_limiting_enabled;
    
    public function __construct() {
        $this->fraud_detection_enabled = get_option('kilismile_payments_fraud_detection', 1);
        $this->rate_limiting_enabled = get_option('kilismile_payments_rate_limiting', 1);
        
        // Initialize security hooks
        add_action('init', array($this, 'init_security'));
        add_action('wp_ajax_kilismile_process_donation', array($this, 'check_security_before_processing'), 1);
        add_action('wp_ajax_nopriv_kilismile_process_donation', array($this, 'check_security_before_processing'), 1);
        
        // Webhook security
        add_action('init', array($this, 'handle_webhooks'));
        
        // Database setup
        register_activation_hook(KILISMILE_PAYMENTS_PLUGIN_FILE, array($this, 'create_security_tables'));
        
        // Cleanup tasks
        add_action('kilismile_security_cleanup', array($this, 'cleanup_security_data'));
        if (!wp_next_scheduled('kilismile_security_cleanup')) {
            wp_schedule_event(time(), 'daily', 'kilismile_security_cleanup');
        }
    }
    
    /**
     * Initialize security system
     */
    public function init_security() {
        // Initialize security headers
        $this->set_security_headers();
        
        // Initialize CSRF protection
        $this->init_csrf_protection();
    }
    
    /**
     * Set security headers
     */
    private function set_security_headers() {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
    
    /**
     * Initialize CSRF protection
     */
    private function init_csrf_protection() {
        // Generate CSRF token for forms
        if (!session_id()) {
            session_start();
        }
        
        if (!isset($_SESSION['kilismile_csrf_token'])) {
            $_SESSION['kilismile_csrf_token'] = wp_generate_password(32, false);
        }
    }
    
    /**
     * Check security before processing donation
     */
    public function check_security_before_processing() {
        $ip_address = $this->get_client_ip();
        
        // Check rate limiting
        if ($this->rate_limiting_enabled && !$this->check_rate_limit($ip_address)) {
            $this->log_security_event('rate_limit_exceeded', $ip_address);
            wp_send_json_error(__('Too many requests. Please try again later.', 'kilismile-payments'));
            return;
        }
        
        // Check for suspicious activity
        if ($this->fraud_detection_enabled && $this->detect_suspicious_activity($_POST, $ip_address)) {
            $this->log_security_event('suspicious_activity', $ip_address, $_POST);
            wp_send_json_error(__('Security check failed. Please contact support.', 'kilismile-payments'));
            return;
        }
        
        // Validate CSRF token
        if (!$this->validate_csrf_token()) {
            $this->log_security_event('csrf_validation_failed', $ip_address);
            wp_send_json_error(__('Security validation failed. Please refresh the page.', 'kilismile-payments'));
            return;
        }
        
        // Update rate limiting counter
        if ($this->rate_limiting_enabled) {
            $this->update_rate_limit($ip_address);
        }
    }
    
    /**
     * Check rate limiting
     */
    private function check_rate_limit($ip_address) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . $this->rate_limit_table;
        $max_attempts = get_option('kilismile_payments_rate_limit_attempts', 5);
        $time_window = get_option('kilismile_payments_rate_limit_window', 60); // minutes
        
        $since = date('Y-m-d H:i:s', strtotime("-{$time_window} minutes"));
        
        $attempts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE ip_address = %s AND attempt_time >= %s",
            $ip_address,
            $since
        ));
        
        return $attempts < $max_attempts;
    }
    
    /**
     * Update rate limiting counter
     */
    private function update_rate_limit($ip_address) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . $this->rate_limit_table;
        
        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $ip_address,
                'attempt_time' => current_time('mysql'),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
            ),
            array('%s', '%s', '%s')
        );
    }
    
    /**
     * Detect suspicious activity
     */
    private function detect_suspicious_activity($data, $ip_address) {
        $suspicious_indicators = 0;
        $max_indicators = 3; // Threshold for blocking
        
        // Check for bot-like behavior
        if ($this->is_bot_request()) {
            $suspicious_indicators++;
        }
        
        // Check for unusual form submission speed
        if ($this->is_form_submitted_too_quickly($data)) {
            $suspicious_indicators++;
        }
        
        // Check for suspicious data patterns
        if ($this->has_suspicious_data_patterns($data)) {
            $suspicious_indicators++;
        }
        
        // Check for blacklisted IP
        if ($this->is_blacklisted_ip($ip_address)) {
            $suspicious_indicators += 5; // Instant block
        }
        
        // Check for multiple failed attempts from same IP
        if ($this->has_multiple_recent_failures($ip_address)) {
            $suspicious_indicators++;
        }
        
        // Check for unusual amount patterns
        if (isset($data['amount']) && $this->is_suspicious_amount($data['amount'])) {
            $suspicious_indicators++;
        }
        
        // Check for VPN/Proxy usage (basic check)
        if ($this->is_likely_proxy($ip_address)) {
            $suspicious_indicators++;
        }
        
        return $suspicious_indicators >= $max_indicators;
    }
    
    /**
     * Check if request is from a bot
     */
    private function is_bot_request() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $bot_patterns = array(
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/curl/i',
            '/wget/i',
            '/python/i'
        );
        
        foreach ($bot_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return true;
            }
        }
        
        // Check for missing user agent
        if (empty($user_agent)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if form was submitted too quickly
     */
    private function is_form_submitted_too_quickly($data) {
        // Check if honeypot field is filled (indicates bot)
        if (isset($data['honeypot']) && !empty($data['honeypot'])) {
            return true;
        }
        
        // Check form load time (if timestamp is provided)
        if (isset($data['form_load_time'])) {
            $load_time = intval($data['form_load_time']);
            $current_time = time();
            $time_diff = $current_time - $load_time;
            
            // If form was submitted in less than 5 seconds, it's suspicious
            if ($time_diff < 5) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for suspicious data patterns
     */
    private function has_suspicious_data_patterns($data) {
        $suspicious_patterns = array(
            '/script/i',
            '/<[^>]*>/',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/eval\s*\(/i',
            '/document\./i',
            '/window\./i',
            '/%[0-9a-f]{2}/i' // URL encoded
        );
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($suspicious_patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if IP is blacklisted
     */
    private function is_blacklisted_ip($ip_address) {
        $blacklisted_ips = get_option('kilismile_payments_blacklisted_ips', array());
        
        if (in_array($ip_address, $blacklisted_ips)) {
            return true;
        }
        
        // Check for IP ranges (CIDR notation)
        foreach ($blacklisted_ips as $blocked_ip) {
            if (strpos($blocked_ip, '/') !== false) {
                if ($this->ip_in_range($ip_address, $blocked_ip)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if IP has multiple recent failures
     */
    private function has_multiple_recent_failures($ip_address) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . $this->security_log_table;
        $since = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $failures = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} 
             WHERE ip_address = %s 
             AND event_type IN ('payment_failed', 'validation_failed', 'suspicious_activity') 
             AND event_time >= %s",
            $ip_address,
            $since
        ));
        
        return $failures >= 3;
    }
    
    /**
     * Check for suspicious amount patterns
     */
    private function is_suspicious_amount($amount) {
        $amount = floatval($amount);
        
        // Very large amounts
        if ($amount > 5000) {
            return true;
        }
        
        // Unusual precise amounts (e.g., 1234.56)
        $amount_str = strval($amount);
        if (preg_match('/1234|5678|9876|0123/', $amount_str)) {
            return true;
        }
        
        // Very small amounts that might be testing
        if ($amount < 0.01) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Basic proxy/VPN detection
     */
    private function is_likely_proxy($ip_address) {
        // Check for common proxy headers
        $proxy_headers = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        );
        
        foreach ($proxy_headers as $header) {
            if (!empty($_SERVER[$header])) {
                return true;
            }
        }
        
        // Check against known proxy IP ranges (simplified)
        $proxy_ranges = array(
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16'
        );
        
        foreach ($proxy_ranges as $range) {
            if ($this->ip_in_range($ip_address, $range)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate CSRF token
     */
    private function validate_csrf_token() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['kilismile_csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['kilismile_csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * Handle webhooks with security validation
     */
    public function handle_webhooks() {
        if (!isset($_GET['kilismile_webhook'])) {
            return;
        }
        
        $gateway = sanitize_text_field($_GET['kilismile_webhook']);
        
        // Validate webhook signature
        if (!$this->validate_webhook_signature($gateway)) {
            $this->log_security_event('invalid_webhook_signature', $this->get_client_ip(), array('gateway' => $gateway));
            http_response_code(401);
            exit('Unauthorized');
        }
        
        // Rate limit webhooks
        if (!$this->check_webhook_rate_limit($gateway)) {
            $this->log_security_event('webhook_rate_limit_exceeded', $this->get_client_ip(), array('gateway' => $gateway));
            http_response_code(429);
            exit('Too Many Requests');
        }
        
        // Process webhook
        do_action('kilismile_process_webhook', $gateway, $_POST);
    }
    
    /**
     * Validate webhook signature
     */
    private function validate_webhook_signature($gateway) {
        $webhook_secret = get_option('kilismile_payments_webhook_secret', '');
        
        if (empty($webhook_secret)) {
            return false;
        }
        
        $signature_header = '';
        
        // Get signature based on gateway
        switch ($gateway) {
            case 'selcom':
                $signature_header = $_SERVER['HTTP_X_SELCOM_SIGNATURE'] ?? '';
                break;
            case 'stripe':
                $signature_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
                break;
            default:
                $signature_header = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
        }
        
        if (empty($signature_header)) {
            return false;
        }
        
        $payload = file_get_contents('php://input');
        $expected_signature = hash_hmac('sha256', $payload, $webhook_secret);
        
        return hash_equals($expected_signature, $signature_header);
    }
    
    /**
     * Check webhook rate limiting
     */
    private function check_webhook_rate_limit($gateway) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . $this->rate_limit_table;
        $ip_address = $this->get_client_ip();
        $since = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        
        $attempts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} 
             WHERE ip_address = %s 
             AND attempt_time >= %s 
             AND request_type = 'webhook'",
            $ip_address,
            $since
        ));
        
        // Allow up to 100 webhook requests per 5 minutes
        return $attempts < 100;
    }
    
    /**
     * Log security events
     */
    private function log_security_event($event_type, $ip_address, $data = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . $this->security_log_table;
        
        $wpdb->insert(
            $table_name,
            array(
                'event_type' => $event_type,
                'ip_address' => $ip_address,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'event_data' => json_encode($data),
                'event_time' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
        
        // Send alert for critical events
        if (in_array($event_type, array('suspicious_activity', 'multiple_failures', 'invalid_webhook_signature'))) {
            $this->send_security_alert($event_type, $ip_address, $data);
        }
    }
    
    /**
     * Send security alert
     */
    private function send_security_alert($event_type, $ip_address, $data) {
        $admin_email = get_option('kilismile_payments_admin_email', get_option('admin_email'));
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(__('[%s] Security Alert: %s', 'kilismile-payments'), $site_name, $event_type);
        
        $message = sprintf(
            __("A security event has been detected on your website:\n\nEvent Type: %s\nIP Address: %s\nTime: %s\nUser Agent: %s\n\nPlease review your security logs for more details.", 'kilismile-payments'),
            $event_type,
            $ip_address,
            current_time('mysql'),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_headers = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Handle comma-separated IPs (load balancers)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Check if IP is in range (CIDR notation)
     */
    private function ip_in_range($ip, $range) {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }
        
        list($subnet, $bits) = explode('/', $range);
        
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || 
            !filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        
        $ip_long = ip2long($ip);
        $subnet_long = ip2long($subnet);
        $mask = -1 << (32 - (int)$bits);
        
        return ($ip_long & $mask) === ($subnet_long & $mask);
    }
    
    /**
     * Create security tables
     */
    public function create_security_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Rate limiting table
        $rate_limit_table = $wpdb->prefix . $this->rate_limit_table;
        $rate_limit_sql = "CREATE TABLE $rate_limit_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            attempt_time datetime DEFAULT CURRENT_TIMESTAMP,
            user_agent varchar(255) DEFAULT '',
            request_type varchar(50) DEFAULT 'payment',
            PRIMARY KEY (id),
            KEY ip_address (ip_address),
            KEY attempt_time (attempt_time)
        ) $charset_collate;";
        
        // Security log table
        $security_log_table = $wpdb->prefix . $this->security_log_table;
        $security_log_sql = "CREATE TABLE $security_log_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent varchar(255) DEFAULT '',
            event_data longtext,
            event_time datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY ip_address (ip_address),
            KEY event_time (event_time)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($rate_limit_sql);
        dbDelta($security_log_sql);
    }
    
    /**
     * Cleanup old security data
     */
    public function cleanup_security_data() {
        global $wpdb;
        
        $retention_days = get_option('kilismile_payments_security_retention', 30);
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));
        
        // Cleanup rate limiting data
        $rate_limit_table = $wpdb->prefix . $this->rate_limit_table;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$rate_limit_table} WHERE attempt_time < %s",
            $cutoff_date
        ));
        
        // Cleanup security logs (keep only critical events longer)
        $security_log_table = $wpdb->prefix . $this->security_log_table;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$security_log_table} 
             WHERE event_time < %s 
             AND event_type NOT IN ('suspicious_activity', 'invalid_webhook_signature', 'multiple_failures')",
            $cutoff_date
        ));
        
        // Keep critical events for 90 days
        $critical_cutoff = date('Y-m-d H:i:s', strtotime('-90 days'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$security_log_table} WHERE event_time < %s",
            $critical_cutoff
        ));
    }
    
    /**
     * Get security statistics
     */
    public function get_security_stats() {
        global $wpdb;
        
        $security_log_table = $wpdb->prefix . $this->security_log_table;
        $rate_limit_table = $wpdb->prefix . $this->rate_limit_table;
        
        $stats = array();
        
        // Recent security events (last 24 hours)
        $since = date('Y-m-d H:i:s', strtotime('-24 hours'));
        
        $stats['recent_events'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$security_log_table} WHERE event_time >= %s",
            $since
        ));
        
        $stats['blocked_attempts'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$security_log_table} 
             WHERE event_time >= %s 
             AND event_type IN ('rate_limit_exceeded', 'suspicious_activity')",
            $since
        ));
        
        $stats['unique_ips'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT ip_address) FROM {$rate_limit_table} WHERE attempt_time >= %s",
            $since
        ));
        
        $stats['total_requests'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$rate_limit_table} WHERE attempt_time >= %s",
            $since
        ));
        
        return $stats;
    }
    
    /**
     * Add IP to blacklist
     */
    public function blacklist_ip($ip_address, $reason = '') {
        $blacklisted_ips = get_option('kilismile_payments_blacklisted_ips', array());
        
        if (!in_array($ip_address, $blacklisted_ips)) {
            $blacklisted_ips[] = $ip_address;
            update_option('kilismile_payments_blacklisted_ips', $blacklisted_ips);
            
            $this->log_security_event('ip_blacklisted', $ip_address, array('reason' => $reason));
        }
    }
    
    /**
     * Remove IP from blacklist
     */
    public function whitelist_ip($ip_address) {
        $blacklisted_ips = get_option('kilismile_payments_blacklisted_ips', array());
        $key = array_search($ip_address, $blacklisted_ips);
        
        if ($key !== false) {
            unset($blacklisted_ips[$key]);
            update_option('kilismile_payments_blacklisted_ips', array_values($blacklisted_ips));
            
            $this->log_security_event('ip_whitelisted', $ip_address);
        }
    }
    
    /**
     * Generate CSRF token for forms
     */
    public function get_csrf_token() {
        if (!session_id()) {
            session_start();
        }
        
        if (!isset($_SESSION['kilismile_csrf_token'])) {
            $_SESSION['kilismile_csrf_token'] = wp_generate_password(32, false);
        }
        
        return $_SESSION['kilismile_csrf_token'];
    }
    
    /**
     * Generate honeypot field
     */
    public function get_honeypot_field() {
        return '<input type="text" name="honeypot" style="position:absolute;left:-5000px;" tabindex="-1" autocomplete="off">';
    }
}

// Initialize security manager
new KiliSmile_Security_Manager();

