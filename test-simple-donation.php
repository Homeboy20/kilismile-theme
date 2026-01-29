<?php
/**
 * Simple donation button test - Standalone version
 */

// Include WordPress bootstrap
require_once('../../../wp-load.php');

// Basic HTML structure
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Donation Button</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Test Donation Button</h1>
    
    <!-- Simple test form -->
    <form id="simple-donation-test" style="background: #f9f9f9; padding: 30px; border-radius: 10px;">
        <h3>Quick Test Form</h3>
        
        <p>
            <label>Amount: <input type="number" name="amount" value="10000" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        </p>
        
        <p>
            <label>Currency: 
                <select name="currency" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="TZS">TZS</option>
                    <option value="USD">USD</option>
                </select>
            </label>
        </p>
        
        <p>
            <label>First Name: <input type="text" name="first_name" value="Test" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        </p>
        
        <p>
            <label>Last Name: <input type="text" name="last_name" value="User" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        </p>
        
        <p>
            <label>Email: <input type="email" name="email" value="test@example.com" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        </p>
        
        <p>
            <label>Phone: <input type="tel" name="phone" value="0712345678" style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        </p>
        
        <p>
            <label>Mobile Network: 
                <select name="mobile_network" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="vodacom">M-Pesa (Vodacom)</option>
                    <option value="airtel">Airtel Money</option>
                    <option value="tigo">Tigo Pesa</option>
                </select>
            </label>
        </p>
        
        <p>
            <button type="submit" id="test-submit-btn" style="background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%;">
                Test Complete Donation
            </button>
        </p>
        
        <div id="test-result" style="margin-top: 20px; padding: 15px; background: white; border-radius: 5px; display: none;"></div>
    </form>
    
    <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 10px;">
        <h3>Debug Information</h3>
        
        <h4>Required Classes:</h4>
        <?php
        $classes_status = array(
            'KiliSmile_Payment_Processor' => class_exists('KiliSmile_Payment_Processor'),
            'KiliSmile_AzamPay' => class_exists('KiliSmile_AzamPay'),
            'KiliSmile_Donation_Database' => class_exists('KiliSmile_Donation_Database')
        );
        
        foreach ($classes_status as $class => $exists) {
            echo $exists ? "✅ $class" : "❌ $class";
            echo "<br>";
        }
        ?>
        
        <h4>AJAX Actions:</h4>
        <?php
        $actions_status = array(
            'wp_ajax_kilismile_process_payment' => has_action('wp_ajax_kilismile_process_payment'),
            'wp_ajax_nopriv_kilismile_process_payment' => has_action('wp_ajax_nopriv_kilismile_process_payment')
        );
        
        foreach ($actions_status as $action => $registered) {
            echo $registered ? "✅ $action" : "❌ $action";
            echo "<br>";
        }
        ?>
        
        <h4>AJAX URL:</h4>
        <code><?php echo admin_url('admin-ajax.php'); ?></code>
        
        <h4>Browser Console:</h4>
        <p>Open browser console (F12) to see any JavaScript errors.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test page loaded');
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('AJAX URL:', '<?php echo admin_url("admin-ajax.php"); ?>');
    
    const form = document.getElementById('simple-donation-test');
    const resultDiv = document.getElementById('test-result');
    const submitBtn = document.getElementById('test-submit-btn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Show loading
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = 'Processing payment...';
        
        // Collect form data
        const formData = new FormData();
        formData.append('action', 'kilismile_process_payment');
        
        // Add form fields
        const fields = ['amount', 'currency', 'first_name', 'last_name', 'email', 'phone', 'mobile_network'];
        fields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                formData.append(field, input.value);
            }
        });
        
        // Add additional required fields
        formData.append('donor_name', form.querySelector('[name="first_name"]').value + ' ' + form.querySelector('[name="last_name"]').value);
        formData.append('donor_email', form.querySelector('[name="email"]').value);
        formData.append('donor_phone', form.querySelector('[name="phone"]').value);
        formData.append('payment_phone', form.querySelector('[name="phone"]').value);
        formData.append('payment_gateway', form.querySelector('[name="currency"]').value === 'USD' ? 'paypal' : 'azampay');
        formData.append('use_checkout', 'false');
        formData.append('anonymous', 'false');
        formData.append('recurring', 'false');
        formData.append('nonce', '<?php echo wp_create_nonce("kilismile_payment_nonce"); ?>');
        
        console.log('Sending AJAX request...');
        
        // Log all form data
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response received:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                console.log('Parsed response:', data);
                
                if (data.success) {
                    resultDiv.innerHTML = '<div style="color: green;"><h4>✅ Success!</h4><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: red;"><h4>❌ Error</h4><p>' + (data.message || 'Unknown error') + '</p><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                resultDiv.innerHTML = '<div style="color: red;"><h4>❌ Response Error</h4><p>Invalid JSON response</p><pre>' + text + '</pre></div>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            resultDiv.innerHTML = '<div style="color: red;"><h4>❌ Network Error</h4><p>' + error.message + '</p></div>';
        })
        .finally(() => {
            submitBtn.textContent = 'Test Complete Donation';
            submitBtn.disabled = false;
        });
    });
});
</script>

</div>
</body>
</html>

