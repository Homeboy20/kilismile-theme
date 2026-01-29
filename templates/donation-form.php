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


<!-- Modern Donation Page Hero & Form -->
<section id="kilismile-modern-donation" style="max-width: 600px; margin: 40px auto; background: #fff; border-radius: 24px; box-shadow: 0 8px 32px rgba(40,167,69,0.10); overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
  <div style="background: linear-gradient(135deg, #28a745, #20c997); padding: 48px 32px 32px 32px; text-align: center; color: #fff;">
    <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -1px;">Support Our Mission</h1>
    <p style="font-size: 1.15rem; opacity: 0.95; margin-bottom: 0;">Your donation brings healthcare, education, and hope to communities in need. Every gift makes a difference.</p>
  </div>
  <div style="padding: 40px 32px 32px 32px;">
    <!-- Modern Progress Bar -->
    <div class="modern-progress" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
      <div class="modern-step active" data-step="1" style="flex:1; text-align:center;">
        <div class="modern-circle" style="width: 38px; height: 38px; border-radius: 50%; background: #28a745; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 6px auto;">1</div>
        <span style="font-size: 0.9rem;">Amount</span>
      </div>
      <div style="flex:1; height: 2px; background: #e9ecef; margin: 0 8px;"></div>
      <div class="modern-step" data-step="2" style="flex:1; text-align:center;">
        <div class="modern-circle" style="width: 38px; height: 38px; border-radius: 50%; background: #e9ecef; color: #28a745; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 6px auto;">2</div>
        <span style="font-size: 0.9rem; color: #6c757d;">Details</span>
      </div>
      <div style="flex:1; height: 2px; background: #e9ecef; margin: 0 8px;"></div>
      <div class="modern-step" data-step="3" style="flex:1; text-align:center;">
        <div class="modern-circle" style="width: 38px; height: 38px; border-radius: 50%; background: #e9ecef; color: #28a745; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 6px auto;">3</div>
        <span style="font-size: 0.9rem; color: #6c757d;">Payment</span>
      </div>
    </div>
    <!-- Modern Multi-Step Form (reuse existing form logic below) -->
    <div class="modern-donation-form">
      <!-- ...existing form logic and fields... -->

