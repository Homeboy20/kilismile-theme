<?php
/**
 * AzamPay Gateway Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AzamPay Payment Gateway
 * 
 * Handles payments through AzamPay API with support for:
 * - STK Push (direct mobile payments)
 * - Checkout (hosted payment page)
 * - Mobile Money networks (Vodacom M-Pesa, Tigo Pesa, Airtel Money, Halotel, Azam Pay)
 */
class KiliSmile_AzamPay_Gateway extends KiliSmile_Payment_Gateway {
    
    /**
     * Gateway constructor
     */
    public function init() {
        $this->id = 'azampay';
        $this->title = __('AzamPay Mobile Money', 'kilismile-payments');
        $this->description = __('Pay with mobile money through AzamPay (Tanzania)', 'kilismile-payments');
        
        $this->supports = array(
            'donations',
            'subscriptions',
            'refunds',
            'webhooks',
            'mobile_money',
            'stkpush',
            'checkout'
        );
        
        // AzamPay specific hooks
        add_action('wp_ajax_kilismile_azampay_callback', array($this, 'handle_callback'));
        add_action('wp_ajax_nopriv_kilismile_azampay_callback', array($this, 'handle_callback'));
    }
    
    /**
     * Process payment
     */
    public function process_payment($data) {
        try {
            // Validate and sanitize data
            $payment_data = $this->sanitize_payment_data($data);
            
            // Validate required fields
            $required_fields = array('donor_name', 'donor_email', 'amount', 'currency');
            $errors = $this->validate_required_fields($payment_data, $required_fields);
            
            if (!empty($errors)) {
                return new WP_Error('validation_error', implode(' ', $errors));
            }
            
            // Check minimum amount
            if ($payment_data['amount'] < 1000) {
                return new WP_Error('amount_error', __('Minimum donation amount is TZS 1,000', 'kilismile-payments'));
            }
            
            // Save transaction first
            $transaction_id = $this->save_transaction(array_merge($payment_data, array(
                'status' => 'pending',
                'gateway' => $this->id,
                'created_at' => current_time('mysql')
            )));
            
            if (!$transaction_id) {
                return new WP_Error('database_error', __('Failed to save transaction', 'kilismile-payments'));
            }
            
            // Determine payment method
            $azampay_type = sanitize_text_field($data['azampay_type'] ?? 'stkpush');
            
            if ($azampay_type === 'checkout') {
                return $this->process_checkout_payment($payment_data, $transaction_id);
            } else {
                return $this->process_stkpush_payment($payment_data, $transaction_id, $data);
            }
            
        } catch (Exception $e) {
            $this->log('Payment processing error', 'error', array(
                'error' => $e->getMessage(),
                'data' => $payment_data
            ));
            
            return new WP_Error('processing_error', $e->getMessage());
        }
    }
    
    /**
     * Process STK Push payment
     */
    private function process_stkpush_payment($payment_data, $transaction_id, $raw_data) {
        // Validate mobile number
        $mobile_network = sanitize_text_field($raw_data['mobile_network'] ?? '');
        $payment_phone = sanitize_text_field($raw_data['payment_phone'] ?? '');
        
        if (empty($payment_phone)) {
            return new WP_Error('phone_required', __('Mobile phone number is required for STK Push', 'kilismile-payments'));
        }
        
        // Format phone number
        $phone = $this->format_phone_number($payment_phone);
        if (!$phone) {
            return new WP_Error('invalid_phone', __('Invalid phone number format', 'kilismile-payments'));
        }
        
        // Get provider based on network
        $provider = $this->get_mobile_provider($mobile_network);
        if (!$provider) {
            return new WP_Error('invalid_provider', __('Unsupported mobile network', 'kilismile-payments'));
        }
        
        // Prepare STK Push request
        $credentials = $this->get_api_credentials();
        $reference = 'KS_' . $transaction_id . '_' . time();
        
        $request_data = array(
            'accountNumber' => $credentials['account_number'],
            'amount' => $payment_data['amount'],
            'currency' => 'TZS',
            'externalId' => $reference,
            'provider' => $provider,
            'additionalProperties' => array(
                'msisdn' => $phone,
                'donor_name' => $payment_data['donor_name'],
                'donor_email' => $payment_data['donor_email']
            )
        );
        
        // Make API request
        $response = $this->make_azampay_request('/azampay/mno/checkout', $request_data);
        
        if (is_wp_error($response)) {
            $this->update_transaction_status($transaction_id, 'failed', array(
                'error' => $response->get_error_message()
            ));
            return $response;
        }
        
        // Update transaction with gateway response
        $this->update_transaction_status($transaction_id, 'processing', array(
            'gateway_transaction_id' => $response['transactionId'] ?? '',
            'gateway_reference' => $reference,
            'gateway_response' => $response
        ));
        
        return array(
            'success' => true,
            'message' => __('Please check your phone and enter your mobile money PIN to complete the payment.', 'kilismile-payments'),
            'transaction_id' => $transaction_id,
            'gateway_reference' => $reference,
            'payment_method' => 'STK Push',
            'provider' => $provider,
            'phone' => $this->mask_phone($phone)
        );
    }
    
