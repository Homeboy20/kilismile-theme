<?php
/**
 * Plugin Name: KiliSmile Payments
 * Plugin URI: https://kilismile.org/payments
 * Description: Unified payment solution for KiliSmile Organization with AzamPay, PayPal, and mobile money integration for donations and corporate subscriptions.
 * Version: 1.0.0
 * Author: KiliSmile Organization
 * Author URI: https://kilismile.org
 * Text Domain: kilismile-payments
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KILISMILE_PAYMENTS_VERSION', '1.0.0');
define('KILISMILE_PAYMENTS_PLUGIN_FILE', __FILE__);
define('KILISMILE_PAYMENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KILISMILE_PAYMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KILISMILE_PAYMENTS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main KiliSmile Payments Plugin Class
 */
class KiliSmile_Payments_Plugin {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Payment gateways
     */
    public $gateways = array();
    
    /**
     * Database instance
     */
    public $database = null;
    
    /**
     * Logger instance
     */
    public $logger = null;
    
    /**
     * Admin instance
     */
    public $admin = null;
    
    /**
     * AJAX instance
     */
    public $ajax = null;
    
    /**
     * Plugin settings
     */
    public $settings = array();
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
        $this->init_database();
        $this->init_logger();
        $this->init_settings();
        $this->init_gateways();
        $this->init_admin();
        $this->init_ajax();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'), 1); // Load translations early
        add_action('init', array($this, 'init_plugin'), 10);
        
