<?php
/**
 * Modern Donation System for Kilismile
 * 
 * A comprehensive, secure, and scalable donation processing system
 * that integrates seamlessly with the new multi-step donation form.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Donation System Class
 */
class KiliSmile_Modern_Donation_System {
    
    private static $instance = null;
    private $gateway_factory;
    private $validator;
    private $db_handler;
    private $email_handler;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_components();
        $this->register_hooks();
    }
    
    /**
     * Initialize components
     */
    private function init_components() {
        // Check if required classes exist before instantiating
        if (!class_exists('KiliSmile_Payment_Gateway_Factory')) {
            wp_die('KiliSmile_Payment_Gateway_Factory class not found. Please ensure payment-gateways-modern.php is loaded.');
        }
        
        if (!class_exists('KiliSmile_Donation_Database')) {
            wp_die('KiliSmile_Donation_Database class not found. Please ensure donation-database.php is loaded.');
        }
        
        if (!class_exists('KiliSmile_Donation_Email_Handler')) {
            wp_die('KiliSmile_Donation_Email_Handler class not found. Please ensure donation-email-handler.php is loaded.');
        }
        
        $this->gateway_factory = new KiliSmile_Payment_Gateway_Factory();
        $this->validator = new KiliSmile_Donation_Validator();
        $this->db_handler = new KiliSmile_Donation_Database();
        $this->email_handler = new KiliSmile_Donation_Email_Handler();
    }
    
    /**
     * Register WordPress hooks
     */
    private function register_hooks() {
        // AJAX endpoints for donation processing
        add_action('wp_ajax_process_donation', array($this, 'handle_donation_ajax'));
        add_action('wp_ajax_nopriv_process_donation', array($this, 'handle_donation_ajax'));
        
        // Payment method specific endpoints
        add_action('wp_ajax_get_payment_methods', array($this, 'get_payment_methods'));
        add_action('wp_ajax_nopriv_get_payment_methods', array($this, 'get_payment_methods'));
        
        // Validation endpoints
        add_action('wp_ajax_validate_donation_data', array($this, 'validate_donation_data'));
        add_action('wp_ajax_nopriv_validate_donation_data', array($this, 'validate_donation_data'));
        
        // Currency conversion
        add_action('wp_ajax_convert_currency', array($this, 'convert_currency'));
        add_action('wp_ajax_nopriv_convert_currency', array($this, 'convert_currency'));
        
        // Payment status checking
        add_action('wp_ajax_check_payment_status', array($this, 'check_payment_status'));
        add_action('wp_ajax_nopriv_check_payment_status', array($this, 'check_payment_status'));
        
        // Admin endpoints
        add_action('wp_ajax_get_donation_analytics', array($this, 'get_donation_analytics'));
        add_action('wp_ajax_export_donations', array($this, 'export_donations'));
        
        // Webhook handlers
        add_action('wp_ajax_nopriv_paypal_webhook', array($this, 'handle_paypal_webhook'));
        add_action('wp_ajax_nopriv_selcom_webhook', array($this, 'handle_selcom_webhook'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Create database tables
        add_action('after_setup_theme', array($this, 'create_database_tables'));
    }
    
    /**
     * Handle main donation AJAX request
     */
    public function handle_donation_ajax() {
        try {
            // Verify nonce for security
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'donation_nonce')) {
                throw new Exception(__('Security verification failed.', 'kilismile'));
            }
            
            // Rate limiting check
            if (!$this->check_rate_limit()) {
                throw new Exception(__('Too many requests. Please try again later.', 'kilismile'));
            }
            
            // Sanitize and validate input data
            $donation_data = $this->validator->sanitize_donation_data($_POST);
            $validation_result = $this->validator->validate_donation_data($donation_data);
            
            if (!$validation_result['valid']) {
                wp_send_json_error(array(
                    'message' => __('Validation failed.', 'kilismile'),
                    'errors' => $validation_result['errors']
                ));
                return;
            }
            
            // Generate unique donation ID
            $donation_id = $this->generate_donation_id();
            $donation_data['donation_id'] = $donation_id;
            
            // Save donation to database (pending status)
            $db_result = $this->db_handler->create_donation($donation_data);
            if (!$db_result) {
                throw new Exception(__('Failed to save donation data.', 'kilismile'));
            }
            
            // Get appropriate payment gateway
            $gateway = $this->gateway_factory->get_gateway(
                $donation_data['payment_method'],
                $donation_data['currency']
            );
            
            if (!$gateway) {
                throw new Exception(__('Payment method not available.', 'kilismile'));
            }
            
            // Process payment
            $payment_result = $gateway->process_payment($donation_id, $donation_data);
            
            if ($payment_result['success']) {
                // Update donation status
                $this->db_handler->update_donation_status($donation_id, 'processing');
                
                // Send confirmation email
                $this->email_handler->send_donation_confirmation($donation_data);
                
                // Log successful initiation
                $this->log_donation_event($donation_id, 'payment_initiated', $payment_result);
                
                wp_send_json_success(array(
                    'message' => __('Donation initiated successfully.', 'kilismile'),
                    'donation_id' => $donation_id,
                    'redirect_url' => $payment_result['redirect_url'] ?? null,
                    'payment_data' => $payment_result['payment_data'] ?? null
                ));
            } else {
                // Update donation status to failed
                $this->db_handler->update_donation_status($donation_id, 'failed');
                
                // Log failure
                $this->log_donation_event($donation_id, 'payment_failed', $payment_result);
                
                wp_send_json_error(array(
                    'message' => $payment_result['message'] ?? __('Payment processing failed.', 'kilismile'),
                    'error_code' => $payment_result['error_code'] ?? 'PAYMENT_FAILED'
                ));
            }
            
        } catch (Exception $e) {
            error_log('Donation processing error: ' . $e->getMessage());
            
            wp_send_json_error(array(
                'message' => $e->getMessage(),
                'error_code' => 'SYSTEM_ERROR'
            ));
        }
    }
    
    /**
     * Get available payment methods based on currency
     */
    public function get_payment_methods() {
        try {
            $currency = sanitize_text_field($_GET['currency'] ?? 'USD');
            $methods = $this->gateway_factory->get_available_methods($currency);
            
            wp_send_json_success(array(
                'payment_methods' => $methods,
                'currency' => $currency
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Validate donation data via AJAX
     */
    public function validate_donation_data() {
        try {
            $data = $this->validator->sanitize_donation_data($_POST);
            $result = $this->validator->validate_donation_data($data);
            
            wp_send_json_success($result);
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Convert currency amounts
     */
    public function convert_currency() {
        try {
            $amount = floatval($_GET['amount'] ?? 0);
            $from_currency = sanitize_text_field($_GET['from'] ?? 'USD');
            $to_currency = sanitize_text_field($_GET['to'] ?? 'TZS');
            
            $converted_amount = $this->convert_currency_amount($amount, $from_currency, $to_currency);
            
            wp_send_json_success(array(
                'original_amount' => $amount,
                'converted_amount' => $converted_amount,
                'from_currency' => $from_currency,
                'to_currency' => $to_currency,
                'rate' => $this->get_exchange_rate($from_currency, $to_currency)
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Check payment status
     */
    public function check_payment_status() {
        try {
            $donation_id = sanitize_text_field($_GET['donation_id'] ?? '');
            
            if (empty($donation_id)) {
                throw new Exception(__('Invalid donation ID.', 'kilismile'));
            }
            
            $donation = $this->db_handler->get_donation($donation_id);
            if (!$donation) {
                throw new Exception(__('Donation not found.', 'kilismile'));
            }
            
            // Check with payment gateway for real-time status
            $gateway = $this->gateway_factory->get_gateway(
                $donation['payment_method'],
                $donation['currency']
            );
            
            if ($gateway) {
                $status = $gateway->check_payment_status($donation_id, $donation);
                
                // Update database if status changed
                if ($status['status'] !== $donation['status']) {
                    $this->db_handler->update_donation_status($donation_id, $status['status']);
                    $this->log_donation_event($donation_id, 'status_updated', $status);
                }
                
                wp_send_json_success(array(
                    'donation_id' => $donation_id,
                    'status' => $status['status'],
                    'message' => $status['message'] ?? '',
                    'updated_at' => current_time('mysql')
                ));
            } else {
                wp_send_json_success(array(
                    'donation_id' => $donation_id,
                    'status' => $donation['status'],
                    'message' => __('Status from database record.', 'kilismile')
                ));
            }
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Handle PayPal webhook
     */
    public function handle_paypal_webhook() {
        try {
            $gateway = $this->gateway_factory->get_gateway('paypal', 'USD');
            if ($gateway && method_exists($gateway, 'handle_webhook')) {
                $result = $gateway->handle_webhook();
                
                if ($result['success']) {
                    http_response_code(200);
                    echo 'OK';
                } else {
                    http_response_code(400);
                    echo 'Invalid webhook';
                }
            } else {
                http_response_code(404);
                echo 'Gateway not found';
            }
        } catch (Exception $e) {
            error_log('PayPal webhook error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Server error';
        }
        exit;
    }
    
    /**
     * Handle Selcom webhook
     */
    public function handle_selcom_webhook() {
        try {
            $gateway = $this->gateway_factory->get_gateway('selcom', 'TZS');
            if ($gateway && method_exists($gateway, 'handle_webhook')) {
                $result = $gateway->handle_webhook();
                
                if ($result['success']) {
                    http_response_code(200);
                    echo 'OK';
                } else {
                    http_response_code(400);
                    echo 'Invalid webhook';
                }
            } else {
                http_response_code(404);
                echo 'Gateway not found';
            }
        } catch (Exception $e) {
            error_log('Selcom webhook error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Server error';
        }
        exit;
    }
    
    /**
     * Enqueue necessary assets
     */
    public function enqueue_assets() {
        // Only enqueue on donation pages
        if (is_page_template('page-donations.php') || is_page('donate') || is_page('donation')) {
            wp_enqueue_script('kilismile-donation-modern', 
                get_template_directory_uri() . '/assets/js/donation-modern.js',
                array('jquery'), 
                '2.0.0', 
                true
            );
            
            wp_localize_script('kilismile-donation-modern', 'kilismileDonation', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('donation_nonce'),
                'currency_rates' => $this->get_currency_rates(),
                'strings' => array(
                    'processing' => __('Processing donation...', 'kilismile'),
                    'success' => __('Thank you for your donation!', 'kilismile'),
                    'error' => __('An error occurred. Please try again.', 'kilismile'),
                    'validating' => __('Validating...', 'kilismile'),
                    'invalid_amount' => __('Please enter a valid amount.', 'kilismile'),
                    'invalid_email' => __('Please enter a valid email address.', 'kilismile'),
                    'required_field' => __('This field is required.', 'kilismile')
                )
            ));
        }
    }
    
    /**
     * Create database tables
     */
    public function create_database_tables() {
        $this->db_handler->create_tables();
    }
    
    /**
     * Utility Methods
     */
    
    /**
     * Generate unique donation ID
     */
    private function generate_donation_id() {
        return 'DON_' . strtoupper(uniqid() . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
    }
    
    /**
     * Check rate limiting
     */
    private function check_rate_limit() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = 'donation_rate_limit_' . md5($ip);
        $attempts = get_transient($transient_key) ?: 0;
        
        if ($attempts >= 5) { // Max 5 donations per hour per IP
            return false;
        }
        
        set_transient($transient_key, $attempts + 1, HOUR_IN_SECONDS);
        return true;
    }
    
    /**
     * Convert currency amount
     */
    private function convert_currency_amount($amount, $from_currency, $to_currency) {
        if ($from_currency === $to_currency) {
            return $amount;
        }
        
        $rate = $this->get_exchange_rate($from_currency, $to_currency);
        return round($amount * $rate, 2);
    }
    
    /**
     * Get exchange rate
     */
    private function get_exchange_rate($from_currency, $to_currency) {
        // Default rates (should be updated from external API)
        $rates = array(
            'USD_to_TZS' => 2500,
            'TZS_to_USD' => 0.0004,
            'USD_to_USD' => 1,
            'TZS_to_TZS' => 1
        );
        
        $rate_key = $from_currency . '_to_' . $to_currency;
        return $rates[$rate_key] ?? 1;
    }
    
    /**
     * Get all currency rates
     */
    private function get_currency_rates() {
        return array(
            'USD_to_TZS' => 2500,
            'TZS_to_USD' => 0.0004
        );
    }
    
    /**
     * Log donation event
     */
    private function log_donation_event($donation_id, $event_type, $data = array()) {
        $this->db_handler->log_event($donation_id, $event_type, $data);
    }
}

/**
 * Payment Gateway Factory
 */
class KiliSmile_Payment_Gateway_Factory {
    
    private $gateways = array();
    
    public function __construct() {
        $this->register_gateways();
    }
    
    /**
     * Register available payment gateways
     */
    private function register_gateways() {
        // USD Gateways
        $this->gateways['USD'] = array(
            'paypal' => 'KiliSmile_PayPal_Gateway_Modern',
            'stripe' => 'KiliSmile_Stripe_Gateway_Modern',
            'bank_transfer' => 'KiliSmile_BankTransfer_Gateway_Modern'
        );
        
        // TZS Gateways  
        $this->gateways['TZS'] = array(
            'selcom' => 'KiliSmile_Selcom_Gateway_Modern',
            'mpesa' => 'KiliSmile_MPesa_Gateway_Modern',
            'airtel_money' => 'KiliSmile_AirtelMoney_Gateway_Modern',
            'tigo_pesa' => 'KiliSmile_TigoPesa_Gateway_Modern',
            'bank_transfer' => 'KiliSmile_BankTransfer_Gateway_Modern'
        );
    }
    
    /**
     * Get payment gateway instance
     */
    public function get_gateway($method, $currency) {
        if (!isset($this->gateways[$currency][$method])) {
            return null;
        }
        
        $gateway_class = $this->gateways[$currency][$method];
        
        if (!class_exists($gateway_class)) {
            return null;
        }
        
        return new $gateway_class();
    }
    
    /**
     * Get available payment methods for currency
     */
    public function get_available_methods($currency) {
        $methods = array();
        
        if (isset($this->gateways[$currency])) {
            foreach ($this->gateways[$currency] as $method => $class) {
                if (class_exists($class)) {
                    $gateway = new $class();
                    if (method_exists($gateway, 'is_available') && $gateway->is_available()) {
                        $methods[] = array(
                            'id' => $method,
                            'name' => $gateway->get_name(),
                            'description' => $gateway->get_description(),
                            'icon' => $gateway->get_icon(),
                            'supports_recurring' => $gateway->supports_recurring()
                        );
                    }
                }
            }
        }
        
        return $methods;
    }
}

/**
 * Donation Validator
 */
class KiliSmile_Donation_Validator {
    
    /**
     * Sanitize donation data
     */
    public function sanitize_donation_data($data) {
        return array(
            'amount' => floatval($data['amount'] ?? 0),
            'currency' => sanitize_text_field($data['currency'] ?? 'USD'),
            'recurring' => (bool) ($data['recurring'] ?? false),
            'first_name' => sanitize_text_field($data['first_name'] ?? ''),
            'last_name' => sanitize_text_field($data['last_name'] ?? ''),
            'email' => sanitize_email($data['email'] ?? ''),
            'phone' => sanitize_text_field($data['phone'] ?? ''),
            'anonymous' => (bool) ($data['anonymous'] ?? false),
            'payment_method' => sanitize_text_field($data['payment_method'] ?? ''),
            'purpose' => sanitize_text_field($data['purpose'] ?? 'general'),
            'message' => sanitize_textarea_field($data['message'] ?? ''),
            'country' => sanitize_text_field($data['country'] ?? ''),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
    }
    
    /**
     * Validate donation data
     */
    public function validate_donation_data($data) {
        $errors = array();
        
        // Amount validation
        if (empty($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = __('Please enter a valid donation amount.', 'kilismile');
        } elseif ($data['amount'] < $this->get_minimum_amount($data['currency'])) {
            $errors['amount'] = sprintf(
                __('Minimum donation amount is %s %s.', 'kilismile'),
                $this->get_minimum_amount($data['currency']),
                $data['currency']
            );
        } elseif ($data['amount'] > $this->get_maximum_amount($data['currency'])) {
            $errors['amount'] = sprintf(
                __('Maximum donation amount is %s %s.', 'kilismile'),
                $this->get_maximum_amount($data['currency']),
                $data['currency']
            );
        }
        
        // Personal information validation
        if (empty($data['first_name'])) {
            $errors['first_name'] = __('First name is required.', 'kilismile');
        }
        
        if (empty($data['last_name'])) {
            $errors['last_name'] = __('Last name is required.', 'kilismile');
        }
        
        if (empty($data['email'])) {
            $errors['email'] = __('Email address is required.', 'kilismile');
        } elseif (!is_email($data['email'])) {
            $errors['email'] = __('Please enter a valid email address.', 'kilismile');
        }
        
        // Payment method validation
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = __('Please select a payment method.', 'kilismile');
        }
        
        // Currency validation
        if (!in_array($data['currency'], array('USD', 'TZS'))) {
            $errors['currency'] = __('Invalid currency selected.', 'kilismile');
        }
        
        // Phone validation for mobile money
        if (in_array($data['payment_method'], array('mpesa', 'airtel_money', 'tigo_pesa'))) {
            if (empty($data['phone'])) {
                $errors['phone'] = __('Phone number is required for mobile money payments.', 'kilismile');
            } elseif (!$this->validate_phone_number($data['phone'], $data['payment_method'])) {
                $errors['phone'] = __('Please enter a valid phone number for the selected mobile money service.', 'kilismile');
            }
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
    
    /**
     * Get minimum donation amount by currency
     */
    private function get_minimum_amount($currency) {
        $minimums = array(
            'USD' => 1,
            'TZS' => 1000
        );
        
        return $minimums[$currency] ?? 1;
    }
    
    /**
     * Get maximum donation amount by currency
     */
    private function get_maximum_amount($currency) {
        $maximums = array(
            'USD' => 50000,
            'TZS' => 100000000
        );
        
        return $maximums[$currency] ?? 50000;
    }
    
    /**
     * Validate phone number for mobile money services
     */
    private function validate_phone_number($phone, $payment_method) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Validate based on payment method
        switch ($payment_method) {
            case 'mpesa':
                // M-Pesa uses Vodacom numbers: 0754, 0755, 0756, 0757, 0768, 0769
                return preg_match('/^(255)?(0)?(75[4-7]|76[89])\d{6}$/', $phone);
                
            case 'airtel_money':
                // Airtel Money: 0754, 0755, 0756, 0678, 0679
                return preg_match('/^(255)?(0)?(75[4-6]|67[89])\d{6}$/', $phone);
                
            case 'tigo_pesa':
                // Tigo Pesa: 0713, 0714, 0715, 0716, 0717, 0652, 0653, 0655, 0656
                return preg_match('/^(255)?(0)?(71[3-7]|65[2356])\d{6}$/', $phone);
                
            default:
                // Generic Tanzania mobile number validation
                return preg_match('/^(255)?(0)?[67]\d{8}$/', $phone);
        }
    }
}

// Initialize the modern donation system
KiliSmile_Modern_Donation_System::get_instance();