    /**
     * Process checkout payment
     */
    private function process_checkout_payment($payment_data, $transaction_id) {
        $credentials = $this->get_api_credentials();
        $reference = 'KS_CHECKOUT_' . $transaction_id . '_' . time();
        
        $request_data = array(
            'accountNumber' => $credentials['account_number'],
            'amount' => $payment_data['amount'],
            'currency' => 'TZS',
            'externalId' => $reference,
            'requestOrigin' => home_url(),
            'resultUrl' => $this->get_callback_url(),
            'additionalProperties' => array(
                'donor_name' => $payment_data['donor_name'],
                'donor_email' => $payment_data['donor_email'],
                'transaction_id' => $transaction_id
            )
        );
        
        // Make API request
        $response = $this->make_azampay_request('/azampay/checkout', $request_data);
        
        if (is_wp_error($response)) {
            $this->update_transaction_status($transaction_id, 'failed', array(
                'error' => $response->get_error_message()
            ));
            return $response;
        }
        
        // Update transaction with gateway response
        $this->update_transaction_status($transaction_id, 'processing', array(
            'gateway_transaction_id' => $response['transactionId'] ?? '',
            'gateway_reference' => $reference,
            'gateway_response' => $response
        ));
        
        return array(
            'success' => true,
            'redirect' => true,
            'redirect_url' => $response['checkoutUrl'] ?? '',
            'message' => __('Redirecting to AzamPay checkout...', 'kilismile-payments'),
            'transaction_id' => $transaction_id,
            'gateway_reference' => $reference,
            'payment_method' => 'AzamPay Checkout'
        );
    }
    
    /**
     * Make AzamPay API request
     */
    private function make_azampay_request($endpoint, $data) {
        $credentials = $this->get_api_credentials();
        $base_url = $credentials['endpoint'];
        
        if (empty($credentials['api_key']) || empty($credentials['secret_key'])) {
            return new WP_Error('credentials_error', __('AzamPay API credentials not configured', 'kilismile-payments'));
        }
        
        // Generate Bearer token
        $token = $this->get_bearer_token();
        if (is_wp_error($token)) {
            return $token;
        }
        
        $headers = array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        );
        
