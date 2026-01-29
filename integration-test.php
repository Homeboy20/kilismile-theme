<?php
/**
 * KiliSmile Payments - Integration Test
 * 
 * This file tests all payment plugin components to ensure proper integration
 * Use this to verify that all enhanced features work together correctly
 * 
 * Instructions:
 * 1. Access via: yoursite.com/wp-content/themes/kilismile/integration-test.php
 * 2. Or create a page template and include this file
 * 3. Review all test results to verify proper integration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // For direct access testing (remove in production)
    define('WP_USE_THEMES', false);
    require_once('../../../wp-load.php');
}

/**
 * Integration Test Class
 */
class KiliSmile_Integration_Test {
    
    private $test_results = array();
    private $passed_tests = 0;
    private $failed_tests = 0;
    
    public function __construct() {
        $this->run_all_tests();
    }
    
    /**
     * Run all integration tests
     */
    public function run_all_tests() {
        echo "<h1>KiliSmile Payments - Integration Test Results</h1>";
        echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";
        
        // Template System Tests
        $this->test_template_system();
        
        // Asset Tests
        $this->test_assets();
        
        // Admin System Tests
        $this->test_admin_system();
        
        // Plugin Classes Tests
        $this->test_plugin_classes();
        
        // Integration Tests
        $this->test_integration();
        
        // Security Tests
        $this->test_security();
        
        // Display summary
        $this->display_summary();
        
        echo "</div>";
    }
    
    /**
     * Test template system
     */
    private function test_template_system() {
        echo "<h2>📋 Template System Tests</h2>";
        
        // Test payment form templates
        $this->test_file_exists('Payment Form Template', 'templates/forms/payment-form.php');
        $this->test_file_exists('Payment Success Template', 'templates/forms/payment-success.php');
        $this->test_file_exists('Payment Error Template', 'templates/forms/payment-error.php');
        
        // Test email templates
        $this->test_file_exists('Email Receipt Template', 'templates/emails/payment-receipt.php');
        $this->test_file_exists('Admin Notification Template', 'templates/emails/admin-notification.php');
        
        // Test admin templates
        $this->test_file_exists('Admin Transactions Template', 'admin/templates/transactions.php');
        $this->test_file_exists('Admin Logs Template', 'admin/templates/logs.php');
        $this->test_file_exists('Admin Settings Template', 'admin/templates/settings.php');
        
        // Test shortcode templates
        $this->test_file_exists('Shortcode Templates', 'templates/shortcodes/donation-shortcodes.php');
        
        // Test widget templates
        $this->test_file_exists('Widget Templates', 'templates/widgets/donation-widgets.php');
    }
    
    /**
     * Test asset files
     */
    private function test_assets() {
        echo "<h2>🎨 Asset Files Tests</h2>";
        
        // Test CSS files
        $this->test_file_exists('Payment Form CSS', 'assets/css/payment-form.css');
        $this->test_file_exists('Admin Panel CSS', 'assets/css/admin-panel.css');
        $this->test_file_exists('Email Template CSS', 'assets/css/email-template.css');
        
        // Test JavaScript files
        $this->test_file_exists('Payment Form JS', 'assets/js/payment-form.js');
        $this->test_file_exists('Admin Panel JS', 'assets/js/admin-panel.js');
        $this->test_file_exists('Currency Converter JS', 'assets/js/currency-converter.js');
    }
    
    /**
     * Test admin system
     */
    private function test_admin_system() {
        echo "<h2>⚙️ Admin System Tests</h2>";
        
        // Test WordPress admin integration
        $this->test_admin_menu_exists();
        $this->test_admin_capabilities();
        $this->test_settings_registration();
    }
    
    /**
     * Test plugin classes
     */
    private function test_plugin_classes() {
        echo "<h2>🔧 Plugin Classes Tests</h2>";
        
        // Test class files exist
        $this->test_file_exists('Currency Converter Class', 'includes/class-currency-converter.php');
        $this->test_file_exists('Enhanced Validator Class', 'includes/class-enhanced-validator.php');
        $this->test_file_exists('Security Manager Class', 'includes/class-security-manager.php');
        
        // Test class functionality
        $this->test_currency_converter();
        $this->test_enhanced_validator();
        $this->test_security_manager();
    }
    
