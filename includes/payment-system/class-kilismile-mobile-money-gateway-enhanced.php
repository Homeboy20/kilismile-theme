<?php
/**
 * Enhanced Mobile Money Gateway Implementation
 * 
 * Provides comprehensive mobile money payment support for Tanzania
 * with advanced features like multi-provider support, retry logic,
 * and enhanced security.
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
 * Enhanced Mobile Money Payment Gateway
 */
class KiliSmile_Mobile_Money_Gateway_Enhanced extends KiliSmile_Payment_Gateway_Enhanced {
    
    /**
     * Gateway ID
     */
    protected $id = 'mobile_money_enhanced';
    
    /**
     * Gateway title
     */
    protected $title = 'Mobile Money Enhanced';
    
    /**
     * Gateway description
     */
    protected $description = 'Enhanced Mobile Money payments with multi-provider support';
    
    /**
     * Supported mobile money providers
     */
    protected $providers = [
        'mpesa' => [
            'name' => 'M-Pesa',
            'code' => 'MPESA',
            'pattern' => '/^(255)?0?[67][0-9]{8}$/',
            'format' => '255XXXXXXXXX',
            'min_amount' => 1000,
            'max_amount' => 5000000
        ],
        'tigo_pesa' => [
            'name' => 'Tigo Pesa',
            'code' => 'TIGOPESA',
            'pattern' => '/^(255)?0?[67][0-9]{8}$/',
            'format' => '255XXXXXXXXX',
            'min_amount' => 500,
            'max_amount' => 3000000
        ],
        'airtel_money' => [
            'name' => 'Airtel Money',
            'code' => 'AIRTELMONEY',
            'pattern' => '/^(255)?0?[67][0-9]{8}$/',
            'format' => '255XXXXXXXXX',
            'min_amount' => 1000,
            'max_amount' => 2000000
        ],
        'halopesa' => [
            'name' => 'HaloPesa',
            'code' => 'HALOPESA',
            'pattern' => '/^(255)?0?[67][0-9]{8}$/',
            'format' => '255XXXXXXXXX',
            'min_amount' => 1000,
            'max_amount' => 1000000
        ]
    ];
    
    /**
     * Enhanced constructor
     */
    public function __construct() {
        parent::__construct();
        
        // Set gateway-specific configuration
        $this->retry_attempts = 5; // Higher retry for mobile money
        $this->circuit_breaker_threshold = 3;
        $this->rate_limit_requests = 30; // Conservative limit
        
        // Load enhanced settings
        $this->load_enhanced_settings();
        
        // Register AJAX handlers
        add_action('wp_ajax_kilismile_validate_mobile_number', [$this, 'ajax_validate_mobile_number']);
        add_action('wp_ajax_nopriv_kilismile_validate_mobile_number', [$this, 'ajax_validate_mobile_number']);
        
        add_action('wp_ajax_kilismile_check_mobile_money_status', [$this, 'ajax_check_payment_status']);
        add_action('wp_ajax_nopriv_kilismile_check_mobile_money_status', [$this, 'ajax_check_payment_status']);
        
        // Register webhook endpoint
        add_action('rest_api_init', [$this, 'register_mobile_money_webhook']);
    }
    
    /**
     * Load enhanced gateway settings
     */
    protected function load_enhanced_settings() {
        $this->settings = wp_parse_args(get_option('kilismile_mobile_money_enhanced_settings', []), [
            'enabled' => true,
            'sandbox_mode' => true,
            'enabled_providers' => ['mpesa', 'tigo_pesa', 'airtel_money'],
            'auto_detect_provider' => true,
            'transaction_timeout' => 300, // 5 minutes
            'callback_timeout' => 600,    // 10 minutes
            'enable_ussd_fallback' => true,
            'require_confirmation' => true,
            'supported_currencies' => ['TZS'],
            
            // Provider-specific settings
            'mpesa_shortcode' => '',
            'mpesa_api_key' => '',
            'mpesa_secret_key' => '',
            
            'tigo_pesa_biller_code' => '',
            'tigo_pesa_api_key' => '',
            'tigo_pesa_secret_key' => '',
            
            'airtel_money_merchant_id' => '',
            'airtel_money_api_key' => '',
            'airtel_money_secret_key' => ''
        ]);
    }
    
