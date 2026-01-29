<?php
/**
 * Payment Settings Page
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if KiliSmile Payments plugin is active
if (function_exists('kilismile_payments_plugin_active') && kilismile_payments_plugin_active()) {
    echo '<div class="notice notice-info"><p>' . 
         sprintf(
             __('The KiliSmile Payments plugin is active. Please use the <a href="%s">plugin\'s settings</a> instead of this page.', 'kilismile'),
             admin_url('admin.php?page=kilismile-payments-gateways')
         ) . 
         '</p></div>';
    echo '<p>' . __('These settings will be ignored while the KiliSmile Payments plugin is active.', 'kilismile') . '</p>';
}

// Save settings
if (isset($_POST['submit'])) {
    update_option('kilismile_selcom_public_key', sanitize_text_field($_POST['kilismile_selcom_public_key']));
    update_option('kilismile_selcom_private_key', sanitize_text_field($_POST['kilismile_selcom_private_key']));
    
    // Updated AzamPay settings for official API
    update_option('kilismile_azampay_app_name', sanitize_text_field($_POST['kilismile_azampay_app_name']));
    update_option('kilismile_azampay_client_id', sanitize_text_field($_POST['kilismile_azampay_client_id']));
    update_option('kilismile_azampay_client_secret', sanitize_text_field($_POST['kilismile_azampay_client_secret']));
    update_option('kilismile_azampay_sandbox', isset($_POST['kilismile_azampay_sandbox']) ? 1 : 0);
    
    update_option('kilismile_payment_sandbox', isset($_POST['kilismile_payment_sandbox']) ? 1 : 0);
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'kilismile') . '</p></div>';
}

// Get current settings
$selcom_public_key = get_option('kilismile_selcom_public_key', '');
$selcom_private_key = get_option('kilismile_selcom_private_key', '');
$azampay_app_name = get_option('kilismile_azampay_app_name', '');
$azampay_client_id = get_option('kilismile_azampay_client_id', '');
$azampay_client_secret = get_option('kilismile_azampay_client_secret', '');
$azampay_sandbox = get_option('kilismile_azampay_sandbox', true);
$sandbox_mode = get_option('kilismile_payment_sandbox', true);
?>

<div class="wrap">
    <h1><?php _e('Payment System Settings', 'kilismile'); ?></h1>
    
    <form method="post" action="">
        
        <!-- Gateway Settings -->
        <div class="settings-section" style="background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                <i class="dashicons dashicons-admin-settings"></i>
                <?php _e('Payment Gateway Configuration', 'kilismile'); ?>
            </h2>
            
            <!-- Sandbox Mode -->
            <div class="setting-group" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #17a2b8;">
                <h3 style="margin-top: 0; color: #17a2b8;">
                    <i class="dashicons dashicons-admin-tools"></i>
                    <?php _e('Environment Settings', 'kilismile'); ?>
                </h3>
                <label style="display: flex; align-items: center; font-weight: 600;">
                    <input type="checkbox" 
                           name="kilismile_payment_sandbox" 
                           value="1" 
                           <?php checked($sandbox_mode, 1); ?>
                           style="margin-right: 10px; transform: scale(1.2);">
                    <?php _e('Enable Sandbox Mode (for testing)', 'kilismile'); ?>
                </label>
                <p style="margin: 10px 0 0; color: #666; font-style: italic;">
                    <?php _e('When enabled, all payments will be processed in test mode. Disable for live payments.', 'kilismile'); ?>
                </p>
            </div>
            
            <!-- Selcom Settings -->
            <div class="setting-group" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #28a745;">
                <h3 style="margin-top: 0; color: #28a745;">
                    <i class="dashicons dashicons-credit-card"></i>
                    <?php _e('Selcom Payment Gateway', 'kilismile'); ?>
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="kilismile_selcom_public_key" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php _e('Selcom Public Key', 'kilismile'); ?>
                        </label>
                        <input type="text" 
                               id="kilismile_selcom_public_key"
                               name="kilismile_selcom_public_key" 
                               value="<?php echo esc_attr($selcom_public_key); ?>"
                               class="regular-text"
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                            <?php _e('Your Selcom API public key', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <div>
                        <label for="kilismile_selcom_private_key" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php _e('Selcom Private Key', 'kilismile'); ?>
                        </label>
                        <input type="password" 
                               id="kilismile_selcom_private_key"
                               name="kilismile_selcom_private_key" 
                               value="<?php echo esc_attr($selcom_private_key); ?>"
                               class="regular-text"
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                            <?php _e('Your Selcom API private key (kept secure)', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
                
                <div style="margin-top: 15px; padding: 15px; background: #e8f5e8; border-radius: 4px; border-left: 3px solid #28a745;">
                    <strong><?php _e('Selcom Setup Instructions:', 'kilismile'); ?></strong>
                    <ol style="margin: 10px 0 0 20px; line-height: 1.6;">
                        <li><?php _e('Visit Selcom Developer Portal and create an account', 'kilismile'); ?></li>
                        <li><?php _e('Generate your API keys from the dashboard', 'kilismile'); ?></li>
                        <li><?php _e('Set up your webhook URL:', 'kilismile'); ?> <code><?php echo home_url('/payment/callback/selcom/'); ?></code></li>
                        <li><?php _e('Enter your keys above and test in sandbox mode first', 'kilismile'); ?></li>
                    </ol>
                </div>
            </div>
            
            <!-- AzamPay Settings -->
            <div class="setting-group" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #007cba;">
                <h3 style="margin-top: 0; color: #007cba;">
                    <i class="dashicons dashicons-smartphone"></i>
                    <?php _e('AzamPay Gateway (Official API)', 'kilismile'); ?>
                </h3>
                
                <!-- AzamPay Sandbox Mode -->
                <div style="margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 4px;">
                    <label style="display: flex; align-items: center; font-weight: 600;">
                        <input type="checkbox" 
                               name="kilismile_azampay_sandbox" 
                               value="1" 
                               <?php checked($azampay_sandbox, 1); ?>
                               style="margin-right: 10px; transform: scale(1.2);">
                        <?php _e('Enable AzamPay Sandbox Mode', 'kilismile'); ?>
                    </label>
                    <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                        <?php _e('Use sandbox.azampay.co.tz for testing. Disable for production.', 'kilismile'); ?>
                    </p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div>
                        <label for="kilismile_azampay_app_name" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php _e('Application Name', 'kilismile'); ?>
                        </label>
                        <input type="text" 
                               id="kilismile_azampay_app_name"
                               name="kilismile_azampay_app_name" 
                               value="<?php echo esc_attr($azampay_app_name); ?>"
                               class="regular-text"
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                            <?php _e('Your registered application name with AzamPay', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <div>
                        <label for="kilismile_azampay_client_id" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php _e('Client ID', 'kilismile'); ?>
                        </label>
                        <input type="text" 
                               id="kilismile_azampay_client_id"
                               name="kilismile_azampay_client_id" 
                               value="<?php echo esc_attr($azampay_client_id); ?>"
                               class="regular-text"
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                            <?php _e('Your AzamPay client identifier', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <div>
                        <label for="kilismile_azampay_client_secret" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php _e('Client Secret', 'kilismile'); ?>
                        </label>
                        <input type="password" 
                               id="kilismile_azampay_client_secret"
                               name="kilismile_azampay_client_secret" 
                               value="<?php echo esc_attr($azampay_client_secret); ?>"
                               class="regular-text"
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin: 5px 0 0; color: #666; font-size: 0.9em;">
                            <?php _e('Your AzamPay client secret (kept secure)', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
                
                <div style="margin-top: 15px; padding: 15px; background: #e3f2fd; border-radius: 4px; border-left: 3px solid #007cba;">
                    <strong><?php _e('AzamPay Official API Setup:', 'kilismile'); ?></strong>
                    <ol style="margin: 10px 0 0 20px; line-height: 1.6;">
                        <li><?php _e('Register your application at AzamPay Developer Portal', 'kilismile'); ?></li>
                        <li><?php _e('Get your appName, clientId, and clientSecret from AzamPay', 'kilismile'); ?></li>
                        <li><?php _e('Configure callback URL (if required):', 'kilismile'); ?> <code><?php echo home_url('/wp-json/kilismile/v1/azampay-callback'); ?></code></li>
                        <li><?php _e('Test with sandbox mode enabled first', 'kilismile'); ?></li>
                        <li><?php _e('Supports: Airtel, Tigo, Halopesa, Azampesa, Mpesa', 'kilismile'); ?></li>
                    </ol>
                    <div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 3px; border: 1px solid #007cba;">
                        <strong><?php _e('API Endpoints:', 'kilismile'); ?></strong><br>
                        <small>
                            <strong><?php _e('Sandbox:', 'kilismile'); ?></strong> sandbox.azampay.co.tz | authenticator-sandbox.azampay.co.tz<br>
                            <strong><?php _e('Production:', 'kilismile'); ?></strong> api.azampay.co.tz | authenticator.azampay.co.tz
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Offline Payment Settings -->
        <div class="settings-section" style="background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; border-bottom: 2px solid #ffc107; padding-bottom: 10px;">
                <i class="dashicons dashicons-money-alt"></i>
                <?php _e('Offline Payment Instructions', 'kilismile'); ?>
            </h2>
            
            <div style="padding: 20px; background: #fff3cd; border-radius: 6px; border-left: 4px solid #ffc107;">
                <h3 style="margin-top: 0; color: #856404;">
                    <?php _e('Payment Methods Setup', 'kilismile'); ?>
                </h3>
                <p style="line-height: 1.6; margin-bottom: 15px;">
                    <?php _e('Update your offline payment instructions by editing the payment methods in the database. Common instructions include:', 'kilismile'); ?>
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4 style="color: #856404; margin-bottom: 10px;">M-Pesa Instructions</h4>
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.5;">
                            <li><?php _e('Paybill number', 'kilismile'); ?></li>
                            <li><?php _e('Account number format', 'kilismile'); ?></li>
                            <li><?php _e('Confirmation phone number', 'kilismile'); ?></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 style="color: #856404; margin-bottom: 10px;">Bank Transfer Details</h4>
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.5;">
                            <li><?php _e('Bank name and branch', 'kilismile'); ?></li>
                            <li><?php _e('Account number', 'kilismile'); ?></li>
                            <li><?php _e('Account name', 'kilismile'); ?></li>
                            <li><?php _e('SWIFT code (if needed)', 'kilismile'); ?></li>
                        </ul>
                    </div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 4px; border: 1px solid #ffc107;">
                    <strong><?php _e('Note:', 'kilismile'); ?></strong>
                    <?php _e('To update payment instructions, go to your WordPress database and edit the records in the kilismile_payment_methods table, or contact your developer.', 'kilismile'); ?>
                </div>
            </div>
        </div>
        
        <!-- Security & Testing -->
        <div class="settings-section" style="background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 10px;">
                <i class="dashicons dashicons-shield-alt"></i>
                <?php _e('Security & Testing', 'kilismile'); ?>
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="padding: 20px; background: #f8d7da; border-radius: 6px; border-left: 4px solid #dc3545;">
                    <h3 style="margin-top: 0; color: #721c24;">
                        <?php _e('Security Best Practices', 'kilismile'); ?>
                    </h3>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.6; color: #721c24;">
                        <li><?php _e('Always test in sandbox mode first', 'kilismile'); ?></li>
                        <li><?php _e('Use SSL certificates (HTTPS)', 'kilismile'); ?></li>
                        <li><?php _e('Keep API keys secure and private', 'kilismile'); ?></li>
                        <li><?php _e('Regularly update the payment system', 'kilismile'); ?></li>
                        <li><?php _e('Monitor transaction logs regularly', 'kilismile'); ?></li>
                    </ul>
                </div>
                
                <div style="padding: 20px; background: #d4edda; border-radius: 6px; border-left: 4px solid #28a745;">
                    <h3 style="margin-top: 0; color: #155724;">
                        <?php _e('Testing Checklist', 'kilismile'); ?>
                    </h3>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.6; color: #155724;">
                        <li><?php _e('Test small amounts first', 'kilismile'); ?></li>
                        <li><?php _e('Verify webhook callbacks work', 'kilismile'); ?></li>
                        <li><?php _e('Test failed payment scenarios', 'kilismile'); ?></li>
                        <li><?php _e('Check email notifications', 'kilismile'); ?></li>
                        <li><?php _e('Verify admin dashboard updates', 'kilismile'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <div style="text-align: center; margin: 30px 0;">
            <button type="submit" 
                    name="submit" 
                    class="button button-primary button-hero"
                    style="padding: 15px 40px; font-size: 16px; background: #4CAF50; border-color: #4CAF50;">
                <i class="dashicons dashicons-yes"></i>
                <?php _e('Save Payment Settings', 'kilismile'); ?>
            </button>
        </div>
        
    </form>
    
    <!-- Test Payment Section -->
    <div class="settings-section" style="background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;">
            <i class="dashicons dashicons-admin-tools"></i>
            <?php _e('Test Payment System', 'kilismile'); ?>
        </h2>
        
        <div style="text-align: center; padding: 20px;">
            <p style="font-size: 1.1em; margin-bottom: 20px; color: #666;">
                <?php _e('Test your payment configuration with a small donation to ensure everything works correctly.', 'kilismile'); ?>
            </p>
            
            <a href="<?php echo home_url('/donate'); ?>" 
               class="button button-secondary button-large"
               target="_blank"
               style="padding: 12px 30px; font-size: 14px;">
                <i class="dashicons dashicons-external"></i>
                <?php _e('Test Donation Form', 'kilismile'); ?>
            </a>
            
            <p style="margin-top: 15px; font-size: 0.9em; color: #999;">
                <?php _e('This will open the donation form in a new tab for testing.', 'kilismile'); ?>
            </p>
        </div>
    </div>
</div>

<style>
.settings-section h3 {
    display: flex;
    align-items: center;
    gap: 8px;
}

.settings-section .dashicons {
    font-size: 18px;
}

.setting-group {
    border: 1px solid #e0e0e0;
}

.setting-group:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .settings-section > div[style*="grid"] {
        grid-template-columns: 1fr !important;
    }
}
</style>


