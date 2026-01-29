<?php
/**
 * AzamPay Class Direct Test
 * Tests the AzamPay integration classes directly
 */

$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}
require_once $wp_load_path;

?>
<!DOCTYPE html>
<html>
<head>
    <title>AzamPay Class Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 AzamPay Class Direct Test</h1>
        
        <?php
        // Test 1: Class availability
        echo '<div class="test-section">';
        echo '<h2>Class Availability Test</h2>';
        
        $azampay_available = class_exists('KiliSmile_AzamPay');
        $enhanced_available = class_exists('KiliSmile_Enhanced_AzamPay');
        
        echo '<div class="' . ($azampay_available ? 'success' : 'error') . '">';
        echo 'KiliSmile_AzamPay: ' . ($azampay_available ? '✅ Available' : '❌ Not Available');
        echo '</div><br>';
        
        echo '<div class="' . ($enhanced_available ? 'success' : 'error') . '">';
        echo 'KiliSmile_Enhanced_AzamPay: ' . ($enhanced_available ? '✅ Available' : '❌ Not Available');
        echo '</div>';
        
        echo '</div>';
        
        // Test 2: Instance creation
        if ($azampay_available || $enhanced_available) {
            echo '<div class="test-section">';
            echo '<h2>Instance Creation Test</h2>';
            
            try {
                if ($enhanced_available) {
                    $azampay = new KiliSmile_Enhanced_AzamPay();
                    echo '<div class="success">✅ Enhanced AzamPay instance created successfully</div>';
                    $class_name = 'KiliSmile_Enhanced_AzamPay';
                } else {
                    $azampay = new KiliSmile_AzamPay();
                    echo '<div class="success">✅ Standard AzamPay instance created successfully</div>';
                    $class_name = 'KiliSmile_AzamPay';
                }
                
                // Test methods
                echo '<h3>Available Methods:</h3>';
                $methods = get_class_methods($azampay);
                echo '<pre>';
                foreach ($methods as $method) {
                    if (!in_array($method, ['__construct', '__destruct'])) {
                        echo "- $method\n";
                    }
                }
                echo '</pre>';
                
                // Test configuration
                echo '<h3>Configuration Test:</h3>';
                if (method_exists($azampay, 'is_available')) {
                    $available = $azampay->is_available();
                    echo '<div class="' . ($available ? 'success' : 'warning') . '">';
                    echo 'Gateway Available: ' . ($available ? 'Yes' : 'No');
                    echo '</div><br>';
                }
                
                if (method_exists($azampay, 'get_name')) {
                    echo '<div class="info">Gateway Name: ' . $azampay->get_name() . '</div><br>';
                }
                
                if (method_exists($azampay, 'get_description')) {
                    echo '<div class="info">Description: ' . $azampay->get_description() . '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="error">❌ Error creating AzamPay instance: ' . $e->getMessage() . '</div>';
            }
            
            echo '</div>';
        }
        
        // Test 3: Payment processing test
        if (isset($azampay)) {
            echo '<div class="test-section">';
            echo '<h2>Payment Processing Test</h2>';
            
            echo '<button onclick="testPayment()">Test Payment Request</button>';
            echo '<div id="payment-result" style="margin-top: 15px;"></div>';
            
            echo '</div>';
        }
        
        // Test 4: Settings and configuration
        echo '<div class="test-section">';
        echo '<h2>AzamPay Settings</h2>';
        
        $settings = [
            'Sandbox Mode' => get_option('kilismile_azampay_sandbox_mode', 'Not Set'),
            'Enhanced Mode' => get_option('kilismile_use_enhanced_azampay', 'Not Set'),
            'Client ID' => !empty(get_option('kilismile_azampay_client_id')) ? 'Configured' : 'Not Set',
            'Client Secret' => !empty(get_option('kilismile_azampay_client_secret')) ? 'Configured' : 'Not Set',
            'App Name' => get_option('kilismile_azampay_app_name', 'Not Set'),
            'Vendor ID' => get_option('kilismile_azampay_vendor_id', 'Not Set')
        ];
        
        echo '<table style="width: 100%; border-collapse: collapse;">';
        foreach ($settings as $key => $value) {
            $status_class = ($value === 'Not Set') ? 'warning' : 'info';
            echo '<tr>';
            echo '<td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">' . $key . '</td>';
            echo '<td style="padding: 8px; border: 1px solid #ddd;" class="' . $status_class . '">' . $value . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        echo '</div>';
        
        // Test 5: Debug information
        echo '<div class="test-section">';
        echo '<h2>Debug Information</h2>';
        
        echo '<h3>WordPress Environment:</h3>';
        echo '<pre>';
        echo 'WordPress Version: ' . get_bloginfo('version') . "\n";
        echo 'PHP Version: ' . PHP_VERSION . "\n";
        echo 'Plugin Active: ' . (defined('KILISMILE_PAYMENTS_ACTIVE') ? 'Yes' : 'No') . "\n";
        echo 'Theme: ' . get_template() . "\n";
        echo '</pre>';
        
        if (isset($azampay)) {
            echo '<h3>AzamPay Instance Properties:</h3>';
            echo '<pre>';
            $reflection = new ReflectionClass($azampay);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                if ($property->isPublic()) {
                    echo $property->getName() . ': ' . print_r($property->getValue($azampay), true) . "\n";
                }
            }
            echo '</pre>';
        }
        
        echo '</div>';
        ?>
    </div>
    
    <script>
    function testPayment() {
        const resultDiv = document.getElementById('payment-result');
        resultDiv.innerHTML = '<div class="info">Testing payment processing...</div>';
        
        const testData = {
            action: 'kilismile_process_payment',
            amount: 5000,
            currency: 'TZS',
            payment_gateway: 'azampay',
            donor_name: 'Test User',
            donor_email: 'test@example.com',
            donor_phone: '+255700123456',
            recurring: false,
            anonymous: false,
            nonce: '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>'
        };
        
        const formData = new FormData();
        for (const key in testData) {
            formData.append(key, testData[key]);
        }
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log('Response:', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = { success: false, message: 'Invalid JSON response', raw: text };
            }
            
            let html = '<h4>Payment Test Result:</h4>';
            html += '<div class="' + (data.success ? 'success' : 'error') + '">';
            html += 'Status: ' + (data.success ? 'SUCCESS' : 'FAILED');
            html += '</div>';
            
            if (data.message) {
                html += '<div class="info">Message: ' + data.message + '</div>';
            }
            
            html += '<h4>Full Response:</h4>';
            html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            
            resultDiv.innerHTML = html;
        })
        .catch(error => {
            resultDiv.innerHTML = '<div class="error">Error: ' + error.message + '</div>';
        });
    }
    </script>
</body>
</html>