    /**
     * Enhanced payment processing
     */
    public function process_payment($donation_id, $payment_data) {
        try {
            // Validate mobile money data
            $validation_result = $this->validate_mobile_money_data($payment_data);
            if (!$validation_result['valid']) {
                return [
                    'success' => false,
                    'message' => $validation_result['message'],
                    'error_code' => 'VALIDATION_FAILED'
                ];
            }
            
            // Auto-detect provider if enabled
            if ($this->settings['auto_detect_provider']) {
                $provider = $this->detect_mobile_provider($payment_data['mobile_number']);
                if (!$provider) {
                    return [
                        'success' => false,
                        'message' => 'Unable to detect mobile money provider',
                        'error_code' => 'PROVIDER_DETECTION_FAILED'
                    ];
                }
                $payment_data['provider'] = $provider;
            }
            
            // Check if provider is enabled
            if (!in_array($payment_data['provider'], $this->settings['enabled_providers'])) {
                return [
                    'success' => false,
                    'message' => 'Selected mobile money provider is not available',
                    'error_code' => 'PROVIDER_DISABLED'
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
            
            // Generate transaction ID
            $transaction_id = $this->generate_mobile_transaction_id($payment_data['provider']);
            
            // Initiate mobile money payment
            $payment_result = $this->initiate_mobile_payment($donation, $payment_data, $transaction_id);
            
            if (!$payment_result['success']) {
                throw new Exception($payment_result['message']);
            }
            
            // Store transaction with enhanced metadata
            $this->store_mobile_transaction($donation_id, $transaction_id, $payment_data, $payment_result);
            
            // Log successful initiation
            $this->log("Mobile money payment initiated", 'info', [
                'transaction_id' => $transaction_id,
                'provider' => $payment_data['provider'],
                'mobile_number' => $this->mask_mobile_number($payment_data['mobile_number']),
                'amount' => $payment_data['amount']
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $transaction_id,
                'provider' => $payment_data['provider'],
                'mobile_number' => $payment_data['mobile_number'],
                'status_check_url' => $this->get_status_check_url($transaction_id),
                'instructions' => $this->get_payment_instructions($payment_data['provider'], $payment_data),
                'timeout' => $this->settings['transaction_timeout']
            ];
            
        } catch (Exception $e) {
            $this->log("Mobile money payment failed: " . $e->getMessage(), 'error', [
                'donation_id' => $donation_id,
                'payment_data' => $payment_data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Mobile money payment failed: ' . $e->getMessage(),
                'error_code' => 'PROCESSING_ERROR'
            ];
        }
    }
    
    /**
     * Validate mobile money payment data
     */
    protected function validate_mobile_money_data($payment_data) {
        $required_fields = ['amount', 'currency', 'mobile_number'];
        
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
                'message' => "Unsupported currency for mobile money: {$currency}"
            ];
        }
        
        // Validate mobile number format
        $mobile_validation = $this->validate_mobile_number($payment_data['mobile_number']);
        if (!$mobile_validation['valid']) {
            return $mobile_validation;
        }
        
        // Validate amount limits if provider is specified
        if (!empty($payment_data['provider'])) {
            $provider_limits = $this->get_provider_limits($payment_data['provider']);
            if ($amount < $provider_limits['min_amount'] || $amount > $provider_limits['max_amount']) {
                return [
                    'valid' => false,
                    'message' => "Amount must be between {$provider_limits['min_amount']} and {$provider_limits['max_amount']} TZS for {$payment_data['provider']}"
                ];
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate mobile number format
     */
    protected function validate_mobile_number($mobile_number) {
        // Clean the number
        $clean_number = preg_replace('/[^0-9+]/', '', $mobile_number);
        
        // Check if it matches any provider pattern
        foreach ($this->providers as $provider_id => $provider) {
            if (preg_match($provider['pattern'], $clean_number)) {
                return [
                    'valid' => true,
                    'provider' => $provider_id,
                    'formatted_number' => $this->format_mobile_number($clean_number, $provider)
                ];
            }
        }
        
        return [
            'valid' => false,
            'message' => 'Invalid mobile number format. Please use a valid Tanzanian mobile number.'
        ];
    }
    
    /**
     * Format mobile number according to provider requirements
     */
    protected function format_mobile_number($number, $provider) {
        // Remove country code if present
        $clean = preg_replace('/^255/', '', $number);
        
        // Remove leading zero if present
        $clean = ltrim($clean, '0');
        
        // Add country code
        return '255' . $clean;
    }
    
    /**
     * Auto-detect mobile money provider from number
     */
    protected function detect_mobile_provider($mobile_number) {
        $validation = $this->validate_mobile_number($mobile_number);
        
        if ($validation['valid']) {
            return $validation['provider'];
        }
        
        return null;
    }
    
    /**
     * Get provider limits
     */
    protected function get_provider_limits($provider_id) {
        return $this->providers[$provider_id] ?? [
            'min_amount' => 1000,
            'max_amount' => 1000000
        ];
    }
    
    /**
     * Generate mobile money transaction ID
     */
    protected function generate_mobile_transaction_id($provider) {
        $prefix_map = [
            'mpesa' => 'MP',
            'tigo_pesa' => 'TP',
            'airtel_money' => 'AM',
            'halopesa' => 'HP'
        ];
        
        $prefix = $prefix_map[$provider] ?? 'MM';
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        
        return "{$prefix}{$timestamp}{$random}";
    }
    
    /**
     * Initiate mobile money payment
     */
    protected function initiate_mobile_payment($donation, $payment_data, $transaction_id) {
        $provider = $payment_data['provider'];
        
        // Format mobile number
        $provider_config = $this->providers[$provider];
        $formatted_number = $this->format_mobile_number($payment_data['mobile_number'], $provider_config);
        
        // Prepare payment request data
        $payment_request = [
            'transaction_id' => $transaction_id,
            'mobile_number' => $formatted_number,
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'description' => $this->get_payment_description($donation),
            'callback_url' => $this->get_callback_url($transaction_id),
            'donor_name' => trim($donation->first_name . ' ' . $donation->last_name),
            'donor_email' => $donation->email
        ];
        
        // Call provider-specific payment initiation
        switch ($provider) {
            case 'mpesa':
                return $this->initiate_mpesa_payment($payment_request);
            
            case 'tigo_pesa':
                return $this->initiate_tigo_pesa_payment($payment_request);
            
            case 'airtel_money':
                return $this->initiate_airtel_money_payment($payment_request);
            
            case 'halopesa':
                return $this->initiate_halopesa_payment($payment_request);
            
            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported mobile money provider'
                ];
        }
    }
    
    /**
     * Initiate M-Pesa payment
     */
    protected function initiate_mpesa_payment($request) {
        try {
            // Prepare M-Pesa specific request
            $mpesa_request = [
                'BusinessShortCode' => $this->settings['mpesa_shortcode'],
                'Password' => $this->generate_mpesa_password(),
                'Timestamp' => date('YmdHis'),
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $request['amount'],
                'PartyA' => $request['mobile_number'],
                'PartyB' => $this->settings['mpesa_shortcode'],
                'PhoneNumber' => $request['mobile_number'],
                'CallBackURL' => $request['callback_url'],
                'AccountReference' => $request['transaction_id'],
                'TransactionDesc' => $request['description']
            ];
            
            // Make API call to M-Pesa
            $response = $this->make_mpesa_api_call('/stkpush/v1/processrequest', $mpesa_request);
            
            if ($response && $response['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'response_description' => $response['ResponseDescription']
                ];
            } else {
                throw new Exception($response['errorMessage'] ?? 'M-Pesa payment initiation failed');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Initiate Tigo Pesa payment
     */
    protected function initiate_tigo_pesa_payment($request) {
        try {
            $tigo_request = [
                'MerchantTransactionID' => $request['transaction_id'],
                'BillerCode' => $this->settings['tigo_pesa_biller_code'],
                'Amount' => $request['amount'],
                'Currency' => $request['currency'],
                'MSISDN' => $request['mobile_number'],
                'CallbackURL' => $request['callback_url'],
                'Description' => $request['description']
            ];
            
            $response = $this->make_tigo_pesa_api_call('/v1/payment/request', $tigo_request);
            
            if ($response && $response['statusCode'] === 'REQUEST_ACCEPTED') {
                return [
                    'success' => true,
                    'reference_id' => $response['referenceId'],
                    'status' => $response['status']
                ];
            } else {
                throw new Exception($response['statusDescription'] ?? 'Tigo Pesa payment initiation failed');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Initiate Airtel Money payment
     */
    protected function initiate_airtel_money_payment($request) {
        try {
            $airtel_request = [
                'transaction_id' => $request['transaction_id'],
                'merchant_id' => $this->settings['airtel_money_merchant_id'],
                'amount' => $request['amount'],
                'currency' => $request['currency'],
                'msisdn' => $request['mobile_number'],
                'callback_url' => $request['callback_url'],
                'description' => $request['description']
            ];
            
            $response = $this->make_airtel_money_api_call('/v1/payments/request', $airtel_request);
            
            if ($response && $response['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'transaction_reference' => $response['transaction_reference'],
                    'status' => $response['status']
                ];
            } else {
                throw new Exception($response['message'] ?? 'Airtel Money payment initiation failed');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Initiate HaloPesa payment
     */
    protected function initiate_halopesa_payment($request) {
        // HaloPesa implementation would go here
        // For now, return a generic response
        return [
            'success' => true,
            'reference_id' => $request['transaction_id'],
            'status' => 'PENDING'
        ];
    }
    
    /**
     * Make M-Pesa API call with authentication
     */
    protected function make_mpesa_api_call($endpoint, $data) {
        $base_url = $this->settings['sandbox_mode'] 
            ? 'https://sandbox.safaricom.co.ke/mpesa'
            : 'https://api.safaricom.co.ke/mpesa';
        
        $access_token = $this->get_mpesa_access_token();
        
        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ];
        
        return $this->make_api_request($base_url . $endpoint, $data, 'POST', $headers);
    }
    
    /**
     * Get M-Pesa access token
     */
    protected function get_mpesa_access_token() {
        $cache_key = 'mpesa_access_token';
        $cached_token = $this->cache->get($cache_key);
        
        if ($cached_token) {
            return $cached_token;
        }
        
        $credentials = base64_encode($this->settings['mpesa_api_key'] . ':' . $this->settings['mpesa_secret_key']);
        
        $response = $this->make_api_request(
            $this->settings['sandbox_mode'] 
                ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
                : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            [],
            'GET',
            ['Authorization' => 'Basic ' . $credentials]
        );
        
        if ($response && isset($response['access_token'])) {
            $token = $response['access_token'];
            $this->cache->set($cache_key, $token, 3500); // Cache for ~1 hour
            return $token;
        }
        
        throw new Exception('Failed to get M-Pesa access token');
    }
    
    /**
     * Generate M-Pesa password
     */
    protected function generate_mpesa_password() {
        $timestamp = date('YmdHis');
        $passkey = $this->settings['mpesa_passkey'] ?? '';
        
        return base64_encode($this->settings['mpesa_shortcode'] . $passkey . $timestamp);
    }
    
    /**
     * Make Tigo Pesa API call
     */
    protected function make_tigo_pesa_api_call($endpoint, $data) {
        $base_url = $this->settings['sandbox_mode']
            ? 'https://api-sandbox.tigo.co.tz'
            : 'https://api.tigo.co.tz';
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->get_tigo_pesa_access_token(),
            'Content-Type' => 'application/json'
        ];
        
        return $this->make_api_request($base_url . $endpoint, $data, 'POST', $headers);
    }
    
    /**
     * Get Tigo Pesa access token
     */
    protected function get_tigo_pesa_access_token() {
        // Implementation for Tigo Pesa authentication
        return $this->settings['tigo_pesa_api_key'];
    }
    
    /**
     * Make Airtel Money API call
     */
    protected function make_airtel_money_api_call($endpoint, $data) {
        $base_url = $this->settings['sandbox_mode']
            ? 'https://openapiuat.airtel.africa'
            : 'https://openapi.airtel.africa';
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->get_airtel_money_access_token(),
            'Content-Type' => 'application/json',
            'X-Country' => 'TZ',
            'X-Currency' => 'TZS'
        ];
        
        return $this->make_api_request($base_url . $endpoint, $data, 'POST', $headers);
    }
    
    /**
     * Get Airtel Money access token
     */
    protected function get_airtel_money_access_token() {
        // Implementation for Airtel Money authentication
        return $this->settings['airtel_money_api_key'];
    }
    
    /**
     * Store mobile transaction with enhanced metadata
     */
    protected function store_mobile_transaction($donation_id, $transaction_id, $payment_data, $payment_result) {
        $transaction_data = [
            'donation_id' => $donation_id,
            'transaction_id' => $transaction_id,
            'payment_method' => $this->id,
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'status' => 'pending',
            'gateway_response' => $payment_result,
            'metadata' => [
                'provider' => $payment_data['provider'],
                'mobile_number' => $this->mask_mobile_number($payment_data['mobile_number']),
                'formatted_number' => $payment_data['formatted_number'] ?? '',
                'gateway_version' => '2.0.0',
                'initiated_at' => current_time('mysql'),
                'timeout_at' => date('Y-m-d H:i:s', time() + $this->settings['transaction_timeout']),
                'checkout_request_id' => $payment_result['checkout_request_id'] ?? null,
                'reference_id' => $payment_result['reference_id'] ?? null
            ]
        ];
        
        KiliSmile_Donation_DB::insert_transaction($transaction_data);
    }
    
    /**
     * Mask mobile number for logging
     */
    protected function mask_mobile_number($number) {
        if (strlen($number) <= 4) {
            return $number;
        }
        
        return substr($number, 0, 3) . str_repeat('*', strlen($number) - 6) . substr($number, -3);
    }
    
    /**
     * Get payment description
     */
    protected function get_payment_description($donation) {
        return "Donation to Kilismile Organization - " . $donation->donation_id;
    }
    
    /**
     * Get callback URL for mobile money provider
     */
    protected function get_callback_url($transaction_id) {
        return rest_url('kilismile/v1/mobile-money-callback/' . $transaction_id);
    }
    
    /**
     * Get status check URL
     */
    protected function get_status_check_url($transaction_id) {
        return admin_url('admin-ajax.php?action=kilismile_check_mobile_money_status&transaction_id=' . $transaction_id);
    }
    
    /**
     * Get payment instructions for user
     */
    protected function get_payment_instructions($provider, $payment_data) {
        $provider_names = [
            'mpesa' => 'M-Pesa',
            'tigo_pesa' => 'Tigo Pesa',
            'airtel_money' => 'Airtel Money',
            'halopesa' => 'HaloPesa'
        ];
        
        $provider_name = $provider_names[$provider] ?? 'Mobile Money';
        $amount = number_format($payment_data['amount'], 0);
        
        $instructions = [
            'title' => "Complete your {$provider_name} payment",
            'steps' => [
                "Check your phone for a {$provider_name} payment request",
                "Enter your {$provider_name} PIN to authorize the payment",
                "You will receive a confirmation SMS when payment is successful",
                "Your donation receipt will be sent to your email"
            ],
            'amount' => "Amount: TZS {$amount}",
            'timeout' => "This payment request will expire in " . ($this->settings['transaction_timeout'] / 60) . " minutes"
        ];
        
        return $instructions;
    }
    
    /**
     * Register mobile money webhook endpoint
     */
    public function register_mobile_money_webhook() {
        register_rest_route('kilismile/v1', '/mobile-money-callback/(?P<transaction_id>[a-zA-Z0-9]+)', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_mobile_money_callback'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Handle mobile money callback
     */
    public function handle_mobile_money_callback($request) {
        try {
            $transaction_id = $request->get_param('transaction_id');
            $callback_data = $request->get_json_params();
            
            $this->log("Mobile money callback received", 'info', [
                'transaction_id' => $transaction_id,
                'data_keys' => array_keys($callback_data ?: [])
            ]);
            
            // Get transaction
            $transaction = KiliSmile_Donation_DB::get_transaction($transaction_id);
            if (!$transaction) {
                $this->log("Transaction not found for callback: {$transaction_id}", 'error');
                return new WP_REST_Response(['error' => 'Transaction not found'], 404);
            }
            
            // Process callback based on provider
            $result = $this->process_mobile_money_callback($transaction, $callback_data);
            
            if ($result) {
                return new WP_REST_Response(['status' => 'success'], 200);
            } else {
                return new WP_REST_Response(['error' => 'Processing failed'], 400);
            }
            
        } catch (Exception $e) {
            $this->log("Mobile money callback error: " . $e->getMessage(), 'error');
            return new WP_REST_Response(['error' => 'Server error'], 500);
        }
    }
    
    /**
     * Process mobile money callback
     */
    protected function process_mobile_money_callback($transaction, $callback_data) {
        $metadata = json_decode($transaction->metadata ?? '{}', true);
        $provider = $metadata['provider'] ?? '';
        
        // Process based on provider
        switch ($provider) {
            case 'mpesa':
                return $this->process_mpesa_callback($transaction, $callback_data);
            
            case 'tigo_pesa':
                return $this->process_tigo_pesa_callback($transaction, $callback_data);
            
            case 'airtel_money':
                return $this->process_airtel_money_callback($transaction, $callback_data);
            
            default:
                return $this->process_generic_callback($transaction, $callback_data);
        }
    }
    
    /**
     * Process M-Pesa callback
     */
    protected function process_mpesa_callback($transaction, $data) {
        $result_code = $data['Body']['stkCallback']['ResultCode'] ?? null;
        
        if ($result_code === 0) {
            // Payment successful
            $callback_metadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
            
            $payment_details = [];
            foreach ($callback_metadata as $item) {
                $payment_details[$item['Name']] = $item['Value'];
            }
            
            // Update transaction
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'completed',
                'gateway_transaction_id' => $payment_details['MpesaReceiptNumber'] ?? '',
                'gateway_response' => $data,
                'completed_at' => current_time('mysql')
            ]);
            
            // Update donation
            KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                'status' => 'completed'
            ]);
            
            // Send confirmation
            $this->send_payment_confirmation($transaction, $payment_details);
            
            return true;
        } else {
            // Payment failed
            $error_message = $data['Body']['stkCallback']['ResultDesc'] ?? 'Payment failed';
            
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'failed',
                'error_message' => $error_message,
                'gateway_response' => $data
            ]);
            
            return true;
        }
    }
    
    /**
     * Process Tigo Pesa callback
     */
    protected function process_tigo_pesa_callback($transaction, $data) {
        $status = $data['TransactionStatus'] ?? '';
        
        if (strtoupper($status) === 'SUCCESSFUL') {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'completed',
                'gateway_transaction_id' => $data['TransactionID'] ?? '',
                'gateway_response' => $data,
                'completed_at' => current_time('mysql')
            ]);
            
            KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                'status' => 'completed'
            ]);
            
            return true;
        } else {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'failed',
                'error_message' => $data['TransactionDescription'] ?? 'Payment failed',
                'gateway_response' => $data
            ]);
            
            return true;
        }
    }
    
