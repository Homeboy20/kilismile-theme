<?php
/**
 * Test Payment Button Debug
 */
get_header();
?>

<div style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h1>Payment Button Debug</h1>
    
    <div id="test-results">
        <h2>Testing AJAX Handler Registration</h2>
        <div id="ajax-test-result"></div>
        
        <h2>Testing Classes</h2>
        <div>
            <?php
            $classes = array(
                'KiliSmile_Payment_Processor',
                'KiliSmile_AzamPay', 
                'KiliSmile_Donation_Database'
            );
            
            foreach ($classes as $class) {
                if (class_exists($class)) {
                    echo "✅ {$class} - Loaded<br>";
                } else {
                    echo "❌ {$class} - Missing<br>";
                }
            }
            ?>
        </div>
        
        <h2>Testing AJAX Actions</h2>
        <div>
            <?php
            $actions = array(
                'wp_ajax_kilismile_process_payment',
                'wp_ajax_nopriv_kilismile_process_payment'
            );
            
            foreach ($actions as $action) {
                if (has_action($action)) {
                    echo "✅ {$action} - Registered<br>";
                } else {
                    echo "❌ {$action} - Not registered<br>";
                }
            }
            ?>
        </div>
        
        <h2>Test Payment Submission</h2>
        <form id="test-payment-form">
            <p>
                <label>Amount: <input type="number" name="amount" value="10000" required></label>
            </p>
            <p>
                <label>Currency: 
                    <select name="currency">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                    </select>
                </label>
            </p>
            <p>
                <label>Name: <input type="text" name="donor_name" value="Test User" required></label>
            </p>
            <p>
                <label>Email: <input type="email" name="donor_email" value="test@example.com" required></label>
            </p>
            <p>
                <label>Phone: <input type="tel" name="donor_phone" value="0712345678"></label>
            </p>
            <p>
                <label>Network: 
                    <select name="mobile_network">
                        <option value="vodacom">M-Pesa (Vodacom)</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="tigo">Tigo Pesa</option>
                    </select>
                </label>
            </p>
            <button type="submit">Test Payment Processing</button>
        </form>
        
        <div id="payment-test-result" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment debug page loaded');
    
    // Test form submission
    document.getElementById('test-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const resultDiv = document.getElementById('payment-test-result');
        resultDiv.innerHTML = 'Processing...';
        
        const formData = new FormData(this);
        formData.append('action', 'kilismile_process_payment');
        formData.append('payment_gateway', formData.get('currency') === 'USD' ? 'paypal' : 'azampay');
        formData.append('use_checkout', false);
        formData.append('nonce', '<?php echo wp_create_nonce("kilismile_payment_nonce"); ?>');
        
        console.log('Submitting payment test...');
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed response:', data);
                resultDiv.innerHTML = '<h3>Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            } catch (e) {
                console.error('JSON parse error:', e);
                resultDiv.innerHTML = '<h3>Raw Response:</h3><pre>' + text + '</pre>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            resultDiv.innerHTML = '<h3>Error:</h3><p style="color: red;">' + error.message + '</p>';
        });
    });
});
</script>

<?php get_footer(); ?>

