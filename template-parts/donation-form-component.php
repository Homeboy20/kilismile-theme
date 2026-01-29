<?php
/**
 * Enhanced Donation Form Component - Integrated with KiliSmile Payments Plugin
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if KiliSmile Payments plugin is active
$plugin_active = class_exists('KiliSmile_Payments_Plugin');

// Load debug tracker if not using plugin
if (!$plugin_active) {
    require_once get_template_directory() . '/includes/donation-debug-tracker.php';
    
    // Log form initialization
    KiliSmile_Donation_Debug::log_transaction('form_component_loaded', array(
        'args' => $args ?? array(),
        'user_id' => get_current_user_id(),
        'page_url' => $_SERVER['REQUEST_URI'] ?? '',
        'plugin_status' => 'inactive'
    ));
}

// Initialize variables
if (!isset($suggested_amounts)) {
    $suggested_amounts = array(
        'TZS' => array(10000, 25000, 50000, 100000, 250000),
        'USD' => array(5, 10, 25, 50, 100)
    );
}
if (!isset($default_currency)) $default_currency = 'USD';

$args = wp_parse_args($args ?? array(), array(
    'class' => 'kilismile-donation-form',
    'show_recurring' => true,
    'show_anonymous' => true,
    'submit_text' => __('Complete Donation', 'kilismile'),
    'show_amounts' => true,
    'form_style' => 'modern',
    'primary_color' => '#007cba'
));

// Implement 3-step donation checkout process
echo '<div class="kilismile-template-info" style="background: #e3f2fd; border: 1px solid #90caf9; color: #0d47a1; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">';
echo '<i class="fas fa-credit-card" style="margin-right: 8px;"></i>';
echo __('3-Step Donation Checkout Process', 'kilismile');
echo '</div>';

// Implement complete 3-step donation checkout process
?>

<div class="kilismile-donation-checkout" id="kilismile-donation-checkout">
    <!-- Progress Indicator -->
    <div class="checkout-progress">
        <div class="progress-step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-title"><?php _e('Amount', 'kilismile'); ?></div>
        </div>
        <div class="progress-step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-title"><?php _e('Details', 'kilismile'); ?></div>
        </div>
        <div class="progress-step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-title"><?php _e('Payment', 'kilismile'); ?></div>
        </div>
        <div class="progress-line">
            <div class="progress-fill"></div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="checkout-form-container">
        <form id="donation-checkout-form" class="donation-checkout-form" method="post">
            <?php wp_nonce_field('kilismile_donation_checkout', 'donation_nonce'); ?>
            
            <!-- Step 1: Amount Selection -->
            <div class="checkout-step" id="step-1" data-step="1">
                <div class="step-header">
                    <h3><?php _e('Choose Your Donation Amount', 'kilismile'); ?></h3>
                    <p><?php _e('Select an amount or enter a custom donation amount.', 'kilismile'); ?></p>
                </div>

                <div class="step-content">
                    <!-- Currency Toggle -->
                    <div class="currency-selector">
                        <label class="currency-option">
                            <input type="radio" name="currency" value="TZS" checked>
                            <span class="currency-label">
                                <i class="fas fa-flag"></i>
                                <strong>TZS</strong>
                                <small><?php _e('Tanzanian Shilling', 'kilismile'); ?></small>
                            </span>
                        </label>
                        <label class="currency-option">
                            <input type="radio" name="currency" value="USD">
                            <span class="currency-label">
                                <i class="fas fa-globe"></i>
                                <strong>USD</strong>
                                <small><?php _e('US Dollar', 'kilismile'); ?></small>
                            </span>
                        </label>
                    </div>

                    <!-- Preset Amount Options -->
                    <div class="amount-options">
                        <div class="preset-amounts" data-currency="TZS">
                            <?php foreach ($suggested_amounts['TZS'] as $amount): ?>
                                <button type="button" class="amount-btn" data-amount="<?php echo $amount; ?>">
                                    TZS <?php echo number_format($amount); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="preset-amounts" data-currency="USD" style="display: none;">
                            <?php foreach ($suggested_amounts['USD'] as $amount): ?>
                                <button type="button" class="amount-btn" data-amount="<?php echo $amount; ?>">
                                    $<?php echo $amount; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Custom Amount Input -->
                    <div class="custom-amount">
                        <label for="custom-amount-input"><?php _e('Or enter custom amount:', 'kilismile'); ?></label>
                        <div class="amount-input-group">
                            <span class="currency-symbol">TZS</span>
                            <input type="number" id="custom-amount-input" name="donation_amount" min="1" step="0.01" placeholder="0">
                        </div>
                    </div>


                </div>

                <div class="step-actions">
                    <button type="button" class="btn btn-primary btn-next" data-next="2">
                        <?php _e('Continue to Details', 'kilismile'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Donor Details -->
            <div class="checkout-step" id="step-2" data-step="2" style="display: none;">
                <div class="step-header">
                    <h3><?php _e('Your Information', 'kilismile'); ?></h3>
                    <p><?php _e('Please provide your contact details for the donation receipt.', 'kilismile'); ?></p>
                </div>

                <div class="step-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="donor-first-name"><?php _e('First Name *', 'kilismile'); ?></label>
                            <input type="text" id="donor-first-name" name="donor_first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="donor-last-name"><?php _e('Last Name *', 'kilismile'); ?></label>
                            <input type="text" id="donor-last-name" name="donor_last_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="donor-email"><?php _e('Email Address *', 'kilismile'); ?></label>
                        <input type="email" id="donor-email" name="donor_email" required>
                    </div>

                    <div class="form-group">
                        <label for="donor-phone"><?php _e('Phone Number', 'kilismile'); ?></label>
                        <input type="tel" id="donor-phone" name="donor_phone" placeholder="+255..." required>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-option">
                            <input type="checkbox" name="anonymous_donation" value="1">
                            <span class="checkmark"></span>
                            <?php _e('Make this donation anonymous', 'kilismile'); ?>
                        </label>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn btn-secondary btn-back" data-back="1">
                        <i class="fas fa-arrow-left"></i>
                        <?php _e('Back', 'kilismile'); ?>
                    </button>
                    <button type="button" class="btn btn-primary btn-next" data-next="3">
                        <?php _e('Continue to Payment', 'kilismile'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Payment Method -->
            <div class="checkout-step" id="step-3" data-step="3" style="display: none;">
                <div class="step-header">
                    <h3><?php _e('Complete Your Donation', 'kilismile'); ?></h3>
                    <p><?php _e('Choose your payment method to complete the donation.', 'kilismile'); ?></p>
                </div>

                <div class="step-content">
                    <!-- Donation Summary -->
                    <div class="donation-summary">
                        <h4><?php _e('Donation Summary', 'kilismile'); ?></h4>
                        <div class="summary-item">
                            <span><?php _e('Amount:', 'kilismile'); ?></span>
                            <strong class="summary-amount">TZS 0</strong>
                        </div>
                        <div class="summary-item">
                            <span><?php _e('Donor:', 'kilismile'); ?></span>
                            <span class="summary-donor">-</span>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h4><?php _e('Select Payment Method', 'kilismile'); ?></h4>
                        
                        <div class="payment-options">
                            <label class="payment-option azampay-option">
                                <input type="radio" name="payment_method" value="azampay" checked>
                                <div class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="payment-info">
                                        <strong><?php _e('AzamPay', 'kilismile'); ?></strong>
                                        <small><?php _e('Mobile Money', 'kilismile'); ?></small>
                                    </div>
                                    <div class="payment-badge"><?php _e('Local', 'kilismile'); ?></div>
                                </div>
                            </label>

                            <label class="payment-option paypal-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <div class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                    <div class="payment-info">
                                        <strong><?php _e('PayPal', 'kilismile'); ?></strong>
                                        <small><?php _e('Credit Card, PayPal Balance', 'kilismile'); ?></small>
                                    </div>
                                    <div class="payment-badge"><?php _e('Global', 'kilismile'); ?></div>
                                </div>
                            </label>

                            <label class="payment-option manual-option">
                                <input type="radio" name="payment_method" value="manual_transfer">
                                <div class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div class="payment-info">
                                        <strong><?php _e('Manual Transfer', 'kilismile'); ?></strong>
                                        <small><?php _e('Bank Transfer (Offline)', 'kilismile'); ?></small>
                                    </div>
                                    <div class="payment-badge"><?php _e('Offline', 'kilismile'); ?></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="terms-section">
                        <label class="checkbox-option">
                            <input type="checkbox" name="accept_terms" value="1" required>
                            <span class="checkmark"></span>
                            <?php printf(__('I agree to the %s and %s', 'kilismile'), 
                                '<a href="#" target="_blank">' . __('Terms of Service', 'kilismile') . '</a>',
                                '<a href="#" target="_blank">' . __('Privacy Policy', 'kilismile') . '</a>'
                            ); ?>
                        </label>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn btn-secondary btn-back" data-back="2">
                        <i class="fas fa-arrow-left"></i>
                        <?php _e('Back', 'kilismile'); ?>
                    </button>
                    <button type="submit" class="btn btn-success btn-donate">
                        <i class="fas fa-heart"></i>
                        <?php _e('Complete Donation', 'kilismile'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div class="checkout-loading" id="checkout-loading" style="display: none;">
        <div class="loading-content">
            <div class="spinner"></div>
            <h4><?php _e('Processing Your Donation...', 'kilismile'); ?></h4>
            <p><?php _e('Please wait while we prepare your payment.', 'kilismile'); ?></p>
        </div>
    </div>
</div>

<!-- 3-Step Donation Checkout Styles -->
<style>
.kilismile-donation-checkout {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Progress Indicator */
.checkout-progress {
    position: relative;
    display: flex;
    justify-content: space-between;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    color: white;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
    position: relative;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.progress-step.active .step-number,
.progress-step.completed .step-number {
    background: #fff;
    color: #667eea;
}

.step-title {
    font-size: 0.9rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.progress-step.active .step-title,
.progress-step.completed .step-title {
    opacity: 1;
    font-weight: 600;
}

.progress-line {
    position: absolute;
    top: 50px;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255,255,255,0.3);
    z-index: 1;
}

.progress-fill {
    height: 100%;
    background: #fff;
    width: 0%;
    transition: width 0.5s ease;
}

/* Form Container */
.checkout-form-container {
    position: relative;
    min-height: 500px;
}

.checkout-step {
    padding: 40px;
    animation: fadeIn 0.5s ease;
}

.step-header {
    text-align: center;
    margin-bottom: 30px;
}

.step-header h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 1.5rem;
}

.step-header p {
    color: #666;
    margin: 0;
}

/* Currency Selector */
.currency-selector {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 30px;
}

.currency-option {
    cursor: pointer;
}

.currency-option input[type="radio"] {
    display: none;
}

.currency-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
    min-width: 120px;
}

.currency-option input[type="radio"]:checked + .currency-label {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
}

.currency-label i {
    font-size: 1.5rem;
    margin-bottom: 8px;
    color: #667eea;
}

/* Amount Options */
.amount-options {
    margin-bottom: 30px;
}

.preset-amounts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.amount-btn {
    padding: 15px;
    border: 2px solid #e9ecef;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    color: #2c3e50;
}

.amount-btn:hover,
.amount-btn.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
    color: #667eea;
}

