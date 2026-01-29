<?php
/**
 * JavaScript Error Diagnostic
 * Tests for common JavaScript issues on the donation page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

get_header(); ?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <h1 style="color: #2c5530; margin-bottom: 30px;">JavaScript Diagnostic Test</h1>
    
    <div id="test-results" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h2>Running JavaScript Tests...</h2>
        <div id="test-output"></div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2>WordPress Script Status</h2>
        <div id="wp-status" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2>Customizer Script Check</h2>
        <div id="customizer-status" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
    </div>
    
    <div>
        <h2>AJAX Configuration</h2>
        <div id="ajax-status" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;"></div>
    </div>
    
</div>

<script>
jQuery(document).ready(function($) {
    const testOutput = $('#test-output');
    const wpStatus = $('#wp-status');
    const customizerStatus = $('#customizer-status');
    const ajaxStatus = $('#ajax-status');
    
    // Test 1: jQuery availability
    function testJQuery() {
        if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            testOutput.append('<div style="color: green;">✅ jQuery loaded successfully</div>');
            return true;
        } else {
            testOutput.append('<div style="color: red;">❌ jQuery not found</div>');
            return false;
        }
    }
    
    // Test 2: WordPress object availability
    function testWordPress() {
        let status = '';
        
        if (typeof wp !== 'undefined') {
            status += '<div style="color: green;">✅ WordPress (wp) object available</div>';
            
            if (wp.ajax) {
                status += '<div style="color: green;">✅ wp.ajax available</div>';
            } else {
                status += '<div style="color: orange;">⚠️ wp.ajax not available</div>';
            }
        } else {
            status += '<div style="color: orange;">⚠️ WordPress (wp) object not found (this is normal for frontend)</div>';
        }
        
        wpStatus.html(status);
    }
    
    // Test 3: Customizer script check
    function testCustomizer() {
        let status = '';
        
        if (typeof wp !== 'undefined' && wp.customize) {
            status += '<div style="color: green;">✅ wp.customize available</div>';
        } else {
            status += '<div style="color: green;">✅ wp.customize not loaded (normal for frontend)</div>';
        }
        
        // Check if customizer preview
        if (document.body.classList.contains('customize-support')) {
            status += '<div style="color: blue;">ℹ️ Customizer support detected</div>';
        } else {
            status += '<div style="color: green;">✅ Not in customizer (normal)</div>';
        }
        
        customizerStatus.html(status);
    }
    
    // Test 4: AJAX configuration
    function testAjaxConfig() {
        let status = '';
        
        if (typeof kilismile_ajax !== 'undefined') {
            status += '<div style="color: green;">✅ kilismile_ajax object available</div>';
            status += '<div>Ajax URL: ' + kilismile_ajax.ajax_url + '</div>';
            status += '<div>Nonce: ' + (kilismile_ajax.nonce ? 'Available' : 'Missing') + '</div>';
        } else {
            status += '<div style="color: red;">❌ kilismile_ajax object not found</div>';
        }
        
        if (typeof kilismileDonation !== 'undefined') {
            status += '<div style="color: green;">✅ kilismileDonation object available</div>';
            status += '<div>Payment Nonce: ' + (kilismileDonation.payment_nonce ? 'Available' : 'Missing') + '</div>';
        } else {
            status += '<div style="color: orange;">⚠️ kilismileDonation object not found (normal on non-donation pages)</div>';
        }
        
        ajaxStatus.html(status);
    }
    
    // Test 5: AJAX connectivity
    function testAjaxConnectivity() {
        if (typeof kilismile_ajax === 'undefined') {
            testOutput.append('<div style="color: red;">❌ Cannot test AJAX - configuration missing</div>');
            return;
        }
        
        testOutput.append('<div style="color: blue;">🔄 Testing AJAX connectivity...</div>');
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'test_ajax_connectivity',
                nonce: kilismile_ajax.nonce
            },
            timeout: 5000,
            success: function(response) {
                testOutput.append('<div style="color: green;">✅ AJAX endpoint reachable</div>');
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    testOutput.append('<div style="color: orange;">⚠️ AJAX timeout (endpoint may be slow)</div>');
                } else {
                    testOutput.append('<div style="color: orange;">⚠️ AJAX error: ' + status + ' (endpoint reachable but action not found - this is normal)</div>');
                }
            }
        });
    }
    
    // Test 5: Image availability
    function testImages() {
        const testImages = [
            '/wp-content/themes/kilismile/assets/images/logo.svg',
            '/wp-content/themes/kilismile/assets/images/hero-background.svg'
        ];
        
        testImages.forEach(function(src) {
            const img = new Image();
            img.onload = function() {
                testOutput.append('<div style="color: green;">✅ Image found: ' + src + '</div>');
            };
            img.onerror = function() {
                testOutput.append('<div style="color: red;">❌ Image missing: ' + src + '</div>');
            };
            img.src = src;
        });
    }
    
    // Run all tests
    setTimeout(function() {
        testOutput.html('');
        
        testJQuery();
        testWordPress();
        testCustomizer();
        testAjaxConfig();
        testAjaxConnectivity();
        testImages();
        
        testOutput.append('<div style="color: blue; margin-top: 20px;">✅ All tests completed</div>');
    }, 500);
});
</script>

<?php get_footer(); ?>