    /**
     * Process Airtel Money callback
     */
    protected function process_airtel_money_callback($transaction, $data) {
        $status = $data['transaction']['status'] ?? '';
        
        if (strtoupper($status) === 'SUCCESS') {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'completed',
                'gateway_transaction_id' => $data['transaction']['id'] ?? '',
                'gateway_response' => $data,
                'completed_at' => current_time('mysql')
            ]);
            
            KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                'status' => 'completed'
            ]);
            
            return true;
        } else {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'failed',
                'error_message' => $data['transaction']['message'] ?? 'Payment failed',
                'gateway_response' => $data
            ]);
            
            return true;
        }
    }
    
    /**
     * Process generic callback
     */
    protected function process_generic_callback($transaction, $data) {
        // Generic callback processing for other providers
        $status = $data['status'] ?? $data['Status'] ?? '';
        
        if (in_array(strtolower($status), ['success', 'successful', 'completed'])) {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'completed',
                'gateway_response' => $data,
                'completed_at' => current_time('mysql')
            ]);
            
            KiliSmile_Donation_DB::update_donation($transaction->donation_id, [
                'status' => 'completed'
            ]);
            
            return true;
        } else {
            KiliSmile_Donation_DB::update_transaction($transaction->transaction_id, [
                'status' => 'failed',
                'gateway_response' => $data
            ]);
            
            return true;
        }
    }
    
    /**
     * AJAX validate mobile number
     */
    public function ajax_validate_mobile_number() {
        try {
            $mobile_number = sanitize_text_field($_POST['mobile_number'] ?? '');
            
            if (empty($mobile_number)) {
                wp_send_json_error(['message' => 'Mobile number is required']);
                return;
            }
            
            $validation = $this->validate_mobile_number($mobile_number);
            
            if ($validation['valid']) {
                wp_send_json_success([
                    'valid' => true,
                    'provider' => $validation['provider'],
                    'provider_name' => $this->providers[$validation['provider']]['name'],
                    'formatted_number' => $validation['formatted_number'],
                    'limits' => $this->get_provider_limits($validation['provider'])
                ]);
            } else {
                wp_send_json_error(['message' => $validation['message']]);
            }
            
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Validation failed']);
        }
    }
    
    /**
     * AJAX check payment status
     */
    public function ajax_check_payment_status() {
        try {
            $transaction_id = sanitize_text_field($_GET['transaction_id'] ?? '');
            
            if (empty($transaction_id)) {
                wp_send_json_error(['message' => 'Transaction ID is required']);
                return;
            }
            
            $transaction = KiliSmile_Donation_DB::get_transaction($transaction_id);
            
            if (!$transaction) {
                wp_send_json_error(['message' => 'Transaction not found']);
                return;
            }
            
            wp_send_json_success([
                'status' => $transaction->status,
                'transaction_id' => $transaction->transaction_id,
                'gateway_transaction_id' => $transaction->gateway_transaction_id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Status check failed']);
        }
    }
    
    /**
     * Send payment confirmation
     */
    protected function send_payment_confirmation($transaction, $payment_details = []) {
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
                'gateway_transaction_id' => $transaction->gateway_transaction_id,
                'payment_method' => 'Mobile Money',
                'date' => current_time('F j, Y'),
                'time' => current_time('g:i A')
            ];
            
            do_action('kilismile_send_donation_confirmation', $donation->email, $email_data);
            
        } catch (Exception $e) {
            $this->log("Failed to send mobile money confirmation: " . $e->getMessage(), 'warning');
        }
    }
    
    /**
     * Gateway-specific health checks
     */
    protected function perform_health_checks(&$health_status) {
        parent::perform_health_checks($health_status);
        
        // Check enabled providers
        $enabled_providers = $this->settings['enabled_providers'];
        if (empty($enabled_providers)) {
            $health_status['status'] = 'misconfigured';
            $health_status['details']['providers'] = 'No mobile money providers enabled';
            return;
        }
        
        // Check provider configurations
        $provider_status = [];
        foreach ($enabled_providers as $provider) {
            $provider_status[$provider] = $this->check_provider_configuration($provider);
        }
        
        $health_status['details']['providers'] = $provider_status;
    }
    
    /**
     * Check provider configuration
     */
    protected function check_provider_configuration($provider) {
        $required_settings = [
            'mpesa' => ['mpesa_shortcode', 'mpesa_api_key', 'mpesa_secret_key'],
            'tigo_pesa' => ['tigo_pesa_biller_code', 'tigo_pesa_api_key'],
            'airtel_money' => ['airtel_money_merchant_id', 'airtel_money_api_key']
        ];
        
        if (!isset($required_settings[$provider])) {
            return 'Configuration not required';
        }
        
        foreach ($required_settings[$provider] as $setting) {
            if (empty($this->settings[$setting])) {
                return 'Misconfigured: Missing ' . $setting;
            }
        }
        
        return 'Configured';
    }
    
    /**
     * Get enhanced settings fields for admin
     */
    public function get_settings_fields() {
        return [
            [
                'id' => 'enabled',
                'title' => 'Enable Enhanced Mobile Money',
                'type' => 'checkbox',
                'description' => 'Enable enhanced mobile money payments'
            ],
            [
                'id' => 'sandbox_mode',
                'title' => 'Sandbox Mode',
                'type' => 'checkbox',
                'description' => 'Enable sandbox mode for testing'
            ],
            [
                'id' => 'enabled_providers',
                'title' => 'Enabled Providers',
                'type' => 'multiselect',
                'options' => [
                    'mpesa' => 'M-Pesa',
                    'tigo_pesa' => 'Tigo Pesa',
                    'airtel_money' => 'Airtel Money',
                    'halopesa' => 'HaloPesa'
                ],
                'description' => 'Select which mobile money providers to enable'
            ],
            [
                'id' => 'auto_detect_provider',
                'title' => 'Auto-detect Provider',
                'type' => 'checkbox',
                'description' => 'Automatically detect mobile money provider from phone number'
            ],
            [
                'id' => 'transaction_timeout',
                'title' => 'Transaction Timeout (seconds)',
                'type' => 'number',
                'description' => 'How long to wait for payment completion',
                'default' => 300
            ],
            
            // M-Pesa settings
            [
                'id' => 'mpesa_shortcode',
                'title' => 'M-Pesa Business Shortcode',
                'type' => 'text',
                'description' => 'Your M-Pesa business shortcode'
            ],
            [
                'id' => 'mpesa_api_key',
                'title' => 'M-Pesa Consumer Key',
                'type' => 'text',
                'description' => 'Your M-Pesa app consumer key'
            ],
            [
                'id' => 'mpesa_secret_key',
                'title' => 'M-Pesa Consumer Secret',
                'type' => 'password',
                'description' => 'Your M-Pesa app consumer secret'
            ],
            
            // Tigo Pesa settings
            [
                'id' => 'tigo_pesa_biller_code',
                'title' => 'Tigo Pesa Biller Code',
                'type' => 'text',
                'description' => 'Your Tigo Pesa biller code'
            ],
            [
                'id' => 'tigo_pesa_api_key',
                'title' => 'Tigo Pesa API Key',
                'type' => 'text',
                'description' => 'Your Tigo Pesa API key'
            ],
            
            // Airtel Money settings
            [
                'id' => 'airtel_money_merchant_id',
                'title' => 'Airtel Money Merchant ID',
                'type' => 'text',
                'description' => 'Your Airtel Money merchant ID'
            ],
            [
                'id' => 'airtel_money_api_key',
                'title' => 'Airtel Money API Key',
                'type' => 'text',
                'description' => 'Your Airtel Money API key'
            ]
        ];
    }
}

// Initialize enhanced mobile money gateway
add_action('init', function() {
    if (class_exists('KiliSmile_Donation_Handler')) {
        new KiliSmile_Mobile_Money_Gateway_Enhanced();
    }
});


