<?php
/**
 * Frontend Payment Form Template
 * Handles automatic payment gateway selection based on currency
 */

// Get payment gateway settings
$paypal_enabled = get_option('kilismile_paypal_enabled', false);
$azampay_enabled = get_option('kilismile_azampay_enabled', false);
$default_currency = get_option('kilismile_default_currency', 'USD');
?>

<div class="kilismile-payment-form" id="kilismile-payment-form">
    <form id="donation-form" class="donation-form">
        <!-- Currency Selection -->
        <div class="form-section currency-section">
            <h3><?php _e('Select Currency', 'kilismile'); ?></h3>
            <div class="currency-options">
                <label class="currency-option">
                    <input type="radio" name="currency" value="USD" <?php checked($default_currency, 'USD'); ?>>
                    <span class="currency-label">
                        <strong>USD ($)</strong>
                        <small><?php _e('International payments via PayPal', 'kilismile'); ?></small>
                    </span>
                </label>
                <label class="currency-option">
                    <input type="radio" name="currency" value="TZS" <?php checked($default_currency, 'TZS'); ?>>
                    <span class="currency-label">
                        <strong>TZS (TSh)</strong>
                        <small><?php _e('Mobile money payments via AzamPay', 'kilismile'); ?></small>
                    </span>
                </label>
            </div>
        </div>

        <!-- Amount Input -->
        <div class="form-section amount-section">
            <h3><?php _e('Donation Amount', 'kilismile'); ?></h3>
            <div class="amount-input-wrapper">
                <span class="currency-symbol" id="currency-symbol">$</span>
                <input type="number" 
                       id="amount" 
                       name="amount" 
                       min="1" 
                       step="0.01" 
                       placeholder="0.00" 
                       required>
            </div>
            <div class="preset-amounts" id="preset-amounts">
                <!-- Preset amounts will be populated by JavaScript -->
            </div>
        </div>

        <!-- Donor Information -->
        <div class="form-section donor-section">
            <h3><?php _e('Your Information', 'kilismile'); ?></h3>
            <div class="form-row">
                <div class="form-field">
                    <label for="donor_name"><?php _e('Full Name', 'kilismile'); ?></label>
                    <input type="text" id="donor_name" name="donor_name" required>
                </div>
                <div class="form-field">
                    <label for="donor_email"><?php _e('Email Address', 'kilismile'); ?></label>
                    <input type="email" id="donor_email" name="donor_email" required>
                </div>
            </div>
            <div class="form-field phone-field" id="phone-field" style="display: none;">
                <label for="donor_phone"><?php _e('Phone Number', 'kilismile'); ?></label>
                <div class="phone-input">
                    <span class="country-code">+255</span>
                    <input type="tel" id="donor_phone" name="donor_phone" placeholder="712345678">
                </div>
            </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="form-section payment-section">
            <h3><?php _e('Payment Method', 'kilismile'); ?></h3>
            
            <!-- PayPal Payment (USD) -->
            <div class="payment-method paypal-method" id="paypal-method" style="display: none;">
                <div class="payment-method-header">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/paypal-logo.png" alt="PayPal" class="payment-logo">
                    <span><?php _e('Secure payment via PayPal', 'kilismile'); ?></span>
                </div>
                <p class="payment-description">
                    <?php _e('You will be redirected to PayPal to complete your payment securely.', 'kilismile'); ?>
                </p>
            </div>

            <!-- Mobile Money Payment (TZS) -->
            <div class="payment-method mobile-money-method" id="mobile-money-method" style="display: none;">
                <div class="payment-method-header">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/azampay-logo.png" alt="AzamPay" class="payment-logo">
                    <span><?php _e('Mobile Money Payment', 'kilismile'); ?></span>
                </div>
                
                <div class="mobile-networks">
                    <h4><?php _e('Select Your Mobile Network', 'kilismile'); ?></h4>
                    <div class="network-options">
                        <label class="network-option">
                            <input type="radio" name="mobile_network" value="vodacom">
                            <div class="network-card">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vodacom-logo.png" alt="Vodacom M-Pesa">
                                <span>Vodacom M-Pesa</span>
                            </div>
                        </label>
                        <label class="network-option">
                            <input type="radio" name="mobile_network" value="airtel">
                            <div class="network-card">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/airtel-logo.png" alt="Airtel Money">
                                <span>Airtel Money</span>
                            </div>
                        </label>
                        <label class="network-option">
                            <input type="radio" name="mobile_network" value="tigo">
                            <div class="network-card">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/tigo-logo.png" alt="Tigo Pesa">
                                <span>Tigo Pesa</span>
                            </div>
                        </label>
                        <label class="network-option">
                            <input type="radio" name="mobile_network" value="halopesa">
                            <div class="network-card">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/halopesa-logo.png" alt="HaloPesa">
                                <span>HaloPesa</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-section submit-section">
            <button type="submit" id="submit-payment" class="payment-submit-btn" disabled>
                <span class="btn-text"><?php _e('Donate Now', 'kilismile'); ?></span>
                <span class="btn-loader" style="display: none;">
                    <svg class="spinner" viewBox="0 0 50 50">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-dashoffset="60" stroke-dasharray="30"></circle>
                    </svg>
                    <?php _e('Processing...', 'kilismile'); ?>
                </span>
            </button>
        </div>
    </form>

    <!-- Payment Status Messages -->
    <div class="payment-messages" id="payment-messages"></div>
</div>

<script>
// Pass PHP data to JavaScript
window.kilismilePayment = {
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>',
    currency: '<?php echo $default_currency; ?>',
    paypalEnabled: <?php echo $paypal_enabled ? 'true' : 'false'; ?>,
    azampayEnabled: <?php echo $azampay_enabled ? 'true' : 'false'; ?>
};
</script>

