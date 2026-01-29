<?php
/**
 * Optimized Single-Page Donation Form Template
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Initialize variables
if (!isset($usd_methods)) $usd_methods = array();
if (!isset($tzs_methods)) $tzs_methods = array();
if (!isset($payment_methods)) $payment_methods = !empty($tzs_methods) ? $tzs_methods : $usd_methods;
if (!isset($suggested_amounts)) {
    $suggested_amounts = array(
        'TZS' => array(10000, 25000, 50000, 100000, 250000),
        'USD' => array(5, 10, 25, 50, 100)
    );
}
if (!isset($default_currency)) $default_currency = 'TZS';
if (!isset($args)) $args = array();

$args = wp_parse_args($args, array(
    'class' => 'kilismile-donation-form',
    'show_recurring' => true,
    'show_anonymous' => true,
    'submit_text' => __('Donate Now', 'kilismile'),
    'title' => __('Make a Donation', 'kilismile'),
    'show_amounts' => true,
    'show_progress' => true
));
?>

<div class="donation-container" style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
<form class="<?php echo esc_attr($args['class']); ?>" data-form-type="donation" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; min-height: 100vh; align-items: start;">
    
    <!-- Left Column: User Information & Amount -->
    <div class="left-column" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); height: fit-content;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);">
                <i class="fas fa-heart" style="color: white; font-size: 24px;"></i>
            </div>
            <h2 style="color: #2c5530; margin: 0 0 10px 0; font-size: 1.8rem; font-weight: 700;">
                <?php echo esc_html($args['title']); ?>
            </h2>
            <p style="color: #6c757d; margin: 0; font-size: 0.95rem;">
                <?php _e('Your support makes a real difference in Tanzania\'s healthcare.', 'kilismile'); ?>
            </p>
        </div>

        <!-- Donor Type Selection -->
        <div style="margin-bottom: 25px;">
            <h4 style="color: #2c5530; margin-bottom: 15px; font-size: 1.1rem; font-weight: 600;">
                <?php _e('Donating as:', 'kilismile'); ?>
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label class="donor-type-option individual-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #28a745; border-radius: 10px; cursor: pointer; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);">
                    <input type="radio" name="donor_type" value="individual" checked style="margin-right: 10px; transform: scale(1.2);">
                    <div>
                        <i class="fas fa-user" style="color: #28a745; margin-right: 8px;"></i>
                        <strong style="color: #155724;"><?php _e('Individual', 'kilismile'); ?></strong>
                    </div>
                </label>
                <label class="donor-type-option business-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white;">
                    <input type="radio" name="donor_type" value="business" style="margin-right: 10px; transform: scale(1.2);">
                    <div>
                        <i class="fas fa-building" style="color: #6f42c1; margin-right: 8px;"></i>
                        <strong style="color: #495057;"><?php _e('Business', 'kilismile'); ?></strong>
                    </div>
                </label>
            </div>
        </div>

        <!-- Individual Information -->
        <div class="individual-info" style="margin-bottom: 25px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Full Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="donor_name" required 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('Enter your name', 'kilismile'); ?>">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Email', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" name="donor_email" required 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('your@email.com', 'kilismile'); ?>">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Phone', 'kilismile'); ?>
                    </label>
                    <input type="tel" name="donor_phone" 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('+255 XXX XXX XXX', 'kilismile'); ?>">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Country', 'kilismile'); ?>
                    </label>
                    <select name="donor_country" 
                            style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;">
                        <option value=""><?php _e('Select', 'kilismile'); ?></option>
                        <option value="TZ"><?php _e('Tanzania', 'kilismile'); ?></option>
                        <option value="KE"><?php _e('Kenya', 'kilismile'); ?></option>
                        <option value="UG"><?php _e('Uganda', 'kilismile'); ?></option>
                        <option value="US"><?php _e('United States', 'kilismile'); ?></option>
                        <option value="UK"><?php _e('United Kingdom', 'kilismile'); ?></option>
                        <option value="CA"><?php _e('Canada', 'kilismile'); ?></option>
                        <option value="other"><?php _e('Other', 'kilismile'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="business-info" style="margin-bottom: 25px; display: none;">
            <div style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Company Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="company_name" 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('Enter company name', 'kilismile'); ?>">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                            <?php _e('Contact Person', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="text" name="contact_person" 
                               style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                               placeholder="<?php _e('Contact name', 'kilismile'); ?>">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                            <?php _e('Business Email', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="email" name="business_email" 
                               style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                               placeholder="<?php _e('business@company.com', 'kilismile'); ?>">
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057; font-size: 0.9rem;">
                        <?php _e('Tax ID (Optional)', 'kilismile'); ?>
                    </label>
                    <input type="text" name="tax_id" 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('For tax deduction purposes', 'kilismile'); ?>">
                </div>
            </div>
        </div>

        <!-- Amount Selection -->
        <div style="margin-bottom: 25px;">
            <h4 style="color: #2c5530; margin-bottom: 15px; font-size: 1.1rem; font-weight: 600;">
                <?php _e('Choose Amount:', 'kilismile'); ?>
            </h4>
            
            <!-- Currency Toggle -->
            <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                <div style="background: #f8f9fa; border-radius: 25px; padding: 3px; display: flex;">
                    <button type="button" class="currency-btn active" data-currency="TZS" style="background: #28a745; color: white; border: none; padding: 8px 20px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.85rem;">
                        TZS
                    </button>
                    <button type="button" class="currency-btn" data-currency="USD" style="background: transparent; color: #28a745; border: none; padding: 8px 20px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.85rem;">
                        USD
                    </button>
                </div>
            </div>

            <!-- TZS Amounts -->
            <div class="preset-amounts" id="amounts-tzs">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px;">
                    <?php foreach ($suggested_amounts['TZS'] as $amount) : ?>
                        <div class="amount-card" data-amount="<?php echo $amount; ?>" style="border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: white;">
                            <div style="font-size: 1.1rem; font-weight: 700; color: #28a745; margin-bottom: 3px;">
                                TZS <?php echo number_format($amount); ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #6c757d;">
                                <?php 
                                switch ($amount) {
                                    case 10000: echo __('Basic help', 'kilismile'); break;
                                    case 25000: echo __('Health checkup', 'kilismile'); break;
                                    case 50000: echo __('Education materials', 'kilismile'); break;
                                    case 100000: echo __('Workshop support', 'kilismile'); break;
                                    case 250000: echo __('Equipment fund', 'kilismile'); break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- USD Amounts -->
            <div class="preset-amounts" id="amounts-usd" style="display: none;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px;">
                    <?php foreach ($suggested_amounts['USD'] as $amount) : ?>
                        <div class="amount-card" data-amount="<?php echo $amount; ?>" style="border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: white;">
                            <div style="font-size: 1.1rem; font-weight: 700; color: #28a745; margin-bottom: 3px;">
                                $<?php echo $amount; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #6c757d;">
                                <?php 
                                switch ($amount) {
                                    case 5: echo __('Basic support', 'kilismile'); break;
                                    case 10: echo __('Health fund', 'kilismile'); break;
                                    case 25: echo __('Monthly check', 'kilismile'); break;
                                    case 50: echo __('Workshop', 'kilismile'); break;
                                    case 100: echo __('Equipment', 'kilismile'); break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Custom Amount -->
            <div style="text-align: center;">
                <button type="button" id="custom-amount-btn" style="background: transparent; color: #28a745; border: 2px solid #28a745; padding: 10px 20px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                    <?php _e('Custom Amount', 'kilismile'); ?>
                </button>
                <div id="custom-amount-input" style="display: none; margin-top: 15px;">
                    <input type="number" class="amount-input" name="custom_amount" min="1" max="10000000" 
                           style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; text-align: center; transition: all 0.3s ease;"
                           placeholder="<?php _e('Enter amount', 'kilismile'); ?>">
                </div>
            </div>
        </div>

        <!-- Options -->
        <div style="margin-bottom: 25px;">
            <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; margin-bottom: 10px; background: white; transition: all 0.3s ease;">
                <input type="checkbox" name="anonymous_donation" style="margin-right: 10px; transform: scale(1.2);">
                <span style="font-size: 0.9rem; color: #495057;"><?php _e('Make anonymous', 'kilismile'); ?></span>
            </label>
            <label class="recurring-label" style="display: flex; align-items: center; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                <input type="checkbox" name="recurring_donation" class="recurring-checkbox" style="margin-right: 10px; transform: scale(1.2);">
                <span style="font-size: 0.9rem; color: #495057;"><?php _e('Make recurring', 'kilismile'); ?></span>
            </label>
            <div class="recurring-options" style="display: none; margin-top: 10px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <select name="recurring_frequency" style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 0.9rem;">
                    <option value="monthly"><?php _e('Monthly', 'kilismile'); ?></option>
                    <option value="quarterly"><?php _e('Quarterly', 'kilismile'); ?></option>
                    <option value="yearly"><?php _e('Yearly', 'kilismile'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- Right Column: Purpose & Payment -->
    <div class="right-column" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); height: fit-content;">
        
        <!-- Donation Purpose -->
        <div style="margin-bottom: 30px;">
            <h4 style="color: #2c5530; margin-bottom: 15px; font-size: 1.1rem; font-weight: 600;">
                <?php _e('Support Area:', 'kilismile'); ?>
            </h4>
            <div style="display: grid; gap: 12px;">
                <label class="purpose-option selected" style="display: flex; align-items: center; padding: 15px; border: 2px solid #28a745; border-radius: 10px; cursor: pointer; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); transition: all 0.3s ease;">
                    <input type="radio" name="donation_purpose" value="greatest_need" checked style="margin-right: 12px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-heart" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #155724; margin-bottom: 3px;"><?php _e('Greatest Need', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Where help is needed most', 'kilismile'); ?></div>
                    </div>
                </label>
                
                <label class="purpose-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="radio" name="donation_purpose" value="education" style="margin-right: 12px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #17a2b8, #138496); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-graduation-cap" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php _e('Health Education', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Community workshops', 'kilismile'); ?></div>
                    </div>
                </label>
                
                <label class="purpose-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="radio" name="donation_purpose" value="equipment" style="margin-right: 12px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #dc3545, #c82333); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-stethoscope" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php _e('Medical Equipment', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Essential medical tools', 'kilismile'); ?></div>
                    </div>
                </label>
                
                <label class="purpose-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="radio" name="donation_purpose" value="outreach" style="margin-right: 12px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #fd7e14, #e55a00); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-users" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php _e('Community Outreach', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Mobile health clinics', 'kilismile'); ?></div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Payment Methods -->
        <div style="margin-bottom: 30px;">
            <h4 style="color: #2c5530; margin-bottom: 15px; font-size: 1.1rem; font-weight: 600;">
                <?php _e('Payment Method:', 'kilismile'); ?>
            </h4>
            <div style="display: grid; gap: 12px;">
                <?php if (!empty($payment_methods) && is_array($payment_methods)) : ?>
                    <?php foreach (array_slice($payment_methods, 0, 4) as $method) : // Limit to 4 methods for compact view ?>
                        <?php 
                        if (is_array($method)) $method = (object) $method;
                        $method_code = isset($method->method_code) ? $method->method_code : (isset($method->id) ? $method->id : 'unknown');
                        $method_name = isset($method->method_name) ? $method->method_name : (isset($method->title) ? $method->title : 'Payment Method');
                        ?>
                        <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                            <input type="radio" name="payment_method" value="<?php echo esc_attr($method_code); ?>" style="margin-right: 12px; transform: scale(1.2);">
                            <div style="background: linear-gradient(135deg, #007bff, #6610f2); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="<?php 
                                    if (strpos($method_code, 'mobile') !== false || strpos($method_code, 'mpesa') !== false) echo 'fas fa-mobile-alt';
                                    elseif (strpos($method_code, 'bank') !== false) echo 'fas fa-university';
                                    elseif (strpos($method_code, 'paypal') !== false) echo 'fab fa-paypal';
                                    else echo 'fas fa-credit-card';
                                ?>" style="color: white; font-size: 16px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php echo esc_html($method_name); ?></div>
                                <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Secure & encrypted', 'kilismile'); ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- Fallback payment methods -->
                    <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="payment_method" value="bank_transfer" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-university" style="color: white; font-size: 16px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php _e('Bank Transfer', 'kilismile'); ?></div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Direct bank transfer', 'kilismile'); ?></div>
                        </div>
                    </label>
                    <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="payment_method" value="mobile_money" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #007bff, #6610f2); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 16px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #495057; margin-bottom: 3px;"><?php _e('Mobile Money', 'kilismile'); ?></div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('M-Pesa, Tigo Pesa, Airtel', 'kilismile'); ?></div>
                        </div>
                    </label>
                <?php endif; ?>
            </div>
        </div>

        <!-- Security Notice -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; text-align: center;">
            <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 10px;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-lock" style="color: #28a745; margin-right: 5px;"></i>
                    <span style="font-size: 0.8rem; color: #495057; font-weight: 600;">SSL Encrypted</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-shield-alt" style="color: #007bff; margin-right: 5px;"></i>
                    <span style="font-size: 0.8rem; color: #495057; font-weight: 600;">Secure Payment</span>
                </div>
            </div>
            <p style="margin: 0; font-size: 0.85rem; color: #6c757d; line-height: 1.4;">
                <?php _e('Your donation is secure and encrypted. We never store your payment information.', 'kilismile'); ?>
            </p>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-donation" style="width: 100%; background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 18px; border-radius: 12px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-heart" style="margin-right: 10px;"></i>
            <?php echo esc_html($args['submit_text']); ?>
        </button>

        <!-- Trust Indicators -->
        <div style="margin-top: 20px; text-align: center;">
            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 10px;">
                <div style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 700; color: #28a745;">2,500+</div>
                    <div style="font-size: 0.75rem; color: #6c757d;">Lives Impacted</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 700; color: #28a745;">98%</div>
                    <div style="font-size: 0.75rem; color: #6c757d;">Goes to Programs</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 700; color: #28a745;">15</div>
                    <div style="font-size: 0.75rem; color: #6c757d;">Communities</div>
                </div>
            </div>
        </div>
    </div>
    
</form>

<!-- Floating Donation Summary -->
<div class="donation-summary" style="position: fixed; bottom: 20px; right: 20px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 1000; min-width: 250px; display: none; border-left: 4px solid #28a745;">
    <div style="text-align: center;">
        <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 5px;">Your Donation</div>
        <div class="summary-amount" style="font-size: 1.4rem; font-weight: 700; color: #28a745;"></div>
        <div class="summary-type" style="font-size: 0.8rem; color: #6c757d; margin-top: 5px;"></div>
        <div class="summary-donor" style="font-size: 0.75rem; color: #28a745; margin-top: 3px; font-weight: 600;"></div>
    </div>
</div>

</div>

<script>
jQuery(document).ready(function($) {
    let selectedAmount = 0;
    let selectedCurrency = 'TZS';
    let donorType = 'individual';
    
    // Donor type toggle
    $('.donor-type-option').on('click', function() {
        donorType = $(this).find('input[type="radio"]').val();
        
        $('.donor-type-option').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        if (donorType === 'individual') {
            $(this).addClass('selected').css({
                'border-color': '#28a745',
                'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
            });
            $('.individual-info').show();
            $('.business-info').hide();
        } else {
            $(this).addClass('selected').css({
                'border-color': '#6f42c1',
                'background': 'linear-gradient(135deg, #f8f5ff 0%, #ede5ff 100%)'
            });
            $('.individual-info').hide();
            $('.business-info').show();
        }
        
        updateDonationSummary();
    });
    
    // Amount selection
    $('.amount-card').on('click', function() {
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).addClass('selected').css({
            'border-color': '#28a745',
            'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
        });
        
        selectedAmount = $(this).data('amount');
        $('#custom-amount-input').hide();
        $('.amount-input').val('');
        updateDonationSummary();
    });
    
    // Currency toggle
    $('.currency-btn').on('click', function() {
        const currency = $(this).data('currency');
        
        $('.currency-btn').removeClass('active').css({
            'background': 'transparent',
            'color': '#28a745'
        });
        
        $(this).addClass('active').css({
            'background': '#28a745',
            'color': 'white'
        });
        
        if (currency === 'USD') {
            $('#amounts-tzs').hide();
            $('#amounts-usd').show();
        } else {
            $('#amounts-usd').hide();
            $('#amounts-tzs').show();
        }
        
        selectedCurrency = currency;
        selectedAmount = 0;
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        updateDonationSummary();
    });
    
    // Custom amount
    $('#custom-amount-btn').on('click', function() {
        $('#custom-amount-input').slideToggle(300);
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        selectedAmount = 0;
    });
    
    $('.amount-input').on('input', function() {
        selectedAmount = parseFloat($(this).val()) || 0;
        updateDonationSummary();
    });
    
    // Purpose selection
    $('.purpose-option').on('click', function() {
        $('.purpose-option').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).addClass('selected').css({
            'border-color': '#28a745',
            'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
        });
        
        updateDonationSummary();
    });
    
    // Payment method selection
    $('.payment-option').on('click', function() {
        $('.payment-option').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).css({
            'border-color': '#007bff',
            'background': 'linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%)'
        });
    });
    
    // Recurring donation toggle
    $('.recurring-checkbox').on('change', function() {
        if ($(this).prop('checked')) {
            $('.recurring-options').slideDown(300);
            $('.recurring-label').css({
                'border-color': '#28a745',
                'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
            });
        } else {
            $('.recurring-options').slideUp(300);
            $('.recurring-label').css({
                'border-color': '#e9ecef',
                'background': 'white'
            });
        }
    });
    
    // Update donation summary
    function updateDonationSummary() {
        const amount = selectedAmount || parseFloat($('.amount-input').val()) || 0;
        
        if (amount > 0) {
            const currencySymbol = selectedCurrency === 'USD' ? '$' : 'TZS ';
            const formattedAmount = selectedCurrency === 'USD' ? 
                amount.toFixed(2) : 
                amount.toLocaleString();
            
            $('.summary-amount').text(currencySymbol + formattedAmount);
            $('.summary-type').text($('.purpose-option.selected input[type="radio"]').val() || 'Greatest Need');
            $('.summary-donor').text(donorType === 'business' ? '(Business)' : '(Individual)');
            $('.donation-summary').fadeIn(300);
        } else {
            $('.donation-summary').fadeOut(300);
        }
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Validate amount
        if (selectedAmount <= 0 && !$('.amount-input').val()) {
            alert('Please select or enter a donation amount.');
            isValid = false;
        }
        
        // Validate required fields based on donor type
        if (donorType === 'individual') {
            const requiredFields = ['donor_name', 'donor_email'];
            requiredFields.forEach(field => {
                if (!$(`input[name="${field}"]`).val().trim()) {
                    $(`input[name="${field}"]`).css('border-color', '#dc3545');
                    isValid = false;
                } else {
                    $(`input[name="${field}"]`).css('border-color', '#28a745');
                }
            });
        } else {
            const requiredFields = ['company_name', 'contact_person', 'business_email'];
            requiredFields.forEach(field => {
                if (!$(`input[name="${field}"]`).val().trim()) {
                    $(`input[name="${field}"]`).css('border-color', '#dc3545');
                    isValid = false;
                } else {
                    $(`input[name="${field}"]`).css('border-color', '#28a745');
                }
            });
        }
        
        // Validate payment method
        if (!$('input[name="payment_method"]:checked').length) {
            alert('Please select a payment method.');
            isValid = false;
        }
        
        if (isValid) {
            $('.submit-donation').html('<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i>Processing...').prop('disabled', true);
            
            // Simulate form submission
            setTimeout(() => {
                // Create enhanced success message with jQuery context
                showSuccessMessage($);
                $('.submit-donation').html('<i class="fas fa-heart" style="margin-right: 10px;"></i><?php echo esc_js($args['submit_text']); ?>').prop('disabled', false);
            }, 2000);
        }
    });
    
    // Initialize
    $('.amount-card:first').trigger('click');
    $('.purpose-option:first').trigger('click');
    $('.payment-option:first input[type="radio"]').prop('checked', true);
    $('.payment-option:first').trigger('click');
});

    // Enhanced success message function
    function showSuccessMessage($) {
        // Fallback if $ is not available - use jQuery directly
        if (typeof $ === 'undefined' && typeof jQuery !== 'undefined') {
            $ = jQuery;
        }
        
        console.log('showSuccessMessage called - jQuery available:', typeof $ !== 'undefined');
        
        if (typeof $ === 'undefined') {
            // Fallback to basic alert if jQuery is not available
            alert('Thank you for your donation! You will be redirected to complete the payment.');
            return;
        }
        
        const selectedAmount = $('.amount-input').val();
        const selectedCurrency = $('input[name="currency"]:checked').val();
        const paymentMethod = $('input[name="payment_method"]:checked').next('label').text().trim();
        console.log('Form data:', {selectedAmount, selectedCurrency, paymentMethod});
    
    // Create success modal
    const successModal = $(`
        <div class="donation-success-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        ">
            <div class="success-content" style="
                background: white;
                padding: 40px;
                border-radius: 15px;
                text-align: center;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                animation: slideUp 0.4s ease;
            ">
                <div style="color: #28a745; font-size: 60px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.8rem;">
                    Thank You for Your Generous Donation!
                </h2>
                <p style="color: #666; margin-bottom: 25px; font-size: 1.1rem; line-height: 1.5;">
                    Your contribution of <strong style="color: #28a745;">${selectedCurrency} ${selectedAmount}</strong> 
                    will make a real difference in the lives of those we serve.
                </p>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
                    <p style="margin: 0; color: #495057; font-size: 0.95rem;">
                        <i class="fas fa-info-circle" style="color: #17a2b8; margin-right: 8px;"></i>
                        You will be redirected to <strong>${paymentMethod}</strong> to complete your secure payment.
                    </p>
                </div>
                <button class="continue-payment-btn" style="
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    border-radius: 25px;
                    font-size: 1.1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                ">
                    <i class="fas fa-arrow-right" style="margin-right: 10px;"></i>
                    Continue to Payment
                </button>
                <p style="margin-top: 15px; font-size: 0.85rem; color: #999;">
                    Secure payment processing • SSL encrypted
                </p>
            </div>
        </div>
    `);
    
    // Add animations
    const animations = $(`
        <style>
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideUp {
                from { transform: translateY(30px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .continue-payment-btn:hover {
                background: linear-gradient(135deg, #218838, #1e7e34) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
            }
        </style>
    `);
    
    $('head').append(animations);
    $('body').append(successModal);
    
    // Handle continue button click
    $('.continue-payment-btn').on('click', function() {
        successModal.fadeOut(300, function() {
            $(this).remove();
            // Here you would normally redirect to payment processor
            console.log('Redirecting to payment processor...');
        });
    });
    
        // Auto close after 8 seconds
        setTimeout(() => {
            if (successModal.is(':visible')) {
                $('.continue-payment-btn').trigger('click');
            }
        }, 8000);
    }

// Add responsive styles
const style = document.createElement('style');
style.textContent = `
    @media (max-width: 768px) {
        .donation-container form {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        .donation-summary {
            bottom: 10px !important;
            right: 10px !important;
            left: 10px !important;
            min-width: auto !important;
        }
    }
    
    .amount-card:hover, .purpose-option:hover, .payment-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    input:focus, select:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1) !important;
        outline: none !important;
    }
`;
document.head.appendChild(style);
</script>