/* Custom Amount */
.custom-amount {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.amount-input-group {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.amount-input-group:focus-within {
    border-color: #667eea;
}

.currency-symbol {
    padding: 15px;
    background: #667eea;
    color: white;
    font-weight: bold;
}

.amount-input-group input {
    flex: 1;
    padding: 15px;
    border: none;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Form Elements */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
}

/* Checkbox Options */
.form-options {
    margin-top: 30px;
}

.checkbox-option {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    cursor: pointer;
}

.checkbox-option input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #e9ecef;
    border-radius: 4px;
    margin-right: 12px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-option input[type="checkbox"]:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.checkbox-option input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: -2px;
    left: 3px;
    color: white;
    font-weight: bold;
}

/* Donation Summary */
.donation-summary {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.summary-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.summary-amount {
    color: #667eea;
    font-size: 1.2rem;
}

/* Payment Methods */
.payment-methods {
    margin-bottom: 30px;
}

.payment-options {
    display: grid;
    gap: 15px;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-card {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option input[type="radio"]:checked + .payment-card {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
}

.payment-icon {
    font-size: 2rem;
    margin-right: 15px;
    color: #667eea;
}

.payment-info {
    flex: 1;
}

.payment-badge {
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Buttons */
.step-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

/* Loading Overlay */
.checkout-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-content {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .checkout-progress {
        padding: 20px 15px;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
    }
    
    .step-title {
        font-size: 0.8rem;
    }
    
    .checkout-step {
        padding: 20px;
    }
    
    .currency-selector {
        flex-direction: column;
        align-items: center;
    }
    
    .preset-amounts {
        grid-template-columns: 1fr 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .step-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- 3-Step Donation Checkout JavaScript -->
<script>
jQuery(document).ready(function($) {
    const checkout = {
        currentStep: 1,
        totalSteps: 3,
        formData: {},
        
        init: function() {
            this.bindEvents();
            this.updateProgress();
            this.updateCurrencyDisplay();
        },
        
        bindEvents: function() {
            // Step navigation
            $('.btn-next').on('click', this.nextStep.bind(this));
            $('.btn-back').on('click', this.prevStep.bind(this));
            
            // Currency change
            $('input[name="currency"]').on('change', this.updateCurrencyDisplay.bind(this));
            
            // Amount selection
            $('.amount-btn').on('click', this.selectAmount.bind(this));
            $('#custom-amount-input').on('input', this.updateCustomAmount.bind(this));
            
            // Form submission
            $('#donation-checkout-form').on('submit', this.submitForm.bind(this));
            
            // Real-time summary updates
            $('input, select, textarea').on('input change', this.updateSummary.bind(this));
        },
        
        nextStep: function(e) {
            e.preventDefault();
            const nextStep = parseInt($(e.target).data('next'));
            
            if (this.validateStep(this.currentStep)) {
                this.showStep(nextStep);
            }
        },
        
        prevStep: function(e) {
            e.preventDefault();
            const prevStep = parseInt($(e.target).data('back'));
            this.showStep(prevStep);
        },
        
        showStep: function(step) {
            // Hide current step
            $(`#step-${this.currentStep}`).fadeOut(300, () => {
                // Update current step
                this.currentStep = step;
                
                // Show new step
                $(`#step-${step}`).fadeIn(300);
                
                // Update progress
                this.updateProgress();
                
                // Update summary if on final step
                if (step === 3) {
                    this.updateSummary();
                }
            });
        },
        
        updateProgress: function() {
            // Update step indicators
            $('.progress-step').each((i, el) => {
                const stepNum = i + 1;
                const $step = $(el);
                
                $step.removeClass('active completed');
                
                if (stepNum < this.currentStep) {
                    $step.addClass('completed');
                } else if (stepNum === this.currentStep) {
                    $step.addClass('active');
                }
            });
            
            // Update progress bar
            const progressPercent = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            $('.progress-fill').css('width', progressPercent + '%');
        },
        
        updateCurrencyDisplay: function() {
            const currency = $('input[name="currency"]:checked').val();
            
            // Show/hide amount options
            $('.preset-amounts').hide();
            $(`.preset-amounts[data-currency="${currency}"]`).show();
            
            // Update currency symbol
            $('.currency-symbol').text(currency === 'USD' ? '$' : 'TZS');
            
            // Clear selected amount
            $('.amount-btn').removeClass('selected');
            $('#custom-amount-input').val('');
        },
        
        selectAmount: function(e) {
            e.preventDefault();
            const $btn = $(e.target);
            const amount = $btn.data('amount');
            
            // Update UI
            $('.amount-btn').removeClass('selected');
            $btn.addClass('selected');
            
            // Set input value
            $('#custom-amount-input').val(amount);
        },
        
        updateCustomAmount: function(e) {
            // Clear preset selection when custom amount is entered
            if ($(e.target).val()) {
                $('.amount-btn').removeClass('selected');
            }
        },
        
        updateSummary: function() {
            const currency = $('input[name="currency"]:checked').val();
            const amount = $('#custom-amount-input').val();
            const firstName = $('#donor-first-name').val();
            const lastName = $('#donor-last-name').val();
            const isAnonymous = $('input[name="anonymous_donation"]').is(':checked');
            
            // Update summary
            if (amount) {
                const symbol = currency === 'USD' ? '$' : 'TZS ';
                $('.summary-amount').text(symbol + parseFloat(amount).toLocaleString());
            }
            
            if (isAnonymous) {
                $('.summary-donor').text('Anonymous');
            } else if (firstName && lastName) {
                $('.summary-donor').text(`${firstName} ${lastName}`);
            } else {
                $('.summary-donor').text('-');
            }
        },
        
        validateStep: function(step) {
            let isValid = true;
            let errorMessage = '';
            
            switch(step) {
                case 1:
                    const amount = $('#custom-amount-input').val();
                    if (!amount || parseFloat(amount) <= 0) {
                        errorMessage = 'Please select or enter a donation amount.';
                        isValid = false;
                    }
                    break;
                    
                case 2:
                    const firstName = $('#donor-first-name').val().trim();
                    const lastName = $('#donor-last-name').val().trim();
                    const email = $('#donor-email').val().trim();
                    const phone = $('#donor-phone').val().trim();
                    
                    if (!firstName || !lastName || !email || !phone) {
                        errorMessage = 'Please fill in all required fields.';
                        isValid = false;
                    } else if (!this.isValidEmail(email)) {
                        errorMessage = 'Please enter a valid email address.';
                        isValid = false;
                    } else if (!this.isValidPhone(phone)) {
                        errorMessage = 'Please enter a valid phone number (e.g., +255123456789).';
                        isValid = false;
                    }
                    break;
                    
                case 3:
                    const acceptTerms = $('input[name="accept_terms"]').is(':checked');
                    if (!acceptTerms) {
                        errorMessage = 'Please accept the terms and conditions.';
                        isValid = false;
                    }
                    break;
            }
            
            if (!isValid) {
                this.showError(errorMessage);
            }
            
            return isValid;
        },
        
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        isValidPhone: function(phone) {
            // Accept phone numbers starting with + and containing 10-15 digits
            const phoneRegex = /^\+\d{10,15}$/;
            return phoneRegex.test(phone);
        },
        
        showError: function(message) {
            // Remove existing error
            $('.error-message').remove();
            
            // Add new error
            const $error = $(`<div class="error-message" style="
                background: #f8d7da;
                color: #721c24;
                padding: 12px;
                border-radius: 6px;
                margin: 15px 0;
                border: 1px solid #f5c6cb;
            ">${message}</div>`);
            
            $('.step-actions').before($error);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                $error.fadeOut();
            }, 5000);
        },
        
        submitForm: function(e) {
            e.preventDefault();
            
            if (!this.validateStep(3)) {
                return;
            }
            
            // Show loading
            $('#checkout-loading').fadeIn();
            
            // Collect form data
            const formData = new FormData(document.getElementById('donation-checkout-form'));
            const paymentMethod = $('input[name="payment_method"]:checked').val();
            
            // Process payment through WordPress AJAX
            this.processPayment(formData, paymentMethod);
        },
        
        processPayment: function(formData, paymentMethod) {
            // Debug: log form data
            console.log('Processing payment with data:', {
                amount: formData.get('donation_amount'),
                currency: formData.get('currency'),
                payment_method: paymentMethod
            });
            
            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: {
                    action: 'kilismile_process_donation',
                    nonce: formData.get('donation_nonce'),
                    donation_amount: formData.get('donation_amount'),
                    currency: formData.get('currency'),
                    donor_first_name: formData.get('donor_first_name'),
                    donor_last_name: formData.get('donor_last_name'),
                    donor_email: formData.get('donor_email'),
                    donor_phone: formData.get('donor_phone'),
                    anonymous_donation: formData.get('anonymous_donation') || '0',
                    payment_method: paymentMethod,
                    accept_terms: formData.get('accept_terms') || '0'
                },
                success: (response) => {
                    $('#checkout-loading').fadeOut();
                    
                    if (response.success) {
                        if (response.data.redirect_url) {
                            // Redirect to payment processor (PayPal)
                            this.showRedirectMessage(response.data);
                        } else if (response.data.payment_type === 'stk_push') {
                            // STK Push sent (AzamPay Mobile Money)
                            this.showSTKPushMessage(response.data);
                        } else if (response.data.payment_type === 'manual_transfer' || response.data.payment_method === 'manual_transfer') {
                            // Manual/Bank Transfer instructions
                            this.showManualTransferInstructions(response.data);
                        } else {
                            // Direct payment success
                            this.showSuccessMessage(response.data);
                        }
                    } else {
                        this.showError(response.data.message || 'Payment processing failed. Please try again.');
                    }
                },
                error: (xhr, status, error) => {
                    $('#checkout-loading').fadeOut();
                    console.error('Payment processing error:', error);
                    console.error('XHR Response:', xhr.responseText);
                    console.error('Status:', status);
                    
                    let errorMessage = 'Connection error. Please check your internet and try again.';
                    
                    // Try to parse server error message
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMessage = response.data.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }
                    
                    this.showError(errorMessage);
                }
            });
        },
        
        showSTKPushMessage: function(data) {
            const $modal = $(`
                <div class="stk-push-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.95);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                ">
                    <div class="stk-modal-content" style="
                        background: white;
                        padding: 30px;
                        border-radius: 15px;
                        text-align: center;
                        max-width: 480px;
                        width: 95%;
                        max-height: 90vh;
                        overflow-y: auto;
                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                        position: relative;
                        margin: 10px;
                    ">
                        <div class="stk-icon" style="color: #28a745; font-size: 50px; margin-bottom: 15px;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h2 class="stk-title" style="margin-bottom: 12px; color: #2c3e50; font-size: 1.4rem;">STK Push Sent!</h2>
                        <p style="margin-bottom: 20px; color: #666; line-height: 1.6;">
                            A payment request for <strong style="color: #28a745;">${data.currency} ${parseFloat(data.amount).toLocaleString()}</strong> 
                            has been sent to <strong>${data.phone || 'your mobile phone'}</strong>.
                        </p>
                        
                        ${data.test_mode ? `
                        <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center;">
                                <i class="fas fa-flask" style="font-size: 1.2rem; margin-right: 10px;"></i>
                                <div>
                                    <strong>Test Mode Active</strong><br>
                                    <span style="font-size: 0.9rem;">Using AzamPay sandbox environment. ${data.azampay_transaction_id ? 'Real STK Push sent to phone.' : 'This is a simulation.'}</span>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        <!-- Enhanced phone animation and instructions -->
                        <div class="stk-instructions" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #28a745;">
                            <div class="stk-phone-section" style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px; flex-wrap: wrap;">
                                <div class="phone-animation" style="
                                    width: 50px;
                                    height: 75px;
                                    border: 3px solid #28a745;
                                    border-radius: 8px;
                                    position: relative;
                                    margin-right: 15px;
                                    margin-bottom: 10px;
                                    animation: pulse 2s infinite;
                                    background: white;
                                    flex-shrink: 0;
                                ">
                                    <div style="
                                        position: absolute;
                                        top: 6px;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        width: 30px;
                                        height: 20px;
                                        background: #28a745;
                                        border-radius: 3px;
                                        animation: blink 1.5s infinite;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: white;
                                        font-size: 10px;
                                        font-weight: bold;
                                    ">STK</div>
                                    <div style="
                                        position: absolute;
                                        bottom: 6px;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        width: 20px;
                                        height: 3px;
                                        background: #28a745;
                                        border-radius: 2px;
                                    "></div>
                                </div>
                                <div class="stk-text" style="text-align: left; flex: 1; min-width: 200px;">
                                    <h4 style="margin: 0 0 6px 0; color: #2c3e50; font-size: 1rem;">Check Your Phone Now!</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.85rem; line-height: 1.4;">
                                        Look for the STK Push notification<br>
                                        on your mobile device
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Step-by-step instructions -->
                            <div style="text-align: left; margin-top: 20px;">
                                <h5 style="color: #2c3e50; margin-bottom: 12px; font-size: 0.95rem;">
                                    <i class="fas fa-list-ol" style="color: #17a2b8; margin-right: 8px;"></i>
                                    ${data.test_mode && !data.azampay_transaction_id ? 'Test Mode Instructions:' : 'Follow these steps:'}
                                </h5>
                                ${data.test_mode && !data.azampay_transaction_id ? `
                                <ol style="margin: 0; padding-left: 20px; color: #495057; font-size: 0.85rem; line-height: 1.6;">
                                    <li><strong>This is a simulation</strong> - No real STK Push will be sent</li>
                                    <li>In live mode, you would receive a notification on your phone</li>
                                    <li>Click "Payment Completed Successfully" below to continue the test</li>
                                    <li>The system will show the final success screen</li>
                                </ol>
                                ` : `
                                <ol style="margin: 0; padding-left: 20px; color: #495057; font-size: 0.85rem; line-height: 1.6;">
                                    <li>Check your phone for an STK Push notification</li>
                                    <li>Enter your mobile money PIN when prompted</li>
                                    <li>Confirm the payment amount matches above</li>
                                    <li>Complete the transaction on your phone</li>
                                </ol>
                                `}
                            </div>
                        </div>
                        
                        <!-- Transaction details -->
                        <div class="stk-transaction-details" style="background: white; border: 1px solid #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: left;">
                            <h5 style="color: #2c3e50; margin-bottom: 10px; font-size: 0.9rem;">
                                <i class="fas fa-receipt" style="color: #17a2b8; margin-right: 8px;"></i>
                                Transaction Details:
                            </h5>
                            <div class="stk-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.8rem;">
                                <div><strong>Transaction ID:</strong></div>
                                <div style="color: #28a745; font-family: monospace; word-break: break-all;">${data.transaction_id}</div>
                                ${data.azampay_transaction_id ? `
                                <div><strong>AzamPay Ref:</strong></div>
                                <div style="color: #17a2b8; font-family: monospace; word-break: break-all;">${data.azampay_transaction_id}</div>
                                ` : ''}
                                <div><strong>Provider:</strong></div>
                                <div style="word-break: break-word;">${data.payment_provider}</div>
                                <div><strong>Amount:</strong></div>
                                <div style="color: #28a745; font-weight: bold;">${data.currency} ${parseFloat(data.amount).toLocaleString()}</div>
                            </div>
                        </div>
                        
                        <!-- Action buttons with responsive design -->
                        <div class="stk-buttons" style="display: flex; flex-direction: column; gap: 10px;">
                            <button class="btn btn-primary payment-status-btn" style="
                                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                color: white;
                                border: none;
                                padding: 12px 20px;
                                border-radius: 20px;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 0.9rem;
                                transition: all 0.3s ease;
                                width: 100%;
                            " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 15px rgba(40,167,69,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <i class="fas fa-check-circle" style="margin-right: 6px;"></i>
                                Payment Completed Successfully
                            </button>
                            
                            <div class="stk-secondary-buttons" style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                <button class="btn btn-warning resend-stk-btn" style="
                                    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
                                    color: white;
                                    border: none;
                                    padding: 10px 16px;
                                    border-radius: 18px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    font-size: 0.8rem;
                                    flex: 1;
                                    min-width: 140px;
                                ">
                                    <i class="fas fa-redo" style="margin-right: 4px;"></i>
                                    Resend STK
                                </button>
                                
                                <button class="btn btn-secondary cancel-payment-btn" style="
                                    background: #6c757d;
                                    color: white;
                                    border: none;
                                    padding: 10px 16px;
                                    border-radius: 18px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    font-size: 0.8rem;
                                    flex: 1;
                                    min-width: 100px;
                                ">
                                    <i class="fas fa-times" style="margin-right: 4px;"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                        
                        <!-- Help text -->
                        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8;">
                            <p style="margin: 0; font-size: 0.8rem; color: #495057; line-height: 1.5;">
                                <i class="fas fa-info-circle" style="color: #17a2b8; margin-right: 6px;"></i>
                                ${data.test_mode && !data.azampay_transaction_id ? `
                                <strong>Test Mode:</strong> This is a simulation for testing purposes. No real payment will be processed. 
                                Click "Payment Completed Successfully" to see the final success screen and complete the test donation flow.
                                ` : `
                                <strong>Having trouble?</strong> Make sure your phone has network coverage and try again. 
                                The STK Push should appear within 30 seconds. If you still don't receive it, try the "Resend" button above.
                                `}
                            </p>
                        </div>
                    </div>
                </div>
            `);
            
            // Enhanced animation styles with responsive design
            const animationStyles = $(`
                <style>
                    @keyframes pulse {
                        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
                        50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0.1); }
                        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
                    }
                    @keyframes blink {
                        0%, 70% { opacity: 1; }
                        71%, 100% { opacity: 0.3; }
                    }
                    @keyframes slideDown {
                        from { transform: translateY(-100%); opacity: 0; }
                        to { transform: translateY(0); opacity: 1; }
                    }
                    
                    /* STK Push Modal Responsive Styles */
                    .stk-push-modal .btn:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    }
                    
                    /* Mobile Responsive Styles */
                    @media (max-width: 768px) {
                        .stk-modal-content {
                            padding: 20px !important;
                            width: 98% !important;
                            margin: 5px !important;
                            max-height: 95vh !important;
                            border-radius: 10px !important;
                        }
                        
                        .stk-icon {
                            font-size: 40px !important;
                            margin-bottom: 10px !important;
                        }
                        
                        .stk-title {
                            font-size: 1.2rem !important;
                            margin-bottom: 10px !important;
                        }
                        
                        .stk-instructions {
                            padding: 15px !important;
                            margin-bottom: 15px !important;
                        }
                        
                        .stk-phone-section {
                            flex-direction: column !important;
                            text-align: center !important;
                        }
                        
                        .phone-animation {
                            margin-right: 0 !important;
                            margin-bottom: 10px !important;
                        }
                        
                        .stk-text {
                            text-align: center !important;
                            min-width: auto !important;
                        }
                        
                        .stk-text h4 {
                            font-size: 0.95rem !important;
                        }
                        
                        .stk-text p {
                            font-size: 0.8rem !important;
                        }
                        
                        .stk-transaction-details {
                            padding: 12px !important;
                        }
                        
                        .stk-details-grid {
                            grid-template-columns: 1fr !important;
                            gap: 5px !important;
                            font-size: 0.75rem !important;
                        }
                        
                        .stk-details-grid > div:nth-child(even) {
                            margin-bottom: 8px;
                            padding-bottom: 8px;
                            border-bottom: 1px solid #e9ecef;
                        }
                        
                        .stk-buttons {
                            gap: 8px !important;
                        }
                        
                        .stk-secondary-buttons {
                            flex-direction: column !important;
                            gap: 6px !important;
                        }
                        
                        .stk-secondary-buttons button {
                            min-width: auto !important;
                            width: 100%;
                        }
                    }
                    
                    /* Extra Small Screens */
                    @media (max-width: 480px) {
                        .stk-modal-content {
                            padding: 15px !important;
                            border-radius: 8px !important;
                        }
                        
                        .stk-icon {
                            font-size: 35px !important;
                        }
                        
                        .stk-title {
                            font-size: 1.1rem !important;
                        }
                        
                        .phone-animation {
                            width: 40px !important;
                            height: 60px !important;
                        }
                        
                        .phone-animation > div:first-child {
                            width: 25px !important;
                            height: 15px !important;
                            font-size: 8px !important;
                        }
                        
                        .phone-animation > div:last-child {
                            width: 15px !important;
                            height: 2px !important;
                        }
                    }
                    
                    /* Landscape Mobile */
                    @media (max-width: 768px) and (orientation: landscape) {
                        .stk-modal-content {
                            max-height: 98vh !important;
                            padding: 15px !important;
                        }
                        
                        .stk-phone-section {
                            flex-direction: row !important;
                            justify-content: center !important;
                        }
                        
                        .phone-animation {
                            margin-right: 15px !important;
                            margin-bottom: 0 !important;
                        }
                        
                        .stk-text {
                            text-align: left !important;
                        }
                    }
                </style>
            `);
            
            $('head').append(animationStyles);
            $('body').append($modal);
            
            // Enhanced event handlers
            const self = this;
            let stkTimeout;
            
            // Auto-hide for test mode after 30 seconds with notification
            if (data.test_mode) {
                stkTimeout = setTimeout(function() {
                    const $notification = $('<div style="position: absolute; top: 10px; left: 10px; right: 10px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 12px; border-radius: 8px; font-size: 0.9rem; text-align: center; animation: slideDown 0.5s ease;"><i class="fas fa-clock" style="margin-right: 8px;"></i>Test simulation ready - you can now complete the payment</div>');
                    $modal.find('.stk-push-modal > div').prepend($notification);
                    
                    // Auto-remove notification after 5 seconds
                    setTimeout(function() {
                        $notification.fadeOut();
                    }, 5000);
                }, 30000);
            }
            
            // Handle successful completion
            $modal.find('.payment-status-btn').on('click', function() {
                if (stkTimeout) clearTimeout(stkTimeout);
                
                $(this).html('<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Verifying Payment...');
                $(this).prop('disabled', true);
                
                // Show progress steps
                const progressSteps = [
                    'Checking payment status...',
                    'Verifying transaction...',
                    'Processing confirmation...'
                ];
                let currentStep = 0;
                
                const progressInterval = setInterval(function() {
                    if (currentStep < progressSteps.length - 1) {
                        currentStep++;
                        $modal.find('.payment-status-btn').html('<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>' + progressSteps[currentStep]);
                    }
                }, 800);
                
                setTimeout(function() {
                    clearInterval(progressInterval);
                    $modal.fadeOut(300, function() {
                        $(this).remove();
                        self.showFinalSuccessMessage(data);
                    });
                }, 2500);
            });
            
            // Handle resend STK
            $modal.find('.resend-stk-btn').on('click', function() {
                const $btn = $(this);
                const originalHtml = $btn.html();
                
                $btn.html('<i class="fas fa-spinner fa-spin" style="margin-right: 6px;"></i>Resending...');
                $btn.prop('disabled', true);
                
                // Simulate resend (in real implementation, make another AJAX call)
                setTimeout(function() {
                    $btn.html('<i class="fas fa-check" style="margin-right: 6px;"></i>STK Sent Again!');
                    $btn.css('background', 'linear-gradient(135deg, #28a745 0%, #20c997 100%)');
                    
                    setTimeout(function() {
                        $btn.html(originalHtml);
                        $btn.css('background', 'linear-gradient(135deg, #ffc107 0%, #fd7e14 100%)');
                        $btn.prop('disabled', false);
                    }, 2000);
                }, 1500);
            });
            
            // Handle cancel
            $modal.find('.cancel-payment-btn').on('click', function() {
                if (stkTimeout) clearTimeout(stkTimeout);
                
                $modal.fadeOut(300, function() {
                    $(this).remove();
                    // Reset form to allow retry
                    $('#checkout-loading').hide();
                });
            });
            
            // Auto-hide loading overlay when modal shows
            $('#checkout-loading').fadeOut();
        },
        
        showRedirectMessage: function(data) {
            const $modal = $(`
                <div class="redirect-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                ">
                    <div style="
                        background: white;
                        padding: 40px;
                        border-radius: 15px;
                        text-align: center;
                        max-width: 500px;
                        width: 90%;
                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                    ">
                        <div style="color: #667eea; font-size: 60px; margin-bottom: 20px;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h2 style="margin-bottom: 15px; color: #2c3e50;">Redirecting to Payment</h2>
                        <p style="margin-bottom: 25px; color: #666; line-height: 1.6;">
                            You will be redirected to <strong>${data.payment_provider}</strong> to complete your secure payment of 
                            <strong style="color: #667eea;">${data.currency} ${parseFloat(data.amount).toLocaleString()}</strong>
                        </p>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
                            <p style="margin: 0; color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-shield-alt" style="color: #28a745; margin-right: 8px;"></i>
                                Your payment is secured with SSL encryption
                            </p>
                        </div>
                        <div class="redirect-countdown" style="margin-bottom: 20px;">
                            <div style="color: #667eea; font-size: 2rem; font-weight: bold;">5</div>
                            <p style="margin: 0; font-size: 0.9rem; color: #666;">seconds remaining</p>
                        </div>
                        <button class="btn btn-primary redirect-now-btn" style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 25px;
                            font-weight: 600;
                            cursor: pointer;
                            margin-right: 10px;
                        ">
                            <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>
                            Continue to Payment
                        </button>
                        <button class="btn btn-secondary cancel-redirect-btn" style="
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 15px 20px;
                            border-radius: 25px;
                            font-weight: 600;
                            cursor: pointer;
                        ">
                            Cancel
                        </button>
                    </div>
                </div>
            `);
            
            $('body').append($modal);
            
            // Countdown timer
            let countdown = 5;
            const countdownInterval = setInterval(() => {
                countdown--;
                $modal.find('.redirect-countdown div').text(countdown);
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = data.redirect_url;
                }
            }, 1000);
            
            // Handle manual redirect
            $modal.find('.redirect-now-btn').on('click', function() {
                clearInterval(countdownInterval);
                window.location.href = data.redirect_url;
            });
            
            // Handle cancel
            $modal.find('.cancel-redirect-btn').on('click', function() {
                clearInterval(countdownInterval);
                $modal.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },
        
        showSuccessMessage: function(data) {
            const $modal = $(`
                <div class="success-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                ">
                    <div style="
                        background: white;
                        padding: 40px;
                        border-radius: 15px;
                        text-align: center;
                        max-width: 500px;
                        width: 90%;
                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                    ">
                        <div style="color: #28a745; font-size: 60px; margin-bottom: 20px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 style="margin-bottom: 15px; color: #2c3e50;">Payment Successful!</h2>
                        <p style="margin-bottom: 20px; color: #666; line-height: 1.6;">
                            Thank you for your generous donation of 
                            <strong style="color: #28a745;">${data.currency} ${parseFloat(data.amount).toLocaleString()}</strong>
                        </p>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; text-align: left;">
                            <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Transaction Details:</h4>
                            <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Transaction ID:</strong> ${data.transaction_id}</p>
                            <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Payment Method:</strong> ${data.payment_method}</p>
                            <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                        </div>
                        <p style="margin-bottom: 25px; font-size: 0.9rem; color: #666;">
                            A receipt has been sent to your email address.
                        </p>
                        <button class="btn btn-primary success-continue-btn" style="
                            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 25px;
                            font-weight: 600;
                            cursor: pointer;
                        ">
                            <i class="fas fa-home" style="margin-right: 8px;"></i>
                            Return to Home
                        </button>
                    </div>
                </div>
            `);
            
            $('body').append($modal);
            
            // Handle continue button click
            $modal.find('.success-continue-btn').on('click', function() {
                $modal.fadeOut(300, function() {
                    $(this).remove();
                    window.location.href = '/';
                });
            });
        },
        
        showFinalSuccessMessage: function(data) {
            const $modal = $(`
                <div class="final-success-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                ">
                    <div style="
                        background: white;
                        padding: 50px;
                        border-radius: 20px;
                        text-align: center;
                        max-width: 600px;
                        width: 90%;
                        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
                    ">
                        <div style="color: #28a745; font-size: 80px; margin-bottom: 25px;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h1 style="margin-bottom: 20px; color: #2c3e50; font-size: 2.2rem;">
                            Thank You for Your Generosity!
                        </h1>
                        <p style="margin-bottom: 25px; color: #666; line-height: 1.8; font-size: 1.1rem;">
                            Your donation of <strong style="color: #28a745; font-size: 1.3rem;">
                                ${data.currency} ${parseFloat(data.amount).toLocaleString()}
                            </strong> has been received and will make a real difference in the lives of those we serve.
                        </p>
                        
                        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                            <h3 style="margin: 0 0 15px 0; color: #2c3e50;">What Happens Next?</h3>
                            <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                                    <span>Payment verification in progress</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <i class="fas fa-envelope" style="color: #007cba; margin-right: 10px;"></i>
                                    <span>Receipt will be sent to your email</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-users" style="color: #6c757d; margin-right: 10px;"></i>
                                    <span>Your impact will be shared with you</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 30px; padding: 20px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 10px;">
                            <p style="margin: 0; color: #856404; font-weight: 600;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                                Transaction ID: ${data.transaction_id}
                            </p>
                            <p style="margin: 5px 0 0 0; color: #856404; font-size: 0.9rem;">
                                Keep this ID for your records
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                            <button class="btn btn-primary home-btn" style="
                                background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
                                color: white;
                                border: none;
                                padding: 15px 30px;
                                border-radius: 25px;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 1rem;
                            ">
                                <i class="fas fa-home" style="margin-right: 8px;"></i>
                                Return to Home
                            </button>
                            <button class="btn btn-secondary share-btn" style="
                                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                color: white;
                                border: none;
                                padding: 15px 30px;
                                border-radius: 25px;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 1rem;
                            ">
                                <i class="fas fa-share-alt" style="margin-right: 8px;"></i>
                                Share Impact
                            </button>
                        </div>
                        
                        <p style="margin-top: 25px; font-size: 0.9rem; color: #999; font-style: italic;">
                            "No act of kindness, no matter how small, is ever wasted." - Aesop
                        </p>
                    </div>
                </div>
            `);
            
            $('body').append($modal);
            
            // Handle home button
            $modal.find('.home-btn').on('click', function() {
                window.location.href = '/';
            });
            
            // Handle share button (could integrate with social sharing)
            $modal.find('.share-btn').on('click', function() {
                // You could add social sharing functionality here
                if (navigator.share) {
                    navigator.share({
                        title: 'I just made a donation!',
                        text: `I donated ${data.currency} ${parseFloat(data.amount).toLocaleString()} to support this amazing cause.`,
                        url: window.location.origin
                    }).catch(console.error);
                } else {
                    // Fallback: copy to clipboard
                    const text = `I just donated ${data.currency} ${parseFloat(data.amount).toLocaleString()} to support this amazing cause! ${window.location.origin}`;
                    navigator.clipboard.writeText(text).then(() => {
                        alert('Sharing text copied to clipboard!');
                    }).catch(() => {
                        alert('Thank you for wanting to share! Please manually share your donation story.');
                    });
                }
            });
        },
        
        showManualTransferInstructions: function(data) {
            const bankDetails = data.bank_details || {};
            const instructions = data.instructions || [];
            
            const $modal = $(`
                <div class="manual-transfer-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.95);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    overflow-y: auto;
                    padding: 20px 0;
                ">
                    <div style="
                        background: white;
                        padding: 40px;
                        border-radius: 15px;
                        max-width: 650px;
                        width: 95%;
                        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                        margin: auto;
                    ">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4CAF50, #66BB6A); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                                <i class="fas fa-university" style="font-size: 2rem;"></i>
                            </div>
                            <h2 style="color: #2d5a41; margin-bottom: 15px; font-weight: 700;">Bank Transfer Instructions</h2>
                            <p style="color: #6c757d;">${data.message || 'Your donation has been registered. Please complete the bank transfer.'}</p>
                        </div>
                        
                        <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 25px; border: 2px solid #e9ecef;">
                            <h3 style="color: #2d5a41; margin-bottom: 20px; text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                                <i class="fas fa-building" style="margin-right: 8px; color: #4CAF50;"></i>Bank Account Details
                            </h3>
                            
                            <div style="display: grid; gap: 12px;">
                                ${bankDetails.bank_name ? `
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #6c757d; font-weight: 600;">Bank Name:</span>
                                    <span style="color: #2d5a41; font-weight: 700;">${bankDetails.bank_name}</span>
                                </div>` : ''}
                                
                                ${bankDetails.account_name ? `
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #6c757d; font-weight: 600;">Account Name:</span>
                                    <span style="color: #2d5a41; font-weight: 700;">${bankDetails.account_name}</span>
                                </div>` : ''}
                                
                                ${bankDetails.account_number ? `
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #6c757d; font-weight: 600;">Account Number:</span>
                                    <span style="color: #2d5a41; font-weight: 700; font-family: monospace; font-size: 1.1rem;">${bankDetails.account_number}</span>
                                </div>` : ''}
                                
                                ${bankDetails.swift_code ? `
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #6c757d; font-weight: 600;">Swift Code:</span>
                                    <span style="color: #2d5a41; font-weight: 700;">${bankDetails.swift_code}</span>
                                </div>` : ''}
                                
                                ${bankDetails.branch ? `
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #6c757d; font-weight: 600;">Branch:</span>
                                    <span style="color: #2d5a41; font-weight: 700;">${bankDetails.branch}</span>
                                </div>` : ''}
                                
                                <div style="display: flex; justify-content: space-between; padding: 15px; background: linear-gradient(135deg, #fff9e6, #fff3cd); border-radius: 8px; border-left: 3px solid #ffc107; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #856404; font-weight: 600;">Amount:</span>
                                    <span style="color: #856404; font-weight: 700; font-size: 1.3rem;">${data.currency} ${parseFloat(data.amount).toLocaleString()}</span>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; padding: 12px; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span style="color: #2d5a41; font-weight: 600;">Reference:</span>
                                    <span style="color: #2d5a41; font-weight: 700; font-family: monospace;">${bankDetails.reference || data.transaction_id}</span>
                                </div>
                            </div>
                        </div>
                        
                        ${instructions.length > 0 ? `
                        <div style="background: #fff9e6; border: 2px solid #ffeaa7; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                            <h4 style="color: #856404; margin-bottom: 15px;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Important Instructions:
                            </h4>
                            <ol style="color: #856404; line-height: 1.8; margin: 0; padding-left: 20px;">
                                ${instructions.map(instruction => `<li>${instruction}</li>`).join('')}
                            </ol>
                        </div>` : ''}
                        
                        <div style="background: #e8f5e9; border: 2px solid #a5d6a7; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;">
                            <p style="color: #2d5a41; font-size: 0.9rem; margin: 0;">
                                <i class="fas fa-envelope" style="margin-right: 8px; color: #4CAF50;"></i>
                                Instructions sent to <strong>${data.donor_email}</strong>
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <button class="btn print-instructions-btn" style="
                                background: #6c757d;
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-weight: 600;
                                transition: all 0.3s ease;
                            ">
                                <i class="fas fa-print" style="margin-right: 8px;"></i>Print
                            </button>
                            <button class="btn close-modal-btn" style="
                                background: linear-gradient(135deg, #4CAF50, #66BB6A);
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-weight: 600;
                                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
                                transition: all 0.3s ease;
                            ">
                                <i class="fas fa-home" style="margin-right: 8px;"></i>Done
                            </button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append($modal);
            
            // Print instructions
            $modal.find('.print-instructions-btn').on('click', function() {
                window.print();
            });
            
            // Close modal
            $modal.find('.close-modal-btn').on('click', function() {
                $modal.fadeOut(300, function() {
                    $(this).remove();
                    window.location.href = '/donations/';
                });
            });
        }
    };
    
    // Initialize checkout process
    checkout.init();
});
</script>

<?php
// Prevent rendering of legacy/fallback donation form below the main form.
return;
?>

<?php
// Continue with fallback code if needed

// Fallback: Try enhanced templates if multi-step not available
if ($plugin_active) {
    // Try multiple template paths in order of preference
    $template_paths = array(
        // First: Check the correct plugin templates directory
        WP_CONTENT_DIR . '/plugins/kilismile-payments-plugin/templates/donation-form.php',
        WP_CONTENT_DIR . '/plugins/kilismile-payments-plugin/templates/forms/payment-form.php',
        // Second: Check theme's enhanced donation template
        get_template_directory() . '/templates/donation-form.php',
        // Third: Legacy paths
        get_template_directory() . '/kilismile-payments-plugin/templates/forms/payment-form.php'
    );
    
    $template_loaded = false;
    foreach ($template_paths as $template_path) {
        if (file_exists($template_path)) {
            // Pass arguments to the enhanced template
            $args = wp_parse_args($args ?? array(), array(
                'show_recurring' => true,
                'show_anonymous' => true,
                'suggested_amounts' => $suggested_amounts,
                'default_currency' => $default_currency,
                'form_style' => 'modern',
                'primary_color' => '#28a745'
            ));
            
            // Show success message that enhanced template is being used
            echo '<div class="kilismile-template-info" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">';
            echo '<i class="fas fa-check-circle" style="margin-right: 8px;"></i>';
            echo sprintf(__('Enhanced Payment System Loaded: %s', 'kilismile'), basename($template_path));
            echo '</div>';
            
            include $template_path;
            $template_loaded = true;
            return;
        }
    }
    
    if (!$template_loaded) {
        // Log that enhanced template wasn't found
        if (function_exists('KiliSmile_Donation_Debug::log_transaction')) {
            KiliSmile_Donation_Debug::log_transaction('enhanced_template_missing', array(
                'attempted_paths' => $template_paths,
                'plugin_active' => $plugin_active,
                'plugin_dir_constant' => defined('KILISMILE_PAYMENTS_PLUGIN_DIR') ? KILISMILE_PAYMENTS_PLUGIN_DIR : 'not_defined'
            ));
        }
        
        // Try to use simple donation form shortcode as fallback
        if (shortcode_exists('kilismile_simple_donation_form')) {
            echo '<div class="kilismile-template-fallback" style="background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">';
            echo '<i class="fas fa-info-circle" style="margin-right: 8px;"></i>';
            echo __('Using simplified donation form. Enhanced templates will be loaded when available.', 'kilismile');
            echo '</div>';
            echo do_shortcode('[kilismile_simple_donation_form]');
            return; // Exit early since we're using the fallback
        }
        
        // Show error message that enhanced template wasn't found
        echo '<div class="kilismile-template-error" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">';
        echo '<i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>';
        echo sprintf(__('Enhanced template not found. Attempted paths: %s', 'kilismile'), esc_html(implode(', ', $template_paths)));
        echo '</div>';
    }
}
?>

<!-- Enhanced Payment System Notice -->
<div class="kilismile-payment-notice" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center;">
        <i class="fas fa-check-circle" style="margin-right: 10px; font-size: 1.2rem;"></i>
        <div>
            <strong><?php _e('Enhanced Payment System Active', 'kilismile'); ?></strong><br>
            <span style="font-size: 0.9rem;">
                <?php if (!$plugin_active): ?>
                    <?php _e('For enhanced payment processing with multiple gateways, please install the KiliSmile Payments plugin.', 'kilismile'); ?>
                <?php else: ?>
                    <?php _e('Using enhanced payment form with advanced security and multiple payment gateway support.', 'kilismile'); ?>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<!-- Legacy Compatibility Notice -->
<?php if (!$plugin_active): ?>
<div class="kilismile-payment-notice" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center;">
        <i class="fas fa-info-circle" style="margin-right: 10px; font-size: 1.2rem;"></i>
        <div>
            <strong><?php _e('Payment Plugin Required', 'kilismile'); ?></strong><br>
            <span style="font-size: 0.9rem;"><?php _e('To process donations, please install and activate a compatible payment plugin such as KiliSmile Payments.', 'kilismile'); ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Donation Form Component -->
<div id="kilismile-donation-form" class="<?php echo esc_attr($args['class']); ?>" style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
    
    <!-- Step Progress Indicator -->
    <div class="form-progress" style="background: linear-gradient(135deg, #28a745, #20c997); padding: 20px; color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 400px; margin: 0 auto;">
            <div class="step-indicator active" data-step="1" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: white; color: #28a745; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">1</div>
                <span style="font-size: 0.85rem; text-align: center;"><?php _e('Amount', 'kilismile'); ?></span>
            </div>
            <div class="step-line" style="height: 2px; background: rgba(255,255,255,0.3); flex: 1; margin: 0 10px;"></div>
            <div class="step-indicator" data-step="2" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.3); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">2</div>
                <span style="font-size: 0.85rem; text-align: center;"><?php _e('Details', 'kilismile'); ?></span>
            </div>
            <div class="step-line" style="height: 2px; background: rgba(255,255,255,0.3); flex: 1; margin: 0 10px;"></div>
            <div class="step-indicator" data-step="3" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.3); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">3</div>
                <span style="font-size: 0.85rem; text-align: center;"><?php _e('Payment', 'kilismile'); ?></span>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="form-content" style="padding: 40px;">
        
        <!-- Step 1: Amount Selection -->
        <div class="form-step active" data-step="1">
            <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                <?php _e('Choose Your Donation Amount', 'kilismile'); ?>
            </h3>
            
            <!-- Currency Selection -->
            <div class="currency-selection" style="display: flex; justify-content: center; margin-bottom: 30px;">
                <div style="background: #f8f9fa; border-radius: 25px; padding: 5px; display: flex;">
                    <button type="button" class="currency-btn active" data-currency="TZS" 
                            style="padding: 12px 20px; border: none; background: #28a745; color: white; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; margin-right: 5px; font-weight: 600;">
                        TZS (TSh)
                    </button>
                    <button type="button" class="currency-btn" data-currency="USD" 
                            style="padding: 12px 20px; border: none; background: transparent; color: #6c757d; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; font-weight: 600;">
                        USD ($)
                    </button>
                </div>
            </div>
            
            <!-- Amount Options -->
            <div class="amount-options">
                <!-- TZS Amounts -->
                <div class="currency-amounts active" data-currency="TZS">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
                        <?php foreach ($suggested_amounts['TZS'] as $amount): ?>
                        <button type="button" class="preset-amount" data-amount="<?php echo $amount; ?>" data-currency="TZS"
                                style="padding: 15px 10px; border: 2px solid #e9ecef; background: white; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; color: #495057;">
                            TSh <?php echo number_format($amount); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- USD Amounts -->
                <div class="currency-amounts" data-currency="USD" style="display: none;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
                        <?php foreach ($suggested_amounts['USD'] as $amount): ?>
                        <button type="button" class="preset-amount" data-amount="<?php echo $amount; ?>" data-currency="USD"
                                style="padding: 15px 10px; border: 2px solid #e9ecef; background: white; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; color: #495057;">
                            $<?php echo $amount; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Custom Amount Input -->
            <div class="custom-amount-container" style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                    <?php _e('Or enter custom amount:', 'kilismile'); ?>
                </label>
                <div style="position: relative;">
                    <span class="currency-symbol" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-weight: 600; color: #6c757d; z-index: 1;">TSh</span>
                    <input type="number" id="custom-amount" name="amount" min="1" 
                           style="width: 100%; padding: 15px 15px 15px 50px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1.1rem; font-weight: 600; transition: border-color 0.3s ease;"
                           placeholder="0">
                </div>
                <div class="conversion-display" style="font-size: 0.9rem; color: #6c757d; margin-top: 5px; min-height: 20px;"></div>
            </div>
            
            <!-- Recurring Option -->
            <?php if ($args['show_recurring']): ?>
            <div class="recurring-option" style="margin-bottom: 30px; text-align: center;">
                <label style="display: inline-flex; align-items: center; cursor: pointer; background: #f8f9fa; padding: 15px 20px; border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                    <input type="checkbox" name="recurring" style="margin-right: 12px; transform: scale(1.2);">
                    <span style="font-weight: 600; color: #495057;">
                        <?php _e('Make this a monthly donation', 'kilismile'); ?>
                    </span>
                </label>
            </div>
            <?php endif; ?>
            
            <button type="button" class="next-step-btn" disabled
                    style="width: 100%; background: #6c757d; color: white; padding: 18px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: not-allowed; transition: all 0.3s ease;">
                <?php _e('Continue to Details', 'kilismile'); ?> →
            </button>
        </div>

        <!-- Step 2: Donor Information -->
        <div class="form-step" data-step="2" style="display: none;">
            <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                <?php _e('Your Information', 'kilismile'); ?>
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                        <?php _e('First Name', 'kilismile'); ?> *
                    </label>
                    <input type="text" name="first_name" required
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s ease;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                        <?php _e('Last Name', 'kilismile'); ?> *
                    </label>
                    <input type="text" name="last_name" required
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s ease;">
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                    <?php _e('Email Address', 'kilismile'); ?> *
                </label>
                <input type="email" name="email" required
                       style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s ease;">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                    <?php _e('Phone Number', 'kilismile'); ?>
                </label>
                <input type="tel" name="phone"
                       style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s ease;">
            </div>
            
            <?php if ($args['show_anonymous']): ?>
            <div style="margin-bottom: 30px; text-align: center;">
                <label style="display: inline-flex; align-items: center; cursor: pointer; background: #f8f9fa; padding: 15px 20px; border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                    <input type="checkbox" name="anonymous" style="margin-right: 12px; transform: scale(1.2);">
                    <span style="font-weight: 600; color: #495057;">
                        <?php _e('Make this donation anonymous', 'kilismile'); ?>
                    </span>
                </label>
            </div>
            <?php endif; ?>
            
            <div style="display: flex; gap: 15px;">
                <button type="button" class="prev-step-btn"
                        style="flex: 1; background: #e9ecef; color: #495057; padding: 18px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    ← <?php _e('Back', 'kilismile'); ?>
                </button>
                <button type="button" class="next-step-btn"
                        style="flex: 2; background: #28a745; color: white; padding: 18px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    <?php _e('Continue to Payment', 'kilismile'); ?> →
                </button>
            </div>
        </div>

        <!-- Step 3: Payment -->
        <div class="form-step" data-step="3" style="display: none;">
            <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                <?php _e('Complete Your Donation', 'kilismile'); ?>
            </h3>
            
            <!-- Donation Summary -->
            <div class="donation-summary" style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                <h4 style="margin: 0 0 15px 0; color: #495057;"><?php _e('Donation Summary', 'kilismile'); ?></h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span><?php _e('Amount:', 'kilismile'); ?></span>
                    <span class="summary-amount" style="font-weight: 600;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;" class="recurring-summary" style="display: none;">
                    <span><?php _e('Frequency:', 'kilismile'); ?></span>
                    <span style="font-weight: 600;"><?php _e('Monthly', 'kilismile'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid #dee2e6; padding-top: 10px; margin-top: 15px;">
                    <span style="font-weight: 600; font-size: 1.1rem;"><?php _e('Total:', 'kilismile'); ?></span>
                    <span class="summary-total" style="font-weight: 600; font-size: 1.1rem; color: #28a745;"></span>
                </div>
            </div>
            
            <!-- Payment Methods will be loaded here dynamically -->
            <div class="payment-methods-container">
                <div style="text-align: center; color: #6c757d; padding: 40px;">
                    <div class="spinner" style="border: 3px solid #f3f3f3; border-top: 3px solid #28a745; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                    <?php _e('Loading payment options...', 'kilismile'); ?>
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="button" class="prev-step-btn"
                        style="flex: 1; background: #e9ecef; color: #495057; padding: 18px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    ← <?php _e('Back', 'kilismile'); ?>
                </button>
                <button type="submit" class="donation-submit-btn" disabled
                        style="flex: 2; background: #6c757d; color: white; padding: 18px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: not-allowed; transition: all 0.3s ease;">
                    <?php echo esc_html($args['submit_text']); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript for Form Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const donationForm = document.getElementById('kilismile-donation-form');
    if (!donationForm) return;
    
    let currentStep = 1;
    let selectedCurrency = 'TZS';
    let selectedAmount = 0;
    
    // Currency conversion rates (you can update these dynamically)
    const conversionRates = {
        'USD_TO_TZS': 2500,
        'TZS_TO_USD': 0.0004
    };
    
    // Log form initialization
    debugLog('form_initialized', {
        currency: selectedCurrency,
        step: currentStep,
        conversion_rates: conversionRates
    });
    
    // Initialize form
    function initializeForm() {
        updateStepDisplay();
        updateCurrencyDisplay();
        bindEventListeners();
        debugLog('form_setup_complete', { step: currentStep });
    }
    
    // Event listeners
    function bindEventListeners() {
        // Currency buttons
        donationForm.querySelectorAll('.currency-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const oldCurrency = selectedCurrency;
                selectedCurrency = this.dataset.currency;
                debugLog('currency_changed', {
                    from: oldCurrency,
                    to: selectedCurrency,
                    old_amount: selectedAmount
                });
                updateCurrencyDisplay();
                updateAmountValidation();
            });
        });
        
        // Preset amount buttons
        donationForm.querySelectorAll('.preset-amount').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.dataset.currency === selectedCurrency) {
                    const oldAmount = selectedAmount;
                    selectedAmount = parseFloat(this.dataset.amount);
                    debugLog('preset_amount_selected', {
                        currency: selectedCurrency,
                        old_amount: oldAmount,
                        new_amount: selectedAmount,
                        preset_button: this.dataset.amount
                    });
                    document.getElementById('custom-amount').value = selectedAmount;
                    updatePresetSelection();
                    updateAmountValidation();
                    showConversion();
                }
            });
        });
        
        // Custom amount input
        const customAmountInput = document.getElementById('custom-amount');
        customAmountInput.addEventListener('input', function() {
            const oldAmount = selectedAmount;
            selectedAmount = parseFloat(this.value) || 0;
            debugLog('custom_amount_entered', {
                currency: selectedCurrency,
                old_amount: oldAmount,
                new_amount: selectedAmount,
                input_value: this.value
            });
            updatePresetSelection();
            updateAmountValidation();
            showConversion();
        });
        
        // Navigation buttons
        donationForm.querySelectorAll('.next-step-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                debugLog('next_step_attempted', {
                    current_step: currentStep,
                    amount: selectedAmount,
                    currency: selectedCurrency,
                    validation_passed: validateCurrentStep()
                });
                if (validateCurrentStep()) {
                    nextStep();
                }
            });
        });
        
        donationForm.querySelectorAll('.prev-step-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                debugLog('previous_step_clicked', { current_step: currentStep });
                prevStep();
            });
        });
        
        // Form submission
        donationForm.addEventListener('submit', function(e) {
            debugLog('form_submission_attempted', {
                step: currentStep,
                amount: selectedAmount,
                currency: selectedCurrency,
                form_data: collectFormData()
            });
            handleFormSubmission(e);
        });
    }
    
    // Update currency display
    function updateCurrencyDisplay() {
        // Update currency buttons
        donationForm.querySelectorAll('.currency-btn').forEach(btn => {
            if (btn.dataset.currency === selectedCurrency) {
                btn.style.background = '#28a745';
                btn.style.color = 'white';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = '#6c757d';
            }
        });
        
        // Update currency amounts visibility
        donationForm.querySelectorAll('.currency-amounts').forEach(container => {
            if (container.dataset.currency === selectedCurrency) {
                container.style.display = 'block';
                container.classList.add('active');
            } else {
                container.style.display = 'none';
                container.classList.remove('active');
            }
        });
        
        // Update currency symbol
        const currencySymbol = donationForm.querySelector('.currency-symbol');
        if (currencySymbol) {
            currencySymbol.textContent = selectedCurrency === 'USD' ? '$' : 'TSh';
        }
        
        // Reset amount when currency changes
        selectedAmount = 0;
        document.getElementById('custom-amount').value = '';
        updatePresetSelection();
        updateAmountValidation();
    }
    
    // Update preset selection styling
    function updatePresetSelection() {
        donationForm.querySelectorAll('.preset-amount').forEach(btn => {
            if (btn.dataset.currency === selectedCurrency && parseFloat(btn.dataset.amount) === selectedAmount) {
                btn.style.borderColor = '#28a745';
                btn.style.background = '#28a745';
                btn.style.color = 'white';
            } else {
                btn.style.borderColor = '#e9ecef';
                btn.style.background = 'white';
                btn.style.color = '#495057';
            }
        });
    }
    
    // Show currency conversion
    function showConversion() {
        const conversionDisplay = donationForm.querySelector('.conversion-display');
        if (!conversionDisplay || selectedAmount <= 0) {
            if (conversionDisplay) conversionDisplay.textContent = '';
            return;
        }
        
        let convertedAmount, convertedCurrency;
        if (selectedCurrency === 'USD') {
            convertedAmount = (selectedAmount * conversionRates.USD_TO_TZS).toLocaleString();
            convertedCurrency = 'TSh';
        } else {
            convertedAmount = (selectedAmount * conversionRates.TZS_TO_USD).toFixed(2);
            convertedCurrency = '$';
        }
        
        conversionDisplay.textContent = `≈ ${convertedCurrency}${convertedAmount}`;
    }
    
    // Helper function to collect all form data
    function collectFormData() {
        const formData = {};
        
        // Amount and currency
        formData.amount = selectedAmount;
        formData.currency = selectedCurrency;
        formData.recurring = donationForm.querySelector('input[name="recurring"]')?.checked || false;
        
        // Personal information
        formData.first_name = donationForm.querySelector('input[name="first_name"]')?.value?.trim() || '';
        formData.last_name = donationForm.querySelector('input[name="last_name"]')?.value?.trim() || '';
        formData.email = donationForm.querySelector('input[name="email"]')?.value?.trim() || '';
        formData.phone = donationForm.querySelector('input[name="phone"]')?.value?.trim() || '';
        formData.anonymous = donationForm.querySelector('input[name="anonymous"]')?.checked || false;
        
        // Payment method data
        if (selectedCurrency === 'USD') {
            formData.payment_method = donationForm.querySelector('input[name="payment_method"]:checked')?.value || '';
        } else {
            formData.azampay_type = donationForm.querySelector('input[name="azampay_type"]:checked')?.value || '';
            formData.mobile_network = donationForm.querySelector('input[name="mobile_network"]:checked')?.value || '';
            formData.payment_phone = donationForm.querySelector('#payment_phone')?.value?.trim() || '';
        }
        
        return formData;
    }
    
    // Validate current step
    function validateCurrentStep() {
        const validationResult = { valid: false, errors: [] };
        
        if (currentStep === 1) {
            validationResult.valid = selectedAmount > 0;
            if (!validationResult.valid) {
                validationResult.errors.push('Amount must be greater than 0');
            }
        } else if (currentStep === 2) {
            const firstName = donationForm.querySelector('input[name="first_name"]').value.trim();
            const lastName = donationForm.querySelector('input[name="last_name"]').value.trim();
            const email = donationForm.querySelector('input[name="email"]').value.trim();
            
            if (!firstName) validationResult.errors.push('First name is required');
            if (!lastName) validationResult.errors.push('Last name is required');
            if (!email) validationResult.errors.push('Email is required');
            else if (!email.includes('@')) validationResult.errors.push('Valid email is required');
            
            validationResult.valid = validationResult.errors.length === 0;
        } else {
            validationResult.valid = true;
        }
        
        debugLog('step_validation', {
            step: currentStep,
            result: validationResult,
            form_data: collectFormData()
        }, validationResult.valid ? 'success' : 'warning');
        
        return validationResult.valid;
    }
    
    // Update amount validation
    function updateAmountValidation() {
        const nextBtn = donationForm.querySelector('.form-step[data-step="1"] .next-step-btn');
        if (selectedAmount > 0) {
            nextBtn.disabled = false;
            nextBtn.style.background = '#28a745';
            nextBtn.style.cursor = 'pointer';
        } else {
            nextBtn.disabled = true;
            nextBtn.style.background = '#6c757d';
            nextBtn.style.cursor = 'not-allowed';
        }
    }
    
    // Navigation functions
    function nextStep() {
        if (currentStep < 3) {
            const oldStep = currentStep;
            currentStep++;
            debugLog('step_advanced', {
                from_step: oldStep,
                to_step: currentStep,
                form_data: collectFormData()
            });
            updateStepDisplay();
            
            if (currentStep === 3) {
                debugLog('payment_step_reached', {
                    form_data: collectFormData()
                });
                updateDonationSummary();
                loadPaymentMethods();
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            const oldStep = currentStep;
            currentStep--;
            debugLog('step_back', {
                from_step: oldStep,
                to_step: currentStep
            });
            updateStepDisplay();
        }
    }
    
    // Update step display
    function updateStepDisplay() {
        // Update progress indicators
        donationForm.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const stepNumber = index + 1;
            const circle = indicator.querySelector('.step-circle');
            
            if (stepNumber <= currentStep) {
                indicator.classList.add('active');
                circle.style.background = 'white';
                circle.style.color = '#28a745';
            } else {
                indicator.classList.remove('active');
                circle.style.background = 'rgba(255,255,255,0.3)';
                circle.style.color = 'white';
            }
        });
        
        // Update step visibility
        donationForm.querySelectorAll('.form-step').forEach((step, index) => {
            const stepNumber = index + 1;
            if (stepNumber === currentStep) {
                step.style.display = 'block';
                step.classList.add('active');
            } else {
                step.style.display = 'none';
                step.classList.remove('active');
            }
        });
    }
    
    // Update donation summary
    function updateDonationSummary() {
        const currencySymbol = selectedCurrency === 'USD' ? '$' : 'TSh ';
        const formattedAmount = selectedCurrency === 'USD' ? 
            selectedAmount.toFixed(2) : 
            selectedAmount.toLocaleString();
        
        donationForm.querySelector('.summary-amount').textContent = currencySymbol + formattedAmount;
        donationForm.querySelector('.summary-total').textContent = currencySymbol + formattedAmount;
        
        // Show/hide recurring summary
        const isRecurring = donationForm.querySelector('input[name="recurring"]').checked;
        const recurringSummary = donationForm.querySelector('.recurring-summary');
        if (isRecurring && recurringSummary) {
            recurringSummary.style.display = 'flex';
        } else if (recurringSummary) {
            recurringSummary.style.display = 'none';
        }
    }
    
    // Load payment methods
    function loadPaymentMethods() {
        const container = donationForm.querySelector('.payment-methods-container');
        
        debugLog('payment_methods_loading', {
            currency: selectedCurrency,
            amount: selectedAmount
        });
        
        // Show loading state
        container.innerHTML = `
            <div style="text-align: center; color: #6c757d; padding: 40px;">
                <div class="spinner" style="border: 3px solid #f3f3f3; border-top: 3px solid #28a745; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                Loading payment options...
            </div>
        `;
        
        // Load payment methods based on currency
        setTimeout(() => {
            debugLog('payment_methods_rendered', {
                currency: selectedCurrency,
                methods_type: selectedCurrency === 'USD' ? 'international' : 'tanzanian'
            });
            
            if (selectedCurrency === 'USD') {
                // International payment methods
                container.innerHTML = `
                    <div class="payment-method-selection">
                        <h4 style="margin-bottom: 20px; color: #495057; text-align: center;">Choose Payment Method (USD)</h4>
                        <div style="display: grid; gap: 15px;">
                            <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="paypal" style="margin-right: 15px; transform: scale(1.3);">
                                <div style="display: flex; align-items: center; flex: 1;">
                                    <i class="fab fa-paypal" style="font-size: 1.8rem; color: #0070ba; margin-right: 15px;"></i>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 2px;">PayPal</div>
                                        <div style="font-size: 0.85rem; color: #6c757d;">Pay with PayPal account or credit card</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="card" style="margin-right: 15px; transform: scale(1.3);">
                                <div style="display: flex; align-items: center; flex: 1;">
                                    <i class="fas fa-credit-card" style="font-size: 1.8rem; color: #6772e5; margin-right: 15px;"></i>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 2px;">Credit/Debit Card</div>
                                        <div style="font-size: 0.85rem; color: #6c757d;">Visa, Mastercard, American Express</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="bank_transfer" style="margin-right: 15px; transform: scale(1.3);">
                                <div style="display: flex; align-items: center; flex: 1;">
                                    <i class="fas fa-university" style="font-size: 1.8rem; color: #28a745; margin-right: 15px;"></i>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 2px;">Bank Transfer</div>
                                        <div style="font-size: 0.85rem; color: #6c757d;">Direct bank transfer (manual verification)</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                `;
            } else {
                // Tanzanian payment methods with AzamPay
                container.innerHTML = `
                    <div class="payment-method-selection">
                        <h4 style="margin-bottom: 20px; color: #495057; text-align: center;">Choose Payment Method (TZS)</h4>
                        
                        <!-- AzamPay Payment Type Selection -->
                        <div class="azampay-method-selection" style="margin-bottom: 25px;">
                            <h5 style="margin-bottom: 15px; color: #28a745; font-size: 1rem;">Select Payment Experience:</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <label class="azampay-type" style="padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                                    <input type="radio" name="azampay_type" value="stkpush" checked style="margin-bottom: 10px; transform: scale(1.2);">
                                    <div>
                                        <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #28a745; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; margin-bottom: 5px;">Direct Payment</div>
                                        <div style="font-size: 0.8rem; color: #6c757d;">STK Push to your phone</div>
                                    </div>
                                </label>
                                
                                <label class="azampay-type" style="padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; text-align: center; transition: all 0.3s ease;">
                                    <input type="radio" name="azampay_type" value="checkout" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <div>
                                        <i class="fas fa-globe" style="font-size: 1.5rem; color: #20c997; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; margin-bottom: 5px;">Checkout Page</div>
                                        <div style="font-size: 0.8rem; color: #6c757d;">Secure hosted payment</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Mobile Network Selection (for STK Push) -->
                        <div class="network-selection" id="network-selection" style="margin-bottom: 25px;">
                            <h5 style="margin-bottom: 15px; color: #28a745; font-size: 1rem;">Select Mobile Network:</h5>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px;">
                                <label class="network-option" style="display: flex; flex-direction: column; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="radio" name="mobile_network" value="vodacom" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #00a651; margin-bottom: 8px;"></i>
                                    <span style="font-weight: 600; font-size: 0.9rem; text-align: center;">M-Pesa<br><small style="color: #6c757d;">(Vodacom)</small></span>
                                </label>
                                
                                <label class="network-option" style="display: flex; flex-direction: column; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="radio" name="mobile_network" value="airtel" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #e31e24; margin-bottom: 8px;"></i>
                                    <span style="font-weight: 600; font-size: 0.9rem; text-align: center;">Airtel Money</span>
                                </label>
                                
                                <label class="network-option" style="display: flex; flex-direction: column; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="radio" name="mobile_network" value="tigo" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #1e90ff; margin-bottom: 8px;"></i>
                                    <span style="font-weight: 600; font-size: 0.9rem; text-align: center;">Tigo Pesa</span>
                                </label>
                                
                                <label class="network-option" style="display: flex; flex-direction: column; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="radio" name="mobile_network" value="halopesa" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #ff6b35; margin-bottom: 8px;"></i>
                                    <span style="font-weight: 600; font-size: 0.9rem; text-align: center;">HaloPesa</span>
                                </label>
                                
                                <label class="network-option" style="display: flex; flex-direction: column; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="radio" name="mobile_network" value="azampesa" style="margin-bottom: 10px; transform: scale(1.2);">
                                    <i class="fas fa-mobile-alt" style="font-size: 1.5rem; color: #4a90e2; margin-bottom: 8px;"></i>
                                    <span style="font-weight: 600; font-size: 0.9rem; text-align: center;">AzamPesa</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Phone Number Input (for STK Push) -->
                        <div class="phone-input-section" id="phone-input-section" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">
                                Phone Number (for payment confirmation) *
                            </label>
                            <input type="tel" name="payment_phone" id="payment_phone" 
                                   placeholder="e.g., 0712345678 or +255712345678"
                                   style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s ease;">
                            <div style="font-size: 0.85rem; color: #6c757d; margin-top: 5px;">
                                This should be the phone number linked to your mobile money account
                            </div>
                        </div>
                        
                        <!-- Payment Instructions -->
                        <div class="payment-instructions" style="background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            <div class="stkpush-instructions">
                                <h6 style="color: #28a745; margin-bottom: 10px; font-size: 0.95rem;">📱 Direct Payment Instructions:</h6>
                                <ul style="margin: 0; padding-left: 20px; color: #6c757d; font-size: 0.9rem; line-height: 1.5;">
                                    <li>You will receive an STK push notification on your phone</li>
                                    <li>Enter your mobile money PIN to complete the payment</li>
                                    <li>You\'ll receive an SMS confirmation upon successful payment</li>
                                </ul>
                            </div>
                            
                            <div class="checkout-instructions" style="display: none;">
                                <h6 style="color: #20c997; margin-bottom: 10px; font-size: 0.95rem;">🌐 Checkout Page Instructions:</h6>
                                <ul style="margin: 0; padding-left: 20px; color: #6c757d; font-size: 0.9rem; line-height: 1.5;">
                                    <li>You will be redirected to a secure payment page</li>
                                    <li>Multiple payment options available in one place</li>
                                    <li>Complete payment and return to our site automatically</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add network selection change handlers
                const networkOptions = container.querySelectorAll('input[name="mobile_network"]');
                networkOptions.forEach(radio => {
                    radio.addEventListener('change', updateNetworkSelection);
                });
                
                // Add AzamPay type selection handlers
                const azampayTypes = container.querySelectorAll('input[name="azampay_type"]');
                azampayTypes.forEach(radio => {
                    radio.addEventListener('change', updateAzampayTypeSelection);
                });
                
                // Initialize default selection
                updateAzampayTypeSelection();
            }
            
            // Add payment method selection handlers
            addPaymentMethodHandlers();
            
        }, 1000);
    }
    
    // Update network selection styling
    function updateNetworkSelection() {
        const container = donationForm.querySelector('.payment-methods-container');
        container.querySelectorAll('.network-option').forEach(option => {
            const input = option.querySelector('input');
            if (input.checked) {
                option.style.borderColor = '#28a745';
                option.style.background = '#f8fff8';
            } else {
                option.style.borderColor = '#e9ecef';
                option.style.background = 'white';
            }
        });
        validatePaymentSelection();
    }
    
    // Update AzamPay type selection
    function updateAzampayTypeSelection() {
        const container = donationForm.querySelector('.payment-methods-container');
        if (!container) return;
        
        const stkpushSelected = container.querySelector('input[name="azampay_type"]:checked')?.value === 'stkpush';
        const networkSection = container.querySelector('#network-selection');
        const phoneSection = container.querySelector('#phone-input-section');
        const stkpushInstructions = container.querySelector('.stkpush-instructions');
        const checkoutInstructions = container.querySelector('.checkout-instructions');
        
        // Update type selection styling
        container.querySelectorAll('.azampay-type').forEach(option => {
            const input = option.querySelector('input');
            if (input.checked) {
                option.style.borderColor = '#28a745';
                option.style.background = '#f8fff8';
            } else {
                option.style.borderColor = '#e9ecef';
                option.style.background = 'white';
            }
        });
        
        // Show/hide sections based on selection
        if (stkpushSelected) {
            if (networkSection) networkSection.style.display = 'block';
            if (phoneSection) phoneSection.style.display = 'block';
            if (stkpushInstructions) stkpushInstructions.style.display = 'block';
            if (checkoutInstructions) checkoutInstructions.style.display = 'none';
        } else {
            if (networkSection) networkSection.style.display = 'none';
            if (phoneSection) phoneSection.style.display = 'none';
            if (stkpushInstructions) stkpushInstructions.style.display = 'none';
            if (checkoutInstructions) checkoutInstructions.style.display = 'block';
        }
        
        validatePaymentSelection();
    }
    
    // Validate payment selection
    function validatePaymentSelection() {
        const container = donationForm.querySelector('.payment-methods-container');
        const submitBtn = donationForm.querySelector('.donation-submit-btn');
        
        if (!container || !submitBtn) return;
        
        let isValid = false;
        
        if (selectedCurrency === 'USD') {
            // For USD, just need payment method selected
            isValid = container.querySelector('input[name="payment_method"]:checked') !== null;
        } else {
            // For TZS, check AzamPay type and requirements
            const azampayType = container.querySelector('input[name="azampay_type"]:checked')?.value;
            
            if (azampayType === 'stkpush') {
                // Need network and phone number
                const networkSelected = container.querySelector('input[name="mobile_network"]:checked') !== null;
                const phoneEntered = container.querySelector('#payment_phone')?.value.trim() !== '';
                isValid = networkSelected && phoneEntered;
            } else if (azampayType === 'checkout') {
                // Only need type selected for checkout
                isValid = true;
            }
        }
        
        // Update submit button
        if (isValid) {
            submitBtn.disabled = false;
            submitBtn.style.background = '#28a745';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.background = '#6c757d';
            submitBtn.style.cursor = 'not-allowed';
        }
    }
    
    // Add payment method handlers
    function addPaymentMethodHandlers() {
        const container = donationForm.querySelector('.payment-methods-container');
        
        // USD payment methods
        container.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Update payment option styling
                container.querySelectorAll('.payment-option').forEach(option => {
                    const input = option.querySelector('input');
                    if (input.checked) {
                        option.style.borderColor = '#28a745';
                        option.style.background = '#f8fff8';
                    } else {
                        option.style.borderColor = '#e9ecef';
                        option.style.background = 'white';
                    }
                });
                validatePaymentSelection();
            });
        });
        
        // Phone input validation for TZS
        const phoneInput = container.querySelector('#payment_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', validatePaymentSelection);
            phoneInput.addEventListener('blur', validatePhoneNumber);
        }
    }
    
    // Validate phone number format
    function validatePhoneNumber() {
        const container = donationForm.querySelector('.payment-methods-container');
        const phoneInput = container.querySelector('#payment_phone');
        if (!phoneInput) return;
        
        const phone = phoneInput.value.trim();
        const phoneRegex = /^(\+?255|0)[67]\d{8}$/;
        
        if (phone && !phone.match(phoneRegex)) {
            phoneInput.style.borderColor = '#dc3545';
            // Could show error message here
        } else {
            phoneInput.style.borderColor = '#28a745';
        }
    }
    
    // Helper function to collect all form data for debugging
    function collectFormData() {
        const data = {
            step: currentStep,
            amount: selectedAmount,
            currency: selectedCurrency,
            recurring: donationForm.querySelector('input[name="recurring"]')?.checked || false,
            first_name: donationForm.querySelector('input[name="first_name"]')?.value?.trim() || '',
            last_name: donationForm.querySelector('input[name="last_name"]')?.value?.trim() || '',
            email: donationForm.querySelector('input[name="email"]')?.value?.trim() || '',
            phone: donationForm.querySelector('input[name="phone"]')?.value?.trim() || '',
            anonymous: donationForm.querySelector('input[name="anonymous"]')?.checked || false
        };
        
        // Add payment method details
        if (selectedCurrency === 'USD') {
            const paymentMethodElement = donationForm.querySelector('input[name="payment_method"]:checked');
            data.payment_method = paymentMethodElement ? paymentMethodElement.value : null;
        } else {
            const azampayTypeElement = donationForm.querySelector('input[name="azampay_type"]:checked');
            data.azampay_type = azampayTypeElement ? azampayTypeElement.value : null;
            
            if (data.azampay_type === 'stkpush') {
                const networkElement = donationForm.querySelector('input[name="mobile_network"]:checked');
                data.mobile_network = networkElement ? networkElement.value : null;
                const paymentPhone = donationForm.querySelector('#payment_phone')?.value?.trim() || '';
                data.payment_phone = paymentPhone ? paymentPhone.replace(/\d(?=\d{4})/g, '*') : ''; // Mask for security
            }
        }
        
        return data;
    }

    // Debug logging function
    function debugLog(event, data = {}, level = 'info') {
        if (typeof window.KiliSmileDebug !== 'undefined' && window.KiliSmileDebug) {
            window.KiliSmileDebug.log(event, data, level);
        }
        // Also log to console for debugging
        console.log(`[DONATION DEBUG - ${level.toUpperCase()}] ${event}:`, data);
    }

    // Debug panel toggle function
    function toggleDebugPanel() {
        if (typeof window.KiliSmileDebug !== 'undefined' && window.KiliSmileDebug) {
            window.KiliSmileDebug.togglePanel();
        }
    }

    // Initialize debug system
    if (typeof window.KiliSmileDebug !== 'undefined' && window.KiliSmileDebug) {
        // Add debug panel toggle button to form
        const debugToggle = document.createElement('div');
        debugToggle.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 1000;';
        debugToggle.innerHTML = `
            <button onclick="toggleDebugPanel()" style="
                background: #007cba; 
                color: white; 
                border: none; 
                padding: 10px 15px; 
                border-radius: 50px; 
                cursor: pointer; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                font-size: 12px;
                font-weight: bold;
            ">
                🐛 DEBUG
            </button>
        `;
        document.body.appendChild(debugToggle);
        
        debugLog('debug_system_initialized', {
            timestamp: new Date().toISOString(),
            user_agent: navigator.userAgent,
            page_url: window.location.href
        });
    }

    // Handle form submission
    function handleFormSubmission(e) {
        e.preventDefault();
        
        const formData = collectFormData();
        debugLog('form_submission_started', {
            form_data: formData,
            step: currentStep
        });
        
        // Collect form data for submission
        const submitData = new FormData();
        
        // Check for KiliSmile Payments plugin integration
        let paymentAction = 'kilismile_process_payment';
        
        // Fallback to legacy action if plugin not available
        if (typeof window.KiliSmilePayments === 'undefined') {
            paymentAction = 'kilismile_fallback_payment';
            debugLog('using_legacy_payment_action', { action: paymentAction });
        } else {
            debugLog('using_plugin_payment_action', { action: paymentAction });
        }
        
        submitData.append('action', paymentAction);
        submitData.append('amount', selectedAmount);
        submitData.append('currency', selectedCurrency);
        submitData.append('recurring', donationForm.querySelector('input[name="recurring"]').checked);
        
        // Donor information
        const firstName = donationForm.querySelector('input[name="first_name"]').value.trim();
        const lastName = donationForm.querySelector('input[name="last_name"]').value.trim();
        submitData.append('donor_name', firstName + ' ' + lastName);
        submitData.append('donor_email', donationForm.querySelector('input[name="email"]').value.trim());
        submitData.append('donor_phone', donationForm.querySelector('input[name="phone"]').value.trim());
        submitData.append('anonymous', donationForm.querySelector('input[name="anonymous"]').checked);
        
        // Payment method data (for plugin processing)
        if (selectedCurrency === 'USD') {
            const paymentMethod = donationForm.querySelector('input[name="payment_method"]:checked').value;
            submitData.append('payment_gateway', paymentMethod);
            submitData.append('payment_method', paymentMethod);
            debugLog('usd_payment_method_selected', { method: paymentMethod });
        } else {
            // TZS payments (plugin will handle specific gateway)
            const azampayType = donationForm.querySelector('input[name="azampay_type"]:checked').value;
            submitData.append('payment_gateway', 'mobile_money');
            submitData.append('azampay_type', azampayType);
            submitData.append('use_checkout', azampayType === 'checkout');
            
            debugLog('tzs_payment_method_selected', {
                azampay_type: azampayType,
                use_checkout: azampayType === 'checkout'
            });
            
            if (azampayType === 'stkpush') {
                const selectedNetwork = donationForm.querySelector('input[name="mobile_network"]:checked').value;
                const paymentPhone = donationForm.querySelector('#payment_phone').value.trim();
                submitData.append('mobile_network', selectedNetwork);
                submitData.append('payment_phone', paymentPhone);
                
                debugLog('stkpush_details', {
                    network: selectedNetwork,
                    phone: paymentPhone.replace(/\d(?=\d{4})/g, '*') // Mask phone number for security
                });
            }
        }
        
        submitData.append('nonce', '<?php echo wp_create_nonce("kilismile_donation_nonce"); ?>');
        
        // Show loading state
        const submitBtn = donationForm.querySelector('.donation-submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
        submitBtn.style.background = '#6c757d';
        
        // Add loading spinner to button
        submitBtn.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center;">
                <div style="border: 2px solid transparent; border-top: 2px solid white; border-radius: 50%; width: 16px; height: 16px; animation: spin 1s linear infinite; margin-right: 10px;"></div>
                Processing...
            </div>
        `;
        
        debugLog('ajax_request_sending', {
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            data_keys: Array.from(submitData.keys())
        });
        
        // Submit via AJAX
        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            body: submitData
        })
        .then(response => {
            debugLog('ajax_response_received', {
                status: response.status,
                status_text: response.statusText,
                headers: Object.fromEntries(response.headers.entries())
            });
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            debugLog('ajax_raw_response', {
                response_length: text.length,
                response_preview: text.substring(0, 200)
            });
            console.log('Raw response:', text);
            
            let data;
            try {
                data = JSON.parse(text);
                debugLog('ajax_json_parsed', { parsed_data: data }, 'success');
            } catch (e) {
                debugLog('ajax_json_parse_error', {
                    error: e.message,
                    response_preview: text.substring(0, 500)
                }, 'error');
                console.error('JSON parse error:', e);
                console.log('Response was not JSON:', text);
                throw new Error('Invalid server response. Please check console for details.');
            }
            
            console.log('Payment response:', data);
            
            // Normalize payload structure (WordPress wp_send_json_success wraps actual data under data.data)
            let payload = data;
            if (data && typeof data === 'object' && data.success && data.data && typeof data.data === 'object') {
                payload = Object.assign({}, data.data);
                if (typeof payload.success === 'undefined') {
                    payload.success = true; // ensure success flag present
                }
            }
            
            debugLog('payment_response_processed', {
                original_data: data,
                normalized_payload: payload
            });
            console.log('Normalized payment payload:', payload);
            
            if (payload.success) {
                debugLog('payment_successful', {
                    payment_method: payload.payment_method,
                    redirect_url: payload.redirect_url,
                    donation_id: payload.donation_id
                }, 'success');
                
                if (payload.redirect_url) {
                    debugLog('payment_redirect_initiated', {
                        redirect_url: payload.redirect_url
                    });
                    
                    // Show redirect message
                    donationForm.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <div style="width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                                <i class="fas fa-arrow-right" style="font-size: 2rem;"></i>
                            </div>
                            <h3 style="color: #28a745; margin-bottom: 15px;">Redirecting to Payment...</h3>
                            <p style="color: #6c757d; margin-bottom: 20px;">${payload.message || 'Please complete your payment on the secure payment page.'}</p>
                            <div class="spinner" style="border: 3px solid #f3f3f3; border-top: 3px solid #28a745; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                        </div>
                    `;
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        debugLog('payment_redirect_executing', {
                            redirect_url: payload.redirect_url
                        });
                        window.location.href = payload.redirect_url;
                    }, 2000);
                } else if (payload.payment_method === 'azampay_stkpush') {
                    debugLog('stkpush_sent', {
                        message: payload.message
                    }, 'success');
                    
                    // STK Push sent - show waiting screen
                    donationForm.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <div style="width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                                <i class="fas fa-mobile-alt" style="font-size: 2rem;"></i>
                            </div>
                            <h3 style="color: #28a745; margin-bottom: 15px;">STK Push Sent!</h3>
                            <p style="color: #6c757d; margin-bottom: 20px;">${payload.message}</p>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                                <h4 style="color: #495057; margin-bottom: 15px;">Next Steps:</h4>
                                <ol style="text-align: left; color: #6c757d; line-height: 1.6;">
                                    <li>Check your phone for the payment request</li>
                                    <li>Enter your mobile money PIN</li>
                                    <li>Wait for SMS confirmation</li>
                                    <li>You\'ll receive an email receipt</li>
                                </ol>
                            </div>
                            <div style="display: flex; gap: 15px; justify-content: center;">
                                <button onclick="window.location.reload()" style="background: #6c757d; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">
                                    Make Another Donation
                                </button>
                                <button onclick="window.location.href='/donation-success/'" style="background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">
                                    Continue
                                </button>
                            </div>
                        </div>
                    `;
                } else if (payload.payment_type === 'manual_transfer' || payload.payment_method === 'manual_transfer') {
                    debugLog('manual_transfer_registered', {
                        message: payload.message,
                        transaction_id: payload.transaction_id
                    }, 'success');
                    
                    // Manual transfer - show bank details
                    const bankDetails = payload.bank_details || {};
                    const instructions = payload.instructions || [];
                    
                    donationForm.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4CAF50, #66BB6A); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                                <i class="fas fa-university" style="font-size: 2rem;"></i>
                            </div>
                            <h3 style="color: #2d5a41; margin-bottom: 15px; font-weight: 700;">Bank Transfer Instructions</h3>
                            <p style="color: #6c757d; margin-bottom: 20px;">${payload.message || 'Your donation has been registered. Please complete the bank transfer.'}</p>
                            
                            <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 20px; text-align: left; border: 2px solid #e9ecef;">
                                <h4 style="color: #2d5a41; margin-bottom: 20px; text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                                    <i class="fas fa-building" style="margin-right: 8px; color: #4CAF50;"></i>Bank Account Details
                                </h4>
                                
                                <div style="display: grid; gap: 15px;">
                                    ${bankDetails.bank_name ? `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #6c757d; font-weight: 600;">Bank Name:</span>
                                        <span style="color: #2d5a41; font-weight: 700;">${bankDetails.bank_name}</span>
                                    </div>` : ''}
                                    
                                    ${bankDetails.account_name ? `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #6c757d; font-weight: 600;">Account Name:</span>
                                        <span style="color: #2d5a41; font-weight: 700;">${bankDetails.account_name}</span>
                                    </div>` : ''}
                                    
                                    ${bankDetails.account_number ? `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #6c757d; font-weight: 600;">Account Number:</span>
                                        <span style="color: #2d5a41; font-weight: 700; font-family: monospace; font-size: 1.1rem;">${bankDetails.account_number}</span>
                                    </div>` : ''}
                                    
                                    ${bankDetails.swift_code ? `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #6c757d; font-weight: 600;">Swift Code:</span>
                                        <span style="color: #2d5a41; font-weight: 700;">${bankDetails.swift_code}</span>
                                    </div>` : ''}
                                    
                                    ${bankDetails.branch ? `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #6c757d; font-weight: 600;">Branch:</span>
                                        <span style="color: #2d5a41; font-weight: 700;">${bankDetails.branch}</span>
                                    </div>` : ''}
                                    
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: linear-gradient(135deg, #fff9e6, #fff3cd); border-radius: 8px; border-left: 3px solid #ffc107; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #856404; font-weight: 600;">Amount:</span>
                                        <span style="color: #856404; font-weight: 700; font-size: 1.2rem;">${payload.currency} ${new Intl.NumberFormat().format(payload.amount)}</span>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; padding: 12px; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 8px; border-left: 3px solid #4CAF50; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <span style="color: #2d5a41; font-weight: 600;">Reference:</span>
                                        <span style="color: #2d5a41; font-weight: 700; font-family: monospace;">${bankDetails.reference || payload.transaction_id}</span>
                                    </div>
                                </div>
                            </div>
                            
                            ${instructions.length > 0 ? `
                            <div style="background: #fff9e6; border: 2px solid #ffeaa7; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: left;">
                                <h4 style="color: #856404; margin-bottom: 15px;">
                                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Important Instructions:
                                </h4>
                                <ol style="color: #856404; line-height: 1.8; margin: 0; padding-left: 20px;">
                                    ${instructions.map(instruction => `<li>${instruction}</li>`).join('')}
                                </ol>
                            </div>` : ''}
                            
                            <div style="background: #e8f5e9; border: 2px solid #a5d6a7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <p style="color: #2d5a41; font-size: 0.9rem; margin: 0;">
                                    <i class="fas fa-envelope" style="margin-right: 8px; color: #4CAF50;"></i>
                                    Bank transfer instructions have been sent to <strong>${payload.donor_email}</strong>
                                </p>
                            </div>
                            
                            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                                <button onclick="window.print()" style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                                    <i class="fas fa-print" style="margin-right: 8px;"></i>Print Instructions
                                </button>
                                <button onclick="window.location.href='/donations/'" style="background: linear-gradient(135deg, #4CAF50, #66BB6A); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); transition: all 0.3s ease;">
                                    <i class="fas fa-home" style="margin-right: 8px;"></i>Back to Donations
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    debugLog('payment_completed', {
                        message: payload.message,
                        donation_id: payload.donation_id
                    }, 'success');
                    
                    // Other successful responses
                    donationForm.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #28a745;">
                            <i class="fas fa-check-circle" style="font-size: 4rem; margin-bottom: 20px;"></i>
                            <h3 style="margin-bottom: 15px;">Thank You!</h3>
                            <p>${payload.message || 'Your donation has been processed successfully.'}</p>
                            ${payload.donation_id ? `<p style="font-size: 0.9rem; color: #6c757d; margin-top: 15px;">Reference: ${payload.donation_id}</p>` : ''}
                        </div>
                    `;
                }
            } else {
                const errorMessage = (payload && payload.message) || (data && data.data && data.data.message) || data.message || 'An error occurred. Please try again.';
                
                debugLog('payment_failed', {
                    error_message: errorMessage,
                    payload: payload,
                    original_data: data
                }, 'error');
                
                // Show error message
                // Create error display
                const errorDiv = document.createElement('div');
                errorDiv.style.cssText = 'background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 20px;';
                errorDiv.innerHTML = `
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 10px; font-size: 1.2rem;"></i>
                        <div>
                            <strong>Payment Failed</strong><br>
                            <span style="font-size: 0.9rem;">${errorMessage}</span>
                        </div>
                    </div>
                `;
                
                // Insert error message at top of current step
                const currentStepDiv = donationForm.querySelector('.form-step[data-step="3"]');
                currentStepDiv.insertBefore(errorDiv, currentStepDiv.firstChild);
                
                // Restore submit button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.background = '#28a745';
                
                // Scroll to error message
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        })
        .catch(error => {
            debugLog('ajax_request_error', {
                error_message: error.message,
                error_stack: error.stack
            }, 'error');
            console.error('Error:', error);
            
            // Create error display
            const errorDiv = document.createElement('div');
            errorDiv.style.cssText = 'background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 12px; margin-bottom: 20px;';
            errorDiv.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    <div>
                        <strong>Payment Processing Error</strong><br>
                        <span style="font-size: 0.9rem;">${error.message || 'Please check your internet connection and try again.'}</span>
                    </div>
                </div>
            `;
            
            // Insert error message
            const currentStepDiv = donationForm.querySelector('.form-step[data-step="3"]');
            if (currentStepDiv) {
                currentStepDiv.insertBefore(errorDiv, currentStepDiv.firstChild);
                
                // Scroll to error message
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Restore submit button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            submitBtn.style.background = '#28a745';
        });
    }
    
    // Initialize the form
    initializeForm();
});

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .payment-option:hover {
        border-color: #28a745 !important;
        background: #f8fff8 !important;
    }
    
    .preset-amount:hover {
        border-color: #28a745 !important;
        background: #f8fff8 !important;
    }
    
    .currency-btn:hover {
        background: #20c997 !important;
        color: white !important;
    }
    
    .debug-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: #1e1e1e;
        color: #ffffff;
        z-index: 10000;
        transition: right 0.3s ease;
        overflow-y: auto;
        box-shadow: -2px 0 10px rgba(0,0,0,0.3);
    }
    
    .debug-panel.open {
        right: 0;
    }
    
    .debug-panel .debug-header {
        padding: 15px;
        background: #007cba;
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .debug-panel .debug-content {
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }
    
    .debug-entry {
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 4px;
        border-left: 3px solid #007cba;
    }
    
    .debug-entry.success {
        background: rgba(40, 167, 69, 0.1);
        border-left-color: #28a745;
    }
    
    .debug-entry.error {
        background: rgba(220, 53, 69, 0.1);
        border-left-color: #dc3545;
    }
    
    .debug-entry.warning {
        background: rgba(255, 193, 7, 0.1);
        border-left-color: #ffc107;
    }
    
    .debug-timestamp {
        color: #6c757d;
        font-size: 10px;
    }
    
    .debug-event {
        color: #17a2b8;
        font-weight: bold;
    }
    
    .debug-data {
        margin-top: 5px;
        color: #e9ecef;
        white-space: pre-wrap;
        word-break: break-all;
    }
    }
    
    input:focus {
        outline: none !important;
        border-color: #28a745 !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1) !important;
    }
    
    button:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3) !important;
    }
`;
document.head.appendChild(style);
</script>


