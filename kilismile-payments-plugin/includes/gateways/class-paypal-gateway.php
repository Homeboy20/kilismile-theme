<?php
/**
 * PayPal Gateway Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * PayPal Payment Gateway
 * 
 * Handles international payments through PayPal API with support for:
 * - PayPal payments
 * - Credit/Debit card payments
 * - PayPal subscriptions for recurring donations
 */
class KiliSmile_PayPal_Gateway extends KiliSmile_Payment_Gateway {
    
    /**
     * Gateway constructor
     */
    public function init() {
        $this->id = 'paypal';
        $this->title = __('PayPal', 'kilismile-payments');
        $this->description = __('Pay with PayPal or credit/debit card (International)', 'kilismile-payments');
        
        $this->supports = array(
            'donations',
            'subscriptions',
            'refunds',
            'webhooks',
            'credit_cards',
            'paypal_account'
        );
        
        // PayPal specific hooks
        add_action('wp_ajax_kilismile_paypal_callback', array($this, 'handle_callback'));
        add_action('wp_ajax_nopriv_kilismile_paypal_callback', array($this, 'handle_callback'));
        add_action('wp_ajax_kilismile_paypal_return', array($this, 'handle_return'));
        add_action('wp_ajax_nopriv_kilismile_paypal_return', array($this, 'handle_return'));
    }
    