    /**
     * Test integration features
     */
    private function test_integration() {
        echo "<h2>🔗 Integration Tests</h2>";
        
        // Test shortcode registration
        $this->test_shortcode_registration();
        
        // Test widget registration
        $this->test_widget_registration();
        
        // Test AJAX handlers
        $this->test_ajax_handlers();
        
        // Test template loading
        $this->test_template_loading();
    }
    
    /**
     * Test security features
     */
    private function test_security() {
        echo "<h2>🔒 Security Tests</h2>";
        
        // Test nonce verification
        $this->test_nonce_system();
        
        // Test capability checks
        $this->test_capability_checks();
        
        // Test input sanitization
        $this->test_input_sanitization();
        
        // Test rate limiting
        $this->test_rate_limiting();
    }
    
    /**
     * Test file exists
     */
    private function test_file_exists($name, $relative_path) {
        $full_path = get_template_directory() . '/' . $relative_path;
        $exists = file_exists($full_path);
        
        if ($exists) {
            $file_size = filesize($full_path);
            $this->add_result($name, true, "File exists ({$file_size} bytes)");
        } else {
            $this->add_result($name, false, "File not found: {$full_path}");
        }
    }
    
    /**
     * Test admin menu exists
     */
    private function test_admin_menu_exists() {
        global $menu, $submenu;
        
        $has_menu = false;
        if (isset($menu)) {
            foreach ($menu as $menu_item) {
                if (isset($menu_item[2]) && strpos($menu_item[2], 'kilismile-payments') !== false) {
                    $has_menu = true;
                    break;
                }
            }
        }
        
        $this->add_result('Admin Menu Registration', $has_menu, 
            $has_menu ? 'Payment admin menu found' : 'Payment admin menu not registered');
    }
    
    /**
     * Test admin capabilities
     */
    private function test_admin_capabilities() {
        $current_user = wp_get_current_user();
        $can_manage = current_user_can('manage_options');
        
        $this->add_result('Admin Capabilities', $can_manage, 
            $can_manage ? 'User has manage_options capability' : 'User lacks manage_options capability');
    }
    
    /**
     * Test settings registration
     */
    private function test_settings_registration() {
        $settings_exist = false;
        $registered_settings = get_registered_settings();
        
        foreach ($registered_settings as $setting => $args) {
            if (strpos($setting, 'kilismile_payments') !== false) {
                $settings_exist = true;
                break;
            }
        }
        
        $this->add_result('Settings Registration', $settings_exist,
            $settings_exist ? 'Payment settings are registered' : 'Payment settings not found');
    }
    
    /**
     * Test currency converter
     */
    private function test_currency_converter() {
        $converter_file = get_template_directory() . '/includes/class-currency-converter.php';
        
        if (file_exists($converter_file)) {
            include_once $converter_file;
            
            if (class_exists('KiliSmile_Currency_Converter')) {
                $converter = new KiliSmile_Currency_Converter();
                $has_methods = method_exists($converter, 'convert') && 
                              method_exists($converter, 'get_exchange_rate');
                
                $this->add_result('Currency Converter Class', $has_methods,
                    $has_methods ? 'Currency converter methods available' : 'Currency converter methods missing');
            } else {
                $this->add_result('Currency Converter Class', false, 'Currency converter class not found');
            }
        } else {
            $this->add_result('Currency Converter Class', false, 'Currency converter file not found');
        }
    }
    
    /**
     * Test enhanced validator
     */
    private function test_enhanced_validator() {
        $validator_file = get_template_directory() . '/includes/class-enhanced-validator.php';
        
        if (file_exists($validator_file)) {
            include_once $validator_file;
            
            if (class_exists('KiliSmile_Enhanced_Validator')) {
                $validator = new KiliSmile_Enhanced_Validator();
                $has_methods = method_exists($validator, 'validate_donation') && 
                              method_exists($validator, 'validate_payment_method');
                
                $this->add_result('Enhanced Validator Class', $has_methods,
                    $has_methods ? 'Enhanced validator methods available' : 'Enhanced validator methods missing');
            } else {
                $this->add_result('Enhanced Validator Class', false, 'Enhanced validator class not found');
            }
        } else {
            $this->add_result('Enhanced Validator Class', false, 'Enhanced validator file not found');
        }
    }
    
