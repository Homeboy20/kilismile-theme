<?php
/**
 * Enhanced Selcom Gateway Implementation
 * 
 * Extends the base Selcom gateway with advanced features like
 * retry logic, circuit breaker, and enhanced security.
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
 * Enhanced Selcom Payment Gateway
 */
class KiliSmile_Selcom_Gateway_Enhanced extends KiliSmile_Payment_Gateway_Enhanced {
    
    /**
     * Gateway ID
     */
    protected $id = 'selcom_enhanced';
    
    /**
     * Gateway title
     */
    protected $title = 'Selcom Enhanced';
    
    /**
     * Gateway description
     */
    protected $description = 'Enhanced Selcom Checkout with advanced reliability features';
    
    /**
     * API endpoints for different environments
     */
    protected $api_endpoints = [
        'sandbox' => 'https://checkout.selcommobile.co.tz/api',
        'production' => 'https://checkout.selcom.co.tz/api'
    ];
    
    /**
     * Enhanced constructor with additional initialization
     */
    public function __construct() {
        parent::__construct();
        
        // Set gateway-specific configuration
        $this->retry_attempts = 3;
        $this->circuit_breaker_threshold = 5;
        $this->rate_limit_requests = 50; // Lower limit for Selcom
        
        // Load enhanced settings
        $this->load_enhanced_settings();
        
        // Register enhanced webhook endpoint
        add_action('rest_api_init', [$this, 'register_enhanced_webhook']);
        
        // Add health check endpoint
        add_action('wp_ajax_kilismile_selcom_health', [$this, 'ajax_health_check']);
        add_action('wp_ajax_nopriv_kilismile_selcom_health', [$this, 'ajax_health_check']);
    }
    
    /**
     * Load enhanced gateway settings
     */
    protected function load_enhanced_settings() {
        $this->settings = wp_parse_args(get_option('kilismile_selcom_enhanced_settings', []), [
            'enabled' => false,
            'sandbox_mode' => true,
            'api_key' => '',
            'api_secret' => '',
            'vendor_id' => '',
            'webhook_secret' => '',
            'order_expiry_minutes' => 30,
            'auto_capture' => true,
            'enable_installments' => false,
            'supported_currencies' => ['TZS', 'USD'],
            'min_amount_tzs' => 1000,
            'max_amount_tzs' => 50000000,
            'min_amount_usd' => 1,
            'max_amount_usd' => 25000
        ]);
        
        // Set API credentials
        $this->api_key = $this->settings['api_key'];
        $this->api_secret = $this->settings['api_secret'];
        $this->vendor_id = $this->settings['vendor_id'];
        
        // Set API URL based on mode
        $mode = $this->settings['sandbox_mode'] ? 'sandbox' : 'production';
        $this->api_url = $this->api_endpoints[$mode];
    }
    
