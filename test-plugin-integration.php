<?php
/**
 * Test Plugin Integration
 * Tests if the payment plugin integration is working correctly
 */

// Include WordPress
$wp_load_path = dirname(__FILE__) . '/../../../../wp-load.php';
if (file_exists($wp_load_path)) {
    require_once $wp_load_path;
} else {
    die('WordPress not found');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Plugin Integration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f1b0b7; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Payment Plugin Integration Test</h1>
    
    <h2>Plugin Detection</h2>
    <?php
    $plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE');
    $bridge_available = class_exists('KiliSmile_Payment_Plugin_Bridge');
    $gateway_factory_available = class_exists('KiliSmile_Payment_Gateway_Factory');
    $payment_processor_available = class_exists('KiliSmile_Payment_Processor');
    
    echo '<div class="status ' . ($plugin_active ? 'success' : 'warning') . '">';
    echo 'Plugin Active: ' . ($plugin_active ? '✅ Yes' : '❌ No');
    echo '</div>';
    
    echo '<div class="status ' . ($bridge_available ? 'success' : 'error') . '">';
    echo 'Bridge Available: ' . ($bridge_available ? '✅ Yes' : '❌ No');
    echo '</div>';
    
    echo '<div class="status ' . ($gateway_factory_available ? 'success' : 'warning') . '">';
    echo 'Gateway Factory: ' . ($gateway_factory_available ? '✅ Available' : '❌ Not Available');
    echo '</div>';
    
    echo '<div class="status ' . ($payment_processor_available ? 'success' : 'warning') . '">';
    echo 'Payment Processor: ' . ($payment_processor_available ? '✅ Available' : '❌ Not Available');
    echo '</div>';
    ?>
    
    <h2>AJAX Hooks Registration</h2>
    <?php
    $payment_ajax = has_action('wp_ajax_kilismile_process_payment');
    $payment_ajax_nopriv = has_action('wp_ajax_nopriv_kilismile_process_payment');
    $status_ajax = has_action('wp_ajax_kilismile_check_payment_status');
    
    echo '<div class="status ' . ($payment_ajax ? 'success' : 'warning') . '">';
    echo 'Payment AJAX (logged in): ' . ($payment_ajax ? '✅ Registered' : '❌ Not Registered');
    echo '</div>';
    
    echo '<div class="status ' . ($payment_ajax_nopriv ? 'success' : 'warning') . '">';
    echo 'Payment AJAX (public): ' . ($payment_ajax_nopriv ? '✅ Registered' : '❌ Not Registered');
    echo '</div>';
    
    echo '<div class="status ' . ($status_ajax ? 'success' : 'warning') . '">';
    echo 'Status Check AJAX: ' . ($status_ajax ? '✅ Registered' : '❌ Not Registered');
    echo '</div>';
    ?>
    
    <h2>Gateway Availability</h2>
    <?php
    if (function_exists('kilismile_get_payment_gateways')) {
        $gateways = kilismile_get_payment_gateways();
        if (!empty($gateways)) {
            echo '<div class="status success">Available Gateways:</div>';
            echo '<pre>' . print_r($gateways, true) . '</pre>';
        } else {
            echo '<div class="status warning">No gateways configured</div>';
        }
    } else {
        echo '<div class="status error">Gateway function not available</div>';
    }
    ?>
    
    <h2>Test Payment Processing</h2>
    <form id="test-payment-form">
        <p>
            <label>Amount: <input type="number" step="0.01" name="amount" value="10.00" required></label>
        </p>
        <p>
            <label>Currency: 
                <select name="currency">
                    <option value="USD">USD</option>
                    <option value="TZS">TZS</option>
                </select>
            </label>
        </p>
        <p>
            <label>Gateway: 
                <select name="gateway">
                    <option value="paypal">PayPal</option>
                    <option value="azampay">AzamPay</option>
                </select>
            </label>
        </p>
        <p>
            <button type="submit">Test Payment Processing</button>
        </p>
    </form>
    
    <div id="test-result"></div>
    
    <script>
    document.getElementById('test-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'kilismile_process_payment');
        formData.append('donor_name', 'Test User');
        formData.append('donor_email', 'test@example.com');
        
        const resultDiv = document.getElementById('test-result');
        resultDiv.innerHTML = '<div class="status warning">Processing...</div>';
        
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
                data = { success: false, message: 'Invalid JSON response: ' + text };
            }
            
            const statusClass = data.success ? 'success' : 'error';
            resultDiv.innerHTML = `
                <div class="status ${statusClass}">
                    Test Result: ${data.success ? 'SUCCESS' : 'FAILED'}
                </div>
                <pre>${JSON.stringify(data, null, 2)}</pre>
            `;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="status error">
                    Error: ${error.message}
                </div>
            `;
        });
    });
    </script>
    
    <h2>PHP Info</h2>
    <pre><?php
    echo "WordPress Version: " . get_bloginfo('version') . "\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Theme Directory: " . get_template_directory() . "\n";
    echo "Plugin Active: " . ($plugin_active ? 'Yes' : 'No') . "\n";
    echo "Bridge Class: " . (class_exists('KiliSmile_Payment_Plugin_Bridge') ? 'Loaded' : 'Not Loaded') . "\n";
    
    // Check for loaded files
    $included_files = get_included_files();
    $payment_files = array_filter($included_files, function($file) {
        return strpos($file, 'payment') !== false || strpos($file, 'kilismile') !== false;
    });
    
    echo "\nPayment-related files loaded:\n";
    foreach ($payment_files as $file) {
        echo "- " . basename($file) . "\n";
    }
    ?></pre>
</body>
</html>

