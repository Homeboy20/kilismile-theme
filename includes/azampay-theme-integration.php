<?php
/**
 * AzamPay Integration Helper Functions
 * 
 * This file provides helper functions to integrate the AzamPay plugin
 * with the existing theme donation system.
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if AzamPay plugin is active and properly configured
 */
function kilismile_is_azampay_ready() {
    // Check if plugin is active
    if (!function_exists('azampay_donation_form')) {
        return false;
    }
    
    // Check if plugin is configured
    $settings = get_option('azampay_settings', array());
    
    return !empty($settings['client_id']) && 
           !empty($settings['client_secret']) && 
           !empty($settings['app_name']);
}

/**
 * Get AzamPay plugin status and configuration info
 */
function kilismile_get_azampay_status() {
    $status = array(
        'plugin_active' => function_exists('azampay_donation_form'),
        'configured' => false,
        'test_mode' => true,
        'supported_methods' => array(),
        'message' => ''
    );
    
    if (!$status['plugin_active']) {
        $status['message'] = __('AzamPay Payment Gateway plugin is not installed or activated.', 'kilismile');
        return $status;
    }
    
    // Check configuration
    $settings = get_option('azampay_settings', array());
    
    if (empty($settings['client_id']) || empty($settings['client_secret']) || empty($settings['app_name'])) {
        $status['message'] = __('AzamPay plugin is not fully configured. Please check API credentials.', 'kilismile');
        return $status;
    }
    
    $status['configured'] = true;
    $status['test_mode'] = !empty($settings['test_mode']);
    
    // Get supported payment methods
    if (!empty($settings['enable_mobile_money'])) {
        $status['supported_methods'][] = 'mobile_money';
    }
    if (!empty($settings['enable_card_payments'])) {
        $status['supported_methods'][] = 'cards';
    }
    if (!empty($settings['enable_bank_transfer'])) {
        $status['supported_methods'][] = 'bank_transfer';
    }
    
    $status['message'] = __('AzamPay plugin is ready to process payments.', 'kilismile');
    
    return $status;
}

/**
 * Display donation form with fallback
 */
