<?php
/**
 * Abstract Payment Gateway Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract Payment Gateway
 * 
 * Base class for all payment gateways
 */
abstract class KiliSmile_Payment_Gateway {
    
    /**
     * Gateway ID
     */
    public $id;
    
    /**
     * Gateway title
     */
    public $title;
    
    /**
     * Gateway description
     */
    public $description;
    
    /**
     * Whether gateway is enabled
     */
    public $enabled = false;
    
    /**
     * Test mode
     */
    public $test_mode = false;
    
    /**
     * Supported features
     */
    public $supports = array();
    
    /**
     * Gateway settings
     */
    public $settings = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init();
        $this->load_settings();
        $this->init_hooks();
    }
    
    /**
     * Initialize gateway
     */
    abstract public function init();
    
    /**
     * Process payment
     */
    abstract public function process_payment($data);
    
    /**
     * Initialize hooks
     */
    protected function init_hooks() {
        // Add any common hooks here
    }
    
    /**
     * Load gateway settings
     */
    protected function load_settings() {
        $this->enabled = get_option($this->id . '_enabled', false);
        $this->test_mode = get_option('kilismile_test_mode', true);
    }
    
    /**
     * Get gateway ID
     */
    public function get_id() {
        return $this->id;
    }
    
    /**
     * Get gateway title
     */
    public function get_title() {
        return $this->title;
    }
    
    /**
     * Get gateway description
     */
    public function get_description() {
        return $this->description;
    }
    
    /**
     * Check if gateway is enabled
     */
    public function is_enabled() {
        return $this->enabled === true || $this->enabled === 'yes' || $this->enabled === '1';
    }
    
    /**
     * Check if in test mode
     */
    public function is_test_mode() {
        return $this->test_mode === true || $this->test_mode === 'yes' || $this->test_mode === '1';
    }
    
    /**
     * Get supported features
     */
    public function get_supported_features() {
        return $this->supports;
    }
    
    /**
     * Check if gateway supports a feature
     */
    public function supports($feature) {
        return in_array($feature, $this->supports);
    }
    
    /**
     * Log gateway activity
     */
    protected function log($message, $level = 'info', $context = array()) {
        if (class_exists('KiliSmile_Payments_Logger')) {
            KiliSmile_Payments_Logger::log($this->id, $message, $level, $context);
        } else {
            error_log(sprintf('[%s] %s: %s', strtoupper($level), $this->id, $message));
        }
    }
    
    /**
     * Validate required fields
     */
    protected function validate_required_fields($data, $required_fields) {
        $errors = array();
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = sprintf(__('%s is required.', 'kilismile-payments'), ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize payment data
     */
    protected function sanitize_payment_data($data) {
        return array(
            'donor_name' => sanitize_text_field($data['donor_name'] ?? ''),
            'donor_email' => sanitize_email($data['donor_email'] ?? ''),
            'donor_phone' => sanitize_text_field($data['donor_phone'] ?? ''),
            'amount' => floatval($data['amount'] ?? 0),
            'currency' => sanitize_text_field($data['currency'] ?? 'TZS'),
            'payment_method' => sanitize_text_field($data['payment_method'] ?? ''),
            'recurring' => !empty($data['recurring']),
            'anonymous' => !empty($data['anonymous']),
            'payment_gateway' => sanitize_text_field($data['payment_gateway'] ?? $this->id)
        );
    }
    
    /**
     * Save transaction to database
     */
    protected function save_transaction($data) {
        if (class_exists('KiliSmile_Payments_Database')) {
            return KiliSmile_Payments_Database::save_transaction($data);
        }
        return false;
    }
    
    /**
     * Update transaction status
     */
    protected function update_transaction_status($transaction_id, $status, $gateway_response = array()) {
        if (class_exists('KiliSmile_Payments_Database')) {
            return KiliSmile_Payments_Database::update_transaction_status($transaction_id, $status, $gateway_response);
        }
        return false;
    }
    
    /**
     * Get API credentials
     */
    protected function get_api_credentials() {
        $prefix = $this->is_test_mode() ? 'test_' : '';
        
        return array(
            'api_key' => get_option($this->id . '_' . $prefix . 'api_key', ''),
            'secret_key' => get_option($this->id . '_' . $prefix . 'secret_key', ''),
            'app_name' => get_option($this->id . '_app_name', ''),
            'endpoint' => $this->is_test_mode() ? $this->get_test_endpoint() : $this->get_live_endpoint()
        );
    }
    
    /**
     * Get test endpoint
     */
    protected function get_test_endpoint() {
        return '';
    }
    
    /**
     * Get live endpoint
     */
    protected function get_live_endpoint() {
        return '';
    }
    
    /**
     * Make API request
     */
    protected function make_api_request($endpoint, $data = array(), $method = 'POST', $headers = array()) {
        $args = array(
            'method' => $method,
            'timeout' => 30,
            'headers' => array_merge(array(
                'Content-Type' => 'application/json'
            ), $headers),
            'body' => $method !== 'GET' ? json_encode($data) : null
        );
        
        if ($method === 'GET' && !empty($data)) {
            $endpoint .= '?' . http_build_query($data);
        }
        
        $this->log('API Request', 'info', array(
            'endpoint' => $endpoint,
            'method' => $method,
            'data' => $data
        ));
        
        $response = wp_remote_request($endpoint, $args);
        
        if (is_wp_error($response)) {
            $this->log('API Error', 'error', array(
                'error' => $response->get_error_message()
            ));
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $status_code = wp_remote_retrieve_response_code($response);
        
        $this->log('API Response', 'info', array(
            'status_code' => $status_code,
            'response' => $body
        ));
        
        $decoded = json_decode($body, true);
        
        if ($status_code >= 200 && $status_code < 300) {
            return $decoded;
        } else {
            return new WP_Error('api_error', 'API request failed', array(
                'status_code' => $status_code,
                'response' => $decoded
            ));
        }
    }
    
    /**
     * Get webhook URL
     */
    protected function get_webhook_url() {
        return add_query_arg(array(
            'kilismile_webhook' => $this->id
        ), home_url('/'));
    }
    
    /**
     * Process webhook
     */
    public function process_webhook($data) {
        // Override in child classes
        $this->log('Webhook received', 'info', $data);
        return false;
    }
    
    /**
     * Format currency amount
     */
    protected function format_amount($amount, $currency = 'TZS') {
        return number_format($amount, 2);
    }
    
    /**
     * Convert currency
     */
    protected function convert_currency($amount, $from_currency, $to_currency) {
        if ($from_currency === $to_currency) {
            return $amount;
        }
        
        $rates = array(
            'USD_to_TZS' => floatval(get_option('kilismile_usd_to_tzs_rate', 2350)),
            'TZS_to_USD' => floatval(get_option('kilismile_tzs_to_usd_rate', 0.000426))
        );
        
        $conversion_key = $from_currency . '_to_' . $to_currency;
        
        if (isset($rates[$conversion_key])) {
            return $amount * $rates[$conversion_key];
        }
        
        return $amount;
    }
    
    /**
     * Get setting value
     */
    protected function get_setting($key, $default = '') {
        return get_option($this->id . '_' . $key, $default);
    }
    
    /**
     * Update setting value
     */
    protected function update_setting($key, $value) {
        return update_option($this->id . '_' . $key, $value);
    }
}