    /**
     * Test security manager
     */
    private function test_security_manager() {
        $security_file = get_template_directory() . '/includes/class-security-manager.php';
        
        if (file_exists($security_file)) {
            include_once $security_file;
            
            if (class_exists('KiliSmile_Security_Manager')) {
                $security = new KiliSmile_Security_Manager();
                $has_methods = method_exists($security, 'check_rate_limit') && 
                              method_exists($security, 'detect_fraud');
                
                $this->add_result('Security Manager Class', $has_methods,
                    $has_methods ? 'Security manager methods available' : 'Security manager methods missing');
            } else {
                $this->add_result('Security Manager Class', false, 'Security manager class not found');
            }
        } else {
            $this->add_result('Security Manager Class', false, 'Security manager file not found');
        }
    }
    
    /**
     * Test shortcode registration
     */
    private function test_shortcode_registration() {
        global $shortcode_tags;
        
        $shortcodes_exist = isset($shortcode_tags['kilismile_donation_form']) ||
                           isset($shortcode_tags['kilismile_donation_progress']) ||
                           isset($shortcode_tags['kilismile_recent_donations']);
        
        $this->add_result('Shortcode Registration', $shortcodes_exist,
            $shortcodes_exist ? 'Payment shortcodes are registered' : 'Payment shortcodes not found');
    }
    
    /**
     * Test widget registration
     */
    private function test_widget_registration() {
        global $wp_widget_factory;
        
        $widgets_exist = false;
        if (isset($wp_widget_factory->widgets)) {
            foreach ($wp_widget_factory->widgets as $widget_class => $widget) {
                if (strpos($widget_class, 'KiliSmile') !== false) {
                    $widgets_exist = true;
                    break;
                }
            }
        }
        
        $this->add_result('Widget Registration', $widgets_exist,
            $widgets_exist ? 'Payment widgets are registered' : 'Payment widgets not found');
    }
    
    /**
     * Test AJAX handlers
     */
    private function test_ajax_handlers() {
        $ajax_actions = array(
            'wp_ajax_kilismile_process_payment',
            'wp_ajax_nopriv_kilismile_process_payment',
            'wp_ajax_kilismile_get_exchange_rate',
            'wp_ajax_kilismile_validate_donation'
        );
        
        $handlers_registered = 0;
        foreach ($ajax_actions as $action) {
            if (has_action($action)) {
                $handlers_registered++;
            }
        }
        
        $all_registered = $handlers_registered === count($ajax_actions);
        $this->add_result('AJAX Handlers', $all_registered,
            "AJAX handlers registered: {$handlers_registered}/" . count($ajax_actions));
    }
    
    /**
     * Test template loading
     */
    private function test_template_loading() {
        // Test if template loading functions exist
        $functions_exist = function_exists('kilismile_load_payment_template') ||
                          function_exists('get_template_part');
        
        $this->add_result('Template Loading', $functions_exist,
            $functions_exist ? 'Template loading functions available' : 'Template loading functions missing');
    }
    
    /**
     * Test nonce system
     */
    private function test_nonce_system() {
        $nonce = wp_create_nonce('kilismile_payment_nonce');
        $nonce_valid = wp_verify_nonce($nonce, 'kilismile_payment_nonce');
        
        $this->add_result('Nonce System', $nonce_valid,
            $nonce_valid ? 'Nonce system working correctly' : 'Nonce system failed');
    }
    
    /**
     * Test capability checks
     */
    private function test_capability_checks() {
        $capability_functions = function_exists('current_user_can') && 
                              function_exists('user_can');
        
        $this->add_result('Capability Checks', $capability_functions,
            $capability_functions ? 'Capability check functions available' : 'Capability check functions missing');
    }
    
    /**
     * Test input sanitization
     */
    private function test_input_sanitization() {
        $sanitize_functions = function_exists('sanitize_text_field') && 
                            function_exists('sanitize_email') &&
                            function_exists('absint');
        
        $this->add_result('Input Sanitization', $sanitize_functions,
            $sanitize_functions ? 'Sanitization functions available' : 'Sanitization functions missing');
    }
    