<form class="<?php echo esc_attr($args['class']); ?>" data-form-type="donation" style="position: relative; z-index: 2;">
    
    <!-- Step 1: User Information -->
    <div class="donation-step active" id="step-1" style="display: block; padding: 50px 40px;">
        
        <!-- Step Header -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 25px rgba(40,167,69,0.3);">
                <i class="fas fa-user" style="color: white; font-size: 28px;"></i>
            </div>
            <h2 style="color: #2c5530; margin: 0 0 10px 0; font-size: 2rem; font-weight: 700; line-height: 1.3;">
                <?php _e('Tell Us About Yourself', 'kilismile'); ?>
            </h2>
            <p style="color: #6c757d; margin: 0; font-size: 1.1rem; line-height: 1.5; max-width: 500px; margin-left: auto; margin-right: auto;">
                <?php _e('Help us personalize your donation experience and ensure we can provide you with a receipt.', 'kilismile'); ?>
            </p>
        </div>
            <p style="color: #6c757d; margin: 0; font-size: 1rem; line-height: 1.5; max-width: 500px; margin-left: auto; margin-right: auto;">
                <?php _e('Help us personalize your donation experience and keep you updated on your impact.', 'kilismile'); ?>
            </p>
        </div>

        <!-- Donor Type Selection -->
        <div style="margin-bottom: 45px;">
            <h4 style="color: #2c5530; margin-bottom: 25px; font-size: 1.2rem; font-weight: 700; text-align: center;">
                <?php _e('I am donating as:', 'kilismile'); ?>
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; max-width: 600px; margin: 0 auto;">
                <label class="donor-type-option individual-option" style="display: flex; flex-direction: column; align-items: center; padding: 30px 20px; border: 3px solid #28a745; border-radius: 16px; cursor: pointer; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-align: center; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(40,167,69,0.2);">
                    <!-- Background decoration -->
                    <div style="position: absolute; top: -15px; right: -15px; width: 50px; height: 50px; background: rgba(40,167,69,0.1); border-radius: 50%;"></div>
                    
                    <input type="radio" name="donor_type" value="individual" checked style="position: absolute; top: 15px; right: 15px; transform: scale(1.5); accent-color: #28a745;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(40,167,69,0.3);">
                        <i class="fas fa-user" style="color: white; font-size: 24px;"></i>
                    </div>
                    <strong style="color: #155724; font-size: 1.2rem; margin-bottom: 8px; font-weight: 700;"><?php _e('Individual', 'kilismile'); ?></strong>
                    <span style="color: #6c757d; font-size: 0.9rem; line-height: 1.4;"><?php _e('Perfect for personal contributions and individual giving', 'kilismile'); ?></span>
                </label>
                
                <label class="donor-type-option business-option" style="display: flex; flex-direction: column; align-items: center; padding: 30px 20px; border: 3px solid #e9ecef; border-radius: 16px; cursor: pointer; background: white; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-align: center; position: relative; overflow: hidden;">
                    <!-- Background decoration -->
                    <div style="position: absolute; top: -15px; right: -15px; width: 50px; height: 50px; background: rgba(111,66,193,0.1); border-radius: 50%;"></div>
                    
                    <input type="radio" name="donor_type" value="business" style="position: absolute; top: 15px; right: 15px; transform: scale(1.5); accent-color: #6f42c1;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #6f42c1, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(111,66,193,0.3);">
                        <i class="fas fa-building" style="color: white; font-size: 24px;"></i>
                    </div>
                    <strong style="color: #495057; font-size: 1.2rem; margin-bottom: 8px; font-weight: 700;"><?php _e('Business', 'kilismile'); ?></strong>
                    <span style="color: #6c757d; font-size: 0.9rem; line-height: 1.4;"><?php _e('Ideal for companies and corporate social responsibility', 'kilismile'); ?></span>
                </label>
            </div>
        </div>

        <!-- Individual Information -->
        <div class="individual-info" style="max-width: 700px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c5530; font-size: 1rem;">
                        <?php _e('First Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="first_name" required 
                           style="width: 100%; padding: 18px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('Enter your first name', 'kilismile'); ?>" 
                           aria-describedby="first-name-error">
                    <div id="first-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c5530; font-size: 1rem;">
                        <?php _e('Last Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="last_name" required 
                           style="width: 100%; padding: 18px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('Enter your last name', 'kilismile'); ?>" 
                           aria-describedby="last-name-error">
                    <div id="last-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c5530; font-size: 1rem;">
                        <?php _e('Email Address', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 18px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('your.email@example.com', 'kilismile'); ?>" 
                           aria-describedby="email-error">
                    <div id="email-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c5530; font-size: 1rem;">
                        <?php _e('Phone Number', 'kilismile'); ?> <span style="color: #6c757d; font-size: 0.9rem;"><?php _e('(Optional)', 'kilismile'); ?></span>
                    </label>
                    <input type="tel" name="phone" 
                           style="width: 100%; padding: 18px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                           placeholder="<?php _e('+255 123 456 789', 'kilismile'); ?>" 
                           aria-describedby="phone-error">
                    <div id="phone-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
            </div>
        </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                        <?php _e('Last Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="last_name" required 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('Enter your last name', 'kilismile'); ?>"
                           aria-describedby="last-name-error">
                    <div id="last-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                        <?php _e('Email Address', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('your@email.com', 'kilismile'); ?>"
                           aria-describedby="email-error">
                    <div id="email-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                        <?php _e('Phone Number', 'kilismile'); ?>
                    </label>
                    <input type="tel" name="phone" 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('+255 XXX XXX XXX', 'kilismile'); ?>">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                    <?php _e('Country', 'kilismile'); ?>
                </label>
                <select name="country" 
                        style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;">
                    <option value=""><?php _e('Select Country', 'kilismile'); ?></option>
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

        <!-- Business Information -->
        <div class="business-info" style="display: none; max-width: 600px; margin: 0 auto;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                    <?php _e('Company Name', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                </label>
                <input type="text" name="company_name" 
                       style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                       placeholder="<?php _e('Enter company name', 'kilismile'); ?>"
                       aria-describedby="company-name-error">
                <div id="company-name-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                        <?php _e('Contact Person', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" name="contact_person" 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('Contact person name', 'kilismile'); ?>"
                           aria-describedby="contact-person-error">
                    <div id="contact-person-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057; font-size: 0.95rem;">
                        <?php _e('Business Email', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" name="business_email" 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;"
                           placeholder="<?php _e('business@company.com', 'kilismile'); ?>"
                           aria-describedby="business-email-error">
                    <div id="business-email-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Step Navigation -->
        <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
            <button type="button" class="btn-next-step" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 15px 40px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center;">
                <?php _e('Continue', 'kilismile'); ?>
                <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Amount & Purpose -->
    <div class="donation-step" id="step-2" style="display: none; padding: 40px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="background: linear-gradient(135deg, #007bff, #6610f2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-dollar-sign" style="color: white; font-size: 24px;"></i>
            </div>
            <h2 style="color: #2c5530; margin: 0 0 15px 0; font-size: 1.6rem; font-weight: 600;">
                <?php _e('Choose Your Impact', 'kilismile'); ?>
            </h2>
            <p style="color: #6c757d; margin: 0; font-size: 1rem; line-height: 1.5; max-width: 500px; margin-left: auto; margin-right: auto;">
                <?php _e('Select your donation amount and how you\'d like to help our mission.', 'kilismile'); ?>
            </p>
        </div>

        <!-- Currency Toggle -->
        <div style="display: flex; justify-content: center; margin-bottom: 30px;">
            <div style="background: #f8f9fa; border-radius: 25px; padding: 4px; display: flex;">
                <button type="button" class="currency-btn active" data-currency="TZS" style="background: #28a745; color: white; border: none; padding: 10px 24px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.95rem;">
                    TZS
                </button>
                <button type="button" class="currency-btn" data-currency="USD" style="background: transparent; color: #28a745; border: none; padding: 10px 24px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.95rem;">
                    USD
                </button>
            </div>
        </div>

        <!-- Amount Selection -->
        <div style="max-width: 700px; margin: 0 auto;">
            <!-- TZS Amounts -->
            <div class="preset-amounts" id="amounts-tzs">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px;">
                    <?php 
                    $tzs_amounts = array(10000, 25000, 50000, 100000, 250000);
                    foreach ($tzs_amounts as $amount) : ?>
                        <div class="amount-card" data-amount="<?php echo $amount; ?>" style="border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: white;">
                            <div style="font-size: 1.3rem; font-weight: 700; color: #28a745; margin-bottom: 8px;">
                                TZS <?php echo number_format($amount); ?>
                            </div>
                            <div style="font-size: 0.85rem; color: #6c757d;">
                                <?php 
                                switch ($amount) {
                                    case 10000: echo __('Basic health support', 'kilismile'); break;
                                    case 25000: echo __('Health checkup fund', 'kilismile'); break;
                                    case 50000: echo __('Education materials', 'kilismile'); break;
                                    case 100000: echo __('Community workshop', 'kilismile'); break;
                                    case 250000: echo __('Equipment support', 'kilismile'); break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- USD Amounts -->
            <div class="preset-amounts" id="amounts-usd" style="display: none;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px;">
                    <?php 
                    $usd_amounts = array(5, 10, 25, 50, 100);
                    foreach ($usd_amounts as $amount) : ?>
                        <div class="amount-card" data-amount="<?php echo $amount; ?>" style="border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: white;">
                            <div style="font-size: 1.3rem; font-weight: 700; color: #28a745; margin-bottom: 8px;">
                                $<?php echo $amount; ?>
                            </div>
                            <div style="font-size: 0.85rem; color: #6c757d;">
                                <?php 
                                switch ($amount) {
                                    case 5: echo __('Basic support', 'kilismile'); break;
                                    case 10: echo __('Health fund', 'kilismile'); break;
                                    case 25: echo __('Monthly impact', 'kilismile'); break;
                                    case 50: echo __('Program support', 'kilismile'); break;
                                    case 100: echo __('Major impact', 'kilismile'); break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Custom Amount -->
            <div style="text-align: center; margin-bottom: 35px;">
                <button type="button" id="custom-amount-btn" style="background: transparent; color: #28a745; border: 2px solid #28a745; padding: 12px 24px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.95rem;">
                    <?php _e('Enter Custom Amount', 'kilismile'); ?>
                </button>
                <div id="custom-amount-input" style="display: none; margin-top: 20px; max-width: 300px; margin-left: auto; margin-right: auto;">
                    <input type="number" class="amount-input" name="custom_amount" min="1" max="10000000" 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1.1rem; text-align: center; transition: all 0.3s ease;"
                           placeholder="<?php _e('Enter amount', 'kilismile'); ?>"
                           aria-describedby="custom-amount-error">
                    <div id="custom-amount-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
            </div>

            <!-- Support Area -->
            <div style="margin-bottom: 35px;">
                <h4 style="color: #2c5530; margin-bottom: 20px; font-size: 1.1rem; font-weight: 600; text-align: center;">
                    <?php _e('Choose support area:', 'kilismile'); ?>
                </h4>
                <div style="display: grid; gap: 12px; max-width: 600px; margin: 0 auto;">
                    <label class="purpose-option selected" style="display: flex; align-items: center; padding: 18px; border: 2px solid #28a745; border-radius: 10px; cursor: pointer; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); transition: all 0.3s ease;">
                        <input type="radio" name="donation_purpose" value="greatest_need" checked style="margin-right: 15px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-heart" style="color: white; font-size: 18px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #155724; margin-bottom: 3px; font-size: 1rem;"><?php _e('Greatest Need', 'kilismile'); ?></div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Direct funds where help is needed most urgently', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                    <label class="purpose-option" style="display: flex; align-items: center; padding: 18px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="donation_purpose" value="education" style="margin-right: 15px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #17a2b8, #138496); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; margin-bottom: 3px; font-size: 1rem;"><?php _e('Health Education', 'kilismile'); ?></div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Support community health workshops and training', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                    <label class="purpose-option" style="display: flex; align-items: center; padding: 18px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="donation_purpose" value="equipment" style="margin-right: 15px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #dc3545, #c82333); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-stethoscope" style="color: white; font-size: 18px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; margin-bottom: 3px; font-size: 1rem;"><?php _e('Medical Equipment', 'kilismile'); ?></div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Help fund essential medical tools and equipment', 'kilismile'); ?></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Options -->
            <div style="margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                <label style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; margin-bottom: 12px; background: white; transition: all 0.3s ease;">
                    <input type="checkbox" name="anonymous_donation" style="margin-right: 12px; transform: scale(1.2);">
                    <span style="font-size: 0.95rem; color: #495057;"><?php _e('Make this donation anonymous', 'kilismile'); ?></span>
                </label>
                <label class="recurring-label" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="checkbox" name="recurring_donation" class="recurring-checkbox" style="margin-right: 12px; transform: scale(1.2);">
                    <span style="font-size: 0.95rem; color: #495057;"><?php _e('Make this a recurring donation', 'kilismile'); ?></span>
                </label>
                <div class="recurring-options" style="display: none; margin-top: 12px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <select name="recurring_frequency" style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 0.95rem;">
                        <option value="monthly"><?php _e('Monthly', 'kilismile'); ?></option>
                        <option value="quarterly"><?php _e('Quarterly', 'kilismile'); ?></option>
                        <option value="yearly"><?php _e('Yearly', 'kilismile'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Step Navigation -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
            <button type="button" class="btn-prev-step" style="background: #f8f9fa; color: #6c757d; border: 2px solid #e9ecef; padding: 12px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                <?php _e('Back', 'kilismile'); ?>
            </button>
            <button type="button" class="btn-next-step" style="background: linear-gradient(135deg, #007bff, #6610f2); color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center;">
                <?php _e('Continue', 'kilismile'); ?>
                <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Payment & Confirmation -->
    <div class="donation-step" id="step-3" style="display: none; padding: 40px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="background: linear-gradient(135deg, #ffc107, #fd7e14); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-credit-card" style="color: white; font-size: 24px;"></i>
            </div>
            <h2 style="color: #2c5530; margin: 0 0 15px 0; font-size: 1.6rem; font-weight: 600;">
                <?php _e('Complete Your Donation', 'kilismile'); ?>
            </h2>
            <p style="color: #6c757d; margin: 0; font-size: 1rem; line-height: 1.5; max-width: 500px; margin-left: auto; margin-right: auto;">
                <?php _e('Review your donation details and choose your payment method.', 'kilismile'); ?>
            </p>
        </div>

        <!-- Donation Summary -->
        <div style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); border: 2px solid #28a745; border-radius: 12px; padding: 25px; margin-bottom: 35px; max-width: 500px; margin-left: auto; margin-right: auto;">
            <h4 style="color: #155724; margin-bottom: 20px; font-size: 1.2rem; font-weight: 600; text-align: center;">
                <?php _e('Donation Summary', 'kilismile'); ?>
            </h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="color: #495057; font-weight: 600;"><?php _e('Amount:', 'kilismile'); ?></span>
                <span class="summary-amount" style="color: #28a745; font-weight: 700; font-size: 1.1rem;">--</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="color: #495057; font-weight: 600;"><?php _e('Purpose:', 'kilismile'); ?></span>
                <span class="summary-purpose" style="color: #495057;">--</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="color: #495057; font-weight: 600;"><?php _e('Donor:', 'kilismile'); ?></span>
                <span class="summary-donor" style="color: #495057;">--</span>
            </div>
            <div class="summary-recurring" style="display: none; text-align: center; margin-top: 15px; padding: 12px; background: rgba(40, 167, 69, 0.1); border-radius: 8px;">
                <span style="color: #155724; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-sync" style="margin-right: 5px;"></i>
                    <?php _e('Recurring donation', 'kilismile'); ?>
                </span>
            </div>
        </div>

        <!-- Payment Methods -->
        <div style="max-width: 600px; margin: 0 auto;">
            <h4 style="color: #2c5530; margin-bottom: 20px; font-size: 1.1rem; font-weight: 600; text-align: center;">
                <?php _e('Choose payment method:', 'kilismile'); ?>
            </h4>
            <div style="display: grid; gap: 12px; margin-bottom: 30px;">
                
                <!-- PayPal for USD -->
                <label class="payment-option" data-currency="USD" style="display: flex; align-items: center; padding: 18px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="radio" name="payment_method" value="paypal" style="margin-right: 15px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #0070ba, #003087); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fab fa-paypal" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #495057; margin-bottom: 3px; font-size: 1rem;"><?php _e('PayPal', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Credit cards, debit cards, PayPal balance (USD)', 'kilismile'); ?></div>
                    </div>
                </label>
                
                <!-- AzamPay Mobile Money for TZS -->
                <label class="payment-option azampay-option" data-currency="TZS" style="display: flex; align-items: center; padding: 18px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; background: white; transition: all 0.3s ease;">
                    <input type="radio" name="payment_method" value="azampay" style="margin-right: 15px; transform: scale(1.2);">
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-mobile-alt" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #495057; margin-bottom: 3px; font-size: 1rem;"><?php _e('Mobile Money', 'kilismile'); ?></div>
                        <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('M-Pesa, Tigo Pesa, Airtel Money, Halopesa (TZS)', 'kilismile'); ?></div>
                    </div>
                </label>
                
            </div>
            
            <!-- Mobile Money Provider Selection (for AzamPay) -->
            <div class="mobile-money-providers" style="display: none; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 2px solid #e9ecef;">
                <h5 style="color: #2c5530; margin-bottom: 15px; font-size: 1rem; font-weight: 600; text-align: center;">
                    <?php _e('Select your mobile money provider:', 'kilismile'); ?>
                </h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                    
                    <label class="provider-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="mobile_provider" value="Mpesa" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; font-size: 0.95rem;"><?php _e('M-Pesa', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Vodacom', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                    <label class="provider-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="mobile_provider" value="Tigo" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #198754, #157347); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; font-size: 0.95rem;"><?php _e('Tigo Pesa', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Tigo', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                    <label class="provider-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="mobile_provider" value="Airtel" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #dc3545, #bb2d3b); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; font-size: 0.95rem;"><?php _e('Airtel Money', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Airtel', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                    <label class="provider-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; background: white; transition: all 0.3s ease;">
                        <input type="radio" name="mobile_provider" value="Halopesa" style="margin-right: 12px; transform: scale(1.2);">
                        <div style="background: linear-gradient(135deg, #fd7e14, #e66100); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 14px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; font-size: 0.95rem;"><?php _e('Halopesa', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Halotel', 'kilismile'); ?></div>
                        </div>
                    </label>
                    
                </div>
                
                <!-- Phone Number Input for Mobile Money -->
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c5530; font-size: 1rem;">
                        <?php _e('Mobile Phone Number', 'kilismile'); ?> <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="tel" name="mobile_phone" 
                           style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fff;"
                           placeholder="<?php _e('Enter phone number (e.g., +255712345678)', 'kilismile'); ?>" 
                           aria-describedby="mobile-phone-error">
                    <div id="mobile-phone-error" class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-top: 5px;">
                        <?php _e('You will receive an STK push notification to complete the payment', 'kilismile'); ?>
                    </div>
                </div>
            </div>

            <!-- AzamPay Payment Method Selection -->
            <div class="azampay-method-selection" style="display: none; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 2px solid #e9ecef;">
                <h5 style="color: #2c5530; margin-bottom: 15px; font-size: 1rem; font-weight: 600; text-align: center;">
                    <?php _e('Choose your preferred payment method:', 'kilismile'); ?>
                </h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    
                    <!-- STK Push Option -->
                    <label class="azampay-method-option" data-method="stkpush" style="display: flex; flex-direction: column; align-items: center; padding: 20px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; background: white; transition: all 0.3s ease; text-align: center;">
                        <input type="radio" name="azampay_method" value="stkpush" checked style="margin-bottom: 12px; transform: scale(1.3);">
                        <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <i class="fas fa-mobile-alt" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; margin-bottom: 5px; font-size: 1rem;"><?php _e('STK Push', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d; line-height: 1.4;"><?php _e('Direct mobile payment - immediate notification to your phone', 'kilismile'); ?></div>
                        </div>
                    </label>

                    <!-- Checkout Page Option -->
                    <label class="azampay-method-option" data-method="checkout" style="display: flex; flex-direction: column; align-items: center; padding: 20px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; background: white; transition: all 0.3s ease; text-align: center;">
                        <input type="radio" name="azampay_method" value="checkout" style="margin-bottom: 12px; transform: scale(1.3);">
                        <div style="background: linear-gradient(135deg, #007bff, #0056b3); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <i class="fas fa-credit-card" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #495057; margin-bottom: 5px; font-size: 1rem;"><?php _e('Checkout Page', 'kilismile'); ?></div>
                            <div style="font-size: 0.8rem; color: #6c757d; line-height: 1.4;"><?php _e('Full payment page with multiple options and better user interface', 'kilismile'); ?></div>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Security Notice -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-lock" style="color: #28a745; margin-right: 8px; font-size: 1rem;"></i>
                        <span style="font-size: 0.9rem; color: #495057; font-weight: 600;">SSL Encrypted</span>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-shield-alt" style="color: #007bff; margin-right: 8px; font-size: 1rem;"></i>
                        <span style="font-size: 0.9rem; color: #495057; font-weight: 600;">Secure Payment</span>
                    </div>
                </div>
                <p style="margin: 0; font-size: 0.85rem; color: #6c757d; line-height: 1.4;">
                    <?php _e('Your donation is secure and encrypted. We never store your payment information.', 'kilismile'); ?>
                </p>
            </div>
        </div>

        <!-- Step Navigation -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
            <button type="button" class="btn-prev-step" style="background: #f8f9fa; color: #6c757d; border: 2px solid #e9ecef; padding: 12px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                <?php _e('Back', 'kilismile'); ?>
            </button>
            <button type="button" class="btn-process-payment" style="background: linear-gradient(135deg, #28a745, #218838); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center;">
                <?php _e('Complete Donation', 'kilismile'); ?>
                <i class="fas fa-heart" style="margin-left: 8px;"></i>
            </button>
        </div>
    </div>

    <!-- Step 4: Confirmation -->
    <div class="donation-step" id="step-4" style="display: none; padding: 40px; text-align: center;">
        <div style="margin-bottom: 40px;">
            <div style="width: 80px; height: 80px; background-color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                <i class="fas fa-check" style="color: white; font-size: 35px;"></i>
            </div>
            <h2 style="color: #28a745; font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;"><?php _e('Thank You!', 'kilismile'); ?></h2>
            <p style="color: #6c757d; font-size: 1rem; margin: 0 auto 25px; max-width: 500px; line-height: 1.5;">
                <?php _e('Your donation has been processed successfully. You\'ve just made a significant difference in the lives of children in Tanzania.', 'kilismile'); ?>
            </p>
        </div>
        
        <!-- Receipt -->
        <div style="background: #f8f9fa; border-radius: 12px; padding: 30px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
            <h3 style="margin-top: 0; color: #333; font-size: 1.2rem; margin-bottom: 20px; text-align: center;">
                <?php _e('Donation Receipt', 'kilismile'); ?>
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.95rem;">
                <div>
                    <div style="color: #6c757d; margin-bottom: 5px; font-weight: 600;"><?php _e('Amount', 'kilismile'); ?></div>
                    <div style="font-weight: 700; color: #28a745;" id="receipt-amount">--</div>
                </div>
                <div>
                    <div style="color: #6c757d; margin-bottom: 5px; font-weight: 600;"><?php _e('Purpose', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="receipt-purpose">--</div>
                </div>
                <div>
                    <div style="color: #6c757d; margin-bottom: 5px; font-weight: 600;"><?php _e('Donor', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="receipt-donor">--</div>
                </div>
                <div>
                    <div style="color: #6c757d; margin-bottom: 5px; font-weight: 600;"><?php _e('Date', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;"><?php echo date('F j, Y'); ?></div>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; text-align: center;">
                <p style="margin: 0; font-size: 0.85rem; color: #6c757d;">
                    <?php _e('A confirmation email has been sent to your email address.', 'kilismile'); ?>
                </p>
            </div>
        </div>
        
        <!-- Return Button -->
        <div>
            <a href="<?php echo esc_url(home_url()); ?>" class="home-link" style="display: inline-flex; align-items: center; background: #f8f9fa; color: #6c757d; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                <i class="fas fa-home" style="margin-right: 8px;"></i>
                <?php _e('Return to Homepage', 'kilismile'); ?>
            </a>
        </div>
    </div>

</form>

</div>
        
        <!-- Donation Receipt -->
        <div style="background: #f8f9fa; border-radius: 10px; padding: 25px; max-width: 600px; margin: 0 auto 30px;">
            <h3 style="margin-top: 0; color: #333; font-size: 1.2rem; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px;">
                <?php _e('Donation Receipt', 'kilismile'); ?>
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Donation ID', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="donation-id">KS-<?php echo date('Ymd') . '-' . rand(1000, 9999); ?></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Date', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;"><?php echo date('F j, Y'); ?></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Donor Name', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="receipt-donor-name"></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Email', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="receipt-donor-email"></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Amount', 'kilismile'); ?></div>
                    <div style="font-weight: 700; color: #28a745;" id="receipt-amount"></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #6c757d; margin-bottom: 3px;"><?php _e('Purpose', 'kilismile'); ?></div>
                    <div style="font-weight: 600; color: #333;" id="receipt-purpose"></div>
                </div>
            </div>
            
            <div style="font-size: 0.85rem; color: #6c757d; text-align: center; margin-top: 20px;">
                <?php _e('A confirmation email has been sent to your email address.', 'kilismile'); ?>
            </div>
        </div>
        
        <!-- Share -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h3 style="color: #333; font-size: 1.1rem; margin-bottom: 15px;">
                <?php _e('Share Your Impact', 'kilismile'); ?>
            </h3>
            <div style="display: flex; justify-content: center; gap: 15px;">
                <a href="#" style="background: #3b5998; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" style="background: #1da1f2; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" style="background: #0e76a8; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" style="background: #25D366; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
        
        <!-- Return Button -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo esc_url(home_url()); ?>" class="home-link" style="display: inline-flex; align-items: center; background: #f8f9fa; color: #6c757d; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: 600;">
                <i class="fas fa-home" style="margin-right: 8px;"></i>
                <?php _e('Return to Homepage', 'kilismile'); ?>
            </a>
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
    
    <!-- Floating Donation Summary -->
    <div class="donation-summary" style="position: fixed; bottom: 30px; right: 30px; background: white; padding: 25px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.15); z-index: 1000; min-width: 280px; border: 2px solid #28a745; backdrop-filter: blur(10px); display: none;">
        <div style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <i class="fas fa-heart" style="color: white; font-size: 16px;"></i>
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 2px;">Your Donation</div>
                    <div class="summary-amount" style="font-size: 1.6rem; font-weight: 800; color: #28a745; line-height: 1;">--</div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                <div class="summary-type" style="font-size: 0.9rem; color: #495057; margin-bottom: 5px; font-weight: 600;">--</div>
                <div class="summary-donor" style="font-size: 0.8rem; color: #28a745; font-weight: 600;">--</div>
                <div class="summary-recurring" style="display: none; margin-top: 8px; padding: 6px 12px; background: #e8f5e8; border-radius: 20px; font-size: 0.75rem; color: #155724; font-weight: 600; border: 1px solid #c3e6cb;">
                    <i class="fas fa-sync-alt" style="margin-right: 5px;"></i>
                    <?php _e('Recurring', 'kilismile'); ?>
                </div>
            </div>
            
            <div style="font-size: 0.8rem; color: #6c757d; line-height: 1.4;">
                <i class="fas fa-shield-alt" style="color: #28a745; margin-right: 5px;"></i>
                <?php _e('Secure & Encrypted', 'kilismile'); ?>
            </div>
        </div>
    </div>
    
</div>

<!-- Enhanced Custom Styles -->
<style>
    @media (max-width: 1024px) {
        .donation-hero {
            padding: 40px 20px !important;
        }
        
        .donation-hero h1 {
            font-size: 2.5rem !important;
        }
        
        .donation-hero p {
            font-size: 1.1rem !important;
        }
        
        .trust-section {
            padding: 20px !important;
        }
        
        .trust-section > div {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }
    }
    
    @media (max-width: 768px) {
        .donation-hero {
            padding: 30px 15px !important;
        }
        
        .donation-hero h1 {
            font-size: 2rem !important;
        }
        
        .donation-hero > div > div {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }
        
        .donation-container {
            padding: 20px 15px !important;
        }
        
        .donation-step {
            padding: 30px 20px !important;
        }
        
        .step-progress {
            padding: 20px 15px !important;
        }
        
        .step-indicator {
            margin: 0 5px !important;
        }
        
        .step-circle {
            width: 50px !important;
            height: 50px !important;
            font-size: 1rem !important;
        }
        
        .step-indicator span {
            font-size: 0.8rem !important;
        }
        
        .individual-info > div,
        .business-info > div {
            grid-template-columns: 1fr !important;
        }
        
        .donor-type-option {
            padding: 25px 15px !important;
        }
        
        .donation-summary {
            bottom: 15px !important;
            right: 15px !important;
            left: 15px !important;
            min-width: auto !important;
        }
        
        .mobile-money-providers > div {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 480px) {
        .donation-hero h1 {
            font-size: 1.8rem !important;
        }
        
        .step-progress > div {
            flex-direction: column !important;
            gap: 15px !important;
        }
        
        .progress-fill {
            display: none !important;
        }
        
        .donor-type-option {
            grid-template-columns: 1fr !important;
        }
        
        .step-header {
            margin-bottom: 30px !important;
        }
        
        .step-header h2 {
            font-size: 1.6rem !important;
        }
        
        .step-header p {
            font-size: 1rem !important;
        }
    }
    
    /* Enhanced hover and focus states */
    .donor-type-option:hover, .provider-option:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 25px rgba(40,167,69,0.25) !important;
    }
    
    input:focus, select:focus, textarea:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2) !important;
        outline: none !important;
    }
    
    .btn-next-step, .btn-prev-step {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .btn-next-step:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(40,167,69,0.3) !important;
    }
    
    .btn-prev-step:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(108,117,125,0.3) !important;
    }
    
    /* Smooth animations */
    .donation-step {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Loading animation for progress fill */
    .progress-fill {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(90deg, #28a745, #20c997, #28a745);
        background-size: 200% 100%;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    /* Provider option transitions */
    .provider-option {
        transition: all 0.3s ease !important;
    }
    
    .provider-option:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
// Localize AJAX for WordPress - with fallback
if (typeof kilismile_ajax === 'undefined') {
    window.kilismile_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>'
    };
}

// Check for WordPress donation script localization
if (typeof kilismileDonation !== 'undefined') {
    // Use the properly localized data from WordPress
    window.kilismile_ajax.ajax_url = kilismileDonation.ajax_url;
    window.kilismile_ajax.nonce = kilismileDonation.payment_nonce || kilismileDonation.nonce;
}

// Ensure WordPress utilities are available
if (typeof wp === 'undefined') {
    window.wp = window.wp || {
        ajax: {
            post: function(action, data) {
                return jQuery.post(kilismile_ajax.ajax_url, {
                    action: action,
                    ...data
                });
            }
        }
    };
}

jQuery(document).ready(function($) {
    let selectedAmount = 0;
    let selectedCurrency = 'TZS';
    let donorType = 'individual';
    let currentStep = 1;
    
    // Enhanced form validation
    function validateField(input, errorMessage = '') {
        const errorDiv = input.siblings('.error-message');
        const value = input.val().trim();
        
        if (input.prop('required') && !value) {
            input.css('border-color', '#dc3545');
            errorDiv.text(errorMessage || 'This field is required').show();
            return false;
        } else if (input.attr('type') === 'email' && value && !isValidEmail(value)) {
            input.css('border-color', '#dc3545');
            errorDiv.text('Please enter a valid email address').show();
            return false;
        } else {
            input.css('border-color', '#28a745');
            errorDiv.hide();
            return true;
        }
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    // Real-time field validation
    $('input[required], input[type="email"]').on('blur', function() {
        validateField($(this));
    });
    
    // Step Navigation with enhanced validation
    $('.btn-next-step').on('click', function() {
        const currentStepDiv = $(this).closest('.donation-step');
        let isValid = true;
        
        // Step 1 validation
        if (currentStepDiv.attr('id') === 'step-1') {
            const requiredFields = donorType === 'individual' ? 
                ['first_name', 'last_name', 'email'] : 
                ['company_name', 'contact_person', 'business_email'];
            
            requiredFields.forEach(function(fieldName) {
                const input = $(`input[name="${fieldName}"]`);
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                showNotification('Please fill in all required fields correctly', 'error');
                return;
            }
        }
        
        // Step 2 validation
        if (currentStepDiv.attr('id') === 'step-2') {
            if (selectedAmount <= 0) {
                showNotification('Please select or enter a valid donation amount', 'error');
                return;
            }
            
            if ($('input[name="donation_purpose"]:checked').length === 0) {
                showNotification('Please select a donation purpose', 'error');
                return;
            }
        }
        
        if (isValid) {
            nextStep();
        }
    });
    
    $('.btn-prev-step').on('click', function() {
        prevStep();
    });
    
    function nextStep() {
        currentStep++;
        updateProgressIndicator();
        showStep(currentStep);
        updateDonationSummary();
        
        // Smooth scroll to top
        $('html, body').animate({scrollTop: 0}, 300);
    }
    
    function prevStep() {
        currentStep--;
        updateProgressIndicator();
        showStep(currentStep);
        
        $('html, body').animate({scrollTop: 0}, 300);
    }
    
    function showStep(stepNumber) {
        $('.donation-step').hide();
        $(`#step-${stepNumber}`).fadeIn(300);
    }
    
    function updateProgressIndicator() {
        $('.step-indicator').removeClass('active completed');
        
        // Mark completed steps
        for (let i = 1; i < currentStep; i++) {
            $(`.step-indicator[data-step="${i}"] .step-circle`).css({
                'background': '#28a745',
                'color': 'white'
            });
            $(`.step-indicator[data-step="${i}"] span`).css('color', '#28a745');
            $(`.step-indicator[data-step="${i}"]`).addClass('completed');
        }
        
        // Mark current step
        $(`.step-indicator[data-step="${currentStep}"] .step-circle`).css({
            'background': '#28a745',
            'color': 'white'
        });
        $(`.step-indicator[data-step="${currentStep}"] span`).css('color', '#28a745');
        $(`.step-indicator[data-step="${currentStep}"]`).addClass('active');
        
        // Update progress fill
        const progressPercentage = ((currentStep - 1) / 2) * 100;
        $('.progress-fill').css('width', `${progressPercentage}%`);
    }
    
    // Donor type selection
    $('.donor-type-option').on('click', function() {
        donorType = $(this).find('input[type="radio"]').val();
        
        $('.donor-type-option').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        if (donorType === 'individual') {
            $(this).css({
                'border-color': '#28a745',
                'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
            });
            $('.individual-info').slideDown(300);
            $('.business-info').slideUp(300);
        } else {
            $(this).css({
                'border-color': '#6f42c1',
                'background': 'linear-gradient(135deg, #f8f5ff 0%, #ede5ff 100%)'
            });
            $('.individual-info').slideUp(300);
            $('.business-info').slideDown(300);
        }
        
        updateDonationSummary();
    });
    
    // Currency toggle
    $('.currency-btn').on('click', function() {
        const currency = $(this).data('currency');
        selectedCurrency = currency;
        
        $('.currency-btn').removeClass('active').css({
            'background': 'transparent',
            'color': '#28a745'
        });
        
        $(this).addClass('active').css({
            'background': '#28a745',
            'color': 'white'
        });
        
        if (currency === 'USD') {
            $('#amounts-tzs').slideUp(200);
            $('#amounts-usd').slideDown(200);
        } else {
            $('#amounts-usd').slideUp(200);
            $('#amounts-tzs').slideDown(200);
        }
        
        // Reset amount selection
        selectedAmount = 0;
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        $('.amount-input').val('');
        updateDonationSummary();
    });
    
    // Amount selection with enhanced UI
    $('.amount-card').on('click', function() {
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white',
            'transform': 'scale(1)'
        });
        
        $(this).addClass('selected').css({
            'border-color': '#28a745',
            'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)',
            'transform': 'scale(1.02)'
        });
        
        selectedAmount = $(this).data('amount');
        $('#custom-amount-input').slideUp(200);
        $('.amount-input').val('');
        updateDonationSummary();
    });
    
    // Custom amount
    $('#custom-amount-btn').on('click', function() {
        $('#custom-amount-input').slideToggle(300);
        $('.amount-card').removeClass('selected').css({
            'border-color': '#e9ecef',
            'background': 'white',
            'transform': 'scale(1)'
        });
        selectedAmount = 0;
        setTimeout(() => $('#custom-amount-input input').focus(), 350);
    });
    
    $('.amount-input').on('input', function() {
        selectedAmount = parseFloat($(this).val()) || 0;
        updateDonationSummary();
        
        if (selectedAmount > 0) {
            validateField($(this));
        }
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
    
    // Payment method selection with provider visibility
    $('.payment-option').on('click', function() {
        $('.payment-option').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).css({
            'border-color': '#007bff',
            'background': 'linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%)'
        });
        
        const selectedMethod = $(this).find('input[name="payment_method"]').val();
        const selectedCurrency = $(this).data('currency');
        
        // Show/hide mobile money providers and method selection based on payment method
        if (selectedMethod === 'azampay') {
            $('.mobile-money-providers').slideDown(300);
            $('.azampay-method-selection').slideDown(300);
            // Switch to TZS currency if not already
            if (selectedCurrency === 'TZS' && selectedCurrency !== $('.currency-btn.active').data('currency')) {
                $('.currency-btn[data-currency="TZS"]').trigger('click');
            }
        } else {
            $('.mobile-money-providers').slideUp(300);
            $('.azampay-method-selection').slideUp(300);
            // Switch to USD currency for PayPal
            if (selectedCurrency === 'USD' && selectedCurrency !== $('.currency-btn.active').data('currency')) {
                $('.currency-btn[data-currency="USD"]').trigger('click');
            }
        }
    });
    
    // Provider selection
    $('.provider-option').on('click', function() {
        $('.provider-option').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).css({
            'border-color': '#28a745',
            'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
        });
    });

    // AzamPay method selection
    $('.azampay-method-option').on('click', function() {
        $('.azampay-method-option').css({
            'border-color': '#e9ecef',
            'background': 'white'
        });
        
        $(this).css({
            'border-color': '#007bff',
            'background': 'linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%)'
        });
        
        const selectedMethod = $(this).data('method');
        
        // Update the mobile money providers visibility based on method
        if (selectedMethod === 'stkpush') {
            $('.mobile-money-providers').slideDown(300);
        } else {
            $('.mobile-money-providers').slideUp(300);
        }
    });
    
    // Recurring donation toggle
    $('.recurring-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('.recurring-options').slideDown(300);
        } else {
            $('.recurring-options').slideUp(300);
        }
        updateDonationSummary();
    });
    
    // Process payment with real integration
    $('.btn-process-payment').on('click', function() {
        // Validate payment method selection
        const selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
        if (!selectedPaymentMethod) {
            showNotification('Please select a payment method', 'error');
            return;
        }
        
        // For AzamPay, validate method selection and required fields
        if (selectedPaymentMethod === 'azampay') {
            const azampayMethod = $('input[name="azampay_method"]:checked').val();
            
            if (!azampayMethod) {
                showNotification('Please select your preferred AzamPay payment method', 'error');
                return;
            }
            
            // For STK Push, validate mobile money provider and phone
            if (azampayMethod === 'stkpush') {
                const selectedProvider = $('input[name="mobile_provider"]:checked').val();
                const mobilePhone = $('input[name="mobile_phone"]').val().trim();
                
                if (!selectedProvider) {
                    showNotification('Please select your mobile money provider', 'error');
                    return;
                }
                
                if (!mobilePhone) {
                    showNotification('Please enter your mobile phone number', 'error');
                    return;
                }
                
                // Validate phone number format
                if (!isValidPhoneNumber(mobilePhone)) {
                    showNotification('Please enter a valid Tanzanian phone number (e.g., +255712345678)', 'error');
                    return;
                }
            }
            // For checkout page, no additional validation needed
        }
        
        const button = $(this);
        const originalText = button.html();
        
        // Show processing state
        button.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        
        // Collect form data
        const formData = collectFormData();
        
        // Debug: Log form data being sent
        console.log('Form Data to Send:', formData);
        console.log('Payment Gateway:', determineGateway(formData.payment_method));
        
        // Process payment via KiliSmile Payments Plugin
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'kilismile_process_payment',
                nonce: '<?php echo wp_create_nonce('kilismile_payments_nonce'); ?>',
                gateway: determineGateway(formData.payment_method),
                ...formData
            },
            success: function(response) {
                try {
                    console.log('Payment Response:', response);
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.success) {
                        console.log('Payment Success Data:', result.data);
                        handlePaymentSuccess(result);
                    } else {
                        console.log('Payment Failed:', result);
                        handlePaymentError(result.data ? result.data.message : (result.message || 'Payment processing failed'));
                    }
                } catch (e) {
                    console.error('Payment response parsing error:', e, 'Raw response:', response);
                    handlePaymentError('Payment processing failed due to server error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Payment AJAX error details:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                // Try to parse error response for more specific error message
                let errorMessage = 'Payment processing failed. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.data && errorResponse.data.message) {
                        errorMessage = errorResponse.data.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                
                handlePaymentError(errorMessage);
            },
            complete: function() {
                // Reset button
                button.html(originalText).prop('disabled', false);
            }
        });
    });
    
    function collectFormData() {
        const donorType = $('input[name="donor_type"]:checked').val();
        
        let donorName = '';
        let donorEmail = '';
        let donorPhone = '';
        
        if (donorType === 'individual') {
            donorName = $('input[name="first_name"]').val().trim() + ' ' + $('input[name="last_name"]').val().trim();
            donorEmail = $('input[name="email"]').val().trim();
            donorPhone = $('input[name="phone"]').val().trim();
        } else {
            donorName = $('input[name="company_name"]').val().trim();
            donorEmail = $('input[name="business_email"]').val().trim();
            donorPhone = $('input[name="business_phone"]').val().trim();
        }
        
        const selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
        const donationPurpose = $('input[name="donation_purpose"]:checked').val();
        const isRecurring = $('.recurring-checkbox').is(':checked');
        
        // Convert amount to appropriate currency for gateway
        let finalAmount = selectedAmount;
        let finalCurrency = selectedCurrency;
        const gateway = determineGateway(selectedPaymentMethod);
        
        if (gateway === 'azampay' && selectedCurrency === 'USD') {
            // Convert USD to TZS for AzamPay
            finalAmount = Math.round(selectedAmount * 2350); // Use exchange rate
            finalCurrency = 'TZS';
        } else if (gateway === 'paypal' && selectedCurrency === 'TZS') {
            // Convert TZS to USD for PayPal
            finalAmount = Math.round((selectedAmount / 2350) * 100) / 100; // Round to 2 decimals
            finalCurrency = 'USD';
        }
        
        const data = {
            transaction_type: 'donation',
            currency: finalCurrency,
            amount: finalAmount,
            donor_name: donorName,
            donor_email: donorEmail,
            donor_phone: donorPhone,
            payment_method: selectedPaymentMethod,
            donation_purpose: donationPurpose,
            recurring: isRecurring ? 1 : 0,
            donor_type: donorType,
            testing_bypass: true // Enable for testing
        };
        
        // Add mobile money specific data for AzamPay
        if (selectedPaymentMethod === 'mobile_money' || selectedPaymentMethod === 'azampay') {
            const mobileNetwork = $('select[name="mobile_network"]').val() || 'mpesa';
            data.mobile_network = mobileNetwork;
            data.azampay_type = 'stkpush'; // Default to STK Push
            
            // Use mobile phone if provided, otherwise use regular phone
            const mobilePhone = $('input[name="mobile_phone"]').val().trim();
            if (mobilePhone) {
                data.donor_phone = mobilePhone;
            }
        }
        
        // Add recurring frequency if applicable
        if (isRecurring) {
            data.recurring_interval = $('select[name="recurring_frequency"]').val() || 'monthly';
        }
        
        return data;
    }
    
    // Determine payment gateway based on payment method
    function determineGateway(paymentMethod) {
        switch (paymentMethod) {
            case 'paypal':
            case 'credit_card':
                return 'paypal';
            case 'mobile_money':
            case 'azampay':
            case 'stkpush':
                return 'azampay';
            default:
                // Default to AzamPay for Tanzania-based donations
                return selectedCurrency === 'TZS' ? 'azampay' : 'paypal';
        }
    }
    
    function handlePaymentSuccess(result) {
        if (result.payment_method === 'paypal' && result.redirect_url) {
            // Redirect to PayPal for approval
            showNotification('Redirecting to PayPal...', 'info');
            window.location.href = result.redirect_url;
        } else if (result.payment_method === 'azampay' || result.payment_method === 'azampay_checkout') {
            if (result.redirect_url) {
                // AzamPay Checkout - redirect to hosted payment page
                showNotification('Redirecting to AzamPay checkout page...', 'info');
                window.location.href = result.redirect_url;
            } else {
                // AzamPay STK Push - update UI and poll for status
                showNotification(result.message || 'STK push sent to your phone. Please complete the payment.', 'success');
                
                // Update summary with transaction details
                const donorName = donorType === 'individual' ? 
                    $('input[name="first_name"]').val() + ' ' + $('input[name="last_name"]').val() :
                    $('input[name="company_name"]').val();
                
                $('#receipt-amount').text(`${selectedCurrency} ${selectedAmount.toLocaleString()}`);
                $('#receipt-donor').text(donorName);
                
                const purposeText = getPurposeText($('input[name="donation_purpose"]:checked').val());
                $('#receipt-purpose').text(purposeText);
                
                // Start polling for payment status
                startPaymentStatusPolling(result.donation_id);
                
                // Show success step
                currentStep = 4;
                updateProgressIndicator();
                showStep(4);
            }
        }
    }
    
    function handlePaymentError(message) {
        showNotification(message, 'error');
        console.error('Payment processing failed:', message);
    }
    
    function startPaymentStatusPolling(donationId) {
        let pollCount = 0;
        const maxPolls = 30; // Poll for 5 minutes (30 * 10 seconds)
        
        const pollInterval = setInterval(function() {
            pollCount++;
            
            if (pollCount > maxPolls) {
                clearInterval(pollInterval);
                showNotification('Payment status check timed out. Please contact support if you completed the payment.', 'warning');
                return;
            }
            
            $.ajax({
                url: kilismile_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_check_payment_status',
                    nonce: kilismile_ajax.nonce,
                    donation_id: donationId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.success && result.status === 'completed') {
                            clearInterval(pollInterval);
                            showNotification('Payment completed successfully!', 'success');
                            // You might want to update the UI or redirect to success page
                        }
                    } catch (e) {
                        console.error('Status check parsing error:', e);
                    }
                },
                error: function() {
                    console.error('Status check failed');
                }
            });
        }, 10000); // Poll every 10 seconds
    }
    
    function isValidPhoneNumber(phone) {
        // Tanzania phone number validation
        const phoneRegex = /^(\+?255|0)[67]\d{8}$/;
        return phoneRegex.test(phone.replace(/\s+/g, ''));
    }
    
    function getPurposeText(value) {
        const purposes = {
            'greatest_need': 'Greatest Need',
            'education': 'Health Education',
            'equipment': 'Medical Equipment',
            'outreach': 'Community Outreach'
        };
        return purposes[value] || 'General Support';
    }
    
    // Update donation summary
    function updateDonationSummary() {
        if (selectedAmount > 0) {
            $('.summary-amount').text(`${selectedCurrency} ${selectedAmount.toLocaleString()}`);
            
            let donationType = $('.recurring-checkbox').is(':checked') ? 
                'Recurring donation' : 'One-time donation';
            
            const selectedPurpose = $('input[name="donation_purpose"]:checked').val();
            if (selectedPurpose) {
                donationType += ' - ' + getPurposeText(selectedPurpose);
            }
            
            $('.summary-type').text(donationType);
            
            // Update donor name
            let donorName = '';
            if (donorType === 'individual') {
                const firstName = $('input[name="first_name"]').val() || '';
                const lastName = $('input[name="last_name"]').val() || '';
                donorName = (firstName + ' ' + lastName).trim() || 'Individual Donor';
            } else {
                donorName = $('input[name="company_name"]').val() || 'Business Donor';
            }
            $('.summary-donor').text(donorName);
            
            // Show recurring indicator if applicable
            if ($('.recurring-checkbox').is(':checked')) {
                $('.summary-recurring').show();
            } else {
                $('.summary-recurring').hide();
            }
        } else {
            $('.summary-amount').text('--');
            $('.summary-type').text('--');
            $('.summary-donor').text('--');
            $('.summary-recurring').hide();
        }
    }
    
    // Notification system
    function showNotification(message, type = 'info') {
        const colors = {
            'success': '#28a745',
            'error': '#dc3545',
            'info': '#007bff'
        };
        
        const notification = $(`
            <div style="position: fixed; top: 20px; right: 20px; background: ${colors[type]}; color: white; padding: 15px 20px; border-radius: 8px; z-index: 10000; font-weight: 600; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Initialize form
    updateProgressIndicator();
    updateDonationSummary();
    
    // Show donation summary after user starts interacting
    setTimeout(() => {
        $('.donation-summary').fadeIn(500);
    }, 3000);
    
    // Enhanced hover effects with 3D transforms
    $('.amount-card, .purpose-option, .payment-option, .donor-type-option').hover(
        function() {
            if (!$(this).hasClass('selected')) {
                $(this).css({
                    'transform': 'translateY(-4px) scale(1.02)',
                    'box-shadow': '0 12px 30px rgba(0,0,0,0.15)'
                });
            }
        },
        function() {
            if (!$(this).hasClass('selected')) {
                $(this).css({
                    'transform': 'translateY(0) scale(1)',
                    'box-shadow': 'none'
                });
            }
        }
    );
    
    // Enhanced focus management for accessibility
    $('input, select, button, .donor-type-option, .amount-card, .purpose-option, .payment-option').on('focus', function() {
        $(this).css({
            'box-shadow': '0 0 0 4px rgba(40, 167, 69, 0.25)',
            'outline': 'none'
        });
    }).on('blur', function() {
        if (!$(this).hasClass('selected')) {
            $(this).css('box-shadow', 'none');
        }
    });
    
    // Smooth scroll to errors
    function scrollToFirstError() {
        const firstError = $('.error-message:visible').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    
    // Enhanced step transitions with better animations
    function showStep(stepNumber) {
        $('.donation-step').fadeOut(200, function() {
            $(`#step-${stepNumber}`).fadeIn(400);
            
            // Add entrance animation
            $(`#step-${stepNumber}`).css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            }).animate({
                'opacity': '1',
                'transform': 'translateY(0)'
            }, 400);
        });
    }
    
    // Progress indicator enhancement
    function updateProgressIndicator() {
        $('.step-indicator').removeClass('active completed');
        
        // Animate completed steps
        for (let i = 1; i < currentStep; i++) {
            const stepIndicator = $(`.step-indicator[data-step="${i}"]`);
            stepIndicator.addClass('completed');
            
            stepIndicator.find('.step-circle').css({
                'background': 'linear-gradient(135deg, #28a745, #20c997)',
                'color': 'white',
                'border-color': '#28a745',
                'transform': 'scale(1.1)'
            });
            
            stepIndicator.find('span').css('color', '#28a745');
            
            // Add checkmark animation
            setTimeout(() => {
                stepIndicator.find('.step-circle').html('<i class="fas fa-check" style="font-size: 1.1rem;"></i>');
            }, 200);
        }
        
        // Animate current step
        const currentStepIndicator = $(`.step-indicator[data-step="${currentStep}"]`);
        currentStepIndicator.addClass('active');
        
        currentStepIndicator.find('.step-circle').css({
            'background': 'linear-gradient(135deg, #28a745, #20c997)',
            'color': 'white',
            'border-color': '#28a745',
            'box-shadow': '0 6px 20px rgba(40,167,69,0.4)',
            'transform': 'scale(1.15)'
        });
        
        currentStepIndicator.find('span').css('color', '#28a745');
        
        // Update progress fill with animation
        const progressPercentage = ((currentStep - 1) / 2) * 100;
        $('.progress-fill').animate({
            width: `${progressPercentage}%`
        }, 600, 'easeInOutCubic');
    }
    
    // Enhanced notification system with better positioning
    function showNotification(message, type = 'info') {
        const colors = {
            'success': '#28a745',
            'error': '#dc3545',
            'info': '#17a2b8',
            'warning': '#ffc107'
        };
        
        const icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'info': 'fas fa-info-circle',
            'warning': 'fas fa-exclamation-triangle'
        };
        
        const notification = $(`
            <div style="position: fixed; top: 30px; right: 30px; background: ${colors[type]}; color: white; padding: 20px 25px; border-radius: 12px; z-index: 10001; font-weight: 600; box-shadow: 0 8px 30px rgba(0,0,0,0.2); max-width: 400px; transform: translateX(100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                <div style="display: flex; align-items: center;">
                    <i class="${icons[type]}" style="margin-right: 12px; font-size: 1.2rem;"></i>
                    <div>${message}</div>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        // Slide in animation
        setTimeout(() => {
            notification.css('transform', 'translateX(0)');
        }, 100);
        
        // Auto hide with slide out
        setTimeout(() => {
            notification.css('transform', 'translateX(100%)');
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, 4000);
    }
    
    // Dynamic donation summary updates
    function updateDonationSummary() {
        const summaryCard = $('.donation-summary');
        
        if (selectedAmount > 0) {
            summaryCard.show();
            
            $('.summary-amount').text(`${selectedCurrency} ${selectedAmount.toLocaleString()}`);
            
            let donationType = $('.recurring-checkbox').is(':checked') ? 
                'Recurring donation' : 'One-time donation';
            
            const selectedPurpose = $('input[name="donation_purpose"]:checked').val();
            if (selectedPurpose) {
                donationType += ' • ' + getPurposeText(selectedPurpose);
            }
            
            $('.summary-type').text(donationType);
            
            // Update donor name with animation
            let donorName = '';
            if (donorType === 'individual') {
                const firstName = $('input[name="first_name"]').val() || '';
                const lastName = $('input[name="last_name"]').val() || '';
                donorName = (firstName + ' ' + lastName).trim() || 'Individual Donor';
            } else {
                donorName = $('input[name="company_name"]').val() || 'Business Donor';
            }
            $('.summary-donor').text(donorName);
            
            // Show recurring indicator with animation
            if ($('.recurring-checkbox').is(':checked')) {
                $('.summary-recurring').slideDown(300);
            } else {
                $('.summary-recurring').slideUp(300);
            }
            
            // Pulse animation for amount change
            $('.summary-amount').addClass('pulse');
            setTimeout(() => $('.summary-amount').removeClass('pulse'), 600);
            
        } else {
            $('.summary-amount').text('--');
            $('.summary-type').text('--');
            $('.summary-donor').text('--');
            $('.summary-recurring').hide();
        }
    }
});

// Enhanced responsive styles
const style = document.createElement('style');
style.textContent = `
    @media (max-width: 768px) {
        .donation-container {
            padding: 10px !important;
        }
        
        .donation-step {
            padding: 20px !important;
        }
        
        .step-progress {
            padding: 15px !important;
        }
        
        .step-indicator {
            margin: 0 10px !important;
        }
        
        .step-circle {
            width: 40px !important;
            height: 40px !important;
            font-size: 0.9rem !important;
        }
        
        .individual-info, .business-info {
            grid-template-columns: 1fr !important;
        }
        
        .preset-amounts > div {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        .amount-card {
            padding: 15px !important;
        }
        
        .purpose-option, .payment-option {
            padding: 15px !important;
        }
    }
    
    @media (max-width: 480px) {
        .step-progress > div {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .progress-fill {
            display: none !important;
        }
        
        .donor-type-option {
            grid-template-columns: 1fr !important;
        }
    }
    
    /* Enhanced animations */
    .amount-card, .purpose-option, .payment-option, .donor-type-option {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .btn-next-step, .btn-prev-step, .btn-process-payment {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .btn-next-step:before, .btn-process-payment:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-next-step:hover:before, .btn-process-payment:hover:before {
        left: 100%;
    }
    
    .btn-next-step:hover, .btn-process-payment:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4) !important;
    }
    
    .btn-prev-step:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3) !important;
    }
    
    input:focus, select:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2) !important;
        outline: none !important;
        transform: scale(1.02) !important;
    }
    
    /* Pulse animation for amount changes */
    .pulse {
        animation: pulse 0.6s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Loading states */
    .btn-process-payment:disabled {
        opacity: 0.7 !important;
        cursor: not-allowed !important;
        transform: none !important;
    }
    
    /* Floating elements */
    .donation-summary {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
    
    /* Success checkmark animation */
    .step-circle .fa-check {
        animation: checkmark 0.5s ease-in-out;
    }
    
    @keyframes checkmark {
        0% { transform: scale(0) rotate(0deg); }
        50% { transform: scale(1.2) rotate(180deg); }
        100% { transform: scale(1) rotate(360deg); }
    }
    
    /* Enhanced card hover effects */
    .amount-card:hover, .purpose-option:hover, .payment-option:hover {
        transform: translateY(-4px) scale(1.02) !important;
        box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
    }
    
    .donor-type-option:hover {
        transform: translateY(-6px) scale(1.03) !important;
        box-shadow: 0 15px 35px rgba(40,167,69,0.2) !important;
    }
    
    /* Progress bar enhancements */
    .progress-fill {
        background: linear-gradient(90deg, #28a745, #20c997, #17a2b8) !important;
        background-size: 200% 100% !important;
        animation: gradient-shift 3s ease infinite !important;
    }
    
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Step circle animations */
    .step-circle {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .step-indicator.active .step-circle {
        animation: glow 2s ease-in-out infinite alternate;
    }
    
    @keyframes glow {
        from { box-shadow: 0 4px 15px rgba(40,167,69,0.3); }
        to { box-shadow: 0 6px 25px rgba(40,167,69,0.6); }
    }
    
    /* Form field animations */
    input, select, textarea {
        transition: all 0.3s ease !important;
    }
    
    input:focus, select:focus, textarea:focus {
        animation: field-focus 0.3s ease;
    }
    
    @keyframes field-focus {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1.02); }
    }
    
    /* Trust indicator animations */
    .trust-section > div > div {
        transition: transform 0.3s ease;
    }
    
    .trust-section > div > div:hover {
        transform: scale(1.05);
    }
    
    /* Hero section enhancements */
    .donation-hero {
        position: relative;
        overflow: hidden;
    }
    
    .donation-hero:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 70%, rgba(255,255,255,0.1) 0%, transparent 50%),
                    radial-gradient(circle at 70% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
        animation: hero-glow 4s ease-in-out infinite alternate;
    }
    
    @keyframes hero-glow {
        0% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
`;
document.head.appendChild(style);
</script>


