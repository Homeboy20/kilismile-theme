<?php
/**
 * Debug Payment Submission
 * Temporary debug handler to see what data is being sent
 */

if (!defined('ABSPATH')) exit;

// Add temporary debug handler
add_action('wp_ajax_debug_payment_data', 'kilismile_debug_payment_data');
add_action('wp_ajax_nopriv_debug_payment_data', 'kilismile_debug_payment_data');

function kilismile_debug_payment_data() {
    // Log all received data
    error_log('Debug Payment Data Received: ' . print_r($_POST, true));
    
    $debug_info = array(
        'timestamp' => current_time('mysql'),
        'post_data' => $_POST,
        'get_data' => $_GET,
        'server_info' => array(
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
            'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
            'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
        )
    );
    
    wp_die(json_encode(array(
        'success' => true,
        'message' => 'Debug data captured',
        'debug_info' => $debug_info
    )));
}

// Enhanced payment processor that logs validation issues
class KiliSmile_Payment_Debug_Processor {
    
    public function __construct() {
        add_action('wp_ajax_kilismile_process_payment_debug', array($this, 'debug_ajax_process_payment'));
        add_action('wp_ajax_nopriv_kilismile_process_payment_debug', array($this, 'debug_ajax_process_payment'));
    }
    
    public function debug_ajax_process_payment() {
        error_log('Payment Debug: Starting payment processing');
        error_log('Payment Debug: POST data: ' . print_r($_POST, true));
        
        // Check nonce first
        if (!isset($_POST['nonce'])) {
            $error = 'No nonce provided';
            error_log('Payment Debug Error: ' . $error);
            wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'no_nonce')));
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'kilismile_payment_nonce')) {
            $error = 'Security verification failed';
            error_log('Payment Debug Error: ' . $error);
            wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'nonce_failed')));
        }
        
        // Check required fields
        $required_fields = array('currency', 'amount', 'donor_name', 'donor_email');
        $missing_fields = array();
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            $error = 'Missing required fields: ' . implode(', ', $missing_fields);
            error_log('Payment Debug Error: ' . $error);
            wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'missing_fields', 'missing' => $missing_fields)));
        }
        
        // Check TZS specific fields
        if ($_POST['currency'] === 'TZS') {
            $tzs_required = array('donor_phone', 'mobile_network');
            $missing_tzs = array();
            
            foreach ($tzs_required as $field) {
                if (empty($_POST[$field])) {
                    $missing_tzs[] = $field;
                }
            }
            
            if (!empty($missing_tzs)) {
                $error = 'Missing TZS payment fields: ' . implode(', ', $missing_tzs);
                error_log('Payment Debug Error: ' . $error);
                wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'missing_tzs_fields', 'missing_tzs' => $missing_tzs)));
            }
        }
        
        // Check if AzamPay is enabled
        $azampay_enabled = get_option('kilismile_azampay_enabled', false);
        if (!$azampay_enabled) {
            $error = 'AzamPay is not enabled';
            error_log('Payment Debug Error: ' . $error);
            wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'azampay_disabled')));
        }
        
        // Check AzamPay credentials
        $client_id = get_option('kilismile_azampay_client_id', '');
        $client_secret = get_option('kilismile_azampay_client_secret', '');
        
        if (empty($client_id) || empty($client_secret)) {
            $error = 'AzamPay credentials not configured';
            error_log('Payment Debug Error: ' . $error);
            wp_die(json_encode(array('success' => false, 'message' => $error, 'debug' => 'azampay_not_configured')));
        }
        
        // If we get here, basic validation passed
        error_log('Payment Debug: Basic validation passed');
        wp_die(json_encode(array(
            'success' => true, 
            'message' => 'Debug validation passed - payment would be processed here',
            'debug' => 'validation_passed',
            'data' => $_POST
        )));
    }
}

// Initialize debug processor
new KiliSmile_Payment_Debug_Processor();

?>