function kilismile_display_donation_form($args = array()) {
    $defaults = array(
        'title' => __('Make a Donation', 'kilismile'),
        'amounts' => '10000,25000,50000,100000,250000',
        'success_url' => home_url('/donation-success'),
        'purpose_options' => 'greatest_need,education,equipment,outreach',
        'show_recurring' => 'true',
        'show_anonymous' => 'true',
        'currency' => 'TZS',
        'class' => 'kilismile-azampay-form'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    if (kilismile_is_azampay_ready()) {
        // Build shortcode with attributes
        $shortcode = '[azampay_donation_form';
        foreach ($args as $key => $value) {
            if ($key !== 'class') {
                $shortcode .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
        }
        $shortcode .= ']';
        
        echo '<div class="' . esc_attr($args['class']) . '">';
        echo do_shortcode($shortcode);
        echo '</div>';
        
    } else {
        // Display fallback message
        kilismile_display_donation_fallback();
    }
}

/**
 * Display fallback message when plugin is not ready
 */
function kilismile_display_donation_fallback() {
    $status = kilismile_get_azampay_status();
    ?>
    <div class="donation-fallback azampay-fallback">
        <i class="fas fa-exclamation-triangle"></i>
        <h3><?php _e('Payment System Setup Required', 'kilismile'); ?></h3>
        <p><?php echo esc_html($status['message']); ?></p>
        
        <?php if (current_user_can('manage_options')) : ?>
        <div class="admin-instructions">
            <strong><?php _e('Administrator Setup Instructions:', 'kilismile'); ?></strong>
            <ol style="text-align: left; margin: 15px 0; padding-left: 20px;">
                <?php if (!$status['plugin_active']) : ?>
                <li><?php _e('Install and activate the AzamPay Payment Gateway plugin', 'kilismile'); ?></li>
                <?php endif; ?>
                
                <?php if ($status['plugin_active'] && !$status['configured']) : ?>
                <li><?php _e('Go to AzamPay Settings in the admin menu', 'kilismile'); ?></li>
                <li><?php _e('Enter your AzamPay API credentials (App Name, Client ID, Client Secret)', 'kilismile'); ?></li>
                <li><?php _e('Configure payment methods and test the connection', 'kilismile'); ?></li>
                <?php endif; ?>
                
                <li><?php _e('Test the payment integration in sandbox mode', 'kilismile'); ?></li>
                <li><?php _e('Switch to live mode when ready for production', 'kilismile'); ?></li>
            </ol>
            
            <?php if ($status['plugin_active']) : ?>
            <a href="<?php echo admin_url('admin.php?page=azampay-settings'); ?>" 
               class="btn btn-primary" 
               style="display: inline-block; padding: 10px 20px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 6px; margin-top: 10px;">
                <?php _e('Go to AzamPay Settings', 'kilismile'); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php else : ?>
        <div class="user-message">
            <p><?php _e('Please contact the site administrator to set up the payment system.', 'kilismile'); ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Add donation success parameters to URL
 */
function kilismile_add_donation_success_params($donation_data) {
    if (!empty($donation_data) && is_array($donation_data)) {
        $success_url = home_url('/donation-success');
        
        $params = array();
        if (!empty($donation_data['donation_id'])) {
            $params['donation_id'] = urlencode($donation_data['donation_id']);
        }
        if (!empty($donation_data['amount'])) {
            $params['amount'] = urlencode($donation_data['amount']);
        }
        if (!empty($donation_data['currency'])) {
            $params['currency'] = urlencode($donation_data['currency']);
        }
        if (!empty($donation_data['donor_name'])) {
            $params['donor'] = urlencode($donation_data['donor_name']);
        }
        
        if (!empty($params)) {
            $success_url = add_query_arg($params, $success_url);
        }
        
        return $success_url;
    }
    
    return home_url('/donation-success');
}

/**
 * Hook to customize AzamPay plugin success URL
 */
add_filter('azampay_success_redirect_url', function($url, $donation_data) {
    return kilismile_add_donation_success_params($donation_data);
}, 10, 2);

/**
 * Add custom CSS for AzamPay integration
 */
function kilismile_azampay_styles() {
    if (is_page_template('page-donate.php') || is_page('donate')) {
        ?>
        <style>
        /* AzamPay Plugin Integration Styles */
        .kilismile-azampay-form .azampay-donation-form {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(40,167,69,0.1);
        }
        
        .kilismile-azampay-form .form-group label {
            color: var(--dark-green);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .kilismile-azampay-form .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .kilismile-azampay-form .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
            outline: none;
        }
        
        .kilismile-azampay-form .azampay-submit-button {
            background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .kilismile-azampay-form .azampay-submit-button:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .kilismile-azampay-form .payment-method {
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .kilismile-azampay-form .payment-method:hover,
        .kilismile-azampay-form .payment-method.selected {
            border-color: var(--primary-green);
            background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
        }
        
        .kilismile-azampay-form .azampay-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .kilismile-azampay-form .azampay-message.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .kilismile-azampay-form .azampay-message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        /* Fallback styles */
        .azampay-fallback {
            text-align: center;
            padding: 60px 40px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }
        
        .azampay-fallback i {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 20px;
            display: block;
        }
        
        .azampay-fallback h3 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .azampay-fallback p {
            color: #6c757d;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .azampay-fallback .admin-instructions {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            text-align: left;
            margin-top: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .azampay-fallback .admin-instructions strong {
            color: #856404;
            display: block;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .azampay-fallback .admin-instructions ol {
            color: #856404;
            line-height: 1.6;
        }
        
        .azampay-fallback .user-message {
            background: #d1ecf1;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
            margin-top: 20px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .kilismile-azampay-form .azampay-donation-form {
                padding: 30px 20px;
            }
            
            .azampay-fallback {
                padding: 40px 20px;
            }
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'kilismile_azampay_styles');

/**
 * Create a shortcode for easy donation form embedding
 */
function kilismile_donation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Support Our Mission', 'kilismile'),
        'amounts' => '10000,25000,50000,100000',
        'currency' => 'TZS',
        'purpose' => 'greatest_need',
        'class' => 'kilismile-donation-embed'
    ), $atts);
    
    ob_start();
    kilismile_display_donation_form($atts);
    return ob_get_clean();
}
add_shortcode('kilismile_donate', 'kilismile_donation_shortcode');

/**
 * Add admin notice if AzamPay is not configured
 */
function kilismile_azampay_admin_notice() {
    if (current_user_can('manage_options') && !kilismile_is_azampay_ready()) {
        $status = kilismile_get_azampay_status();
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php _e('KiliSmile Donation System:', 'kilismile'); ?></strong>
                <?php echo esc_html($status['message']); ?>
                <?php if ($status['plugin_active']) : ?>
                <a href="<?php echo admin_url('admin.php?page=azampay-settings'); ?>" class="button button-primary">
                    <?php _e('Configure Now', 'kilismile'); ?>
                </a>
                <?php endif; ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'kilismile_azampay_admin_notice');

