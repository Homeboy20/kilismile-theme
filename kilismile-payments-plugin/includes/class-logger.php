<?php
/**
 * Logger Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Comprehensive Logging System
 * 
 * Provides detailed logging functionality for:
 * - Payment transaction tracking
 * - Gateway API interactions
 * - Error monitoring and debugging
 * - System performance monitoring
 * - Audit trails and compliance
 */
class KiliSmile_Payments_Logger {
    
    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';
    
    /**
     * Database instance
     */
    private $db;
    
    /**
     * Plugin settings
     */
    private $settings;
    
    /**
     * Log file path
     */
    private $log_file;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->db = $db;
        $this->settings = get_option('kilismile_payments_options', array());
        
        // Set up log file
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/kilismile-payments-logs';
        
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            
            // Create .htaccess to protect log files
            $htaccess_content = "Order deny,allow\nDeny from all";
            file_put_contents($log_dir . '/.htaccess', $htaccess_content);
            
            // Create index.php to prevent directory listing
            file_put_contents($log_dir . '/index.php', '<?php // Silence is golden');
        }
        
        $this->log_file = $log_dir . '/kilismile-payments-' . date('Y-m-d') . '.log';
    }
    
    /**
     * Log a message
     */
    public function log($message, $level = self::LEVEL_INFO, $context = array(), $gateway = '', $transaction_id = null) {
        // Check if logging is enabled for this level
        if (!$this->should_log($level)) {
            return false;
        }
        
        // Prepare log entry
        $log_entry = array(
            'transaction_id' => $transaction_id,
            'gateway' => $gateway,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'created_at' => current_time('mysql'),
            'ip_address' => $this->get_client_ip(),
            'user_id' => get_current_user_id()
        );
        
        // Log to database
        $this->log_to_database($log_entry);
        
        // Log to file if enabled
        if ($this->is_file_logging_enabled()) {
            $this->log_to_file($log_entry);
        }
        
        // Send critical alerts if enabled
        if ($level === self::LEVEL_CRITICAL && $this->is_email_alerts_enabled()) {
            $this->send_critical_alert($log_entry);
        }
        
        return true;
    }
    
    /**
     * Log debug message
     */
    public function debug($message, $context = array(), $gateway = '', $transaction_id = null) {
        return $this->log($message, self::LEVEL_DEBUG, $context, $gateway, $transaction_id);
    }
    
    /**
     * Log info message
     */
    public function info($message, $context = array(), $gateway = '', $transaction_id = null) {
        return $this->log($message, self::LEVEL_INFO, $context, $gateway, $transaction_id);
    }
    
    /**
     * Log warning message
     */
    public function warning($message, $context = array(), $gateway = '', $transaction_id = null) {
        return $this->log($message, self::LEVEL_WARNING, $context, $gateway, $transaction_id);
    }
    
    /**
     * Log error message
     */
    public function error($message, $context = array(), $gateway = '', $transaction_id = null) {
        return $this->log($message, self::LEVEL_ERROR, $context, $gateway, $transaction_id);
    }
    
    /**
     * Log critical message
     */
    public function critical($message, $context = array(), $gateway = '', $transaction_id = null) {
        return $this->log($message, self::LEVEL_CRITICAL, $context, $gateway, $transaction_id);
    }
    
    /**
     * Log payment transaction
     */
    public function log_transaction($action, $transaction_data, $level = self::LEVEL_INFO) {
        $message = sprintf('Transaction %s: %s', $action, $transaction_data['reference_id'] ?? 'Unknown');
        
        $context = array(
            'action' => $action,
            'transaction_data' => $transaction_data,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? ''
        );
        
        return $this->log(
            $message,
            $level,
            $context,
            $transaction_data['gateway'] ?? '',
            $transaction_data['id'] ?? null
        );
    }
    
    /**
     * Log gateway API call
     */
    public function log_api_call($gateway, $endpoint, $method, $request_data, $response_data, $status_code) {
        $message = sprintf('%s API call to %s (%s)', $gateway, $endpoint, $method);
        
        // Sanitize sensitive data
        $sanitized_request = $this->sanitize_sensitive_data($request_data);
        $sanitized_response = $this->sanitize_sensitive_data($response_data);
        
        $context = array(
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => $sanitized_request,
            'response_data' => $sanitized_response,
            'status_code' => $status_code,
            'response_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
        );
        
        $level = ($status_code >= 200 && $status_code < 300) ? self::LEVEL_INFO : self::LEVEL_ERROR;
        
        return $this->log($message, $level, $context, $gateway);
    }
    
    /**
     * Log security event
     */
    public function log_security_event($event, $details, $level = self::LEVEL_WARNING) {
        $message = sprintf('Security event: %s', $event);
        
        $context = array(
            'event' => $event,
            'details' => $details,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'session_id' => session_id()
        );
        
        return $this->log($message, $level, $context, 'security');
    }
    
    /**
     * Log performance metrics
     */
    public function log_performance($action, $duration, $memory_usage = null, $context = array()) {
        $message = sprintf('Performance: %s completed in %.3fs', $action, $duration);
        
        $context = array_merge($context, array(
            'action' => $action,
            'duration' => $duration,
            'memory_usage' => $memory_usage ?? memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ));
        
        return $this->log($message, self::LEVEL_DEBUG, $context, 'performance');
    }
    
    /**
     * Get logs from database
     */
    public function get_logs($args = array()) {
        return $this->db->get_logs($args);
    }
    
    /**
     * Get log statistics
     */
    public function get_log_stats($period = '24_hours') {
        $where_conditions = array('1=1');
        $where_values = array();
        
        // Add period filter
        switch ($period) {
            case '1_hour':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)';
                break;
            case '24_hours':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)';
                break;
            case '7_days':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case '30_days':
                $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $tables = $this->db->get_tables();
        $sql = "
            SELECT 
                level,
                COUNT(*) as count,
                COUNT(DISTINCT gateway) as gateways,
                COUNT(DISTINCT transaction_id) as transactions
            FROM {$tables['payment_logs']} 
            WHERE {$where_clause}
            GROUP BY level
        ";
        
        if (!empty($where_values)) {
            global $wpdb;
            $sql = $wpdb->prepare($sql, $where_values);
        }
        
        global $wpdb;
        $results = $wpdb->get_results($sql, ARRAY_A);
        
        // Format results
        $stats = array(
            'total' => 0,
            'by_level' => array(),
            'gateways' => 0,
            'transactions' => 0
        );
        
        foreach ($results as $row) {
            $stats['by_level'][$row['level']] = intval($row['count']);
            $stats['total'] += intval($row['count']);
            $stats['gateways'] = max($stats['gateways'], intval($row['gateways']));
            $stats['transactions'] = max($stats['transactions'], intval($row['transactions']));
        }
        
        return $stats;
    }
    
    /**
     * Clean old logs
     */
    public function clean_old_logs($days = 30) {
        global $wpdb;
        $tables = $this->db->get_tables();
        
        $result = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$tables['payment_logs']} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
        
        // Clean old log files
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/kilismile-payments-logs';
        
        if (is_dir($log_dir)) {
            $files = glob($log_dir . '/kilismile-payments-*.log');
            $cutoff_time = time() - ($days * 24 * 60 * 60);
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff_time) {
                    unlink($file);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Export logs
     */
    public function export_logs($format = 'csv', $args = array()) {
        $logs = $this->get_logs($args);
        
        if ($format === 'csv') {
            return $this->export_logs_csv($logs);
        } elseif ($format === 'json') {
            return $this->export_logs_json($logs);
        } else {
            return false;
        }
    }
    
    /**
     * Check if should log based on level
     */
    private function should_log($level) {
        $min_level = $this->settings['logging_level'] ?? self::LEVEL_INFO;
        
        $levels = array(
            self::LEVEL_DEBUG => 0,
            self::LEVEL_INFO => 1,
            self::LEVEL_WARNING => 2,
            self::LEVEL_ERROR => 3,
            self::LEVEL_CRITICAL => 4
        );
        
        return ($levels[$level] ?? 1) >= ($levels[$min_level] ?? 1);
    }
    
    /**
     * Log to database
     */
    private function log_to_database($log_entry) {
        return $this->db->add_log($log_entry);
    }
    
    /**
     * Log to file
     */
    private function log_to_file($log_entry) {
        $timestamp = date('Y-m-d H:i:s');
        $level = strtoupper($log_entry['level']);
        $gateway = $log_entry['gateway'] ? '[' . strtoupper($log_entry['gateway']) . ']' : '';
        $message = $log_entry['message'];
        $context = !empty($log_entry['context']) ? ' | Context: ' . json_encode($log_entry['context']) : '';
        
        $log_line = sprintf(
            "[%s] %s %s: %s%s\n",
            $timestamp,
            $level,
            $gateway,
            $message,
            $context
        );
        
        error_log($log_line, 3, $this->log_file);
    }
    
    /**
     * Send critical alert
     */
    private function send_critical_alert($log_entry) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf('[%s] Critical Payment System Alert', $site_name);
        
        $message = sprintf(
            "A critical error occurred in the KiliSmile Payments system:\n\n" .
            "Time: %s\n" .
            "Gateway: %s\n" .
            "Message: %s\n" .
            "IP Address: %s\n\n" .
            "Please check the payment logs for more details.\n\n" .
            "Context: %s",
            $log_entry['created_at'],
            $log_entry['gateway'] ?: 'N/A',
            $log_entry['message'],
            $log_entry['ip_address'],
            json_encode($log_entry['context'], JSON_PRETTY_PRINT)
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Sanitize sensitive data
     */
    private function sanitize_sensitive_data($data) {
        if (!is_array($data) && !is_object($data)) {
            return $data;
        }
        
        $sensitive_keys = array(
            'password', 'pass', 'pwd', 'secret', 'key', 'token', 'auth',
            'authorization', 'credential', 'private', 'signature', 'pin',
            'client_secret', 'access_token', 'refresh_token', 'api_key'
        );
        
        $sanitized = json_decode(json_encode($data), true); // Convert to array
        
        array_walk_recursive($sanitized, function(&$value, $key) use ($sensitive_keys) {
            if (is_string($key)) {
                $key_lower = strtolower($key);
                foreach ($sensitive_keys as $sensitive_key) {
                    if (strpos($key_lower, $sensitive_key) !== false) {
                        $value = '***REDACTED***';
                        break;
                    }
                }
            }
        });
        
        return $sanitized;
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = sanitize_text_field($_SERVER[$key]);
                
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return '127.0.0.1';
    }
    
    /**
     * Check if file logging is enabled
     */
    private function is_file_logging_enabled() {
        return !empty($this->settings['file_logging']);
    }
    
    /**
     * Check if email alerts are enabled
     */
    private function is_email_alerts_enabled() {
        return !empty($this->settings['email_alerts']);
    }
    
    /**
     * Export logs as CSV
     */
    private function export_logs_csv($logs) {
        $filename = 'kilismile_payment_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, array(
            'ID',
            'Transaction ID',
            'Gateway',
            'Level',
            'Message',
            'IP Address',
            'User ID',
            'Created At'
        ));
        
        // Data
        foreach ($logs as $log) {
            fputcsv($output, array(
                $log['id'],
                $log['transaction_id'],
                $log['gateway'],
                $log['level'],
                $log['message'],
                $log['ip_address'],
                $log['user_id'],
                $log['created_at']
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export logs as JSON
     */
    private function export_logs_json($logs) {
        $filename = 'kilismile_payment_logs_' . date('Y-m-d_H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($logs, JSON_PRETTY_PRINT);
        exit;
    }
}

