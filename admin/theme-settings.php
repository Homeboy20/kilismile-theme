<?php
/**
 * KiliSmile Theme Settings Page
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Theme Settings Menu
 */
function kilismile_admin_menu() {
    add_menu_page(
        __('KiliSmile Settings', 'kilismile'),
        __('KiliSmile', 'kilismile'),
        'manage_options',
        'kilismile-settings',
        'kilismile_settings_page',
        'dashicons-heart',
        30
    );
    
    add_submenu_page(
        'kilismile-settings',
        __('Theme Settings', 'kilismile'),
        __('Theme Settings', 'kilismile'),
        'manage_options',
        'kilismile-settings',
        'kilismile_settings_page'
    );

    // Only show Payment Settings if the KiliSmile Payments plugin is not active
    if (!function_exists('kilismile_payments_plugin_active') || !kilismile_payments_plugin_active()) {
        add_submenu_page(
            'kilismile-settings',
            __('Payment Settings', 'kilismile'),
            __('Payment Settings', 'kilismile'),
            'manage_options',
            'kilismile-payment-settings',
            'kilismile_payment_settings_page'
        );
    } else {
        // When plugin is active, add a redirect link to the plugin's settings
        add_submenu_page(
            'kilismile-settings',
            __('Payment Settings', 'kilismile'),
            __('Payment Settings', 'kilismile') . ' <span class="dashicons dashicons-external" style="font-size:14px;vertical-align:text-bottom;"></span>',
            'manage_options',
            'admin.php?page=kilismile-payments-gateways',
            ''
        );
    }

    add_submenu_page(
        'kilismile-settings',
        __('Donation Management', 'kilismile'),
        __('Donations', 'kilismile'),
        'manage_options',
        'kilismile-donations',
        'kilismile_donations_page'
    );
}

/**
 * Enqueue admin scripts for theme settings
 */
function kilismile_admin_settings_scripts() {
    $screen = get_current_screen();
    
    // Only load on our settings pages
    if (strpos($screen->id, 'kilismile-settings') !== false) {
        // Enqueue Chart.js for analytics
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
            array(),
            '3.9.1',
            true
        );
        
        // Enqueue the gateway toggle fix script
        wp_enqueue_script(
            'kilismile-gateway-toggle-fix',
            get_template_directory_uri() . '/admin/js/gateway-toggle-fix.js',
            array('jquery'),
            filemtime(get_template_directory() . '/admin/js/gateway-toggle-fix.js'),
            true
        );
        
        // Pass analytics data to JavaScript
        if (class_exists('KiliSmile_Donation_Database')) {
            $db_handler = new KiliSmile_Donation_Database();
            $analytics = $db_handler->get_analytics_data();
            
            wp_localize_script('kilismile-gateway-toggle-fix', 'kilismileAnalytics', array(
                'monthlyData' => $analytics['monthly_data'] ?? array(),
                'paymentMethods' => $analytics['payment_methods'] ?? array(),
                'currencies' => $analytics['currencies'] ?? array(),
                'statuses' => $analytics['statuses'] ?? array(),
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kilismile_analytics_nonce')
            ));
        }
    }
}
add_action('admin_enqueue_scripts', 'kilismile_admin_settings_scripts');
add_action('admin_menu', 'kilismile_admin_menu');

/**
 * AJAX handler for refreshing analytics data
 */
function kilismile_refresh_analytics_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_analytics_nonce')) {
        wp_die('Security check failed');
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    try {
        if (class_exists('KiliSmile_Donation_Database')) {
            $db_handler = new KiliSmile_Donation_Database();
            $stats = $db_handler->get_donation_statistics();
            $analytics = $db_handler->get_analytics_data();
            
            wp_send_json_success(array(
                'stats' => $stats,
                'monthlyData' => $analytics['monthly_data'] ?? array(),
                'paymentMethods' => $analytics['payment_methods'] ?? array(),
                'currencies' => $analytics['currencies'] ?? array(),
                'statuses' => $analytics['statuses'] ?? array(),
                'timestamp' => current_time('timestamp')
            ));
        } else {
            wp_send_json_error('Database handler not available');
        }
    } catch (Exception $e) {
        wp_send_json_error('Error retrieving analytics: ' . $e->getMessage());
    }
}
add_action('wp_ajax_kilismile_refresh_analytics', 'kilismile_refresh_analytics_ajax');

/**
 * Main Theme Settings Page
 */
