<?php
/**
 * Enhanced Donation Collection System
 * 
 * Improves data collection, validation, and user experience
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Donation Collection Class
 */
class KiliSmile_Enhanced_Donation_Collection {
    
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new KiliSmile_Donation_Database();
        
        // Add AJAX handlers
        add_action('wp_ajax_kilismile_save_draft_donation', array($this, 'save_draft_donation'));
        add_action('wp_ajax_nopriv_kilismile_save_draft_donation', array($this, 'save_draft_donation'));
        
        add_action('wp_ajax_kilismile_validate_field', array($this, 'validate_field_ajax'));
        add_action('wp_ajax_nopriv_kilismile_validate_field', array($this, 'validate_field_ajax'));
        
        add_action('wp_ajax_kilismile_get_donation_campaigns', array($this, 'get_donation_campaigns'));
        add_action('wp_ajax_nopriv_kilismile_get_donation_campaigns', array($this, 'get_donation_campaigns'));
        
        // Enhanced validation
        add_filter('kilismile_validate_donation_data', array($this, 'enhanced_validation'), 10, 2);
        
        // Add additional fields to donation data
        add_filter('kilismile_donation_data_before_save', array($this, 'add_enhanced_fields'), 10, 1);
    }
    
    /**
     * Get enhanced donation form fields
     */
    public static function get_enhanced_fields() {
        return array(
            // Basic Information (already collected)
            'first_name' => array(
                'label' => __('First Name', 'kilismile'),
                'type' => 'text',
                'required' => true,
                'validation' => 'name',
                'maxlength' => 50
            ),
            'last_name' => array(
                'label' => __('Last Name', 'kilismile'),
                'type' => 'text',
                'required' => true,
                'validation' => 'name',
                'maxlength' => 50
            ),
            'email' => array(
                'label' => __('Email Address', 'kilismile'),
                'type' => 'email',
                'required' => true,
                'validation' => 'email',
                'placeholder' => 'example@email.com'
            ),
            'phone' => array(
                'label' => __('Phone Number', 'kilismile'),
                'type' => 'tel',
                'required' => false,
                'validation' => 'phone',
                'placeholder' => '+255123456789',
                'help_text' => __('Include country code (e.g., +255 for Tanzania)', 'kilismile')
            ),
            
            // Enhanced Address Fields
            'address_line1' => array(
                'label' => __('Address Line 1', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 100
            ),
            'address_line2' => array(
                'label' => __('Address Line 2', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 100
            ),
            'city' => array(
                'label' => __('City', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 50
            ),
            'state_province' => array(
                'label' => __('State/Province', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 50
            ),
            'postal_code' => array(
                'label' => __('Postal Code', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'postal_code',
                'maxlength' => 20
            ),
            'country' => array(
                'label' => __('Country', 'kilismile'),
                'type' => 'select',
                'required' => false,
                'validation' => 'country',
                'options' => self::get_countries_list()
            ),
            
            // Enhanced Donation Information
            'donation_purpose' => array(
                'label' => __('Donation Purpose', 'kilismile'),
                'type' => 'select',
                'required' => false,
                'validation' => 'text',
                'options' => self::get_donation_purposes(),
                'help_text' => __('How would you like your donation to be used?', 'kilismile')
            ),
            'donation_campaign' => array(
                'label' => __('Campaign (Optional)', 'kilismile'),
                'type' => 'select',
                'required' => false,
                'validation' => 'text',
                'options' => self::get_active_campaigns(),
                'help_text' => __('Support a specific campaign or program', 'kilismile')
            ),
            'donation_message' => array(
                'label' => __('Message (Optional)', 'kilismile'),
                'type' => 'textarea',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 500,
                'rows' => 4,
                'help_text' => __('Add a personal message with your donation', 'kilismile')
            ),
            
            // Tribute/Memorial Options
            'is_tribute' => array(
                'label' => __('This is a tribute donation', 'kilismile'),
                'type' => 'checkbox',
                'required' => false,
                'validation' => 'boolean'
            ),
            'tribute_type' => array(
                'label' => __('Tribute Type', 'kilismile'),
                'type' => 'select',
                'required' => false,
                'validation' => 'text',
                'options' => array(
                    'honor' => __('In Honor Of', 'kilismile'),
                    'memory' => __('In Memory Of', 'kilismile'),
                    'celebration' => __('Celebration', 'kilismile')
                ),
                'conditional' => 'is_tribute'
            ),
            'tribute_name' => array(
                'label' => __('Tribute Name', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'name',
                'maxlength' => 100,
                'conditional' => 'is_tribute',
                'help_text' => __('Name of person being honored or remembered', 'kilismile')
            ),
            'tribute_message' => array(
                'label' => __('Tribute Message', 'kilismile'),
                'type' => 'textarea',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 300,
                'rows' => 3,
                'conditional' => 'is_tribute'
            ),
            'notify_tribute' => array(
                'label' => __('Notify someone about this tribute', 'kilismile'),
                'type' => 'checkbox',
                'required' => false,
                'validation' => 'boolean',
                'conditional' => 'is_tribute'
            ),
            'tribute_notification_name' => array(
                'label' => __('Recipient Name', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'name',
                'maxlength' => 100,
                'conditional' => 'notify_tribute'
            ),
            'tribute_notification_email' => array(
                'label' => __('Recipient Email', 'kilismile'),
                'type' => 'email',
                'required' => false,
                'validation' => 'email',
                'conditional' => 'notify_tribute'
            ),
            
            // Employer/Organization
            'employer_organization' => array(
                'label' => __('Employer/Organization', 'kilismile'),
                'type' => 'text',
                'required' => false,
                'validation' => 'text',
                'maxlength' => 100,
                'help_text' => __('For employer matching programs', 'kilismile')
            ),
            'employer_match' => array(
                'label' => __('My employer matches donations', 'kilismile'),
                'type' => 'checkbox',
                'required' => false,
                'validation' => 'boolean'
            ),
            
            // Communication Preferences
            'newsletter_subscribe' => array(
                'label' => __('Subscribe to newsletter', 'kilismile'),
                'type' => 'checkbox',
                'required' => false,
                'validation' => 'boolean',
                'default' => true
            ),
            'receive_updates' => array(
                'label' => __('Receive program updates', 'kilismile'),
                'type' => 'checkbox',
                'required' => false,
                'validation' => 'boolean',
                'default' => true
            ),
            'communication_preference' => array(
                'label' => __('Preferred Communication Method', 'kilismile'),
                'type' => 'select',
                'required' => false,
                'validation' => 'text',
                'options' => array(
                    'email' => __('Email', 'kilismile'),
                    'phone' => __('Phone', 'kilismile'),
                    'sms' => __('SMS', 'kilismile'),
                    'mail' => __('Postal Mail', 'kilismile')
                )
            )
        );
    }
    
    /**
     * Get countries list
     */
    public static function get_countries_list() {
        return array(
            'TZ' => __('Tanzania', 'kilismile'),
            'US' => __('United States', 'kilismile'),
            'GB' => __('United Kingdom', 'kilismile'),
            'CA' => __('Canada', 'kilismile'),
            'KE' => __('Kenya', 'kilismile'),
            'UG' => __('Uganda', 'kilismile'),
            'RW' => __('Rwanda', 'kilismile'),
            'ZA' => __('South Africa', 'kilismile'),
            'AU' => __('Australia', 'kilismile'),
            'DE' => __('Germany', 'kilismile'),
            'FR' => __('France', 'kilismile'),
            'IT' => __('Italy', 'kilismile'),
            'ES' => __('Spain', 'kilismile'),
            'NL' => __('Netherlands', 'kilismile'),
            'SE' => __('Sweden', 'kilismile'),
            'NO' => __('Norway', 'kilismile'),
            'DK' => __('Denmark', 'kilismile'),
            'CH' => __('Switzerland', 'kilismile'),
            'BE' => __('Belgium', 'kilismile'),
            'AT' => __('Austria', 'kilismile'),
            'IE' => __('Ireland', 'kilismile'),
            'NZ' => __('New Zealand', 'kilismile'),
            'SG' => __('Singapore', 'kilismile'),
            'AE' => __('United Arab Emirates', 'kilismile'),
            'SA' => __('Saudi Arabia', 'kilismile'),
            'IN' => __('India', 'kilismile'),
            'CN' => __('China', 'kilismile'),
            'JP' => __('Japan', 'kilismile'),
            'OTHER' => __('Other', 'kilismile')
        );
    }
    
    /**
     * Get donation purposes
     */
    public static function get_donation_purposes() {
        return array(
            'general' => __('General Support', 'kilismile'),
            'oral_health' => __('Oral Health Programs', 'kilismile'),
            'health_education' => __('Health Education', 'kilismile'),
            'teacher_training' => __('Teacher Training', 'kilismile'),
            'health_screening' => __('Health Screening', 'kilismile'),
            'elderly_care' => __('Elderly Care Programs', 'kilismile'),
            'children_care' => __('Children\'s Health Programs', 'kilismile'),
            'equipment' => __('Medical Equipment', 'kilismile'),
            'infrastructure' => __('Infrastructure Development', 'kilismile'),
            'emergency' => __('Emergency Relief', 'kilismile'),
            'other' => __('Other', 'kilismile')
        );
    }
    
    /**
     * Get active campaigns
     */
    public static function get_active_campaigns() {
        // Get active programs as campaigns
        $campaigns = array(
            '' => __('No specific campaign', 'kilismile')
        );
        
        $programs = get_posts(array(
            'post_type' => 'programs',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_program_status',
                    'value' => array('active', 'planned'),
                    'compare' => 'IN'
                )
            ),
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        foreach ($programs as $program) {
            $campaigns[$program->ID] = $program->post_title;
        }
        
        return $campaigns;
    }
    
    /**
     * Enhanced validation
     */
    public function enhanced_validation($data, $errors) {
        // Phone number validation for Tanzania
        if (!empty($data['phone'])) {
            $phone = sanitize_text_field($data['phone']);
            
            // Remove spaces and dashes
            $phone = preg_replace('/[\s\-]/', '', $phone);
            
            // Tanzania phone validation
            if (preg_match('/^\+255/', $phone)) {
                // Must be +255 followed by 9 digits
                if (!preg_match('/^\+255\d{9}$/', $phone)) {
                    $errors['phone'] = __('Tanzania phone number must be in format: +255XXXXXXXXX (9 digits after country code)', 'kilismile');
                }
            } elseif (preg_match('/^0/', $phone)) {
                // Local format: 0XXXXXXXXX
                if (!preg_match('/^0\d{9}$/', $phone)) {
                    $errors['phone'] = __('Local phone number must be 10 digits starting with 0', 'kilismile');
                }
            } else {
                $errors['phone'] = __('Please include country code (e.g., +255) or use local format (0XXXXXXXXX)', 'kilismile');
            }
        }
        
        // Email domain validation (optional - can be enabled)
        if (!empty($data['email']) && apply_filters('kilismile_validate_email_domain', false)) {
            $email = sanitize_email($data['email']);
            $domain = substr(strrchr($email, "@"), 1);
            
            // Check for common disposable email domains
            $disposable_domains = array('tempmail.com', '10minutemail.com', 'guerrillamail.com');
            if (in_array(strtolower($domain), $disposable_domains)) {
                $errors['email'] = __('Please use a valid email address. Disposable email addresses are not allowed.', 'kilismile');
            }
        }
        
        // Address validation (if address provided, city should be provided)
        if (!empty($data['address_line1']) && empty($data['city'])) {
            $errors['city'] = __('City is required when providing an address', 'kilismile');
        }
        
        // Tribute validation
        if (!empty($data['is_tribute'])) {
            if (empty($data['tribute_name'])) {
                $errors['tribute_name'] = __('Tribute name is required for tribute donations', 'kilismile');
            }
            
            if (!empty($data['notify_tribute'])) {
                if (empty($data['tribute_notification_email'])) {
                    $errors['tribute_notification_email'] = __('Recipient email is required when notifying about tribute', 'kilismile');
                }
            }
        }
        
        // Postal code validation
        if (!empty($data['postal_code'])) {
            $postal_code = sanitize_text_field($data['postal_code']);
            if (!preg_match('/^[A-Z0-9\s\-]{3,20}$/i', $postal_code)) {
                $errors['postal_code'] = __('Invalid postal code format', 'kilismile');
            }
        }
        
        return $errors;
    }
    
    /**
     * Add enhanced fields to donation data
     */
    public function add_enhanced_fields($donation_data) {
        $enhanced_fields = array(
            'address_line1', 'address_line2', 'city', 'state_province', 
            'postal_code', 'country', 'donation_purpose', 'donation_campaign',
            'donation_message', 'is_tribute', 'tribute_type', 'tribute_name',
            'tribute_message', 'notify_tribute', 'tribute_notification_name',
            'tribute_notification_email', 'employer_organization', 'employer_match',
            'newsletter_subscribe', 'receive_updates', 'communication_preference'
        );
        
        foreach ($enhanced_fields as $field) {
            if (isset($_POST[$field])) {
                if (in_array($field, array('is_tribute', 'notify_tribute', 'employer_match', 'newsletter_subscribe', 'receive_updates'))) {
                    $donation_data[$field] = !empty($_POST[$field]) ? 1 : 0;
                } else {
                    $donation_data[$field] = sanitize_text_field($_POST[$field]);
                }
            }
        }
        
        return $donation_data;
    }
    
    /**
     * Save draft donation (auto-save)
     */
    public function save_draft_donation() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kilismile_donation_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'kilismile')));
        }
        
        // Get donation data
        $donation_data = array();
        $fields = self::get_enhanced_fields();
        
        foreach ($fields as $field_name => $field_config) {
            if (isset($_POST[$field_name])) {
                if ($field_config['type'] === 'checkbox') {
                    $donation_data[$field_name] = !empty($_POST[$field_name]) ? 1 : 0;
                } else {
                    $donation_data[$field_name] = sanitize_text_field($_POST[$field_name]);
                }
            }
        }
        
        // Save to session or transient
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : session_id();
        $transient_key = 'kilismile_draft_donation_' . $session_id;
        
        // Store for 1 hour
        set_transient($transient_key, $donation_data, HOUR_IN_SECONDS);
        
        wp_send_json_success(array(
            'message' => __('Draft saved', 'kilismile'),
            'session_id' => $session_id
        ));
    }
    
    /**
     * Validate field AJAX
     */
    public function validate_field_ajax() {
        $field_name = sanitize_text_field($_POST['field_name']);
        $field_value = isset($_POST['field_value']) ? $_POST['field_value'] : '';
        
        $fields = self::get_enhanced_fields();
        
        if (!isset($fields[$field_name])) {
            wp_send_json_error(array('message' => __('Invalid field', 'kilismile')));
        }
        
        $field_config = $fields[$field_name];
        $errors = array();
        
        // Validate based on field type
        switch ($field_config['validation']) {
            case 'email':
                if (!empty($field_value) && !is_email($field_value)) {
                    $errors[$field_name] = __('Invalid email address', 'kilismile');
                }
                break;
                
            case 'phone':
                if (!empty($field_value)) {
                    $phone = preg_replace('/[\s\-]/', '', $field_value);
                    if (!preg_match('/^(\+255\d{9}|0\d{9})$/', $phone)) {
                        $errors[$field_name] = __('Invalid phone number format', 'kilismile');
                    }
                }
                break;
                
            case 'name':
                if (!empty($field_value) && strlen($field_value) < 2) {
                    $errors[$field_name] = __('Name must be at least 2 characters', 'kilismile');
                }
                break;
        }
        
        if (empty($errors)) {
            wp_send_json_success(array('message' => __('Valid', 'kilismile')));
        } else {
            wp_send_json_error($errors);
        }
    }
    
    /**
     * Get donation campaigns AJAX
     */
    public function get_donation_campaigns() {
        $campaigns = self::get_active_campaigns();
        wp_send_json_success($campaigns);
    }
}

// Initialize
new KiliSmile_Enhanced_Donation_Collection();

/**
 * Helper function to get enhanced fields
 */
function kilismile_get_enhanced_donation_fields() {
    return KiliSmile_Enhanced_Donation_Collection::get_enhanced_fields();
}

/**
 * Helper function to get countries
 */
function kilismile_get_countries_list() {
    return KiliSmile_Enhanced_Donation_Collection::get_countries_list();
}

/**
 * Helper function to get donation purposes
 */
function kilismile_get_donation_purposes() {
    return KiliSmile_Enhanced_Donation_Collection::get_donation_purposes();
}

/**
 * Helper function to get active campaigns
 */
function kilismile_get_active_donation_campaigns() {
    return KiliSmile_Enhanced_Donation_Collection::get_active_campaigns();
}
