<?php
/**
 * KiliSmile Payments - Enhanced Validation System
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Enhanced_Validator {
    
    private $errors = array();
    private $warnings = array();
    private $validation_rules = array();
    
    public function __construct() {
        $this->init_validation_rules();
        
        // Add action hooks
        add_action('init', array($this, 'init_validator'));
        add_filter('kilismile_validate_donation_data', array($this, 'validate_donation_data'), 10, 2);
        add_filter('kilismile_validate_payment_data', array($this, 'validate_payment_data'), 10, 2);
    }
    
    /**
     * Initialize validator
     */
    public function init_validator() {
        // Set up custom validation rules
        $this->setup_custom_rules();
    }
    
    /**
     * Initialize default validation rules
     */
    private function init_validation_rules() {
        $this->validation_rules = array(
            'email' => array(
                'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
                'message' => __('Please enter a valid email address.', 'kilismile-payments')
            ),
            'phone' => array(
                'pattern' => '/^[\+]?[1-9][\d]{0,15}$/',
                'message' => __('Please enter a valid phone number.', 'kilismile-payments')
            ),
            'amount' => array(
                'min' => get_option('kilismile_payments_min_amount', 1),
                'max' => get_option('kilismile_payments_max_amount', 10000),
                'message' => __('Please enter a valid donation amount.', 'kilismile-payments')
            ),
            'name' => array(
                'min_length' => 2,
                'max_length' => 100,
                'pattern' => '/^[a-zA-Z\s\'-\.]+$/',
                'message' => __('Please enter a valid name.', 'kilismile-payments')
            ),
            'currency' => array(
                'allowed' => array('USD', 'TZS', 'EUR', 'GBP', 'KES', 'UGX', 'RWF'),
                'message' => __('Please select a valid currency.', 'kilismile-payments')
            )
        );
    }
    
    /**
     * Setup custom validation rules
     */
    private function setup_custom_rules() {
        // Allow themes and plugins to modify validation rules
        $this->validation_rules = apply_filters('kilismile_validation_rules', $this->validation_rules);
    }
    
    /**
     * Validate donation data
     */
    public function validate_donation_data($data, $context = 'frontend') {
        $this->reset_errors();
        
        // Required field validation
        $this->validate_required_fields($data, array(
            'first_name' => __('First name is required.', 'kilismile-payments'),
            'last_name' => __('Last name is required.', 'kilismile-payments'),
            'email' => __('Email address is required.', 'kilismile-payments'),
            'amount' => __('Donation amount is required.', 'kilismile-payments'),
            'currency' => __('Currency is required.', 'kilismile-payments')
        ));
        
        // Validate individual fields
        if (isset($data['first_name'])) {
            $this->validate_name($data['first_name'], 'first_name');
        }
        
        if (isset($data['last_name'])) {
            $this->validate_name($data['last_name'], 'last_name');
        }
        
        if (isset($data['email'])) {
            $this->validate_email($data['email']);
        }
        
        if (isset($data['phone']) && !empty($data['phone'])) {
            $this->validate_phone($data['phone']);
        }
        
        if (isset($data['amount'])) {
            $this->validate_amount($data['amount'], isset($data['currency']) ? $data['currency'] : 'USD');
        }
        
        if (isset($data['currency'])) {
            $this->validate_currency($data['currency']);
        }
        
        if (isset($data['payment_method'])) {
            $this->validate_payment_method($data['payment_method']);
        }
        
        // Context-specific validation
        if ($context === 'admin') {
            $this->validate_admin_specific_fields($data);
        }
        
        // Custom validation hooks
        do_action('kilismile_validate_donation_data_custom', $data, $this);
        
        return $this->get_validation_result();
    }
    
    /**
     * Validate payment data
     */
    public function validate_payment_data($data, $gateway) {
        $this->reset_errors();
        
        // Gateway-specific validation
        switch ($gateway) {
            case 'selcom':
                $this->validate_selcom_data($data);
                break;
                
            case 'tigo_pesa':
                $this->validate_mobile_money_data($data, 'tigo');
                break;
                
            case 'airtel_money':
                $this->validate_mobile_money_data($data, 'airtel');
                break;
                
            case 'halopesa':
                $this->validate_mobile_money_data($data, 'halo');
                break;
                
            case 'azam_pay':
                $this->validate_mobile_money_data($data, 'azam');
                break;
                
            default:
                $this->validate_generic_payment_data($data);
        }
        
        return $this->get_validation_result();
    }
    
    /**
     * Validate required fields
     */
    private function validate_required_fields($data, $required_fields) {
        foreach ($required_fields as $field => $message) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->add_error($field, $message);
            }
        }
    }
    
    /**
     * Validate name fields
     */
    private function validate_name($name, $field = 'name') {
        $rule = $this->validation_rules['name'];
        
        if (strlen($name) < $rule['min_length']) {
            $this->add_error($field, sprintf(__('Name must be at least %d characters long.', 'kilismile-payments'), $rule['min_length']));
            return false;
        }
        
        if (strlen($name) > $rule['max_length']) {
            $this->add_error($field, sprintf(__('Name must not exceed %d characters.', 'kilismile-payments'), $rule['max_length']));
            return false;
        }
        
        if (!preg_match($rule['pattern'], $name)) {
            $this->add_error($field, __('Name contains invalid characters.', 'kilismile-payments'));
            return false;
        }
        
        // Check for suspicious patterns
        if ($this->is_suspicious_name($name)) {
            $this->add_warning($field, __('Name appears suspicious and may require manual review.', 'kilismile-payments'));
        }
        
        return true;
    }
    
    /**
     * Validate email address
     */
    private function validate_email($email) {
        $rule = $this->validation_rules['email'];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->add_error('email', $rule['message']);
            return false;
        }
        
        if (!preg_match($rule['pattern'], $email)) {
            $this->add_error('email', $rule['message']);
            return false;
        }
        
        // Check for disposable email services
        if ($this->is_disposable_email($email)) {
            $this->add_warning('email', __('Disposable email address detected.', 'kilismile-payments'));
        }
        
        // Check domain reputation
        if ($this->is_suspicious_email_domain($email)) {
            $this->add_warning('email', __('Email domain may be suspicious.', 'kilismile-payments'));
        }
        
        return true;
    }
    
    /**
     * Validate phone number
     */
    private function validate_phone($phone) {
        $rule = $this->validation_rules['phone'];
        
        // Clean phone number
        $cleaned_phone = preg_replace('/[^\d\+]/', '', $phone);
        
        if (!preg_match($rule['pattern'], $cleaned_phone)) {
            $this->add_error('phone', $rule['message']);
            return false;
        }
        
        // Validate length
        if (strlen($cleaned_phone) < 10 || strlen($cleaned_phone) > 15) {
            $this->add_error('phone', __('Phone number must be between 10 and 15 digits.', 'kilismile-payments'));
            return false;
        }
        
        // Country-specific validation
        if (!$this->validate_phone_country_format($cleaned_phone)) {
            $this->add_warning('phone', __('Phone number format may not be valid for the selected country.', 'kilismile-payments'));
        }
        
        return true;
    }
    
    /**
     * Validate donation amount
     */
    private function validate_amount($amount, $currency = 'USD') {
        $rule = $this->validation_rules['amount'];
        $amount = floatval($amount);
        
        if ($amount < $rule['min']) {
            $this->add_error('amount', sprintf(__('Minimum donation amount is %s.', 'kilismile-payments'), $this->format_currency($rule['min'], $currency)));
            return false;
        }
        
        if ($amount > $rule['max']) {
            $this->add_error('amount', sprintf(__('Maximum donation amount is %s.', 'kilismile-payments'), $this->format_currency($rule['max'], $currency)));
            return false;
        }
        
        // Check for suspicious amounts
        if ($this->is_suspicious_amount($amount, $currency)) {
            $this->add_warning('amount', __('Unusual donation amount detected.', 'kilismile-payments'));
        }
        
        return true;
    }
    
    /**
     * Validate currency
     */
    private function validate_currency($currency) {
        $rule = $this->validation_rules['currency'];
        
        if (!in_array($currency, $rule['allowed'])) {
            $this->add_error('currency', $rule['message']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate payment method
     */
    private function validate_payment_method($payment_method) {
        $available_gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();
        $enabled_gateways = array_filter($available_gateways, function($gateway) {
            return $gateway['enabled'];
        });
        
        if (!isset($enabled_gateways[$payment_method])) {
            $this->add_error('payment_method', __('Invalid payment method selected.', 'kilismile-payments'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate Selcom payment data
     */
    private function validate_selcom_data($data) {
        // Selcom-specific validation
        if (isset($data['phone']) && !empty($data['phone'])) {
            $this->validate_tanzanian_phone($data['phone']);
        }
    }
    
    /**
     * Validate mobile money data
     */
    private function validate_mobile_money_data($data, $provider) {
        if (!isset($data['phone']) || empty($data['phone'])) {
            $this->add_error('phone', __('Phone number is required for mobile money payments.', 'kilismile-payments'));
            return false;
        }
        
        switch ($provider) {
            case 'tigo':
                $this->validate_tigo_phone($data['phone']);
                break;
                
            case 'airtel':
                $this->validate_airtel_phone($data['phone']);
                break;
                
            case 'halo':
                $this->validate_halo_phone($data['phone']);
                break;
                
            case 'azam':
                $this->validate_azam_phone($data['phone']);
                break;
        }
        
        return true;
    }
    
    /**
     * Validate generic payment data
     */
    private function validate_generic_payment_data($data) {
        // Generic payment validation
        if (isset($data['card_number'])) {
            $this->validate_card_number($data['card_number']);
        }
        
        if (isset($data['cvv'])) {
            $this->validate_cvv($data['cvv']);
        }
        
        if (isset($data['expiry_date'])) {
            $this->validate_expiry_date($data['expiry_date']);
        }
    }
    
    /**
     * Validate Tanzanian phone number
     */
    private function validate_tanzanian_phone($phone) {
        $cleaned_phone = preg_replace('/[^\d]/', '', $phone);
        
        // Tanzania mobile prefixes
        $valid_prefixes = array('255', '0');
        $provider_prefixes = array(
            'tigo' => array('71', '65'),
            'airtel' => array('78', '68'),
            'vodacom' => array('74', '75', '76'),
            'halo' => array('62'),
            'azam' => array('73')
        );
        
        $is_valid = false;
        
        // Check for international format
        if (substr($cleaned_phone, 0, 3) === '255') {
            $local_part = substr($cleaned_phone, 3);
            if (strlen($local_part) === 9) {
                $is_valid = true;
            }
        }
        // Check for local format
        elseif (substr($cleaned_phone, 0, 1) === '0') {
            $local_part = substr($cleaned_phone, 1);
            if (strlen($local_part) === 9) {
                $is_valid = true;
            }
        }
        // Check for format without country code or leading zero
        elseif (strlen($cleaned_phone) === 9) {
            $is_valid = true;
        }
        
        if (!$is_valid) {
            $this->add_error('phone', __('Invalid Tanzanian phone number format.', 'kilismile-payments'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Provider-specific phone validation
     */
    private function validate_tigo_phone($phone) {
        $this->validate_provider_phone($phone, 'tigo', array('71', '65'));
    }
    
    private function validate_airtel_phone($phone) {
        $this->validate_provider_phone($phone, 'airtel', array('78', '68'));
    }
    
    private function validate_halo_phone($phone) {
        $this->validate_provider_phone($phone, 'halo', array('62'));
    }
    
    private function validate_azam_phone($phone) {
        $this->validate_provider_phone($phone, 'azam', array('73'));
    }
    
    private function validate_provider_phone($phone, $provider, $valid_prefixes) {
        $cleaned_phone = preg_replace('/[^\d]/', '', $phone);
        
        // Extract the prefix (first 2 digits after country code/leading zero)
        $prefix = '';
        if (substr($cleaned_phone, 0, 3) === '255') {
            $prefix = substr($cleaned_phone, 3, 2);
        } elseif (substr($cleaned_phone, 0, 1) === '0') {
            $prefix = substr($cleaned_phone, 1, 2);
        } else {
            $prefix = substr($cleaned_phone, 0, 2);
        }
        
        if (!in_array($prefix, $valid_prefixes)) {
            $this->add_warning('phone', sprintf(__('Phone number may not be compatible with %s.', 'kilismile-payments'), ucfirst($provider)));
        }
    }
    
    /**
     * Check if name is suspicious
     */
    private function is_suspicious_name($name) {
        $suspicious_patterns = array(
            '/test/i',
            '/fake/i',
            '/admin/i',
            '/null/i',
            '/undefined/i',
            '/^[a-z]$/i',
            '/\d{4,}/', // Numbers with 4+ digits
            '/(.)\1{4,}/', // Repeated characters
        );
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if email is from a disposable service
     */
    private function is_disposable_email($email) {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        
        $disposable_domains = array(
            '10minutemail.com',
            'tempmail.org',
            'guerrillamail.com',
            'mailinator.com',
            'yopmail.com',
            'temp-mail.org',
            'throwaway.email'
        );
        
        return in_array($domain, $disposable_domains);
    }
    
    /**
     * Check if email domain is suspicious
     */
    private function is_suspicious_email_domain($email) {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        
        // Check for common typos in popular domains
        $suspicious_patterns = array(
            '/gmai\.com$/',
            '/yahooo\.com$/',
            '/hotmial\.com$/',
            '/outlok\.com$/'
        );
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $domain)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if amount is suspicious
     */
    private function is_suspicious_amount($amount, $currency) {
        // Very large round numbers
        if ($amount >= 1000 && $amount % 100 === 0) {
            return true;
        }
        
        // Suspicious patterns (e.g., 1234.56)
        $amount_str = strval($amount);
        if (preg_match('/1234|5678|9876|0000/', $amount_str)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Validate phone number country format
     */
    private function validate_phone_country_format($phone) {
        // For now, focus on East African formats
        $country_patterns = array(
            '/^255\d{9}$/', // Tanzania
            '/^254\d{9}$/', // Kenya
            '/^256\d{9}$/', // Uganda
            '/^250\d{9}$/', // Rwanda
        );
        
        foreach ($country_patterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Admin-specific field validation
     */
    private function validate_admin_specific_fields($data) {
        // Additional validation for admin-entered data
        if (isset($data['notes']) && strlen($data['notes']) > 1000) {
            $this->add_error('notes', __('Notes must not exceed 1000 characters.', 'kilismile-payments'));
        }
        
        if (isset($data['reference']) && !empty($data['reference'])) {
            if (strlen($data['reference']) > 50) {
                $this->add_error('reference', __('Reference must not exceed 50 characters.', 'kilismile-payments'));
            }
        }
    }
    
    /**
     * Format currency amount
     */
    private function format_currency($amount, $currency) {
        $converter = new KiliSmile_Currency_Converter();
        return $converter->format_amount($amount, $currency);
    }
    
    /**
     * Add validation error
     */
    private function add_error($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = array();
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Add validation warning
     */
    private function add_warning($field, $message) {
        if (!isset($this->warnings[$field])) {
            $this->warnings[$field] = array();
        }
        $this->warnings[$field][] = $message;
    }
    
    /**
     * Reset errors and warnings
     */
    private function reset_errors() {
        $this->errors = array();
        $this->warnings = array();
    }
    
    /**
     * Get validation result
     */
    private function get_validation_result() {
        return array(
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'warnings' => $this->warnings
        );
    }
    
    /**
     * Get all errors as flat array
     */
    public function get_error_messages() {
        $messages = array();
        foreach ($this->errors as $field => $field_errors) {
            $messages = array_merge($messages, $field_errors);
        }
        return $messages;
    }
    
    /**
     * Get all warnings as flat array
     */
    public function get_warning_messages() {
        $messages = array();
        foreach ($this->warnings as $field => $field_warnings) {
            $messages = array_merge($messages, $field_warnings);
        }
        return $messages;
    }
    
    /**
     * Validate card number (Luhn algorithm)
     */
    private function validate_card_number($card_number) {
        $card_number = preg_replace('/\D/', '', $card_number);
        
        if (strlen($card_number) < 13 || strlen($card_number) > 19) {
            $this->add_error('card_number', __('Invalid card number length.', 'kilismile-payments'));
            return false;
        }
        
        // Luhn algorithm
        $sum = 0;
        $reverse = strrev($card_number);
        
        for ($i = 0; $i < strlen($reverse); $i++) {
            $digit = intval($reverse[$i]);
            
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit % 10 + 1;
                }
            }
            
            $sum += $digit;
        }
        
        if ($sum % 10 !== 0) {
            $this->add_error('card_number', __('Invalid card number.', 'kilismile-payments'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate CVV
     */
    private function validate_cvv($cvv) {
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            $this->add_error('cvv', __('Invalid CVV format.', 'kilismile-payments'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate expiry date
     */
    private function validate_expiry_date($expiry_date) {
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
            $this->add_error('expiry_date', __('Invalid expiry date format. Use MM/YY.', 'kilismile-payments'));
            return false;
        }
        
        list($month, $year) = explode('/', $expiry_date);
        $year = '20' . $year;
        
        $expiry_timestamp = mktime(0, 0, 0, intval($month) + 1, 1, intval($year));
        
        if ($expiry_timestamp < time()) {
            $this->add_error('expiry_date', __('Card has expired.', 'kilismile-payments'));
            return false;
        }
        
        return true;
    }
}

// Initialize enhanced validator
new KiliSmile_Enhanced_Validator();

