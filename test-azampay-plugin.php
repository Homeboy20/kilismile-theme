<?php
/**
 * Simple AzamPay Payment Plugin Test
 * Tests AzamPay integration functionality within the payment plugin
 */

// Include WordPress
$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}
require_once $wp_load_path;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AzamPay Plugin Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .test-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #005a87;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #e9ecef;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 AzamPay Payment Plugin Test</h1>
        <p>This test verifies that the AzamPay integration is working correctly through the payment plugin.</p>

        <?php
        // Test 1: Plugin and Class Availability
        echo '<div class="section">';
        echo '<h2>📋 System Checks</h2>';
        
        $plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE') || class_exists('KiliSmile_Payments_Plugin');
        echo '<div class="status ' . ($plugin_active ? 'success' : 'error') . '">';
        echo '<strong>Payment Plugin:</strong> ' . ($plugin_active ? '✅ Active' : '❌ Not Active');
        echo '</div>';

        $azampay_class = class_exists('KiliSmile_AzamPay') || class_exists('KiliSmile_Enhanced_AzamPay');
        echo '<div class="status ' . ($azampay_class ? 'success' : 'error') . '">';
        echo '<strong>AzamPay Class:</strong> ' . ($azampay_class ? '✅ Available' : '❌ Not Available');
        echo '</div>';

        $gateway_factory = class_exists('KiliSmile_Payment_Gateway_Factory');
        echo '<div class="status ' . ($gateway_factory ? 'success' : 'error') . '">';
        echo '<strong>Gateway Factory:</strong> ' . ($gateway_factory ? '✅ Available' : '❌ Not Available');
        echo '</div>';

        $ajax_hook = has_action('wp_ajax_kilismile_process_payment') || has_action('wp_ajax_nopriv_kilismile_process_payment');
        echo '<div class="status ' . ($ajax_hook ? 'success' : 'error') . '">';
        echo '<strong>AJAX Handler:</strong> ' . ($ajax_hook ? '✅ Registered' : '❌ Not Registered');
        echo '</div>';

        echo '</div>';

        // Test 2: AzamPay Configuration
        echo '<div class="section">';
        echo '<h2>⚙️ AzamPay Configuration</h2>';
        
        $sandbox_mode = get_option('kilismile_azampay_sandbox_mode', true);
        echo '<div class="status info">';
        echo '<strong>Sandbox Mode:</strong> ' . ($sandbox_mode ? 'Enabled (Test Mode)' : 'Disabled (Live Mode)');
        echo '</div>';

        $enhanced_mode = get_option('kilismile_use_enhanced_azampay', false);
        echo '<div class="status info">';
        echo '<strong>Enhanced AzamPay:</strong> ' . ($enhanced_mode ? 'Enabled' : 'Disabled');
        echo '</div>';

        // Check AzamPay credentials
        $client_id = get_option('kilismile_azampay_client_id', '');
        $client_secret = get_option('kilismile_azampay_client_secret', '');
        
        echo '<div class="status ' . (!empty($client_id) ? 'success' : 'warning') . '">';
        echo '<strong>Client ID:</strong> ' . (!empty($client_id) ? '✅ Configured' : '⚠️ Not Set');
        echo '</div>';

        echo '<div class="status ' . (!empty($client_secret) ? 'success' : 'warning') . '">';
        echo '<strong>Client Secret:</strong> ' . (!empty($client_secret) ? '✅ Configured' : '⚠️ Not Set');
        echo '</div>';

        echo '</div>';

        // Test 3: Gateway Availability Test
        echo '<div class="section">';
        echo '<h2>🔍 Gateway Tests</h2>';
        
        if (function_exists('kilismile_get_payment_gateways')) {
            try {
                $gateways = kilismile_get_payment_gateways('TZS');
                $azampay_available = false;
                
                echo '<h3>Available Gateways for TZS:</h3>';
                if (!empty($gateways)) {
                    echo '<ul>';
                    foreach ($gateways as $id => $gateway) {
                        echo '<li><strong>' . $id . ':</strong> ' . (isset($gateway['name']) ? $gateway['name'] : 'Gateway Object') . '</li>';
                        if (strpos($id, 'azam') !== false || (isset($gateway['name']) && strpos(strtolower($gateway['name']), 'azam') !== false)) {
                            $azampay_available = true;
                        }
                    }
                    echo '</ul>';
                } else {
                    echo '<div class="status warning">No gateways available for TZS currency</div>';
                }
                
                echo '<div class="status ' . ($azampay_available ? 'success' : 'warning') . '">';
                echo '<strong>AzamPay Gateway:</strong> ' . ($azampay_available ? '✅ Available for TZS' : '⚠️ Not Available for TZS');
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="status error"><strong>Gateway Test Error:</strong> ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="status error">Gateway function not available</div>';
        }
        
        echo '</div>';
        ?>

        <!-- Test 4: Interactive Payment Test -->
        <div class="section">
            <h2>💳 Interactive Payment Test</h2>
            <p>Test AzamPay payment processing with sample data:</p>
            
            <form id="azampay-test-form" class="test-form">
                <div class="form-group">
                    <label for="test_amount">Amount (TZS):</label>
                    <input type="number" id="test_amount" name="amount" value="5000" min="100" step="100" required>
                    <small>Minimum: 100 TZS</small>
                </div>
                
                <div class="form-group">
                    <label for="test_phone">Phone Number:</label>
                    <input type="tel" id="test_phone" name="phone" value="+255700123456" pattern="\+255[0-9]{9}" required>
                    <small>Format: +255XXXXXXXXX</small>
                </div>
                
                <div class="form-group">
                    <label for="test_email">Email:</label>
                    <input type="email" id="test_email" name="email" value="test@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="test_name">Donor Name:</label>
                    <input type="text" id="test_name" name="name" value="Test User" required>
                </div>
                
                <div class="form-group">
                    <label for="test_gateway">Payment Method:</label>
                    <select id="test_gateway" name="gateway" required>
                        <option value="azampay">AzamPay</option>
                        <option value="azampay_enhanced">Enhanced AzamPay</option>
                    </select>
                </div>
                
                <button type="submit" id="test-submit-btn">🚀 Test AzamPay Payment</button>
            </form>
            
            <div id="test-result" style="margin-top: 20px;"></div>
        </div>

        <!-- Test 5: Debug Information -->
        <div class="section">
            <h2>🔧 Debug Information</h2>
            <h3>Loaded Classes:</h3>
            <pre><?php
            $payment_classes = array_filter(get_declared_classes(), function($class) {
                return stripos($class, 'azam') !== false || stripos($class, 'payment') !== false || stripos($class, 'kilismile') !== false;
            });
            sort($payment_classes);
            foreach ($payment_classes as $class) {
                echo "- $class\n";
            }
            ?></pre>
            
            <h3>WordPress Hooks:</h3>
            <pre><?php
            global $wp_filter;
            $hooks = ['wp_ajax_kilismile_process_payment', 'wp_ajax_nopriv_kilismile_process_payment', 'wp_ajax_azampay_callback'];
            foreach ($hooks as $hook) {
                $registered = isset($wp_filter[$hook]) ? 'YES' : 'NO';
                echo "- $hook: $registered\n";
            }
            ?></pre>
            
            <h3>AzamPay Settings:</h3>
            <pre><?php
            $azampay_settings = [
                'kilismile_azampay_sandbox_mode' => get_option('kilismile_azampay_sandbox_mode'),
                'kilismile_use_enhanced_azampay' => get_option('kilismile_use_enhanced_azampay'),
                'kilismile_azampay_client_id' => !empty(get_option('kilismile_azampay_client_id')) ? 'SET' : 'NOT SET',
                'kilismile_azampay_client_secret' => !empty(get_option('kilismile_azampay_client_secret')) ? 'SET' : 'NOT SET'
            ];
            foreach ($azampay_settings as $key => $value) {
                echo "- $key: $value\n";
            }
            ?></pre>
        </div>
    </div>

    <script>
    document.getElementById('azampay-test-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'kilismile_process_payment');
        formData.append('currency', 'TZS');
        formData.append('payment_gateway', formData.get('gateway'));
        formData.append('donor_name', formData.get('name'));
        formData.append('donor_email', formData.get('email'));
        formData.append('donor_phone', formData.get('phone'));
        formData.append('recurring', false);
        formData.append('anonymous', false);
        formData.append('nonce', '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>');
        
        const submitBtn = document.getElementById('test-submit-btn');
        const resultDiv = document.getElementById('test-result');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '🔄 Processing...';
        
        resultDiv.innerHTML = '<div class="status info">Sending payment request to AzamPay...</div>';
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log('Raw response:', text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                data = { 
                    success: false, 
                    message: 'Invalid server response', 
                    raw_response: text.substring(0, 500) + (text.length > 500 ? '...' : '')
                };
            }
            
            const statusClass = data.success ? 'success' : 'error';
            let resultHtml = `<div class="status ${statusClass}">
                <strong>Test Result:</strong> ${data.success ? '✅ SUCCESS' : '❌ FAILED'}
            </div>`;
            
            if (data.message) {
                resultHtml += `<div class="status info"><strong>Message:</strong> ${data.message}</div>`;
            }
            
            if (data.redirect_url) {
                resultHtml += `<div class="status success">
                    <strong>Payment URL:</strong> <a href="${data.redirect_url}" target="_blank">${data.redirect_url}</a>
                </div>`;
            }
            
            if (data.transaction_id) {
                resultHtml += `<div class="status info"><strong>Transaction ID:</strong> ${data.transaction_id}</div>`;
            }
            
            // Show full response for debugging
            resultHtml += `<h3>Full Response:</h3><pre>${JSON.stringify(data, null, 2)}</pre>`;
            
            resultDiv.innerHTML = resultHtml;
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = `<div class="status error">
                <strong>Network Error:</strong> ${error.message}
            </div>`;
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '🚀 Test AzamPay Payment';
        });
    });
    </script>
</body>
</html>

