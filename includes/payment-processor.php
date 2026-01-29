<?php
/**
 * Complete Payment Processing System for KiliSmile Donations
 * Integrates with KiliSmile Payments Plugin for AzamPay and PayPal
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * KiliSmile Payment Processor Class
 */
class KiliSmile_Payment_Processor {
    
    /**
     * Initialize the payment processor
     */
    public function __construct() {
        add_action('wp_ajax_kilismile_process_payment', array($this, 'process_payment_ajax'));
        add_action('wp_ajax_nopriv_kilismile_process_payment', array($this, 'process_payment_ajax'));
        
        // Add payment confirmation handlers
        add_action('wp_ajax_kilismile_confirm_payment', array($this, 'confirm_payment_ajax'));
        add_action('wp_ajax_nopriv_kilismile_confirm_payment', array($this, 'confirm_payment_ajax'));
        
        // Add payment status check
        add_action('wp_ajax_kilismile_check_payment_status', array($this, 'check_payment_status_ajax'));
        add_action('wp_ajax_nopriv_kilismile_check_payment_status', array($this, 'check_payment_status_ajax'));
        
        // Add webhook handlers
        add_action('wp_ajax_kilismile_azampay_webhook', array($this, 'handle_azampay_webhook'));
        add_action('wp_ajax_nopriv_kilismile_azampay_webhook', array($this, 'handle_azampay_webhook'));
        
        add_action('wp_ajax_kilismile_paypal_webhook', array($this, 'handle_paypal_webhook'));
        add_action('wp_ajax_nopriv_kilismile_paypal_webhook', array($this, 'handle_paypal_webhook'));
        
        // Database setup
        $this->setup_database();
    }
    
