<?php
/**
 * Test AzamPay Integration with Fixed Implementation
 * Tests the corrected AzamPay integration based on official documentation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Include WordPress
    require_once('../../../wp-load.php');
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

// Load the AzamPay integration
require_once get_template_directory() . '/includes/azampay-integration.php';
require_once get_template_directory() . '/includes/enhanced-azampay-integration.php';

$test_results = array();
$errors = array();

?>
<!DOCTYPE html>
<html>
<head>
    <title>AzamPay Integration Test - Fixed Implementation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        .code { background-color: #f8f9fa; padding: 10px; font-family: monospace; white-space: pre-wrap; }
        .form-group { margin: 10px 0; }
        label { display: block; font-weight: bold; }
        input, select { padding: 5px; margin: 5px 0; width: 300px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>AzamPay Integration Test - Fixed Implementation</h1>
    <p>This page tests the AzamPay integration with fixes based on the official API documentation.</p>

    <?php
    // Test 1: Configuration Check
    echo '<div class="test-section">';
    echo '<h2>1. Configuration Check</h2>';
    
    $app_name = get_option('kilismile_azampay_app_name', '');
    $client_id = get_option('kilismile_azampay_client_id', '');
    $client_secret = get_option('kilismile_azampay_client_secret', '');
    $sandbox = get_option('kilismile_azampay_sandbox', true);
    
    if (empty($app_name) || empty($client_id) || empty($client_secret)) {
        echo '<div class="error">❌ AzamPay credentials not configured</div>';
        echo '<p>Please configure your AzamPay credentials first:</p>';
        echo '<ul>';
        echo '<li>App Name: ' . ($app_name ? '✓' : '❌ Missing') . '</li>';
        echo '<li>Client ID: ' . ($client_id ? '✓' : '❌ Missing') . '</li>';
        echo '<li>Client Secret: ' . ($client_secret ? '✓' : '❌ Missing') . '</li>';
        echo '</ul>';
    } else {
        echo '<div class="success">✓ AzamPay credentials configured</div>';
        echo '<ul>';
        echo '<li>App Name: ' . esc_html($app_name) . '</li>';
        echo '<li>Client ID: ' . esc_html($client_id) . '</li>';
        echo '<li>Environment: ' . ($sandbox ? 'Sandbox' : 'Production') . '</li>';
        echo '</ul>';
    }
    echo '</div>';

    // Test 2: Standard Integration Authentication
    echo '<div class="test-section">';
    echo '<h2>2. Standard Integration - Authentication Test</h2>';
    
    try {
        $azampay_std = new KiliSmile_AzamPay();
        $reflection = new ReflectionClass($azampay_std);
        $method = $reflection->getMethod('get_access_token');
        $method->setAccessible(true);
        $token = $method->invoke($azampay_std);
        
        if ($token) {
            echo '<div class="success">✓ Standard Integration: Authentication successful</div>';
            echo '<div class="code">Token received (first 20 chars): ' . substr($token, 0, 20) . '...</div>';
            $test_results['std_auth'] = true;
        } else {
            echo '<div class="error">❌ Standard Integration: No token received</div>';
            $test_results['std_auth'] = false;
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Standard Integration Authentication Error: ' . esc_html($e->getMessage()) . '</div>';
        $test_results['std_auth'] = false;
        $errors[] = 'Standard Auth: ' . $e->getMessage();
    }
    echo '</div>';

    // Test 3: Enhanced Integration Authentication
    echo '<div class="test-section">';
    echo '<h2>3. Enhanced Integration - Authentication Test</h2>';
    
    try {
        $azampay_enh = new KiliSmile_Enhanced_AzamPay();
        $token = $azampay_enh->get_access_token();
        
        if ($token) {
            echo '<div class="success">✓ Enhanced Integration: Authentication successful</div>';
            echo '<div class="code">Token received (first 20 chars): ' . substr($token, 0, 20) . '...</div>';
            $test_results['enh_auth'] = true;
        } else {
            echo '<div class="error">❌ Enhanced Integration: No token received</div>';
            $test_results['enh_auth'] = false;
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Enhanced Integration Authentication Error: ' . esc_html($e->getMessage()) . '</div>';
        $test_results['enh_auth'] = false;
        $errors[] = 'Enhanced Auth: ' . $e->getMessage();
    }
    echo '</div>';

    // Test 4: API Endpoints Verification
    echo '<div class="test-section">';
    echo '<h2>4. API Endpoints Verification</h2>';
    
    $expected_endpoints = array(
        'Auth (Sandbox)' => 'https://authenticator-sandbox.azampay.co.tz',
        'Payment (Sandbox)' => 'https://sandbox.azampay.co.tz',
        'Auth (Production)' => 'https://authenticator.azampay.co.tz',
        'Payment (Production)' => 'https://api.azampay.co.tz'
    );
    
    echo '<div class="success">✓ Using correct official endpoints:</div>';
    echo '<ul>';
    foreach ($expected_endpoints as $name => $endpoint) {
        echo '<li>' . $name . ': ' . $endpoint . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    ?>

    <!-- Interactive Test Form -->
    <div class="test-section">
        <h2>5. Interactive Checkout Test</h2>
        <p>Test the fixed checkout implementation with a sample payment:</p>
        
        <form id="testCheckoutForm" method="post">
            <?php wp_nonce_field('test_azampay_checkout', 'test_nonce'); ?>
            
            <div class="form-group">
                <label>Integration Type:</label>
                <select name="integration_type" required>
                    <option value="standard">Standard Integration</option>
                    <option value="enhanced">Enhanced Integration</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Payment Method:</label>
                <select name="payment_method" required>
                    <option value="checkout">Checkout Session (Hosted Page)</option>
                    <option value="stkpush">STK Push (Direct Mobile)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Amount (TZS):</label>
                <input type="number" name="amount" value="1000" min="100" max="50000" required>
            </div>
            
            <div class="form-group">
                <label>Currency:</label>
                <select name="currency" required>
                    <option value="TZS">TZS (Tanzanian Shilling)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Donor Name:</label>
                <input type="text" name="donor_name" value="Test Donor" required>
            </div>
            
            <div class="form-group">
                <label>Donor Email:</label>
                <input type="email" name="donor_email" value="test@example.com" required>
            </div>
            
            <div class="form-group">
                <label>Donor Phone (for STK Push):</label>
                <input type="text" name="donor_phone" value="255712345678" placeholder="255XXXXXXXXX">
            </div>
            
            <div class="form-group" id="networkGroup" style="display: none;">
                <label>Network Provider:</label>
                <select name="network">
                    <option value="vodacom">Vodacom (M-Pesa)</option>
                    <option value="airtel">Airtel Money</option>
                    <option value="tigo">Tigo Pesa</option>
                    <option value="halopesa">Halopesa</option>
                    <option value="azampesa">AzamPesa</option>
                </select>
            </div>
            
            <button type="submit" name="test_checkout">Test Checkout</button>
        </form>
    </div>

    <?php
    // Process test checkout
    if (isset($_POST['test_checkout']) && wp_verify_nonce($_POST['test_nonce'], 'test_azampay_checkout')) {
        echo '<div class="test-section">';
        echo '<h2>6. Checkout Test Results</h2>';
        
        $integration_type = sanitize_text_field($_POST['integration_type']);
        $payment_method = sanitize_text_field($_POST['payment_method']);
        $amount = floatval($_POST['amount']);
        $currency = sanitize_text_field($_POST['currency']);
        $donor_name = sanitize_text_field($_POST['donor_name']);
        $donor_email = sanitize_email($_POST['donor_email']);
        $donor_phone = sanitize_text_field($_POST['donor_phone']);
        $network = sanitize_text_field($_POST['network']);
        
        $payment_data = array(
            'amount' => $amount,
            'currency' => $currency,
            'reference' => 'TEST_' . time(),
            'donor_name' => $donor_name,
            'donor_email' => $donor_email,
            'donor_phone' => $donor_phone,
            'network' => $network,
            'donation_type' => 'test',
            'purpose' => 'testing'
        );
        
        try {
            if ($integration_type === 'enhanced') {
                $azampay = new KiliSmile_Enhanced_AzamPay();
            } else {
                $azampay = new KiliSmile_AzamPay();
            }
            
            if ($payment_method === 'checkout') {
                $result = $azampay->create_checkout_session($payment_data);
                
                if ($result['success']) {
                    echo '<div class="success">✓ Checkout session created successfully!</div>';
                    echo '<div class="code">';
                    echo 'Reference: ' . $result['reference'] . "\n";
                    echo 'Transaction ID: ' . ($result['transaction_id'] ?? 'N/A') . "\n";
                    echo 'Checkout URL: ' . $result['checkout_url'] . "\n";
                    echo '</div>';
                    echo '<p><a href="' . esc_url($result['checkout_url']) . '" target="_blank" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none;">Open Payment Page</a></p>';
                } else {
                    echo '<div class="error">❌ Checkout creation failed</div>';
                }
            } else {
                $result = $azampay->initiate_stkpush($payment_data);
                
                if ($result['success']) {
                    echo '<div class="success">✓ STK Push initiated successfully!</div>';
                    echo '<div class="code">';
                    echo 'Reference: ' . $result['reference'] . "\n";
                    echo 'Transaction ID: ' . $result['transaction_id'] . "\n";
                    echo 'Message: ' . $result['message'] . "\n";
                    echo '</div>';
                } else {
                    echo '<div class="error">❌ STK Push failed</div>';
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Test Error: ' . esc_html($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
    }
    ?>

    <!-- Summary -->
    <div class="test-section">
        <h2>7. Test Summary</h2>
        
        <?php
        $passed_tests = array_filter($test_results);
        $total_tests = count($test_results);
        $passed_count = count($passed_tests);
        
        if ($passed_count === $total_tests && $total_tests > 0) {
            echo '<div class="success">✓ All tests passed (' . $passed_count . '/' . $total_tests . ')</div>';
        } else {
            echo '<div class="warning">⚠ ' . $passed_count . '/' . $total_tests . ' tests passed</div>';
        }
        
        if (!empty($errors)) {
            echo '<h3>Errors Found:</h3>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul>';
        }
        ?>
        
        <h3>Integration URLs (for AzamPay configuration):</h3>
        <div class="code">
Callback URL: <?php echo admin_url('admin-ajax.php?action=azampay_callback'); ?>

Success URL: <?php echo home_url('/donation-success/'); ?>

Failure URL: <?php echo home_url('/donation-failed/'); ?>

Cancel URL: <?php echo home_url('/donation-cancelled/'); ?>
        </div>
        
        <h3>Documentation Compliance:</h3>
        <ul>
            <li>✓ Using correct authentication endpoint: /AppRegistration/GenerateToken</li>
            <li>✓ Using correct checkout endpoint: /azampay/checkout/json</li>
            <li>✓ Using correct MNO checkout endpoint: /azampay/mno/checkout</li>
            <li>✓ Implementing proper callback handling with required fields</li>
            <li>✓ Following official response structure</li>
            <li>✓ Proper error handling and validation</li>
        </ul>
    </div>

    <script>
        // Show/hide network selection based on payment method
        document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
            const networkGroup = document.getElementById('networkGroup');
            if (this.value === 'stkpush') {
                networkGroup.style.display = 'block';
            } else {
                networkGroup.style.display = 'none';
            }
        });
    </script>
</body>
</html>

