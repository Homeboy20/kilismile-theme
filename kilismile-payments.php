<?php
/**
 * Plugin Name: KiliSmile Payments
 * Plugin URI: https://kilismile.org
 * Description: Payment gateway integration for KiliSmile donation system. Provides PayPal and AzamPay payment processing.
 * Version: 1.0.0
 * Author: KiliSmile Organization
 * License: GPL v2 or later
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Text Domain: kilismile-payments
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if the plugin version is already active - if so, don't load theme version
if (defined('KILISMILE_PAYMENTS_ACTIVE') || class_exists('KiliSmile_Payments_Plugin')) {
    // Plugin version is already loaded, exit silently
    return;
}

// Define plugin constants only if not already defined
if (!defined('KILISMILE_PAYMENTS_VERSION')) {
    define('KILISMILE_PAYMENTS_VERSION', '1.0.0');
}
if (!defined('KILISMILE_PAYMENTS_PLUGIN_FILE')) {
    define('KILISMILE_PAYMENTS_PLUGIN_FILE', __FILE__);
}
if (!defined('KILISMILE_PAYMENTS_PLUGIN_DIR')) {
    define('KILISMILE_PAYMENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('KILISMILE_PAYMENTS_PLUGIN_URL')) {
    define('KILISMILE_PAYMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Mark plugin as active for bridge detection
if (!defined('KILISMILE_PAYMENTS_ACTIVE')) {
    define('KILISMILE_PAYMENTS_ACTIVE', true);
}

/**
 * Main plugin class
 */
if (!class_exists('KiliSmile_Payments_Plugin')) {
class KiliSmile_Payments_Plugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('init', array($this, 'init'), 1); // Very early initialization
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize payment system
        $this->init_payment_system();
        
        // Load textdomain
        load_plugin_textdomain('kilismile-payments', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    private function load_dependencies() {
        // DISABLED: Theme-based includes have been disabled to prevent conflicts
        // with the standalone kilismile-payments plugin.
        // All payment functionality should come from the plugin in wp-content/plugins/
        
        // Log that theme version is being skipped
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('KiliSmile Payments Theme: Skipping file loading - using standalone plugin');
        }
        
        return; // Exit early - no files to load
        
        // Original file loading code (disabled):
        /*
        // For development: Load from theme's plugin-includes directory
        // In production: This would be KILISMILE_PAYMENTS_PLUGIN_DIR . 'includes/'
        $includes_path = get_template_directory() . '/plugin-includes/';
        
        // Core payment classes (order matters for dependencies)
        $required_files = array(
            'payment-debug.php',               // Debug utilities (must be first)
            'payment-gateways-modern.php',     // Gateway factory and base classes
            'donation-system-modern.php',      // Modern donation system
            'payment-processor.php',           // Unified payment processor
            'paypal-integration.php',          // PayPal gateway
            'azampay-integration.php',         // AzamPay gateway
            'enhanced-azampay-integration.php' // Enhanced AzamPay
        );
        
        foreach ($required_files as $file) {
            $file_path = $includes_path . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
        */
        
        // Load admin functionality if in admin area
        if (is_admin()) {
            $this->load_admin_files();
        }
    }
    
    private function load_admin_files() {
        // Load admin interface
        $admin_file = get_template_directory() . '/admin/kilismile-payments-admin.php';
        if (file_exists($admin_file)) {
            require_once $admin_file;
        }
        
        $admin_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'admin/';
        
        // Admin files
        $admin_files = array(
            'class-payments-admin.php',
            'class-gateway-settings.php'
        );
        
        foreach ($admin_files as $file) {
            $file_path = $admin_path . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    private function init_payment_system() {
        // Load admin interface if in admin area
        if (is_admin()) {
            $this->load_admin_files();
        }
        
        // Register AJAX handlers for payment processing
        add_action('wp_ajax_kilismile_process_payment', array($this, 'handle_payment_ajax'));
        add_action('wp_ajax_nopriv_kilismile_process_payment', array($this, 'handle_payment_ajax'));
        
        // Add shortcode support
        add_shortcode('kilismile_donation_form', array($this, 'donation_form_shortcode'));
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('KiliSmile Payments: Plugin initialized successfully');
        }
    }
    
    public function activate() {
        // Create necessary database tables
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clean up scheduled events
        wp_clear_scheduled_hook('kilismile_payment_cleanup');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    private function create_tables() {
        global $wpdb;
        
        // This would create any additional tables needed by the plugin
        // For now, we rely on the donation system's existing tables
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Payment logs table
        $table_name = $wpdb->prefix . 'kilismile_payment_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            donation_id mediumint(9) NOT NULL,
            gateway varchar(50) NOT NULL,
            transaction_id varchar(255),
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            status varchar(20) NOT NULL,
            gateway_response text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY donation_id (donation_id),
            KEY gateway (gateway),
            KEY status (status)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    private function set_default_options() {
        // Set default gateway options
        add_option('kilismile_paypal_sandbox_mode', 1);
        add_option('kilismile_azampay_sandbox_mode', 1);
        add_option('kilismile_use_enhanced_azampay', 0);
        add_option('kilismile_payments_version', KILISMILE_PAYMENTS_VERSION);
    }
    
    /**
     * Handle AJAX payment processing
     */
    public function handle_payment_ajax() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payment_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
            return;
        }
        
        // For now, return a simple response
        wp_send_json_success(array(
            'message' => 'Payment system is active but not fully configured. Please configure PayPal and AzamPay settings.',
            'redirect_url' => admin_url('admin.php?page=kilismile-payments')
        ));
    }
    
    /**
     * Donation form shortcode
     */
    public function donation_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Make a Donation',
            'show_amounts' => true,
            'show_progress' => false
        ), $atts);
        
        // Include the donation form component
        $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
        if (file_exists($component_path)) {
            ob_start();
            include $component_path;
            return ob_get_clean();
        }
        
        return '<div class="kilismile-donation-notice">Donation form component not found. Please check your theme files.</div>';
    }
}
} // End class_exists check

/**
 * Initialize the plugin
 */
if (!function_exists('kilismile_payments_init')) {
function kilismile_payments_init() {
    return KiliSmile_Payments_Plugin::get_instance();
}
}

// Start the plugin only if class exists
if (class_exists('KiliSmile_Payments_Plugin')) {
    kilismile_payments_init();
}

/**
 * Helper function to check if plugin is active
 */
if (!function_exists('kilismile_payments_is_active')) {
function kilismile_payments_is_active() {
    return defined('KILISMILE_PAYMENTS_ACTIVE');
}
}

/**
 * Get plugin version
 */
if (!function_exists('kilismile_payments_version')) {
function kilismile_payments_version() {
    return KILISMILE_PAYMENTS_VERSION;
}
}