        // Frontend assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Register with theme hooks
        add_action('kilismile_register_payment_hooks', array($this, 'register_payment_hooks'));
        add_action('kilismile_enqueue_payment_scripts', array($this, 'enqueue_payment_scripts'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
        
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Core classes
        $this->include_file('includes/class-database.php');
        $this->include_file('includes/class-logger.php');
        $this->include_file('includes/class-admin.php');
        $this->include_file('includes/class-ajax.php');
        
        // Abstract gateway class
        $this->include_file('includes/abstracts/class-payment-gateway.php');
        
        // Gateway implementations
        $this->include_file('includes/gateways/class-azampay-gateway.php');
        $this->include_file('includes/gateways/class-paypal-gateway.php');
    }
    
    /**
     * Include file if it exists
     */
    private function include_file($file) {
        $path = KILISMILE_PAYMENTS_PLUGIN_DIR . $file;
        if (file_exists($path)) {
            require_once $path;
        }
    }
    
    /**
     * Initialize plugin settings
     */
    private function init_settings() {
        $this->settings = get_option('kilismile_payments_options', array());
    }
    
    /**
     * Initialize database
     */
    private function init_database() {
        if (class_exists('KiliSmile_Payments_Database')) {
            $this->database = new KiliSmile_Payments_Database();
        }
    }
    
    /**
     * Initialize logger
     */
    private function init_logger() {
        if (class_exists('KiliSmile_Payments_Logger') && $this->database) {
            $this->logger = new KiliSmile_Payments_Logger($this->database);
        }
    }
    
    /**
     * Initialize admin interface
     */
    private function init_admin() {
        if (is_admin() && class_exists('KiliSmile_Payments_Admin')) {
            $this->admin = new KiliSmile_Payments_Admin($this);
        }
    }
    
    /**
     * Initialize AJAX handlers
     */
    private function init_ajax() {
        if (class_exists('KiliSmile_Payments_AJAX')) {
            $this->ajax = new KiliSmile_Payments_AJAX($this);
            
            // Initialize shortcodes
            if (method_exists($this->ajax, 'init_shortcodes')) {
                $this->ajax->init_shortcodes();
            }
        }
    }
    
    /**
     * Initialize payment gateways
     */
    private function init_gateways() {
        $gateways = array();
        
        if (class_exists('KiliSmile_AzamPay_Gateway')) {
            $azampay = new KiliSmile_AzamPay_Gateway();
            if (method_exists($azampay, 'init')) {
                $azampay->init();
            }
            $gateways['azampay'] = $azampay;
        }
        
        if (class_exists('KiliSmile_PayPal_Gateway')) {
            $paypal = new KiliSmile_PayPal_Gateway();
            if (method_exists($paypal, 'init')) {
                $paypal->init();
            }
            $gateways['paypal'] = $paypal;
        }
        
        $this->gateways = apply_filters('kilismile_payment_gateways', $gateways);
    }
    
    /**
     * Initialize the plugin
     */
    public function init_plugin() {
        // Set global flag that plugin is active
        define('KILISMILE_PAYMENTS_ACTIVE', true);
        
        // Initialize database tables if needed
        if ($this->database && get_option('kilismile_payments_db_version') !== KILISMILE_PAYMENTS_VERSION) {
            $this->database->create_tables();
            update_option('kilismile_payments_db_version', KILISMILE_PAYMENTS_VERSION);
        }
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'kilismile-payments',
            false,
            dirname(KILISMILE_PAYMENTS_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Register payment hooks with theme
     */
    public function register_payment_hooks() {
        // Register our payment processing action
        add_action('wp_ajax_kilismile_process_payment', array($this, 'process_payment_ajax'));
        add_action('wp_ajax_nopriv_kilismile_process_payment', array($this, 'process_payment_ajax'));
        
        // Set the payment action for theme forms
        add_action('wp_footer', function() {
            echo '<script>window.kilismilePaymentAction = "kilismile_process_payment";</script>';
        });
        
        // Add donation form filter
        add_filter('kilismile_donation_form', array($this, 'provide_donation_form'), 10, 2);
        
        // Add corporate subscription form filter
        add_filter('kilismile_corporate_subscription_form', array($this, 'provide_subscription_form'), 10, 2);
    }
    
    /**
     * Enqueue payment scripts
     */
    public function enqueue_payment_scripts() {
        $script_path = KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/js/payments.js';
        $style_path = KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/css/payments.css';
        
        if (file_exists(KILISMILE_PAYMENTS_PLUGIN_DIR . 'assets/js/payments.js')) {
            wp_enqueue_script(
                'kilismile-payments',
                $script_path,
                array('jquery'),
                KILISMILE_PAYMENTS_VERSION,
                true
            );
            
            wp_localize_script('kilismile-payments', 'kilismilePayments', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kilismile_payment_nonce'),
                'currency_rates' => $this->get_currency_rates(),
                'gateways' => $this->get_enabled_gateways_config()
            ));
        }
        
        if (file_exists(KILISMILE_PAYMENTS_PLUGIN_DIR . 'assets/css/payments.css')) {
            wp_enqueue_style(
                'kilismile-payments',
                $style_path,
                array(),
                KILISMILE_PAYMENTS_VERSION
            );
        }
    }
    
    /**
     * Process payment AJAX request
     */
    public function process_payment_ajax() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_payment_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security verification failed.', 'kilismile-payments')
            ));
            return;
        }
        
        // Get gateway
        $gateway_id = sanitize_text_field($_POST['payment_gateway'] ?? '');
        if (!isset($this->gateways[$gateway_id])) {
            wp_send_json_error(array(
                'message' => __('Invalid payment gateway.', 'kilismile-payments')
            ));
            return;
        }
        
        $gateway = $this->gateways[$gateway_id];
        
        // Process payment
        try {
            $result = $gateway->process_payment($_POST);
            
            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ));
            } else {
                wp_send_json_success($result);
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Provide donation form via filter
     */
    public function provide_donation_form($form, $args) {
        if (empty($form)) {
            $template_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'templates/donation-form.php';
            if (file_exists($template_path)) {
                ob_start();
                include $template_path;
                return ob_get_clean();
            }
        }
        return $form;
    }
    
    /**
     * Provide subscription form via filter
     */
    public function provide_subscription_form($form, $args) {
        if (empty($form)) {
            $template_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'templates/subscription-form.php';
            if (file_exists($template_path)) {
                ob_start();
                include $template_path;
                return ob_get_clean();
            }
        }
        return $form;
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('KiliSmile Payments', 'kilismile-payments'),
            __('Payments', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments',
            array($this, 'admin_page'),
            'dashicons-money-alt',
            30
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Transactions', 'kilismile-payments'),
            __('Transactions', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-transactions',
            array($this, 'transactions_page')
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Settings', 'kilismile-payments'),
            __('Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        $view_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'includes/admin/views/dashboard.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h1>KiliSmile Payments Dashboard</h1><p>Dashboard view is being prepared...</p></div>';
        }
    }
    
    /**
     * Transactions page callback
     */
    public function transactions_page() {
        $view_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'includes/admin/views/transactions.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h1>Transactions</h1><p>Transactions view is being prepared...</p></div>';
        }
    }
    
    /**
     * Settings page callback
     */
    public function settings_page() {
        $view_path = KILISMILE_PAYMENTS_PLUGIN_DIR . 'includes/admin/views/settings.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h1>Payment Settings</h1><p>Settings view is being prepared...</p></div>';
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'kilismile-payments') !== false) {
            $admin_js = KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/js/admin.js';
            $admin_css = KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/css/admin.css';
            
            if (file_exists(KILISMILE_PAYMENTS_PLUGIN_DIR . 'assets/js/admin.js')) {
                wp_enqueue_script(
                    'kilismile-payments-admin',
                    $admin_js,
                    array('jquery'),
                    KILISMILE_PAYMENTS_VERSION,
                    true
                );
            }
            
            if (file_exists(KILISMILE_PAYMENTS_PLUGIN_DIR . 'assets/css/admin.css')) {
                wp_enqueue_style(
                    'kilismile-payments-admin',
                    $admin_css,
                    array(),
                    KILISMILE_PAYMENTS_VERSION
                );
            }
        }
    }
    
    /**
     * Get currency rates
     */
    private function get_currency_rates() {
        return array(
            'USD_to_TZS' => floatval(get_option('kilismile_usd_to_tzs_rate', 2350)),
            'TZS_to_USD' => floatval(get_option('kilismile_tzs_to_usd_rate', 0.000426))
        );
    }
    
    /**
     * Get enabled gateways configuration
     */
    private function get_enabled_gateways_config() {
        $config = array();
        foreach ($this->gateways as $id => $gateway) {
            if (method_exists($gateway, 'is_enabled') && $gateway->is_enabled()) {
                $config[$id] = array(
                    'id' => $id,
                    'title' => method_exists($gateway, 'get_title') ? $gateway->get_title() : ucfirst($id),
                    'description' => method_exists($gateway, 'get_description') ? $gateway->get_description() : '',
                    'supports' => method_exists($gateway, 'get_supported_features') ? $gateway->get_supported_features() : array()
                );
            }
        }
        return $config;
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only load on donation/payment related pages
        if (!is_page(array('donate', 'donation', 'corporate', 'partnerships')) && 
            !has_shortcode(get_post()->post_content ?? '', 'kilismile_donation_form') &&
            !is_single()) {
            return;
        }
        
        wp_enqueue_script('jquery');
        
        // Enqueue frontend JavaScript
        wp_enqueue_script(
            'kilismile-payments-frontend',
            KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            KILISMILE_PAYMENTS_VERSION,
            true
        );
        
        // Localize script with configuration
        wp_localize_script('kilismile-payments-frontend', 'kilismile_payments', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_payment_nonce'),
            'gateways' => $this->get_available_gateways(),
            'currency_symbols' => array(
                'USD' => '$',
                'TZS' => 'TSh',
                'EUR' => '€',
                'GBP' => '£'
            ),
            'strings' => array(
                'processing' => __('Processing payment...', 'kilismile-payments'),
                'redirecting' => __('Redirecting to payment gateway...', 'kilismile-payments'),
                'payment_successful' => __('Payment completed successfully!', 'kilismile-payments'),
                'payment_failed' => __('Payment failed. Please try again.', 'kilismile-payments'),
                'payment_cancelled' => __('Payment was cancelled.', 'kilismile-payments'),
                'network_error' => __('Network error. Please check your connection.', 'kilismile-payments'),
                'validation_error' => __('Please correct the errors below.', 'kilismile-payments'),
                'min_amount_error' => __('Amount must be at least %s', 'kilismile-payments'),
                'phone_format_error' => __('Please enter a valid Tanzanian phone number', 'kilismile-payments'),
                'email_format_error' => __('Please enter a valid email address', 'kilismile-payments')
            )
        ));
        
        // Enqueue frontend CSS
        wp_enqueue_style(
            'kilismile-payments-frontend',
            KILISMILE_PAYMENTS_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            KILISMILE_PAYMENTS_VERSION
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        if ($this->database) {
            $this->database->create_tables();
        }
        
        // Set default settings
        $defaults = array(
            'test_mode' => 1,
            'default_currency' => 'USD',
            'logging_level' => 'info',
            'file_logging' => 1,
            'email_alerts' => 1
        );
        
        $existing_options = get_option('kilismile_payments_options', array());
        $options = array_merge($defaults, $existing_options);
        update_option('kilismile_payments_options', $options);
        
        // Set database version
        update_option('kilismile_payments_db_version', KILISMILE_PAYMENTS_VERSION);
        
        // Flush rewrite rules for webhooks
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up scheduled events
        wp_clear_scheduled_hook('kilismile_payments_cleanup');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Get gateway instance
     */
    public function get_gateway($gateway_id) {
        return isset($this->gateways[$gateway_id]) ? $this->gateways[$gateway_id] : null;
    }
    
    /**
     * Get all gateways
     */
    public function get_gateways() {
        return $this->gateways;
    }
    
    /**
     * Get available gateways for frontend
     */
    public function get_available_gateways() {
        $available = array();
        foreach ($this->gateways as $id => $gateway) {
            if ($gateway->is_enabled()) {
                $available[$id] = array(
                    'id' => $id,
                    'title' => $gateway->get_title(),
                    'description' => $gateway->get_description(),
                    'supports' => $gateway->get_supported_features()
                );
            }
        }
        return $available;
    }
    
    /**
     * Get enabled gateways
     */
    public function get_enabled_gateways() {
        $enabled = array();
        foreach ($this->gateways as $id => $gateway) {
            if (method_exists($gateway, 'is_enabled') && $gateway->is_enabled()) {
                $enabled[$id] = $gateway;
            }
        }
        return $enabled;
    }
    
    /**
     * Get database instance
     */
    public function get_database() {
        return $this->database;
    }
    
    /**
     * Get logger instance
     */
    public function get_logger() {
        return $this->logger;
    }
    
    /**
     * Log message using plugin logger
     */
    public function log($message, $level = 'info', $context = array(), $gateway = '', $transaction_id = null) {
        if ($this->logger) {
            return $this->logger->log($message, $level, $context, $gateway, $transaction_id);
        }
        return false;
    }
    
    /**
     * Get admin instance
     */
    public function get_admin() {
        return $this->admin;
    }
    
    /**
     * Get AJAX instance
     */
    public function get_ajax() {
        return $this->ajax;
    }
}

/**
 * Initialize the plugin
 */
function kilismile_payments() {
    return KiliSmile_Payments_Plugin::get_instance();
}

// Initialize
kilismile_payments();

/**
 * Plugin loaded hook for other plugins/themes
 */
do_action('kilismile_payments_loaded');

