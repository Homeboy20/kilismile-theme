<?php
/**
 * Payment AJAX Diagnostic Test
 * Tests the donation form AJAX endpoints
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

get_header(); ?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <h1 style="color: #2c5530; margin-bottom: 30px;">Payment AJAX Diagnostic</h1>
    
    <div id="test-results" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h2>AJAX Actions Status</h2>
        <div id="ajax-actions"></div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2>Payment Gateway Configuration</h2>
        <div id="gateway-config" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2>Test Payment Processing</h2>
        <div id="payment-test" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
        <button id="test-payment-btn" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px;">
            Test Payment AJAX
        </button>
    </div>
    
    <div>
        <h2>Script Loading Status</h2>
        <div id="script-status" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const ajaxActions = $('#ajax-actions');
    const gatewayConfig = $('#gateway-config');
    const paymentTest = $('#payment-test');
    const scriptStatus = $('#script-status');
    
    // Test 1: Check AJAX action registration
    function testAjaxActions() {
        let status = '<h3>AJAX Action Registration</h3>';
        
        // Check for required localized variables
        if (typeof kilismile_ajax !== 'undefined') {
            status += '<div style="color: green;">✅ kilismile_ajax available</div>';
            status += '<div>Ajax URL: ' + kilismile_ajax.ajax_url + '</div>';
            status += '<div>Nonce: ' + (kilismile_ajax.nonce ? 'Available' : 'Missing') + '</div>';
        } else {
            status += '<div style="color: red;">❌ kilismile_ajax not found</div>';
        }
        
        if (typeof kilismileDonation !== 'undefined') {
            status += '<div style="color: green;">✅ kilismileDonation available</div>';
            status += '<div>Payment Nonce: ' + (kilismileDonation.payment_nonce ? 'Available' : 'Missing') + '</div>';
        } else {
            status += '<div style="color: orange;">⚠️ kilismileDonation not found</div>';
        }
        
        ajaxActions.html(status);
    }
    
    // Test 2: Check gateway configuration
    function testGatewayConfig() {
        let status = '<h3>Checking Gateway Configuration</h3>';
        
        if (typeof kilismile_ajax === 'undefined') {
            status += '<div style="color: red;">❌ Cannot test - AJAX not configured</div>';
            gatewayConfig.html(status);
            return;
        }
        
        status += '<div style="color: blue;">🔄 Checking payment gateways...</div>';
        gatewayConfig.html(status);
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_payment_methods',
                nonce: kilismile_ajax.nonce
            },
            success: function(response) {
                let result = '<h3>Payment Gateway Status</h3>';
                if (response.success && response.data) {
                    result += '<div style="color: green;">✅ Payment methods loaded successfully</div>';
                    result += '<div>Available gateways: ' + Object.keys(response.data).length + '</div>';
                    
                    Object.keys(response.data).forEach(gateway => {
                        result += '<div>• ' + gateway + ': ' + (response.data[gateway].enabled ? 'Enabled' : 'Disabled') + '</div>';
                    });
                } else {
                    result += '<div style="color: orange;">⚠️ No payment methods returned</div>';
                    result += '<div>Response: ' + JSON.stringify(response) + '</div>';
                }
                gatewayConfig.html(result);
            },
            error: function(xhr, status, error) {
                gatewayConfig.html('<h3>Payment Gateway Status</h3><div style="color: red;">❌ Failed to load payment methods</div><div>Error: ' + error + '</div>');
            }
        });
    }
    
    // Test 3: Test payment processing endpoint
    function testPaymentProcessing() {
        let status = '<h3>Testing Payment Processing</h3>';
        
        if (typeof kilismile_ajax === 'undefined') {
            status += '<div style="color: red;">❌ Cannot test - AJAX not configured</div>';
            paymentTest.html(status);
            return;
        }
        
        status += '<div style="color: blue;">🔄 Testing payment endpoint...</div>';
        paymentTest.html(status);
        
        // Test with minimal data to check if endpoint exists
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'process_donation',
                nonce: kilismile_ajax.nonce,
                test: true
            },
            success: function(response) {
                let result = '<h3>Payment Processing Test</h3>';
                result += '<div style="color: green;">✅ Payment endpoint reachable</div>';
                result += '<div>Response type: ' + typeof response + '</div>';
                result += '<div>Response: ' + JSON.stringify(response) + '</div>';
                paymentTest.html(result);
            },
            error: function(xhr, status, error) {
                let result = '<h3>Payment Processing Test</h3>';
                if (xhr.status === 400) {
                    result += '<div style="color: orange;">⚠️ Payment endpoint exists but returned error (normal for test data)</div>';
                } else {
                    result += '<div style="color: red;">❌ Payment endpoint not reachable</div>';
                }
                result += '<div>Status: ' + status + ', Error: ' + error + '</div>';
                result += '<div>HTTP Status: ' + xhr.status + '</div>';
                paymentTest.html(result);
            }
        });
    }
    
    // Test 4: Check script loading
    function testScriptLoading() {
        let status = '<h3>Script Loading Status</h3>';
        
        // Check if payment-related scripts are loaded
        const scripts = document.querySelectorAll('script[src*="donation"], script[src*="payment"]');
        status += '<div>Payment/Donation scripts loaded: ' + scripts.length + '</div>';
        
        scripts.forEach((script, index) => {
            const src = script.src.replace(window.location.origin, '');
            status += '<div>• Script ' + (index + 1) + ': ' + src + '</div>';
        });
        
        // Check for key JavaScript variables
        const vars = ['kilismile_ajax', 'kilismileDonation', 'wp'];
        vars.forEach(varName => {
            if (typeof window[varName] !== 'undefined') {
                status += '<div style="color: green;">✅ ' + varName + ' loaded</div>';
            } else {
                status += '<div style="color: orange;">⚠️ ' + varName + ' not loaded</div>';
            }
        });
        
        scriptStatus.html(status);
    }
    
    // Manual test button
    $('#test-payment-btn').on('click', function() {
        $(this).text('Testing...').prop('disabled', true);
        
        if (typeof kilismile_ajax === 'undefined') {
            alert('AJAX configuration not available');
            $(this).text('Test Payment AJAX').prop('disabled', false);
            return;
        }
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'kilismile_process_payment',
                amount: 1000,
                currency: 'TZS',
                payment_method: 'azampay_mpesa',
                phone: '255123456789',
                donor_name: 'Test User',
                donor_email: 'test@example.com',
                nonce: kilismile_ajax.nonce || kilismileDonation?.payment_nonce
            },
            success: function(response) {
                alert('Payment endpoint responded: ' + JSON.stringify(response));
            },
            error: function(xhr, status, error) {
                alert('Payment test error: ' + error + ' (Status: ' + xhr.status + ')');
            },
            complete: function() {
                $('#test-payment-btn').text('Test Payment AJAX').prop('disabled', false);
            }
        });
    });
    
    // Run all tests
    setTimeout(function() {
        testAjaxActions();
        testGatewayConfig();
        testPaymentProcessing();
        testScriptLoading();
    }, 500);
});
</script>

<?php get_footer(); ?>

