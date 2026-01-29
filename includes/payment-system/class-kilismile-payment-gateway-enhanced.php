<?php
/**
 * Enhanced Payment Gateway Base Class
 * 
 * Provides advanced features like retry logic, circuit breaker,
 * and enhanced error handling for payment gateways.
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Payment Gateway Abstract Class
 */
abstract class KiliSmile_Payment_Gateway_Enhanced extends KiliSmile_Payment_Gateway {
    
    /**
     * Retry configuration
     */
    protected $retry_attempts = 3;
    protected $retry_delay_base = 2; // Base delay in seconds for exponential backoff
    
    /**
     * Circuit breaker configuration
     */
    protected $circuit_breaker_threshold = 5; // Failures before opening circuit
    protected $circuit_breaker_timeout = 300; // 5 minutes before retry
    protected $health_check_interval = 60; // 1 minute health check
    
    /**
     * Rate limiting configuration
     */
    protected $rate_limit_requests = 100; // Requests per hour
    protected $rate_limit_window = 3600; // 1 hour in seconds
    
    /**
     * Enhanced payment processing with retry logic and circuit breaker
     * 
     * @param string $donation_id Donation ID
     * @param array $payment_data Payment data
     * @return array Response with success/error status and data
     */
    public function process_payment_with_retry($donation_id, $payment_data) {
        // Check circuit breaker status
        if ($this->is_circuit_breaker_open()) {
            return [
                'success' => false,
                'message' => 'Payment gateway temporarily unavailable. Please try again later.',
                'error_code' => 'GATEWAY_UNAVAILABLE'
            ];
        }
        
        // Check rate limiting
        if (!$this->check_rate_limit()) {
            return [
                'success' => false,
                'message' => 'Too many payment attempts. Please try again later.',
                'error_code' => 'RATE_LIMITED'
            ];
        }
        
        $attempts = 0;
        $last_error = null;
        $start_time = microtime(true);
        
        while ($attempts < $this->retry_attempts) {
            try {
                $attempt_number = $attempts + 1;
                $this->log("Payment attempt #{$attempt_number} for donation: {$donation_id}");
                
                $result = $this->process_payment($donation_id, $payment_data);
                
                if ($result['success']) {
                    $duration = microtime(true) - $start_time;
                    $this->log("Payment successful after {$attempt_number} attempts in {$duration}s");
                    $this->record_success();
                    return $result;
                }
                
                $last_error = $result['message'] ?? 'Payment failed';
                $this->log("Payment attempt failed: {$last_error}", 'warning');
                
            } catch (Exception $e) {
                $last_error = $e->getMessage();
                $this->log("Payment exception: {$last_error}", 'error');
            }
            
            $attempts++;
            $this->record_failure();
            
            // Apply exponential backoff if not the last attempt
            if ($attempts < $this->retry_attempts) {
                $delay = pow($this->retry_delay_base, $attempts);
                $this->log("Waiting {$delay}s before retry...");
                sleep($delay);
            }
        }
        
        $total_duration = microtime(true) - $start_time;
        $this->log("Payment failed after {$this->retry_attempts} attempts in {$total_duration}s", 'error');
        
        return [
            'success' => false,
            'message' => "Payment failed after {$this->retry_attempts} attempts: {$last_error}",
            'error_code' => 'PAYMENT_FAILED',
            'attempts' => $attempts
        ];
    }
    