        return $this->make_api_request($base_url . $endpoint, $data, 'POST', $headers);
    }
    
    /**
     * Get bearer token
     */
    private function get_bearer_token() {
        $credentials = $this->get_api_credentials();
        
        // Check cached token
        $cached_token = get_transient('kilismile_azampay_token');
        if ($cached_token) {
            return $cached_token;
        }
        
        // Request new token
        $auth_data = array(
            'appName' => $credentials['app_name'],
            'clientId' => $credentials['api_key'],
            'clientSecret' => $credentials['secret_key']
        );
        
        $response = $this->make_api_request($credentials['endpoint'] . '/azampay/oauth/token', $auth_data);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        if (empty($response['data']['accessToken'])) {
            return new WP_Error('token_error', __('Failed to get access token', 'kilismile-payments'));
        }
        
        $token = $response['data']['accessToken'];
        $expires_in = intval($response['data']['expire'] ?? 3600) - 300; // Refresh 5 minutes early
        
        // Cache token
        set_transient('kilismile_azampay_token', $token, $expires_in);
        
        return $token;
    }
    
    /**
     * Handle callback from AzamPay
     */
    public function handle_callback() {
        $raw_body = file_get_contents('php://input');
        $data = json_decode($raw_body, true);
        
        $this->log('Callback received', 'info', array(
            'raw_body' => $raw_body,
            'parsed_data' => $data
        ));
        
        if (empty($data)) {
            wp_die('Invalid callback data', 'Bad Request', array('response' => 400));
            return;
        }
        
        // Extract transaction reference
        $external_id = sanitize_text_field($data['externalId'] ?? '');
        if (empty($external_id)) {
            wp_die('Missing external ID', 'Bad Request', array('response' => 400));
            return;
        }
        
        // Parse transaction ID from reference
        if (preg_match('/KS(?:_CHECKOUT)?_(\d+)_/', $external_id, $matches)) {
            $transaction_id = intval($matches[1]);
        } else {
            wp_die('Invalid external ID format', 'Bad Request', array('response' => 400));
            return;
        }
        
        // Get transaction status
        $status = strtolower($data['status'] ?? '');
        $new_status = $this->map_azampay_status($status);
        
        // Update transaction
        $this->update_transaction_status($transaction_id, $new_status, array(
            'gateway_transaction_id' => $data['transactionId'] ?? '',
            'callback_data' => $data,
            'callback_time' => current_time('mysql')
        ));
        
        // Log the update
        $this->log('Transaction updated', 'info', array(
            'transaction_id' => $transaction_id,
            'status' => $new_status,
            'azampay_status' => $status
        ));
        
        wp_die('OK', 'Success', array('response' => 200));
    }
    
    /**
     * Map AzamPay status to internal status
     */
    private function map_azampay_status($azampay_status) {
        $status_map = array(
            'success' => 'completed',
            'completed' => 'completed',
            'failed' => 'failed',
            'cancelled' => 'cancelled',
            'pending' => 'processing',
            'processing' => 'processing'
        );
        
        return $status_map[$azampay_status] ?? 'processing';
    }
    
    /**
     * Get mobile provider code
     */
    private function get_mobile_provider($network) {
        $providers = array(
            'vodacom' => 'Mpesa',
            'tigo' => 'TigoPesa',
            'airtel' => 'AirtelMoney',
            'halotel' => 'HaloPesa',
            'azampay' => 'AzamPay'
        );
        
        return $providers[$network] ?? null;
    }
    
    /**
     * Format phone number for Tanzania
     */
    private function format_phone_number($phone) {
        // Remove non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle different formats
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // 0123456789 -> 255123456789
            return '255' . substr($phone, 1);
        } elseif (strlen($phone) === 9) {
            // 123456789 -> 255123456789
            return '255' . $phone;
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '255') {
            // 255123456789 -> keep as is
            return $phone;
        } elseif (strlen($phone) === 13 && substr($phone, 0, 4) === '+255') {
            // +255123456789 -> 255123456789
            return substr($phone, 1);
        }
        
        return false; // Invalid format
    }
    
    /**
     * Mask phone number for display
     */
    private function mask_phone($phone) {
        if (strlen($phone) >= 8) {
            return substr($phone, 0, 6) . '****' . substr($phone, -2);
        }
        return $phone;
    }
    
    /**
     * Get callback URL
     */
    private function get_callback_url() {
        return add_query_arg(array(
            'action' => 'kilismile_azampay_callback'
        ), admin_url('admin-ajax.php'));
    }
    
    /**
     * Get test endpoint
     */
    protected function get_test_endpoint() {
        return 'https://sandbox.azampay.co.tz';
    }
    
    /**
     * Get live endpoint
     */
    protected function get_live_endpoint() {
        return 'https://checkout.azampay.co.tz';
    }
    
    /**
     * Get API credentials with AzamPay specific fields
     */
    protected function get_api_credentials() {
        $prefix = $this->is_test_mode() ? 'test_' : '';
        
        return array(
            'api_key' => $this->get_setting($prefix . 'api_key'),
            'secret_key' => $this->get_setting($prefix . 'secret_key'),
            'app_name' => $this->get_setting('app_name', 'KiliSmile'),
            'account_number' => $this->get_setting($prefix . 'account_number'),
            'endpoint' => $this->is_test_mode() ? $this->get_test_endpoint() : $this->get_live_endpoint()
        );
    }
    
    /**
     * Process webhook (for backward compatibility)
     */
    public function process_webhook($data) {
        return $this->handle_callback();
    }
    
    /**
     * Validate configuration
     */
    public function is_configured() {
        $credentials = $this->get_api_credentials();
        return !empty($credentials['api_key']) && 
               !empty($credentials['secret_key']) && 
               !empty($credentials['account_number']);
    }
    
    /**
     * Get configuration status
     */
    public function get_configuration_status() {
        if (!$this->is_enabled()) {
            return array(
                'status' => 'disabled',
                'message' => __('AzamPay gateway is disabled', 'kilismile-payments')
            );
        }
        
        if (!$this->is_configured()) {
            return array(
                'status' => 'incomplete',
                'message' => __('AzamPay API credentials not configured', 'kilismile-payments')
            );
        }
        
        return array(
            'status' => 'active',
            'message' => sprintf(
                __('AzamPay is active in %s mode', 'kilismile-payments'),
                $this->is_test_mode() ? __('test', 'kilismile-payments') : __('live', 'kilismile-payments')
            )
        );
    }
}

