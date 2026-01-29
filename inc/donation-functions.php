<?php
/**
 * Donation Functions
 *
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Donation customizer has been moved to admin settings page
// See admin/theme-settings.php for the new payment and donation configuration interface

/**
 * Donation Progress Bar
 * 
 * @deprecated Use the new OOP system instead
 * 
 * Note: This function is now defined in bridge-functions.php
 * This is just a wrapper to avoid fatal errors in legacy code
 */
function kilismile_donation_progress_bar_legacy($currency = 'USD') {
    if (function_exists('_kilismile_legacy_donation_progress_bar')) {
        return _kilismile_legacy_donation_progress_bar($currency);
    }
    
    $currency = strtoupper($currency);
    $exchange_rate = get_option('kilismile_exchange_rate', 2500);
    
    if ($currency === 'USD') {
        $goal = get_option('kilismile_donation_goal_usd', 10000);
        $current = get_option('kilismile_current_donations_usd', 2500);
        $currency_symbol = '$';
        $currency_code = 'USD';
    } else {
        $goal = get_option('kilismile_donation_goal_tzs', 25000000);
        $current = get_option('kilismile_current_donations_tzs', 6250000);
        $currency_symbol = 'TZS ';
        $currency_code = 'TZS';
    }
    
    $percentage = $goal > 0 ? min(($current / $goal) * 100, 100) : 0;
    
    ob_start();
    ?>
    <div class="donation-progress" style="margin: 30px 0;">
        <div class="progress-header" style="text-align: center; margin-bottom: 15px;">
            <h4 style="margin: 0; color: var(--dark-green);"><?php _e('Monthly Donation Progress', 'kilismile'); ?></h4>
            <div class="currency-toggle" style="margin-top: 8px;">
                <button type="button" class="currency-btn <?php echo $currency === 'USD' ? 'active' : ''; ?>" data-currency="USD" style="background: <?php echo $currency === 'USD' ? 'var(--primary-green)' : 'transparent'; ?>; color: <?php echo $currency === 'USD' ? 'white' : 'var(--primary-green)'; ?>; border: 2px solid var(--primary-green); padding: 5px 15px; margin: 0 5px; border-radius: 20px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s ease;">USD</button>
                <button type="button" class="currency-btn <?php echo $currency === 'TZS' ? 'active' : ''; ?>" data-currency="TZS" style="background: <?php echo $currency === 'TZS' ? 'var(--primary-green)' : 'transparent'; ?>; color: <?php echo $currency === 'TZS' ? 'white' : 'var(--primary-green)'; ?>; border: 2px solid var(--primary-green); padding: 5px 15px; margin: 0 5px; border-radius: 20px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s ease;">TZS</button>
            </div>
        </div>
        <div class="progress-info" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #666;">
            <span><?php printf(__('Raised: %s%s', 'kilismile'), $currency_symbol, number_format($current)); ?></span>
            <span><?php printf(__('Goal: %s%s', 'kilismile'), $currency_symbol, number_format($goal)); ?></span>
        </div>
        <div class="progress-bar" style="background: #e0e0e0; height: 20px; border-radius: 10px; overflow: hidden;">
            <div class="progress-fill" style="background: linear-gradient(90deg, var(--primary-green), var(--accent-green)); height: 100%; width: <?php echo esc_attr($percentage); ?>%; transition: width 0.3s ease; border-radius: 10px;"></div>
        </div>
        <div class="progress-percentage" style="text-align: center; margin-top: 10px; font-weight: 600; color: var(--primary-green);">
            <?php printf(__('%d%% of monthly goal reached', 'kilismile'), round($percentage)); ?>
        </div>
        <?php if ($currency === 'TZS') : ?>
            <div class="exchange-rate-info" style="text-align: center; margin-top: 8px; font-size: 0.8rem; color: #888;">
                <?php printf(__('≈ $%s USD (Rate: 1 USD = %s TZS)', 'kilismile'), number_format($current / $exchange_rate, 0), number_format($exchange_rate)); ?>
            </div>
        <?php elseif ($currency === 'USD') : ?>
            <div class="exchange-rate-info" style="text-align: center; margin-top: 8px; font-size: 0.8rem; color: #888;">
                <?php printf(__('≈ TZS %s (Rate: 1 USD = %s TZS)', 'kilismile'), number_format($current * $exchange_rate, 0), number_format($exchange_rate)); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencyBtns = document.querySelectorAll('.currency-btn');
            currencyBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const currency = this.getAttribute('data-currency');
                    
                    // Update active state
                    currencyBtns.forEach(b => {
                        b.classList.remove('active');
                        b.style.background = 'transparent';
                        b.style.color = 'var(--primary-green)';
                    });
                    this.classList.add('active');
                    this.style.background = 'var(--primary-green)';
                    this.style.color = 'white';
                    
                    // Trigger form currency change if donation form exists
                    const currencySelect = document.querySelector('select[name="donation_currency"]');
                    if (currencySelect) {
                        currencySelect.value = currency;
                        currencySelect.dispatchEvent(new Event('change'));
                    }
                    
                    // Reload progress bar
                    fetch(window.location.href + '?ajax_progress=1&currency=' + currency)
                        .then(response => response.text())
                        .then(html => {
                            const progressDiv = document.querySelector('.donation-progress');
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;
                            const newProgress = tempDiv.querySelector('.donation-progress');
                            if (newProgress && progressDiv) {
                                progressDiv.innerHTML = newProgress.innerHTML;
                            }
                        });
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Enhanced Donation Form with Currency Selection
 * 
 * @deprecated Use the new OOP system instead
 */
function _kilismile_legacy_donation_form($args = array()) {
    $donations_enabled = get_option('kilismile_enable_donations', 1);
    error_log('Donations enabled: ' . $donations_enabled);
    
    if (!$donations_enabled) {
        error_log('Donations are disabled');
        return '<div class="notice notice-warning"><p>' . __('Donation system is currently disabled.', 'kilismile') . '</p></div>';
    }
    
    $defaults = array(
        'title' => __('Make a Donation', 'kilismile'),
        'show_amounts' => true,
        'show_progress' => true,
        'style' => 'default'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Get default currency
    $default_currency = get_option('kilismile_default_currency', 'USD');
    $exchange_rate = get_option('kilismile_exchange_rate', 2500);
    
    // Suggested amounts for different currencies
    $suggested_amounts = array(
        'USD' => array(25, 50, 100, 250, 500),
        'TZS' => array(50000, 100000, 250000, 500000, 1000000)
    );
    
    ob_start();
    ?>
    <div class="donation-form-container" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <?php if ($args['title']) : ?>
            <h3 style="color: var(--dark-green); margin-bottom: 20px; text-align: center;"><?php echo esc_html($args['title']); ?></h3>
        <?php endif; ?>
        
        <?php if ($args['show_progress']) : ?>
            <?php echo kilismile_donation_progress_bar_legacy($default_currency); ?>
        <?php endif; ?>
        
        <form class="donation-form kilismile-donation-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('kilismile_donation_nonce', 'donation_nonce'); ?>
            <input type="hidden" name="action" value="kilismile_process_donation">
            
            <!-- Currency Selection -->
            <div class="currency-selection" style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600; color: var(--dark-green);">
                    <i class="fas fa-globe" style="margin-right: 8px;"></i>
                    <?php _e('Select Currency', 'kilismile'); ?>
                </label>
                <select name="donation_currency" id="donation_currency" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: white;" required>
                    <option value="USD" <?php selected($default_currency, 'USD'); ?>>
                        🇺🇸 <?php _e('US Dollar (USD) - International', 'kilismile'); ?>
                    </option>
                    <option value="TZS" <?php selected($default_currency, 'TZS'); ?>>
                        🇹🇿 <?php _e('Tanzanian Shilling (TZS) - Local', 'kilismile'); ?>
                    </option>
                </select>
                <div class="currency-info" style="margin-top: 8px; font-size: 0.9rem; color: #666;">
                    <span id="currency-note">
                        <?php if ($default_currency === 'USD') : ?>
                            <?php _e('International payment methods available', 'kilismile'); ?>
                        <?php else : ?>
                            <?php _e('Local Tanzanian payment methods available', 'kilismile'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <?php if ($args['show_amounts']) : ?>
                <div class="donation-amounts" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: var(--dark-green);">
                        <i class="fas fa-hand-holding-heart" style="margin-right: 8px;"></i>
                        <span id="amount-label"><?php _e('Select Amount (USD)', 'kilismile'); ?></span>
                    </label>
                    
                    <!-- USD Amounts -->
                    <div class="amount-buttons usd-amounts" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px; margin-bottom: 15px;">
                        <?php foreach ($suggested_amounts['USD'] as $amount) : ?>
                            <button type="button" class="amount-btn" data-amount="<?php echo esc_attr($amount); ?>" data-currency="USD" style="padding: 10px; border: 2px solid var(--primary-green); background: white; color: var(--primary-green); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600;">
                                $<?php echo number_format($amount); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- TZS Amounts -->
                    <div class="amount-buttons tzs-amounts" style="display: none; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; margin-bottom: 15px;">
                        <?php foreach ($suggested_amounts['TZS'] as $amount) : ?>
                            <button type="button" class="amount-btn" data-amount="<?php echo esc_attr($amount); ?>" data-currency="TZS" style="padding: 10px; border: 2px solid var(--primary-green); background: white; color: var(--primary-green); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; font-size: 0.9rem;">
                                TZS <?php echo number_format($amount); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="custom-amount" style="margin-bottom: 20px;">
                        <label for="donation_amount" style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">
                            <span id="custom-amount-label"><?php _e('Or enter custom amount:', 'kilismile'); ?></span>
                        </label>
                        <div style="position: relative;">
                            <span id="currency-symbol" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666; font-weight: 600;">$</span>
                            <input type="number" id="donation_amount" name="donation_amount" min="1" placeholder="0" style="width: 100%; padding: 12px 12px 12px 40px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" required>
                        </div>
                        <div id="conversion-display" style="margin-top: 5px; font-size: 0.8rem; color: #888;"></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="donor-info" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: var(--dark-green);">
                    <i class="fas fa-user" style="margin-right: 8px;"></i>
                    <?php _e('Donor Information', 'kilismile'); ?>
                </h4>
                <div class="row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div class="col" style="flex: 1;">
                        <input type="text" name="donor_first_name" placeholder="<?php _e('First Name', 'kilismile'); ?>" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" required>
                    </div>
                    <div class="col" style="flex: 1;">
                        <input type="text" name="donor_last_name" placeholder="<?php _e('Last Name', 'kilismile'); ?>" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" required>
                    </div>
                </div>
                <div class="row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div class="col" style="flex: 1;">
                        <input type="email" name="donor_email" placeholder="<?php _e('Email Address', 'kilismile'); ?>" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" required>
                    </div>
                    <div class="col" style="flex: 1;">
                        <input type="tel" name="donor_phone" placeholder="<?php _e('Phone Number', 'kilismile'); ?>" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                    </div>
                </div>
                <textarea name="donor_message" placeholder="<?php _e('Message (optional)', 'kilismile'); ?>" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; resize: vertical;"></textarea>
            </div>
            
            <!-- Payment Methods - Dynamic based on currency -->
            <div class="donation-methods" style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px; color: var(--dark-green);">
                    <i class="fas fa-credit-card" style="margin-right: 8px;"></i>
                    <span id="payment-methods-title"><?php _e('Select Payment Method', 'kilismile'); ?></span>
                </h4>
                
                <!-- International Payment Methods (USD) -->
                <div class="international-methods">
                    <?php 
                    $usd_methods_available = false;
                    $usd_methods_count = 0;
                    ?>
                    
                    <!-- PayPal Gateway -->
                    <?php if (get_option('kilismile_paypal_enabled') && get_option('kilismile_paypal_email')) : 
                        $usd_methods_available = true;
                        $usd_methods_count++;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #0070ba, #003087); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fab fa-paypal" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('PayPal Gateway', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Secure & Trusted', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #0070ba; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #f8f9ff;">
                                    <input type="radio" name="payment_method" value="paypal" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fab fa-paypal" style="color: #0070ba; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #0070ba;"><?php _e('PayPal', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('Pay securely with PayPal account or credit card', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-shield-alt" style="color: #00a651; margin-right: 5px;"></i>
                                            <?php _e('Buyer Protection • Instant Processing', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <i class="fas fa-credit-card" style="color: #666; margin: 2px;"></i>
                                        <i class="fab fa-cc-visa" style="color: #1a1f71; margin: 2px;"></i>
                                        <i class="fab fa-cc-mastercard" style="color: #eb001b; margin: 2px;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Stripe Gateway -->
                    <?php if (get_option('kilismile_stripe_enabled') && get_option('kilismile_stripe_public_key')) : 
                        $usd_methods_available = true;
                        $usd_methods_count++;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #635bff, #4c44db); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fab fa-stripe" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('Stripe Gateway', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Global Leader', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #635bff; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #fafbff;">
                                    <input type="radio" name="payment_method" value="stripe" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fas fa-credit-card" style="color: #635bff; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #635bff;"><?php _e('Credit/Debit Cards', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('Visa, Mastercard, American Express & more', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-lock" style="color: #00a651; margin-right: 5px;"></i>
                                            <?php _e('256-bit SSL Encryption • PCI Compliant', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <i class="fab fa-cc-visa" style="color: #1a1f71; margin: 2px; font-size: 1.5rem;"></i>
                                        <i class="fab fa-cc-mastercard" style="color: #eb001b; margin: 2px; font-size: 1.5rem;"></i>
                                        <i class="fab fa-cc-amex" style="color: #006fcf; margin: 2px; font-size: 1.5rem;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Wire Transfer Gateway -->
                    <?php if (get_option('kilismile_wire_transfer_enabled') && get_option('kilismile_wire_transfer_details')) : 
                        $usd_methods_available = true;
                        $usd_methods_count++;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fas fa-university" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('International Bank Transfer', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Direct Transfer', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #2c3e50; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #f8f9fa;">
                                    <input type="radio" name="payment_method" value="wire_transfer" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fas fa-university" style="color: #2c3e50; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;"><?php _e('Wire Transfer', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('International bank transfer (SWIFT)', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-clock" style="color: #f39c12; margin-right: 5px;"></i>
                                            <?php _e('Processing time: 1-3 business days', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; color: #2c3e50;">
                                        <i class="fas fa-globe" style="font-size: 1.5rem;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$usd_methods_available) : ?>
                        <div class="no-payment-methods" style="padding: 25px; background: linear-gradient(135deg, #fff3cd, #ffeaa7); border: 2px solid #ffd700; border-radius: 12px; text-align: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 2.5rem; margin-bottom: 15px;"></i>
                            <h5 style="margin: 0 0 10px 0; color: #856404; font-weight: 600;"><?php _e('No International Payment Methods Available', 'kilismile'); ?></h5>
                            <p style="margin: 0 0 15px 0; color: #856404;">
                                <?php _e('International payment gateways are currently being configured. Please contact us directly to make a donation.', 'kilismile'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary" style="padding: 12px 24px; border-radius: 6px; text-decoration: none; background: #856404; color: white; font-weight: 600;">
                                <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                                <?php _e('Contact Us', 'kilismile'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Local Payment Methods (TZS) -->
                <div class="local-methods" style="display: none;">
                    <?php 
                    $tzs_methods_available = false;
                    $tzs_phone_methods_available = false;
                    $tzs_bank_methods_available = false;
                    ?>
                    
                    <!-- Phone Payment Gateway -->
                    <?php 
                    // Check if any phone payment methods are available
                    if ((get_option('kilismile_mpesa_enabled') && get_option('kilismile_mpesa_number')) ||
                        (get_option('kilismile_tigo_pesa_enabled') && get_option('kilismile_tigo_pesa_number')) ||
                        (get_option('kilismile_airtel_money_enabled') && get_option('kilismile_airtel_money_number'))) {
                        $tzs_phone_methods_available = true;
                        $tzs_methods_available = true;
                    }
                    ?>
                    
                    <?php if ($tzs_phone_methods_available) : ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #00a651, #007d3e); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fas fa-mobile-alt" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('Pay by Phone', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Mobile Money', 'kilismile'); ?></span>
                            </div>
                            <div style="border: 2px solid #00a651; border-top: none; border-radius: 0 0 8px 8px; background: #f0fff4;">
                                
                                <!-- M-Pesa -->
                                <?php if (get_option('kilismile_mpesa_enabled') && get_option('kilismile_mpesa_number')) : ?>
                                    <div class="payment-method" style="border-bottom: 1px solid #e0e0e0;">
                                        <label style="display: flex; align-items: center; cursor: pointer; padding: 18px 20px; transition: all 0.3s ease;">
                                            <input type="radio" name="payment_method" value="mpesa" style="margin-right: 15px; transform: scale(1.2);" required>
                                            <div style="background: #00a651; color: white; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                <span style="font-weight: bold; font-size: 0.8rem;">M</span>
                                            </div>
                                            <div style="flex: 1;">
                                                <span style="font-weight: 600; font-size: 1.1rem; color: #00a651;"><?php _e('M-Pesa', 'kilismile'); ?></span>
                                                <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php printf(__('Send money to: %s', 'kilismile'), get_option('kilismile_mpesa_number')); ?></div>
                                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                                    <i class="fas fa-bolt" style="color: #00a651; margin-right: 5px;"></i>
                                                    <?php _e('Instant • Secure • Widely accepted', 'kilismile'); ?>
                                                </div>
                                            </div>
                                            <div style="text-align: right; color: #00a651;">
                                                <i class="fas fa-mobile-alt" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Tigo Pesa -->
                                <?php if (get_option('kilismile_tigo_pesa_enabled') && get_option('kilismile_tigo_pesa_number')) : ?>
                                    <div class="payment-method" style="border-bottom: 1px solid #e0e0e0;">
                                        <label style="display: flex; align-items: center; cursor: pointer; padding: 18px 20px; transition: all 0.3s ease;">
                                            <input type="radio" name="payment_method" value="tigo_pesa" style="margin-right: 15px; transform: scale(1.2);" required>
                                            <div style="background: #0066cc; color: white; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                <span style="font-weight: bold; font-size: 0.8rem;">T</span>
                                            </div>
                                            <div style="flex: 1;">
                                                <span style="font-weight: 600; font-size: 1.1rem; color: #0066cc;"><?php _e('Tigo Pesa', 'kilismile'); ?></span>
                                                <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php printf(__('Send money to: %s', 'kilismile'), get_option('kilismile_tigo_pesa_number')); ?></div>
                                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                                    <i class="fas fa-zap" style="color: #0066cc; margin-right: 5px;"></i>
                                                    <?php _e('Fast transfer • Reliable network', 'kilismile'); ?>
                                                </div>
                                            </div>
                                            <div style="text-align: right; color: #0066cc;">
                                                <i class="fas fa-mobile-alt" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Airtel Money -->
                                <?php if (get_option('kilismile_airtel_money_enabled') && get_option('kilismile_airtel_money_number')) : ?>
                                    <div class="payment-method">
                                        <label style="display: flex; align-items: center; cursor: pointer; padding: 18px 20px; transition: all 0.3s ease;">
                                            <input type="radio" name="payment_method" value="airtel_money" style="margin-right: 15px; transform: scale(1.2);" required>
                                            <div style="background: #ff0000; color: white; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                <span style="font-weight: bold; font-size: 0.8rem;">A</span>
                                            </div>
                                            <div style="flex: 1;">
                                                <span style="font-weight: 600; font-size: 1.1rem; color: #ff0000;"><?php _e('Airtel Money', 'kilismile'); ?></span>
                                                <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php printf(__('Send money to: %s', 'kilismile'), get_option('kilismile_airtel_money_number')); ?></div>
                                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                                    <i class="fas fa-signal" style="color: #ff0000; margin-right: 5px;"></i>
                                                    <?php _e('Wide coverage • Simple to use', 'kilismile'); ?>
                                                </div>
                                            </div>
                                            <div style="text-align: right; color: #ff0000;">
                                                <i class="fas fa-mobile-alt" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Selcom Payment Gateway -->
                    <?php if (get_option('kilismile_selcom_enabled') && get_option('kilismile_selcom_api_key')) : 
                        $tzs_methods_available = true;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fas fa-credit-card" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('Selcom Payment Gateway', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('All Payment Methods', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #1e3a8a; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #eff6ff;">
                                    <input type="radio" name="payment_method" value="selcom" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fas fa-shield-alt" style="color: #1e3a8a; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #1e3a8a;"><?php _e('Selcom Pay', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('Cards, Mobile Money, Bank Transfer & More', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-certificate" style="color: #1e3a8a; margin-right: 5px;"></i>
                                            <?php _e('Secure • Trusted • All payment methods', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; color: #1e3a8a; display: flex; flex-direction: column; align-items: center;">
                                        <i class="fas fa-mobile-alt" style="font-size: 1.2rem; margin: 1px;"></i>
                                        <i class="fas fa-credit-card" style="font-size: 1.2rem; margin: 1px;"></i>
                                        <i class="fas fa-university" style="font-size: 1.2rem; margin: 1px;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Azam Pay Gateway -->
                    <?php if (get_option('kilismile_azam_pay_enabled') && get_option('kilismile_azam_api_key')) : 
                        $tzs_methods_available = true;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fas fa-mobile-alt" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('Azam Pay Gateway', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Mobile Payments', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #dc2626; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #fef2f2;">
                                    <input type="radio" name="payment_method" value="azam_pay" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fas fa-wallet" style="color: #dc2626; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #dc2626;"><?php _e('Azam Pay', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('Mobile money and digital wallet payments', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-lightning-bolt" style="color: #dc2626; margin-right: 5px;"></i>
                                            <?php _e('Fast • Convenient • Digital wallet', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; color: #dc2626; display: flex; flex-direction: column; align-items: center;">
                                        <i class="fas fa-mobile-alt" style="font-size: 1.3rem; margin: 2px;"></i>
                                        <i class="fas fa-wallet" style="font-size: 1.3rem; margin: 2px;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Local Bank Transfer Gateway -->
                    <?php if (get_option('kilismile_local_bank_enabled') && get_option('kilismile_local_bank_details')) : 
                        $tzs_bank_methods_available = true;
                        $tzs_methods_available = true;
                    ?>
                        <div class="payment-gateway" style="margin-bottom: 15px;">
                            <div class="gateway-header" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: 600; display: flex; align-items: center;">
                                <i class="fas fa-university" style="font-size: 1.8rem; margin-right: 12px;"></i>
                                <span><?php _e('Local Bank Transfer', 'kilismile'); ?></span>
                                <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.9;"><?php _e('Tanzanian Banks', 'kilismile'); ?></span>
                            </div>
                            <div class="payment-method" style="border: 2px solid #2c3e50; border-top: none; border-radius: 0 0 8px 8px;">
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 20px; transition: all 0.3s ease; background: #f8f9fa;">
                                    <input type="radio" name="payment_method" value="local_bank" style="margin-right: 15px; transform: scale(1.2);" required>
                                    <i class="fas fa-university" style="color: #2c3e50; font-size: 2rem; margin-right: 15px;"></i>
                                    <div style="flex: 1;">
                                        <span style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;"><?php _e('Bank Transfer (TZS)', 'kilismile'); ?></span>
                                        <div style="font-size: 0.9rem; color: #666; margin-top: 3px;"><?php _e('Transfer from any Tanzanian bank', 'kilismile'); ?></div>
                                        <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <i class="fas fa-building" style="color: #2c3e50; margin-right: 5px;"></i>
                                            <?php _e('CRDB • NBC • NMB • All local banks', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; color: #2c3e50;">
                                        <i class="fas fa-building" style="font-size: 1.5rem;"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$tzs_methods_available) : ?>
                        <div class="no-payment-methods" style="padding: 25px; background: linear-gradient(135deg, #fff3cd, #ffeaa7); border: 2px solid #ffd700; border-radius: 12px; text-align: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 2.5rem; margin-bottom: 15px;"></i>
                            <h5 style="margin: 0 0 10px 0; color: #856404; font-weight: 600;"><?php _e('No Local Payment Methods Available', 'kilismile'); ?></h5>
                            <p style="margin: 0 0 15px 0; color: #856404;">
                                <?php _e('Local payment methods are currently being configured. Please contact us directly to make a donation.', 'kilismile'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary" style="padding: 12px 24px; border-radius: 6px; text-decoration: none; background: #856404; color: white; font-weight: 600;">
                                <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                                <?php _e('Contact Us', 'kilismile'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="donation-frequency" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">
                    <input type="checkbox" name="recurring_donation" value="1" style="margin-right: 10px;">
                    <i class="fas fa-sync-alt" style="color: var(--primary-green); margin-right: 8px;"></i>
                    <span><?php _e('Make this a monthly recurring donation', 'kilismile'); ?></span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; background: var(--primary-green); color: white;">
                <i class="fas fa-heart" style="margin-right: 8px;"></i>
                <span id="donate-button-text"><?php _e('Donate Now', 'kilismile'); ?></span>
            </button>
        </form>
        
        <div class="donation-info" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem; color: #666;">
            <p style="margin: 0; text-align: center;">
                <i class="fas fa-shield-alt" style="color: var(--primary-green); margin-right: 5px;"></i>
                <span id="security-message"><?php _e('Your donation is secure and helps us continue our mission of promoting health in remote areas of Tanzania.', 'kilismile'); ?></span>
            </p>
        </div>
    </div>
    
    <style>
        .amount-btn:hover,
        .amount-btn.selected {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        /* Payment Gateway Styles */
        .payment-gateway {
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .payment-gateway:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .payment-gateway:hover .gateway-header {
            filter: brightness(1.1);
        }
        
        .payment-method label:hover {
            background: rgba(76, 175, 80, 0.05) !important;
        }
        
        .payment-method input[type="radio"]:checked + div {
            background: #e8f5e8 !important;
        }
        
        .payment-method input[type="radio"]:checked + div .gateway-header {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        }
        
        /* Phone payment method specific styles */
        .local-methods .payment-method:hover label {
            background: rgba(0, 166, 81, 0.05) !important;
        }
        
        /* Individual mobile money provider hover effects */
        .payment-method input[name="payment_method"][value="mpesa"]:checked ~ * {
            background: rgba(0, 166, 81, 0.1) !important;
        }
        
        .payment-method input[name="payment_method"][value="tigo_pesa"]:checked ~ * {
            background: rgba(0, 102, 204, 0.1) !important;
        }
        
        .payment-method input[name="payment_method"][value="airtel_money"]:checked ~ * {
            background: rgba(255, 0, 0, 0.1) !important;
        }
        
        /* Gateway header animations */
        .gateway-header {
            position: relative;
            overflow: hidden;
        }
        
        .gateway-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .payment-gateway:hover .gateway-header::before {
            left: 100%;
        }
        
        /* Radio button custom styling */
        .payment-method input[type="radio"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 50%;
            position: relative;
            background: white;
            cursor: pointer;
        }
        
        .payment-method input[type="radio"]:checked {
            border-color: var(--primary-green);
            background: var(--primary-green);
        }
        
        .payment-method input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }
        
        /* Submit button enhanced styling */
        .donation-form button[type="submit"] {
            position: relative;
            overflow: hidden;
        }
        
        .donation-form button[type="submit"]:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        .donation-form button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .donation-form button[type="submit"]:hover::before {
            left: 100%;
        }
        
        /* Currency select styling */
        #donation_currency {
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23666" d="m0,1l2,2l2,-2z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            appearance: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .payment-gateway {
                margin-bottom: 10px;
            }
            
            .gateway-header {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .payment-method label {
                padding: 15px !important;
            }
            
            .payment-method i {
                font-size: 1.5rem !important;
            }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Donation form script loaded');
            
            const form = document.getElementById('kilismile-donation-form');
            if (!form) {
                console.error('Donation form not found');
                return;
            }
            
            // Add form submission handler
            form.addEventListener('submit', function(e) {
                console.log('Form submission attempted');
                
                // Validate required fields
                const amount = document.getElementById('donation_amount').value;
                const currency = document.getElementById('donation_currency').value;
                const firstName = document.querySelector('input[name="donor_first_name"]').value;
                const lastName = document.querySelector('input[name="donor_last_name"]').value;
                const email = document.querySelector('input[name="donor_email"]').value;
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                
                console.log('Form data:', {
                    amount: amount,
                    currency: currency,
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    paymentMethod: paymentMethod ? paymentMethod.value : 'none'
                });
                
                if (!amount || amount <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid donation amount');
                    console.error('Invalid amount');
                    return false;
                }
                
                if (!firstName || !lastName || !email) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                    console.error('Missing required fields');
                    return false;
                }
                
                if (!paymentMethod) {
                    e.preventDefault();
                    alert('Please select a payment method');
                    console.error('No payment method selected');
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
                return true;
            });
            
            const currencySelect = document.getElementById('donation_currency');
            const amountInput = document.getElementById('donation_amount');
            const currencySymbol = document.getElementById('currency-symbol');
            const amountLabel = document.getElementById('amount-label');
            const customAmountLabel = document.getElementById('custom-amount-label');
            const currencyNote = document.getElementById('currency-note');
            const conversionDisplay = document.getElementById('conversion-display');
            const paymentMethodsTitle = document.getElementById('payment-methods-title');
            const donateButtonText = document.getElementById('donate-button-text');
            const securityMessage = document.getElementById('security-message');
            const exchangeRate = <?php echo esc_js($exchange_rate); ?>;
            
            const usdAmounts = document.querySelector('.usd-amounts');
            const tzsAmounts = document.querySelector('.tzs-amounts');
            const internationalMethods = document.querySelector('.international-methods');
            const localMethods = document.querySelector('.local-methods');
            
            function updateCurrency() {
                const currency = currencySelect.value;
                console.log('Currency changed to:', currency);
                
                if (currency === 'USD') {
                    // Update labels and symbols
                    if (currencySymbol) currencySymbol.textContent = '$';
                    if (amountLabel) amountLabel.textContent = '<?php _e('Select Amount (USD)', 'kilismile'); ?>';
                    if (customAmountLabel) customAmountLabel.textContent = '<?php _e('Or enter custom amount (USD):', 'kilismile'); ?>';
                    if (currencyNote) currencyNote.textContent = '<?php _e('International payment gateways available', 'kilismile'); ?>';
                    if (paymentMethodsTitle) paymentMethodsTitle.textContent = '<?php _e('Select Payment Method', 'kilismile'); ?>';
                    if (donateButtonText) donateButtonText.textContent = '<?php _e('Donate Now (USD)', 'kilismile'); ?>';
                    if (securityMessage) securityMessage.textContent = '<?php _e('Your donation is secure and processed through international payment gateways.', 'kilismile'); ?>';
                    
                    // Show/hide amounts
                    if (usdAmounts) usdAmounts.style.display = 'grid';
                    if (tzsAmounts) tzsAmounts.style.display = 'none';
                    
                    // Show/hide payment methods
                    if (internationalMethods) internationalMethods.style.display = 'block';
                    if (localMethods) localMethods.style.display = 'none';
                    
                    // Clear TZS selections
                    if (tzsAmounts) {
                        tzsAmounts.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                    }
                    
                } else if (currency === 'TZS') {
                    // Update labels and symbols
                    if (currencySymbol) currencySymbol.textContent = 'TZS';
                    if (amountLabel) amountLabel.textContent = '<?php _e('Select Amount (TZS)', 'kilismile'); ?>';
                    if (customAmountLabel) customAmountLabel.textContent = '<?php _e('Or enter custom amount (TZS):', 'kilismile'); ?>';
                    if (currencyNote) currencyNote.textContent = '<?php _e('Mobile money and local banking available', 'kilismile'); ?>';
                    if (paymentMethodsTitle) paymentMethodsTitle.textContent = '<?php _e('Select Payment Method', 'kilismile'); ?>';
                    if (donateButtonText) donateButtonText.textContent = '<?php _e('Donate Now (TZS)', 'kilismile'); ?>';
                    if (securityMessage) securityMessage.textContent = '<?php _e('Your donation is secure and processed through trusted local payment providers.', 'kilismile'); ?>';
                    
                    // Show/hide amounts
                    if (usdAmounts) usdAmounts.style.display = 'none';
                    if (tzsAmounts) tzsAmounts.style.display = 'grid';
                    
                    // Show/hide payment methods
                    if (internationalMethods) internationalMethods.style.display = 'none';
                    if (localMethods) localMethods.style.display = 'block';
                    
                    // Clear USD selections
                    if (usdAmounts) {
                        usdAmounts.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                    }
                }
                
                // Clear amount input and conversion
                if (amountInput) amountInput.value = '';
                if (conversionDisplay) conversionDisplay.textContent = '';
                
                // Clear all amount button selections
                document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                
                // Clear payment method selections
                document.querySelectorAll('input[name="payment_method"]').forEach(input => input.checked = false);
            }
            
            function updateConversion() {
                const amount = parseFloat(amountInput.value);
                const currency = currencySelect.value;
                
                if (amount && amount > 0) {
                    if (currency === 'USD') {
                        const tzsAmount = amount * exchangeRate;
                        conversionDisplay.textContent = `≈ TZS ${tzsAmount.toLocaleString()}`;
                    } else if (currency === 'TZS') {
                        const usdAmount = amount / exchangeRate;
                        conversionDisplay.textContent = `≈ $${usdAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    }
                } else {
                    conversionDisplay.textContent = '';
                }
            }
            
            // Event listeners
            currencySelect.addEventListener('change', updateCurrency);
            amountInput.addEventListener('input', updateConversion);
            
            // Amount button handlers
            document.querySelectorAll('.amount-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const amount = this.getAttribute('data-amount');
                    const buttonCurrency = this.getAttribute('data-currency');
                    
                    // Set currency if needed
                    if (currencySelect.value !== buttonCurrency) {
                        currencySelect.value = buttonCurrency;
                        updateCurrency();
                    }
                    
                    amountInput.value = amount;
                    updateConversion();
                    
                    // Update button states
                    document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
            
            // Clear button selection when custom amount is entered
            amountInput.addEventListener('input', function() {
                document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                updateConversion();
            });
            
            // Initialize
            updateCurrency();
        });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Process Donation Form Submission
 * 
 * @deprecated Use the new OOP system instead
 */
function _kilismile_legacy_process_donation() {
    // Debug: Log that the function was called
    error_log('Legacy donation processing function called');
    
    // Verify nonce
    if (!isset($_POST['donation_nonce']) || !wp_verify_nonce($_POST['donation_nonce'], 'kilismile_donation_nonce')) {
        error_log('Donation processing: Nonce verification failed');
        wp_die(__('Security check failed', 'kilismile'));
    }
    
    error_log('Donation processing: Nonce verified successfully');
    error_log('POST data: ' . print_r($_POST, true));
    
    // Sanitize and validate input
    $donation_data = array(
        'amount' => floatval($_POST['donation_amount']),
        'currency' => sanitize_text_field($_POST['donation_currency']),
        'first_name' => sanitize_text_field($_POST['donor_first_name']),
        'last_name' => sanitize_text_field($_POST['donor_last_name']),
        'email' => sanitize_email($_POST['donor_email']),
        'phone' => sanitize_text_field($_POST['donor_phone']),
        'message' => sanitize_textarea_field($_POST['donor_message']),
        'payment_method' => sanitize_text_field($_POST['payment_method']),
        'recurring' => isset($_POST['recurring_donation']) ? 1 : 0,
        'date' => current_time('mysql'),
        'status' => 'pending'
    );
    
    error_log('Donation data: ' . print_r($donation_data, true));
    
    // Validate required fields
    if (empty($donation_data['amount']) || $donation_data['amount'] < 1) {
        error_log('Donation processing: Invalid amount - ' . $donation_data['amount']);
        wp_redirect(add_query_arg('donation_error', 'invalid_amount', wp_get_referer()));
        exit;
    }
    
    if (empty($donation_data['currency']) || !in_array($donation_data['currency'], array('USD', 'TZS'))) {
        error_log('Donation processing: Invalid currency - ' . $donation_data['currency']);
        wp_redirect(add_query_arg('donation_error', 'invalid_currency', wp_get_referer()));
        exit;
    }
    
    if (empty($donation_data['first_name']) || empty($donation_data['last_name']) || empty($donation_data['email'])) {
        error_log('Donation processing: Missing required info');
        wp_redirect(add_query_arg('donation_error', 'missing_info', wp_get_referer()));
        exit;
    }
    
    // Validate payment method availability based on currency
    $valid_methods = kilismile_get_available_payment_methods($donation_data['currency']);
    error_log('Available payment methods: ' . print_r($valid_methods, true));
    
    if (!in_array($donation_data['payment_method'], $valid_methods)) {
        error_log('Donation processing: Invalid payment method - ' . $donation_data['payment_method']);
        wp_redirect(add_query_arg('donation_error', 'invalid_payment_method', wp_get_referer()));
        exit;
    }
    
    // Store donation in database
    $donation_id = kilismile_store_donation_legacy($donation_data);
    error_log('Donation stored with ID: ' . $donation_id);
    
    if (!$donation_id) {
        error_log('Donation processing: Storage failed');
        wp_redirect(add_query_arg('donation_error', 'storage_failed', wp_get_referer()));
        exit;
    }
    
    // Add ID to donation data for email
    $donation_data['id'] = $donation_id;
    $donation_data['created_at'] = $donation_data['date'];
    
    // Send confirmation email using the enhanced email system
    if (function_exists('kilismile_send_donation_confirmation')) {
        error_log('Sending donation confirmation email');
        kilismile_send_donation_confirmation($donation_data['email'], $donation_data);
    } else {
        error_log('Donation confirmation email function not found');
    }
    
    // Process payment based on method and currency
    error_log('Processing payment method: ' . $donation_data['payment_method']);
    
    switch ($donation_data['payment_method']) {
        // International Methods (USD)
        case 'paypal':
            $redirect_url = kilismile_process_paypal_donation($donation_data, $donation_id);
            break;
        case 'stripe':
            $redirect_url = kilismile_process_stripe_donation($donation_data, $donation_id);
            break;
        case 'wire_transfer':
            $redirect_url = kilismile_process_wire_transfer_donation($donation_data, $donation_id);
            break;
            
        // Local Methods (TZS)
        case 'mpesa':
            $redirect_url = kilismile_process_mpesa_donation($donation_data, $donation_id);
            break;
        case 'tigo_pesa':
            $redirect_url = kilismile_process_tigo_pesa_donation($donation_data, $donation_id);
            break;
        case 'airtel_money':
            $redirect_url = kilismile_process_airtel_money_donation($donation_data, $donation_id);
            break;
        case 'selcom':
            $redirect_url = kilismile_process_selcom_donation($donation_data, $donation_id);
            break;
        case 'azam_pay':
            $redirect_url = kilismile_process_azam_pay_donation($donation_data, $donation_id);
            break;
        case 'local_bank':
            $redirect_url = kilismile_process_local_bank_donation($donation_data, $donation_id);
            break;
            
        default:
            error_log('Donation processing: Invalid payment method - ' . $donation_data['payment_method']);
            wp_redirect(add_query_arg('donation_error', 'invalid_method', wp_get_referer()));
            exit;
    }
    
    error_log('Redirecting to: ' . $redirect_url);
    wp_redirect($redirect_url);
    exit;
}
// Removed action hooks to avoid conflicts with the new system
// add_action('admin_post_kilismile_process_donation', 'kilismile_process_donation');
// add_action('admin_post_nopriv_kilismile_process_donation', 'kilismile_process_donation');

/**
 * Get Available Payment Methods Based on Currency
 * 
 * @deprecated Use the new OOP system instead
 */
function _kilismile_legacy_get_available_payment_methods($currency) {
    $methods = array();
    
    error_log('Checking payment methods for currency: ' . $currency);
    
    if ($currency === 'USD') {
        // International payment methods
        $paypal_enabled = get_option('kilismile_paypal_enabled', 0);
        $paypal_email = get_option('kilismile_paypal_email', '');
        error_log('PayPal - Enabled: ' . $paypal_enabled . ', Email: ' . $paypal_email);
        
        if ($paypal_enabled && !empty($paypal_email)) {
            $methods[] = 'paypal';
        }
        
        $stripe_enabled = get_option('kilismile_stripe_enabled', 0);
        $stripe_public_key = get_option('kilismile_stripe_public_key', '');
        error_log('Stripe - Enabled: ' . $stripe_enabled . ', Public Key: ' . (!empty($stripe_public_key) ? 'Set' : 'Not set'));
        
        if ($stripe_enabled && !empty($stripe_public_key)) {
            $methods[] = 'stripe';
        }
        
        $wire_enabled = get_option('kilismile_wire_transfer_enabled', 0);
        $wire_details = get_option('kilismile_wire_transfer_details', '');
        error_log('Wire Transfer - Enabled: ' . $wire_enabled . ', Details: ' . (!empty($wire_details) ? 'Set' : 'Not set'));
        
        if ($wire_enabled && !empty($wire_details)) {
            $methods[] = 'wire_transfer';
        }
    } elseif ($currency === 'TZS') {
        // Local payment methods
        $mpesa_enabled = get_option('kilismile_mpesa_enabled', 0);
        $mpesa_number = get_option('kilismile_mpesa_number', '');
        error_log('M-Pesa - Enabled: ' . $mpesa_enabled . ', Number: ' . $mpesa_number);
        
        if ($mpesa_enabled && !empty($mpesa_number)) {
            $methods[] = 'mpesa';
        }
        
        $tigo_enabled = get_option('kilismile_tigo_pesa_enabled', 0);
        $tigo_number = get_option('kilismile_tigo_pesa_number', '');
        error_log('Tigo Pesa - Enabled: ' . $tigo_enabled . ', Number: ' . $tigo_number);
        
        if ($tigo_enabled && !empty($tigo_number)) {
            $methods[] = 'tigo_pesa';
        }
        
        $airtel_enabled = get_option('kilismile_airtel_money_enabled', 0);
        $airtel_number = get_option('kilismile_airtel_money_number', '');
        error_log('Airtel Money - Enabled: ' . $airtel_enabled . ', Number: ' . $airtel_number);
        
        if ($airtel_enabled && !empty($airtel_number)) {
            $methods[] = 'airtel_money';
        }
        
        // Selcom Payment Gateway
        $selcom_enabled = get_option('kilismile_selcom_enabled', 0);
        $selcom_key = get_option('kilismile_selcom_api_key', '');
        error_log('Selcom - Enabled: ' . $selcom_enabled . ', Key: ' . (!empty($selcom_key) ? 'Set' : 'Not set'));
        
        if ($selcom_enabled && !empty($selcom_key)) {
            $methods[] = 'selcom';
        }
        
        // Azam Pay Gateway
        $azam_enabled = get_option('kilismile_azam_pay_enabled', 0);
        $azam_key = get_option('kilismile_azam_api_key', '');
        error_log('Azam Pay - Enabled: ' . $azam_enabled . ', Key: ' . (!empty($azam_key) ? 'Set' : 'Not set'));
        
        if ($azam_enabled && !empty($azam_key)) {
            $methods[] = 'azam_pay';
        }
        
        $bank_enabled = get_option('kilismile_local_bank_enabled', 0);
        $bank_details = get_option('kilismile_local_bank_details', '');
        error_log('Local Bank - Enabled: ' . $bank_enabled . ', Details: ' . (!empty($bank_details) ? 'Set' : 'Not set'));
        
        if ($bank_enabled && !empty($bank_details)) {
            $methods[] = 'local_bank';
        }
    }
    
    error_log('Available payment methods: ' . print_r($methods, true));
    return $methods;
}

/**
 * Store Donation in Database
 * 
 * @deprecated Use the function in bridge-functions.php instead
 * This is kept for legacy compatibility only
 */
function kilismile_store_donation_legacy($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    // Create table if it doesn't exist
    kilismile_create_donations_table_legacy();
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'payment_method' => $data['payment_method'],
            'recurring' => $data['recurring'],
            'status' => $data['status'],
            'created_at' => $data['date']
        ),
        array('%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
    );
    
    return $result ? $wpdb->insert_id : false;
}

/**
 * Create Donations Table
 * 
 * @deprecated Use the new OOP system instead
 * This is kept for legacy compatibility only
 */
function kilismile_create_donations_table_legacy() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        amount decimal(10,2) NOT NULL,
        currency varchar(3) NOT NULL DEFAULT 'USD',
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50),
        message text,
        payment_method varchar(50) NOT NULL,
        recurring tinyint(1) DEFAULT 0,
        status varchar(50) DEFAULT 'pending',
        transaction_id varchar(255),
        gateway_response text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        INDEX email_idx (email),
        INDEX status_idx (status),
        INDEX currency_idx (currency),
        INDEX payment_method_idx (payment_method),
        INDEX created_at_idx (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Process PayPal Donation
 */
function kilismile_process_paypal_donation($data, $donation_id) {
    $paypal_email = get_option('kilismile_paypal_email');
    
    if (!$paypal_email) {
        return add_query_arg('donation_error', 'paypal_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    $return_url = add_query_arg(array(
        'donation_success' => '1',
        'donation_id' => $donation_id
    ), kilismile_get_donation_page_url_legacy());
    
    $cancel_url = add_query_arg('donation_cancelled', '1', kilismile_get_donation_page_url_legacy());
    
    $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    $params = array(
        'cmd' => '_donations',
        'business' => $paypal_email,
        'item_name' => 'Donation to Kilismile Organization',
        'amount' => $data['amount'],
        'currency_code' => $data['currency'],
        'custom' => $donation_id,
        'return' => $return_url,
        'cancel_return' => $cancel_url,
        'notify_url' => home_url('/wp-admin/admin-post.php?action=kilismile_paypal_ipn')
    );
    
    return $paypal_url . '?' . http_build_query($params);
}

/**
 * Process Stripe Donation
 */
function kilismile_process_stripe_donation($data, $donation_id) {
    if (!get_option('kilismile_stripe_enabled') || !get_option('kilismile_stripe_public_key')) {
        return add_query_arg('donation_error', 'stripe_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Store stripe session data for processing
    update_option('kilismile_stripe_pending_' . $donation_id, array(
        'amount' => $data['amount'] * 100, // Stripe uses cents
        'currency' => strtolower($data['currency']),
        'description' => 'Donation to Kilismile Organization',
        'donor_name' => $data['first_name'] . ' ' . $data['last_name'],
        'donor_email' => $data['email']
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'stripe'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Process Wire Transfer Donation
 */
function kilismile_process_wire_transfer_donation($data, $donation_id) {
    $wire_details = get_option('kilismile_wire_transfer_details');
    
    if (!$wire_details) {
        return add_query_arg('donation_error', 'wire_transfer_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'wire_transfer'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Process M-Pesa Donation
 */
function kilismile_process_mpesa_donation($data, $donation_id) {
    $mpesa_number = get_option('kilismile_mpesa_number');
    $mpesa_name = get_option('kilismile_mpesa_name', 'Kilismile Organization');
    
    if (!$mpesa_number) {
        return add_query_arg('donation_error', 'mpesa_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Store instructions for M-Pesa payment
    update_option('kilismile_mpesa_instructions_' . $donation_id, array(
        'number' => $mpesa_number,
        'name' => $mpesa_name,
        'amount' => $data['amount'],
        'reference' => 'KILI' . $donation_id,
        'currency' => $data['currency']
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'mpesa'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Process Tigo Pesa Donation
 */
function kilismile_process_tigo_pesa_donation($data, $donation_id) {
    $tigo_number = get_option('kilismile_tigo_pesa_number');
    
    if (!$tigo_number) {
        return add_query_arg('donation_error', 'tigo_pesa_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Store instructions for Tigo Pesa payment
    update_option('kilismile_tigo_pesa_instructions_' . $donation_id, array(
        'number' => $tigo_number,
        'amount' => $data['amount'],
        'reference' => 'KILI' . $donation_id,
        'currency' => $data['currency']
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'tigo_pesa'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Process Airtel Money Donation
 */
function kilismile_process_airtel_money_donation($data, $donation_id) {
    $airtel_number = get_option('kilismile_airtel_money_number');
    
    if (!$airtel_number) {
        return add_query_arg('donation_error', 'airtel_money_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Store instructions for Airtel Money payment
    update_option('kilismile_airtel_money_instructions_' . $donation_id, array(
        'number' => $airtel_number,
        'amount' => $data['amount'],
        'reference' => 'KILI' . $donation_id,
        'currency' => $data['currency']
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'airtel_money'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Process Local Bank Transfer Donation
 */
function kilismile_process_local_bank_donation($data, $donation_id) {
    $bank_details = get_option('kilismile_local_bank_details');
    
    if (!$bank_details) {
        return add_query_arg('donation_error', 'local_bank_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'local_bank'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Get the correct donation page URL
 * 
 * @deprecated Use the function in bridge-functions.php instead
 * This is kept for legacy compatibility only
 */
function kilismile_get_donation_page_url_legacy() {
    // Try to find an actual donation page
    $donation_page = get_page_by_path('donation');
    if (!$donation_page) {
        $donation_page = get_page_by_path('donate');
    }
    if (!$donation_page) {
        $donation_page = get_page_by_path('donations');
    }
    
    // If we found a page, return its permalink
    if ($donation_page) {
        return get_permalink($donation_page->ID);
    }
    
    // Fallback: try to find pages using the donation template
    $pages = get_pages();
    foreach ($pages as $page) {
        $template = get_page_template_slug($page->ID);
        if (strpos($template, 'donation') !== false) {
            return get_permalink($page->ID);
        }
    }
    
    // Final fallback
    return home_url('/donation/');
}

/**
 * Process Selcom Payment Donation
 */
function kilismile_process_selcom_donation($data, $donation_id) {
    $selcom_key = get_option('kilismile_selcom_api_key');
    $selcom_secret = get_option('kilismile_selcom_api_secret');
    $vendor_id = get_option('kilismile_selcom_vendor_id');
    
    if (!$selcom_key || !$selcom_secret || !$vendor_id) {
        return add_query_arg('donation_error', 'selcom_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Create Selcom checkout order
    $order_data = array(
        'vendor' => $vendor_id,
        'order_id' => 'KILI_' . $donation_id,
        'buyer_email' => $data['email'],
        'buyer_name' => $data['first_name'] . ' ' . $data['last_name'],
        'buyer_phone' => $data['phone'],
        'amount' => floatval($data['amount']),
        'currency' => $data['currency'],
        'payment_methods' => 'ALL',
        'redirect_url' => base64_encode(add_query_arg(array(
            'donation_success' => '1',
            'donation_id' => $donation_id,
            'payment_method' => 'selcom'
        ), kilismile_get_donation_page_url_legacy())),
        'cancel_url' => base64_encode(add_query_arg('donation_cancelled', '1', kilismile_get_donation_page_url_legacy())),
        'webhook' => base64_encode(home_url('/wp-admin/admin-post.php?action=kilismile_selcom_webhook')),
        'buyer_remarks' => 'Donation to Kilismile Organization',
        'merchant_remarks' => 'Donation ID: ' . $donation_id,
        'no_of_items' => 1,
        'header_colour' => '#0066cc',
        'button_colour' => '#0066cc',
        'expiry' => 30 // 30 minutes
    );
    
    // Call Selcom API to create order
    $response = kilismile_selcom_api_request('POST', '/v1/checkout/create-order-minimal', $order_data, $selcom_key, $selcom_secret);
    
    if ($response && isset($response['resultcode']) && $response['resultcode'] === '000') {
        // Store order data for tracking
        update_option('kilismile_selcom_order_' . $donation_id, array(
            'order_id' => 'KILI_' . $donation_id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'donor_email' => $data['email'],
            'donor_name' => $data['first_name'] . ' ' . $data['last_name'],
            'created_at' => current_time('mysql')
        ));
        
        // Redirect to Selcom payment gateway
        if (isset($response['data'][0]['payment_gateway_url'])) {
            return base64_decode($response['data'][0]['payment_gateway_url']);
        }
    }
    
    // If failed, redirect with error
    return add_query_arg('donation_error', 'selcom_order_failed', kilismile_get_donation_page_url_legacy());
}

/**
 * Make authenticated API request to Selcom
 */
function kilismile_selcom_api_request($method, $endpoint, $data, $api_key, $api_secret) {
    $base_url = 'https://apigw.selcommobile.com';
    $url = $base_url . $endpoint;
    
    // Generate timestamp in ISO 8601 format
    $timestamp = date('c');
    
    // Create signed fields string from data keys
    $signed_fields = implode(',', array_keys($data));
    
    // Create digest string for signing
    $digest_string = 'timestamp=' . $timestamp;
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $subkey => $subvalue) {
                $digest_string .= '&' . $key . '.' . $subkey . '=' . $subvalue;
            }
        } else {
            $digest_string .= '&' . $key . '=' . $value;
        }
    }
    
    // Generate HMAC SHA256 digest
    $digest = base64_encode(hash_hmac('sha256', $digest_string, $api_secret, true));
    
    // Prepare headers
    $headers = array(
        'Content-Type: application/json',
        'Authorization: SELCOM ' . base64_encode($api_key),
        'Digest-Method: HS256',
        'Digest: ' . $digest,
        'Timestamp: ' . $timestamp,
        'Signed-Fields: ' . $signed_fields
    );
    
    // Make the API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log the response for debugging
    error_log('Selcom API Response: ' . $response);
    
    if ($response && $http_code === 200) {
        return json_decode($response, true);
    }
    
    return false;
}

/**
 * Handle Selcom webhook notifications
 */
function _kilismile_legacy_handle_selcom_webhook() {
    // Verify the request is from Selcom (you should implement proper signature verification)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['order_id']) || !isset($data['payment_status'])) {
        http_response_code(400);
        exit('Invalid webhook data');
    }
    
    // Extract donation ID from order ID
    $order_id = $data['order_id'];
    if (strpos($order_id, 'KILI_') !== 0) {
        http_response_code(400);
        exit('Invalid order ID format');
    }
    
    $donation_id = str_replace('KILI_', '', $order_id);
    
    // Get stored order data
    $order_data = get_option('kilismile_selcom_order_' . $donation_id);
    if (!$order_data) {
        http_response_code(404);
        exit('Order not found');
    }
    
    // Process payment status
    if ($data['payment_status'] === 'COMPLETED' && $data['result'] === 'SUCCESS') {
        // Payment successful
        $donation_data = array(
            'donation_id' => $donation_id,
            'amount' => $order_data['amount'],
            'currency' => $order_data['currency'],
            'payment_method' => 'selcom',
            'transaction_id' => isset($data['transid']) ? $data['transid'] : '',
            'reference' => isset($data['reference']) ? $data['reference'] : '',
            'status' => 'completed',
            'donor_email' => $order_data['donor_email'],
            'donor_name' => $order_data['donor_name'],
            'completed_at' => current_time('mysql')
        );
        
        // Save donation record
        kilismile_save_donation_record($donation_data);
        
        // Send confirmation email
        kilismile_send_donation_confirmation_email($donation_data);
        
        // Clean up temporary data
        delete_option('kilismile_selcom_order_' . $donation_id);
        
        // Log successful payment
        error_log('Selcom payment completed for donation ID: ' . $donation_id);
        
    } elseif ($data['payment_status'] === 'CANCELLED' || $data['payment_status'] === 'USERCANCELLED') {
        // Payment cancelled
        error_log('Selcom payment cancelled for donation ID: ' . $donation_id);
        
    } else {
        // Payment failed or other status
        error_log('Selcom payment failed for donation ID: ' . $donation_id . ', Status: ' . $data['payment_status']);
    }
    
    // Return success response to Selcom
    http_response_code(200);
    exit('OK');
}

// Register webhook handler
// Comment out the old webhook handlers as they will be handled by the new system
// add_action('wp_ajax_nopriv_kilismile_selcom_webhook', '_kilismile_legacy_handle_selcom_webhook');
// add_action('wp_ajax_kilismile_selcom_webhook', '_kilismile_legacy_handle_selcom_webhook');

/**
 * Save donation record to database
 */
function kilismile_save_donation_record($donation_data) {
    global $wpdb;
    
    // Create donations table if it doesn't exist
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        donation_id varchar(100) NOT NULL,
        amount decimal(10,2) NOT NULL,
        currency varchar(10) NOT NULL,
        payment_method varchar(50) NOT NULL,
        transaction_id varchar(255) DEFAULT '',
        reference varchar(255) DEFAULT '',
        status varchar(50) NOT NULL,
        donor_name varchar(255) NOT NULL,
        donor_email varchar(255) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        completed_at datetime DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY donation_id (donation_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Insert or update donation record
    $wpdb->replace(
        $table_name,
        array(
            'donation_id' => $donation_data['donation_id'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'payment_method' => $donation_data['payment_method'],
            'transaction_id' => $donation_data['transaction_id'],
            'reference' => $donation_data['reference'],
            'status' => $donation_data['status'],
            'donor_name' => $donation_data['donor_name'],
            'donor_email' => $donation_data['donor_email'],
            'completed_at' => $donation_data['completed_at']
        ),
        array(
            '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
        )
    );
}

/**
 * Send donation confirmation email
 */
function kilismile_send_donation_confirmation_email($donation_data) {
    $to = $donation_data['donor_email'];
    $subject = 'Thank you for your donation to Kilismile Organization';
    
    $message = sprintf(
        "Dear %s,\n\n" .
        "Thank you for your generous donation to Kilismile Organization!\n\n" .
        "Donation Details:\n" .
        "Amount: %s %s\n" .
        "Donation ID: %s\n" .
        "Transaction ID: %s\n" .
        "Payment Method: %s\n" .
        "Date: %s\n\n" .
        "Your support helps us make a difference in the community.\n\n" .
        "Best regards,\n" .
        "Kilismile Organization Team",
        $donation_data['donor_name'],
        $donation_data['amount'],
        $donation_data['currency'],
        $donation_data['donation_id'],
        $donation_data['transaction_id'],
        ucfirst(str_replace('_', ' ', $donation_data['payment_method'])),
        $donation_data['completed_at']
    );
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($to, $subject, $message, $headers);
}

/**
 * Process Azam Pay Donation
 */
function kilismile_process_azam_pay_donation($data, $donation_id) {
    $azam_key = get_option('kilismile_azam_api_key');
    
    if (!$azam_key) {
        return add_query_arg('donation_error', 'azam_pay_not_configured', kilismile_get_donation_page_url_legacy());
    }
    
    // Store Azam Pay payment data for processing
    update_option('kilismile_azam_pay_pending_' . $donation_id, array(
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'description' => 'Donation to Kilismile Organization',
        'donor_name' => $data['first_name'] . ' ' . $data['last_name'],
        'donor_email' => $data['email'],
        'donor_phone' => $data['phone'],
        'reference' => 'KILI' . $donation_id
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'azam_pay'
    ), kilismile_get_donation_page_url_legacy());
}

/**
 * Donation Shortcode
 */
function _kilismile_legacy_donation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Make a Donation', 'kilismile'),
        'show_amounts' => 'true',
        'show_progress' => 'true',
        'style' => 'default'
    ), $atts);
    
    $args = array(
        'title' => $atts['title'],
        'show_amounts' => $atts['show_amounts'] === 'true',
        'show_progress' => $atts['show_progress'] === 'true',
        'style' => $atts['style']
    );
    
    return kilismile_donation_form($args);
}
// Comment out the old shortcode registration since it will be handled by the bridge system
// add_shortcode('kilismile_donation', '_kilismile_legacy_donation_shortcode');

/**
 * Initialize default payment settings
 */
function _kilismile_legacy_init_default_payment_settings() {
    // Ensure donations are enabled by default
    if (get_option('kilismile_enable_donations') === false) {
        update_option('kilismile_enable_donations', 1);
    }
    
    // Set up default M-Pesa configuration - default to DISABLED
    if (get_option('kilismile_mpesa_enabled') === false || get_option('kilismile_mpesa_enabled') === '') {
        update_option('kilismile_mpesa_enabled', 0); // Changed from 1 to 0 to default to disabled
    }
    if (!get_option('kilismile_mpesa_number')) {
        update_option('kilismile_mpesa_number', '+255763495575');
    }
    if (!get_option('kilismile_mpesa_name')) {
        update_option('kilismile_mpesa_name', 'Kilismile Organization');
    }
    
    // Set up additional local payment methods with sample data - default to DISABLED
    if (get_option('kilismile_tigo_pesa_enabled') === false || get_option('kilismile_tigo_pesa_enabled') === '') {
        update_option('kilismile_tigo_pesa_enabled', 0); // Changed from 1 to 0 to default to disabled
    }
    if (!get_option('kilismile_tigo_pesa_number')) {
        update_option('kilismile_tigo_pesa_number', '+255763495575');
    }
    
    if (get_option('kilismile_airtel_money_enabled') === false || get_option('kilismile_airtel_money_enabled') === '') {
        update_option('kilismile_airtel_money_enabled', 0); // Changed from 1 to 0 to default to disabled
    }
    if (!get_option('kilismile_airtel_money_number')) {
        update_option('kilismile_airtel_money_number', '+255763495575');
    }
    
    if (get_option('kilismile_local_bank_enabled') === false || get_option('kilismile_local_bank_enabled') === '') {
        update_option('kilismile_local_bank_enabled', 0); // Changed from 1 to 0 to default to disabled
    }
    if (!get_option('kilismile_local_bank_details')) {
        update_option('kilismile_local_bank_details', "Bank Name: CRDB Bank\nAccount Name: Kilismile Organization\nAccount Number: 0150123456789\nSwift Code: CORUTZTZ\nBranch: Dar es Salaam\n\nReference: KILI[DONATION_ID]");
    }
    
    // Set up Selcom Payment Gateway - already defaulting to disabled
    if (get_option('kilismile_selcom_enabled') === false || get_option('kilismile_selcom_enabled') === '') {
        update_option('kilismile_selcom_enabled', 0);
    }
    if (!get_option('kilismile_selcom_api_key')) {
        update_option('kilismile_selcom_api_key', 'SELCOMPUBK_[YOUR_KEY_HERE]');
    }
    
    // Set up Azam Pay Gateway - already defaulting to disabled
    if (get_option('kilismile_azam_pay_enabled') === false || get_option('kilismile_azam_pay_enabled') === '') {
        update_option('kilismile_azam_pay_enabled', 0);
    }
    if (!get_option('kilismile_azam_api_key')) {
        update_option('kilismile_azam_api_key', 'AZAM_API_[YOUR_KEY_HERE]');
    }
    
    // Additional international methods - default to DISABLED
    if (get_option('kilismile_paypal_enabled') === false || get_option('kilismile_paypal_enabled') === '') {
        update_option('kilismile_paypal_enabled', 0);
    }
    
    if (get_option('kilismile_stripe_enabled') === false || get_option('kilismile_stripe_enabled') === '') {
        update_option('kilismile_stripe_enabled', 0);
    }
    
    if (get_option('kilismile_wire_transfer_enabled') === false || get_option('kilismile_wire_transfer_enabled') === '') {
        update_option('kilismile_wire_transfer_enabled', 0);
    }
    
    // Set default currency to TZS since this is a Tanzanian organization
    if (!get_option('kilismile_default_currency')) {
        update_option('kilismile_default_currency', 'TZS');
    }
}
// Comment out since this will be handled by the new system
// add_action('after_setup_theme', '_kilismile_legacy_init_default_payment_settings');
// Force initialization has been moved to test file

/**
 * Force initialize payment settings (for manual setup)
 */
function kilismile_force_init_payment_settings() {
    error_log('Forcing payment settings initialization...');
    
    // Enable donations system
    update_option('kilismile_enable_donations', 1);
    error_log('Donations enabled: ' . get_option('kilismile_enable_donations'));
    
    // M-Pesa Setup - default to DISABLED
    update_option('kilismile_mpesa_enabled', 0);
    update_option('kilismile_mpesa_number', '+255763495575');
    update_option('kilismile_mpesa_name', 'Kilismile Organization');
    error_log('M-Pesa setup: Enabled=' . get_option('kilismile_mpesa_enabled') . ', Number=' . get_option('kilismile_mpesa_number'));
    
    // Tigo Pesa Setup - default to DISABLED
    update_option('kilismile_tigo_pesa_enabled', 0);
    update_option('kilismile_tigo_pesa_number', '+255763495575');
    error_log('Tigo Pesa setup: Enabled=' . get_option('kilismile_tigo_pesa_enabled') . ', Number=' . get_option('kilismile_tigo_pesa_number'));
    
    // Airtel Money Setup - default to DISABLED
    update_option('kilismile_airtel_money_enabled', 0);
    update_option('kilismile_airtel_money_number', '+255763495575');
    error_log('Airtel Money setup: Enabled=' . get_option('kilismile_airtel_money_enabled') . ', Number=' . get_option('kilismile_airtel_money_number'));
    
    // Local Bank Setup - default to DISABLED
    update_option('kilismile_local_bank_enabled', 0);
    update_option('kilismile_local_bank_details', "Bank Name: CRDB Bank\nAccount Name: Kilismile Organization\nAccount Number: 0150123456789\nSwift Code: CORUTZTZ\nBranch: Dar es Salaam\n\nReference: KILI[DONATION_ID]");
    error_log('Local Bank setup: Enabled=' . get_option('kilismile_local_bank_enabled') . ', Details=' . (get_option('kilismile_local_bank_details') ? 'Set' : 'Not set'));
    
    // Selcom Payment Gateway Setup - already defaults to DISABLED
    update_option('kilismile_selcom_enabled', 0);
    update_option('kilismile_selcom_api_key', 'SELCOMPUBK_[YOUR_KEY_HERE]');
    error_log('Selcom setup: Enabled=' . get_option('kilismile_selcom_enabled') . ', Key=' . (get_option('kilismile_selcom_api_key') ? 'Set' : 'Not set'));
    
    // Azam Pay Gateway Setup - already defaults to DISABLED
    update_option('kilismile_azam_pay_enabled', 0);
    update_option('kilismile_azam_api_key', 'AZAM_API_[YOUR_KEY_HERE]');
    error_log('Azam Pay setup: Enabled=' . get_option('kilismile_azam_pay_enabled') . ', Key=' . (get_option('kilismile_azam_api_key') ? 'Set' : 'Not set'));
    
    // PayPal - default to DISABLED
    update_option('kilismile_paypal_enabled', 0);
    error_log('PayPal setup: Enabled=' . get_option('kilismile_paypal_enabled'));
    
    // Stripe - default to DISABLED
    update_option('kilismile_stripe_enabled', 0);
    error_log('Stripe setup: Enabled=' . get_option('kilismile_stripe_enabled'));
    
    // Wire Transfer - default to DISABLED
    update_option('kilismile_wire_transfer_enabled', 0);
    error_log('Wire Transfer setup: Enabled=' . get_option('kilismile_wire_transfer_enabled'));
    
    // Set default currency
    update_option('kilismile_default_currency', 'TZS');
    error_log('Default currency set to: ' . get_option('kilismile_default_currency'));
    
    return true;
}

/**
 * Newsletter Subscription AJAX Handler
 */
function _kilismile_legacy_newsletter_subscribe() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_nonce')) {
        wp_send_json_error(__('Security check failed', 'kilismile'));
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(__('Please enter a valid email address', 'kilismile'));
    }
    
    // Store newsletter subscription
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_newsletter';
    
    // Create table if needed
    kilismile_create_simple_newsletter_table();
    
    // Check if email already exists
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE email = %s",
        $email
    ));
    
    if ($existing) {
        wp_send_json_error(__('This email is already subscribed', 'kilismile'));
    }
    
    // Insert new subscription
    $result = $wpdb->insert(
        $table_name,
        array(
            'email' => $email,
            'status' => 'active',
            'subscribed_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s')
    );
    
    if ($result) {
        // Send welcome email
        kilismile_send_simple_welcome_email($email);
        wp_send_json_success(__('Thank you for subscribing to our newsletter!', 'kilismile'));
    } else {
        wp_send_json_error(__('Subscription failed. Please try again.', 'kilismile'));
    }
}
// Comment out the legacy Ajax handlers
// add_action('wp_ajax_kilismile_newsletter_subscribe', '_kilismile_legacy_newsletter_subscribe');
// add_action('wp_ajax_nopriv_kilismile_newsletter_subscribe', '_kilismile_legacy_newsletter_subscribe');

/**
 * Create Simple Newsletter Table
 */
function kilismile_create_simple_newsletter_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_newsletter';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        status varchar(20) DEFAULT 'active',
        subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
        unsubscribed_at datetime NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email),
        INDEX status_idx (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Send Simple Welcome Email for Donation Subscribers
 */
function kilismile_send_simple_welcome_email($email) {
    $subject = __('Welcome to Kilismile Organization Newsletter', 'kilismile');
    $message = sprintf(
        __('Thank you for subscribing to our newsletter! You will receive updates about our health programs and impact in remote areas of Tanzania.

Best regards,
The Kilismile Organization Team

--
If you no longer wish to receive these emails, you can unsubscribe at: %s', 'kilismile'),
        home_url('/unsubscribe?email=' . urlencode($email))
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>'
    );
    
    wp_mail($email, $subject, $message, $headers);
}

/**
 * AJAX handler for getting donation details
 */
function _kilismile_legacy_get_donation_details() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'donation_admin_nonce')) {
        wp_send_json_error('Unauthorized access');
    }
    
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    // Get donation ID
    $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
    if (!$donation_id) {
        wp_send_json_error('Invalid donation ID');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    // Get donation data
    $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $donation_id));
    
    if (!$donation) {
        wp_send_json_error('Donation not found');
    }
    
    // Set default values for missing fields to avoid undefined property errors
    $donation_data = array(
        'id' => $donation->id,
        'amount' => $donation->amount,
        'currency' => isset($donation->currency) ? $donation->currency : 'TZS',
        'payment_method' => $donation->payment_method,
        'transaction_id' => isset($donation->transaction_id) ? $donation->transaction_id : '',
        'status' => isset($donation->status) ? $donation->status : 
                   (isset($donation->payment_status) ? $donation->payment_status : 'pending'),
        'donor_name' => isset($donation->donor_name) ? $donation->donor_name : 
                       (isset($donation->first_name) && isset($donation->last_name) ? 
                        $donation->first_name . ' ' . $donation->last_name : 'Unknown'),
        'donor_email' => isset($donation->donor_email) ? $donation->donor_email : 
                        (isset($donation->email) ? $donation->email : ''),
        'donor_phone' => isset($donation->donor_phone) ? $donation->donor_phone : 
                        (isset($donation->phone) ? $donation->phone : ''),
        'is_anonymous' => isset($donation->is_anonymous) ? (bool)$donation->is_anonymous : false,
        'donation_type' => isset($donation->donation_type) ? $donation->donation_type : 'general',
        'message' => isset($donation->message) ? $donation->message : 
                    (isset($donation->donation_message) ? $donation->donation_message : ''),
        'created_at' => $donation->created_at,
        'completed_at' => isset($donation->completed_at) ? $donation->completed_at : ''
    );
    
    wp_send_json_success($donation_data);
}
// Comment out admin Ajax handlers as they will be handled by the new system
// add_action('wp_ajax_get_donation_details', '_kilismile_legacy_get_donation_details');

/**
 * AJAX handler for marking a donation as completed
 */
function _kilismile_legacy_mark_donation_completed() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'donation_admin_nonce')) {
        wp_send_json_error('Unauthorized access');
    }
    
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    // Get donation ID
    $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
    if (!$donation_id) {
        wp_send_json_error('Invalid donation ID');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    // Update donation status
    $result = $wpdb->update(
        $table_name,
        array(
            'status' => 'completed',
            'completed_at' => current_time('mysql')
        ),
        array('id' => $donation_id),
        array('%s', '%s'),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error('Failed to update donation status');
    }
    
    wp_send_json_success('Donation marked as completed');
}
// Comment out admin Ajax handlers as they will be handled by the new system
// add_action('wp_ajax_mark_donation_completed', '_kilismile_legacy_mark_donation_completed');

?>


