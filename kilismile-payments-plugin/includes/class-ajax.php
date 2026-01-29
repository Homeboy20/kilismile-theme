<?php
/**
 * AJAX Handlers Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Handlers
 * 
 * Handles all AJAX requests for the payment plugin including:
 * - Payment form processing
 * - Transaction status checking
 * - Gateway specific AJAX calls
 * - Frontend integration with existing theme
 */
class KiliSmile_Payments_AJAX {
    
    /**
     * Plugin instance
     */
    private $plugin;
    
    /**
     * Database instance
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->db = $plugin->get_database();
        
        // Public AJAX handlers (logged in and logged out users)
        add_action('wp_ajax_kilismile_process_payment', array($this, 'process_payment'));
        add_action('wp_ajax_nopriv_kilismile_process_payment', array($this, 'process_payment'));
        
        add_action('wp_ajax_kilismile_check_payment_status', array($this, 'check_payment_status'));
        add_action('wp_ajax_nopriv_kilismile_check_payment_status', array($this, 'check_payment_status'));
        
        add_action('wp_ajax_kilismile_get_payment_form', array($this, 'get_payment_form'));
        add_action('wp_ajax_nopriv_kilismile_get_payment_form', array($this, 'get_payment_form'));
        
        add_action('wp_ajax_kilismile_validate_payment_data', array($this, 'validate_payment_data'));
        add_action('wp_ajax_nopriv_kilismile_validate_payment_data', array($this, 'validate_payment_data'));
        
        // Gateway specific webhooks (already handled in gateway classes, but adding for completeness)
        add_action('wp_ajax_kilismile_azampay_webhook', array($this, 'handle_azampay_webhook'));
        add_action('wp_ajax_nopriv_kilismile_azampay_webhook', array($this, 'handle_azampay_webhook'));
        
        add_action('wp_ajax_kilismile_paypal_webhook', array($this, 'handle_paypal_webhook'));
        add_action('wp_ajax_nopriv_kilismile_paypal_webhook', array($this, 'handle_paypal_webhook'));
        
        // Currency conversion
        add_action('wp_ajax_kilismile_convert_currency', array($this, 'convert_currency'));
        add_action('wp_ajax_nopriv_kilismile_convert_currency', array($this, 'convert_currency'));
        
        // Enqueue frontend scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }
    
    /**
     * Process payment
     */
    public function process_payment() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payments_nonce')) {
            wp_send_json_error(__('Security check failed', 'kilismile-payments'));
        }
        
        try {
            // Get and sanitize form data
            $gateway_id = sanitize_text_field($_POST['gateway'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $currency = sanitize_text_field($_POST['currency'] ?? 'USD');
            $donor_name = sanitize_text_field($_POST['donor_name'] ?? '');
            $donor_email = sanitize_email($_POST['donor_email'] ?? '');
            $donor_phone = sanitize_text_field($_POST['donor_phone'] ?? '');
            $donor_address = sanitize_textarea_field($_POST['donor_address'] ?? '');
            $payment_method = sanitize_text_field($_POST['payment_method'] ?? '');
            $recurring = !empty($_POST['recurring']) ? 1 : 0;
            $recurring_interval = sanitize_text_field($_POST['recurring_interval'] ?? '');
            $transaction_type = sanitize_text_field($_POST['transaction_type'] ?? 'donation');
            
            // Validate required fields
            if (empty($gateway_id) || empty($amount) || empty($donor_name) || empty($donor_email)) {
                wp_send_json_error(__('Please fill in all required fields', 'kilismile-payments'));
            }
            
            // Validate email
            if (!is_email($donor_email)) {
                wp_send_json_error(__('Please enter a valid email address', 'kilismile-payments'));
            }
            
            // Validate amount
            if ($amount <= 0) {
                wp_send_json_error(__('Please enter a valid amount', 'kilismile-payments'));
            }
            
            // Get gateway
            $gateways = $this->plugin->get_gateways();
            if (!isset($gateways[$gateway_id])) {
                wp_send_json_error(__('Invalid payment gateway', 'kilismile-payments'));
            }
            
            $gateway = $gateways[$gateway_id];
            
            // Check if gateway is enabled
            if (!$gateway->is_enabled()) {
                wp_send_json_error(__('Payment gateway is not available', 'kilismile-payments'));
            }
            
            // Prepare payment data
            $payment_data = array(
                'gateway' => $gateway_id,
                'transaction_type' => $transaction_type,
                'amount' => $amount,
                'currency' => $currency,
                'donor_name' => $donor_name,
                'donor_email' => $donor_email,
                'donor_phone' => $donor_phone,
                'donor_address' => $donor_address,
                'payment_method' => $payment_method,
                'recurring' => $recurring,
                'recurring_interval' => $recurring_interval
            );
            
            // Process payment through gateway
            $result = $gateway->process_payment($payment_data);
            
            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            }
            
            // Log successful payment initiation
            $this->plugin->log('Payment initiated successfully', 'info', array(
                'gateway' => $gateway_id,
                'amount' => $amount,
                'currency' => $currency,
                'donor_email' => $donor_email,
                'transaction_id' => $result['transaction_id'] ?? null
            ));
            
            wp_send_json_success($result);
            
        } catch (Exception $e) {
            $this->plugin->log('Payment processing error', 'error', array(
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            
            wp_send_json_error(__('An error occurred while processing your payment', 'kilismile-payments'));
        }
    }
    
    /**
     * Check payment status
     */
    public function check_payment_status() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payments_nonce')) {
            wp_send_json_error(__('Security check failed', 'kilismile-payments'));
        }
        
        $transaction_id = intval($_POST['transaction_id'] ?? 0);
        $reference_id = sanitize_text_field($_POST['reference_id'] ?? '');
        
        if (empty($transaction_id) && empty($reference_id)) {
            wp_send_json_error(__('Invalid transaction identifier', 'kilismile-payments'));
        }
        
        // Get transaction
        if (!empty($transaction_id)) {
            $transaction = $this->db->get_transaction($transaction_id);
        } else {
            $transaction = $this->db->get_transaction_by_reference($reference_id);
        }
        
        if (!$transaction) {
            wp_send_json_error(__('Transaction not found', 'kilismile-payments'));
        }
        
        // Return transaction status
        wp_send_json_success(array(
            'status' => $transaction['status'],
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'donor_name' => $transaction['donor_name'],
            'created_at' => $transaction['created_at'],
            'completed_at' => $transaction['completed_at'],
            'gateway' => $transaction['gateway'],
            'payment_method' => $transaction['payment_method'],
            'reference_id' => $transaction['reference_id']
        ));
    }
    
    /**
     * Get payment form HTML
     */
    public function get_payment_form() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payments_nonce')) {
            wp_send_json_error(__('Security check failed', 'kilismile-payments'));
        }
        
        $gateway_id = sanitize_text_field($_POST['gateway'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $currency = sanitize_text_field($_POST['currency'] ?? 'USD');
        $transaction_type = sanitize_text_field($_POST['transaction_type'] ?? 'donation');
        
        // Get gateways
        $gateways = $this->plugin->get_gateways();
        if (!isset($gateways[$gateway_id])) {
            wp_send_json_error(__('Invalid payment gateway', 'kilismile-payments'));
        }
        
        $gateway = $gateways[$gateway_id];
        
        // Generate payment form HTML
        ob_start();
        include plugin_dir_path(dirname(__FILE__)) . 'templates/payment-form.php';
        $form_html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $form_html,
            'gateway_title' => $gateway->get_title(),
            'gateway_description' => $gateway->get_description()
        ));
    }
    
    /**
     * Validate payment data
     */
    public function validate_payment_data() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payments_nonce')) {
            wp_send_json_error(__('Security check failed', 'kilismile-payments'));
        }
        
        $errors = array();
        
        // Validate donor name
        $donor_name = sanitize_text_field($_POST['donor_name'] ?? '');
        if (empty($donor_name)) {
            $errors['donor_name'] = __('Name is required', 'kilismile-payments');
        } elseif (strlen($donor_name) < 2) {
            $errors['donor_name'] = __('Name must be at least 2 characters', 'kilismile-payments');
        }
        
        // Validate email
        $donor_email = sanitize_email($_POST['donor_email'] ?? '');
        if (empty($donor_email)) {
            $errors['donor_email'] = __('Email is required', 'kilismile-payments');
        } elseif (!is_email($donor_email)) {
            $errors['donor_email'] = __('Please enter a valid email address', 'kilismile-payments');
        }
        
        // Validate amount
        $amount = floatval($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            $errors['amount'] = __('Please enter a valid amount', 'kilismile-payments');
        }
        
        $gateway_id = sanitize_text_field($_POST['gateway'] ?? '');
        if ($gateway_id === 'azampay' && $amount < 1000) {
            $errors['amount'] = __('Minimum amount for AzamPay is TZS 1,000', 'kilismile-payments');
        } elseif ($gateway_id === 'paypal' && $amount < 1) {
            $errors['amount'] = __('Minimum amount for PayPal is USD 1.00', 'kilismile-payments');
        }
        
        // Validate phone for AzamPay
        if ($gateway_id === 'azampay') {
            $donor_phone = sanitize_text_field($_POST['donor_phone'] ?? '');
            if (empty($donor_phone)) {
                $errors['donor_phone'] = __('Phone number is required for mobile money payments', 'kilismile-payments');
            } elseif (!preg_match('/^(\+?255|0)?[67]\d{8}$/', $donor_phone)) {
                $errors['donor_phone'] = __('Please enter a valid Tanzanian phone number', 'kilismile-payments');
            }
        }
        
        if (!empty($errors)) {
            wp_send_json_error(array('validation_errors' => $errors));
        }
        
        wp_send_json_success(__('Validation passed', 'kilismile-payments'));
    }
    
    /**
     * Handle AzamPay webhook
     */
    public function handle_azampay_webhook() {
        $gateways = $this->plugin->get_gateways();
        if (isset($gateways['azampay'])) {
            $gateways['azampay']->handle_callback();
        } else {
            wp_die('Gateway not found', 'Error', array('response' => 404));
        }
    }
    
    /**
     * Handle PayPal webhook
     */
    public function handle_paypal_webhook() {
        $gateways = $this->plugin->get_gateways();
        if (isset($gateways['paypal'])) {
            $gateways['paypal']->handle_callback();
        } else {
            wp_die('Gateway not found', 'Error', array('response' => 404));
        }
    }
    
    /**
     * Convert currency
     */
    public function convert_currency() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payments_nonce')) {
            wp_send_json_error(__('Security check failed', 'kilismile-payments'));
        }
        
        $amount = floatval($_POST['amount'] ?? 0);
        $from_currency = sanitize_text_field($_POST['from_currency'] ?? 'USD');
        $to_currency = sanitize_text_field($_POST['to_currency'] ?? 'TZS');
        
        if ($amount <= 0) {
            wp_send_json_error(__('Invalid amount', 'kilismile-payments'));
        }
        
        // Simple currency conversion rates (in production, use live rates)
        $rates = array(
            'USD_TZS' => 2300,
            'TZS_USD' => 1/2300,
            'USD_EUR' => 0.85,
            'EUR_USD' => 1/0.85,
            'USD_GBP' => 0.75,
            'GBP_USD' => 1/0.75
        );
        
        $conversion_key = $from_currency . '_' . $to_currency;
        
        if ($from_currency === $to_currency) {
            $converted_amount = $amount;
        } elseif (isset($rates[$conversion_key])) {
            $converted_amount = $amount * $rates[$conversion_key];
        } else {
            wp_send_json_error(__('Currency conversion not supported', 'kilismile-payments'));
        }
        
        wp_send_json_success(array(
            'original_amount' => $amount,
            'original_currency' => $from_currency,
            'converted_amount' => round($converted_amount, 2),
            'converted_currency' => $to_currency,
            'exchange_rate' => $rates[$conversion_key] ?? 1
        ));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        // Only enqueue on pages that might have payment forms
        if (!is_page(array('donate', 'donation', 'corporate', 'partnerships')) && !is_single()) {
            return;
        }
        
        wp_enqueue_script('jquery');
        
        wp_enqueue_script(
            'kilismile-payments-frontend',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/frontend.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('kilismile-payments-frontend', 'kilismile_payments', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_payments_nonce'),
            'strings' => array(
                'processing' => __('Processing payment...', 'kilismile-payments'),
                'redirecting' => __('Redirecting to payment gateway...', 'kilismile-payments'),
                'checking_status' => __('Checking payment status...', 'kilismile-payments'),
                'payment_successful' => __('Payment completed successfully!', 'kilismile-payments'),
                'payment_failed' => __('Payment failed. Please try again.', 'kilismile-payments'),
                'payment_cancelled' => __('Payment was cancelled.', 'kilismile-payments'),
                'network_error' => __('Network error. Please check your connection and try again.', 'kilismile-payments'),
                'validation_error' => __('Please correct the errors below and try again.', 'kilismile-payments'),
                'required_field' => __('This field is required', 'kilismile-payments'),
                'invalid_email' => __('Please enter a valid email address', 'kilismile-payments'),
                'invalid_phone' => __('Please enter a valid phone number', 'kilismile-payments'),
                'invalid_amount' => __('Please enter a valid amount', 'kilismile-payments'),
                'confirm_payment' => __('Confirm payment of {amount} {currency}?', 'kilismile-payments')
            ),
            'currency_symbols' => array(
                'USD' => '$',
                'TZS' => 'TZS',
                'EUR' => '€',
                'GBP' => '£'
            ),
            'gateways' => $this->get_enabled_gateways_data()
        ));
        
        wp_enqueue_style(
            'kilismile-payments-frontend',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/frontend.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Get enabled gateways data for frontend
     */
    private function get_enabled_gateways_data() {
        $gateways_data = array();
        $gateways = $this->plugin->get_gateways();
        
        foreach ($gateways as $gateway_id => $gateway) {
            if ($gateway->is_enabled()) {
                $gateways_data[$gateway_id] = array(
                    'id' => $gateway_id,
                    'title' => $gateway->get_title(),
                    'description' => $gateway->get_description(),
                    'supports' => $gateway->get_supports(),
                    'currencies' => $gateway->get_supported_currencies(),
                    'min_amount' => $gateway->get_min_amount(),
                    'max_amount' => $gateway->get_max_amount()
                );
            }
        }
        
        return $gateways_data;
    }
    
    /**
     * Get payment form shortcode
     */
    public function payment_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'gateway' => '',
            'amount' => '',
            'currency' => 'USD',
            'type' => 'donation',
            'title' => __('Make a Payment', 'kilismile-payments'),
            'description' => '',
            'show_amounts' => 'yes',
            'show_recurring' => 'yes'
        ), $atts);
        
        ob_start();
        include plugin_dir_path(dirname(__FILE__)) . 'templates/shortcode-form.php';
        return ob_get_clean();
    }
    
    /**
     * Initialize shortcodes
     */
    public function init_shortcodes() {
        add_shortcode('kilismile_payment_form', array($this, 'payment_form_shortcode'));
    }
}