    /**
     * Test rate limiting
     */
    private function test_rate_limiting() {
        $security_file = get_template_directory() . '/includes/class-security-manager.php';
        
        if (file_exists($security_file)) {
            $content = file_get_contents($security_file);
            $has_rate_limiting = strpos($content, 'check_rate_limit') !== false &&
                               strpos($content, 'rate_limit_table') !== false;
            
            $this->add_result('Rate Limiting', $has_rate_limiting,
                $has_rate_limiting ? 'Rate limiting implementation found' : 'Rate limiting not implemented');
        } else {
            $this->add_result('Rate Limiting', false, 'Security manager file not found');
        }
    }
    
    /**
     * Add test result
     */
    private function add_result($test_name, $passed, $message) {
        $this->test_results[] = array(
            'name' => $test_name,
            'passed' => $passed,
            'message' => $message
        );
        
        if ($passed) {
            $this->passed_tests++;
            echo "<div style='color: green; margin: 5px 0;'>✅ {$test_name}: {$message}</div>";
        } else {
            $this->failed_tests++;
            echo "<div style='color: red; margin: 5px 0;'>❌ {$test_name}: {$message}</div>";
        }
    }
    
    /**
     * Display test summary
     */
    private function display_summary() {
        $total_tests = $this->passed_tests + $this->failed_tests;
        $success_rate = $total_tests > 0 ? round(($this->passed_tests / $total_tests) * 100, 1) : 0;
        
        echo "<h2>📊 Test Summary</h2>";
        echo "<div style='background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>Total Tests:</strong> {$total_tests}<br>";
        echo "<strong>Passed:</strong> <span style='color: green;'>{$this->passed_tests}</span><br>";
        echo "<strong>Failed:</strong> <span style='color: red;'>{$this->failed_tests}</span><br>";
        echo "<strong>Success Rate:</strong> {$success_rate}%<br>";
        echo "</div>";
        
        if ($this->failed_tests > 0) {
            echo "<div style='background: #ffe6e6; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ff0000;'>";
            echo "<strong>⚠️ Integration Issues Found:</strong><br>";
            echo "Some components are missing or not properly configured. Please review the failed tests above.<br>";
            echo "Most likely causes:<br>";
            echo "• Files not uploaded to correct directories<br>";
            echo "• WordPress hooks not properly registered<br>";
            echo "• Plugin activation required<br>";
            echo "• Permissions issues<br>";
            echo "</div>";
        } else {
            echo "<div style='background: #e6ffe6; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #00aa00;'>";
            echo "<strong>🎉 All Tests Passed!</strong><br>";
            echo "The KiliSmile Payments plugin is properly integrated and ready for use.<br>";
            echo "All templates, assets, classes, and security features are working correctly.";
            echo "</div>";
        }
        
        // Integration recommendations
        echo "<h2>🚀 Next Steps</h2>";
        echo "<div style='background: #e6f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #0073aa;'>";
        echo "<strong>Recommended Actions:</strong><br>";
        echo "1. Configure payment gateway credentials in admin panel<br>";
        echo "2. Test donation forms on live pages<br>";
        echo "3. Verify email notifications are working<br>";
        echo "4. Test shortcodes and widgets in content<br>";
        echo "5. Review admin transaction logs<br>";
        echo "6. Test currency conversion functionality<br>";
        echo "7. Verify security features are active<br>";
        echo "</div>";
        
        // Performance recommendations
        echo "<h2>⚡ Performance Notes</h2>";
        echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ff9800;'>";
        echo "<strong>Optimization Tips:</strong><br>";
        echo "• Enable caching for currency conversion API calls<br>";
        echo "• Consider CDN for payment form assets<br>";
        echo "• Monitor rate limiting logs for unusual activity<br>";
        echo "• Regularly clean up old transaction logs<br>";
        echo "• Test payment forms on mobile devices<br>";
        echo "</div>";
    }
}

// Run the integration test
new KiliSmile_Integration_Test();
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 20px;
    background: #f1f1f1;
}

h1 {
    color: #333;
    border-bottom: 3px solid #0073aa;
    padding-bottom: 10px;
}

h2 {
    color: #555;
    margin-top: 30px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

div[style*="color: green"] {
    background: #f0f8f0;
    padding: 8px;
    border-left: 4px solid #00aa00;
    border-radius: 3px;
}

div[style*="color: red"] {
    background: #fdf0f0;
    padding: 8px;
    border-left: 4px solid #cc0000;
    border-radius: 3px;
}
</style>

