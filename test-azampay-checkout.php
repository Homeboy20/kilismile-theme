<?php
/**
 * Test AzamPay Checkout Integration
 * Demonstrates both STK Push and Checkout Page functionality
 */

require_once get_template_directory() . '/includes/azampay-integration.php';
require_once get_template_directory() . '/includes/payment-processor.php';

get_header();

// Handle form submission
if ($_POST && wp_verify_nonce($_POST['test_nonce'], 'test_azampay_checkout')) {
    try {
        $azampay = new KiliSmile_AzamPay();
        $payment_method = sanitize_text_field($_POST['payment_method']);
        
        $payment_data = array(
            'amount' => floatval($_POST['amount']),
            'currency' => 'TZS',
            'reference' => 'TEST_' . time() . '_' . wp_rand(1000, 9999),
            'donor_name' => sanitize_text_field($_POST['donor_name']),
            'donor_email' => sanitize_email($_POST['donor_email']),
            'donor_phone' => sanitize_text_field($_POST['donor_phone']),
            'network' => sanitize_text_field($_POST['mobile_network']),
            'donation_type' => 'test',
            'purpose' => 'testing'
        );
        
        if ($payment_method === 'checkout') {
            // Test AzamPay Checkout (Hosted Payment Page)
            $result = $azampay->create_checkout_session($payment_data);
            
            if ($result['success']) {
                echo '<div class="success-message">';
                echo '<h3>✅ Checkout Session Created Successfully!</h3>';
                echo '<p><strong>Reference:</strong> ' . esc_html($result['reference']) . '</p>';
                echo '<p><strong>Checkout ID:</strong> ' . esc_html($result['checkout_id']) . '</p>';
                echo '<p><a href="' . esc_url($result['checkout_url']) . '" target="_blank" class="button">Open Checkout Page</a></p>';
                echo '</div>';
            }
        } else {
            // Test STK Push (Direct Mobile Payment)
            $result = $azampay->initiate_stkpush($payment_data);
            
            if ($result['success']) {
                echo '<div class="success-message">';
                echo '<h3>📱 STK Push Sent Successfully!</h3>';
                echo '<p><strong>Reference:</strong> ' . esc_html($result['reference']) . '</p>';
                echo '<p><strong>Transaction ID:</strong> ' . esc_html($result['transaction_id']) . '</p>';
                echo '<p><strong>Message:</strong> ' . esc_html($result['message']) . '</p>';
                echo '</div>';
            }
        }
        
    } catch (Exception $e) {
        echo '<div class="error-message">';
        echo '<h3>❌ Error</h3>';
        echo '<p>' . esc_html($e->getMessage()) . '</p>';
        echo '</div>';
    }
}
?>

<style>
.azampay-test-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.test-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.payment-method-group {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.payment-method-option {
    flex: 1;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-option:hover {
    border-color: #4CAF50;
    background-color: #f8f8f8;
}

.payment-method-option.selected {
    border-color: #4CAF50;
    background-color: #e8f5e8;
}

.payment-method-option input[type="radio"] {
    margin-right: 10px;
}

.submit-button {
    background: #4CAF50;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 4px;
    font-size: 18px;
    cursor: pointer;
    width: 100%;
}

.submit-button:hover {
    background: #45a049;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.button {
    display: inline-block;
    padding: 10px 20px;
    background: #007cba;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 10px;
}

.info-box {
    background: #d1ecf1;
    color: #0c5460;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #bee5eb;
}
</style>

<div class="azampay-test-container">
    <h1>🧪 AzamPay Checkout Integration Test</h1>
    
    <div class="info-box">
        <h3>📋 Test Information</h3>
        <p><strong>Purpose:</strong> Test both AzamPay STK Push and Checkout Page functionality</p>
        <p><strong>STK Push:</strong> Sends payment request directly to customer's mobile phone</p>
        <p><strong>Checkout Page:</strong> Redirects customer to AzamPay hosted payment page</p>
        <p><strong>Environment:</strong> <?php echo get_option('kilismile_azampay_sandbox', true) ? 'Sandbox' : 'Production'; ?></p>
    </div>
    
    <form method="post" class="test-form">
        <?php wp_nonce_field('test_azampay_checkout', 'test_nonce'); ?>
        
        <h2>💳 Payment Method Selection</h2>
        <div class="payment-method-group">
            <div class="payment-method-option" onclick="selectPaymentMethod('stkpush')">
                <input type="radio" name="payment_method" value="stkpush" id="stkpush" checked>
                <label for="stkpush">
                    <h3>📱 STK Push</h3>
                    <p>Direct mobile payment</p>
                </label>
            </div>
            <div class="payment-method-option" onclick="selectPaymentMethod('checkout')">
                <input type="radio" name="payment_method" value="checkout" id="checkout">
                <label for="checkout">
                    <h3>🌐 Checkout Page</h3>
                    <p>Hosted payment page</p>
                </label>
            </div>
        </div>
        
        <h2>👤 Donor Information</h2>
        <div class="form-group">
            <label for="donor_name">Full Name:</label>
            <input type="text" name="donor_name" id="donor_name" value="Test Donor" required>
        </div>
        
        <div class="form-group">
            <label for="donor_email">Email Address:</label>
            <input type="email" name="donor_email" id="donor_email" value="test@example.com" required>
        </div>
        
        <div class="form-group">
            <label for="donor_phone">Phone Number (255 format):</label>
            <input type="text" name="donor_phone" id="donor_phone" value="255712345678" required>
        </div>
        
        <h2>💰 Payment Details</h2>
        <div class="form-group">
            <label for="amount">Amount (TZS):</label>
            <input type="number" name="amount" id="amount" value="1000" min="1" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="mobile_network">Mobile Network:</label>
            <select name="mobile_network" id="mobile_network" required>
                <option value="vodacom">Vodacom (M-Pesa)</option>
                <option value="airtel">Airtel Money</option>
                <option value="tigo">Tigo Pesa</option>
                <option value="halopesa">HaloPesa</option>
                <option value="azampesa">AzamPesa</option>
            </select>
        </div>
        
        <button type="submit" class="submit-button">
            🚀 Test Payment Processing
        </button>
    </form>
    
    <div style="margin-top: 40px; padding: 20px; background: white; border-radius: 8px;">
        <h3>🔗 Integration URLs</h3>
        <p><strong>Callback URL:</strong> <?php echo admin_url('admin-ajax.php?action=azampay_callback'); ?></p>
        <p><strong>Success URL:</strong> <?php echo home_url('/donation-success/'); ?></p>
        <p><strong>Failure URL:</strong> <?php echo home_url('/donation-failed/'); ?></p>
        <p><strong>Cancel URL:</strong> <?php echo home_url('/donation-cancelled/'); ?></p>
    </div>
</div>

<script>
function selectPaymentMethod(method) {
    // Remove selected class from all options
    document.querySelectorAll('.payment-method-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    // Check the radio button
    document.getElementById(method).checked = true;
}

// Initialize first option as selected
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.payment-method-option').classList.add('selected');
});
</script>

<?php get_footer(); ?>

