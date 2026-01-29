<?php
/**
 * AJAX Bridge Diagnostic
 * Tests the AJAX bridge functionality and provides detailed debugging
 */

// Include WordPress
if (!defined('ABSPATH')) {
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

get_header(); ?>

<div style="max-width: 1000px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #2c5530; font-size: 2.5rem; margin-bottom: 20px;">
            🔍 AJAX Bridge Diagnostic
        </h1>
        <p style="color: #6c757d; font-size: 1.1rem;">
            Detailed analysis of the AJAX endpoint bridge functionality.
        </p>
    </div>

    <!-- Real-time Test Section -->
    <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
        <h2 style="color: #28a745; margin-bottom: 25px;">🧪 Live AJAX Test</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <button onclick="testAjaxBridge()" style="background: #007cba; color: white; border: none; padding: 15px 20px; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                🔧 Test Bridge Function
            </button>
            <button onclick="testDirectPlugin()" style="background: #28a745; color: white; border: none; padding: 15px 20px; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                🔌 Test Plugin Direct
            </button>
        </div>
        
        <div id="test-results" style="margin-top: 20px; min-height: 100px; background: white; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6;">
            <em style="color: #6c757d;">Click a test button to see results...</em>
        </div>
    </div>

    <!-- System Information -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px;">📊 System Information</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            
            <!-- Plugin Status -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Plugin Status</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 0; font-weight: 600;">KILISMILE_PAYMENTS_ACTIVE:</td>
                        <td style="padding: 8px 0; color: <?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? 'TRUE' : 'FALSE'; ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 0; font-weight: 600;">Plugin Function:</td>
                        <td style="padding: 8px 0; color: <?php echo function_exists('kilismile_payments_plugin_loaded') ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo function_exists('kilismile_payments_plugin_loaded') ? 'EXISTS' : 'MISSING'; ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 0; font-weight: 600;">Processor Class:</td>
                        <td style="padding: 8px 0; color: <?php echo class_exists('KiliSmile_Payment_Processor') ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo class_exists('KiliSmile_Payment_Processor') ? 'LOADED' : 'MISSING'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600;">Shortcode:</td>
                        <td style="padding: 8px 0; color: <?php echo shortcode_exists('kilismile_donation_form') ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo shortcode_exists('kilismile_donation_form') ? 'REGISTERED' : 'MISSING'; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- AJAX Endpoints -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">AJAX Endpoints</h3>
                <?php
                global $wp_filter;
                $endpoints = array(
                    'wp_ajax_kilismile_process_payment',
                    'wp_ajax_nopriv_kilismile_process_payment',
                    'wp_ajax_azampay_process_payment',
                    'wp_ajax_kilismile_donation_form'
                );
                
                foreach ($endpoints as $endpoint) {
                    $exists = isset($wp_filter[$endpoint]) && !empty($wp_filter[$endpoint]->callbacks);
                    echo '<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6;">';
                    echo '<span style="font-family: monospace; font-size: 0.9rem;">' . $endpoint . '</span>';
                    echo '<span style="color: ' . ($exists ? '#28a745' : '#dc3545') . ';">' . ($exists ? '✅' : '❌') . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Detailed Action Analysis -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-top: 20px;">
            <h3 style="margin: 0 0 15px 0; color: #495057;">Action Hook Analysis</h3>
            <?php
            $target_action = 'wp_ajax_kilismile_process_payment';
            if (isset($wp_filter[$target_action]) && !empty($wp_filter[$target_action]->callbacks)) {
                echo '<div style="margin-bottom: 15px; color: #28a745;"><strong>✅ Handlers found for ' . $target_action . ':</strong></div>';
                echo '<div style="background: white; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.9rem;">';
                
                foreach ($wp_filter[$target_action]->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $callback) {
                        echo '<div style="margin-bottom: 8px;">';
                        echo '<strong>Priority ' . $priority . ':</strong> ';
                        
                        if (is_array($callback['function'])) {
                            if (is_object($callback['function'][0])) {
                                echo get_class($callback['function'][0]) . '::' . $callback['function'][1];
                            } else {
                                echo $callback['function'][0] . '::' . $callback['function'][1];
                            }
                        } else {
                            echo $callback['function'];
                        }
                        echo '</div>';
                    }
                }
                echo '</div>';
            } else {
                echo '<div style="color: #dc3545;">❌ No handlers found for ' . $target_action . '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Available Classes and Functions -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px;">🔍 Available Classes & Functions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            
            <!-- Payment Classes -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Payment Classes</h3>
                <?php
                $payment_classes = array(
                    'KiliSmile_Payment_Processor',
                    'KiliSmile_Payment_Gateway_Base',
                    'KiliSmile_Payment_Gateway_Factory',
                    'KiliSmile_Modern_Donation_System',
                    'AzamPay_Payment_Gateway'
                );
                
                foreach ($payment_classes as $class) {
                    $exists = class_exists($class);
                    echo '<div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #dee2e6;">';
                    echo '<span style="font-family: monospace; font-size: 0.9rem;">' . $class . '</span>';
                    echo '<span style="color: ' . ($exists ? '#28a745' : '#dc3545') . ';">' . ($exists ? '✅' : '❌') . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
            
            <!-- Payment Functions -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Payment Functions</h3>
                <?php
                $payment_functions = array(
                    'kilismile_payments_plugin_loaded',
                    'kilismile_payments_process_payment',
                    'kilismile_process_payment_request',
                    'kilismile_donation_form',
                    'azampay_process_payment'
                );
                
                foreach ($payment_functions as $function) {
                    $exists = function_exists($function);
                    echo '<div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #dee2e6;">';
                    echo '<span style="font-family: monospace; font-size: 0.9rem;">' . $function . '</span>';
                    echo '<span style="color: ' . ($exists ? '#28a745' : '#dc3545') . ';">' . ($exists ? '✅' : '❌') . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

</div>

<script>
function testAjaxBridge() {
    const resultDiv = document.getElementById('test-results');
    resultDiv.innerHTML = '<div style="color: #007cba;">🔧 Testing AJAX bridge...</div>';
    
    const formData = new FormData();
    formData.append('action', 'kilismile_process_payment');
    formData.append('test', 'true');
    formData.append('amount', '1000');
    formData.append('currency', 'TZS');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Bridge Test Response:', data);
        
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.success) {
                resultDiv.innerHTML = `
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;">
                        <h4 style="margin: 0 0 10px 0;">✅ Bridge Test Successful</h4>
                        <div style="margin-bottom: 10px;"><strong>Status:</strong> ${jsonData.data.status}</div>
                        <div style="margin-bottom: 10px;"><strong>Message:</strong> ${jsonData.data.message}</div>
                        <details style="margin-top: 15px;">
                            <summary style="cursor: pointer; font-weight: 600;">📋 Diagnostic Data</summary>
                            <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; overflow-x: auto; font-size: 0.85rem;">${JSON.stringify(jsonData.data.diagnostic, null, 2)}</pre>
                        </details>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;">
                        <h4 style="margin: 0 0 10px 0;">⚠️ Bridge Test Failed</h4>
                        <div><strong>Error:</strong> ${JSON.stringify(jsonData.data)}</div>
                    </div>
                `;
            }
        } catch (e) {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <h4 style="margin: 0 0 10px 0;">❌ Parse Error</h4>
                    <div style="margin-bottom: 10px;"><strong>Error:</strong> ${e.message}</div>
                    <details>
                        <summary style="cursor: pointer; font-weight: 600;">Raw Response</summary>
                        <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; overflow-x: auto; font-size: 0.85rem;">${data}</pre>
                    </details>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Bridge Test Error:', error);
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                <h4 style="margin: 0 0 10px 0;">❌ Network Error</h4>
                <div><strong>Error:</strong> ${error.message}</div>
            </div>
        `;
    });
}

function testDirectPlugin() {
    const resultDiv = document.getElementById('test-results');
    resultDiv.innerHTML = '<div style="color: #28a745;">🔌 Testing direct plugin...</div>';
    
    const formData = new FormData();
    formData.append('action', 'azampay_process_payment');
    formData.append('amount', '1000');
    formData.append('currency', 'TZS');
    formData.append('test', 'true');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Plugin Direct Response:', data);
        
        try {
            const jsonData = JSON.parse(data);
            resultDiv.innerHTML = `
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;">
                    <h4 style="margin: 0 0 10px 0;">✅ Plugin Direct Test</h4>
                    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; overflow-x: auto; font-size: 0.85rem;">${JSON.stringify(jsonData, null, 2)}</pre>
                </div>
            `;
        } catch (e) {
            if (data.includes('0')) {
                resultDiv.innerHTML = `
                    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;">
                        <h4 style="margin: 0 0 10px 0;">⚠️ Plugin Response</h4>
                        <div>Plugin responded but no JSON output (likely no direct AJAX handler)</div>
                        <details>
                            <summary style="cursor: pointer; font-weight: 600;">Raw Response</summary>
                            <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; overflow-x: auto; font-size: 0.85rem;">${data}</pre>
                        </details>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                        <h4 style="margin: 0 0 10px 0;">❌ Plugin Error</h4>
                        <div><strong>Parse Error:</strong> ${e.message}</div>
                        <details>
                            <summary style="cursor: pointer; font-weight: 600;">Raw Response</summary>
                            <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px; overflow-x: auto; font-size: 0.85rem;">${data}</pre>
                        </details>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Plugin Direct Error:', error);
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                <h4 style="margin: 0 0 10px 0;">❌ Network Error</h4>
                <div><strong>Error:</strong> ${error.message}</div>
            </div>
        `;
    });
}
</script>

<?php get_footer(); ?>

