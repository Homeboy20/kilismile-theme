<?php
/**
 * Admin Gateways Template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('kilismile_payments_gateways'); ?>
        
        <div class="kilismile-payments-gateways">
            
            <?php foreach ($gateways as $gateway_id => $gateway): ?>
                <div class="gateway-section" id="<?php echo esc_attr($gateway_id); ?>">
                    <div class="gateway-header">
                        <h2><?php echo esc_html($gateway->get_title()); ?></h2>
                        <p><?php echo esc_html($gateway->get_description()); ?></p>
                    </div>
                    
                    <table class="form-table">
                        <tbody>
                            <!-- Enable Gateway -->
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo $gateway_id; ?>_enabled">
                                        <?php _e('Enable Gateway', 'kilismile-payments'); ?>
                                    </label>
                                </th>
                                <td>
                                    <?php
                                    $enabled = $this->db->get_gateway_setting($gateway_id, 'enabled', 0);
                                    ?>
                                    <input type="checkbox" 
                                           id="<?php echo $gateway_id; ?>_enabled"
                                           name="<?php echo $gateway_id; ?>[enabled]" 
                                           value="1" 
                                           <?php checked(1, $enabled); ?> />
                                    <label for="<?php echo $gateway_id; ?>_enabled">
                                        <?php _e('Enable this payment gateway', 'kilismile-payments'); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <!-- Gateway Title -->
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo $gateway_id; ?>_title">
                                        <?php _e('Title', 'kilismile-payments'); ?>
                                    </label>
                                </th>
                                <td>
                                    <?php
                                    $title = $this->db->get_gateway_setting($gateway_id, 'title', $gateway->get_title());
                                    ?>
                                    <input type="text" 
                                           id="<?php echo $gateway_id; ?>_title"
                                           name="<?php echo $gateway_id; ?>[title]" 
                                           value="<?php echo esc_attr($title); ?>" 
                                           class="regular-text" />
                                    <p class="description">
                                        <?php _e('This controls the title displayed to users during checkout.', 'kilismile-payments'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Gateway Description -->
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo $gateway_id; ?>_description">
                                        <?php _e('Description', 'kilismile-payments'); ?>
                                    </label>
                                </th>
                                <td>
                                    <?php
                                    $description = $this->db->get_gateway_setting($gateway_id, 'description', $gateway->get_description());
                                    ?>
                                    <textarea id="<?php echo $gateway_id; ?>_description"
                                              name="<?php echo $gateway_id; ?>[description]" 
                                              rows="3" 
                                              cols="50"><?php echo esc_textarea($description); ?></textarea>
                                    <p class="description">
                                        <?php _e('This controls the description displayed to users during checkout.', 'kilismile-payments'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <?php if ($gateway_id === 'azampay'): ?>
                                <!-- AzamPay Specific Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3><?php _e('Live Settings', 'kilismile-payments'); ?></h3>
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_app_name">
                                            <?php _e('App Name', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $app_name = $this->db->get_gateway_setting($gateway_id, 'app_name', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_app_name"
                                               name="<?php echo $gateway_id; ?>[app_name]" 
                                               value="<?php echo esc_attr($app_name); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_client_id">
                                            <?php _e('Client ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $client_id = $this->db->get_gateway_setting($gateway_id, 'client_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_client_id"
                                               name="<?php echo $gateway_id; ?>[client_id]" 
                                               value="<?php echo esc_attr($client_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_client_secret">
                                            <?php _e('Client Secret', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $client_secret = $this->db->get_gateway_setting($gateway_id, 'client_secret', '');
                                        ?>
                                        <input type="password" 
                                               id="<?php echo $gateway_id; ?>_client_secret"
                                               name="<?php echo $gateway_id; ?>[client_secret]" 
                                               value="<?php echo esc_attr($client_secret); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3><?php _e('Test Settings', 'kilismile-payments'); ?></h3>
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_app_name">
                                            <?php _e('Test App Name', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_app_name = $this->db->get_gateway_setting($gateway_id, 'test_app_name', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_test_app_name"
                                               name="<?php echo $gateway_id; ?>[test_app_name]" 
                                               value="<?php echo esc_attr($test_app_name); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_client_id">
                                            <?php _e('Test Client ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_client_id = $this->db->get_gateway_setting($gateway_id, 'test_client_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_test_client_id"
                                               name="<?php echo $gateway_id; ?>[test_client_id]" 
                                               value="<?php echo esc_attr($test_client_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_client_secret">
                                            <?php _e('Test Client Secret', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_client_secret = $this->db->get_gateway_setting($gateway_id, 'test_client_secret', '');
                                        ?>
                                        <input type="password" 
                                               id="<?php echo $gateway_id; ?>_test_client_secret"
                                               name="<?php echo $gateway_id; ?>[test_client_secret]" 
                                               value="<?php echo esc_attr($test_client_secret); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                            <?php elseif ($gateway_id === 'paypal'): ?>
                                <!-- PayPal Specific Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3><?php _e('Live Settings', 'kilismile-payments'); ?></h3>
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_client_id">
                                            <?php _e('Client ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $client_id = $this->db->get_gateway_setting($gateway_id, 'client_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_client_id"
                                               name="<?php echo $gateway_id; ?>[client_id]" 
                                               value="<?php echo esc_attr($client_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_client_secret">
                                            <?php _e('Client Secret', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $client_secret = $this->db->get_gateway_setting($gateway_id, 'client_secret', '');
                                        ?>
                                        <input type="password" 
                                               id="<?php echo $gateway_id; ?>_client_secret"
                                               name="<?php echo $gateway_id; ?>[client_secret]" 
                                               value="<?php echo esc_attr($client_secret); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_webhook_id">
                                            <?php _e('Webhook ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $webhook_id = $this->db->get_gateway_setting($gateway_id, 'webhook_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_webhook_id"
                                               name="<?php echo $gateway_id; ?>[webhook_id]" 
                                               value="<?php echo esc_attr($webhook_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3><?php _e('Test Settings', 'kilismile-payments'); ?></h3>
                                    </th>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_client_id">
                                            <?php _e('Test Client ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_client_id = $this->db->get_gateway_setting($gateway_id, 'test_client_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_test_client_id"
                                               name="<?php echo $gateway_id; ?>[test_client_id]" 
                                               value="<?php echo esc_attr($test_client_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_client_secret">
                                            <?php _e('Test Client Secret', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_client_secret = $this->db->get_gateway_setting($gateway_id, 'test_client_secret', '');
                                        ?>
                                        <input type="password" 
                                               id="<?php echo $gateway_id; ?>_test_client_secret"
                                               name="<?php echo $gateway_id; ?>[test_client_secret]" 
                                               value="<?php echo esc_attr($test_client_secret); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="<?php echo $gateway_id; ?>_test_webhook_id">
                                            <?php _e('Test Webhook ID', 'kilismile-payments'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $test_webhook_id = $this->db->get_gateway_setting($gateway_id, 'test_webhook_id', '');
                                        ?>
                                        <input type="text" 
                                               id="<?php echo $gateway_id; ?>_test_webhook_id"
                                               name="<?php echo $gateway_id; ?>[test_webhook_id]" 
                                               value="<?php echo esc_attr($test_webhook_id); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <!-- Test Connection -->
                            <tr>
                                <th scope="row"></th>
                                <td>
                                    <button type="button" class="button test-gateway" data-gateway="<?php echo esc_attr($gateway_id); ?>">
                                        <?php _e('Test Connection', 'kilismile-payments'); ?>
                                    </button>
                                    <div class="test-result" id="test-result-<?php echo esc_attr($gateway_id); ?>"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <hr>
                
            <?php endforeach; ?>
            
        </div>
        
        <?php submit_button(__('Save Settings', 'kilismile-payments')); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Test gateway connection
    $('.test-gateway').on('click', function() {
        var button = $(this);
        var gatewayId = button.data('gateway');
        var resultDiv = $('#test-result-' + gatewayId);
        
        button.prop('disabled', true).text(kilismile_payments_admin.strings.testing_connection);
        resultDiv.html('');
        
        $.post(kilismile_payments_admin.ajax_url, {
            action: 'kilismile_test_gateway',
            gateway_id: gatewayId,
            nonce: kilismile_payments_admin.nonce
        }, function(response) {
            if (response.success) {
                resultDiv.html('<span class="test-success">✓ ' + response.data + '</span>');
            } else {
                resultDiv.html('<span class="test-error">✗ ' + response.data + '</span>');
            }
            
            button.prop('disabled', false).text('<?php _e('Test Connection', 'kilismile-payments'); ?>');
        });
    });
});
</script>

<style>
.gateway-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 20px;
}

.gateway-header h2 {
    margin-top: 0;
    margin-bottom: 5px;
}

.gateway-header p {
    color: #666;
    margin-top: 0;
}

.test-result {
    margin-top: 10px;
}

.test-success {
    color: #46b450;
    font-weight: bold;
}

.test-error {
    color: #dc3232;
    font-weight: bold;
}
</style>

