<?php
/**
 * AzamPay Integration Settings Page
 * Allows administrators to configure the AzamPay integration
 */

if (!defined('ABSPATH')) exit;

class KiliSmile_AzamPay_Settings {
    private $settings_key = 'kilismile_azampay_settings';
    private $settings_page;
    
    public function __construct() {
        // Add settings page to admin menu
        add_action('admin_menu', array($this, 'add_settings_page'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add AJAX handlers for testing connection
        add_action('wp_ajax_test_azampay_admin_connection', array($this, 'test_connection_ajax'));
    }
    
    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        // Attach AzamPay settings under the main KiliSmile Payments menu
        $this->settings_page = add_submenu_page(
            'kilismile-payments', // Parent slug (KiliSmile Payments)
            'AzamPay Settings', // Page title
            'AzamPay Settings', // Menu title
            'manage_options', // Capability
            'kilismile-azampay-settings', // Menu slug
            array($this, 'render_settings_page') // Callback
        );
        
        // Add admin scripts
        add_action('admin_print_styles-' . $this->settings_page, array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style('kilismile-admin-styles', get_template_directory_uri() . '/admin/css/admin-styles.css');
        wp_enqueue_script('kilismile-admin-scripts', get_template_directory_uri() . '/admin/js/admin-scripts.js', array('jquery'), null, true);
        
        // Add settings for AJAX
        wp_localize_script('kilismile-admin-scripts', 'kiliSmileAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_admin_nonce')
        ));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // AzamPay settings
        register_setting($this->settings_key, 'kilismile_azampay_app_name');
        register_setting($this->settings_key, 'kilismile_azampay_client_id');
        register_setting($this->settings_key, 'kilismile_azampay_client_secret');
        register_setting($this->settings_key, 'kilismile_azampay_partner_id');
        register_setting($this->settings_key, 'kilismile_azampay_vendor_name');
        register_setting($this->settings_key, 'kilismile_azampay_sandbox');
        register_setting($this->settings_key, 'kilismile_azampay_api_version');
        register_setting($this->settings_key, 'kilismile_azampay_logo_url');
        
        // Register integration toggle so the Integration Version radio persists
        register_setting('kilismile_integration_options', 'kilismile_use_enhanced_azampay');
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Get current values
        $app_name = get_option('kilismile_azampay_app_name', '');
        $client_id = get_option('kilismile_azampay_client_id', '');
        $client_secret = get_option('kilismile_azampay_client_secret', '');
        $partner_id = get_option('kilismile_azampay_partner_id', '');
        $vendor_name = get_option('kilismile_azampay_vendor_name', 'KiliSmile Organization');
        $sandbox = get_option('kilismile_azampay_sandbox', true);
        $api_version = get_option('kilismile_azampay_api_version', 'v1');
        $logo_url = get_option('kilismile_azampay_logo_url', get_site_icon_url());
        
        // Is enhanced version enabled?
        $use_enhanced = get_option('kilismile_use_enhanced_azampay', false);
        
        ?>
        <div class="wrap kilismile-admin-page">
            <h1><span class="dashicons dashicons-money-alt"></span> AzamPay Integration Settings</h1>
            
            <div class="kilismile-admin-container">
                <div class="kilismile-admin-header">
                    <div class="kilismile-admin-header-content">
                        <h2>Configure AzamPay Integration</h2>
                        <p>Configure your AzamPay integration settings to accept mobile money payments in Tanzania Shillings (TZS).</p>
                    </div>
                </div>
                
                <!-- Enhanced Version Toggle -->
                <div class="kilismile-admin-section">
                    <h3>Integration Version</h3>
                    <p>Choose which version of the AzamPay integration to use.</p>
                    
                    <form method="post" action="options.php">
                        <?php settings_fields('kilismile_integration_options'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Integration Version</th>
                                <td>
                                    <label>
                                        <input type="radio" name="kilismile_use_enhanced_azampay" value="0" <?php checked($use_enhanced, false); ?>>
                                        Standard Integration
                                    </label>
                                    <br>
                                    <label>
                                        <input type="radio" name="kilismile_use_enhanced_azampay" value="1" <?php checked($use_enhanced, true); ?>>
                                        Enhanced Integration (Recommended)
                                    </label>
                                    <p class="description">The enhanced version includes improved error handling, better logging, and more payment options.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Integration Version'); ?>
                    </form>
                </div>
                
                <!-- API Credentials -->
                <div class="kilismile-admin-section">
                    <h3>API Credentials</h3>
                    <p>Enter your AzamPay API credentials. You can obtain these from your AzamPay dashboard.</p>
                    
                    <form method="post" action="options.php">
                        <?php settings_fields($this->settings_key); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">App Name</th>
                                <td>
                                    <input type="text" name="kilismile_azampay_app_name" value="<?php echo esc_attr($app_name); ?>" class="regular-text">
                                    <p class="description">The registered application name provided by AzamPay.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Client ID</th>
                                <td>
                                    <input type="text" name="kilismile_azampay_client_id" value="<?php echo esc_attr($client_id); ?>" class="regular-text">
                                    <p class="description">Your AzamPay Client ID.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Client Secret</th>
                                <td>
                                    <input type="password" name="kilismile_azampay_client_secret" value="<?php echo esc_attr($client_secret); ?>" class="regular-text">
                                    <p class="description">Your AzamPay Client Secret. This is sensitive information.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Partner ID</th>
                                <td>
                                    <input type="text" name="kilismile_azampay_partner_id" value="<?php echo esc_attr($partner_id); ?>" class="regular-text">
                                    <p class="description">Your AzamPay Partner ID (optional, only if provided by AzamPay).</p>
                                </td>
                            </tr>
                        </table>
                        
                        <h3>Environment Settings</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Environment</th>
                                <td>
                                    <label>
                                        <input type="radio" name="kilismile_azampay_sandbox" value="1" <?php checked($sandbox, true); ?>>
                                        Sandbox (Testing)
                                    </label>
                                    <br>
                                    <label>
                                        <input type="radio" name="kilismile_azampay_sandbox" value="0" <?php checked($sandbox, false); ?>>
                                        Production (Live)
                                    </label>
                                    <p class="description">Always test in sandbox mode before going live.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">API Version</th>
                                <td>
                                    <select name="kilismile_azampay_api_version">
                                        <option value="v1" <?php selected($api_version, 'v1'); ?>>v1</option>
                                        <option value="v2" <?php selected($api_version, 'v2'); ?>>v2</option>
                                    </select>
                                    <p class="description">The API version to use (default: v1).</p>
                                </td>
                            </tr>
                        </table>
                        
                        <h3>Display Settings</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Vendor Name</th>
                                <td>
                                    <input type="text" name="kilismile_azampay_vendor_name" value="<?php echo esc_attr($vendor_name); ?>" class="regular-text">
                                    <p class="description">This name will be displayed on the AzamPay checkout page.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Logo URL</th>
                                <td>
                                    <input type="url" name="kilismile_azampay_logo_url" value="<?php echo esc_url($logo_url); ?>" class="regular-text">
                                    <p class="description">URL to your logo image for display on the AzamPay checkout page.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save AzamPay Settings'); ?>
                    </form>
                </div>
                
                <!-- Test Connection -->
                <div class="kilismile-admin-section">
                    <h3>Test Connection</h3>
                    <p>Test your AzamPay connection to ensure everything is configured correctly.</p>
                    
                    <div class="test-connection-container">
                        <button id="test-azampay-connection" class="button button-primary">Test AzamPay Connection</button>
                        <div id="test-results" class="test-results" style="display: none;"></div>
                    </div>
                </div>
                
                <!-- Integration Information -->
                <div class="kilismile-admin-section">
                    <h3>Integration Information</h3>
                    
                    <div class="integration-info">
                        <table class="form-table">
                            <tr>
                                <th scope="row">Callback URL</th>
                                <td>
                                    <code><?php echo esc_url(admin_url('admin-ajax.php?action=azampay_callback')); ?></code>
                                    <p class="description">Configure this URL in your AzamPay dashboard to receive payment notifications.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Success URL</th>
                                <td>
                                    <code><?php echo esc_url(home_url('/donation-success/')); ?></code>
                                    <p class="description">Customers will be redirected here after successful payments.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Failure URL</th>
                                <td>
                                    <code><?php echo esc_url(home_url('/donation-failed/')); ?></code>
                                    <p class="description">Customers will be redirected here after failed payments.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Cancel URL</th>
                                <td>
                                    <code><?php echo esc_url(home_url('/donation-cancelled/')); ?></code>
                                    <p class="description">Customers will be redirected here if they cancel the payment.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Supported Networks</th>
                                <td>
                                    <ul style="margin-top: 0;">
                                        <li><strong>Vodacom M-Pesa</strong></li>
                                        <li><strong>Airtel Money</strong></li>
                                        <li><strong>Tigo Pesa</strong></li>
                                        <li><strong>Halopesa</strong></li>
                                        <li><strong>AzamPesa</strong></li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Test connection button
            $('#test-azampay-connection').on('click', function() {
                var $button = $(this);
                var $results = $('#test-results');
                
                $button.prop('disabled', true).text('Testing...');
                $results.hide();
                
                $.ajax({
                    url: kiliSmileAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'test_azampay_admin_connection',
                        nonce: kiliSmileAdmin.nonce
                    },
                    success: function(response) {
                        $button.prop('disabled', false).text('Test AzamPay Connection');
                        
                        if (response.success) {
                            var data = response.data;
                            var html = '<div class="notice notice-success inline"><p><strong>✅ Connection Successful!</strong></p>';
                            
                            html += '<ul>';
                            html += '<li><strong>Environment:</strong> ' + data.environment + '</li>';
                            html += '<li><strong>Token Received:</strong> ' + (data.token_received ? 'Yes' : 'No') + '</li>';
                            if (data.token_expiry) {
                                html += '<li><strong>Token Expiry:</strong> ' + data.token_expiry + '</li>';
                            }
                            html += '<li><strong>Auth Endpoint:</strong> ' + data.auth_endpoint + '</li>';
                            html += '<li><strong>Payment Endpoint:</strong> ' + data.payment_endpoint + '</li>';
                            html += '<li><strong>API Version:</strong> ' + data.api_version + '</li>';
                            html += '<li><strong>Timestamp:</strong> ' + data.timestamp + '</li>';
                            html += '</ul>';
                            
                            html += '</div>';
                            $results.html(html).show();
                        } else {
                            var html = '<div class="notice notice-error inline"><p><strong>❌ Connection Failed!</strong></p>';
                            html += '<p>' + response.data + '</p>';
                            html += '<p>Please check your credentials and try again.</p>';
                            html += '</div>';
                            $results.html(html).show();
                        }
                    },
                    error: function() {
                        $button.prop('disabled', false).text('Test AzamPay Connection');
                        var html = '<div class="notice notice-error inline"><p><strong>❌ Connection Test Failed!</strong></p>';
                        html += '<p>There was an error processing your request. Please try again.</p>';
                        html += '</div>';
                        $results.html(html).show();
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Test connection AJAX handler
     */
    public function test_connection_ajax() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kilismile_admin_nonce')) {
            wp_send_json_error('Security verification failed');
        }
        
        // Check if using enhanced version
        $use_enhanced = get_option('kilismile_use_enhanced_azampay', false);
        
        try {
            if ($use_enhanced) {
                // Use enhanced version
                require_once get_template_directory() . '/includes/enhanced-azampay-integration.php';
                $azampay = new KiliSmile_Enhanced_AzamPay();
            } else {
                // Use standard version
                require_once get_template_directory() . '/includes/azampay-integration.php';
                $azampay = new KiliSmile_AzamPay();
            }
            
            // Test connection
            $result = $azampay->test_connection_ajax();
            
            // The test_connection_ajax method calls wp_die(), so we won't reach this point
            // But just in case, we'll handle the response
            if ($result) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error('Connection test failed');
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}

// Initialize settings page
new KiliSmile_AzamPay_Settings();

