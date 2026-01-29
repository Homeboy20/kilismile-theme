<?php
/**
 * Test Payment Form Submission
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

// Load the debug processor
require_once get_template_directory() . '/debug-payment-processor.php';

get_header(); ?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <h1 style="color: #2c5530; margin-bottom: 30px;">Test Payment Submission</h1>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h2>Quick Payment Test</h2>
        <p>This will test the payment submission with minimal data to identify issues.</p>
        
        <button id="test-minimal-payment" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            Test Minimal Payment (TZS)
        </button>
        
        <button id="test-debug-data" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            Test Debug Data Collection
        </button>
        
        <button id="test-azampay-status" style="background: #6f42c1; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Check AzamPay Status
        </button>
        
        <div id="test-result" style="margin-top: 20px; padding: 15px; border-radius: 5px; display: none;"></div>
    </div>
    
    <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2>Test Results</h2>
        <div id="detailed-result"></div>
    </div>
    
</div>

<script>
jQuery(document).ready(function($) {
    
    function showResult(message, type, details = null) {
        const colors = {
            success: '#d4edda',
            error: '#f8d7da',
            info: '#d1ecf1',
            warning: '#fff3cd'
        };
        
        $('#test-result')
            .css('background-color', colors[type])
            .css('border', '1px solid ' + (type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'))
            .html('<strong>' + message + '</strong>')
            .show();
            
        if (details) {
            $('#detailed-result').html('<pre>' + JSON.stringify(details, null, 2) + '</pre>');
        }
    }
    
    $('#test-minimal-payment').on('click', function() {
        const button = $(this);
        button.text('Testing...').prop('disabled', true);
        
        const testData = {
            action: 'kilismile_process_payment_debug',
            nonce: kilismile_ajax?.nonce || 'test-nonce',
            currency: 'TZS',
            amount: '1000',
            donor_name: 'Test User',
            donor_email: 'test@example.com',
            donor_phone: '255123456789',
            mobile_network: 'mpesa',
            payment_method: 'azampay'
        };
        
        $.ajax({
            url: kilismile_ajax?.ajax_url || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: testData,
            success: function(response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    showResult('✅ Payment validation passed!', 'success', result);
                } else {
                    showResult('❌ Payment validation failed: ' + result.message, 'error', result);
                }
            },
            error: function(xhr, status, error) {
                showResult('❌ AJAX Error: ' + error, 'error', {xhr: xhr.responseText, status: status});
            },
            complete: function() {
                button.text('Test Minimal Payment (TZS)').prop('disabled', false);
            }
        });
    });
    
    $('#test-debug-data').on('click', function() {
        const button = $(this);
        button.text('Testing...').prop('disabled', true);
        
        $.ajax({
            url: kilismile_ajax?.ajax_url || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'debug_payment_data',
                test_field: 'test_value',
                timestamp: Date.now()
            },
            success: function(response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                showResult('✅ Debug data captured', 'info', result);
            },
            error: function(xhr, status, error) {
                showResult('❌ Debug Error: ' + error, 'error', {xhr: xhr.responseText, status: status});
            },
            complete: function() {
                button.text('Test Debug Data Collection').prop('disabled', false);
            }
        });
    });
    
    $('#test-azampay-status').on('click', function() {
        const button = $(this);
        button.text('Checking...').prop('disabled', true);
        
        // Check if AzamPay is enabled and configured
        const azampayInfo = {
            ajax_available: typeof kilismile_ajax !== 'undefined',
            ajax_url: kilismile_ajax?.ajax_url || 'Not available',
            nonce_available: !!(kilismile_ajax?.nonce)
        };
        
        showResult('ℹ️ AzamPay Configuration Check', 'info', azampayInfo);
        button.text('Check AzamPay Status').prop('disabled', false);
    });
    
});
</script>

<?php get_footer(); ?>