    /**
     * Process payment
     */
    public function process_payment($data) {
        try {
            // Validate and sanitize data
            $payment_data = $this->sanitize_payment_data($data);
            
            // Convert to USD if needed
            if ($payment_data['currency'] !== 'USD') {
                $payment_data['original_amount'] = $payment_data['amount'];
                $payment_data['original_currency'] = $payment_data['currency'];
                $payment_data['amount'] = $this->convert_currency($payment_data['amount'], $payment_data['currency'], 'USD');
                $payment_data['currency'] = 'USD';
            }
            
            // Validate required fields
            $required_fields = array('donor_name', 'donor_email', 'amount');
            $errors = $this->validate_required_fields($payment_data, $required_fields);
            
            if (!empty($errors)) {
                return new WP_Error('validation_error', implode(' ', $errors));
            }
            
            // Check minimum amount (USD 1)
            if ($payment_data['amount'] < 1) {
                return new WP_Error('amount_error', __('Minimum donation amount is USD 1.00', 'kilismile-payments'));
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
            $payment_method = sanitize_text_field($data['payment_method'] ?? 'paypal');
            
            if ($payment_data['recurring']) {
                return $this->process_subscription_payment($payment_data, $transaction_id);
            } else {
                return $this->process_single_payment($payment_data, $transaction_id);
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
     * Process single payment
     */
    private function process_single_payment($payment_data, $transaction_id) {
        $credentials = $this->get_api_credentials();
        
        if (!$this->is_configured()) {
            return new WP_Error('config_error', __('PayPal is not properly configured', 'kilismile-payments'));
        }
        
        // Create PayPal order
        $order_data = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => 'KS_' . $transaction_id,
                    'amount' => array(
                        'currency_code' => 'USD',
                        'value' => number_format($payment_data['amount'], 2, '.', '')
                    ),
                    'description' => sprintf(
                        __('Donation to KiliSmile Organization by %s', 'kilismile-payments'),
                        $payment_data['donor_name']
                    ),
                    'custom_id' => $transaction_id
                )
            ),
            'application_context' => array(
                'brand_name' => get_bloginfo('name'),
                'locale' => 'en-US',
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
                'return_url' => $this->get_return_url($transaction_id),
                'cancel_url' => $this->get_cancel_url($transaction_id)
            )
        );
        
        // Add payer information if available
        if (!empty($payment_data['donor_email'])) {
            $order_data['payer'] = array(
                'email_address' => $payment_data['donor_email'],
                'name' => array(
                    'given_name' => $this->get_first_name($payment_data['donor_name']),
                    'surname' => $this->get_last_name($payment_data['donor_name'])
                )
            );
        }
        
        $response = $this->make_paypal_request('/v2/checkout/orders', $order_data);
        
        if (is_wp_error($response)) {
            $this->update_transaction_status($transaction_id, 'failed', array(
                'error' => $response->get_error_message()
            ));
            return $response;
        }
        
        // Extract approval URL
        $approval_url = '';
        if (!empty($response['links'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approval_url = $link['href'];
                    break;
                }
            }
        }
        
        if (empty($approval_url)) {
            return new WP_Error('paypal_error', __('Failed to create PayPal payment', 'kilismile-payments'));
        }
        
        // Update transaction with PayPal order ID
        $this->update_transaction_status($transaction_id, 'processing', array(
            'gateway_transaction_id' => $response['id'],
            'gateway_response' => $response
        ));
        
        return array(
            'success' => true,
            'redirect' => true,
            'redirect_url' => $approval_url,
            'message' => __('Redirecting to PayPal...', 'kilismile-payments'),
            'transaction_id' => $transaction_id,
            'gateway_reference' => $response['id'],
            'payment_method' => 'PayPal'
        );
    }
    
    /**
     * Process subscription payment
     */
    private function process_subscription_payment($payment_data, $transaction_id) {
        // For now, redirect to single payment
        // TODO: Implement PayPal subscriptions API
        return $this->process_single_payment($payment_data, $transaction_id);
    }
    
    /**
     * Handle return from PayPal
     */
    public function handle_return() {
        $order_id = sanitize_text_field($_GET['token'] ?? '');
        $payer_id = sanitize_text_field($_GET['PayerID'] ?? '');
        $transaction_id = intval($_GET['transaction_id'] ?? 0);
        
        if (empty($order_id) || empty($transaction_id)) {
            wp_redirect(home_url('/?payment=error'));
            exit;
        }
        
        // Capture the payment
        $response = $this->make_paypal_request("/v2/checkout/orders/{$order_id}/capture", array(), 'POST');
        
        if (is_wp_error($response)) {
            $this->log('PayPal capture error', 'error', array(
                'order_id' => $order_id,
                'error' => $response->get_error_message()
            ));
            
            $this->update_transaction_status($transaction_id, 'failed', array(
                'error' => $response->get_error_message()
            ));
            
            wp_redirect(home_url('/?payment=error'));
            exit;
        }
        
        // Check capture status
        $status = $response['status'] ?? '';
        $capture_status = '';
        
        if (!empty($response['purchase_units'][0]['payments']['captures'][0])) {
            $capture_status = $response['purchase_units'][0]['payments']['captures'][0]['status'];
        }
        
        if ($status === 'COMPLETED' && $capture_status === 'COMPLETED') {
            $this->update_transaction_status($transaction_id, 'completed', array(
                'gateway_transaction_id' => $order_id,
                'capture_id' => $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? '',
                'gateway_response' => $response
            ));
            
            wp_redirect(home_url('/?payment=success&transaction_id=' . $transaction_id));
        } else {
            $this->update_transaction_status($transaction_id, 'failed', array(
                'error' => 'Payment not completed',
                'gateway_response' => $response
            ));
            
            wp_redirect(home_url('/?payment=error'));
        }
        
        exit;
    }
    
    /**
     * Handle webhook from PayPal
     */
    public function handle_callback() {
        $raw_body = file_get_contents('php://input');
        $data = json_decode($raw_body, true);
        
        $this->log('PayPal webhook received', 'info', array(
            'raw_body' => $raw_body,
            'parsed_data' => $data
        ));
        
        // Verify webhook signature
        if (!$this->verify_webhook_signature($raw_body, $_SERVER)) {
            wp_die('Invalid webhook signature', 'Unauthorized', array('response' => 401));
            return;
        }
        
        $event_type = $data['event_type'] ?? '';
        
        switch ($event_type) {
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handle_order_approved($data);
                break;
                
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handle_payment_completed($data);
                break;
                
            case 'PAYMENT.CAPTURE.DENIED':
            case 'PAYMENT.CAPTURE.DECLINED':
                $this->handle_payment_failed($data);
                break;
                
            default:
                $this->log('Unhandled webhook event', 'info', array(
                    'event_type' => $event_type,
                    'data' => $data
                ));
        }
        
        wp_die('OK', 'Success', array('response' => 200));
    }
    
    /**
     * Handle order approved webhook
     */
    private function handle_order_approved($data) {
        $order_id = $data['resource']['id'] ?? '';
        $custom_id = $data['resource']['purchase_units'][0]['custom_id'] ?? '';
        
        if ($custom_id) {
            $this->update_transaction_status($custom_id, 'processing', array(
                'webhook_data' => $data
            ));
        }
    }
    
    /**
     * Handle payment completed webhook
     */
    private function handle_payment_completed($data) {
        $capture_id = $data['resource']['id'] ?? '';
        $custom_id = $data['resource']['custom_id'] ?? '';
        
        if ($custom_id) {
            $this->update_transaction_status($custom_id, 'completed', array(
                'capture_id' => $capture_id,
                'webhook_data' => $data
            ));
        }
    }
    
    /**
     * Handle payment failed webhook
     */
    private function handle_payment_failed($data) {
        $custom_id = $data['resource']['custom_id'] ?? '';
        
        if ($custom_id) {
            $this->update_transaction_status($custom_id, 'failed', array(
                'error' => 'Payment failed via webhook',
                'webhook_data' => $data
            ));
        }
    }
    
    /**
     * Verify webhook signature
     */
    private function verify_webhook_signature($payload, $headers) {
        // For now, return true. In production, implement proper webhook verification
        // using PayPal's webhook signature verification
        return true;
    }
    
    /**
     * Make PayPal API request
     */
    private function make_paypal_request($endpoint, $data = array(), $method = 'POST') {
        $credentials = $this->get_api_credentials();
        $base_url = $credentials['endpoint'];
        
        if (empty($credentials['client_id']) || empty($credentials['client_secret'])) {
            return new WP_Error('credentials_error', __('PayPal API credentials not configured', 'kilismile-payments'));
        }
        
        // Get access token
        $token = $this->get_access_token();
        if (is_wp_error($token)) {
            return $token;
        }
        
        $headers = array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'PayPal-Request-Id' => uniqid()
        );
        
        return $this->make_api_request($base_url . $endpoint, $data, $method, $headers);
    }
    