    /**
     * Enhanced payment processing with comprehensive error handling
     */
    public function process_payment($donation_id, $payment_data) {
        try {
            // Validate input data
            $validation_result = $this->validate_payment_data($payment_data);
            if (!$validation_result['valid']) {
                return [
                    'success' => false,
                    'message' => $validation_result['message'],
                    'error_code' => 'VALIDATION_FAILED'
                ];
            }
            
            // Get donation details with enhanced error handling
            $donation = $this->get_donation_safely($donation_id);
            if (!$donation) {
                return [
                    'success' => false,
                    'message' => 'Donation record not found',
                    'error_code' => 'DONATION_NOT_FOUND'
                ];
            }
            
            // Generate unique order ID with additional entropy
            $order_id = $this->generate_order_id();
            
            // Prepare enhanced order data
            $order_data = $this->prepare_order_data($donation, $payment_data, $order_id);
            
            // Create Selcom order with retry logic
            $response = $this->create_selcom_order_enhanced($order_data);
            
            if (!$response || !isset($response['data']['checkout_url'])) {
                throw new Exception('Failed to create Selcom order: ' . ($response['message'] ?? 'Unknown error'));
            }
            
            // Store transaction with enhanced metadata
            $this->store_transaction_enhanced($donation_id, $order_id, $payment_data, $response);
            
            // Log successful order creation
            $this->log("Selcom order created successfully", 'info', [
                'order_id' => $order_id,
                'donation_id' => $donation_id,
                'amount' => $payment_data['amount'],
                'currency' => $payment_data['currency']
            ]);
            
            return [
                'success' => true,
                'redirect' => $response['data']['checkout_url'],
                'transaction_id' => $order_id,
                'expires_at' => $response['data']['expires_at'] ?? null
            ];
            
        } catch (Exception $e) {
            $this->log("Payment processing failed: " . $e->getMessage(), 'error', [
                'donation_id' => $donation_id,
                'payment_data' => $payment_data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'error_code' => 'PROCESSING_ERROR'
            ];
        }
    }
    
    /**
     * Enhanced payment data validation
     */
    protected function validate_payment_data($payment_data) {
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
        $currency = strtoupper($payment_data['currency']);
        
        if ($amount <= 0) {
            return [
                'valid' => false,
                'message' => 'Amount must be greater than zero'
            ];
        }
        
        // Check currency support
        if (!in_array($currency, $this->settings['supported_currencies'])) {
            return [
                'valid' => false,
                'message' => "Unsupported currency: {$currency}"
            ];
        }
        
        // Check amount limits
        $min_key = 'min_amount_' . strtolower($currency);
        $max_key = 'max_amount_' . strtolower($currency);
        
        $min_amount = $this->settings[$min_key] ?? 0;
        $max_amount = $this->settings[$max_key] ?? PHP_INT_MAX;
        
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
        
        return ['valid' => true];
    }
    
    /**
     * Safely retrieve donation with error handling
     */
    protected function get_donation_safely($donation_id) {
        try {
            $donation = KiliSmile_Donation_DB::get_donation_by_donation_id($donation_id);
            
            if (!$donation) {
                $this->log("Donation not found: {$donation_id}", 'warning');
                return null;
            }
            
            return $donation;
            
        } catch (Exception $e) {
            $this->log("Error retrieving donation: " . $e->getMessage(), 'error');
            return null;
        }
    }
    
    /**
     * Generate unique order ID with enhanced entropy
     */
    protected function generate_order_id() {
        $timestamp = time();
        $random = mt_rand(10000, 99999);
        $checksum = substr(md5($this->vendor_id . $timestamp . $random), 0, 4);
        
        return "SC{$timestamp}{$random}{$checksum}";
    }
    
    /**
     * Prepare enhanced order data with additional fields
     */
    protected function prepare_order_data($donation, $payment_data, $order_id) {
        // Calculate expiry time
        $expiry_minutes = $this->settings['order_expiry_minutes'];
        $expiry_time = date('Y-m-d H:i:s', strtotime("+{$expiry_minutes} minutes"));
        
        // Prepare URLs with enhanced tracking
        $base_url = home_url('/donation-confirmation/');
        $return_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'gateway' => $this->id,
            'action' => 'return',
            'order_id' => $order_id
        ], $base_url);
        
        $cancel_url = add_query_arg([
            'donation_id' => $donation->donation_id,
            'gateway' => $this->id,
            'action' => 'cancel',
            'order_id' => $order_id
        ], $base_url);
        
        $webhook_url = rest_url('kilismile/v1/selcom-enhanced-webhook');
        
        // Prepare order items with detailed description
        $order_items = [
            [
                'name' => 'Donation to Kilismile Organization',
                'description' => $this->get_donation_description($donation, $payment_data),
                'quantity' => 1,
                'unit_price' => number_format($payment_data['amount'], 2, '.', ''),
                'amount' => number_format($payment_data['amount'], 2, '.', '')
            ]
        ];
        
        // Build order data
        $order_data = [
            'vendor' => $this->vendor_id,
            'order_id' => $order_id,
            'buyer_email' => $donation->email,
            'buyer_name' => trim($donation->first_name . ' ' . $donation->last_name),
            'buyer_phone' => $donation->phone ?: '',
            'amount' => number_format($payment_data['amount'], 2, '.', ''),
            'currency' => $payment_data['currency'],
            'no_of_items' => count($order_items),
            'order_items' => $order_items,
            'redirect_url' => $return_url,
            'cancel_url' => $cancel_url,
            'webhook' => $webhook_url,
            'expiry_date' => $expiry_time,
            'payment_options' => $this->get_payment_options($payment_data),
            'metadata' => [
                'donation_id' => $donation->donation_id,
                'donor_type' => $donation->is_anonymous ? 'anonymous' : 'named',
                'source' => 'kilismile_website'
            ]
        ];
        