function kilismile_settings_page() {
    $saved_section = '';
    
    // Save Donation Settings
    if (isset($_POST['save_donation_settings']) && wp_verify_nonce($_POST['kilismile_donation_nonce'], 'kilismile_donation_action')) {
        update_option('kilismile_enable_donations', isset($_POST['kilismile_enable_donations']) ? 1 : 0);
        update_option('kilismile_default_currency', sanitize_text_field($_POST['kilismile_default_currency']));
        update_option('kilismile_donation_goal_usd', absint($_POST['kilismile_donation_goal_usd']));
        update_option('kilismile_current_donations_usd', absint($_POST['kilismile_current_donations_usd']));
        update_option('kilismile_donation_goal_tzs', absint($_POST['kilismile_donation_goal_tzs']));
        update_option('kilismile_current_donations_tzs', absint($_POST['kilismile_current_donations_tzs']));
        update_option('kilismile_exchange_rate', floatval($_POST['kilismile_exchange_rate']));
        
        $saved_section = 'donation';
        echo '<div class="notice notice-success"><p>' . __('Donation settings saved successfully!', 'kilismile') . '</p></div>';
    }
    
    // Save Payment Methods
    if (isset($_POST['save_payment_methods']) && wp_verify_nonce($_POST['kilismile_payment_nonce'], 'kilismile_payment_action')) {
        // Get all possible payment method toggles
        $payment_toggles = array(
            'kilismile_paypal_enabled',
            'kilismile_stripe_enabled',
            'kilismile_wire_transfer_enabled',
            'kilismile_mpesa_enabled',
            'kilismile_tigo_pesa_enabled',
            'kilismile_airtel_money_enabled',
            'kilismile_local_bank_enabled'
        );
        
        // Process all payment toggles - ensuring both checked AND unchecked states are handled
        foreach ($payment_toggles as $toggle) {
            // If toggle exists in POST, use its value (1 for checked, 0 for unchecked)
            // If toggle doesn't exist in POST (unchecked), default to 0 (disabled)
            $value = (isset($_POST[$toggle]) && $_POST[$toggle] == 1) ? 1 : 0;
            
            // Update the option and log the change
            $current_value = get_option($toggle, 0);
            update_option($toggle, $value);
            error_log("THEME SETTINGS: $toggle changed from $current_value to $value");
        }
        
        // International Payment Methods fields
        update_option('kilismile_paypal_email', sanitize_email($_POST['kilismile_paypal_email']));
        update_option('kilismile_stripe_public_key', sanitize_text_field($_POST['kilismile_stripe_public_key']));
        update_option('kilismile_stripe_secret_key', sanitize_text_field($_POST['kilismile_stripe_secret_key']));
        update_option('kilismile_wire_transfer_details', sanitize_textarea_field($_POST['kilismile_wire_transfer_details']));
        
        // Local Payment Methods fields
        update_option('kilismile_mpesa_number', sanitize_text_field($_POST['kilismile_mpesa_number']));
        update_option('kilismile_mpesa_name', sanitize_text_field($_POST['kilismile_mpesa_name']));
        update_option('kilismile_tigo_pesa_number', sanitize_text_field($_POST['kilismile_tigo_pesa_number']));
        update_option('kilismile_airtel_money_number', sanitize_text_field($_POST['kilismile_airtel_money_number']));
        update_option('kilismile_local_bank_details', sanitize_textarea_field($_POST['kilismile_local_bank_details']));
        
        $saved_section = 'payment';
        echo '<div class="notice notice-success"><p>' . __('Payment methods saved successfully!', 'kilismile') . '</p></div>';
    }
    
    // Save Gateway Integration
    if (isset($_POST['save_gateway_integration']) && wp_verify_nonce($_POST['kilismile_gateway_nonce'], 'kilismile_gateway_action')) {
        // Get all possible gateway toggles
        $gateway_toggles = array(
            'kilismile_selcom_enabled',
            'kilismile_azam_pay_enabled'
        );
        
        // Process all gateway toggles - ensuring both checked AND unchecked states are handled
        foreach ($gateway_toggles as $toggle) {
            // If toggle exists in POST, use its value (1 for checked, 0 for unchecked)
            // If toggle doesn't exist in POST (unchecked), default to 0 (disabled)
            $value = (isset($_POST[$toggle]) && $_POST[$toggle] == 1) ? 1 : 0;
            
            // Update the option and log the change
            $current_value = get_option($toggle, 0);
            update_option($toggle, $value);
            error_log("THEME SETTINGS: $toggle changed from $current_value to $value");
        }
        
        // Process other gateway settings
        update_option('kilismile_selcom_api_key', sanitize_text_field($_POST['kilismile_selcom_api_key']));
        update_option('kilismile_selcom_api_secret', sanitize_text_field($_POST['kilismile_selcom_api_secret']));
        update_option('kilismile_selcom_vendor_id', sanitize_text_field($_POST['kilismile_selcom_vendor_id']));
        
        // Azam Pay Gateway
        update_option('kilismile_azam_api_key', sanitize_text_field($_POST['kilismile_azam_api_key']));
        update_option('kilismile_azam_client_id', sanitize_text_field($_POST['kilismile_azam_client_id']));
        update_option('kilismile_azam_client_secret', sanitize_text_field($_POST['kilismile_azam_client_secret']));
        
        $saved_section = 'gateway';
        echo '<div class="notice notice-success"><p>' . __('Gateway integration settings saved successfully!', 'kilismile') . '</p></div>';
    }
    
    // Save All Settings (legacy support)
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['kilismile_save_all_nonce'], 'kilismile_settings_action')) {
        // Donation Settings
        update_option('kilismile_enable_donations', isset($_POST['kilismile_enable_donations']) ? 1 : 0);
        update_option('kilismile_default_currency', sanitize_text_field($_POST['kilismile_default_currency']));
        update_option('kilismile_donation_goal_usd', absint($_POST['kilismile_donation_goal_usd']));
        update_option('kilismile_current_donations_usd', absint($_POST['kilismile_current_donations_usd']));
        update_option('kilismile_donation_goal_tzs', absint($_POST['kilismile_donation_goal_tzs']));
        update_option('kilismile_current_donations_tzs', absint($_POST['kilismile_current_donations_tzs']));
        update_option('kilismile_exchange_rate', floatval($_POST['kilismile_exchange_rate']));
        
        // Payment Methods - REMOVED to prevent conflicts with individual payment method saves
        // Payment methods have their own dedicated save functionality
        
        // Gateway Integration Settings - REMOVED to prevent conflicts
        // Gateway methods have their own dedicated save functionality
        
        $saved_section = 'all';
        echo '<div class="notice notice-success"><p>' . __('All settings saved successfully!', 'kilismile') . '</p></div>';
    }
    
    // Get current settings
    $enable_donations = get_option('kilismile_enable_donations', 1);
    $default_currency = get_option('kilismile_default_currency', 'USD');
    $donation_goal_usd = get_option('kilismile_donation_goal_usd', 10000);
    $current_donations_usd = get_option('kilismile_current_donations_usd', 2500);
    $donation_goal_tzs = get_option('kilismile_donation_goal_tzs', 25000000);
    $current_donations_tzs = get_option('kilismile_current_donations_tzs', 6250000);
    $exchange_rate = get_option('kilismile_exchange_rate', 2500);
    
    // International Payment Methods
    $paypal_enabled = get_option('kilismile_paypal_enabled', 0);
    $paypal_email = get_option('kilismile_paypal_email', '');
    $stripe_enabled = get_option('kilismile_stripe_enabled', 0);
    $stripe_public_key = get_option('kilismile_stripe_public_key', '');
    $stripe_secret_key = get_option('kilismile_stripe_secret_key', '');
    $wire_transfer_enabled = get_option('kilismile_wire_transfer_enabled', 0);
    $wire_transfer_details = get_option('kilismile_wire_transfer_details', '');
    
    // Local Payment Methods
    $mpesa_enabled = get_option('kilismile_mpesa_enabled', 0);
    $mpesa_number = get_option('kilismile_mpesa_number', '');
    $mpesa_name = get_option('kilismile_mpesa_name', 'Kilismile Organization');
    $tigo_pesa_enabled = get_option('kilismile_tigo_pesa_enabled', 0);
    $tigo_pesa_number = get_option('kilismile_tigo_pesa_number', '');
    $airtel_money_enabled = get_option('kilismile_airtel_money_enabled', 0);
    $airtel_money_number = get_option('kilismile_airtel_money_number', '');
    $local_bank_enabled = get_option('kilismile_local_bank_enabled', 0);
    $local_bank_details = get_option('kilismile_local_bank_details', '');
    
    // Gateway Integration Settings
    $selcom_enabled = get_option('kilismile_selcom_enabled', 0);
    $selcom_api_key = get_option('kilismile_selcom_api_key', '');
    $selcom_api_secret = get_option('kilismile_selcom_api_secret', '');
    $selcom_vendor_id = get_option('kilismile_selcom_vendor_id', '');
    $azam_pay_enabled = get_option('kilismile_azam_pay_enabled', 0);
    ?>
    
    <div class="wrap kilismile-admin-page">
        <h1><?php _e('KiliSmile Theme Settings', 'kilismile'); ?></h1>
        
        <?php 
        // Show migration status
        kilismile_show_migration_status();
        
        // Show development tools if in debug mode
        kilismile_add_migration_tools();
        ?>
        
        <div class="nav-tab-wrapper">
            <a href="#donation-settings" class="nav-tab nav-tab-active" data-tab="donation-settings">
                <i class="dashicons dashicons-heart"></i> <?php _e('Donation Settings', 'kilismile'); ?>
                <span class="section-status" id="donation-status"><?php _e('Ready', 'kilismile'); ?></span>
            </a>
            <a href="#payment-methods" class="nav-tab" data-tab="payment-methods">
                <i class="dashicons dashicons-money-alt"></i> <?php _e('Payment Methods', 'kilismile'); ?>
                <span class="section-status" id="payment-status"><?php _e('Ready', 'kilismile'); ?></span>
            </a>
            <a href="#gateway-integration" class="nav-tab" data-tab="gateway-integration">
                <i class="dashicons dashicons-admin-plugins"></i> <?php _e('Gateway Integration', 'kilismile'); ?>
                <span class="section-status" id="gateway-status"><?php _e('Ready', 'kilismile'); ?></span>
            </a>
        </div>
        
        <!-- Donation Settings Tab -->
        <div id="donation-settings" class="tab-content active">
            <form method="post" action="" class="settings-form">
                <?php wp_nonce_field('kilismile_donation_action', 'kilismile_donation_nonce'); ?>
                
                <div class="settings-section">
                    <h2><?php _e('Donation System Configuration', 'kilismile'); ?></h2>
                    
                    <!-- Master Enable/Disable -->
                    <div class="setting-group master-toggle">
                        <h3>
                            <i class="dashicons dashicons-admin-settings"></i>
                            <?php _e('Master Settings', 'kilismile'); ?>
                        </h3>
                        <div class="toggle-container">
                            <label class="toggle-label">
                                <input type="checkbox" 
                                       name="kilismile_enable_donations" 
                                       value="1" 
                                       <?php checked($enable_donations, 1); ?>
                                       class="master-toggle-input">
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">
                                    <?php _e('Enable Donation System', 'kilismile'); ?>
                                </span>
                            </label>
                            <p class="description">
                                <?php _e('When disabled, all donation forms and payment options will be hidden from the website.', 'kilismile'); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="donation-subsettings" <?php echo !$enable_donations ? 'style="opacity: 0.6; pointer-events: none;"' : ''; ?>>
                        
                        <!-- Currency Settings -->
                        <div class="setting-group">
                            <h3>
                                <i class="dashicons dashicons-money-alt"></i>
                                <?php _e('Currency Settings', 'kilismile'); ?>
                            </h3>
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="kilismile_default_currency"><?php _e('Default Currency', 'kilismile'); ?></label>
                                    <select name="kilismile_default_currency" id="kilismile_default_currency">
                                        <option value="USD" <?php selected($default_currency, 'USD'); ?>>
                                            🇺🇸 <?php _e('US Dollar (USD)', 'kilismile'); ?>
                                        </option>
                                        <option value="TZS" <?php selected($default_currency, 'TZS'); ?>>
                                            🇹🇿 <?php _e('Tanzanian Shilling (TZS)', 'kilismile'); ?>
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="form-field">
                                    <label for="kilismile_exchange_rate"><?php _e('Exchange Rate (TZS per USD)', 'kilismile'); ?></label>
                                    <input type="number" 
                                           name="kilismile_exchange_rate" 
                                           id="kilismile_exchange_rate"
                                           value="<?php echo esc_attr($exchange_rate); ?>"
                                           step="0.01"
                                           min="1">
                                    <p class="description"><?php _e('Current exchange rate for currency conversion display', 'kilismile'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Live Analytics Dashboard -->
                        <div class="setting-group">
                            <h3>
                                <i class="dashicons dashicons-chart-area"></i>
                                <?php _e('Live Donation Analytics', 'kilismile'); ?>
                            </h3>
                            
                            <?php 
                            // Get real-time analytics data
                            if (class_exists('KiliSmile_Donation_Database')) {
                                $db_handler = new KiliSmile_Donation_Database();
                                $stats = $db_handler->get_donation_statistics();
                                $analytics = $db_handler->get_analytics_data();
                            } else {
                                $stats = array();
                                $analytics = array();
                            }
                            ?>
                            
                            <div class="analytics-dashboard">
                                <!-- Quick Stats Cards -->
                                <div class="stats-grid">
                                    <div class="stat-card total-donations">
                                        <div class="stat-icon">
                                            <i class="dashicons dashicons-heart"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number"><?php echo number_format($stats['total_donations'] ?? 0); ?></div>
                                            <div class="stat-label"><?php _e('Total Donations', 'kilismile'); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="stat-card monthly-donations">
                                        <div class="stat-icon">
                                            <i class="dashicons dashicons-calendar-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number"><?php echo number_format($stats['monthly_count'] ?? 0); ?></div>
                                            <div class="stat-label"><?php _e('This Month', 'kilismile'); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="stat-card total-raised-usd">
                                        <div class="stat-icon">
                                            <i class="dashicons dashicons-money-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number">$<?php echo number_format($stats['total_amount_usd'] ?? 0, 2); ?></div>
                                            <div class="stat-label"><?php _e('Total Raised (USD)', 'kilismile'); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="stat-card total-raised-tzs">
                                        <div class="stat-icon">
                                            <i class="dashicons dashicons-money-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number">TSh <?php echo number_format($stats['total_amount_tzs'] ?? 0); ?></div>
                                            <div class="stat-label"><?php _e('Total Raised (TZS)', 'kilismile'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Charts Section -->
                                <div class="charts-section">
                                    <div class="chart-container">
                                        <h4><?php _e('Monthly Donation Trends', 'kilismile'); ?></h4>
                                        <canvas id="monthly-trends-chart" width="400" height="200"></canvas>
                                    </div>
                                    
                                    <div class="chart-container">
                                        <h4><?php _e('Payment Methods Distribution', 'kilismile'); ?></h4>
                                        <canvas id="payment-methods-chart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Real-time Updates -->
                                <div class="realtime-section">
                                    <div class="realtime-indicator">
                                        <div class="pulse-dot"></div>
                                        <span><?php _e('Live Updates', 'kilismile'); ?></span>
                                        <small id="last-update"><?php _e('Just now', 'kilismile'); ?></small>
                                    </div>
                                    <button type="button" id="refresh-analytics" class="button button-secondary">
                                        <i class="dashicons dashicons-update"></i>
                                        <?php _e('Refresh Data', 'kilismile'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Donation Goals -->
                        <div class="setting-group">
                            <h3>
                                <i class="dashicons dashicons-chart-line"></i>
                                <?php _e('Donation Goals & Progress', 'kilismile'); ?>
                            </h3>
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="kilismile_donation_goal_usd"><?php _e('Monthly Goal (USD)', 'kilismile'); ?></label>
                                    <input type="number" 
                                           name="kilismile_donation_goal_usd" 
                                           id="kilismile_donation_goal_usd"
                                           value="<?php echo esc_attr($donation_goal_usd); ?>"
                                           min="0">
                                    <div class="goal-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $donation_goal_usd > 0 ? min(100, (($stats['monthly_amount_usd'] ?? 0) / $donation_goal_usd) * 100) : 0; ?>%"></div>
                                        </div>
                                        <small><?php echo number_format(($stats['monthly_amount_usd'] ?? 0), 2); ?> / <?php echo number_format($donation_goal_usd, 2); ?> (<?php echo $donation_goal_usd > 0 ? round((($stats['monthly_amount_usd'] ?? 0) / $donation_goal_usd) * 100, 1) : 0; ?>%)</small>
                                    </div>
                                </div>
                                
                                <div class="form-field">
                                    <label for="kilismile_current_donations_usd"><?php _e('Current Monthly Donations (USD)', 'kilismile'); ?></label>
                                    <input type="number" 
                                           name="kilismile_current_donations_usd" 
                                           id="kilismile_current_donations_usd"
                                           value="<?php echo esc_attr($stats['monthly_amount_usd'] ?? $current_donations_usd); ?>"
                                           min="0"
                                           readonly
                                           title="<?php _e('This value is automatically calculated from actual donations', 'kilismile'); ?>">
                                    <small class="description"><?php _e('Automatically calculated from database', 'kilismile'); ?></small>
                                </div>
                                
                                <div class="form-field">
                                    <label for="kilismile_donation_goal_tzs"><?php _e('Monthly Goal (TZS)', 'kilismile'); ?></label>
                                    <input type="number" 
                                           name="kilismile_donation_goal_tzs" 
                                           id="kilismile_donation_goal_tzs"
                                           value="<?php echo esc_attr($donation_goal_tzs); ?>"
                                           min="0">
                                    <div class="goal-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $donation_goal_tzs > 0 ? min(100, (($stats['monthly_amount_tzs'] ?? 0) / $donation_goal_tzs) * 100) : 0; ?>%"></div>
                                        </div>
                                        <small><?php echo number_format(($stats['monthly_amount_tzs'] ?? 0)); ?> / <?php echo number_format($donation_goal_tzs); ?> (<?php echo $donation_goal_tzs > 0 ? round((($stats['monthly_amount_tzs'] ?? 0) / $donation_goal_tzs) * 100, 1) : 0; ?>%)</small>
                                    </div>
                                </div>
                                
                                <div class="form-field">
                                    <label for="kilismile_current_donations_tzs"><?php _e('Current Monthly Donations (TZS)', 'kilismile'); ?></label>
                                    <input type="number" 
                                           name="kilismile_current_donations_tzs" 
                                           id="kilismile_current_donations_tzs"
                                           value="<?php echo esc_attr($stats['monthly_amount_tzs'] ?? $current_donations_tzs); ?>"
                                           min="0"
                                           readonly
                                           title="<?php _e('This value is automatically calculated from actual donations', 'kilismile'); ?>">
                                    <small class="description"><?php _e('Automatically calculated from database', 'kilismile'); ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campaign Management -->
                        <div class="setting-group">
                            <h3>
                                <i class="dashicons dashicons-megaphone"></i>
                                <?php _e('Donation Campaigns', 'kilismile'); ?>
                            </h3>
                            
                            <div class="campaigns-section">
                                <div class="campaigns-header">
                                    <p class="description"><?php _e('Create and manage targeted donation campaigns with specific goals and deadlines.', 'kilismile'); ?></p>
                                    <button type="button" class="button button-secondary" id="create-campaign-btn">
                                        <i class="dashicons dashicons-plus-alt2"></i>
                                        <?php _e('Create New Campaign', 'kilismile'); ?>
                                    </button>
                                </div>
                                
                                <?php
                                // Get existing campaigns (simplified - would be from database in full implementation)
                                $campaigns = get_option('kilismile_campaigns', array());
                                
                                if (empty($campaigns)) {
                                    $campaigns = array(
                                        array(
                                            'id' => 'general',
                                            'name' => 'General Donations',
                                            'goal' => 10000,
                                            'raised' => $stats['monthly_amount_usd'] ?? 0,
                                            'end_date' => date('Y-m-d', strtotime('+1 month')),
                                            'status' => 'active',
                                            'description' => 'General donations for our ongoing programs and initiatives.'
                                        )
                                    );
                                }
                                ?>
                                
                                <div class="campaigns-list">
                                    <?php foreach ($campaigns as $campaign): ?>
                                    <div class="campaign-card" data-campaign-id="<?php echo esc_attr($campaign['id']); ?>">
                                        <div class="campaign-header">
                                            <h4 class="campaign-title"><?php echo esc_html($campaign['name']); ?></h4>
                                            <span class="campaign-status <?php echo esc_attr($campaign['status']); ?>">
                                                <?php echo esc_html(ucfirst($campaign['status'])); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="campaign-stats">
                                            <div class="campaign-stat">
                                                <div class="campaign-stat-value">$<?php echo number_format($campaign['raised'], 2); ?></div>
                                                <div class="campaign-stat-label"><?php _e('Raised', 'kilismile'); ?></div>
                                            </div>
                                            <div class="campaign-stat">
                                                <div class="campaign-stat-value">$<?php echo number_format($campaign['goal'], 2); ?></div>
                                                <div class="campaign-stat-label"><?php _e('Goal', 'kilismile'); ?></div>
                                            </div>
                                            <div class="campaign-stat">
                                                <div class="campaign-stat-value"><?php echo round(($campaign['raised'] / $campaign['goal']) * 100, 1); ?>%</div>
                                                <div class="campaign-stat-label"><?php _e('Progress', 'kilismile'); ?></div>
                                            </div>
                                            <div class="campaign-stat">
                                                <div class="campaign-stat-value"><?php echo date('M j', strtotime($campaign['end_date'])); ?></div>
                                                <div class="campaign-stat-label"><?php _e('End Date', 'kilismile'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="goal-progress">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo min(100, ($campaign['raised'] / $campaign['goal']) * 100); ?>%"></div>
                                            </div>
                                        </div>
                                        
                                        <p class="campaign-description"><?php echo esc_html($campaign['description']); ?></p>
                                        
                                        <div class="campaign-actions">
                                            <button type="button" class="button button-small edit-campaign" data-campaign-id="<?php echo esc_attr($campaign['id']); ?>">
                                                <i class="dashicons dashicons-edit"></i>
                                                <?php _e('Edit', 'kilismile'); ?>
                                            </button>
                                            <button type="button" class="button button-small view-campaign" data-campaign-id="<?php echo esc_attr($campaign['id']); ?>">
                                                <i class="dashicons dashicons-visibility"></i>
                                                <?php _e('View', 'kilismile'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Campaign Creation Modal (hidden by default) -->
                <div id="campaign-modal" class="campaign-modal" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><?php _e('Create New Campaign', 'kilismile'); ?></h3>
                            <button type="button" class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="campaign-form">
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="campaign-name"><?php _e('Campaign Name', 'kilismile'); ?></label>
                                        <input type="text" id="campaign-name" name="campaign_name" required>
                                    </div>
                                    
                                    <div class="form-field">
                                        <label for="campaign-goal"><?php _e('Fundraising Goal ($)', 'kilismile'); ?></label>
                                        <input type="number" id="campaign-goal" name="campaign_goal" min="1" step="0.01" required>
                                    </div>
                                    
                                    <div class="form-field">
                                        <label for="campaign-start"><?php _e('Start Date', 'kilismile'); ?></label>
                                        <input type="date" id="campaign-start" name="campaign_start" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="form-field">
                                        <label for="campaign-end"><?php _e('End Date', 'kilismile'); ?></label>
                                        <input type="date" id="campaign-end" name="campaign_end" required>
                                    </div>
                                </div>
                                
                                <div class="form-field">
                                    <label for="campaign-description"><?php _e('Description', 'kilismile'); ?></label>
                                    <textarea id="campaign-description" name="campaign_description" rows="4" placeholder="Describe the purpose and goals of this campaign..."></textarea>
                                </div>
                                
                                <div class="form-field">
                                    <label for="campaign-image"><?php _e('Campaign Image URL (optional)', 'kilismile'); ?></label>
                                    <input type="url" id="campaign-image" name="campaign_image" placeholder="https://example.com/image.jpg">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="button button-secondary modal-cancel"><?php _e('Cancel', 'kilismile'); ?></button>
                            <button type="button" class="button button-primary" id="save-campaign"><?php _e('Create Campaign', 'kilismile'); ?></button>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button for Donation Settings -->
                <div class="section-save">
                    <button type="submit" name="save_donation_settings" class="button button-primary button-large">
                        <i class="dashicons dashicons-heart"></i>
                        <?php _e('Save Donation Settings', 'kilismile'); ?>
                    </button>
                    <p class="description">
                        <?php _e('Save donation system configuration and goals.', 'kilismile'); ?>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Payment Methods Tab -->
        <div id="payment-methods" class="tab-content">
            <form method="post" action="" class="settings-form">
                <?php wp_nonce_field('kilismile_payment_action', 'kilismile_payment_nonce'); ?>
                
                <div class="settings-section">
                    <h2><?php _e('Payment Methods Configuration', 'kilismile'); ?></h2>
                    
                    <!-- International Payment Methods -->
                    <div class="setting-group">
                        <h3>
                            <i class="dashicons dashicons-admin-site-alt3"></i>
                            <?php _e('International Payment Methods (USD)', 'kilismile'); ?>
                        </h3>
                        
                        <!-- PayPal -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_paypal_enabled" 
                                           value="1" 
                                           <?php checked($paypal_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fab fa-paypal"></i>
                                        <?php _e('PayPal', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$paypal_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-field">
                                    <label for="kilismile_paypal_email"><?php _e('PayPal Email', 'kilismile'); ?></label>
                                    <input type="email" 
                                           name="kilismile_paypal_email" 
                                           id="kilismile_paypal_email"
                                           value="<?php echo esc_attr($paypal_email); ?>"
                                           placeholder="your-paypal@email.com">
                                    <p class="description"><?php _e('Your PayPal account email for receiving donations', 'kilismile'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stripe -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_stripe_enabled" 
                                           value="1" 
                                           <?php checked($stripe_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fab fa-stripe"></i>
                                        <?php _e('Stripe (Credit/Debit Cards)', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$stripe_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="kilismile_stripe_public_key"><?php _e('Stripe Publishable Key', 'kilismile'); ?></label>
                                        <input type="text" 
                                               name="kilismile_stripe_public_key" 
                                               id="kilismile_stripe_public_key"
                                               value="<?php echo esc_attr($stripe_public_key); ?>"
                                               placeholder="pk_test_...">
                                    </div>
                                    <div class="form-field">
                                        <label for="kilismile_stripe_secret_key"><?php _e('Stripe Secret Key', 'kilismile'); ?></label>
                                        <input type="password" 
                                               name="kilismile_stripe_secret_key" 
                                               id="kilismile_stripe_secret_key"
                                               value="<?php echo esc_attr($stripe_secret_key); ?>"
                                               placeholder="sk_test_...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Wire Transfer -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_wire_transfer_enabled" 
                                           value="1" 
                                           <?php checked($wire_transfer_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-university"></i>
                                        <?php _e('International Wire Transfer', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$wire_transfer_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-field">
                                    <label for="kilismile_wire_transfer_details"><?php _e('Wire Transfer Details', 'kilismile'); ?></label>
                                    <textarea name="kilismile_wire_transfer_details" 
                                              id="kilismile_wire_transfer_details"
                                              rows="6"
                                              placeholder="Bank Name: 
Account Name: 
Account Number: 
SWIFT Code: 
Bank Address: "><?php echo esc_textarea($wire_transfer_details); ?></textarea>
                                    <p class="description"><?php _e('Provide complete wire transfer instructions for international donors', 'kilismile'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Local Payment Methods -->
                    <div class="setting-group">
                        <h3>
                            <i class="dashicons dashicons-location-alt"></i>
                            <?php _e('Local Payment Methods (Tanzania)', 'kilismile'); ?>
                        </h3>
                        
                        <!-- M-Pesa -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_mpesa_enabled" 
                                           value="1" 
                                           <?php checked($mpesa_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-mobile-alt"></i>
                                        <?php _e('M-Pesa', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$mpesa_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="kilismile_mpesa_number"><?php _e('M-Pesa Business Number', 'kilismile'); ?></label>
                                        <input type="text" 
                                               name="kilismile_mpesa_number" 
                                               id="kilismile_mpesa_number"
                                               value="<?php echo esc_attr($mpesa_number); ?>"
                                               placeholder="123456">
                                    </div>
                                    <div class="form-field">
                                        <label for="kilismile_mpesa_name"><?php _e('M-Pesa Account Name', 'kilismile'); ?></label>
                                        <input type="text" 
                                               name="kilismile_mpesa_name" 
                                               id="kilismile_mpesa_name"
                                               value="<?php echo esc_attr($mpesa_name); ?>"
                                               placeholder="Kilismile Organization">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tigo Pesa -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_tigo_pesa_enabled" 
                                           value="1" 
                                           <?php checked($tigo_pesa_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-mobile-alt"></i>
                                        <?php _e('Tigo Pesa', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$tigo_pesa_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-field">
                                    <label for="kilismile_tigo_pesa_number"><?php _e('Tigo Pesa Number', 'kilismile'); ?></label>
                                    <input type="text" 
                                           name="kilismile_tigo_pesa_number" 
                                           id="kilismile_tigo_pesa_number"
                                           value="<?php echo esc_attr($tigo_pesa_number); ?>"
                                           placeholder="+255763495575/+255735495575">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Airtel Money -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_airtel_money_enabled" 
                                           value="1" 
                                           <?php checked($airtel_money_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-mobile-alt"></i>
                                        <?php _e('Airtel Money', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$airtel_money_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-field">
                                    <label for="kilismile_airtel_money_number"><?php _e('Airtel Money Number', 'kilismile'); ?></label>
                                    <input type="text" 
                                           name="kilismile_airtel_money_number" 
                                           id="kilismile_airtel_money_number"
                                           value="<?php echo esc_attr($airtel_money_number); ?>"
                                           placeholder="+255763495575/+255735495575">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Local Bank Transfer -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_local_bank_enabled" 
                                           value="1" 
                                           <?php checked($local_bank_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-university"></i>
                                        <?php _e('Local Bank Transfer', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$local_bank_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-field">
                                    <label for="kilismile_local_bank_details"><?php _e('Bank Transfer Details (TZS)', 'kilismile'); ?></label>
                                    <textarea name="kilismile_local_bank_details" 
                                              id="kilismile_local_bank_details"
                                              rows="6"
                                              placeholder="Bank Name: 
Account Name: 
Account Number: 
Branch: 
Additional Instructions:"><?php echo esc_textarea($local_bank_details); ?></textarea>
                                    <p class="description"><?php _e('Provide complete bank transfer instructions for local donors', 'kilismile'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button for Payment Methods -->
                <div class="section-save">
                    <button type="submit" name="save_payment_methods" class="button button-primary button-large">
                        <i class="dashicons dashicons-money-alt"></i>
                        <?php _e('Save Payment Methods', 'kilismile'); ?>
                    </button>
                    <p class="description">
                        <?php _e('Save all payment method configurations.', 'kilismile'); ?>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Gateway Integration Tab -->
        <div id="gateway-integration" class="tab-content">
            <form method="post" action="" class="settings-form">
                <?php wp_nonce_field('kilismile_gateway_action', 'kilismile_gateway_nonce'); ?>
                
                <div class="settings-section">
                    <h2><?php _e('Payment Gateway Integration', 'kilismile'); ?></h2>
                    
                    <div class="setting-group">
                        <p class="section-description">
                            <?php _e('These are automated payment processing gateways that integrate with your payment system for real-time transaction processing.', 'kilismile'); ?>
                        </p>
                        
                        <!-- Selcom Payment Gateway -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_selcom_enabled" 
                                           value="1" 
                                           <?php checked($selcom_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-credit-card"></i>
                                        <?php _e('Selcom Payment Gateway', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$selcom_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="kilismile_selcom_api_key"><?php _e('Selcom API Key', 'kilismile'); ?></label>
                                        <input type="text" 
                                               name="kilismile_selcom_api_key" 
                                               id="kilismile_selcom_api_key"
                                               value="<?php echo esc_attr($selcom_api_key); ?>"
                                               placeholder="Enter your Selcom API Key">
                                        <small class="field-description"><?php _e('Your Selcom API Key from your Selcom merchant dashboard', 'kilismile'); ?></small>
                                    </div>
                                    <div class="form-field">
                                        <label for="kilismile_selcom_api_secret"><?php _e('Selcom API Secret', 'kilismile'); ?></label>
                                        <input type="password" 
                                               name="kilismile_selcom_api_secret" 
                                               id="kilismile_selcom_api_secret"
                                               value="<?php echo esc_attr($selcom_api_secret); ?>"
                                               placeholder="Enter your Selcom API Secret">
                                        <small class="field-description"><?php _e('Your Selcom API Secret for secure transactions', 'kilismile'); ?></small>
                                    </div>
                                    <div class="form-field">
                                        <label for="kilismile_selcom_vendor_id"><?php _e('Selcom Vendor ID', 'kilismile'); ?></label>
                                        <input type="text" 
                                               name="kilismile_selcom_vendor_id" 
                                               id="kilismile_selcom_vendor_id"
                                               value="<?php echo esc_attr($selcom_vendor_id); ?>"
                                               placeholder="Enter your Selcom Vendor ID">
                                        <small class="field-description"><?php _e('Your unique Selcom Vendor/Merchant ID', 'kilismile'); ?></small>
                                    </div>
                                </div>
                                <div class="integration-info">
                                    <div class="info-card">
                                        <h4><?php _e('Selcom Integration Status', 'kilismile'); ?></h4>
                                        <p><?php _e('Selcom Payment Gateway enables secure processing of credit cards, mobile money, and bank payments in Tanzania.', 'kilismile'); ?></p>
                                        <div class="status-indicators">
                                            <div class="status-item">
                                                <span class="status-label"><?php _e('API Configuration:', 'kilismile'); ?></span>
                                                <span class="status-value <?php echo ($selcom_api_key && $selcom_api_secret && $selcom_vendor_id) ? 'configured' : 'not-configured'; ?>">
                                                    <?php echo ($selcom_api_key && $selcom_api_secret && $selcom_vendor_id) ? __('Configured', 'kilismile') : __('Not Configured', 'kilismile'); ?>
                                                </span>
                                            </div>
                                            <div class="status-item">
                                                <span class="status-label"><?php _e('Webhook URL:', 'kilismile'); ?></span>
                                                <code><?php echo home_url('/wp-admin/admin-post.php?action=kilismile_selcom_webhook'); ?></code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Azam Pay Gateway -->
                        <div class="payment-method-config">
                            <div class="method-header">
                                <label class="toggle-label">
                                    <input type="checkbox" 
                                           name="kilismile_azam_pay_enabled" 
                                           value="1" 
                                           <?php checked($azam_pay_enabled, 1); ?>
                                           class="method-toggle">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        <i class="fas fa-mobile-alt"></i>
                                        <?php _e('Azam Pay Gateway', 'kilismile'); ?>
                                    </span>
                                </label>
                            </div>
                            <div class="method-settings" <?php echo !$azam_pay_enabled ? 'style="display: none;"' : ''; ?>>
                                <div class="integration-info">
                                    <div class="info-card">
                                        <h4><?php _e('Azam Pay Integration Status', 'kilismile'); ?></h4>
                                        <p><?php _e('Azam Pay Gateway enables mobile money payments and other local payment methods in Tanzania.', 'kilismile'); ?></p>
                                        <div class="status-indicators">
                                            <div class="status-item">
                                                <span class="status-label"><?php _e('API Configuration:', 'kilismile'); ?></span>
                                                <span class="status-value <?php echo get_option('kilismile_azam_api_key') ? 'configured' : 'not-configured'; ?>">
                                                    <?php echo get_option('kilismile_azam_api_key') ? __('Configured', 'kilismile') : __('Not Configured', 'kilismile'); ?>
                                                </span>
                                            </div>
                                            <div class="status-item">
                                                <span class="status-label"><?php _e('Callback URL:', 'kilismile'); ?></span>
                                                <code><?php echo home_url('/payment/callback/azam/'); ?></code>
                                            </div>
                                        </div>
                                        <a href="<?php echo admin_url('admin.php?page=kilismile-payment-settings'); ?>" class="button button-secondary">
                                            <?php _e('Configure API Keys', 'kilismile'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Integration Notes -->
                    <div class="setting-group">
                        <h3>
                            <i class="dashicons dashicons-info"></i>
                            <?php _e('Integration Notes', 'kilismile'); ?>
                        </h3>
                        <div class="info-grid">
                            <div class="info-card">
                                <h4><?php _e('Gateway vs Manual Methods', 'kilismile'); ?></h4>
                                <p><?php _e('Payment gateways process transactions automatically, while manual methods require donors to follow instructions and confirm payments manually.', 'kilismile'); ?></p>
                            </div>
                            <div class="info-card">
                                <h4><?php _e('Testing & Security', 'kilismile'); ?></h4>
                                <p><?php _e('Always test in sandbox mode first. Configure API keys in the Payment Settings page for secure transaction processing.', 'kilismile'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button for Gateway Integration -->
                <div class="section-save">
                    <button type="submit" name="save_gateway_integration" class="button button-primary button-large">
                        <i class="dashicons dashicons-admin-plugins"></i>
                        <?php _e('Save Gateway Settings', 'kilismile'); ?>
                    </button>
                    <p class="description">
                        <?php _e('Save gateway integration preferences.', 'kilismile'); ?>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Legacy Save All Settings (hidden by default) -->
        <div id="save-all-section" class="tab-content" style="display: none;">
            <form method="post" action="" class="settings-form">
                <?php wp_nonce_field('kilismile_settings_action', 'kilismile_settings_nonce'); ?>
                
                <!-- All form fields would be duplicated here for legacy support -->
                
                <div class="section-save">
                    <button type="submit" name="submit" class="button button-primary button-hero">
                        <i class="dashicons dashicons-yes-alt"></i>
                        <?php _e('Save All Settings', 'kilismile'); ?>
                    </button>
                    <p class="description">
                        <?php _e('Save all donation and payment configuration changes at once.', 'kilismile'); ?>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Quick Actions Panel -->
        <div class="quick-actions-panel">
            <div class="settings-section">
                <h3><?php _e('Quick Actions', 'kilismile'); ?></h3>
                <div class="quick-actions-grid">
                    <div class="quick-action">
                        <button type="button" class="button button-secondary" id="save-all-btn">
                            <i class="dashicons dashicons-yes-alt"></i>
                            <?php _e('Save All Changes', 'kilismile'); ?>
                        </button>
                        <p class="description"><?php _e('Save all sections at once', 'kilismile'); ?></p>
                    </div>
                    
                    <div class="quick-action">
                        <a href="<?php echo home_url('/donate'); ?>" class="button button-secondary" target="_blank">
                            <i class="dashicons dashicons-external"></i>
                            <?php _e('Test Donation Form', 'kilismile'); ?>
                        </a>
                        <p class="description"><?php _e('Preview donation form', 'kilismile'); ?></p>
                    </div>
                    
                    <div class="quick-action">
                        <a href="<?php echo admin_url('admin.php?page=kilismile-payment-settings'); ?>" class="button button-secondary">
                            <i class="dashicons dashicons-admin-settings"></i>
                            <?php _e('API Configuration', 'kilismile'); ?>
                        </a>
                        <p class="description"><?php _e('Configure payment APIs', 'kilismile'); ?></p>
                    </div>
                    
                    <div class="quick-action">
                        <a href="<?php echo admin_url('admin.php?page=kilismile-donations'); ?>" class="button button-secondary">
                            <i class="dashicons dashicons-chart-area"></i>
                            <?php _e('View Donations', 'kilismile'); ?>
                        </a>
                        <p class="description"><?php _e('Manage donations', 'kilismile'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden form for "Save All" functionality -->
    <form id="save-all-form" method="post" action="" style="display: none;">
        <?php wp_nonce_field('kilismile_settings_action', 'kilismile_save_all_nonce'); ?>
        <input type="hidden" name="submit" value="1">
        
        <!-- Donation Settings Fields -->
        <input type="hidden" name="kilismile_enable_donations" value="<?php echo $enable_donations ? '1' : '0'; ?>">
        <input type="hidden" name="kilismile_default_currency" value="<?php echo esc_attr($default_currency); ?>">
        <input type="hidden" name="kilismile_donation_goal_usd" value="<?php echo esc_attr($donation_goal_usd); ?>">
        <input type="hidden" name="kilismile_current_donations_usd" value="<?php echo esc_attr($current_donations_usd); ?>">
        <input type="hidden" name="kilismile_donation_goal_tzs" value="<?php echo esc_attr($donation_goal_tzs); ?>">
        <input type="hidden" name="kilismile_current_donations_tzs" value="<?php echo esc_attr($current_donations_tzs); ?>">
        <input type="hidden" name="kilismile_exchange_rate" value="<?php echo esc_attr($exchange_rate); ?>">
        
        <!-- Payment Method Fields - REMOVED to prevent conflicts with individual payment method saves -->
        <!-- Payment methods have their own dedicated save functionality -->
        
        <!-- Gateway Integration Fields - REMOVED to prevent conflicts -->
        <!-- Gateway methods have their own dedicated save functionality -->
    </form>
    
    <style>
        .nav-tab-wrapper {
            margin-bottom: 20px;
        }
        
        .nav-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .nav-tab:hover {
            background: #f0f0f1;
        }
        
        .nav-tab.nav-tab-active {
            background: #2271b1 !important;
            color: white !important;
        }
        
        .nav-tab.nav-tab-active:hover {
            background: #135e96 !important;
        }
        
        /* Section status indicators */
        .section-status {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
            font-weight: normal;
            background: #46b450;
            color: white;
            opacity: 0.8;
        }
        
        .section-status.modified {
            background: #ffb900;
            color: #000;
        }
        
        .section-status.saving {
            background: #0073aa;
            color: white;
        }
        
        .section-status.error {
            background: #dc3232;
            color: white;
        }
        
        .nav-tab .section-status {
            display: inline-block;
            vertical-align: middle;
        }
        
        .tab-content {
            display: none !important;
        }
        
        .tab-content.active {
            display: block !important;
        }
        
        .settings-section {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .setting-group {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2271b1;
        }
        
        .setting-group.master-toggle {
            border-left-color: #d63384;
            background: #fdf2f8;
        }
        
        .setting-group h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2271b1;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1em;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-field {
            margin-bottom: 15px;
        }
        
        .form-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-field input,
        .form-field select,
        .form-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-field input:focus,
        .form-field select:focus,
        .form-field textarea:focus {
            border-color: #2271b1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.1);
        }
        
        .form-field .description {
            margin-top: 5px;
            color: #666;
            font-size: 0.9em;
            font-style: italic;
        }
        
        /* Toggle Switches */
        .toggle-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            gap: 12px;
            font-weight: 600;
        }
        
        .toggle-label input[type="checkbox"] {
            display: none;
        }
        
        .toggle-slider {
            position: relative;
            width: 50px;
            height: 24px;
            background: #ccc;
            border-radius: 24px;
            transition: all 0.3s ease;
        }
        
        .toggle-slider:before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            top: 2px;
            left: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .toggle-label input[type="checkbox"]:checked + .toggle-slider {
            background: #2271b1;
        }
        
        .toggle-label input[type="checkbox"]:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .toggle-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1em;
        }
        
        /* Payment Method Configuration */
        .payment-method-config {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .method-header {
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .method-settings {
            padding: 20px;
            background: #fafafa;
        }
        
        /* Gateway Integration Styles */
        .integration-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }
        
        .info-card h4 {
            margin-top: 0;
            color: #17a2b8;
        }
        
        .status-indicators {
            margin: 15px 0;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: 600;
        }
        
        .status-value.configured {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-value.not-configured {
            color: #dc3545;
            font-weight: 600;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .submit-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-save {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2271b1;
        }
        
        .section-save .button {
            margin-bottom: 10px;
        }
        
        .quick-actions-panel {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #00a32a;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .quick-action {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .quick-action:hover {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .quick-action .button {
            width: 100%;
            margin-bottom: 8px;
        }
        
        .settings-form {
            position: relative;
        }
        
        .form-saved-indicator {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #00a32a;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .form-saved-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .section-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 10px;
            background: #f0f0f1;
            color: #646970;
        }
        
        .section-status.saved {
            background: #d5e8d4;
            color: #155724;
        }
        
        .section-status.modified {
            background: #fff3cd;
            color: #856404;
        }
        
        .button-hero {
            padding: 15px 30px !important;
            font-size: 16px !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-description {
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }
        
        /* Analytics Dashboard Styles */
        .analytics-dashboard {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-card.total-donations {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        }
        
        .stat-card.monthly-donations {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        }
        
        .stat-card.total-raised-usd {
            background: linear-gradient(135deg, #45b7d1 0%, #96c93d 100%);
        }
        
        .stat-card.total-raised-tzs {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-icon {
            font-size: 2.5em;
            opacity: 0.8;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-number {
            font-size: 1.8em;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 20px;
        }
        
        .chart-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .chart-container h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            text-align: center;
        }
        
        .realtime-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f0f8ff;
            border-radius: 6px;
            border: 1px solid #cce7ff;
        }
        
        .realtime-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #0073aa;
        }
        
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #00a32a;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Goal Progress Bars */
        .goal-progress {
            margin-top: 8px;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4ecdc4 0%, #44a08d 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-fill[style*="100%"] {
            background: linear-gradient(90deg, #00a32a 0%, #4ecdc4 100%);
        }
        
        /* Form Field Enhancements */
        .form-field input[readonly] {
            background: #f8f9fa;
            border-color: #e0e0e0;
            color: #666;
        }
        
        .form-field small.description {
            color: #666;
            font-style: italic;
            font-size: 0.85em;
        }
        
        /* Campaign Management Styles */
        .campaigns-section {
            margin-top: 20px;
        }
        
        .campaigns-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .campaigns-list {
            display: grid;
            gap: 20px;
        }
        
        .campaign-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: box-shadow 0.3s ease;
        }
        
        .campaign-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .campaign-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .campaign-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .campaign-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .campaign-status.active {
            background: #d5e8d4;
            color: #155724;
        }
        
        .campaign-status.ended {
            background: #f8d7da;
            color: #721c24;
        }
        
        .campaign-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .campaign-stat {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .campaign-stat-value {
            font-size: 1.3em;
            font-weight: bold;
            color: #2271b1;
        }
        
        .campaign-stat-label {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        
        .campaign-description {
            color: #666;
            font-style: italic;
            margin: 15px 0;
            line-height: 1.5;
        }
        
        .campaign-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }
        
        /* Modal Styles */
        .campaign-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-tab {
                font-size: 12px;
                padding: 8px 12px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .realtime-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
    
    <script>
        (function($) {
            'use strict';
            
            $(document).ready(function() {
                console.log('Theme Settings Initialized');
                
                // Ensure we have jQuery
                if (typeof $ === 'undefined') {
                    console.error('jQuery not loaded!');
                    return;
                }
                
                // Tab switching with enhanced error handling
                $('.nav-tab').off('click.kilismile').on('click.kilismile', function(e) {
                    e.preventDefault();
                    
                    var $this = $(this);
                    var targetTab = $this.attr('data-tab');
                    
                    console.log('Switching to tab:', targetTab);
                    
                    if (!targetTab) {
                        console.error('No data-tab attribute found');
                        return;
                    }
                    
                    // Update tab states
                    $('.nav-tab').removeClass('nav-tab-active');
                    $this.addClass('nav-tab-active');
                    
                    // Update content with animation
                    $('.tab-content').removeClass('active').hide();
                    var $targetContent = $('#' + targetTab);
                    
                    if ($targetContent.length === 0) {
                        console.error('Target tab content not found:', targetTab);
                        return;
                    }
                    
                    $targetContent.addClass('active').fadeIn(200);
                    
                    // Initialize status for the current tab
                    setTimeout(function() {
                        updateTabStatus(targetTab);
                    }, 100);
                
                    // Update URL hash without page reload
                    if (history.pushState) {
                        history.pushState(null, null, '#' + targetTab);
                    }
                });
                
                // Helper function to update tab status
                function updateTabStatus(targetTab) {
                    var form = $('#' + targetTab + ' .settings-form');
                    var formId = targetTab;
                    var statusSelector = '#' + formId.replace('-settings', '-status').replace('-methods', '-status').replace('-integration', '-status');
                    
                    if (form.hasClass('modified')) {
                        $(statusSelector).addClass('modified').text('<?php _e('Modified', 'kilismile'); ?>');
                    }
                }
                
                // Load tab from URL hash
                function loadTabFromHash() {
                    var hash = window.location.hash.substr(1);
                    console.log('Loading tab from hash:', hash);
                    if (hash && $('#' + hash).length) {
                        console.log('Found tab for hash, clicking...');
                        $('.nav-tab[data-tab="' + hash + '"]').trigger('click.kilismile');
                    } else {
                        console.log('No valid hash found, staying on default tab');
                    }
                }
            
            // Load tab on page load
            loadTabFromHash();
            
            // Handle browser back/forward
            $(window).on('popstate', function() {
                loadTabFromHash();
            });
            
            // Method toggle functionality
            $('.method-toggle').on('change', function() {
                var methodSettings = $(this).closest('.payment-method-config').find('.method-settings');
                if ($(this).is(':checked')) {
                    methodSettings.slideDown(300);
                } else {
                    methodSettings.slideUp(300);
                }
                markFormModified($(this).closest('.settings-form'));
            });
            
            // Master toggle functionality
            $('.master-toggle-input').on('change', function() {
                var subsettings = $('.donation-subsettings');
                if ($(this).is(':checked')) {
                    subsettings.css({
                        'opacity': '1',
                        'pointer-events': 'auto'
                    });
                } else {
                    subsettings.css({
                        'opacity': '0.6',
                        'pointer-events': 'none'
                    });
                }
                markFormModified($(this).closest('.settings-form'));
            });
            
            // Initialize form tracking
            function initializeFormTracking() {
                // Track form modifications
                $('.settings-form input, .settings-form select, .settings-form textarea').off('change.kilismile input.kilismile').on('change.kilismile input.kilismile', function() {
                    markFormModified($(this).closest('.settings-form'));
                });
            }
            
            function markFormModified(form) {
                var indicator = form.find('.form-saved-indicator');
                if (indicator.length === 0) {
                    indicator = $('<div class="form-saved-indicator">Unsaved Changes</div>');
                    form.append(indicator);
                }
                indicator.removeClass('show').text('Unsaved Changes').addClass('show');
                
                // Update tab status indicator
                var formId = form.closest('.tab-content').attr('id');
                var statusSelector = '#' + formId.replace('-settings', '-status').replace('-methods', '-status').replace('-integration', '-status');
                $(statusSelector).removeClass('saving error').addClass('modified').text('<?php _e('Modified', 'kilismile'); ?>');
                
                // Update save button text
                var saveBtn = form.find('button[type="submit"]');
                var originalText = saveBtn.data('original-text') || saveBtn.text();
                saveBtn.data('original-text', originalText);
                
                if (!saveBtn.text().includes('*')) {
                    saveBtn.text('* ' + originalText.replace('* ', ''));
                }
            }
            
            function markFormSaved(form) {
                var indicator = form.find('.form-saved-indicator');
                if (indicator.length > 0) {
                    indicator.text('Saved!').addClass('show');
                    setTimeout(function() {
                        indicator.removeClass('show');
                    }, 3000);
                }
                
                // Update tab status indicator
                var formId = form.closest('.tab-content').attr('id');
                var statusSelector = '#' + formId.replace('-settings', '-status').replace('-methods', '-status').replace('-integration', '-status');
                $(statusSelector).removeClass('modified saving error').text('<?php _e('Saved', 'kilismile'); ?>');
                
                // Reset save button text
                var saveBtn = form.find('button[type="submit"]');
                var originalText = saveBtn.data('original-text');
                if (originalText) {
                    saveBtn.text(originalText.replace('* ', ''));
                }
            }
            
            // Handle form submissions with AJAX (optional enhancement)
            $('.settings-form').on('submit', function(e) {
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.text();
                
                // Update tab status to saving
                var formId = form.closest('.tab-content').attr('id');
                var statusSelector = '#' + formId.replace('-settings', '-status').replace('-methods', '-status').replace('-integration', '-status');
                $(statusSelector).removeClass('modified error').addClass('saving').text('<?php _e('Saving...', 'kilismile'); ?>');
                
                // Show loading state
                submitBtn.prop('disabled', true).html('<i class="dashicons dashicons-update spin"></i> Saving...');
                
                // Let the form submit normally, but show feedback
                setTimeout(function() {
                    if (!form.find('.notice').length) {
                        markFormSaved(form);
                    } else {
                        // Check for error notices
                        if (form.find('.notice-error').length) {
                            $(statusSelector).removeClass('saving').addClass('error').text('<?php _e('Error', 'kilismile'); ?>');
                        } else {
                            markFormSaved(form);
                        }
                    }
                    submitBtn.prop('disabled', false).html(originalText);
                }, 1000);
            });
            
            // Save All functionality
            $('#save-all-btn').on('click', function(e) {
                e.preventDefault();
                
                if (confirm('<?php echo esc_js(__('Are you sure you want to save all changes across all sections?', 'kilismile')); ?>')) {
                    // Update hidden form with current values
                    updateSaveAllForm();
                    
                    // Submit the hidden form
                    $('#save-all-form').submit();
                }
            });
            
            function updateSaveAllForm() {
                // Donation settings
                var donationForm = $('#donation-settings .settings-form');
                $('#save-all-form input[name="kilismile_enable_donations"]').val(
                    donationForm.find('input[name="kilismile_enable_donations"]').is(':checked') ? '1' : '0'
                );
                $('#save-all-form input[name="kilismile_default_currency"]').val(
                    donationForm.find('select[name="kilismile_default_currency"]').val()
                );
                $('#save-all-form input[name="kilismile_donation_goal_usd"]').val(
                    donationForm.find('input[name="kilismile_donation_goal_usd"]').val()
                );
                $('#save-all-form input[name="kilismile_current_donations_usd"]').val(
                    donationForm.find('input[name="kilismile_current_donations_usd"]').val()
                );
                $('#save-all-form input[name="kilismile_donation_goal_tzs"]').val(
                    donationForm.find('input[name="kilismile_donation_goal_tzs"]').val()
                );
                $('#save-all-form input[name="kilismile_current_donations_tzs"]').val(
                    donationForm.find('input[name="kilismile_current_donations_tzs"]').val()
                );
                $('#save-all-form input[name="kilismile_exchange_rate"]').val(
                    donationForm.find('input[name="kilismile_exchange_rate"]').val()
                );
                
                // Payment methods - REMOVED to prevent conflicts with individual saves
                // Payment methods have their own dedicated save functionality
                
                // Gateway integration - REMOVED to prevent conflicts with individual saves  
                // Gateway methods have their own dedicated save functionality
            }
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+S to save current section
                if (e.ctrlKey && e.which === 83) {
                    e.preventDefault();
                    var activeTab = $('.tab-content.active');
                    var saveBtn = activeTab.find('button[type="submit"]');
                    if (saveBtn.length) {
                        saveBtn.trigger('click');
                    }
                }
                
                // Ctrl+Shift+S to save all
                if (e.ctrlKey && e.shiftKey && e.which === 83) {
                    e.preventDefault();
                    $('#save-all-btn').trigger('click');
                }
            });
            
            // Auto-save draft functionality (optional)
            var autoSaveTimeout;
            $('.settings-form input, .settings-form select, .settings-form textarea').on('change input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(function() {
                    // Save draft to localStorage
                    saveDraft();
                }, 2000);
            });
            
            function saveDraft() {
                var draftData = {};
                
                $('.settings-form').each(function() {
                    var form = $(this);
                    var formData = form.serializeArray();
                    
                    $.each(formData, function(i, field) {
                        draftData[field.name] = field.value;
                    });
                });
                
                localStorage.setItem('kilismile_settings_draft', JSON.stringify(draftData));
                console.log('Settings draft saved');
            }
            
            function loadDraft() {
                var draft = localStorage.getItem('kilismile_settings_draft');
                if (draft) {
                    try {
                        var draftData = JSON.parse(draft);
                        // Could implement draft loading if needed
                    } catch (e) {
                        console.error('Error loading settings draft:', e);
                    }
                }
            }
            
            // Show tooltips for configuration status
            $('.status-value').each(function() {
                var status = $(this);
                if (status.hasClass('not-configured')) {
                    status.attr('title', 'Click "Configure API Keys" to set up this gateway');
                } else if (status.hasClass('configured')) {
                    status.attr('title', 'Gateway is properly configured and ready to use');
                }
            });
            
            // Add spinning animation class
            $('<style>.spin { animation: spin 1s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>').appendTo('head');
            
            <?php if ($saved_section): ?>
            // Show success message for saved section
            var savedSection = '<?php echo esc_js($saved_section); ?>';
            if (savedSection && savedSection !== 'all') {
                // Switch to the saved tab
                $('.nav-tab[data-tab="' + savedSection + '-settings"], .nav-tab[data-tab="' + savedSection + '-methods"], .nav-tab[data-tab="gateway-integration"]').trigger('click');
                
                // Show saved indicator
                setTimeout(function() {
                    var form = $('.tab-content.active .settings-form');
                    markFormSaved(form);
                }, 500);
            }
            <?php endif; ?>
            
            // Initialize status indicators based on form state
            function initializeStatusIndicators() {
                $('.settings-form').each(function() {
                    var form = $(this);
                    var formId = form.closest('.tab-content').attr('id');
                    var statusSelector = '#' + formId.replace('-settings', '-status').replace('-methods', '-status').replace('-integration', '-status');
                    
                    // Check if form has any errors
                    if (form.find('.notice-error').length > 0) {
                        $(statusSelector).removeClass('modified saving').addClass('error').text('<?php _e('Error', 'kilismile'); ?>');
                    } else if (form.find('.notice-success').length > 0) {
                        $(statusSelector).removeClass('modified saving error').text('<?php _e('Saved', 'kilismile'); ?>');
                    } else {
                        $(statusSelector).removeClass('modified saving error').text('<?php _e('Ready', 'kilismile'); ?>');
                    }
                });
            }
            
            // Initialize everything
            function initializeThemeSettings() {
                console.log('Initializing theme settings...');
                
                // Initialize status indicators
                initializeStatusIndicators();
                
                // Load tab from URL hash or default
                loadTabFromHash();
                
                // Initialize form tracking
                initializeFormTracking();
                
                // Initialize analytics if Chart.js is available
                if (typeof Chart !== 'undefined' && typeof kilismileAnalytics !== 'undefined') {
                    initializeAnalytics();
                }
                
                // Initialize campaign management
                initializeCampaignManagement();
                
                console.log('Theme settings initialized successfully');
            }
            
            // Analytics initialization
            function initializeAnalytics() {
                console.log('Initializing analytics charts...');
                
                // Monthly trends chart
                initializeMonthlyTrendsChart();
                
                // Payment methods chart
                initializePaymentMethodsChart();
                
                // Set up real-time updates
                setupRealTimeUpdates();
            }
            
            function initializeMonthlyTrendsChart() {
                const ctx = document.getElementById('monthly-trends-chart');
                if (!ctx || !kilismileAnalytics.monthlyData) return;
                
                const monthlyData = kilismileAnalytics.monthlyData.slice(0, 12).reverse(); // Last 12 months
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(item => {
                            const date = new Date(item.month + '-01');
                            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                        }),
                        datasets: [{
                            label: 'Donations Count',
                            data: monthlyData.map(item => item.count),
                            borderColor: '#2271b1',
                            backgroundColor: 'rgba(34, 113, 177, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        }, {
                            label: 'Amount (USD)',
                            data: monthlyData.map(item => item.amount),
                            borderColor: '#00a32a',
                            backgroundColor: 'rgba(0, 163, 42, 0.1)',
                            tension: 0.4,
                            fill: false,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Number of Donations'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Amount (USD)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        }
                    }
                });
            }
            
            function initializePaymentMethodsChart() {
                const ctx = document.getElementById('payment-methods-chart');
                if (!ctx || !kilismileAnalytics.paymentMethods) return;
                
                const paymentData = kilismileAnalytics.paymentMethods;
                const colors = [
                    '#ff6b6b', '#4ecdc4', '#45b7d1', '#96c93d', 
                    '#f093fb', '#feca57', '#ff9ff3', '#54a0ff'
                ];
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: paymentData.map(item => item.method),
                        datasets: [{
                            data: paymentData.map(item => item.count),
                            backgroundColor: colors.slice(0, paymentData.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
            
            function setupRealTimeUpdates() {
                // Refresh analytics button
                $('#refresh-analytics').on('click', function() {
                    refreshAnalyticsData();
                });
                
                // Auto-refresh every 5 minutes
                setInterval(function() {
                    refreshAnalyticsData();
                }, 5 * 60 * 1000);
                
                // Update last update time
                updateLastUpdateTime();
                setInterval(updateLastUpdateTime, 60 * 1000); // Update every minute
            }
            
            function refreshAnalyticsData() {
                const refreshBtn = $('#refresh-analytics');
                const originalText = refreshBtn.text();
                
                refreshBtn.prop('disabled', true).html('<i class="dashicons dashicons-update spin"></i> Refreshing...');
                
                $.ajax({
                    url: kilismileAnalytics.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'kilismile_refresh_analytics',
                        nonce: kilismileAnalytics.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the analytics data
                            kilismileAnalytics = response.data;
                            
                            // Refresh charts
                            Chart.helpers.each(Chart.instances, function(instance) {
                                instance.destroy();
                            });
                            
                            initializeAnalytics();
                            
                            // Update stats cards
                            updateStatsCards(response.data.stats);
                            
                            updateLastUpdateTime();
                        }
                    },
                    error: function() {
                        console.error('Failed to refresh analytics data');
                    },
                    complete: function() {
                        refreshBtn.prop('disabled', false).text(originalText);
                    }
                });
            }
            
            function updateStatsCards(stats) {
                if (!stats) return;
                
                $('.stat-card.total-donations .stat-number').text(
                    new Intl.NumberFormat().format(stats.total_donations || 0)
                );
                $('.stat-card.monthly-donations .stat-number').text(
                    new Intl.NumberFormat().format(stats.monthly_count || 0)
                );
                $('.stat-card.total-raised-usd .stat-number').text(
                    '$' + new Intl.NumberFormat().format(stats.total_amount_usd || 0)
                );
                $('.stat-card.total-raised-tzs .stat-number').text(
                    'TSh ' + new Intl.NumberFormat().format(stats.total_amount_tzs || 0)
                );
            }
            
            function updateLastUpdateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                $('#last-update').text('Last updated: ' + timeString);
            }
            
            // Campaign Management Functions
            function initializeCampaignManagement() {
                console.log('Initializing campaign management...');
                
                // Create campaign button
                $('#create-campaign-btn').on('click', function() {
                    openCampaignModal();
                });
                
                // Modal close buttons
                $('.modal-close, .modal-cancel').on('click', function() {
                    closeCampaignModal();
                });
                
                // Save campaign button
                $('#save-campaign').on('click', function() {
                    saveCampaign();
                });
                
                // Edit campaign buttons
                $('.edit-campaign').on('click', function() {
                    const campaignId = $(this).data('campaign-id');
                    editCampaign(campaignId);
                });
                
                // View campaign buttons
                $('.view-campaign').on('click', function() {
                    const campaignId = $(this).data('campaign-id');
                    viewCampaign(campaignId);
                });
                
                // Close modal when clicking outside
                $(document).on('click', '.campaign-modal', function(e) {
                    if (e.target === this) {
                        closeCampaignModal();
                    }
                });
                
                // Set minimum end date to tomorrow
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                $('#campaign-end').attr('min', tomorrow.toISOString().split('T')[0]);
            }
            
            function openCampaignModal(campaignData = null) {
                if (campaignData) {
                    // Editing existing campaign
                    $('#campaign-name').val(campaignData.name);
                    $('#campaign-goal').val(campaignData.goal);
                    $('#campaign-start').val(campaignData.start_date);
                    $('#campaign-end').val(campaignData.end_date);
                    $('#campaign-description').val(campaignData.description);
                    $('#campaign-image').val(campaignData.image || '');
                    
                    $('.modal-header h3').text('<?php echo esc_js(__('Edit Campaign', 'kilismile')); ?>');
                    $('#save-campaign').text('<?php echo esc_js(__('Update Campaign', 'kilismile')); ?>');
                } else {
                    // Creating new campaign
                    $('#campaign-form')[0].reset();
                    $('#campaign-start').val(new Date().toISOString().split('T')[0]);
                    
                    $('.modal-header h3').text('<?php echo esc_js(__('Create New Campaign', 'kilismile')); ?>');
                    $('#save-campaign').text('<?php echo esc_js(__('Create Campaign', 'kilismile')); ?>');
                }
                
                $('#campaign-modal').fadeIn(300);
                $('#campaign-name').focus();
            }
            
            function closeCampaignModal() {
                $('#campaign-modal').fadeOut(300);
            }
            
            function saveCampaign() {
                const form = $('#campaign-form')[0];
                
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                const formData = new FormData(form);
                const campaignData = {
                    name: formData.get('campaign_name'),
                    goal: parseFloat(formData.get('campaign_goal')),
                    start_date: formData.get('campaign_start'),
                    end_date: formData.get('campaign_end'),
                    description: formData.get('campaign_description'),
                    image: formData.get('campaign_image')
                };
                
                // Validate dates
                const startDate = new Date(campaignData.start_date);
                const endDate = new Date(campaignData.end_date);
                
                if (endDate <= startDate) {
                    alert('<?php echo esc_js(__('End date must be after start date.', 'kilismile')); ?>');
                    return;
                }
                
                const saveBtn = $('#save-campaign');
                const originalText = saveBtn.text();
                
                saveBtn.prop('disabled', true).text('<?php echo esc_js(__('Saving...', 'kilismile')); ?>');
                
                // Here you would normally send AJAX request to save the campaign
                // For now, we'll simulate it with a timeout
                setTimeout(function() {
                    // Create campaign card HTML
                    const campaignCard = createCampaignCard({
                        id: 'campaign_' + Date.now(),
                        name: campaignData.name,
                        goal: campaignData.goal,
                        raised: 0,
                        end_date: campaignData.end_date,
                        status: 'active',
                        description: campaignData.description
                    });
                    
                    $('.campaigns-list').append(campaignCard);
                    
                    // Re-initialize event handlers for new campaign
                    initializeCampaignManagement();
                    
                    saveBtn.prop('disabled', false).text(originalText);
                    closeCampaignModal();
                    
                    // Show success message
                    $('<div class="notice notice-success is-dismissible"><p><?php echo esc_js(__('Campaign created successfully!', 'kilismile')); ?></p></div>')
                        .insertAfter('.campaigns-header')
                        .delay(3000)
                        .fadeOut();
                }, 1000);
            }
            
            function createCampaignCard(campaign) {
                const progressPercent = Math.min(100, (campaign.raised / campaign.goal) * 100);
                const endDate = new Date(campaign.end_date);
                const formattedDate = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                
                return `
                    <div class="campaign-card" data-campaign-id="${campaign.id}">
                        <div class="campaign-header">
                            <h4 class="campaign-title">${campaign.name}</h4>
                            <span class="campaign-status ${campaign.status}">${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}</span>
                        </div>
                        
                        <div class="campaign-stats">
                            <div class="campaign-stat">
                                <div class="campaign-stat-value">$${campaign.raised.toLocaleString()}</div>
                                <div class="campaign-stat-label"><?php echo esc_js(__('Raised', 'kilismile')); ?></div>
                            </div>
                            <div class="campaign-stat">
                                <div class="campaign-stat-value">$${campaign.goal.toLocaleString()}</div>
                                <div class="campaign-stat-label"><?php echo esc_js(__('Goal', 'kilismile')); ?></div>
                            </div>
                            <div class="campaign-stat">
                                <div class="campaign-stat-value">${progressPercent.toFixed(1)}%</div>
                                <div class="campaign-stat-label"><?php echo esc_js(__('Progress', 'kilismile')); ?></div>
                            </div>
                            <div class="campaign-stat">
                                <div class="campaign-stat-value">${formattedDate}</div>
                                <div class="campaign-stat-label"><?php echo esc_js(__('End Date', 'kilismile')); ?></div>
                            </div>
                        </div>
                        
                        <div class="goal-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${progressPercent}%"></div>
                            </div>
                        </div>
                        
                        <p class="campaign-description">${campaign.description}</p>
                        
                        <div class="campaign-actions">
                            <button type="button" class="button button-small edit-campaign" data-campaign-id="${campaign.id}">
                                <i class="dashicons dashicons-edit"></i>
                                <?php echo esc_js(__('Edit', 'kilismile')); ?>
                            </button>
                            <button type="button" class="button button-small view-campaign" data-campaign-id="${campaign.id}">
                                <i class="dashicons dashicons-visibility"></i>
                                <?php echo esc_js(__('View', 'kilismile')); ?>
                            </button>
                        </div>
                    </div>
                `;
            }
            
            function editCampaign(campaignId) {
                // In a real implementation, you would fetch campaign data from the server
                alert('<?php echo esc_js(__('Edit campaign functionality would be implemented here.', 'kilismile')); ?>');
            }
            
            function viewCampaign(campaignId) {
                // In a real implementation, you would show campaign details or redirect to campaign page
                const campaignUrl = '<?php echo home_url('/donate'); ?>?campaign=' + campaignId;
                window.open(campaignUrl, '_blank');
            }
            
            // Call initialization
            initializeThemeSettings();
            
            }); // End document ready
            
        })(jQuery); // End jQuery wrapper
    </script>
    
    <?php
}

/**
 * Payment Settings Page (Enhanced)
 */
function kilismile_payment_settings_page() {
    // Include the existing payment settings page but with updates
    include get_template_directory() . '/admin/payment-settings.php';
}

/**
 * Donations Management Page
 */
function kilismile_donations_page() {
    // Include the existing donations admin page
    include get_template_directory() . '/admin/payment-admin.php';
}

/**
 * Update theme customizer functions to use database options instead
 */
function kilismile_update_customizer_functions() {
    // Override existing customizer functions
    remove_action('customize_register', 'kilismile_donation_customizer');
    
    // Add admin notice about moved settings
    add_action('admin_notices', function() {
        if (is_customize_preview() || (isset($_GET['page']) && $_GET['page'] === 'customize.php')) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>' . __('KiliSmile Settings Moved!', 'kilismile') . '</strong> ';
            echo sprintf(
                __('Donation and payment settings have been moved to a dedicated admin page. <a href="%s">Click here to access the new settings</a>.', 'kilismile'),
                admin_url('admin.php?page=kilismile-settings')
            );
            echo '</p>';
            echo '</div>';
        }
    });
}
add_action('init', 'kilismile_update_customizer_functions', 20);

/**
 * Override theme_mod functions to use database options
 */
function kilismile_get_theme_option($option, $default = '') {
    // Map old theme_mod names to new option names
    $option_map = array(
        'kilismile_enable_donations' => 'kilismile_enable_donations',
        'kilismile_default_currency' => 'kilismile_default_currency',
        'kilismile_donation_goal_usd' => 'kilismile_donation_goal_usd',
        'kilismile_current_donations_usd' => 'kilismile_current_donations_usd',
        'kilismile_donation_goal_tzs' => 'kilismile_donation_goal_tzs',
        'kilismile_current_donations_tzs' => 'kilismile_current_donations_tzs',
        'kilismile_exchange_rate' => 'kilismile_exchange_rate',
        'kilismile_paypal_email' => 'kilismile_paypal_email',
        'kilismile_stripe_enabled' => 'kilismile_stripe_enabled',
        'kilismile_stripe_public_key' => 'kilismile_stripe_public_key',
        'kilismile_stripe_secret_key' => 'kilismile_stripe_secret_key',
        'kilismile_wire_transfer_details' => 'kilismile_wire_transfer_details',
        'kilismile_mpesa_enabled' => 'kilismile_mpesa_enabled',
        'kilismile_mpesa_number' => 'kilismile_mpesa_number',
        'kilismile_mpesa_name' => 'kilismile_mpesa_name',
        'kilismile_tigo_pesa_enabled' => 'kilismile_tigo_pesa_enabled',
        'kilismile_tigo_pesa_number' => 'kilismile_tigo_pesa_number',
        'kilismile_airtel_money_enabled' => 'kilismile_airtel_money_enabled',
        'kilismile_airtel_money_number' => 'kilismile_airtel_money_number',
        'kilismile_local_bank_enabled' => 'kilismile_local_bank_enabled',
        'kilismile_local_bank_details' => 'kilismile_local_bank_details',
    );
    
    $option_name = isset($option_map[$option]) ? $option_map[$option] : $option;
    return get_option($option_name, $default);
}

/**
 * Helper function to check if payment method is enabled
 */
function kilismile_is_payment_method_enabled($method) {
    $enabled_options = array(
        'paypal' => get_option('kilismile_paypal_enabled', 0) && get_option('kilismile_paypal_email'),
        'stripe' => get_option('kilismile_stripe_enabled', 0) && get_option('kilismile_stripe_public_key'),
        'wire_transfer' => get_option('kilismile_wire_transfer_enabled', 0) && get_option('kilismile_wire_transfer_details'),
        'mpesa' => get_option('kilismile_mpesa_enabled', 1) && get_option('kilismile_mpesa_number'),
        'tigo_pesa' => get_option('kilismile_tigo_pesa_enabled', 0) && get_option('kilismile_tigo_pesa_number'),
        'airtel_money' => get_option('kilismile_airtel_money_enabled', 0) && get_option('kilismile_airtel_money_number'),
        'local_bank' => get_option('kilismile_local_bank_enabled', 1) && get_option('kilismile_local_bank_details'),
        'selcom' => get_option('kilismile_selcom_enabled', 0),
        'azam_pay' => get_option('kilismile_azam_pay_enabled', 0),
    );
    
    return isset($enabled_options[$method]) ? $enabled_options[$method] : false;
}


