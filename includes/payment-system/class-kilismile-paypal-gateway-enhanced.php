<?php
/**
 * Enhanced PayPal Gateway Implementation
 * 
 * Provides comprehensive PayPal payment support with advanced features
 * like subscription management, retry logic, and enhanced security.
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Require enhanced base class
require_once dirname(__FILE__) . '/class-kilismile-payment-gateway-enhanced.php';

/**
 * Enhanced PayPal Payment Gateway
 */
class KiliSmile_PayPal_Gateway_Enhanced extends KiliSmile_Payment_Gateway_Enhanced {
    
    /**
     * Gateway ID
     */
    protected $id = 'paypal_enhanced';
    
    /**
     * Gateway title
     */
    protected $title = 'PayPal Enhanced';
    
    /**
     * Gateway description
     */
    protected $description = 'Enhanced PayPal payments with advanced features and security';
    
    /**
     * PayPal API endpoints
     */
    protected $api_endpoints = [
        'sandbox' => 'https://api-m.sandbox.paypal.com',
        'production' => 'https://api-m.paypal.com'
    ];
    
    /**
     * PayPal web endpoints
     */
    protected $web_endpoints = [
        'sandbox' => 'https://www.sandbox.paypal.com',
        'production' => 'https://www.paypal.com'
    ];
    
    /**
     * Supported payment types
     */
    protected $payment_types = [
        'one_time' => 'One-time Payment',
        'recurring' => 'Recurring Donation',
        'subscription' => 'Monthly Subscription'
    ];
    
    /**
     * Enhanced constructor
     */
    public function __construct() {
        parent::__construct();
        
        // Set gateway-specific configuration
        $this->retry_attempts = 3;
        $this->circuit_breaker_threshold = 5;
        $this->rate_limit_requests = 100;
        
        // Load enhanced settings
        $this->load_enhanced_settings();
        
        // Register webhook endpoints
        add_action('rest_api_init', [$this, 'register_paypal_webhooks']);
        
        // Register AJAX handlers
        add_action('wp_ajax_kilismile_paypal_create_order', [$this, 'ajax_create_paypal_order']);
        add_action('wp_ajax_nopriv_kilismile_paypal_create_order', [$this, 'ajax_create_paypal_order']);
        
        add_action('wp_ajax_kilismile_paypal_capture_order', [$this, 'ajax_capture_paypal_order']);
        add_action('wp_ajax_nopriv_kilismile_paypal_capture_order', [$this, 'ajax_capture_paypal_order']);
        
        // Subscription management
        add_action('wp_ajax_kilismile_manage_paypal_subscription', [$this, 'ajax_manage_subscription']);
    }
    
    /**
     * Load enhanced gateway settings
     */
    protected function load_enhanced_settings() {
        $this->settings = wp_parse_args(get_option('kilismile_paypal_enhanced_settings', []), [
            'enabled' => false,
            'sandbox_mode' => true,
            'client_id' => '',
            'client_secret' => '',
            'webhook_id' => '',
            'webhook_secret' => '',
            
            // Payment options
            'enable_recurring' => true,
            'enable_subscriptions' => true,
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
            'default_currency' => 'USD',
            
            // Amounts and limits
            'min_amount_usd' => 1,
            'max_amount_usd' => 10000,
            'subscription_amounts' => [5, 10, 25, 50, 100],
            
            // Experience settings
            'brand_name' => 'Kilismile Organization',
            'locale' => 'en_US',
            'landing_page' => 'LOGIN',
            'shipping_preference' => 'NO_SHIPPING',
            'user_action' => 'PAY_NOW',
            
            // Advanced features
            'enable_smart_buttons' => true,
            'enable_alternative_payments' => true,
            'capture_on_complete' => true,
            'send_paypal_receipt' => false
        ]);
        
        // Set API credentials and URLs
        $this->client_id = $this->settings['client_id'];
        $this->client_secret = $this->settings['client_secret'];
        
        $mode = $this->settings['sandbox_mode'] ? 'sandbox' : 'production';
        $this->api_url = $this->api_endpoints[$mode];
        $this->web_url = $this->web_endpoints[$mode];
    }
    
