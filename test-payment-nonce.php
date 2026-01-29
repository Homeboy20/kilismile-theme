<?php
/**
 * Nonce Security Test
 * Tests if the payment nonce is working correctly
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
    <title>Payment Nonce Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Payment Nonce Security Test</h1>
        
        <h2>Nonce Information</h2>
        <div class="info">
            <strong>Current Nonce:</strong> <code><?php echo wp_create_nonce('kilismile_payment_nonce'); ?></code>
        </div>
        
        <div class="info">
            <strong>Nonce Action:</strong> <code>kilismile_payment_nonce</code>
        </div>
        
        <h2>Test Nonce Validation</h2>
        <p>This test will verify that the payment security nonce is working correctly.</p>
        
        <button onclick="testValidNonce()">✅ Test Valid Nonce</button>
        <button onclick="testInvalidNonce()">❌ Test Invalid Nonce</button>
        <button onclick="testMissingNonce()">⚠️ Test Missing Nonce</button>
        
        <div id="test-results" style="margin-top: 20px;"></div>
        
        <h2>Expected Behavior</h2>
        <div class="info">
            <p><strong>Valid Nonce:</strong> Should proceed to payment validation (may fail due to missing data, but nonce should pass)</p>
            <p><strong>Invalid Nonce:</strong> Should return "Security verification failed"</p>
            <p><strong>Missing Nonce:</strong> Should return "Security verification failed"</p>
        </div>
    </div>
    
    <script>
    const ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
    const validNonce = '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>';
    
    function testValidNonce() {
        testNonce(validNonce, 'Valid Nonce Test');
    }
    
    function testInvalidNonce() {
        testNonce('invalid_nonce_12345', 'Invalid Nonce Test');
    }
    
    function testMissingNonce() {
        testNonce(null, 'Missing Nonce Test');
    }
    
    function testNonce(nonce, testName) {
        const resultDiv = document.getElementById('test-results');
        resultDiv.innerHTML = `<div class="info">Running ${testName}...</div>`;
        
        const formData = new FormData();
        formData.append('action', 'kilismile_process_payment');
        formData.append('amount', '1000');
        formData.append('currency', 'TZS');
        formData.append('payment_gateway', 'azampay');
        formData.append('donor_name', 'Test User');
        formData.append('donor_email', 'test@example.com');
        formData.append('donor_phone', '+255700123456');
        
        if (nonce !== null) {
            formData.append('nonce', nonce);
        }
        
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log(`${testName} Response:`, text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = { success: false, message: 'Invalid JSON response', raw: text };
            }
            
            let resultClass = 'info';
            let resultText = '';
            
            if (testName.includes('Valid')) {
                // For valid nonce, we expect either success or a different error (not security)
                if (data.success || (data.data && data.data.message && !data.data.message.includes('Security verification failed'))) {
                    resultClass = 'success';
                    resultText = '✅ Valid nonce accepted (security passed)';
                } else if (data.data && data.data.message === 'Security verification failed') {
                    resultClass = 'error';
                    resultText = '❌ Valid nonce rejected (security failed)';
                } else {
                    resultClass = 'success';
                    resultText = '✅ Valid nonce accepted (different error occurred)';
                }
            } else {
                // For invalid/missing nonce, we expect security failure
                if (data.data && data.data.message === 'Security verification failed') {
                    resultClass = 'success';
                    resultText = '✅ Invalid/missing nonce correctly rejected';
                } else {
                    resultClass = 'error';
                    resultText = '❌ Invalid/missing nonce was not rejected';
                }
            }
            
            resultDiv.innerHTML = `
                <h3>${testName} Result</h3>
                <div class="${resultClass}">${resultText}</div>
                <div class="info"><strong>Response Message:</strong> ${data.data ? data.data.message : data.message || 'No message'}</div>
                <h4>Full Response:</h4>
                <pre>${JSON.stringify(data, null, 2)}</pre>
            `;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <h3>${testName} Result</h3>
                <div class="error">❌ Network Error: ${error.message}</div>
            `;
        });
    }
    </script>
</body>
</html>

