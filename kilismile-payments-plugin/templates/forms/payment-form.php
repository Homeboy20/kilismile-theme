<?php
/**
 * KiliSmile Payments - Enhanced Payment Form Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get form configuration
$form_config = wp_parse_args($args ?? array(), array(
    'show_recurring' => true,
    'show_anonymous' => true,
    'suggested_amounts' => array(
        'USD' => array(5, 10, 25, 50, 100),
        'TZS' => array(10000, 25000, 50000, 100000, 250000)
    ),
    'min_amount' => array(
        'USD' => 1,
        'TZS' => 1000
    ),
    'default_currency' => 'USD',
    'enable_currency_conversion' => true,
    'form_style' => 'modern', // modern, classic, minimal
    'primary_color' => '#007cba',
    'success_url' => '',
    'cancel_url' => '',
    'class' => 'kilismile-payment-form'
));

// Get available gateways
$gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();

// Enqueue required assets
wp_enqueue_style('kilismile-payments-frontend');
wp_enqueue_script('kilismile-payments-frontend');

// Localize script with configuration
wp_localize_script('kilismile-payments-frontend', 'kilismile_payments', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('kilismile_payment_nonce'),
    'gateways' => $gateways,
    'form_config' => $form_config,
    'currency_symbols' => array(
        'USD' => '$',
        'TZS' => 'TSh',
        'EUR' => '€',
        'GBP' => '£'
    ),
    'strings' => array(
        'processing' => __('Processing payment...', 'kilismile-payments'),
        'redirecting' => __('Redirecting to payment gateway...', 'kilismile-payments'),
        'payment_successful' => __('Payment completed successfully!', 'kilismile-payments'),
        'payment_failed' => __('Payment failed. Please try again.', 'kilismile-payments'),
        'payment_cancelled' => __('Payment was cancelled.', 'kilismile-payments'),
        'network_error' => __('Network error. Please check your connection.', 'kilismile-payments'),
        'validation_error' => __('Please correct the errors below.', 'kilismile-payments'),
        'min_amount_error' => __('Amount must be at least %s', 'kilismile-payments'),
        'phone_format_error' => __('Please enter a valid Tanzanian phone number', 'kilismile-payments'),
        'email_format_error' => __('Please enter a valid email address', 'kilismile-payments')
    )
));
?>

<div class="kilismile-payment-form-container">
    <!-- Payment Form -->
    <form class="<?php echo esc_attr($form_config['class']); ?>" id="kilismile-payment-form" method="post">
        
        <!-- Security nonce -->
        <?php wp_nonce_field('kilismile_payment_nonce', 'payment_nonce'); ?>
        
        <!-- Form Progress -->
        <div class="payment-progress">
            <div class="progress-step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label"><?php _e('Amount', 'kilismile-payments'); ?></div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label"><?php _e('Details', 'kilismile-payments'); ?></div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label"><?php _e('Payment', 'kilismile-payments'); ?></div>
            </div>
        </div>

        <!-- Payment Messages -->
        <div class="payment-message" style="display: none;"></div>

        <!-- Step 1: Amount Selection -->
        <div class="payment-step" data-step="1">
            <div class="step-header">
                <h3><?php _e('Choose Your Donation Amount', 'kilismile-payments'); ?></h3>
                <p><?php _e('Select an amount or enter a custom amount below.', 'kilismile-payments'); ?></p>
            </div>

            <!-- Currency Selection -->
            <?php if (count($form_config['suggested_amounts']) > 1): ?>
            <div class="currency-selection">
                <div class="field-label"><?php _e('Currency', 'kilismile-payments'); ?></div>
                <div class="currency-buttons">
                    <?php foreach ($form_config['suggested_amounts'] as $currency => $amounts): ?>
                    <button type="button" class="currency-btn <?php echo $currency === $form_config['default_currency'] ? 'active' : ''; ?>" 
                            data-currency="<?php echo esc_attr($currency); ?>">
                        <?php echo esc_html($currency); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Amount Presets -->
            <div class="amount-presets">
                <div class="field-label"><?php _e('Suggested Amounts', 'kilismile-payments'); ?></div>
                <?php foreach ($form_config['suggested_amounts'] as $currency => $amounts): ?>
                <div class="currency-amounts <?php echo $currency === $form_config['default_currency'] ? 'active' : ''; ?>" 
                     data-currency="<?php echo esc_attr($currency); ?>">
                    <div class="preset-grid">
                        <?php foreach ($amounts as $amount): ?>
                        <button type="button" class="amount-preset" data-amount="<?php echo esc_attr($amount); ?>">
                            <span class="amount-value">
                                <?php 
                                $symbol = $currency === 'USD' ? '$' : ($currency === 'TZS' ? 'TSh ' : $currency . ' ');
                                echo esc_html($symbol . number_format($amount));
                                ?>
                            </span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Custom Amount -->
            <div class="custom-amount">
                <div class="field-label"><?php _e('Custom Amount', 'kilismile-payments'); ?></div>
                <div class="form-field">
                    <div class="amount-input-wrapper">
                        <span class="currency-symbol">$</span>
                        <input type="number" name="amount" id="amount" min="1" step="0.01" 
                               placeholder="0.00" class="amount-input">
                    </div>
                    <?php if ($form_config['enable_currency_conversion']): ?>
                    <div class="currency-conversion-info"></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recurring Option -->
            <?php if ($form_config['show_recurring']): ?>
            <div class="recurring-option">
                <div class="form-field checkbox-field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="recurring" value="1">
                        <span class="checkmark"></span>
                        <span class="label-text"><?php _e('Make this a monthly recurring donation', 'kilismile-payments'); ?></span>
                    </label>
                </div>
                <div class="recurring-frequency" style="display: none;">
                    <div class="field-label"><?php _e('Frequency', 'kilismile-payments'); ?></div>
                    <select name="recurring_interval" class="form-control">
                        <option value="monthly"><?php _e('Monthly', 'kilismile-payments'); ?></option>
                        <option value="quarterly"><?php _e('Quarterly', 'kilismile-payments'); ?></option>
                        <option value="yearly"><?php _e('Yearly', 'kilismile-payments'); ?></option>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="step-actions">
                <button type="button" class="btn btn-primary btn-next" disabled>
                    <?php _e('Continue', 'kilismile-payments'); ?>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Donor Information -->
        <div class="payment-step" data-step="2" style="display: none;">
            <div class="step-header">
                <h3><?php _e('Your Information', 'kilismile-payments'); ?></h3>
                <p><?php _e('Please provide your contact information for the donation receipt.', 'kilismile-payments'); ?></p>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="donor_name"><?php _e('Full Name', 'kilismile-payments'); ?> *</label>
                    <input type="text" name="donor_name" id="donor_name" required class="form-control">
                </div>

                <div class="form-field">
                    <label for="donor_email"><?php _e('Email Address', 'kilismile-payments'); ?> *</label>
                    <input type="email" name="donor_email" id="donor_email" required class="form-control">
                </div>

                <div class="form-field">
                    <label for="donor_phone"><?php _e('Phone Number', 'kilismile-payments'); ?></label>
                    <input type="tel" name="donor_phone" id="donor_phone" class="form-control"
                           placeholder="<?php _e('Optional', 'kilismile-payments'); ?>">
                    <small class="help-text phone-help" style="display: none;">
                        <?php _e('Enter Tanzanian mobile number (e.g., +255712345678)', 'kilismile-payments'); ?>
                    </small>
                </div>

                <div class="form-field full-width">
                    <label for="donor_address"><?php _e('Address', 'kilismile-payments'); ?></label>
                    <textarea name="donor_address" id="donor_address" class="form-control" rows="3"
                              placeholder="<?php _e('Optional', 'kilismile-payments'); ?>"></textarea>
                </div>
            </div>

            <!-- Anonymous Option -->
            <?php if ($form_config['show_anonymous']): ?>
            <div class="anonymous-option">
                <div class="form-field checkbox-field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="anonymous" value="1">
                        <span class="checkmark"></span>
                        <span class="label-text"><?php _e('Make this donation anonymous', 'kilismile-payments'); ?></span>
                    </label>
                    <small class="help-text">
                        <?php _e('Your name will not be displayed publicly, but we still need your contact information for tax purposes.', 'kilismile-payments'); ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>

            <div class="step-actions">
                <button type="button" class="btn btn-secondary btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php _e('Back', 'kilismile-payments'); ?>
                </button>
                <button type="button" class="btn btn-primary btn-next">
                    <?php _e('Continue to Payment', 'kilismile-payments'); ?>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Payment Method -->
        <div class="payment-step" data-step="3" style="display: none;">
            <div class="step-header">
                <h3><?php _e('Payment Method', 'kilismile-payments'); ?></h3>
                <p><?php _e('Choose how you would like to complete your donation.', 'kilismile-payments'); ?></p>
            </div>

            <!-- Payment Summary -->
            <div class="payment-summary">
                <h4><?php _e('Donation Summary', 'kilismile-payments'); ?></h4>
                <div class="summary-content">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Gateway Selection -->
            <div class="gateway-selection">
                <div class="field-label"><?php _e('Select Payment Method', 'kilismile-payments'); ?></div>
                <div class="gateway-options">
                    <?php if (empty($gateways)): ?>
                    <div class="no-gateways">
                        <p><?php _e('No payment methods are currently available. Please contact us to complete your donation.', 'kilismile-payments'); ?></p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($gateways as $gateway_id => $gateway): ?>
                        <div class="gateway-option" data-gateway="<?php echo esc_attr($gateway_id); ?>">
                            <label class="gateway-label">
                                <input type="radio" name="gateway" value="<?php echo esc_attr($gateway_id); ?>" 
                                       class="gateway-radio">
                                <span class="gateway-info">
                                    <span class="gateway-name"><?php echo esc_html($gateway['title']); ?></span>
                                    <span class="gateway-description"><?php echo esc_html($gateway['description']); ?></span>
                                    <span class="gateway-currencies">
                                        <?php 
                                        if (!empty($gateway['currencies'])) {
                                            echo esc_html(sprintf(__('Accepts: %s', 'kilismile-payments'), implode(', ', $gateway['currencies'])));
                                        }
                                        ?>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gateway-specific fields will be loaded here -->
            <div class="gateway-fields"></div>

            <div class="step-actions">
                <button type="button" class="btn btn-secondary btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <?php _e('Back', 'kilismile-payments'); ?>
                </button>
                <button type="submit" class="btn btn-primary payment-submit" disabled>
                    <span class="btn-text"><?php _e('Complete Donation', 'kilismile-payments'); ?></span>
                    <span class="loading-spinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="action" value="kilismile_process_payment">
        <input type="hidden" name="form_id" value="<?php echo esc_attr(uniqid('form_')); ?>">
        <input type="hidden" name="currency" value="<?php echo esc_attr($form_config['default_currency']); ?>">
        
        <?php if (!empty($form_config['success_url'])): ?>
        <input type="hidden" name="success_url" value="<?php echo esc_url($form_config['success_url']); ?>">
        <?php endif; ?>
        
        <?php if (!empty($form_config['cancel_url'])): ?>
        <input type="hidden" name="cancel_url" value="<?php echo esc_url($form_config['cancel_url']); ?>">
        <?php endif; ?>
    </form>

    <!-- Loading Overlay -->
    <div class="form-loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <div class="loading-text"><?php _e('Processing...', 'kilismile-payments'); ?></div>
        </div>
    </div>
</div>

<?php
/**
 * Action hook for additional form content
 * 
 * @since 1.0.0
 * @param array $form_config Form configuration
 * @param array $gateways Available gateways
 */
do_action('kilismile_payments_after_form', $form_config, $gateways);
?>

