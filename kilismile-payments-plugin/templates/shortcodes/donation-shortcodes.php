<?php
/**
 * KiliSmile Payments - Donation Form Shortcode Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donation Form Shortcode
 * Usage: [kilismile_donation_form campaign="campaign-id" amount="50" currency="USD" style="modern"]
 */
function kilismile_donation_form_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'campaign' => '',
        'amount' => '',
        'currency' => get_option('kilismile_payments_currency', 'USD'),
        'style' => 'default',
        'title' => __('Make a Donation', 'kilismile-payments'),
        'description' => '',
        'goal' => '',
        'raised' => '',
        'show_progress' => 'yes',
        'show_amounts' => 'yes',
        'amounts' => '10,25,50,100,250,500',
        'custom_amount' => 'yes',
        'recurring' => 'yes',
        'anonymous' => 'yes',
        'redirect_url' => '',
        'class' => '',
        'id' => '',
        'width' => '100%',
        'alignment' => 'left'
    ), $atts, 'kilismile_donation_form');
    
    // Sanitize attributes
    $campaign = sanitize_text_field($atts['campaign']);
    $amount = floatval($atts['amount']);
    $currency = sanitize_text_field($atts['currency']);
    $style = sanitize_text_field($atts['style']);
    $title = sanitize_text_field($atts['title']);
    $description = wp_kses_post($atts['description']);
    $goal = floatval($atts['goal']);
    $raised = floatval($atts['raised']);
    $show_progress = $atts['show_progress'] === 'yes';
    $show_amounts = $atts['show_amounts'] === 'yes';
    $custom_amount = $atts['custom_amount'] === 'yes';
    $recurring = $atts['recurring'] === 'yes';
    $anonymous = $atts['anonymous'] === 'yes';
    $redirect_url = esc_url($atts['redirect_url']);
    $class = sanitize_html_class($atts['class']);
    $id = sanitize_html_class($atts['id']);
    $width = sanitize_text_field($atts['width']);
    $alignment = in_array($atts['alignment'], array('left', 'center', 'right')) ? $atts['alignment'] : 'left';
    
    // Parse amounts
    $amounts = array_map('floatval', array_filter(explode(',', $atts['amounts'])));
    if (empty($amounts)) {
        $amounts = array(10, 25, 50, 100, 250, 500);
    }
    
    // Generate unique form ID
    $form_id = $id ?: 'kilismile-form-' . wp_rand(1000, 9999);
    
    // Start output buffering
    ob_start();
    
    // Enqueue necessary scripts and styles
    wp_enqueue_script('kilismile-payments-public');
    wp_enqueue_style('kilismile-payments-public');
    ?>
    
    <div class="kilismile-donation-form-wrapper <?php echo esc_attr($style); ?> <?php echo esc_attr($class); ?>" 
         style="width: <?php echo esc_attr($width); ?>; text-align: <?php echo esc_attr($alignment); ?>;">
         
        <?php if ($title): ?>
        <div class="kilismile-form-header">
            <h3 class="kilismile-form-title"><?php echo esc_html($title); ?></h3>
            <?php if ($description): ?>
            <div class="kilismile-form-description"><?php echo wp_kses_post($description); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($show_progress && $goal > 0): ?>
        <div class="kilismile-progress-section">
            <?php
            $percentage = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
            $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
            ?>
            <div class="kilismile-progress-bar">
                <div class="kilismile-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
            </div>
            <div class="kilismile-progress-stats">
                <span class="raised"><?php echo esc_html($currency_symbol . number_format($raised, 2)); ?> <?php _e('raised', 'kilismile-payments'); ?></span>
                <span class="goal"><?php echo esc_html($currency_symbol . number_format($goal, 2)); ?> <?php _e('goal', 'kilismile-payments'); ?></span>
                <span class="percentage"><?php echo esc_html(number_format($percentage, 1)); ?>%</span>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="<?php echo esc_attr($form_id); ?>" class="kilismile-donation-form" method="post" data-style="<?php echo esc_attr($style); ?>">
            <?php wp_nonce_field('kilismile_donation_form', 'kilismile_nonce'); ?>
            <input type="hidden" name="action" value="kilismile_process_donation">
            <input type="hidden" name="campaign" value="<?php echo esc_attr($campaign); ?>">
            <input type="hidden" name="redirect_url" value="<?php echo esc_attr($redirect_url); ?>">
            
            <?php if ($show_amounts): ?>
            <div class="kilismile-amount-section">
                <label class="kilismile-section-label"><?php _e('Select Amount', 'kilismile-payments'); ?></label>
                <div class="kilismile-amount-buttons">
                    <?php foreach ($amounts as $preset_amount): ?>
                    <button type="button" class="kilismile-amount-btn" data-amount="<?php echo esc_attr($preset_amount); ?>">
                        <?php 
                        $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
                        echo esc_html($currency_symbol . number_format($preset_amount));
                        ?>
                    </button>
                    <?php endforeach; ?>
                    
                    <?php if ($custom_amount): ?>
                    <button type="button" class="kilismile-amount-btn kilismile-custom-btn" data-amount="custom">
                        <?php _e('Other', 'kilismile-payments'); ?>
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="kilismile-custom-amount" style="display: none;">
                    <label for="custom_amount_<?php echo esc_attr($form_id); ?>"><?php _e('Enter Amount', 'kilismile-payments'); ?></label>
                    <div class="kilismile-amount-input-wrapper">
                        <span class="kilismile-currency-symbol"><?php echo esc_html($currency_symbol); ?></span>
                        <input type="number" id="custom_amount_<?php echo esc_attr($form_id); ?>" 
                               name="custom_amount" min="1" step="0.01" placeholder="0.00">
                    </div>
                </div>
                
                <input type="hidden" name="amount" value="<?php echo esc_attr($amount); ?>">
                <input type="hidden" name="currency" value="<?php echo esc_attr($currency); ?>">
            </div>
            <?php endif; ?>
            
            <?php if ($recurring): ?>
            <div class="kilismile-frequency-section">
                <label class="kilismile-section-label"><?php _e('Donation Frequency', 'kilismile-payments'); ?></label>
                <div class="kilismile-frequency-options">
                    <label class="kilismile-radio-label">
                        <input type="radio" name="frequency" value="once" checked>
                        <span class="kilismile-radio-text"><?php _e('One-time', 'kilismile-payments'); ?></span>
                    </label>
                    <label class="kilismile-radio-label">
                        <input type="radio" name="frequency" value="monthly">
                        <span class="kilismile-radio-text"><?php _e('Monthly', 'kilismile-payments'); ?></span>
                    </label>
                    <label class="kilismile-radio-label">
                        <input type="radio" name="frequency" value="yearly">
                        <span class="kilismile-radio-text"><?php _e('Yearly', 'kilismile-payments'); ?></span>
                    </label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="kilismile-donor-section">
                <label class="kilismile-section-label"><?php _e('Donor Information', 'kilismile-payments'); ?></label>
                
                <div class="kilismile-field-row">
                    <div class="kilismile-field-col">
                        <label for="first_name_<?php echo esc_attr($form_id); ?>"><?php _e('First Name', 'kilismile-payments'); ?> *</label>
                        <input type="text" id="first_name_<?php echo esc_attr($form_id); ?>" 
                               name="first_name" required>
                    </div>
                    <div class="kilismile-field-col">
                        <label for="last_name_<?php echo esc_attr($form_id); ?>"><?php _e('Last Name', 'kilismile-payments'); ?> *</label>
                        <input type="text" id="last_name_<?php echo esc_attr($form_id); ?>" 
                               name="last_name" required>
                    </div>
                </div>
                
                <div class="kilismile-field-full">
                    <label for="email_<?php echo esc_attr($form_id); ?>"><?php _e('Email Address', 'kilismile-payments'); ?> *</label>
                    <input type="email" id="email_<?php echo esc_attr($form_id); ?>" 
                           name="email" required>
                </div>
                
                <div class="kilismile-field-full">
                    <label for="phone_<?php echo esc_attr($form_id); ?>"><?php _e('Phone Number', 'kilismile-payments'); ?></label>
                    <input type="tel" id="phone_<?php echo esc_attr($form_id); ?>" 
                           name="phone">
                </div>
                
                <?php if ($anonymous): ?>
                <div class="kilismile-field-checkbox">
                    <label class="kilismile-checkbox-label">
                        <input type="checkbox" name="anonymous" value="1">
                        <span class="kilismile-checkbox-text"><?php _e('Make this donation anonymous', 'kilismile-payments'); ?></span>
                    </label>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="kilismile-payment-section">
                <label class="kilismile-section-label"><?php _e('Payment Method', 'kilismile-payments'); ?></label>
                
                <?php
                // Get available gateways
                $gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();
                $enabled_gateways = array_filter($gateways, function($gateway) {
                    return $gateway['enabled'];
                });
                ?>
                
                <div class="kilismile-payment-methods">
                    <?php foreach ($enabled_gateways as $gateway_id => $gateway): ?>
                    <label class="kilismile-payment-method">
                        <input type="radio" name="payment_method" value="<?php echo esc_attr($gateway_id); ?>" 
                               <?php echo reset($enabled_gateways) === $gateway ? 'checked' : ''; ?>>
                        <div class="kilismile-payment-method-card">
                            <div class="kilismile-payment-method-icon">
                                <img src="<?php echo esc_url($gateway['icon'] ?? ''); ?>" 
                                     alt="<?php echo esc_attr($gateway['title']); ?>">
                            </div>
                            <div class="kilismile-payment-method-info">
                                <span class="kilismile-payment-method-title"><?php echo esc_html($gateway['title']); ?></span>
                                <span class="kilismile-payment-method-desc"><?php echo esc_html($gateway['description'] ?? ''); ?></span>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="kilismile-submit-section">
                <button type="submit" class="kilismile-submit-btn">
                    <span class="kilismile-btn-text"><?php _e('Donate Now', 'kilismile-payments'); ?></span>
                    <span class="kilismile-btn-loading" style="display: none;">
                        <span class="kilismile-spinner"></span>
                        <?php _e('Processing...', 'kilismile-payments'); ?>
                    </span>
                </button>
                
                <div class="kilismile-security-notice">
                    <span class="kilismile-security-icon">🔒</span>
                    <?php _e('Your payment information is secure and encrypted', 'kilismile-payments'); ?>
                </div>
            </div>
            
            <div class="kilismile-form-messages"></div>
        </form>
    </div>
    
    <style>
    .kilismile-donation-form-wrapper {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 20px 0;
        max-width: 500px;
    }
    
    .kilismile-donation-form-wrapper.modern {
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .kilismile-donation-form-wrapper.minimal {
        box-shadow: none;
        border: 2px solid #e1e1e1;
        padding: 20px;
    }
    
    .kilismile-form-header {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .kilismile-form-title {
        margin: 0 0 10px 0;
        font-size: 24px;
        font-weight: 600;
    }
    
    .kilismile-form-description {
        color: #666;
        line-height: 1.5;
    }
    
    .modern .kilismile-form-description {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .kilismile-progress-section {
        margin-bottom: 25px;
    }
    
    .kilismile-progress-bar {
        height: 8px;
        background: #e1e1e1;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .kilismile-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #4CAF50, #45a049);
        transition: width 0.3s ease;
    }
    
    .kilismile-progress-stats {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: #666;
    }
    
    .modern .kilismile-progress-stats {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .kilismile-section-label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .modern .kilismile-section-label {
        color: white;
    }
    
    .kilismile-amount-buttons {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .kilismile-amount-btn {
        padding: 12px;
        border: 2px solid #e1e1e1;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .kilismile-amount-btn:hover,
    .kilismile-amount-btn.active {
        border-color: #007cba;
        background: #007cba;
        color: white;
    }
    
    .kilismile-field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .kilismile-field-full {
        margin-bottom: 15px;
    }
    
    .kilismile-field-full label,
    .kilismile-field-col label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
    }
    
    .modern .kilismile-field-full label,
    .modern .kilismile-field-col label {
        color: white;
    }
    
    .kilismile-field-full input,
    .kilismile-field-col input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
    
    .kilismile-submit-btn {
        width: 100%;
        padding: 15px;
        background: #007cba;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-bottom: 15px;
    }
    
    .kilismile-submit-btn:hover {
        background: #005a87;
    }
    
    .kilismile-submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .kilismile-security-notice {
        text-align: center;
        font-size: 14px;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .modern .kilismile-security-notice {
        color: rgba(255, 255, 255, 0.9);
    }
    
    @media (max-width: 768px) {
        .kilismile-donation-form-wrapper {
            padding: 20px;
        }
        
        .kilismile-amount-buttons {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .kilismile-field-row {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('<?php echo esc_js($form_id); ?>');
        const amountButtons = form.querySelectorAll('.kilismile-amount-btn');
        const customAmountSection = form.querySelector('.kilismile-custom-amount');
        const amountInput = form.querySelector('input[name="amount"]');
        const customAmountInput = form.querySelector('input[name="custom_amount"]');
        
        // Amount button selection
        amountButtons.forEach(button => {
            button.addEventListener('click', function() {
                amountButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                if (this.dataset.amount === 'custom') {
                    customAmountSection.style.display = 'block';
                    customAmountInput.focus();
                    amountInput.value = '';
                } else {
                    customAmountSection.style.display = 'none';
                    amountInput.value = this.dataset.amount;
                    customAmountInput.value = '';
                }
            });
        });
        
        // Custom amount input
        if (customAmountInput) {
            customAmountInput.addEventListener('input', function() {
                amountInput.value = this.value;
            });
        }
        
        // Pre-select amount if provided
        <?php if ($amount > 0): ?>
        const presetBtn = form.querySelector('.kilismile-amount-btn[data-amount="<?php echo esc_js($amount); ?>"]');
        if (presetBtn) {
            presetBtn.click();
        }
        <?php endif; ?>
        
        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('.kilismile-submit-btn');
            const btnText = submitBtn.querySelector('.kilismile-btn-text');
            const btnLoading = submitBtn.querySelector('.kilismile-btn-loading');
            
            // Validate amount
            const finalAmount = parseFloat(amountInput.value);
            if (!finalAmount || finalAmount <= 0) {
                alert('<?php esc_js(_e('Please select or enter a valid donation amount.', 'kilismile-payments')); ?>');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            
            // Submit form via AJAX
            const formData = new FormData(form);
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.redirect_url) {
                        window.location.href = data.data.redirect_url;
                    } else {
                        // Show success message
                        const messagesDiv = form.querySelector('.kilismile-form-messages');
                        messagesDiv.innerHTML = '<div class="kilismile-success">' + data.data.message + '</div>';
                        form.reset();
                    }
                } else {
                    // Show error message
                    const messagesDiv = form.querySelector('.kilismile-form-messages');
                    messagesDiv.innerHTML = '<div class="kilismile-error">' + data.data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messagesDiv = form.querySelector('.kilismile-form-messages');
                messagesDiv.innerHTML = '<div class="kilismile-error"><?php esc_js(_e('An error occurred. Please try again.', 'kilismile-payments')); ?></div>';
            })
            .finally(() => {
                // Reset loading state
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            });
        });
    });
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('kilismile_donation_form', 'kilismile_donation_form_shortcode');

/**
 * Progress Bar Shortcode
 * Usage: [kilismile_progress goal="5000" raised="3250" currency="USD" show_percentage="yes"]
 */
function kilismile_progress_shortcode($atts) {
    $atts = shortcode_atts(array(
        'goal' => 1000,
        'raised' => 0,
        'currency' => get_option('kilismile_payments_currency', 'USD'),
        'show_percentage' => 'yes',
        'show_amounts' => 'yes',
        'height' => '20px',
        'color' => '#4CAF50',
        'background' => '#e1e1e1',
        'border_radius' => '10px',
        'animation' => 'yes',
        'class' => ''
    ), $atts, 'kilismile_progress');
    
    $goal = floatval($atts['goal']);
    $raised = floatval($atts['raised']);
    $currency = sanitize_text_field($atts['currency']);
    $show_percentage = $atts['show_percentage'] === 'yes';
    $show_amounts = $atts['show_amounts'] === 'yes';
    $height = sanitize_text_field($atts['height']);
    $color = sanitize_hex_color($atts['color']) ?: '#4CAF50';
    $background = sanitize_hex_color($atts['background']) ?: '#e1e1e1';
    $border_radius = sanitize_text_field($atts['border_radius']);
    $animation = $atts['animation'] === 'yes';
    $class = sanitize_html_class($atts['class']);
    
    $percentage = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
    $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
    
    $unique_id = 'kilismile-progress-' . wp_rand(1000, 9999);
    
    ob_start();
    ?>
    <div class="kilismile-progress-wrapper <?php echo esc_attr($class); ?>">
        <?php if ($show_amounts): ?>
        <div class="kilismile-progress-amounts">
            <span class="raised"><?php echo esc_html($currency_symbol . number_format($raised, 2)); ?> <?php _e('raised', 'kilismile-payments'); ?></span>
            <span class="goal"><?php _e('of', 'kilismile-payments'); ?> <?php echo esc_html($currency_symbol . number_format($goal, 2)); ?></span>
        </div>
        <?php endif; ?>
        
        <div class="kilismile-progress-bar" id="<?php echo esc_attr($unique_id); ?>" 
             style="height: <?php echo esc_attr($height); ?>; background: <?php echo esc_attr($background); ?>; border-radius: <?php echo esc_attr($border_radius); ?>;">
            <div class="kilismile-progress-fill" 
                 style="width: <?php echo $animation ? '0' : esc_attr($percentage); ?>%; background: <?php echo esc_attr($color); ?>; border-radius: <?php echo esc_attr($border_radius); ?>;">
            </div>
        </div>
        
        <?php if ($show_percentage): ?>
        <div class="kilismile-progress-percentage">
            <?php echo esc_html(number_format($percentage, 1)); ?>% <?php _e('complete', 'kilismile-payments'); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($animation): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.getElementById('<?php echo esc_js($unique_id); ?>');
        const progressFill = progressBar.querySelector('.kilismile-progress-fill');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    progressFill.style.transition = 'width 2s ease-in-out';
                    progressFill.style.width = '<?php echo esc_js($percentage); ?>%';
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(progressBar);
    });
    </script>
    <?php endif; ?>
    
    <style>
    .kilismile-progress-wrapper {
        margin: 20px 0;
    }
    
    .kilismile-progress-amounts {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-weight: 500;
        color: #333;
    }
    
    .kilismile-progress-bar {
        position: relative;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .kilismile-progress-fill {
        height: 100%;
        transition: width 0.3s ease;
    }
    
    .kilismile-progress-percentage {
        text-align: center;
        font-weight: 600;
        color: #666;
    }
    </style>
    
    <?php
    return ob_get_clean();
}
add_shortcode('kilismile_progress', 'kilismile_progress_shortcode');

/**
 * Recent Donations Shortcode
 * Usage: [kilismile_recent_donations limit="5" show_amount="yes" show_date="yes"]
 */
function kilismile_recent_donations_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 5,
        'show_amount' => 'yes',
        'show_date' => 'yes',
        'show_anonymous' => 'yes',
        'campaign' => '',
        'class' => ''
    ), $atts, 'kilismile_recent_donations');
    
    $limit = max(1, intval($atts['limit']));
    $show_amount = $atts['show_amount'] === 'yes';
    $show_date = $atts['show_date'] === 'yes';
    $show_anonymous = $atts['show_anonymous'] === 'yes';
    $campaign = sanitize_text_field($atts['campaign']);
    $class = sanitize_html_class($atts['class']);
    
    // Get recent donations
    $database = KiliSmile_Payments_Plugin::get_instance()->get_database();
    $donations = $database->get_recent_donations($limit, $campaign);
    
    if (empty($donations)) {
        return '<div class="kilismile-no-donations">' . __('No recent donations found.', 'kilismile-payments') . '</div>';
    }
    
    ob_start();
    ?>
    <div class="kilismile-recent-donations <?php echo esc_attr($class); ?>">
        <h3 class="kilismile-recent-title"><?php _e('Recent Donations', 'kilismile-payments'); ?></h3>
        <ul class="kilismile-donations-list">
            <?php foreach ($donations as $donation): ?>
            <li class="kilismile-donation-item">
                <div class="kilismile-donor-info">
                    <span class="kilismile-donor-name">
                        <?php 
                        if ($donation->anonymous || empty($donation->donor_name)) {
                            echo __('Anonymous', 'kilismile-payments');
                        } else {
                            echo esc_html($donation->donor_name);
                        }
                        ?>
                    </span>
                    
                    <?php if ($show_amount): ?>
                    <span class="kilismile-donation-amount">
                        <?php 
                        $currency_symbol = $donation->currency === 'USD' ? '$' : 'TSh ';
                        echo esc_html($currency_symbol . number_format($donation->amount, 2));
                        ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($show_date): ?>
                <div class="kilismile-donation-date">
                    <?php echo esc_html(human_time_diff(strtotime($donation->created_at), current_time('timestamp')) . ' ago'); ?>
                </div>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <style>
    .kilismile-recent-donations {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .kilismile-recent-title {
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    
    .kilismile-donations-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .kilismile-donation-item {
        padding: 12px 0;
        border-bottom: 1px solid #e1e1e1;
    }
    
    .kilismile-donation-item:last-child {
        border-bottom: none;
    }
    
    .kilismile-donor-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .kilismile-donor-name {
        font-weight: 500;
        color: #333;
    }
    
    .kilismile-donation-amount {
        font-weight: 600;
        color: #007cba;
    }
    
    .kilismile-donation-date {
        font-size: 12px;
        color: #666;
    }
    
    .kilismile-no-donations {
        text-align: center;
        padding: 20px;
        color: #666;
        font-style: italic;
    }
    </style>
    
    <?php
    return ob_get_clean();
}
add_shortcode('kilismile_recent_donations', 'kilismile_recent_donations_shortcode');