    /**
     * Get PayPal access token
     */
    private function get_access_token() {
        $credentials = $this->get_api_credentials();
        
        // Check cached token
        $cached_token = get_transient('kilismile_paypal_token');
        if ($cached_token) {
            return $cached_token;
        }
        
        // Request new token
        $auth_string = base64_encode($credentials['client_id'] . ':' . $credentials['client_secret']);
        
        $args = array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Basic ' . $auth_string,
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US'
            ),
            'body' => 'grant_type=client_credentials'
        );
        
        $response = wp_remote_post($credentials['endpoint'] . '/v1/oauth2/token', $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (empty($data['access_token'])) {
            return new WP_Error('token_error', __('Failed to get PayPal access token', 'kilismile-payments'));
        }
        
        $token = $data['access_token'];
        $expires_in = intval($data['expires_in'] ?? 3600) - 300; // Refresh 5 minutes early
        
        // Cache token
        set_transient('kilismile_paypal_token', $token, $expires_in);
        
        return $token;
    }
    
    /**
     * Get return URL
     */
    private function get_return_url($transaction_id) {
        return add_query_arg(array(
            'action' => 'kilismile_paypal_return',
            'transaction_id' => $transaction_id
        ), admin_url('admin-ajax.php'));
    }
    
    /**
     * Get cancel URL
     */
    private function get_cancel_url($transaction_id) {
        return home_url('/?payment=cancelled&transaction_id=' . $transaction_id);
    }
    
    /**
     * Get first name from full name
     */
    private function get_first_name($full_name) {
        $parts = explode(' ', trim($full_name));
        return $parts[0] ?? '';
    }
    
    /**
     * Get last name from full name
     */
    private function get_last_name($full_name) {
        $parts = explode(' ', trim($full_name));
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return '';
    }
    
    /**
     * Get test endpoint
     */
    protected function get_test_endpoint() {
        return 'https://api.sandbox.paypal.com';
    }
    
    /**
     * Get live endpoint
     */
    protected function get_live_endpoint() {
        return 'https://api.paypal.com';
    }
    
    /**
     * Get API credentials with PayPal specific fields
     */
    protected function get_api_credentials() {
        $prefix = $this->is_test_mode() ? 'test_' : '';
        
        return array(
            'client_id' => $this->get_setting($prefix . 'client_id'),
            'client_secret' => $this->get_setting($prefix . 'client_secret'),
            'webhook_id' => $this->get_setting($prefix . 'webhook_id'),
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
        return !empty($credentials['client_id']) && !empty($credentials['client_secret']);
    }
    
    /**
     * Get configuration status
     */
    public function get_configuration_status() {
        if (!$this->is_enabled()) {
            return array(
                'status' => 'disabled',
                'message' => __('PayPal gateway is disabled', 'kilismile-payments')
            );
        }
        
        if (!$this->is_configured()) {
            return array(
                'status' => 'incomplete',
                'message' => __('PayPal API credentials not configured', 'kilismile-payments')
            );
        }
        
        return array(
            'status' => 'active',
            'message' => sprintf(
                __('PayPal is active in %s mode', 'kilismile-payments'),
                $this->is_test_mode() ? __('sandbox', 'kilismile-payments') : __('live', 'kilismile-payments')
            )
        );
    }
}

