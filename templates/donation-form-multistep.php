<?php
/**
 * Multi-Step Donation Form Template with KiliSmile Payments Integration
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

// Initialize variables
if (!isset($suggested_amounts)) {
    $suggested_amounts = array(
        'TZS' => array(10000, 25000, 50000, 100000, 250000),
        'USD' => array(5, 10, 25, 50, 100)
    );
}
if (!isset($default_currency)) $default_currency = 'USD';
if (!isset($args)) $args = array();

$args = wp_parse_args($args, array(
    'class' => 'kilismile-donation-form',
    'show_recurring' => true,
    'show_anonymous' => true,
    'submit_text' => __('Complete Donation', 'kilismile'),
    'title' => __('Support Our Mission', 'kilismile'),
    'show_amounts' => true,
    'show_progress' => true,
    'form_style' => 'multi-step'
));
?>

<!-- Multi-Step Donation Form Container -->
<div id="kilismile-donation-form" class="<?php echo esc_attr($args['class']); ?>" style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; width: 100%; max-width: 100%; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
    
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

    <!-- Plugin Status Notice -->
    <div class="kilismile-plugin-status" style="background: #d4edda; border-left: 4px solid #28a745; color: #155724; padding: 15px 20px; font-size: 0.9rem;">
        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
        <?php
        if ($plugin_active) {
            _e('Enhanced Payment System Active - AzamPay & PayPal Available', 'kilismile');
        } else {
            _e('Secure Payment System Ready - Choose AzamPay or PayPal below', 'kilismile');
        }
        ?>
    </div>

    <!-- Form Content -->
    <div class="form-content" style="padding: 40px;">

        <div id="ks-form-message" class="ks-form-message" role="alert" aria-live="polite"></div>
        
        <form id="multi-step-donation-form" method="post">
            <!-- Step 1: Amount Selection -->
            <div class="form-step active" data-step="1" style="display: block;">
                <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                    <?php echo esc_html($args['title']); ?>
                </h3>
                
                <!-- Currency Selection -->
                <div class="currency-selection" style="display: flex; justify-content: center; margin-bottom: 30px;">
                    <div style="background: #f8f9fa; border-radius: 25px; padding: 5px; display: flex;">
                        <button type="button" onclick="switchCurrency('TZS')" id="tzs-btn" style="background: transparent; color: #6c757d; border: none; padding: 10px 20px; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            TZS (Tanzania)
                        </button>
                        <button type="button" onclick="switchCurrency('USD')" id="usd-btn" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 10px 20px; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            USD (International)
                        </button>
                    </div>
                </div>
                
                <!-- Amount Selection Grid -->
                <div id="amount-grid-tzs" class="amount-grid" style="display: none; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
                    <?php foreach ($suggested_amounts['TZS'] as $amount): ?>
                    <button type="button" class="amount-btn tzs-amount" data-amount="<?php echo $amount; ?>" data-currency="TZS" style="background: white; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; font-weight: 600;">
                        <div style="font-size: 1.2rem; color: #28a745; margin-bottom: 5px;">
                            <?php echo number_format($amount); ?>
                        </div>
                        <div style="font-size: 0.8rem; color: #6c757d;">TZS</div>
                    </button>
                    <?php endforeach; ?>
                </div>
                
                <div id="amount-grid-usd" class="amount-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
                    <?php foreach ($suggested_amounts['USD'] as $amount): ?>
                    <button type="button" class="amount-btn usd-amount" data-amount="<?php echo $amount; ?>" data-currency="USD" style="background: white; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; font-weight: 600;">
                        <div style="font-size: 1.2rem; color: #28a745; margin-bottom: 5px;">
                            $<?php echo number_format($amount); ?>
                        </div>
                        <div style="font-size: 0.8rem; color: #6c757d;">USD</div>
                    </button>
                    <?php endforeach; ?>
                </div>
                
                <!-- Custom Amount Input -->
                <div style="margin-bottom: 30px;">
                    <label style="display: block; color: #495057; margin-bottom: 8px; font-weight: 600;">
                        <?php _e('Or enter custom amount:', 'kilismile'); ?>
                    </label>
                    <div style="position: relative;">
                        <span id="currency-symbol" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; font-weight: 600;">$</span>
                        <input type="number" id="custom-amount" placeholder="0" style="width: 100%; padding: 15px 15px 15px 60px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1.1rem; font-weight: 600; transition: border-color 0.3s ease;" min="1">
                    </div>
                </div>
                
                <!-- Recurring Donation Option -->
                <?php if ($args['show_recurring']): ?>
                <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid #28a745;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="recurring-donation" style="margin-right: 12px; transform: scale(1.3); accent-color: #28a745;">
                        <div>
                            <strong style="color: #495057; font-size: 1rem;"><?php _e('Make this a monthly donation', 'kilismile'); ?></strong>
                            <div style="color: #6c757d; font-size: 0.9rem; margin-top: 5px;">
                                <?php _e('Maximize your impact with recurring support', 'kilismile'); ?>
                            </div>
                        </div>
                    </label>
                </div>
                <?php endif; ?>
                
                <!-- Next Button -->
                <button type="button" onclick="nextStep(2)" style="width: 100%; background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 18px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(40,167,69,0.3);">
                    <?php _e('Continue to Details', 'kilismile'); ?>
                    <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </button>
            </div>
            
            <!-- Step 2: Donor Information -->
            <div class="form-step" data-step="2" style="display: none;">
                <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                    <?php _e('Your Information', 'kilismile'); ?>
                </h3>
                
                <div class="ks-donor-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; color: #495057; margin-bottom: 8px; font-weight: 600;">
                            <?php _e('First Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="text" name="donor_first_name" required 
                               style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                               placeholder="<?php _e('Enter your first name', 'kilismile'); ?>" 
                               aria-describedby="first-name-error">
                        <div id="first-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                    </div>
                    <div>
                        <label style="display: block; color: #495057; margin-bottom: 8px; font-weight: 600;">
                            <?php _e('Last Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="text" name="donor_last_name" required 
                               style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                               placeholder="<?php _e('Enter your last name', 'kilismile'); ?>" 
                               aria-describedby="last-name-error">
                        <div id="last-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                    </div>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #495057; margin-bottom: 8px; font-weight: 600;">
                        <?php _e('Email Address', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" name="donor_email" required 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('your.email@example.com', 'kilismile'); ?>" 
                           aria-describedby="email-error">
                    <div id="email-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #495057; margin-bottom: 8px; font-weight: 600;">
                        <?php _e('Phone Number', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="tel" name="donor_phone" required
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('+255 123 456 789', 'kilismile'); ?>" 
                           aria-describedby="phone-error">
                    <div id="phone-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                
                <?php if ($args['show_anonymous']): ?>
                <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="anonymous_donation" value="1" style="margin-right: 12px; transform: scale(1.3); accent-color: #28a745;">
                        <div>
                            <strong style="color: #495057; font-size: 1rem;"><?php _e('Make this donation anonymous', 'kilismile'); ?></strong>
                            <div style="color: #6c757d; font-size: 0.9rem; margin-top: 5px;">
                                <?php _e('Your name will not be publicly displayed', 'kilismile'); ?>
                            </div>
                        </div>
                    </label>
                </div>
                <?php endif; ?>
                
                <div class="ks-step-actions" style="display: flex; gap: 15px;">
                    <button type="button" onclick="previousStep(1)" style="flex: 1; background: #6c757d; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-arrow-left" style="margin-right: 10px;"></i>
                        <?php _e('Back', 'kilismile'); ?>
                    </button>
                    <button type="button" onclick="nextStep(3)" style="flex: 2; background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 15px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
                        <?php _e('Continue to Payment', 'kilismile'); ?>
                        <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Payment Selection -->
            <div class="form-step" data-step="3" style="display: none;">
                <h3 style="color: #28a745; margin-bottom: 25px; font-size: 1.5rem; text-align: center;">
                    <?php _e('Choose Payment Method', 'kilismile'); ?>
                </h3>
                
                <!-- Donation Summary -->
                <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 30px; border-left: 4px solid #28a745;">
                    <h4 style="margin: 0 0 15px 0; color: #495057; font-size: 1.1rem;"><?php _e('Donation Summary', 'kilismile'); ?></h4>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="color: #6c757d;"><?php _e('Amount:', 'kilismile'); ?></span>
                        <strong id="summary-amount" style="color: #28a745; font-size: 1.2rem;">$0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="color: #6c757d;"><?php _e('Type:', 'kilismile'); ?></span>
                        <span id="summary-type"><?php _e('One-time', 'kilismile'); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6c757d;"><?php _e('Donor:', 'kilismile'); ?></span>
                        <span id="summary-donor"></span>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div id="payment-methods" style="margin-bottom: 30px;">
                    <!-- TZS Payment Methods (AzamPay) -->
                    <div id="tzs-payment-methods" style="display: none;">
                        <div class="payment-method" style="border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease;">
                            <input type="radio" name="payment_method" value="azampay" id="azampay" style="margin-right: 15px; transform: scale(1.3); accent-color: #28a745;">
                            <label for="azampay" style="cursor: pointer; display: flex; align-items: center; width: 100%;">
                                <div style="background: linear-gradient(135deg, #FF6B35, #F7941D); width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-mobile-alt" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong style="color: #495057; font-size: 1.1rem; display: block;"><?php _e('Mobile Money (AzamPay)', 'kilismile'); ?></strong>
                                    <span style="color: #6c757d; font-size: 0.9rem;"><?php _e('Vodacom M-Pesa, Airtel Money, Tigo Pesa, Halotel', 'kilismile'); ?></span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- USD Payment Methods (PayPal) -->
                    <div id="usd-payment-methods" style="display: block;">
                        <div class="payment-method" style="border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease;">
                            <input type="radio" name="payment_method" value="paypal" id="paypal" checked style="margin-right: 15px; transform: scale(1.3); accent-color: #28a745;">
                            <label for="paypal" style="cursor: pointer; display: flex; align-items: center; width: 100%;">
                                <div style="background: linear-gradient(135deg, #0070BA, #00A0D6); width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fab fa-paypal" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong style="color: #495057; font-size: 1.1rem; display: block;"><?php _e('PayPal', 'kilismile'); ?></strong>
                                    <span style="color: #6c757d; font-size: 0.9rem;"><?php _e('Credit/Debit Cards, PayPal Balance', 'kilismile'); ?></span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="ks-step-actions" style="display: flex; gap: 15px;">
                    <button type="button" onclick="previousStep(2)" style="flex: 1; background: #6c757d; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-arrow-left" style="margin-right: 10px;"></i>
                        <?php _e('Back', 'kilismile'); ?>
                    </button>
                    <button type="submit" id="submit-donation" style="flex: 2; background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 15px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(40,167,69,0.3);">
                        <i class="fas fa-heart" style="margin-right: 10px;"></i>
                        <?php echo esc_html($args['submit_text']); ?>
                    </button>
                </div>
            </div>
            
            <!-- Hidden form fields -->
            <input type="hidden" name="donation_amount" id="donation_amount">
            <input type="hidden" name="currency" id="currency" value="USD">
            <input type="hidden" name="donation_recurring" id="donation_recurring" value="0">
            <input type="hidden" name="action" value="kilismile_process_donation">
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('kilismile_donation_checkout')); ?>">
        </form>
    </div>
</div>

<!-- Multi-Step Form JavaScript -->
<script>
let currentStep = 1;
let selectedCurrency = 'USD';
let selectedAmount = 0;

function ksSetMessage(type, message) {
    const el = document.getElementById('ks-form-message');
    if (!el) {
        if (message) alert(message);
        return;
    }

    el.className = 'ks-form-message ks-form-message--show' + (type ? (' ks-form-message--' + type) : '');
    el.innerHTML = message ? ('<strong>' + (type === 'error' ? 'Error: ' : '') + '</strong>' + message) : '';
    if (message) {
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        el.className = 'ks-form-message';
    }
}

function ksIsDebugEnabled() {
    try {
        return new URLSearchParams(window.location.search).get('ks_debug') === '1';
    } catch (e) {
        return false;
    }
}

function ksSanitizeDebugPayload(payload) {
    if (!payload || typeof payload !== 'object') return payload;
    const blockedKeys = ['token', 'secret', 'nonce', 'client', 'authorization'];
    const safe = Array.isArray(payload) ? [] : {};
    Object.keys(payload).forEach((key) => {
        const lower = String(key).toLowerCase();
        if (blockedKeys.some((b) => lower.includes(b))) {
            return;
        }
        const value = payload[key];
        if (value && typeof value === 'object') {
            safe[key] = ksSanitizeDebugPayload(value);
        } else {
            safe[key] = value;
        }
    });
    return safe;
}

function ksRenderDebugPanel(data) {
    const root = document.getElementById('kilismile-donation-form');
    if (!root) return;

    let panel = document.getElementById('ks-debug-panel');
    if (!panel) {
        panel = document.createElement('div');
        panel.id = 'ks-debug-panel';
        panel.style.marginTop = '16px';
        panel.style.padding = '14px';
        panel.style.borderRadius = '12px';
        panel.style.border = '1px solid #e9ecef';
        panel.style.background = '#ffffff';
        panel.style.boxShadow = '0 8px 20px rgba(0,0,0,0.06)';
        panel.innerHTML =
            '<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">' +
            '<strong style="color:#0f172a;">Debug: AJAX Response</strong>' +
            '<span style="font-size:0.85rem;color:#64748b;">Remove by dropping ?ks_debug=1</span>' +
            '</div>' +
            '<pre id="ks-debug-json" style="white-space:pre-wrap;word-break:break-word;margin:10px 0 0;max-height:320px;overflow:auto;background:#0b1220;color:#e2e8f0;padding:12px;border-radius:10px;"></pre>';
        root.appendChild(panel);
    }

    const pre = document.getElementById('ks-debug-json');
    if (!pre) return;
    pre.textContent = JSON.stringify(ksSanitizeDebugPayload(data), null, 2);
}

function switchCurrency(currency) {
    selectedCurrency = currency;
    document.getElementById('currency').value = currency;
    
    // Update currency buttons
    const tzsBtn = document.getElementById('tzs-btn');
    const usdBtn = document.getElementById('usd-btn');
    
    if (currency === 'TZS') {
        tzsBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        tzsBtn.style.color = 'white';
        usdBtn.style.background = 'transparent';
        usdBtn.style.color = '#6c757d';
        
        document.getElementById('amount-grid-tzs').style.display = 'grid';
        document.getElementById('amount-grid-usd').style.display = 'none';
        document.getElementById('currency-symbol').textContent = 'TZS';
        
        document.getElementById('tzs-payment-methods').style.display = 'block';
        document.getElementById('usd-payment-methods').style.display = 'none';

        const azam = document.getElementById('azampay');
        if (azam) azam.checked = true;
    } else {
        usdBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        usdBtn.style.color = 'white';
        tzsBtn.style.background = 'transparent';
        tzsBtn.style.color = '#6c757d';
        
        document.getElementById('amount-grid-usd').style.display = 'grid';
        document.getElementById('amount-grid-tzs').style.display = 'none';
        document.getElementById('currency-symbol').textContent = '$';
        
        document.getElementById('usd-payment-methods').style.display = 'block';
        document.getElementById('tzs-payment-methods').style.display = 'none';

        const paypal = document.getElementById('paypal');
        if (paypal) paypal.checked = true;
    }

    ksSetMessage('', '');
    
    // Clear selected amount
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.style.background = 'white';
        btn.style.borderColor = '#e9ecef';
        btn.style.color = '#495057';
    });
    document.getElementById('custom-amount').value = '';
    selectedAmount = 0;
    document.getElementById('donation_amount').value = '';
}

// Amount button selection
document.addEventListener('DOMContentLoaded', function() {
    const initialCurrency = document.getElementById('currency') ? document.getElementById('currency').value : 'USD';
    if (initialCurrency) {
        switchCurrency(initialCurrency);
    }

    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            ksSetMessage('', '');
            // Clear all selections
            document.querySelectorAll('.amount-btn').forEach(b => {
                b.style.background = 'white';
                b.style.borderColor = '#e9ecef';
                b.style.color = '#495057';
            });
            
            // Select this button
            this.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            this.style.borderColor = '#28a745';
            this.style.color = 'white';
            
            selectedAmount = this.dataset.amount;
            document.getElementById('custom-amount').value = '';
            document.getElementById('donation_amount').value = selectedAmount;
        });
    });
    
    // Custom amount input
    document.getElementById('custom-amount').addEventListener('input', function() {
        if (this.value) {
            ksSetMessage('', '');
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.style.background = 'white';
                btn.style.borderColor = '#e9ecef';
                btn.style.color = '#495057';
            });
            selectedAmount = this.value;
            document.getElementById('donation_amount').value = selectedAmount;
        }
    });
    
    // Recurring donation checkbox
    document.getElementById('recurring-donation').addEventListener('change', function() {
        document.getElementById('donation_recurring').value = this.checked ? '1' : '0';
    });
});

function nextStep(step) {
    if (step === 2) {
        // Validate amount selection
        if (!selectedAmount || selectedAmount <= 0) {
            ksSetMessage('error', '<?php echo esc_js(__('Please select or enter a donation amount.', 'kilismile')); ?>');
            return;
        }
    }
    
    if (step === 3) {
        // Validate donor information
        const firstName = document.querySelector('input[name="donor_first_name"]').value;
        const lastName = document.querySelector('input[name="donor_last_name"]').value;
        const email = document.querySelector('input[name="donor_email"]').value;
        const phone = document.querySelector('input[name="donor_phone"]').value;
        
        if (!firstName || !lastName || !email || !phone) {
            ksSetMessage('error', '<?php echo esc_js(__('Please fill in all required fields.', 'kilismile')); ?>');
            return;
        }

        // Basic phone sanity check (backend will validate strictly)
        const digits = phone.replace(/\D/g, '');
        if (digits.length < 9) {
            ksSetMessage('error', '<?php echo esc_js(__('Please enter a valid phone number.', 'kilismile')); ?>');
            return;
        }
        
        // Update summary
        const isRecurring = document.getElementById('recurring-donation').checked;
        const formattedAmount = selectedCurrency === 'TZS' ? 
            'TZS ' + Number(selectedAmount).toLocaleString() :
            '$' + Number(selectedAmount).toLocaleString();
            
        document.getElementById('summary-amount').textContent = formattedAmount;
        document.getElementById('summary-type').textContent = isRecurring ? 
            '<?php _e('Monthly', 'kilismile'); ?>' : '<?php _e('One-time', 'kilismile'); ?>';
        document.getElementById('summary-donor').textContent = firstName + ' ' + lastName;
    }
    
    // Hide current step
    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    
    // Show target step
    document.querySelector('[data-step="' + step + '"]').style.display = 'block';
    
    // Update progress indicator
    updateProgressIndicator(step);
    currentStep = step;
}

function previousStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    document.querySelector('[data-step="' + step + '"]').style.display = 'block';
    updateProgressIndicator(step);
    currentStep = step;
}

function updateProgressIndicator(step) {
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const circle = indicator.querySelector('.step-circle');
        if (index + 1 <= step) {
            indicator.classList.add('active');
            circle.style.background = 'white';
            circle.style.color = '#28a745';
        } else {
            indicator.classList.remove('active');
            circle.style.background = 'rgba(255,255,255,0.3)';
            circle.style.color = 'white';
        }
    });
}

// Form submission
document.getElementById('multi-step-donation-form').addEventListener('submit', function(e) {
    e.preventDefault();

    ksSetMessage('', '');

    if (!selectedAmount || Number(selectedAmount) <= 0) {
        ksSetMessage('error', '<?php echo esc_js(__('Please select or enter a donation amount.', 'kilismile')); ?>');
        return;
    }

    const requiredFields = ['donor_first_name', 'donor_last_name', 'donor_email', 'donor_phone'];
    for (const name of requiredFields) {
        const input = this.querySelector('[name="' + name + '"]');
        if (!input || !String(input.value || '').trim()) {
            ksSetMessage('error', '<?php echo esc_js(__('Please fill in all required fields.', 'kilismile')); ?>');
            if (input) input.focus();
            return;
        }
    }

    const method = this.querySelector('input[name="payment_method"]:checked');
    if (!method) {
        ksSetMessage('error', '<?php echo esc_js(__('Please select a payment method.', 'kilismile')); ?>');
        return;
    }
    
    const formData = new FormData(this);
    if (ksIsDebugEnabled()) {
        formData.append('ks_debug', '1');
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submit-donation');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i><?php _e('Processing...', 'kilismile'); ?>';
    submitBtn.disabled = true;
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (ksIsDebugEnabled()) {
            console.log('[KiliSmile Donation Debug] Raw response:', data);
            ksRenderDebugPanel(data);
        }
        if (data.success) {
            if (data.data.redirect_url) {
                window.location.href = data.data.redirect_url;
            } else {
                ksSetMessage('success', '<?php echo esc_js(__('Payment processed successfully!', 'kilismile')); ?>');
                window.location.reload();
            }
        } else {
            ksSetMessage('error', (data && data.data && data.data.message) ? data.data.message : '<?php echo esc_js(__('Payment failed. Please try again.', 'kilismile')); ?>');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ksSetMessage('error', '<?php echo esc_js(__('An error occurred. Please try again.', 'kilismile')); ?>');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>