    /**
     * Setup database tables for donations
     */
    private function setup_database() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            transaction_id varchar(100) NOT NULL,
            donor_email varchar(100) NOT NULL,
            donor_name varchar(200) NOT NULL,
            donor_phone varchar(20),
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            payment_method varchar(50) NOT NULL,
            payment_status varchar(20) DEFAULT 'pending',
            is_recurring tinyint(1) DEFAULT 0,
            is_anonymous tinyint(1) DEFAULT 0,
            gateway_reference varchar(100),
            gateway_response longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY transaction_id (transaction_id),
            KEY donor_email (donor_email),
            KEY payment_status (payment_status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Process payment AJAX handler
     */
    public function process_payment_ajax() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['payment_nonce'], 'kilismile_payment_nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Security check failed', 'kilismile'))
            )));
        }
        
        // Sanitize input data
        $donation_data = array(
            'amount' => floatval($_POST['donation_amount']),
            'currency' => sanitize_text_field($_POST['donation_currency']),
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'recurring' => isset($_POST['donation_recurring']) ? intval($_POST['donation_recurring']) : 0,
            'anonymous' => isset($_POST['anonymous']) ? 1 : 0
        );
        
        // Validate required fields
        $validation = $this->validate_donation_data($donation_data);
        if (!$validation['valid']) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => $validation['message'])
            )));
        }
        
        // Generate unique transaction ID
        $transaction_id = $this->generate_transaction_id();
        
        // Save donation to database
        $donation_id = $this->save_donation($transaction_id, $donation_data);
        
        if (!$donation_id) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Failed to save donation. Please try again.', 'kilismile'))
            )));
        }
        
        // Process payment based on method
        $result = $this->process_payment_by_method($transaction_id, $donation_data);
        
        wp_die(json_encode($result));
    }
    
    /**
     * Validate donation data
     */
    private function validate_donation_data($data) {
        if (empty($data['amount']) || $data['amount'] <= 0) {
            return array('valid' => false, 'message' => __('Invalid donation amount', 'kilismile'));
        }
        
        if (empty($data['currency']) || !in_array($data['currency'], array('USD', 'TZS'))) {
            return array('valid' => false, 'message' => __('Invalid currency', 'kilismile'));
        }
        
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return array('valid' => false, 'message' => __('Name is required', 'kilismile'));
        }
        
        if (empty($data['email']) || !is_email($data['email'])) {
            return array('valid' => false, 'message' => __('Valid email is required', 'kilismile'));
        }
        
        if (empty($data['payment_method'])) {
            return array('valid' => false, 'message' => __('Payment method is required', 'kilismile'));
        }
        
        return array('valid' => true);
    }
    
    /**
     * Generate unique transaction ID
     */
    private function generate_transaction_id() {
        return 'KS_' . date('Ymd') . '_' . strtoupper(wp_generate_password(8, false));
    }
    
    /**
     * Save donation to database
     */
    private function save_donation($transaction_id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'transaction_id' => $transaction_id,
                'donor_email' => $data['email'],
                'donor_name' => $data['first_name'] . ' ' . $data['last_name'],
                'donor_phone' => $data['phone'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'payment_method' => $data['payment_method'],
                'is_recurring' => $data['recurring'],
                'is_anonymous' => $data['anonymous'],
                'payment_status' => 'pending'
            ),
            array('%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%d', '%s')
        );
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Process payment by method
     */
    private function process_payment_by_method($transaction_id, $data) {
        switch ($data['payment_method']) {
            case 'azampay':
                return $this->process_azampay_payment($transaction_id, $data);
            case 'paypal':
                return $this->process_paypal_payment($transaction_id, $data);
            default:
                return array(
                    'success' => false,
                    'data' => array('message' => __('Unsupported payment method', 'kilismile'))
                );
        }
    }
    
    /**
     * Process AzamPay payment
     */
    private function process_azampay_payment($transaction_id, $data) {
        // Check if KiliSmile Payments plugin is active
        if (!class_exists('KiliSmile_Payments_Plugin')) {
            return array(
                'success' => false,
                'data' => array('message' => __('Payment plugin not available', 'kilismile'))
            );
        }
        
        // Get AzamPay gateway
        $plugin = KiliSmile_Payments_Plugin::get_instance();
        $azampay_gateway = $plugin->get_gateway('azampay');
        
        if (!$azampay_gateway || !method_exists($azampay_gateway, 'is_enabled') || !$azampay_gateway->is_enabled()) {
            return array(
                'success' => false,
                'data' => array('message' => __('AzamPay gateway not available', 'kilismile'))
            );
        }
        
        try {
            // Prepare AzamPay payment data
            $payment_data = array(
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reference' => $transaction_id,
                'description' => sprintf(__('Donation to KiliSmile - %s', 'kilismile'), $transaction_id),
                'customer_name' => $data['first_name'] . ' ' . $data['last_name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'callback_url' => home_url('/wp-admin/admin-ajax.php?action=kilismile_azampay_webhook'),
                'return_url' => home_url('/donation-success/?transaction_id=' . $transaction_id),
                'cancel_url' => home_url('/donation-cancelled/?transaction_id=' . $transaction_id)
            );
            
            // Process with AzamPay
            $result = $azampay_gateway->create_payment($payment_data);
            
            if ($result && isset($result['success']) && $result['success']) {
                // Update donation with gateway reference
                $this->update_donation_gateway_reference($transaction_id, $result['reference']);
                
                return array(
                    'success' => true,
                    'data' => array(
                        'redirect_url' => $result['payment_url'],
                        'transaction_id' => $transaction_id,
                        'message' => __('Redirecting to AzamPay...', 'kilismile')
                    )
                );
            } else {
                return array(
                    'success' => false,
                    'data' => array('message' => __('Failed to initialize AzamPay payment', 'kilismile'))
                );
            }
            
        } catch (Exception $e) {
            error_log('AzamPay Payment Error: ' . $e->getMessage());
            return array(
                'success' => false,
                'data' => array('message' => __('Payment processing failed. Please try again.', 'kilismile'))
            );
        }
    }
    
    /**
     * Process PayPal payment
     */
    private function process_paypal_payment($transaction_id, $data) {
        // Check if KiliSmile Payments plugin is active
        if (!class_exists('KiliSmile_Payments_Plugin')) {
            return array(
                'success' => false,
                'data' => array('message' => __('Payment plugin not available', 'kilismile'))
            );
        }
        
        // Get PayPal gateway
        $plugin = KiliSmile_Payments_Plugin::get_instance();
        $paypal_gateway = $plugin->get_gateway('paypal');
        
        if (!$paypal_gateway || !method_exists($paypal_gateway, 'is_enabled') || !$paypal_gateway->is_enabled()) {
            return array(
                'success' => false,
                'data' => array('message' => __('PayPal gateway not available', 'kilismile'))
            );
        }
        
        try {
            // Prepare PayPal payment data
            $payment_data = array(
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reference' => $transaction_id,
                'description' => sprintf(__('Donation to KiliSmile - %s', 'kilismile'), $transaction_id),
                'customer_name' => $data['first_name'] . ' ' . $data['last_name'],
                'customer_email' => $data['email'],
                'return_url' => home_url('/donation-success/?transaction_id=' . $transaction_id),
                'cancel_url' => home_url('/donation-cancelled/?transaction_id=' . $transaction_id),
                'notify_url' => home_url('/wp-admin/admin-ajax.php?action=kilismile_paypal_webhook')
            );
            
            // Process with PayPal
            $result = $paypal_gateway->create_payment($payment_data);
            
            if ($result && isset($result['success']) && $result['success']) {
                // Update donation with gateway reference
                $this->update_donation_gateway_reference($transaction_id, $result['reference']);
                
                return array(
                    'success' => true,
                    'data' => array(
                        'redirect_url' => $result['payment_url'],
                        'transaction_id' => $transaction_id,
                        'message' => __('Redirecting to PayPal...', 'kilismile')
                    )
                );
            } else {
                return array(
                    'success' => false,
                    'data' => array('message' => __('Failed to initialize PayPal payment', 'kilismile'))
                );
            }
            
        } catch (Exception $e) {
            error_log('PayPal Payment Error: ' . $e->getMessage());
            return array(
                'success' => false,
                'data' => array('message' => __('Payment processing failed. Please try again.', 'kilismile'))
            );
        }
    }
    
    /**
     * Update donation with gateway reference
     */
    private function update_donation_gateway_reference($transaction_id, $gateway_reference) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        $wpdb->update(
            $table_name,
            array('gateway_reference' => $gateway_reference),
            array('transaction_id' => $transaction_id),
            array('%s'),
            array('%s')
        );
    }
    
    /**
     * Handle AzamPay webhook
     */
    public function handle_azampay_webhook() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            http_response_code(400);
            exit('Invalid data');
        }
        
        // Log webhook data
        error_log('AzamPay Webhook: ' . $input);
        
        // Process the webhook
        $this->process_payment_webhook($data, 'azampay');
        
        http_response_code(200);
        exit('OK');
    }
    
    /**
     * Handle PayPal webhook
     */
    public function handle_paypal_webhook() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            http_response_code(400);
            exit('Invalid data');
        }
        
        // Log webhook data
        error_log('PayPal Webhook: ' . $input);
        
        // Process the webhook
        $this->process_payment_webhook($data, 'paypal');
        
        http_response_code(200);
        exit('OK');
    }
    
    /**
     * Process payment webhook
     */
    private function process_payment_webhook($data, $gateway) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        // Extract transaction info based on gateway
        $transaction_id = null;
        $status = 'pending';
        
        if ($gateway === 'azampay') {
            $transaction_id = isset($data['reference']) ? $data['reference'] : null;
            $status = isset($data['status']) && $data['status'] === 'success' ? 'completed' : 'failed';
        } elseif ($gateway === 'paypal') {
            $transaction_id = isset($data['custom']) ? $data['custom'] : null;
            $status = isset($data['payment_status']) && $data['payment_status'] === 'Completed' ? 'completed' : 'failed';
        }
        
        if (!$transaction_id) {
            error_log('Webhook missing transaction ID: ' . json_encode($data));
            return;
        }
        
        // Update donation status
        $updated = $wpdb->update(
            $table_name,
            array(
                'payment_status' => $status,
                'gateway_response' => json_encode($data)
            ),
            array('transaction_id' => $transaction_id),
            array('%s', '%s'),
            array('%s')
        );
        
        if ($updated && $status === 'completed') {
            // Send thank you email
            $this->send_thank_you_email($transaction_id);
            
            // Trigger completion actions
            do_action('kilismile_donation_completed', $transaction_id, $data);
        }
    }
    
    /**
     * Send thank you email
     */
    private function send_thank_you_email($transaction_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        $donation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE transaction_id = %s",
            $transaction_id
        ));
        
        if (!$donation) {
            return;
        }
        
        $subject = sprintf(__('Thank you for your donation - %s', 'kilismile'), $transaction_id);
        
        $message = sprintf(
            __('Dear %s,

Thank you for your generous donation of %s %s to KiliSmile Organization.

Your transaction ID is: %s
Date: %s

Your contribution will help us continue our mission of improving health education and providing essential care to underserved communities in Tanzania.

You will receive a tax-deductible receipt shortly.

With gratitude,
The KiliSmile Team

---
This is an automated message. Please do not reply to this email.', 'kilismile'),
            $donation->donor_name,
            $donation->currency,
            number_format($donation->amount, 2),
            $donation->transaction_id,
            date('F j, Y', strtotime($donation->created_at))
        );
        
        wp_mail($donation->donor_email, $subject, $message);
    }
    
    /**
     * Check payment status AJAX
     */
    public function check_payment_status_ajax() {
        $transaction_id = sanitize_text_field($_POST['transaction_id']);
        
        if (!$transaction_id) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Invalid transaction ID', 'kilismile'))
            )));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        $donation = $wpdb->get_row($wpdb->prepare(
            "SELECT payment_status, amount, currency FROM $table_name WHERE transaction_id = %s",
            $transaction_id
        ));
        
        if (!$donation) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Transaction not found', 'kilismile'))
            )));
        }
        
        wp_die(json_encode(array(
            'success' => true,
            'data' => array(
                'status' => $donation->payment_status,
                'amount' => $donation->amount,
                'currency' => $donation->currency
            )
        )));
    }
}

// Initialize the payment processor
new KiliSmile_Payment_Processor();