    /**
     * Check if circuit breaker is open
     * 
     * @return bool True if circuit is open (gateway unavailable)
     */
    protected function is_circuit_breaker_open() {
        $option_key = "kilismile_cb_{$this->id}";
        $circuit_data = get_option($option_key, [
            'failures' => 0,
            'last_failure' => 0,
            'state' => 'closed' // closed, open, half-open
        ]);
        
        $now = time();
        
        // If circuit is open, check if timeout has passed
        if ($circuit_data['state'] === 'open') {
            if (($now - $circuit_data['last_failure']) >= $this->circuit_breaker_timeout) {
                // Move to half-open state
                $circuit_data['state'] = 'half-open';
                update_option($option_key, $circuit_data);
                $this->log("Circuit breaker moved to half-open state");
                return false;
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Record successful operation
     */
    protected function record_success() {
        $option_key = "kilismile_cb_{$this->id}";
        $circuit_data = get_option($option_key, [
            'failures' => 0,
            'last_failure' => 0,
            'state' => 'closed'
        ]);
        
        // Reset circuit breaker on success
        $circuit_data['failures'] = 0;
        $circuit_data['state'] = 'closed';
        update_option($option_key, $circuit_data);
        
        // Record success metrics
        $this->record_metric('payment_success', 1);
    }
    
    /**
     * Record failed operation
     */
    protected function record_failure() {
        $option_key = "kilismile_cb_{$this->id}";
        $circuit_data = get_option($option_key, [
            'failures' => 0,
            'last_failure' => 0,
            'state' => 'closed'
        ]);
        
        $circuit_data['failures']++;
        $circuit_data['last_failure'] = time();
        
        // Open circuit if threshold is reached
        if ($circuit_data['failures'] >= $this->circuit_breaker_threshold) {
            $circuit_data['state'] = 'open';
            $this->log("Circuit breaker opened due to {$circuit_data['failures']} failures", 'error');
        }
        
        update_option($option_key, $circuit_data);
        
        // Record failure metrics
        $this->record_metric('payment_failure', 1);
    }
    
    /**
     * Check rate limiting
     * 
     * @return bool True if request is allowed
     */
    protected function check_rate_limit() {
        $ip = $this->get_client_ip();
        $option_key = "kilismile_rl_{$this->id}_{$ip}";
        
        $rate_data = get_option($option_key, [
            'requests' => 0,
            'window_start' => time()
        ]);
        
        $now = time();
        
        // Reset window if expired
        if (($now - $rate_data['window_start']) >= $this->rate_limit_window) {
            $rate_data = [
                'requests' => 0,
                'window_start' => $now
            ];
        }
        
        // Check if limit exceeded
        if ($rate_data['requests'] >= $this->rate_limit_requests) {
            $this->log("Rate limit exceeded for IP: {$ip}", 'warning');
            return false;
        }
        
        // Increment counter
        $rate_data['requests']++;
        update_option($option_key, $rate_data);
        
        return true;
    }
    
    /**
     * Get client IP address
     * 
     * @return string Client IP address
     */
    protected function get_client_ip() {
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Enhanced webhook validation with signature verification
     * 
     * @param array $data Webhook data
     * @param string $signature Webhook signature
     * @return bool True if webhook is valid
     */
    protected function validate_webhook_signature($data, $signature) {
        if (empty($signature)) {
            $this->log('Webhook signature missing', 'error');
            return false;
        }
        
        $payload = is_array($data) ? json_encode($data) : $data;
        $expected_signature = $this->generate_webhook_signature($payload);
        
        if (!hash_equals($expected_signature, $signature)) {
            $this->log('Webhook signature mismatch', 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate webhook signature
     * 
     * @param string $payload Webhook payload
     * @return string Generated signature
     */
    protected function generate_webhook_signature($payload) {
        $secret = $this->get_setting('webhook_secret', '');
        return hash_hmac('sha256', $payload, $secret);
    }
    
    /**
     * Record performance metrics
     * 
     * @param string $metric_name Metric name
     * @param mixed $value Metric value
     * @param array $tags Additional tags
     */
    protected function record_metric($metric_name, $value, $tags = []) {
        $metric_data = [
            'gateway' => $this->id,
            'metric' => $metric_name,
            'value' => $value,
            'timestamp' => time(),
            'tags' => $tags
        ];
        
        // Store in database or send to monitoring service
        do_action('kilismile_payment_metric', $metric_data);
    }
    
    /**
     * Enhanced logging with structured data
     * 
     * @param string $message Log message
     * @param string $level Log level (info, warning, error)
     * @param array $context Additional context data
     */
    protected function log($message, $level = 'info', $context = []) {
        if (!$this->logging_enabled) {
            return;
        }
        
        $log_data = [
            'timestamp' => current_time('mysql'),
            'gateway' => $this->id,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'ip' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        // Log to WordPress debug log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("KiliSmile Payment [{$level}] [{$this->id}]: {$message}");
        }
        
        // Store in database for analysis
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_payment_logs';
        
        $wpdb->insert(
            $table_name,
            [
                'gateway_id' => $this->id,
                'level' => $level,
                'message' => $message,
                'context' => json_encode($context),
                'ip_address' => $log_data['ip'],
                'user_agent' => $log_data['user_agent'],
                'created_at' => $log_data['timestamp']
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );
    }
    
    /**
     * Health check endpoint
     * 
     * @return array Health check results
     */
    public function health_check() {
        $start_time = microtime(true);
        $health_status = [
            'gateway' => $this->id,
            'status' => 'healthy',
            'timestamp' => time(),
            'response_time' => 0,
            'details' => []
        ];
        
        try {
            // Perform gateway-specific health checks
            $this->perform_health_checks($health_status);
            
        } catch (Exception $e) {
            $health_status['status'] = 'unhealthy';
            $health_status['details']['error'] = $e->getMessage();
        }
        
        $health_status['response_time'] = microtime(true) - $start_time;
        
        // Record health check metric
        $this->record_metric('health_check', $health_status['status'] === 'healthy' ? 1 : 0);
        
        return $health_status;
    }
    
    /**
     * Perform gateway-specific health checks
     * Override in child classes
     * 
     * @param array &$health_status Health status array to modify
     */
    protected function perform_health_checks(&$health_status) {
        // Default implementation - check if gateway is enabled
        if (!$this->is_enabled()) {
            $health_status['status'] = 'disabled';
            $health_status['details']['enabled'] = false;
        } else {
            $health_status['details']['enabled'] = true;
        }
    }
    
    /**
     * Get gateway statistics
     * 
     * @param int $days Number of days to analyze
     * @return array Gateway statistics
     */
    public function get_statistics($days = 30) {
        global $wpdb;
        
        $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        // Get transaction statistics
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful_transactions,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_transactions,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
                AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as average_amount
            FROM {$wpdb->prefix}kilismile_transactions 
            WHERE payment_method = %s 
            AND date_created >= %s
        ", $this->id, $start_date), ARRAY_A);
        
        // Calculate success rate
        $success_rate = $stats['total_transactions'] > 0 
            ? ($stats['successful_transactions'] / $stats['total_transactions']) * 100 
            : 0;
        
        return [
            'gateway' => $this->id,
            'period_days' => $days,
            'total_transactions' => (int) $stats['total_transactions'],
            'successful_transactions' => (int) $stats['successful_transactions'],
            'failed_transactions' => (int) $stats['failed_transactions'],
            'success_rate' => round($success_rate, 2),
            'total_amount' => (float) $stats['total_amount'],
            'average_amount' => (float) $stats['average_amount']
        ];
    }
}

/**
 * Create payment log table on activation
 */
function kilismile_create_payment_log_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_payment_logs';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        gateway_id varchar(50) NOT NULL,
        level varchar(10) NOT NULL,
        message text NOT NULL,
        context longtext,
        ip_address varchar(45),
        user_agent text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY gateway_id (gateway_id),
        KEY level (level),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register activation hook
register_activation_hook(__FILE__, 'kilismile_create_payment_log_table');