        return $order_data;
    }
    
    /**
     * Get donation description for order
     */
    protected function get_donation_description($donation, $payment_data) {
        $purpose = $payment_data['purpose'] ?? 'general';
        
        $descriptions = [
            'general' => 'General donation to support our health programs',
            'education' => 'Health education program support',
            'equipment' => 'Medical equipment funding',
            'outreach' => 'Community outreach program support'
        ];
        
        return $descriptions[$purpose] ?? $descriptions['general'];
    }
    
    /**
     * Get payment options based on amount and currency
     */
    protected function get_payment_options($payment_data) {
        $options = ['CARD', 'MOBILEMONEY'];
        
        // Add bank transfer for larger amounts
        if ($payment_data['amount'] >= 100000) {
            $options[] = 'BANK';
        }
        
        // Add installments if enabled and amount qualifies
        if ($this->settings['enable_installments'] && $payment_data['amount'] >= 50000) {
            $options[] = 'INSTALLMENTS';
        }
        
        return implode(',', $options);
    }
    
    /**
     * Enhanced Selcom order creation with retry logic
     */
    protected function create_selcom_order_enhanced($order_data) {
        $max_retries = 3;
        $retry_delay = 1; // Start with 1 second
        
        for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
            try {
                $this->log("Creating Selcom order (attempt {$attempt})", 'info', [
                    'order_id' => $order_data['order_id']
                ]);
                
                $response = $this->make_api_request('/v1/checkout/create-order', $order_data);
                
                if ($response && isset($response['data']['checkout_url'])) {
                    return $response;
                }
                
                throw new Exception('Invalid response from Selcom API');
                
            } catch (Exception $e) {
                $this->log("Selcom order creation failed (attempt {$attempt}): " . $e->getMessage(), 'warning');
                
                if ($attempt < $max_retries) {
                    sleep($retry_delay);
                    $retry_delay *= 2; // Exponential backoff
                } else {
                    throw $e;
                }
            }
        }
        
        throw new Exception('Failed to create Selcom order after all retry attempts');
    }
    
    /**
     * Enhanced API request with comprehensive error handling
     */
    protected function make_api_request($endpoint, $data, $method = 'POST') {
        $url = $this->api_url . $endpoint;
        $headers = $this->get_enhanced_headers($data);
        
        $args = [
            'method' => $method,
            'headers' => $headers,
            'body' => json_encode($data),
            'timeout' => 30,
            'sslverify' => !$this->settings['sandbox_mode']
        ];
        
        $this->log("Making API request to: {$endpoint}", 'debug', [
            'method' => $method,
            'data_keys' => array_keys($data)
        ]);
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception("API request failed: " . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        $this->log("API response received", 'debug', [
            'status_code' => $status_code,
            'body_length' => strlen($body)
        ]);
        
        if ($status_code < 200 || $status_code >= 300) {
            throw new Exception("API returned error status: {$status_code}. Body: {$body}");
        }
        
        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        return $decoded;
    }
    
    /**
     * Get enhanced headers with additional security
     */
    protected function get_enhanced_headers($data) {
        $timestamp = time();
        $nonce = $this->generate_nonce();
        
        // Create signature with request body
        $signature_data = $this->vendor_id . $nonce . $timestamp . json_encode($data);
        $signature = hash_hmac('sha256', $signature_data, $this->api_secret);
        
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'SELCOM ' . $this->api_key,
            'Digest-Method' => 'HS256',
            'Timestamp' => $timestamp,
            'Nonce' => $nonce,
            'Signature' => $signature,
            'User-Agent' => 'KiliSmile-Enhanced/2.0.0',
            'Accept' => 'application/json'
        ];
    }
    
    /**
     * Store transaction with enhanced metadata
     */
    protected function store_transaction_enhanced($donation_id, $order_id, $payment_data, $response) {
        $transaction_data = [
            'donation_id' => $donation_id,
            'transaction_id' => $order_id,
            'payment_method' => $this->id,
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'status' => 'pending',
            'gateway_response' => $response,
            'metadata' => [
                'gateway_version' => '2.0.0',
                'api_version' => 'v1',
                'created_via' => 'enhanced_gateway',
                'expires_at' => $response['data']['expires_at'] ?? null,
                'payment_options' => $this->get_payment_options($payment_data)
            ]
        ];
        
        KiliSmile_Donation_DB::insert_transaction($transaction_data);
    }
    
    /**
     * Enhanced webhook handling with signature verification
     */
    public function register_enhanced_webhook() {
        register_rest_route('kilismile/v1', '/selcom-enhanced-webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_enhanced_webhook'],
            'permission_callback' => '__return_true',
            'args' => [
                'signature' => [
                    'required' => false,
                    'type' => 'string'
                ]
            ]
        ]);
    }
    
    /**
     * Handle enhanced webhook with comprehensive validation
     */
    public function handle_enhanced_webhook($request) {
        try {
            $data = $request->get_json_params();
            $signature = $request->get_header('X-Selcom-Signature') ?: $request->get_param('signature');
            
            $this->log("Webhook received", 'info', [
                'data_keys' => array_keys($data ?: []),
                'has_signature' => !empty($signature)
            ]);
            
            // Validate webhook signature if available
            if (!empty($this->settings['webhook_secret']) && !empty($signature)) {
                if (!$this->validate_webhook_signature($data, $signature)) {
                    $this->log("Webhook signature validation failed", 'error');
                    return new WP_REST_Response(['error' => 'Invalid signature'], 403);
                }
            }
            
            // Process webhook data
            $result = $this->process_webhook_enhanced($data);
            
            if ($result) {
                return new WP_REST_Response(['status' => 'success'], 200);
            } else {
                return new WP_REST_Response(['error' => 'Processing failed'], 400);
            }
            
        } catch (Exception $e) {
            $this->log("Webhook processing error: " . $e->getMessage(), 'error');
            return new WP_REST_Response(['error' => 'Server error'], 500);
        }
    }
    
    /**
     * Enhanced webhook processing
     */
    protected function process_webhook_enhanced($data) {
        if (empty($data['order_id']) || empty($data['transaction_status'])) {
            $this->log('Invalid webhook data: missing required fields', 'error');
            return false;
        }
        
        $order_id = sanitize_text_field($data['order_id']);
        $status = strtolower(sanitize_text_field($data['transaction_status']));
        
        // Get transaction with enhanced error handling
        $transaction = KiliSmile_Donation_DB::get_transaction($order_id);
        if (!$transaction) {
            $this->log("Transaction not found: {$order_id}", 'error');
            return false;
        }
        
        // Map Selcom status to internal status
        $status_map = [
            'success' => 'completed',
            'failed' => 'failed',
            'cancelled' => 'cancelled',
            'pending' => 'pending'
        ];
        
        $new_status = $status_map[$status] ?? 'unknown';
        
        // Update transaction with enhanced metadata
        $update_data = [
            'status' => $new_status,
            'gateway_response' => $data,
            'metadata' => array_merge(
                json_decode($transaction->metadata ?? '{}', true),
                [
                    'webhook_processed_at' => current_time('mysql'),
                    'final_status' => $status,
                    'processing_time' => $data['processing_time'] ?? null
                ]
            )
        ];
        
        KiliSmile_Donation_DB::update_transaction($order_id, $update_data);
        
        // Update donation status
        KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
            'status' => $new_status
        ]);
        
        // Send notification emails for completed donations
        if ($new_status === 'completed') {
            $this->send_donation_confirmation($transaction);
        }
        
        $this->log("Webhook processed successfully", 'info', [
            'order_id' => $order_id,
            'status' => $new_status
        ]);
        
        return true;
    }
    
    /**
     * Send donation confirmation email
     */
    protected function send_donation_confirmation($transaction) {
        try {
            // Get donation details
            $donation = KiliSmile_Donation_DB::get_donation_by_donation_id($transaction->donation_id);
            if (!$donation) {
                return;
            }
            
            // Prepare email data
            $email_data = [
                'donor_name' => trim($donation->first_name . ' ' . $donation->last_name),
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'transaction_id' => $transaction->transaction_id,
                'date' => current_time('F j, Y'),
                'receipt_url' => $this->generate_receipt_url($transaction)
            ];
            
            // Send email (hook into existing email system)
            do_action('kilismile_send_donation_confirmation', $donation->email, $email_data);
            
        } catch (Exception $e) {
            $this->log("Failed to send confirmation email: " . $e->getMessage(), 'warning');
        }
    }
    
    /**
     * Generate receipt URL
     */
    protected function generate_receipt_url($transaction) {
        return add_query_arg([
            'action' => 'download_receipt',
            'transaction_id' => $transaction->transaction_id
        ], home_url('/donation-receipt/'));
    }
    
    /**
     * Gateway-specific health checks
     */
    protected function perform_health_checks(&$health_status) {
        parent::perform_health_checks($health_status);
        
        // Check API credentials
        if (empty($this->api_key) || empty($this->api_secret) || empty($this->vendor_id)) {
            $health_status['status'] = 'misconfigured';
            $health_status['details']['credentials'] = 'Missing API credentials';
            return;
        }
        
        // Test API connectivity
        try {
            $test_response = $this->test_api_connectivity();
            $health_status['details']['api_connectivity'] = $test_response;
            
        } catch (Exception $e) {
            $health_status['status'] = 'unhealthy';
            $health_status['details']['api_connectivity'] = 'Failed: ' . $e->getMessage();
        }
    }
    
    /**
     * Test API connectivity
     */
    protected function test_api_connectivity() {
        // Make a simple API call to test connectivity
        $endpoint = '/v1/checkout/test-connection';
        
        try {
            $response = $this->make_api_request($endpoint, [
                'vendor' => $this->vendor_id,
                'test' => true
            ]);
            
            return 'Connected successfully';
            
        } catch (Exception $e) {
            throw new Exception("API connectivity test failed: " . $e->getMessage());
        }
    }
    
    /**
     * AJAX health check endpoint
     */
    public function ajax_health_check() {
        // Verify permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $health_status = $this->health_check();
        wp_send_json($health_status);
    }
    
    /**
     * Get enhanced settings fields for admin
     */
    public function get_settings_fields() {
        return [
            [
                'id' => 'enabled',
                'title' => 'Enable Enhanced Selcom Gateway',
                'type' => 'checkbox',
                'description' => 'Enable enhanced Selcom payments with advanced features'
            ],
            [
                'id' => 'sandbox_mode',
                'title' => 'Sandbox Mode',
                'type' => 'checkbox',
                'description' => 'Enable sandbox mode for testing'
            ],
            [
                'id' => 'vendor_id',
                'title' => 'Vendor ID',
                'type' => 'text',
                'description' => 'Your Selcom vendor/merchant ID'
            ],
            [
                'id' => 'api_key',
                'title' => 'API Key',
                'type' => 'text',
                'description' => 'Your Selcom API key'
            ],
            [
                'id' => 'api_secret',
                'title' => 'API Secret',
                'type' => 'password',
                'description' => 'Your Selcom API secret key'
            ],
            [
                'id' => 'webhook_secret',
                'title' => 'Webhook Secret',
                'type' => 'password',
                'description' => 'Secret key for webhook signature verification'
            ],
            [
                'id' => 'order_expiry_minutes',
                'title' => 'Order Expiry (Minutes)',
                'type' => 'number',
                'description' => 'How long payment orders remain valid (default: 30 minutes)',
                'default' => 30
            ],
            [
                'id' => 'enable_installments',
                'title' => 'Enable Installments',
                'type' => 'checkbox',
                'description' => 'Allow installment payments for qualifying amounts'
            ]
        ];
    }
}

// Initialize enhanced Selcom gateway
add_action('init', function() {
    if (class_exists('KiliSmile_Donation_Handler')) {
        new KiliSmile_Selcom_Gateway_Enhanced();
    }
});


