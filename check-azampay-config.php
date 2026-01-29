<?php
/**
 * Check AzamPay Configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

get_header(); ?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <h1 style="color: #2c5530; margin-bottom: 30px;">AzamPay Configuration Status</h1>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h2>Current Configuration</h2>
        <?php
        $azampay_enabled = get_option('kilismile_azampay_enabled', false);
        $client_id = get_option('kilismile_azampay_client_id', '');
        $client_secret = get_option('kilismile_azampay_client_secret', '');
        $app_name = get_option('kilismile_azampay_app_name', '');
        $sandbox = get_option('kilismile_azampay_sandbox', true);
        
        echo '<p><strong>AzamPay Enabled:</strong> ' . ($azampay_enabled ? '<span style="color: green;">✅ Yes</span>' : '<span style="color: red;">❌ No</span>') . '</p>';
        echo '<p><strong>Client ID:</strong> ' . ($client_id ? '<span style="color: green;">✅ Set (' . substr($client_id, 0, 8) . '...)</span>' : '<span style="color: red;">❌ Not set</span>') . '</p>';
        echo '<p><strong>Client Secret:</strong> ' . ($client_secret ? '<span style="color: green;">✅ Set (' . substr($client_secret, 0, 8) . '...)</span>' : '<span style="color: red;">❌ Not set</span>') . '</p>';
        echo '<p><strong>App Name:</strong> ' . ($app_name ? '<span style="color: green;">✅ Set (' . $app_name . ')</span>' : '<span style="color: orange;">⚠️ Not set</span>') . '</p>';
        echo '<p><strong>Sandbox Mode:</strong> ' . ($sandbox ? '<span style="color: blue;">🧪 Enabled</span>' : '<span style="color: green;">🔴 Production</span>') . '</p>';
        ?>
    </div>
    
    <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px;">
        <h2>Quick Actions</h2>
        
        <?php if (!$azampay_enabled): ?>
        <button onclick="enableAzamPay()" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            Enable AzamPay
        </button>
        <?php else: ?>
        <button onclick="disableAzamPay()" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            Disable AzamPay
        </button>
        <?php endif; ?>
        
        <button onclick="testConnection()" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Test Connection
        </button>
        
        <div id="action-result" style="margin-top: 15px; padding: 10px; border-radius: 5px; display: none;"></div>
    </div>
    
    <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2>Payment Methods Status</h2>
        <div id="payment-methods-status">Loading...</div>
    </div>
    
</div>

<script>
jQuery(document).ready(function($) {
    
    // Check payment methods
    function checkPaymentMethods() {
        if (typeof kilismile_ajax === 'undefined') {
            $('#payment-methods-status').html('<span style="color: red;">❌ AJAX not configured</span>');
            return;
        }
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_payment_methods',
                nonce: kilismile_ajax.nonce
            },
            success: function(response) {
                let html = '';
                if (response.success && response.data) {
                    Object.keys(response.data).forEach(gateway => {
                        const method = response.data[gateway];
                        const status = method.enabled ? '<span style="color: green;">✅ Enabled</span>' : '<span style="color: red;">❌ Disabled</span>';
                        html += '<p><strong>' + gateway + ':</strong> ' + status + '</p>';
                    });
                } else {
                    html = '<span style="color: red;">❌ Failed to load payment methods</span>';
                }
                $('#payment-methods-status').html(html);
            },
            error: function() {
                $('#payment-methods-status').html('<span style="color: red;">❌ Error loading payment methods</span>');
            }
        });
    }
    
    checkPaymentMethods();
    
    window.enableAzamPay = function() {
        if (typeof kilismile_ajax === 'undefined') {
            alert('AJAX not configured');
            return;
        }
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'update_payment_setting',
                setting: 'kilismile_azampay_enabled',
                value: '1',
                nonce: kilismile_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showResult('AzamPay enabled successfully', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showResult('Failed to enable AzamPay', 'error');
                }
            },
            error: function() {
                showResult('Error enabling AzamPay', 'error');
            }
        });
    }
    
    window.disableAzamPay = function() {
        if (typeof kilismile_ajax === 'undefined') {
            alert('AJAX not configured');
            return;
        }
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'update_payment_setting',
                setting: 'kilismile_azampay_enabled',
                value: '0',
                nonce: kilismile_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showResult('AzamPay disabled successfully', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showResult('Failed to disable AzamPay', 'error');
                }
            },
            error: function() {
                showResult('Error disabling AzamPay', 'error');
            }
        });
    }
    
    window.testConnection = function() {
        if (typeof kilismile_ajax === 'undefined') {
            alert('AJAX not configured');
            return;
        }
        
        showResult('Testing connection...', 'info');
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'test_azampay_connection',
                nonce: kilismile_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showResult('✅ Connection successful: ' + response.data, 'success');
                } else {
                    showResult('❌ Connection failed: ' + response.data, 'error');
                }
            },
            error: function() {
                showResult('❌ Error testing connection', 'error');
            }
        });
    }
    
    function showResult(message, type) {
        const colors = {
            success: '#d4edda',
            error: '#f8d7da',
            info: '#d1ecf1'
        };
        
        $('#action-result')
            .css('background-color', colors[type])
            .html(message)
            .show()
            .delay(3000)
            .fadeOut();
    }
});
</script>

<?php get_footer(); ?>

