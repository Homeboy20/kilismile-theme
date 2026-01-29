<?php
/**
 * Payment Plugin Bridge
 * Provides integration bridge between theme and kilismile-payments plugin
 * Registers AJAX handlers and ensures payment functionality works
 */

if (!defined('ABSPATH')) exit;

class KiliSmile_Payment_Plugin_Bridge {
    
    private static $instance = null;
    private $plugin_active = false;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('init', array($this, 'init'), 5); // Early initialization
        add_action('plugins_loaded', array($this, 'check_plugin_availability'), 10);
    }
    
    public function init() {
        // Check if the kilismile-payments plugin is active
        $this->plugin_active = $this->is_plugin_active();
        
        // If plugin is not active, provide fallback AJAX handlers
        if (!$this->plugin_active) {
            $this->register_fallback_handlers();
        }
        
        // Always ensure these hooks are available for compatibility
        $this->ensure_payment_hooks();
    }
    
    public function check_plugin_availability() {
        $this->plugin_active = $this->is_plugin_active();
        
        if (!$this->plugin_active) {
            // Show admin notice
            add_action('admin_notices', array($this, 'plugin_missing_notice'));
        }
    }
    
    private function is_plugin_active() {
        // Check if the plugin classes are available or plugin is loaded
        return class_exists('KiliSmile_Payment_Gateway_Factory') || 
               class_exists('KiliSmile_Payments_Plugin') ||
               function_exists('kilismile_payments_init') ||
               defined('KILISMILE_PAYMENTS_VERSION') ||
               defined('KILISMILE_PAYMENTS_ACTIVE');
    }
    
    public function register_fallback_handlers() {
        // Load theme-based payment components as fallback
        $this->load_fallback_components();
        
        // Register AJAX handlers if not already registered by plugin
        if (!has_action('wp_ajax_kilismile_process_payment')) {
            add_action('wp_ajax_kilismile_process_payment', array($this, 'handle_payment_ajax'));
            add_action('wp_ajax_nopriv_kilismile_process_payment', array($this, 'handle_payment_ajax'));
        }
        
        if (!has_action('wp_ajax_kilismile_check_payment_status')) {
            add_action('wp_ajax_kilismile_check_payment_status', array($this, 'handle_status_check_ajax'));
            add_action('wp_ajax_nopriv_kilismile_check_payment_status', array($this, 'handle_status_check_ajax'));
        }
    }
    
    private function load_fallback_components() {
        $theme_dir = get_template_directory();
        
        // Load payment components if they exist in theme (fallback mode)
        $fallback_files = array(
            '/includes/payment-gateways-modern.php',
            '/includes/payment-processor.php',
            '/includes/paypal-integration.php',
            '/includes/azampay-integration.php',
            '/includes/donation-system-modern.php'
        );
        
        foreach ($fallback_files as $file) {
            $file_path = $theme_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
        
        // Initialize payment processor if class exists
        if (class_exists('KiliSmile_Payment_Processor')) {
            new KiliSmile_Payment_Processor();
        }
    }
    
    public function ensure_payment_hooks() {
        // Ensure these functions are available for backward compatibility
        if (!function_exists('kilismile_get_payment_gateways')) {
            function kilismile_get_payment_gateways() {
                if (class_exists('KiliSmile_Payment_Gateway_Factory')) {
                    $factory = new KiliSmile_Payment_Gateway_Factory();
                    return $factory->get_available_gateways();
                }
                return array(); // Empty if no gateways available
            }
        }
        
        if (!function_exists('kilismile_process_payment_request')) {
            function kilismile_process_payment_request($data) {
                if (class_exists('KiliSmile_Payment_Processor')) {
                    $processor = new KiliSmile_Payment_Processor();
                    return $processor->process_payment($data);
                }
                return array('success' => false, 'message' => 'Payment processor not available');
            }
        }
    }
    
    public function handle_payment_ajax() {
        // Delegate to plugin if available
        if ($this->plugin_active && class_exists('KiliSmile_Payment_Processor')) {
            $processor = new KiliSmile_Payment_Processor();
            if (method_exists($processor, 'ajax_process_payment')) {
                return $processor->ajax_process_payment();
            }
        }
        
        // Fallback handling
        $this->fallback_payment_ajax();
    }
    
    public function handle_status_check_ajax() {
        // Delegate to plugin if available
        if ($this->plugin_active && class_exists('KiliSmile_Payment_Processor')) {
            $processor = new KiliSmile_Payment_Processor();
            if (method_exists($processor, 'ajax_check_payment_status')) {
                return $processor->ajax_check_payment_status();
            }
        }
        
        // Fallback handling
        wp_send_json_error('Payment status check not available');
    }
    
    private function fallback_payment_ajax() {
        // Basic fallback payment processing
        wp_send_json_error('Payment processing is currently unavailable. Please install the kilismile-payments plugin.');
    }
    
    public function plugin_missing_notice() {
        ?>
        <div class="notice notice-warning">
            <p>
                <strong>KiliSmile Payments:</strong> 
                Payment functionality has been moved to the <code>kilismile-payments</code> plugin. 
                Please install and activate it to enable donation processing.
                <a href="<?php echo admin_url('plugins.php'); ?>">View Plugins</a>
            </p>
        </div>
        <?php
    }
    
    public function is_payment_system_available() {
        return $this->plugin_active || class_exists('KiliSmile_Payment_Processor');
    }
}

// Initialize the bridge
KiliSmile_Payment_Plugin_Bridge::get_instance();

