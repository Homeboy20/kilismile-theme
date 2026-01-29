<?php
/**
 * KiliSmile Payments - Settings Configuration Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submissions
if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'kilismile_payments_settings')) {
    // Save general settings
    update_option('kilismile_payments_mode', sanitize_text_field($_POST['payment_mode']));
    update_option('kilismile_payments_currency', sanitize_text_field($_POST['default_currency']));
    update_option('kilismile_payments_currency_position', sanitize_text_field($_POST['currency_position']));
    update_option('kilismile_payments_decimal_places', intval($_POST['decimal_places']));
    update_option('kilismile_payments_thousand_separator', sanitize_text_field($_POST['thousand_separator']));
    update_option('kilismile_payments_decimal_separator', sanitize_text_field($_POST['decimal_separator']));
    
    // Save email settings
    update_option('kilismile_payments_email_from_name', sanitize_text_field($_POST['email_from_name']));
    update_option('kilismile_payments_email_from_address', sanitize_email($_POST['email_from_address']));
    update_option('kilismile_payments_admin_email', sanitize_email($_POST['admin_email']));
    update_option('kilismile_payments_send_admin_notifications', isset($_POST['send_admin_notifications']) ? 1 : 0);
    update_option('kilismile_payments_send_donor_receipts', isset($_POST['send_donor_receipts']) ? 1 : 0);
    
    // Save security settings
    update_option('kilismile_payments_rate_limiting', isset($_POST['enable_rate_limiting']) ? 1 : 0);
    update_option('kilismile_payments_rate_limit_attempts', intval($_POST['rate_limit_attempts']));
    update_option('kilismile_payments_rate_limit_window', intval($_POST['rate_limit_window']));
    update_option('kilismile_payments_fraud_detection', isset($_POST['enable_fraud_detection']) ? 1 : 0);
    update_option('kilismile_payments_max_amount', floatval($_POST['max_donation_amount']));
    update_option('kilismile_payments_min_amount', floatval($_POST['min_donation_amount']));
    
    // Save gateway order
    if (isset($_POST['gateway_order']) && is_array($_POST['gateway_order'])) {
        $gateway_order = array_map('sanitize_text_field', $_POST['gateway_order']);
        update_option('kilismile_payments_gateway_order', $gateway_order);
    }
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'kilismile-payments') . '</p></div>';
}

// Get current settings
$payment_mode = get_option('kilismile_payments_mode', 'live');
$default_currency = get_option('kilismile_payments_currency', 'USD');
$currency_position = get_option('kilismile_payments_currency_position', 'left');
$decimal_places = get_option('kilismile_payments_decimal_places', 2);
$thousand_separator = get_option('kilismile_payments_thousand_separator', ',');
$decimal_separator = get_option('kilismile_payments_decimal_separator', '.');

$email_from_name = get_option('kilismile_payments_email_from_name', get_bloginfo('name'));
$email_from_address = get_option('kilismile_payments_email_from_address', get_option('admin_email'));
$admin_email = get_option('kilismile_payments_admin_email', get_option('admin_email'));
$send_admin_notifications = get_option('kilismile_payments_send_admin_notifications', 1);
$send_donor_receipts = get_option('kilismile_payments_send_donor_receipts', 1);

$rate_limiting = get_option('kilismile_payments_rate_limiting', 1);
$rate_limit_attempts = get_option('kilismile_payments_rate_limit_attempts', 5);
$rate_limit_window = get_option('kilismile_payments_rate_limit_window', 60);
$fraud_detection = get_option('kilismile_payments_fraud_detection', 1);
$max_amount = get_option('kilismile_payments_max_amount', 10000);
$min_amount = get_option('kilismile_payments_min_amount', 1);

$gateway_order = get_option('kilismile_payments_gateway_order', array('selcom', 'tigo_pesa', 'airtel_money', 'halopesa', 'azam_pay'));

// Get available gateways
$gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();
?>

<div class="wrap">
    <h1><?php _e('Payment Settings', 'kilismile-payments'); ?></h1>
    
    <form method="post" action="" class="kilismile-payments-settings-form">
        <?php wp_nonce_field('kilismile_payments_settings'); ?>
        
        <div class="settings-tabs">
            <nav class="nav-tab-wrapper">
                <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'kilismile-payments'); ?></a>
                <a href="#gateways" class="nav-tab"><?php _e('Payment Gateways', 'kilismile-payments'); ?></a>
                <a href="#email" class="nav-tab"><?php _e('Email', 'kilismile-payments'); ?></a>
                <a href="#security" class="nav-tab"><?php _e('Security', 'kilismile-payments'); ?></a>
                <a href="#advanced" class="nav-tab"><?php _e('Advanced', 'kilismile-payments'); ?></a>
            </nav>
            
            <!-- General Settings -->
            <div id="general" class="tab-content active">
                <h2><?php _e('General Settings', 'kilismile-payments'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Payment Mode', 'kilismile-payments'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="payment_mode" value="sandbox" <?php checked($payment_mode, 'sandbox'); ?>>
                                    <span><?php _e('Sandbox (Testing)', 'kilismile-payments'); ?></span>
                                </label>
                                <br>
                                <label>
                                    <input type="radio" name="payment_mode" value="live" <?php checked($payment_mode, 'live'); ?>>
                                    <span><?php _e('Live (Production)', 'kilismile-payments'); ?></span>
                                </label>
                            </fieldset>
                            <p class="description"><?php _e('Use sandbox mode for testing payments without real transactions.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Default Currency', 'kilismile-payments'); ?></th>
                        <td>
                            <select name="default_currency">
                                <option value="USD" <?php selected($default_currency, 'USD'); ?>>USD - US Dollar</option>
                                <option value="TZS" <?php selected($default_currency, 'TZS'); ?>>TZS - Tanzanian Shilling</option>
                                <option value="EUR" <?php selected($default_currency, 'EUR'); ?>>EUR - Euro</option>
                                <option value="GBP" <?php selected($default_currency, 'GBP'); ?>>GBP - British Pound</option>
                            </select>
                            <p class="description"><?php _e('The default currency for new donations.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Currency Position', 'kilismile-payments'); ?></th>
                        <td>
                            <select name="currency_position">
                                <option value="left" <?php selected($currency_position, 'left'); ?>><?php _e('Left ($99.99)', 'kilismile-payments'); ?></option>
                                <option value="right" <?php selected($currency_position, 'right'); ?>><?php _e('Right (99.99$)', 'kilismile-payments'); ?></option>
                                <option value="left_space" <?php selected($currency_position, 'left_space'); ?>><?php _e('Left with space ($ 99.99)', 'kilismile-payments'); ?></option>
                                <option value="right_space" <?php selected($currency_position, 'right_space'); ?>><?php _e('Right with space (99.99 $)', 'kilismile-payments'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Number Format', 'kilismile-payments'); ?></th>
                        <td>
                            <fieldset>
                                <label for="decimal_places"><?php _e('Decimal Places:', 'kilismile-payments'); ?></label>
                                <input type="number" name="decimal_places" id="decimal_places" value="<?php echo esc_attr($decimal_places); ?>" min="0" max="4" class="small-text">
                                <br><br>
                                
                                <label for="thousand_separator"><?php _e('Thousand Separator:', 'kilismile-payments'); ?></label>
                                <input type="text" name="thousand_separator" id="thousand_separator" value="<?php echo esc_attr($thousand_separator); ?>" class="small-text">
                                <br><br>
                                
                                <label for="decimal_separator"><?php _e('Decimal Separator:', 'kilismile-payments'); ?></label>
                                <input type="text" name="decimal_separator" id="decimal_separator" value="<?php echo esc_attr($decimal_separator); ?>" class="small-text">
                            </fieldset>
                            <p class="description"><?php _e('Configure how amounts are displayed.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Donation Limits', 'kilismile-payments'); ?></th>
                        <td>
                            <fieldset>
                                <label for="min_donation_amount"><?php _e('Minimum Amount:', 'kilismile-payments'); ?></label>
                                <input type="number" name="min_donation_amount" id="min_donation_amount" 
                                       value="<?php echo esc_attr($min_amount); ?>" min="0" step="0.01" class="small-text">
                                <br><br>
                                
                                <label for="max_donation_amount"><?php _e('Maximum Amount:', 'kilismile-payments'); ?></label>
                                <input type="number" name="max_donation_amount" id="max_donation_amount" 
                                       value="<?php echo esc_attr($max_amount); ?>" min="0" step="0.01" class="small-text">
                            </fieldset>
                            <p class="description"><?php _e('Set minimum and maximum donation amounts to prevent fraud and errors.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Gateway Settings -->
            <div id="gateways" class="tab-content">
                <h2><?php _e('Payment Gateway Configuration', 'kilismile-payments'); ?></h2>
                
                <div class="gateway-order-section">
                    <h3><?php _e('Gateway Display Order', 'kilismile-payments'); ?></h3>
                    <p class="description"><?php _e('Drag and drop to reorder how payment methods appear to users.', 'kilismile-payments'); ?></p>
                    
                    <ul id="gateway-sortable" class="gateway-list">
                        <?php foreach ($gateway_order as $gateway_id): ?>
                            <?php if (isset($gateways[$gateway_id])): ?>
                            <li class="gateway-item" data-gateway="<?php echo esc_attr($gateway_id); ?>">
                                <span class="dashicons dashicons-menu"></span>
                                <span class="gateway-name"><?php echo esc_html($gateways[$gateway_id]['title']); ?></span>
                                <span class="gateway-status <?php echo $gateways[$gateway_id]['enabled'] ? 'enabled' : 'disabled'; ?>">
                                    <?php echo $gateways[$gateway_id]['enabled'] ? __('Enabled', 'kilismile-payments') : __('Disabled', 'kilismile-payments'); ?>
                                </span>
                                <input type="hidden" name="gateway_order[]" value="<?php echo esc_attr($gateway_id); ?>">
                            </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="gateway-settings-section">
                    <h3><?php _e('Gateway Settings', 'kilismile-payments'); ?></h3>
                    <p class="description"><?php _e('Configure individual payment gateway settings below.', 'kilismile-payments'); ?></p>
                    
                    <div class="gateway-settings-links">
                        <?php foreach ($gateways as $gateway_id => $gateway): ?>
                        <a href="<?php echo admin_url('admin.php?page=kilismile-payments-gateways&gateway=' . $gateway_id); ?>" 
                           class="button button-secondary">
                            <?php printf(__('Configure %s', 'kilismile-payments'), $gateway['title']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Email Settings -->
            <div id="email" class="tab-content">
                <h2><?php _e('Email Settings', 'kilismile-payments'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('From Name', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="text" name="email_from_name" value="<?php echo esc_attr($email_from_name); ?>" class="regular-text">
                            <p class="description"><?php _e('The name that appears in the "From" field of emails.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('From Email Address', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="email" name="email_from_address" value="<?php echo esc_attr($email_from_address); ?>" class="regular-text">
                            <p class="description"><?php _e('The email address that appears in the "From" field of emails.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Admin Email Address', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="email" name="admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text">
                            <p class="description"><?php _e('Email address to receive admin notifications.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Email Notifications', 'kilismile-payments'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="send_admin_notifications" value="1" <?php checked($send_admin_notifications); ?>>
                                    <?php _e('Send admin notifications for new donations', 'kilismile-payments'); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="checkbox" name="send_donor_receipts" value="1" <?php checked($send_donor_receipts); ?>>
                                    <?php _e('Send receipt emails to donors', 'kilismile-payments'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                
                <div class="email-templates-section">
                    <h3><?php _e('Email Templates', 'kilismile-payments'); ?></h3>
                    <p class="description"><?php _e('Customize email templates sent to donors and administrators.', 'kilismile-payments'); ?></p>
                    
                    <div class="email-template-links">
                        <a href="<?php echo admin_url('admin.php?page=kilismile-payments-email-templates&template=receipt'); ?>" class="button button-secondary">
                            <?php _e('Donation Receipt Template', 'kilismile-payments'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=kilismile-payments-email-templates&template=admin'); ?>" class="button button-secondary">
                            <?php _e('Admin Notification Template', 'kilismile-payments'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div id="security" class="tab-content">
                <h2><?php _e('Security Settings', 'kilismile-payments'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Rate Limiting', 'kilismile-payments'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="enable_rate_limiting" value="1" <?php checked($rate_limiting); ?>>
                                    <?php _e('Enable rate limiting for payment attempts', 'kilismile-payments'); ?>
                                </label>
                                <br><br>
                                
                                <label for="rate_limit_attempts"><?php _e('Maximum Attempts:', 'kilismile-payments'); ?></label>
                                <input type="number" name="rate_limit_attempts" id="rate_limit_attempts" 
                                       value="<?php echo esc_attr($rate_limit_attempts); ?>" min="1" max="50" class="small-text">
                                
                                <label for="rate_limit_window"><?php _e('Time Window (minutes):', 'kilismile-payments'); ?></label>
                                <input type="number" name="rate_limit_window" id="rate_limit_window" 
                                       value="<?php echo esc_attr($rate_limit_window); ?>" min="1" max="1440" class="small-text">
                            </fieldset>
                            <p class="description"><?php _e('Limit the number of payment attempts from the same IP address to prevent abuse.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Fraud Detection', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_fraud_detection" value="1" <?php checked($fraud_detection); ?>>
                                <?php _e('Enable basic fraud detection checks', 'kilismile-payments'); ?>
                            </label>
                            <p class="description"><?php _e('Performs basic checks for suspicious payment patterns and behavior.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Data Retention', 'kilismile-payments'); ?></th>
                        <td>
                            <select name="data_retention_period">
                                <?php $retention_period = get_option('kilismile_payments_data_retention', 365); ?>
                                <option value="90" <?php selected($retention_period, 90); ?>><?php _e('3 months', 'kilismile-payments'); ?></option>
                                <option value="180" <?php selected($retention_period, 180); ?>><?php _e('6 months', 'kilismile-payments'); ?></option>
                                <option value="365" <?php selected($retention_period, 365); ?>><?php _e('1 year', 'kilismile-payments'); ?></option>
                                <option value="730" <?php selected($retention_period, 730); ?>><?php _e('2 years', 'kilismile-payments'); ?></option>
                                <option value="0" <?php selected($retention_period, 0); ?>><?php _e('Forever', 'kilismile-payments'); ?></option>
                            </select>
                            <p class="description"><?php _e('How long to keep transaction data before automatic cleanup.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Advanced Settings -->
            <div id="advanced" class="tab-content">
                <h2><?php _e('Advanced Settings', 'kilismile-payments'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Debug Mode', 'kilismile-payments'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="debug_mode" value="1" <?php checked(get_option('kilismile_payments_debug_mode', 0)); ?>>
                                <?php _e('Enable debug logging', 'kilismile-payments'); ?>
                            </label>
                            <p class="description"><?php _e('Enable detailed logging for troubleshooting. Disable in production.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Webhook Security', 'kilismile-payments'); ?></th>
                        <td>
                            <input type="text" name="webhook_secret" 
                                   value="<?php echo esc_attr(get_option('kilismile_payments_webhook_secret', '')); ?>" 
                                   class="regular-text" placeholder="<?php esc_attr_e('Auto-generated if empty', 'kilismile-payments'); ?>">
                            <p class="description"><?php _e('Secret key for validating webhook authenticity. Leave empty to auto-generate.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Custom CSS', 'kilismile-payments'); ?></th>
                        <td>
                            <textarea name="custom_css" rows="10" cols="80" class="large-text code"><?php echo esc_textarea(get_option('kilismile_payments_custom_css', '')); ?></textarea>
                            <p class="description"><?php _e('Custom CSS to style payment forms. Use with caution.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Database Optimization', 'kilismile-payments'); ?></th>
                        <td>
                            <button type="button" id="optimize-database" class="button button-secondary">
                                <?php _e('Optimize Database Tables', 'kilismile-payments'); ?>
                            </button>
                            <button type="button" id="cleanup-logs" class="button button-secondary">
                                <?php _e('Cleanup Old Logs', 'kilismile-payments'); ?>
                            </button>
                            <p class="description"><?php _e('Optimize database tables and clean up old log entries for better performance.', 'kilismile-payments'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <p class="submit">
            <?php submit_button(__('Save Settings', 'kilismile-payments'), 'primary', 'submit', false); ?>
            <button type="button" id="reset-settings" class="button button-secondary">
                <?php _e('Reset to Defaults', 'kilismile-payments'); ?>
            </button>
        </p>
    </form>
</div>

<style>
.kilismile-payments-settings-form {
    max-width: 1000px;
}

.settings-tabs {
    margin-top: 20px;
}

.nav-tab-wrapper {
    border-bottom: 1px solid #c3c4c7;
    margin-bottom: 20px;
}

.tab-content {
    display: none;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.gateway-list {
    list-style: none;
    margin: 0;
    padding: 0;
    max-width: 600px;
}

.gateway-item {
    display: flex;
    align-items: center;
    padding: 10px;
    margin: 5px 0;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: move;
}

.gateway-item:hover {
    background: #f0f0f0;
}

.gateway-item .dashicons {
    margin-right: 10px;
    color: #666;
}

.gateway-name {
    flex: 1;
    font-weight: 500;
}

.gateway-status {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.gateway-status.enabled {
    background: #d4edda;
    color: #155724;
}

.gateway-status.disabled {
    background: #f8d7da;
    color: #721c24;
}

.gateway-settings-links,
.email-template-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.ui-sortable-helper {
    opacity: 0.8;
    transform: rotate(2deg);
}

.ui-sortable-placeholder {
    border: 2px dashed #0073aa;
    background: rgba(0, 115, 170, 0.1);
    height: 40px;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .gateway-settings-links,
    .email-template-links {
        flex-direction: column;
        align-items: stretch;
    }
    
    .gateway-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .gateway-name {
        margin-left: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Update tab states
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Update content visibility
        $('.tab-content').removeClass('active');
        $(target).addClass('active');
    });
    
    // Make gateway list sortable
    $('#gateway-sortable').sortable({
        placeholder: 'ui-sortable-placeholder',
        helper: 'clone',
        update: function(event, ui) {
            // Update hidden inputs order
            $(this).find('li').each(function(index) {
                $(this).find('input[name="gateway_order[]"]').val($(this).data('gateway'));
            });
        }
    });
    
    // Database optimization
    $('#optimize-database').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('<?php esc_js(_e('Optimizing...', 'kilismile-payments')); ?>');
        
        $.post(ajaxurl, {
            action: 'kilismile_optimize_database',
            nonce: '<?php echo wp_create_nonce('kilismile_admin_action'); ?>'
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js(_e('Database optimized successfully.', 'kilismile-payments')); ?>');
            } else {
                alert('<?php esc_js(_e('Error optimizing database.', 'kilismile-payments')); ?>');
            }
            $button.prop('disabled', false).text('<?php esc_js(_e('Optimize Database Tables', 'kilismile-payments')); ?>');
        });
    });
    
    // Cleanup logs
    $('#cleanup-logs').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('<?php esc_js(_e('Cleaning...', 'kilismile-payments')); ?>');
        
        $.post(ajaxurl, {
            action: 'kilismile_cleanup_logs',
            nonce: '<?php echo wp_create_nonce('kilismile_admin_action'); ?>'
        }, function(response) {
            if (response.success) {
                alert('<?php esc_js(_e('Logs cleaned up successfully.', 'kilismile-payments')); ?>');
            } else {
                alert('<?php esc_js(_e('Error cleaning up logs.', 'kilismile-payments')); ?>');
            }
            $button.prop('disabled', false).text('<?php esc_js(_e('Cleanup Old Logs', 'kilismile-payments')); ?>');
        });
    });
    
    // Reset settings
    $('#reset-settings').on('click', function() {
        if (confirm('<?php esc_js(_e('Are you sure you want to reset all settings to defaults? This action cannot be undone.', 'kilismile-payments')); ?>')) {
            $.post(ajaxurl, {
                action: 'kilismile_reset_settings',
                nonce: '<?php echo wp_create_nonce('kilismile_admin_action'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php esc_js(_e('Error resetting settings.', 'kilismile-payments')); ?>');
                }
            });
        }
    });
    
    // Load initial tab from URL hash
    if (window.location.hash) {
        var targetTab = $('a[href="' + window.location.hash + '"]');
        if (targetTab.length) {
            targetTab.click();
        }
    }
    
    // Update URL hash when tab changes
    $('.nav-tab').on('click', function() {
        var hash = $(this).attr('href');
        history.replaceState(null, null, hash);
    });
});
</script>