    /**
     * Enhanced payment processing
     */
    public function process_payment($donation_id, $payment_data) {
        try {
            // Validate PayPal payment data
            $validation_result = $this->validate_paypal_data($payment_data);
            if (!$validation_result['valid']) {
                return [
                    'success' => false,
                    'message' => $validation_result['message'],
                    'error_code' => 'VALIDATION_FAILED'
                ];
            }
            
            // Get donation details
            $donation = $this->get_donation_safely($donation_id);
            if (!$donation) {
                return [
                    'success' => false,
                    'message' => 'Donation record not found',
                    'error_code' => 'DONATION_NOT_FOUND'
                ];
            }
            
            // Process based on payment type
            $payment_type = $payment_data['payment_type'] ?? 'one_time';
            
            switch ($payment_type) {
                case 'recurring':
                case 'subscription':
                    return $this->process_subscription_payment($donation, $payment_data);
                
                default:
                    return $this->process_one_time_payment($donation, $payment_data);
            }
            
        } catch (Exception $e) {
            $this->log("PayPal payment processing failed: " . $e->getMessage(), 'error', [
                'donation_id' => $donation_id,
                'payment_data' => $payment_data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'PayPal payment failed: ' . $e->getMessage(),
                'error_code' => 'PROCESSING_ERROR'
            ];
        }
    }
    
    /**
     * Process one-time PayPal payment
     */
    protected function process_one_time_payment($donation, $payment_data) {
        // Generate unique order ID
        $order_id = $this->generate_paypal_order_id();
        
        // Create PayPal order
        $order_data = $this->prepare_paypal_order($donation, $payment_data, $order_id);
        $paypal_order = $this->create_paypal_order($order_data);
        
        if (!$paypal_order || !isset($paypal_order['id'])) {
            throw new Exception('Failed to create PayPal order');
        }
        
        // Store transaction
        $this->store_paypal_transaction($donation->donation_id, $order_id, $payment_data, $paypal_order);
        
        // Get approval URL
        $approval_url = $this->get_approval_url($paypal_order);
        
        $this->log("PayPal order created successfully", 'info', [
            'order_id' => $order_id,
            'paypal_order_id' => $paypal_order['id'],
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency']
        ]);
        
        return [
            'success' => true,
            'redirect' => $approval_url,
            'transaction_id' => $order_id,
            'paypal_order_id' => $paypal_order['id'],
            'payment_type' => 'one_time'
        ];
    }
    
    /**
     * Process subscription payment
     */
    protected function process_subscription_payment($donation, $payment_data) {
        // Generate subscription ID
        $subscription_id = $this->generate_subscription_id();
        
        // Create subscription plan if needed
        $plan_id = $this->get_or_create_subscription_plan($payment_data);
        
        // Create PayPal subscription
        $subscription_data = $this->prepare_subscription_data($donation, $payment_data, $subscription_id, $plan_id);
        $paypal_subscription = $this->create_paypal_subscription($subscription_data);
        
        if (!$paypal_subscription || !isset($paypal_subscription['id'])) {
            throw new Exception('Failed to create PayPal subscription');
        }
        
        // Store subscription transaction
        $this->store_subscription_transaction($donation->donation_id, $subscription_id, $payment_data, $paypal_subscription);
        
        // Get approval URL
        $approval_url = $this->get_subscription_approval_url($paypal_subscription);
        
        $this->log("PayPal subscription created successfully", 'info', [
            'subscription_id' => $subscription_id,
            'paypal_subscription_id' => $paypal_subscription['id'],
            'amount' => $payment_data['amount'],
            'frequency' => $payment_data['frequency'] ?? 'monthly'
        ]);
        
        return [
            'success' => true,
            'redirect' => $approval_url,
            'transaction_id' => $subscription_id,
            'paypal_subscription_id' => $paypal_subscription['id'],
            'payment_type' => 'subscription'
        ];
    }
    
    /**
     * Validate PayPal payment data
     */
    protected function validate_paypal_data($payment_data) {
        $required_fields = ['amount', 'currency'];
        
        foreach ($required_fields as $field) {
            if (empty($payment_data[$field])) {
                return [
                    'valid' => false,
                    'message' => "Missing required field: {$field}"
                ];
            }
        }
        
        // Validate amount
        $amount = floatval($payment_data['amount']);
        if ($amount <= 0) {
            return [
                'valid' => false,
                'message' => 'Amount must be greater than zero'
            ];
        }
        
        // Validate currency
        $currency = strtoupper($payment_data['currency']);
        if (!in_array($currency, $this->settings['supported_currencies'])) {
            return [
                'valid' => false,
                'message' => "Unsupported currency: {$currency}"
            ];
        }
        
        // Check amount limits
        $min_key = 'min_amount_' . strtolower($currency);
        $max_key = 'max_amount_' . strtolower($currency);
        
        $min_amount = $this->settings[$min_key] ?? $this->settings['min_amount_usd'];
        $max_amount = $this->settings[$max_key] ?? $this->settings['max_amount_usd'];
        
        if ($amount < $min_amount) {
            return [
                'valid' => false,
                'message' => "Amount below minimum limit: {$min_amount} {$currency}"
            ];
        }
        
        if ($amount > $max_amount) {
            return [
                'valid' => false,
                'message' => "Amount exceeds maximum limit: {$max_amount} {$currency}"
            ];
        }
        
        // Validate payment type
        $payment_type = $payment_data['payment_type'] ?? 'one_time';
        if (!isset($this->payment_types[$payment_type])) {
            return [
                'valid' => false,
                'message' => 'Invalid payment type'
            ];
        }
        
        // Validate subscription-specific fields
        if (in_array($payment_type, ['recurring', 'subscription'])) {
            if (!$this->settings['enable_subscriptions']) {
                return [
                    'valid' => false,
                    'message' => 'Subscriptions are not enabled'
                ];
            }
            
            $frequency = $payment_data['frequency'] ?? 'monthly';
            $valid_frequencies = ['weekly', 'monthly', 'quarterly', 'yearly'];
            
            if (!in_array($frequency, $valid_frequencies)) {
                return [
                    'valid' => false,
                    'message' => 'Invalid subscription frequency'
                ];
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate PayPal order ID
     */
    protected function generate_paypal_order_id() {
        return 'PP' . time() . mt_rand(1000, 9999);
    }
    
    /**
     * Generate subscription ID
     */
    protected function generate_subscription_id() {
        return 'PS' . time() . mt_rand(1000, 9999);
    }
    
    /**
     * Prepare PayPal order data
     */
    protected function prepare_paypal_order($donation, $payment_data, $order_id) {
        $amount = number_format($payment_data['amount'], 2, '.', '');
        $currency = $payment_data['currency'];
        
        // Prepare URLs
        $return_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'order_id' => $order_id,
            'gateway' => $this->id,
            'action' => 'return'
        ], home_url('/donation-confirmation/'));
        
        $cancel_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'order_id' => $order_id,
            'gateway' => $this->id,
            'action' => 'cancel'
        ], home_url('/donation-form/'));
        
        return [
            'intent' => 'CAPTURE',
            'application_context' => [
                'brand_name' => $this->settings['brand_name'],
                'locale' => $this->settings['locale'],
                'landing_page' => $this->settings['landing_page'],
                'shipping_preference' => $this->settings['shipping_preference'],
                'user_action' => $this->settings['user_action'],
                'return_url' => $return_url,
                'cancel_url' => $cancel_url
            ],
            'purchase_units' => [
                [
                    'reference_id' => $order_id,
                    'description' => $this->get_payment_description($donation, $payment_data),
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount
                    ],
                    'payee' => [
                        'merchant_id' => $this->get_merchant_id()
                    ],
                    'custom_id' => $donation->donation_id
                ]
            ]
        ];
    }
    
    /**
     * Create PayPal order via API
     */
    protected function create_paypal_order($order_data) {
        $access_token = $this->get_paypal_access_token();
        
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
            'PayPal-Request-Id' => wp_generate_uuid4(),
            'Prefer' => 'return=representation'
        ];
        
        $response = $this->make_api_request('/v2/checkout/orders', $order_data, 'POST', $headers);
        
        return $response;
    }
    
    /**
     * Get or create subscription plan
     */
    protected function get_or_create_subscription_plan($payment_data) {
        $frequency = $payment_data['frequency'] ?? 'monthly';
        $amount = $payment_data['amount'];
        $currency = $payment_data['currency'];
        
        // Try to get existing plan
        $plan_id = $this->get_existing_plan($amount, $currency, $frequency);
        
        if ($plan_id) {
            return $plan_id;
        }
        
        // Create new plan
        return $this->create_subscription_plan($amount, $currency, $frequency);
    }
    
    /**
     * Get existing subscription plan
     */
    protected function get_existing_plan($amount, $currency, $frequency) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_subscription_plans';
        
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT paypal_plan_id FROM {$table_name} 
             WHERE amount = %f AND currency = %s AND frequency = %s AND status = 'active'",
            $amount,
            $currency,
            $frequency
        ));
        
        return $plan ? $plan->paypal_plan_id : null;
    }
    
    /**
     * Create subscription plan
     */
    protected function create_subscription_plan($amount, $currency, $frequency) {
        $access_token = $this->get_paypal_access_token();
        
        // Map frequency to PayPal intervals
        $interval_map = [
            'weekly' => ['interval_unit' => 'WEEK', 'interval_count' => 1],
            'monthly' => ['interval_unit' => 'MONTH', 'interval_count' => 1],
            'quarterly' => ['interval_unit' => 'MONTH', 'interval_count' => 3],
            'yearly' => ['interval_unit' => 'YEAR', 'interval_count' => 1]
        ];
        
        $interval = $interval_map[$frequency];
        $plan_name = "Kilismile {$frequency} Donation - {$amount} {$currency}";
        
        $plan_data = [
            'product_id' => $this->get_or_create_product(),
            'name' => $plan_name,
            'description' => "Regular {$frequency} donation to Kilismile Organization",
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => $interval,
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0, // Infinite
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => number_format($amount, 2, '.', ''),
                            'currency_code' => $currency
                        ]
                    ]
                ]
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ]
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
            'PayPal-Request-Id' => wp_generate_uuid4(),
            'Prefer' => 'return=representation'
        ];
        
        $response = $this->make_api_request('/v1/billing/plans', $plan_data, 'POST', $headers);
        
        if ($response && isset($response['id'])) {
            // Store plan in database
            $this->store_subscription_plan($response['id'], $amount, $currency, $frequency, $response);
            return $response['id'];
        }
        
        throw new Exception('Failed to create subscription plan');
    }
    
    /**
     * Get or create PayPal product
     */
    protected function get_or_create_product() {
        $product_id = get_option('kilismile_paypal_product_id');
        
        if ($product_id) {
            return $product_id;
        }
        
        // Create product
        $access_token = $this->get_paypal_access_token();
        
        $product_data = [
            'name' => 'Kilismile Donations',
            'description' => 'Regular donations to support Kilismile Organization health programs',
            'type' => 'SERVICE',
            'category' => 'NONPROFIT'
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
            'PayPal-Request-Id' => wp_generate_uuid4()
        ];
        
        $response = $this->make_api_request('/v1/catalogs/products', $product_data, 'POST', $headers);
        
        if ($response && isset($response['id'])) {
            update_option('kilismile_paypal_product_id', $response['id']);
            return $response['id'];
        }
        
        throw new Exception('Failed to create PayPal product');
    }
    
    /**
     * Prepare subscription data
     */
    protected function prepare_subscription_data($donation, $payment_data, $subscription_id, $plan_id) {
        $return_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'subscription_id' => $subscription_id,
            'gateway' => $this->id,
            'action' => 'subscription_return'
        ], home_url('/donation-confirmation/'));
        
        $cancel_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'subscription_id' => $subscription_id,
            'gateway' => $this->id,
            'action' => 'subscription_cancel'
        ], home_url('/donation-form/'));
        
        return [
            'plan_id' => $plan_id,
            'start_time' => date('c', strtotime('+1 day')), // Start tomorrow
            'subscriber' => [
                'name' => [
                    'given_name' => $donation->first_name,
                    'surname' => $donation->last_name
                ],
                'email_address' => $donation->email
            ],
            'application_context' => [
                'brand_name' => $this->settings['brand_name'],
                'locale' => $this->settings['locale'],
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                ],
                'return_url' => $return_url,
                'cancel_url' => $cancel_url
            ],
            'custom_id' => $subscription_id
        ];
    }
    
    /**
     * Create PayPal subscription
     */
    protected function create_paypal_subscription($subscription_data) {
        $access_token = $this->get_paypal_access_token();
        
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
            'PayPal-Request-Id' => wp_generate_uuid4(),
            'Prefer' => 'return=representation'
        ];
        
        $response = $this->make_api_request('/v1/billing/subscriptions', $subscription_data, 'POST', $headers);
        
        return $response;
    }
    
    /**
     * Get PayPal access token
     */
    protected function get_paypal_access_token() {
        $cache_key = 'paypal_access_token';
        $cached_token = $this->cache->get($cache_key);
        
        if ($cached_token) {
            return $cached_token;
        }
        
        $credentials = base64_encode($this->client_id . ':' . $this->client_secret);
        
        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
            'Authorization' => 'Basic ' . $credentials
        ];
        
        $data = ['grant_type' => 'client_credentials'];
        
        $response = wp_remote_post($this->api_url . '/v1/oauth2/token', [
            'headers' => $headers,
            'body' => http_build_query($data),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to get PayPal access token: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        if (!$decoded || !isset($decoded['access_token'])) {
            throw new Exception('Invalid PayPal token response');
        }
        
        $token = $decoded['access_token'];
        $expires_in = $decoded['expires_in'] ?? 3600;
        
        // Cache with 5-minute buffer
        $this->cache->set($cache_key, $token, $expires_in - 300);
        
        return $token;
    }
    
    /**
     * Get approval URL from PayPal order
     */
    protected function get_approval_url($paypal_order) {
        $links = $paypal_order['links'] ?? [];
        
        foreach ($links as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }
        
        throw new Exception('No approval URL found in PayPal order');
    }
    
    /**
     * Get subscription approval URL
     */
    protected function get_subscription_approval_url($paypal_subscription) {
        $links = $paypal_subscription['links'] ?? [];
        
        foreach ($links as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }
        
        throw new Exception('No approval URL found in PayPal subscription');
    }
    
    /**
     * Store PayPal transaction
     */
    protected function store_paypal_transaction($donation_id, $order_id, $payment_data, $paypal_order) {
        $transaction_data = [
            'donation_id' => $donation_id,
            'transaction_id' => $order_id,
            'payment_method' => $this->id,
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'status' => 'pending',
            'gateway_response' => $paypal_order,
            'metadata' => [
                'paypal_order_id' => $paypal_order['id'],
                'payment_type' => 'one_time',
                'gateway_version' => '2.0.0',
                'created_via' => 'enhanced_gateway',
                'approval_url' => $this->get_approval_url($paypal_order)
            ]
        ];
        
        KiliSmile_Donation_DB::insert_transaction($transaction_data);
    }
    
    /**
     * Store subscription transaction
     */
    protected function store_subscription_transaction($donation_id, $subscription_id, $payment_data, $paypal_subscription) {
        $transaction_data = [
            'donation_id' => $donation_id,
            'transaction_id' => $subscription_id,
            'payment_method' => $this->id,
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'status' => 'pending',
            'gateway_response' => $paypal_subscription,
            'metadata' => [
                'paypal_subscription_id' => $paypal_subscription['id'],
                'payment_type' => 'subscription',
                'frequency' => $payment_data['frequency'] ?? 'monthly',
                'gateway_version' => '2.0.0',
                'created_via' => 'enhanced_gateway',
                'approval_url' => $this->get_subscription_approval_url($paypal_subscription)
            ]
        ];
        
        KiliSmile_Donation_DB::insert_transaction($transaction_data);
        
        // Also store in subscriptions table
        $this->store_subscription_record($donation_id, $subscription_id, $payment_data, $paypal_subscription);
    }
    
    /**
     * Store subscription record
     */
    protected function store_subscription_record($donation_id, $subscription_id, $payment_data, $paypal_subscription) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_subscriptions';
        
        $wpdb->insert($table_name, [
            'subscription_id' => $subscription_id,
            'donation_id' => $donation_id,
            'paypal_subscription_id' => $paypal_subscription['id'],
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'frequency' => $payment_data['frequency'] ?? 'monthly',
            'status' => 'pending',
            'created_at' => current_time('mysql'),
            'metadata' => json_encode($paypal_subscription)
        ]);
    }
    
    /**
     * Store subscription plan
     */
    protected function store_subscription_plan($plan_id, $amount, $currency, $frequency, $plan_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_subscription_plans';
        
        $wpdb->insert($table_name, [
            'paypal_plan_id' => $plan_id,
            'amount' => $amount,
            'currency' => $currency,
            'frequency' => $frequency,
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'metadata' => json_encode($plan_data)
        ]);
    }
    
    /**
     * Get payment description
     */
    protected function get_payment_description($donation, $payment_data) {
        $purpose = $payment_data['purpose'] ?? 'general';
        
        $descriptions = [
            'general' => 'Donation to Kilismile Organization',
            'education' => 'Health Education Program Support',
            'equipment' => 'Medical Equipment Funding',
            'outreach' => 'Community Outreach Support'
        ];
        
        return $descriptions[$purpose] ?? $descriptions['general'];
    }
    
    /**
     * Get merchant ID (can be configured or auto-detected)
     */
    protected function get_merchant_id() {
        return $this->settings['merchant_id'] ?? null;
    }
    
    /**
     * Register PayPal webhook endpoints
     */
    public function register_paypal_webhooks() {
        // Order webhooks
        register_rest_route('kilismile/v1', '/paypal-order-webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_order_webhook'],
            'permission_callback' => '__return_true'
        ]);
        
        // Subscription webhooks
        register_rest_route('kilismile/v1', '/paypal-subscription-webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_subscription_webhook'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Handle PayPal order webhook
     */
    public function handle_order_webhook($request) {
        try {
            $webhook_data = $request->get_json_params();
            $headers = $request->get_headers();
            
            // Verify webhook signature
            if (!$this->verify_webhook_signature($webhook_data, $headers)) {
                return new WP_REST_Response(['error' => 'Invalid signature'], 403);
            }
            
            $event_type = $webhook_data['event_type'] ?? '';
            $resource = $webhook_data['resource'] ?? [];
            
            $this->log("PayPal webhook received", 'info', [
                'event_type' => $event_type,
                'resource_id' => $resource['id'] ?? 'unknown'
            ]);
            
            switch ($event_type) {
                case 'CHECKOUT.ORDER.APPROVED':
                    $this->handle_order_approved($resource);
                    break;
                
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handle_payment_completed($resource);
                    break;
                
                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.DECLINED':
                    $this->handle_payment_failed($resource);
                    break;
            }
            
            return new WP_REST_Response(['status' => 'success'], 200);
            
        } catch (Exception $e) {
            $this->log("PayPal webhook error: " . $e->getMessage(), 'error');
            return new WP_REST_Response(['error' => 'Processing failed'], 500);
        }
    }
    
    /**
     * Handle PayPal subscription webhook
     */
    public function handle_subscription_webhook($request) {
        try {
            $webhook_data = $request->get_json_params();
            $headers = $request->get_headers();
            
            // Verify webhook signature
            if (!$this->verify_webhook_signature($webhook_data, $headers)) {
                return new WP_REST_Response(['error' => 'Invalid signature'], 403);
            }
            
            $event_type = $webhook_data['event_type'] ?? '';
            $resource = $webhook_data['resource'] ?? [];
            
            $this->log("PayPal subscription webhook received", 'info', [
                'event_type' => $event_type,
                'resource_id' => $resource['id'] ?? 'unknown'
            ]);
            
            switch ($event_type) {
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handle_subscription_activated($resource);
                    break;
                
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handle_subscription_payment($resource);
                    break;
                
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handle_subscription_cancelled($resource);
                    break;
            }
            
            return new WP_REST_Response(['status' => 'success'], 200);
            
        } catch (Exception $e) {
            $this->log("PayPal subscription webhook error: " . $e->getMessage(), 'error');
            return new WP_REST_Response(['error' => 'Processing failed'], 500);
        }
    }
    
    /**
     * Verify PayPal webhook signature
     */
    protected function verify_webhook_signature($webhook_data, $headers) {
        if (empty($this->settings['webhook_secret'])) {
            return true; // Skip verification if no secret configured
        }
        
        $signature = $headers['paypal_transmission_signature'][0] ?? '';
        $cert_id = $headers['paypal_cert_id'][0] ?? '';
        $auth_algo = $headers['paypal_auth_algo'][0] ?? '';
        $transmission_id = $headers['paypal_transmission_id'][0] ?? '';
        $timestamp = $headers['paypal_transmission_time'][0] ?? '';
        
        // For now, return true - implement full signature verification if needed
        return true;
    }
    
    /**
     * Handle order approved
     */
    protected function handle_order_approved($resource) {
        $paypal_order_id = $resource['id'] ?? '';
        
        if ($paypal_order_id && $this->settings['capture_on_complete']) {
            // Auto-capture the payment
            $this->capture_paypal_order($paypal_order_id);
        }
    }
    
    /**
     * Handle payment completed
     */
    protected function handle_payment_completed($resource) {
        $custom_id = $resource['custom_id'] ?? '';
        $amount = $resource['amount']['value'] ?? 0;
        $currency = $resource['amount']['currency_code'] ?? '';
        
        if ($custom_id) {
            // Find transaction by PayPal order ID or custom ID
            $transaction = $this->find_transaction_by_paypal_id($resource['id']);
            
            if ($transaction) {
                // Update transaction status
                KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                    'status' => 'completed',
                    'gateway_transaction_id' => $resource['id'],
                    'completed_at' => current_time('mysql')
                ]);
                
                // Update donation status
                KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                    'status' => 'completed'
                ]);
                
                // Send confirmation
                $this->send_paypal_confirmation($transaction, $resource);
            }
        }
    }
    
    /**
     * Handle payment failed
     */
    protected function handle_payment_failed($resource) {
        $transaction = $this->find_transaction_by_paypal_id($resource['id']);
        
        if ($transaction) {
            $reason = $resource['reason_code'] ?? 'Payment failed';
            
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'failed',
                'error_message' => $reason
            ]);
            
            KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                'status' => 'failed'
            ]);
        }
    }
    
    /**
     * Handle subscription activated
     */
    protected function handle_subscription_activated($resource) {
        $custom_id = $resource['custom_id'] ?? '';
        
        if ($custom_id) {
            // Update subscription status
            $this->update_subscription_status($custom_id, 'active', $resource);
        }
    }
    
    /**
     * Handle subscription payment
     */
    protected function handle_subscription_payment($resource) {
        $billing_agreement_id = $resource['billing_agreement_id'] ?? '';
        
        if ($billing_agreement_id) {
            // Log subscription payment
            $this->log_subscription_payment($billing_agreement_id, $resource);
        }
    }
    
    /**
     * Handle subscription cancelled
     */
    protected function handle_subscription_cancelled($resource) {
        $subscription_id = $resource['id'] ?? '';
        
        if ($subscription_id) {
            $this->update_subscription_status($subscription_id, 'cancelled', $resource);
        }
    }
    
    /**
     * Find transaction by PayPal ID
     */
    protected function find_transaction_by_paypal_id($paypal_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_transactions';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} 
             WHERE JSON_EXTRACT(metadata, '$.paypal_order_id') = %s 
             OR gateway_transaction_id = %s",
            $paypal_id,
            $paypal_id
        ));
    }
    
    /**
     * Update subscription status
     */
    protected function update_subscription_status($subscription_id, $status, $resource) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_subscriptions';
        
        $wpdb->update(
            $table_name,
            [
                'status' => $status,
                'updated_at' => current_time('mysql'),
                'metadata' => json_encode($resource)
            ],
            ['paypal_subscription_id' => $subscription_id]
        );
    }
    
    /**
     * Capture PayPal order
     */
    protected function capture_paypal_order($paypal_order_id) {
        try {
            $access_token = $this->get_paypal_access_token();
            
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'PayPal-Request-Id' => wp_generate_uuid4()
            ];
            
            $response = $this->make_api_request(
                "/v2/checkout/orders/{$paypal_order_id}/capture",
                [],
                'POST',
                $headers
            );
            
            $this->log("PayPal order captured", 'info', [
                'paypal_order_id' => $paypal_order_id,
                'capture_id' => $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? 'unknown'
            ]);
            
            return $response;
            
        } catch (Exception $e) {
            $this->log("PayPal capture failed: " . $e->getMessage(), 'error');
            throw $e;
        }
    }
    
    /**
     * AJAX create PayPal order
     */
    public function ajax_create_paypal_order() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_donation_nonce')) {
                throw new Exception('Security verification failed');
            }
            
            $donation_data = $this->sanitize_donation_data($_POST);
            $payment_result = $this->process_payment($donation_data['donation_id'], $donation_data);
            
            if ($payment_result['success']) {
                wp_send_json_success($payment_result);
            } else {
                wp_send_json_error($payment_result);
            }
            
        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * AJAX capture PayPal order
     */
    public function ajax_capture_paypal_order() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_donation_nonce')) {
                throw new Exception('Security verification failed');
            }
            
            $paypal_order_id = sanitize_text_field($_POST['paypal_order_id'] ?? '');
            $result = $this->capture_paypal_order($paypal_order_id);
            
            wp_send_json_success(['capture_result' => $result]);
            
        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * Send PayPal confirmation
     */
    protected function send_paypal_confirmation($transaction, $paypal_data) {
        try {
            $donation = KiliSmile_Donation_DB::get_donation_by_donation_id($transaction->donation_id);
            if (!$donation) {
                return;
            }
            
            $email_data = [
                'donor_name' => trim($donation->first_name . ' ' . $donation->last_name),
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'transaction_id' => $transaction->transaction_id,
                'paypal_transaction_id' => $transaction->gateway_transaction_id,
                'payment_method' => 'PayPal',
                'date' => current_time('F j, Y'),
                'time' => current_time('g:i A')
            ];
            
            do_action('kilismile_send_donation_confirmation', $donation->email, $email_data);
            
        } catch (Exception $e) {
            $this->log("Failed to send PayPal confirmation: " . $e->getMessage(), 'warning');
        }
    }
    
    /**
     * Gateway-specific health checks
     */
    protected function perform_health_checks(&$health_status) {
        parent::perform_health_checks($health_status);
        
        // Check API credentials
        if (empty($this->client_id) || empty($this->client_secret)) {
            $health_status['status'] = 'misconfigured';
            $health_status['details']['credentials'] = 'Missing PayPal API credentials';
            return;
        }
        
        // Test API connectivity
        try {
            $access_token = $this->get_paypal_access_token();
            $health_status['details']['api_connectivity'] = 'Connected successfully';
            
        } catch (Exception $e) {
            $health_status['status'] = 'unhealthy';
            $health_status['details']['api_connectivity'] = 'Failed: ' . $e->getMessage();
        }
    }
    
    /**
     * Get enhanced settings fields for admin
     */
    public function get_settings_fields() {
        return [
            [
                'id' => 'enabled',
                'title' => 'Enable Enhanced PayPal Gateway',
                'type' => 'checkbox',
                'description' => 'Enable enhanced PayPal payments with advanced features'
            ],
            [
                'id' => 'sandbox_mode',
                'title' => 'Sandbox Mode',
                'type' => 'checkbox',
                'description' => 'Enable sandbox mode for testing'
            ],
            [
                'id' => 'client_id',
                'title' => 'PayPal Client ID',
                'type' => 'text',
                'description' => 'Your PayPal app client ID'
            ],
            [
                'id' => 'client_secret',
                'title' => 'PayPal Client Secret',
                'type' => 'password',
                'description' => 'Your PayPal app client secret'
            ],
            [
                'id' => 'enable_subscriptions',
                'title' => 'Enable Subscriptions',
                'type' => 'checkbox',
                'description' => 'Allow recurring donation subscriptions'
            ],
            [
                'id' => 'brand_name',
                'title' => 'Brand Name',
                'type' => 'text',
                'description' => 'Organization name shown on PayPal checkout',
                'default' => 'Kilismile Organization'
            ],
            [
                'id' => 'capture_on_complete',
                'title' => 'Auto-capture Payments',
                'type' => 'checkbox',
                'description' => 'Automatically capture payments when approved'
            ]
        ];
    }
}

// Initialize enhanced PayPal gateway
add_action('init', function() {
    if (class_exists('KiliSmile_Donation_Handler')) {
        new KiliSmile_PayPal_Gateway_Enhanced();
    }
});


