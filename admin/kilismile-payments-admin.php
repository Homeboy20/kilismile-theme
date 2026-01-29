<?php
/**
 * KiliSmile Payments Admin Interface
 * 
 * Provides WordPress admin dashboard integration for payment settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Payments_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('KiliSmile Payments', 'kilismile-payments'),
            __('KiliSmile Payments', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments',
            array($this, 'main_settings_page'),
            'dashicons-heart',
            55
        );
        
        // Settings submenu
        add_submenu_page(
            'kilismile-payments',
            __('Payment Settings', 'kilismile-payments'),
            __('Payment Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments',
            array($this, 'main_settings_page')
        );
        
        // PayPal settings
        add_submenu_page(
            'kilismile-payments',
            __('PayPal Settings', 'kilismile-payments'),
            __('PayPal Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-paypal',
            array($this, 'paypal_settings_page')
        );
        
        // AzamPay settings
        add_submenu_page(
            'kilismile-payments',
            __('AzamPay Settings', 'kilismile-payments'),
            __('AzamPay Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-azampay',
            array($this, 'azampay_settings_page')
        );
        
        // Transaction logs
        add_submenu_page(
            'kilismile-payments',
            __('Transaction Logs', 'kilismile-payments'),
            __('Transaction Logs', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-logs',
            array($this, 'transaction_logs_page')
        );
        
        // Debug tools
        add_submenu_page(
            'kilismile-payments',
            __('Debug Tools', 'kilismile-payments'),
            __('Debug Tools', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-debug',
            array($this, 'debug_tools_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register settings groups
        register_setting('kilismile_payments_general', 'kilismile_payments_general');
        register_setting('kilismile_payments_paypal', 'kilismile_payments_paypal');
        register_setting('kilismile_payments_azampay', 'kilismile_payments_azampay');
        
        // General settings section
        add_settings_section(
            'kilismile_payments_general_section',
            __('General Settings', 'kilismile-payments'),
            array($this, 'general_section_callback'),
            'kilismile_payments_general'
        );
        
        // PayPal settings section
        add_settings_section(
            'kilismile_payments_paypal_section',
            __('PayPal Configuration', 'kilismile-payments'),
            array($this, 'paypal_section_callback'),
            'kilismile_payments_paypal'
        );
        
        // AzamPay settings section
        add_settings_section(
            'kilismile_payments_azampay_section',
            __('AzamPay Configuration', 'kilismile-payments'),
            array($this, 'azampay_section_callback'),
            'kilismile_payments_azampay'
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'kilismile-payments') !== false) {
            wp_enqueue_style('kilismile-payments-admin', plugin_dir_url(__FILE__) . 'assets/admin.css', array(), KILISMILE_PAYMENTS_VERSION);
            wp_enqueue_script('kilismile-payments-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', array('jquery'), KILISMILE_PAYMENTS_VERSION, true);
        }
    }
    
    /**
     * Main settings page
     */
    public function main_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="kilismile-payments-admin">
                <div class="kilismile-admin-header">
                    <h2><?php _e('Payment Gateway Status', 'kilismile-payments'); ?></h2>
                </div>
                
                <div class="kilismile-admin-grid">
                    <!-- Status Cards -->
                    <div class="kilismile-status-card">
                        <h3><?php _e('PayPal Status', 'kilismile-payments'); ?></h3>
                        <?php $this->render_paypal_status(); ?>
                    </div>
                    
                    <div class="kilismile-status-card">
                        <h3><?php _e('AzamPay Status', 'kilismile-payments'); ?></h3>
                        <?php $this->render_azampay_status(); ?>
                    </div>
                    
                    <div class="kilismile-status-card">
                        <h3><?php _e('Recent Transactions', 'kilismile-payments'); ?></h3>
                        <?php $this->render_recent_transactions(); ?>
                    </div>
                    
                    <div class="kilismile-status-card">
                        <h3><?php _e('Quick Actions', 'kilismile-payments'); ?></h3>
                        <div class="kilismile-quick-actions">
                            <a href="<?php echo admin_url('admin.php?page=kilismile-payments-paypal'); ?>" class="button button-primary">
                                <?php _e('Configure PayPal', 'kilismile-payments'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=kilismile-payments-azampay'); ?>" class="button button-primary">
                                <?php _e('Configure AzamPay', 'kilismile-payments'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=kilismile-payments-logs'); ?>" class="button button-secondary">
                                <?php _e('View Logs', 'kilismile-payments'); ?>
                            </a>
                            <a href="<?php echo home_url('/donations/'); ?>" class="button button-secondary" target="_blank">
                                <?php _e('Test Donation Form', 'kilismile-payments'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .kilismile-payments-admin {
                margin-top: 20px;
            }
            .kilismile-admin-header {
                background: #0073aa;
                color: white;
                padding: 20px;
                border-radius: 5px 5px 0 0;
            }
            .kilismile-admin-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 0 0 5px 5px;
            }
            .kilismile-status-card {
                background: white;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .kilismile-status-card h3 {
                margin-top: 0;
                color: #0073aa;
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }
            .kilismile-quick-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .kilismile-quick-actions .button {
                text-align: center;
            }
            .status-indicator {
                display: inline-block;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                margin-right: 8px;
            }
            .status-active { background: #46b450; }
            .status-inactive { background: #dc3232; }
            .status-warning { background: #ffb900; }
        </style>
        <?php
    }
    
    /**
     * PayPal settings page
     */
    public function paypal_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('kilismile_payments_paypal');
                do_settings_sections('kilismile_payments_paypal');
                
                $options = get_option('kilismile_payments_paypal', array());
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable PayPal', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="kilismile_payments_paypal[enabled]" value="1" 
                                       <?php checked(isset($options['enabled']) ? $options['enabled'] : 0, 1); ?> />
                                <?php _e('Enable PayPal payments', 'kilismile-payments'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Sandbox Mode', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="kilismile_payments_paypal[sandbox]" value="1" 
                                       <?php checked(isset($options['sandbox']) ? $options['sandbox'] : 1, 1); ?> />
                                <?php _e('Use PayPal sandbox for testing', 'kilismile-payments'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('PayPal Email', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="email" name="kilismile_payments_paypal[email]" 
                                   value="<?php echo esc_attr($options['email'] ?? ''); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('Your PayPal business email address', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * AzamPay settings page
     */
    public function azampay_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('kilismile_payments_azampay');
                do_settings_sections('kilismile_payments_azampay');
                
                $options = get_option('kilismile_payments_azampay', array());
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable AzamPay', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="kilismile_payments_azampay[enabled]" value="1" 
                                       <?php checked(isset($options['enabled']) ? $options['enabled'] : 0, 1); ?> />
                                <?php _e('Enable AzamPay payments', 'kilismile-payments'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Sandbox Mode', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="kilismile_payments_azampay[sandbox]" value="1" 
                                       <?php checked(isset($options['sandbox']) ? $options['sandbox'] : 1, 1); ?> />
                                <?php _e('Use AzamPay sandbox for testing', 'kilismile-payments'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('App Name', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="text" name="kilismile_payments_azampay[app_name]" 
                                   value="<?php echo esc_attr($options['app_name'] ?? ''); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('Your AzamPay application name', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Client ID', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="text" name="kilismile_payments_azampay[client_id]" 
                                   value="<?php echo esc_attr($options['client_id'] ?? ''); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Client Secret', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="password" name="kilismile_payments_azampay[client_secret]" 
                                   value="<?php echo esc_attr($options['client_secret'] ?? ''); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Transaction logs page
     */
    public function transaction_logs_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="kilismile-logs-container">
                <p><?php _e('Recent payment transactions and logs will appear here.', 'kilismile-payments'); ?></p>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'kilismile-payments'); ?></th>
                            <th><?php _e('Amount', 'kilismile-payments'); ?></th>
                            <th><?php _e('Gateway', 'kilismile-payments'); ?></th>
                            <th><?php _e('Status', 'kilismile-payments'); ?></th>
                            <th><?php _e('Transaction ID', 'kilismile-payments'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5"><?php _e('No transactions yet. Configure your payment gateways and test the donation form.', 'kilismile-payments'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Debug tools page
     */
    public function debug_tools_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="kilismile-debug-tools">
                <div class="postbox">
                    <h3 class="hndle"><?php _e('System Status', 'kilismile-payments'); ?></h3>
                    <div class="inside">
                        <?php $this->render_system_status(); ?>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Test Payment Gateways', 'kilismile-payments'); ?></h3>
                    <div class="inside">
                        <p><?php _e('Use these tools to test your payment gateway configurations.', 'kilismile-payments'); ?></p>
                        <p>
                            <a href="<?php echo home_url('/donations/'); ?>" class="button button-primary" target="_blank">
                                <?php _e('Test Donation Form', 'kilismile-payments'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=kilismile-payments-logs'); ?>" class="button button-secondary">
                                <?php _e('View Debug Logs', 'kilismile-payments'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render PayPal status
     */
    private function render_paypal_status() {
        $options = get_option('kilismile_payments_paypal', array());
        $enabled = isset($options['enabled']) && $options['enabled'];
        $configured = !empty($options['email']);
        
        echo '<p>';
        if ($enabled && $configured) {
            echo '<span class="status-indicator status-active"></span>';
            echo __('Active and configured', 'kilismile-payments');
        } elseif ($enabled) {
            echo '<span class="status-indicator status-warning"></span>';
            echo __('Enabled but not configured', 'kilismile-payments');
        } else {
            echo '<span class="status-indicator status-inactive"></span>';
            echo __('Disabled', 'kilismile-payments');
        }
        echo '</p>';
        
        if (!empty($options['email'])) {
            echo '<p><small>' . sprintf(__('Email: %s', 'kilismile-payments'), esc_html($options['email'])) . '</small></p>';
        }
    }
    
    /**
     * Render AzamPay status
     */
    private function render_azampay_status() {
        $options = get_option('kilismile_payments_azampay', array());
        $enabled = isset($options['enabled']) && $options['enabled'];
        $configured = !empty($options['app_name']) && !empty($options['client_id']);
        
        echo '<p>';
        if ($enabled && $configured) {
            echo '<span class="status-indicator status-active"></span>';
            echo __('Active and configured', 'kilismile-payments');
        } elseif ($enabled) {
            echo '<span class="status-indicator status-warning"></span>';
            echo __('Enabled but not configured', 'kilismile-payments');
        } else {
            echo '<span class="status-indicator status-inactive"></span>';
            echo __('Disabled', 'kilismile-payments');
        }
        echo '</p>';
        
        if (!empty($options['app_name'])) {
            echo '<p><small>' . sprintf(__('App: %s', 'kilismile-payments'), esc_html($options['app_name'])) . '</small></p>';
        }
    }
    
    /**
     * Render recent transactions
     */
    private function render_recent_transactions() {
        echo '<p>' . __('No recent transactions', 'kilismile-payments') . '</p>';
        echo '<p><small>' . __('Transactions will appear here once you start accepting donations.', 'kilismile-payments') . '</small></p>';
    }
    
    /**
     * Render system status
     */
    private function render_system_status() {
        echo '<ul>';
        echo '<li><strong>' . __('Plugin Version:', 'kilismile-payments') . '</strong> ' . KILISMILE_PAYMENTS_VERSION . '</li>';
        echo '<li><strong>' . __('WordPress Version:', 'kilismile-payments') . '</strong> ' . get_bloginfo('version') . '</li>';
        echo '<li><strong>' . __('PHP Version:', 'kilismile-payments') . '</strong> ' . PHP_VERSION . '</li>';
        echo '<li><strong>' . __('cURL Support:', 'kilismile-payments') . '</strong> ' . (function_exists('curl_init') ? __('Yes', 'kilismile-payments') : __('No', 'kilismile-payments')) . '</li>';
        echo '<li><strong>' . __('SSL Support:', 'kilismile-payments') . '</strong> ' . (is_ssl() ? __('Yes', 'kilismile-payments') : __('No', 'kilismile-payments')) . '</li>';
        echo '</ul>';
    }
    
    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure general payment settings for your donation system.', 'kilismile-payments') . '</p>';
    }
    
    public function paypal_section_callback() {
        echo '<p>' . __('Configure PayPal settings for USD donations.', 'kilismile-payments') . '</p>';
    }
    
    public function azampay_section_callback() {
        echo '<p>' . __('Configure AzamPay settings for TZS donations.', 'kilismile-payments') . '</p>';
    }
}

// Initialize admin class
if (is_admin()) {
    KiliSmile_Payments_Admin::get_instance();
}

