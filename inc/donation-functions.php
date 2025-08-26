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

/**
 * Add Donation-related Customizer Settings
 */
function kilismile_donation_customizer($wp_customize) {
    // Donation Settings Section
    $wp_customize->add_section('kilismile_donation_settings', array(
        'title'    => __('Donation Settings', 'kilismile'),
        'priority' => 45,
    ));
    
    // Enable donation system
    $wp_customize->add_setting('kilismile_enable_donations', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_enable_donations', array(
        'label'   => __('Enable Donation System', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'checkbox',
    ));
    
    // Default Currency
    $wp_customize->add_setting('kilismile_default_currency', array(
        'default'           => 'USD',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_default_currency', array(
        'label'   => __('Default Currency', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'select',
        'choices' => array(
            'USD' => __('US Dollar (USD)', 'kilismile'),
            'TZS' => __('Tanzanian Shilling (TZS)', 'kilismile'),
        ),
    ));
    
    // Donation goal USD
    $wp_customize->add_setting('kilismile_donation_goal_usd', array(
        'default'           => '10000',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_donation_goal_usd', array(
        'label'   => __('Monthly Donation Goal (USD)', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'number',
    ));
    
    // Current donations USD
    $wp_customize->add_setting('kilismile_current_donations_usd', array(
        'default'           => '2500',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_current_donations_usd', array(
        'label'   => __('Current Monthly Donations (USD)', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'number',
    ));
    
    // Donation goal TZS
    $wp_customize->add_setting('kilismile_donation_goal_tzs', array(
        'default'           => '25000000',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_donation_goal_tzs', array(
        'label'   => __('Monthly Donation Goal (TZS)', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'number',
    ));
    
    // Current donations TZS
    $wp_customize->add_setting('kilismile_current_donations_tzs', array(
        'default'           => '6250000',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_current_donations_tzs', array(
        'label'   => __('Current Monthly Donations (TZS)', 'kilismile'),
        'section' => 'kilismile_donation_settings',
        'type'    => 'number',
    ));
    
    // Exchange Rate
    $wp_customize->add_setting('kilismile_exchange_rate', array(
        'default'           => '2500',
        'sanitize_callback' => 'floatval',
    ));
    
    $wp_customize->add_control('kilismile_exchange_rate', array(
        'label'       => __('Exchange Rate (TZS per USD)', 'kilismile'),
        'description' => __('Current exchange rate for currency conversion display', 'kilismile'),
        'section'     => 'kilismile_donation_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'step' => '0.01',
            'min'  => '1'
        ),
    ));
    
    // International Gateways Section
    $wp_customize->add_section('kilismile_international_gateways', array(
        'title'    => __('International Payment Gateways (USD)', 'kilismile'),
        'priority' => 46,
    ));
    
    // PayPal settings
    $wp_customize->add_setting('kilismile_paypal_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('kilismile_paypal_email', array(
        'label'   => __('PayPal Email', 'kilismile'),
        'section' => 'kilismile_international_gateways',
        'type'    => 'email',
    ));
    
    // Stripe settings
    $wp_customize->add_setting('kilismile_stripe_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_stripe_enabled', array(
        'label'   => __('Enable Stripe', 'kilismile'),
        'section' => 'kilismile_international_gateways',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_stripe_public_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_stripe_public_key', array(
        'label'   => __('Stripe Publishable Key', 'kilismile'),
        'section' => 'kilismile_international_gateways',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_stripe_secret_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_stripe_secret_key', array(
        'label'   => __('Stripe Secret Key', 'kilismile'),
        'section' => 'kilismile_international_gateways',
        'type'    => 'password',
    ));
    
    // Wire Transfer International
    $wp_customize->add_setting('kilismile_wire_transfer_details', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_wire_transfer_details', array(
        'label'   => __('International Wire Transfer Details', 'kilismile'),
        'section' => 'kilismile_international_gateways',
        'type'    => 'textarea',
    ));
    
    // Local Gateways Section  
    $wp_customize->add_section('kilismile_local_gateways', array(
        'title'    => __('Local Payment Gateways (TZS)', 'kilismile'),
        'priority' => 47,
    ));
    
    // M-Pesa settings
    $wp_customize->add_setting('kilismile_mpesa_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_mpesa_enabled', array(
        'label'   => __('Enable M-Pesa', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_mpesa_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_mpesa_number', array(
        'label'   => __('M-Pesa Business Number', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_mpesa_name', array(
        'default'           => 'Kili Smile Organization',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_mpesa_name', array(
        'label'   => __('M-Pesa Account Name', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'text',
    ));
    
    // Tigo Pesa settings
    $wp_customize->add_setting('kilismile_tigo_pesa_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_tigo_pesa_enabled', array(
        'label'   => __('Enable Tigo Pesa', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_tigo_pesa_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_tigo_pesa_number', array(
        'label'   => __('Tigo Pesa Number', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'text',
    ));
    
    // Airtel Money settings
    $wp_customize->add_setting('kilismile_airtel_money_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_airtel_money_enabled', array(
        'label'   => __('Enable Airtel Money', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_airtel_money_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_airtel_money_number', array(
        'label'   => __('Airtel Money Number', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'text',
    ));
    
    // Local Bank Transfer
    $wp_customize->add_setting('kilismile_local_bank_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_local_bank_enabled', array(
        'label'   => __('Enable Local Bank Transfer', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_local_bank_details', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_local_bank_details', array(
        'label'   => __('Local Bank Transfer Details (TZS)', 'kilismile'),
        'section' => 'kilismile_local_gateways',
        'type'    => 'textarea',
    ));
}
add_action('customize_register', 'kilismile_donation_customizer');

/**
 * Donation Progress Bar
 */
function kilismile_donation_progress_bar($currency = 'USD') {
    if (!get_theme_mod('kilismile_enable_donations', true)) {
        return '';
    }
    
    $currency = strtoupper($currency);
    $exchange_rate = get_theme_mod('kilismile_exchange_rate', 2500);
    
    if ($currency === 'USD') {
        $goal = get_theme_mod('kilismile_donation_goal_usd', 10000);
        $current = get_theme_mod('kilismile_current_donations_usd', 2500);
        $currency_symbol = '$';
        $currency_code = 'USD';
    } else {
        $goal = get_theme_mod('kilismile_donation_goal_tzs', 25000000);
        $current = get_theme_mod('kilismile_current_donations_tzs', 6250000);
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
 */
function kilismile_donation_form($args = array()) {
    if (!get_theme_mod('kilismile_enable_donations', true)) {
        return '';
    }
    
    $defaults = array(
        'title' => __('Make a Donation', 'kilismile'),
        'show_amounts' => true,
        'show_progress' => true,
        'style' => 'default'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Get default currency
    $default_currency = get_theme_mod('kilismile_default_currency', 'USD');
    $exchange_rate = get_theme_mod('kilismile_exchange_rate', 2500);
    
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
            <?php echo kilismile_donation_progress_bar($default_currency); ?>
        <?php endif; ?>
        
        <form class="donation-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="kilismile-donation-form">
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
                    <span id="payment-methods-title"><?php _e('International Payment Methods', 'kilismile'); ?></span>
                </h4>
                
                <!-- International Payment Methods (USD) -->
                <div class="international-methods">
                    <?php if (get_theme_mod('kilismile_paypal_email')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="paypal" style="margin-right: 10px;" required>
                                <i class="fab fa-paypal" style="color: #0070ba; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('PayPal', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php _e('Pay securely with PayPal', 'kilismile'); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('kilismile_stripe_enabled')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="stripe" style="margin-right: 10px;" required>
                                <i class="fab fa-stripe" style="color: #635bff; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('Credit/Debit Card', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php _e('Visa, Mastercard, American Express', 'kilismile'); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('kilismile_wire_transfer_details')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="wire_transfer" style="margin-right: 10px;" required>
                                <i class="fas fa-university" style="color: #333; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('Wire Transfer', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php _e('International bank transfer', 'kilismile'); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Local Payment Methods (TZS) -->
                <div class="local-methods" style="display: none;">
                    <?php if (get_theme_mod('kilismile_mpesa_enabled') && get_theme_mod('kilismile_mpesa_number')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="mpesa" style="margin-right: 10px;" required>
                                <i class="fas fa-mobile-alt" style="color: #00a651; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('M-Pesa', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php printf(__('Send to: %s', 'kilismile'), get_theme_mod('kilismile_mpesa_number')); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('kilismile_tigo_pesa_enabled') && get_theme_mod('kilismile_tigo_pesa_number')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="tigo_pesa" style="margin-right: 10px;" required>
                                <i class="fas fa-mobile-alt" style="color: #0066cc; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('Tigo Pesa', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php printf(__('Send to: %s', 'kilismile'), get_theme_mod('kilismile_tigo_pesa_number')); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('kilismile_airtel_money_enabled') && get_theme_mod('kilismile_airtel_money_number')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="airtel_money" style="margin-right: 10px;" required>
                                <i class="fas fa-mobile-alt" style="color: #ff0000; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('Airtel Money', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php printf(__('Send to: %s', 'kilismile'), get_theme_mod('kilismile_airtel_money_number')); ?></div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('kilismile_local_bank_enabled') && get_theme_mod('kilismile_local_bank_details')) : ?>
                        <div class="payment-method" style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;">
                                <input type="radio" name="payment_method" value="local_bank" style="margin-right: 10px;" required>
                                <i class="fas fa-university" style="color: #333; font-size: 1.5rem; margin-right: 10px;"></i>
                                <div>
                                    <span style="font-weight: 600;"><?php _e('Local Bank Transfer', 'kilismile'); ?></span>
                                    <div style="font-size: 0.8rem; color: #666;"><?php _e('Tanzanian bank transfer', 'kilismile'); ?></div>
                                </div>
                            </label>
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
        
        .payment-method:hover label {
            border-color: var(--primary-green);
            background: rgba(76, 175, 80, 0.05);
        }
        
        .payment-method input[type="radio"]:checked + i + div {
            color: var(--primary-green);
        }
        
        .payment-method input[type="radio"]:checked ~ * {
            font-weight: 600;
        }
        
        .donation-form button[type="submit"]:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        #donation_currency {
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23666" d="m0,1l2,2l2,-2z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            appearance: none;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                
                if (currency === 'USD') {
                    // Update labels and symbols
                    currencySymbol.textContent = '$';
                    amountLabel.textContent = '<?php _e('Select Amount (USD)', 'kilismile'); ?>';
                    customAmountLabel.textContent = '<?php _e('Or enter custom amount (USD):', 'kilismile'); ?>';
                    currencyNote.textContent = '<?php _e('International payment methods available', 'kilismile'); ?>';
                    paymentMethodsTitle.textContent = '<?php _e('International Payment Methods', 'kilismile'); ?>';
                    donateButtonText.textContent = '<?php _e('Donate Now (USD)', 'kilismile'); ?>';
                    securityMessage.textContent = '<?php _e('Your donation is secure and processed through international payment gateways.', 'kilismile'); ?>';
                    
                    // Show/hide amounts
                    usdAmounts.style.display = 'grid';
                    tzsAmounts.style.display = 'none';
                    
                    // Show/hide payment methods
                    internationalMethods.style.display = 'block';
                    localMethods.style.display = 'none';
                    
                    // Clear TZS selections
                    tzsAmounts.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                    
                } else if (currency === 'TZS') {
                    // Update labels and symbols
                    currencySymbol.textContent = 'TZS';
                    amountLabel.textContent = '<?php _e('Select Amount (TZS)', 'kilismile'); ?>';
                    customAmountLabel.textContent = '<?php _e('Or enter custom amount (TZS):', 'kilismile'); ?>';
                    currencyNote.textContent = '<?php _e('Local Tanzanian payment methods available', 'kilismile'); ?>';
                    paymentMethodsTitle.textContent = '<?php _e('Local Payment Methods', 'kilismile'); ?>';
                    donateButtonText.textContent = '<?php _e('Donate Now (TZS)', 'kilismile'); ?>';
                    securityMessage.textContent = '<?php _e('Your donation is secure and processed through trusted local payment providers.', 'kilismile'); ?>';
                    
                    // Show/hide amounts
                    usdAmounts.style.display = 'none';
                    tzsAmounts.style.display = 'grid';
                    
                    // Show/hide payment methods
                    internationalMethods.style.display = 'none';
                    localMethods.style.display = 'block';
                    
                    // Clear USD selections
                    usdAmounts.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
                }
                
                // Clear amount input and conversion
                amountInput.value = '';
                conversionDisplay.textContent = '';
                
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
 */
function kilismile_process_donation() {
    // Verify nonce
    if (!isset($_POST['donation_nonce']) || !wp_verify_nonce($_POST['donation_nonce'], 'kilismile_donation_nonce')) {
        wp_die(__('Security check failed', 'kilismile'));
    }
    
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
    
    // Validate required fields
    if (empty($donation_data['amount']) || $donation_data['amount'] < 1) {
        wp_redirect(add_query_arg('donation_error', 'invalid_amount', wp_get_referer()));
        exit;
    }
    
    if (empty($donation_data['currency']) || !in_array($donation_data['currency'], array('USD', 'TZS'))) {
        wp_redirect(add_query_arg('donation_error', 'invalid_currency', wp_get_referer()));
        exit;
    }
    
    if (empty($donation_data['first_name']) || empty($donation_data['last_name']) || empty($donation_data['email'])) {
        wp_redirect(add_query_arg('donation_error', 'missing_info', wp_get_referer()));
        exit;
    }
    
    // Validate payment method availability based on currency
    $valid_methods = kilismile_get_available_payment_methods($donation_data['currency']);
    if (!in_array($donation_data['payment_method'], $valid_methods)) {
        wp_redirect(add_query_arg('donation_error', 'invalid_payment_method', wp_get_referer()));
        exit;
    }
    
    // Store donation in database
    $donation_id = kilismile_store_donation($donation_data);
    
    if (!$donation_id) {
        wp_redirect(add_query_arg('donation_error', 'storage_failed', wp_get_referer()));
        exit;
    }
    
    // Add ID to donation data for email
    $donation_data['id'] = $donation_id;
    $donation_data['created_at'] = $donation_data['date'];
    
    // Send confirmation email using the enhanced email system
    if (function_exists('kilismile_send_donation_confirmation')) {
        kilismile_send_donation_confirmation($donation_data['email'], $donation_data);
    }
    
    // Process payment based on method and currency
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
        case 'local_bank':
            $redirect_url = kilismile_process_local_bank_donation($donation_data, $donation_id);
            break;
            
        default:
            wp_redirect(add_query_arg('donation_error', 'invalid_method', wp_get_referer()));
            exit;
    }
    
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_kilismile_process_donation', 'kilismile_process_donation');
add_action('admin_post_nopriv_kilismile_process_donation', 'kilismile_process_donation');

/**
 * Get Available Payment Methods Based on Currency
 */
function kilismile_get_available_payment_methods($currency) {
    $methods = array();
    
    if ($currency === 'USD') {
        // International payment methods
        if (get_theme_mod('kilismile_paypal_email')) {
            $methods[] = 'paypal';
        }
        if (get_theme_mod('kilismile_stripe_enabled')) {
            $methods[] = 'stripe';
        }
        if (get_theme_mod('kilismile_wire_transfer_details')) {
            $methods[] = 'wire_transfer';
        }
    } elseif ($currency === 'TZS') {
        // Local payment methods
        if (get_theme_mod('kilismile_mpesa_enabled') && get_theme_mod('kilismile_mpesa_number')) {
            $methods[] = 'mpesa';
        }
        if (get_theme_mod('kilismile_tigo_pesa_enabled') && get_theme_mod('kilismile_tigo_pesa_number')) {
            $methods[] = 'tigo_pesa';
        }
        if (get_theme_mod('kilismile_airtel_money_enabled') && get_theme_mod('kilismile_airtel_money_number')) {
            $methods[] = 'airtel_money';
        }
        if (get_theme_mod('kilismile_local_bank_enabled') && get_theme_mod('kilismile_local_bank_details')) {
            $methods[] = 'local_bank';
        }
    }
    
    return $methods;
}

/**
 * Store Donation in Database
 */
function kilismile_store_donation($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_donations';
    
    // Create table if it doesn't exist
    kilismile_create_donations_table();
    
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
 */
function kilismile_create_donations_table() {
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
    $paypal_email = get_theme_mod('kilismile_paypal_email');
    
    if (!$paypal_email) {
        return add_query_arg('donation_error', 'paypal_not_configured', home_url('/donate'));
    }
    
    $return_url = add_query_arg(array(
        'donation_success' => '1',
        'donation_id' => $donation_id
    ), home_url('/donate'));
    
    $cancel_url = add_query_arg('donation_cancelled', '1', home_url('/donate'));
    
    $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    $params = array(
        'cmd' => '_donations',
        'business' => $paypal_email,
        'item_name' => 'Donation to Kili Smile Organization',
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
    if (!get_theme_mod('kilismile_stripe_enabled') || !get_theme_mod('kilismile_stripe_public_key')) {
        return add_query_arg('donation_error', 'stripe_not_configured', home_url('/donate'));
    }
    
    // Store stripe session data for processing
    update_option('kilismile_stripe_pending_' . $donation_id, array(
        'amount' => $data['amount'] * 100, // Stripe uses cents
        'currency' => strtolower($data['currency']),
        'description' => 'Donation to Kili Smile Organization',
        'donor_name' => $data['first_name'] . ' ' . $data['last_name'],
        'donor_email' => $data['email']
    ));
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'stripe'
    ), home_url('/donate'));
}

/**
 * Process Wire Transfer Donation
 */
function kilismile_process_wire_transfer_donation($data, $donation_id) {
    $wire_details = get_theme_mod('kilismile_wire_transfer_details');
    
    if (!$wire_details) {
        return add_query_arg('donation_error', 'wire_transfer_not_configured', home_url('/donate'));
    }
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'wire_transfer'
    ), home_url('/donate'));
}

/**
 * Process M-Pesa Donation
 */
function kilismile_process_mpesa_donation($data, $donation_id) {
    $mpesa_number = get_theme_mod('kilismile_mpesa_number');
    $mpesa_name = get_theme_mod('kilismile_mpesa_name', 'Kili Smile Organization');
    
    if (!$mpesa_number) {
        return add_query_arg('donation_error', 'mpesa_not_configured', home_url('/donate'));
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
    ), home_url('/donate'));
}

/**
 * Process Tigo Pesa Donation
 */
function kilismile_process_tigo_pesa_donation($data, $donation_id) {
    $tigo_number = get_theme_mod('kilismile_tigo_pesa_number');
    
    if (!$tigo_number) {
        return add_query_arg('donation_error', 'tigo_pesa_not_configured', home_url('/donate'));
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
    ), home_url('/donate'));
}

/**
 * Process Airtel Money Donation
 */
function kilismile_process_airtel_money_donation($data, $donation_id) {
    $airtel_number = get_theme_mod('kilismile_airtel_money_number');
    
    if (!$airtel_number) {
        return add_query_arg('donation_error', 'airtel_money_not_configured', home_url('/donate'));
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
    ), home_url('/donate'));
}

/**
 * Process Local Bank Transfer Donation
 */
function kilismile_process_local_bank_donation($data, $donation_id) {
    $bank_details = get_theme_mod('kilismile_local_bank_details');
    
    if (!$bank_details) {
        return add_query_arg('donation_error', 'local_bank_not_configured', home_url('/donate'));
    }
    
    return add_query_arg(array(
        'donation_pending' => '1',
        'donation_id' => $donation_id,
        'payment_method' => 'local_bank'
    ), home_url('/donate'));
}

/**
 * Donation Shortcode
 */
function kilismile_donation_shortcode($atts) {
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
add_shortcode('kilismile_donation', 'kilismile_donation_shortcode');

/**
 * Newsletter Subscription AJAX Handler
 */
function kilismile_newsletter_subscribe() {
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
add_action('wp_ajax_kilismile_newsletter_subscribe', 'kilismile_newsletter_subscribe');
add_action('wp_ajax_nopriv_kilismile_newsletter_subscribe', 'kilismile_newsletter_subscribe');

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
    $subject = __('Welcome to Kili Smile Organization Newsletter', 'kilismile');
    $message = sprintf(
        __('Thank you for subscribing to our newsletter! You will receive updates about our health programs and impact in remote areas of Tanzania.

Best regards,
The Kili Smile Organization Team

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

?>
