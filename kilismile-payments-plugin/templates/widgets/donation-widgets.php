<?php
/**
 * KiliSmile Payments - Donation Widgets
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quick Donation Widget
 */
class KiliSmile_Quick_Donation_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kilismile_quick_donation',
            __('KiliSmile Quick Donation', 'kilismile-payments'),
            array(
                'description' => __('A quick donation form widget for sidebars and footer areas.', 'kilismile-payments'),
                'classname' => 'kilismile-quick-donation-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Make a Donation', 'kilismile-payments');
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $amounts = !empty($instance['amounts']) ? $instance['amounts'] : '10,25,50,100';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Donate', 'kilismile-payments');
        $style = !empty($instance['style']) ? $instance['style'] : 'compact';
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        $widget_id = 'kilismile-widget-' . wp_rand(1000, 9999);
        $amounts_array = array_map('trim', explode(',', $amounts));
        $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
        ?>
        
        <div class="kilismile-quick-donation-form <?php echo esc_attr($style); ?>">
            <?php if ($description): ?>
            <p class="kilismile-widget-description"><?php echo wp_kses_post($description); ?></p>
            <?php endif; ?>
            
            <form id="<?php echo esc_attr($widget_id); ?>" class="kilismile-widget-form" method="post">
                <?php wp_nonce_field('kilismile_widget_donation', 'kilismile_nonce'); ?>
                <input type="hidden" name="action" value="kilismile_process_donation">
                <input type="hidden" name="widget" value="1">
                
                <div class="kilismile-widget-amounts">
                    <?php foreach ($amounts_array as $amount): ?>
                    <?php $amount = floatval(trim($amount)); ?>
                    <?php if ($amount > 0): ?>
                    <button type="button" class="kilismile-widget-amount-btn" data-amount="<?php echo esc_attr($amount); ?>">
                        <?php echo esc_html($currency_symbol . number_format($amount)); ?>
                    </button>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="kilismile-widget-custom">
                    <input type="number" name="amount" placeholder="<?php esc_attr_e('Other amount', 'kilismile-payments'); ?>" 
                           min="1" step="0.01" class="kilismile-widget-amount-input">
                </div>
                
                <div class="kilismile-widget-fields">
                    <input type="text" name="donor_name" placeholder="<?php esc_attr_e('Your name', 'kilismile-payments'); ?>" required>
                    <input type="email" name="email" placeholder="<?php esc_attr_e('Your email', 'kilismile-payments'); ?>" required>
                </div>
                
                <input type="hidden" name="currency" value="<?php echo esc_attr($currency); ?>">
                
                <button type="submit" class="kilismile-widget-submit">
                    <span class="kilismile-widget-btn-text"><?php echo esc_html($button_text); ?></span>
                    <span class="kilismile-widget-btn-loading" style="display: none;">
                        <span class="kilismile-widget-spinner"></span>
                    </span>
                </button>
                
                <div class="kilismile-widget-messages"></div>
            </form>
        </div>
        
        <style>
        .kilismile-quick-donation-form {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .kilismile-widget-description {
            margin: 0 0 15px 0;
            font-size: 14px;
            line-height: 1.4;
            color: #666;
        }
        
        .kilismile-widget-amounts {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .kilismile-widget-amount-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .kilismile-widget-amount-btn:hover,
        .kilismile-widget-amount-btn.active {
            border-color: #007cba;
            background: #007cba;
            color: white;
        }
        
        .kilismile-widget-custom {
            margin-bottom: 10px;
        }
        
        .kilismile-widget-amount-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .kilismile-widget-fields input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .kilismile-widget-submit {
            width: 100%;
            padding: 10px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .kilismile-widget-submit:hover {
            background: #005a87;
        }
        
        .kilismile-widget-submit:disabled {
            background: #ccc;
        }
        
        .kilismile-widget-spinner {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .kilismile-widget-messages {
            margin-top: 10px;
            font-size: 13px;
        }
        
        .kilismile-widget-messages .error {
            color: #d63384;
            background: #f8d7da;
            padding: 8px;
            border-radius: 4px;
        }
        
        .kilismile-widget-messages .success {
            color: #155724;
            background: #d4edda;
            padding: 8px;
            border-radius: 4px;
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('<?php echo esc_js($widget_id); ?>');
            const amountButtons = form.querySelectorAll('.kilismile-widget-amount-btn');
            const amountInput = form.querySelector('.kilismile-widget-amount-input');
            const submitBtn = form.querySelector('.kilismile-widget-submit');
            const btnText = submitBtn.querySelector('.kilismile-widget-btn-text');
            const btnLoading = submitBtn.querySelector('.kilismile-widget-btn-loading');
            const messagesDiv = form.querySelector('.kilismile-widget-messages');
            
            // Amount button selection
            amountButtons.forEach(button => {
                button.addEventListener('click', function() {
                    amountButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    amountInput.value = this.dataset.amount;
                });
            });
            
            // Custom amount input
            amountInput.addEventListener('input', function() {
                if (this.value) {
                    amountButtons.forEach(btn => btn.classList.remove('active'));
                }
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const amount = parseFloat(amountInput.value);
                if (!amount || amount <= 0) {
                    messagesDiv.innerHTML = '<div class="error"><?php esc_js(_e('Please enter a valid amount.', 'kilismile-payments')); ?></div>';
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                messagesDiv.innerHTML = '';
                
                // Submit form
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
                            messagesDiv.innerHTML = '<div class="success">' + data.data.message + '</div>';
                            form.reset();
                            amountButtons.forEach(btn => btn.classList.remove('active'));
                        }
                    } else {
                        messagesDiv.innerHTML = '<div class="error">' + data.data.message + '</div>';
                    }
                })
                .catch(error => {
                    messagesDiv.innerHTML = '<div class="error"><?php esc_js(_e('An error occurred. Please try again.', 'kilismile-payments')); ?></div>';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                });
            });
        });
        </script>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Make a Donation', 'kilismile-payments');
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $amounts = !empty($instance['amounts']) ? $instance['amounts'] : '10,25,50,100';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Donate', 'kilismile-payments');
        $style = !empty($instance['style']) ? $instance['style'] : 'compact';
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php _e('Description:', 'kilismile-payments'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" 
                      name="<?php echo esc_attr($this->get_field_name('description')); ?>" rows="3"><?php echo esc_textarea($description); ?></textarea>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('amounts')); ?>"><?php _e('Preset Amounts (comma-separated):', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('amounts')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('amounts')); ?>" type="text" 
                   value="<?php echo esc_attr($amounts); ?>">
            <small><?php _e('Example: 10,25,50,100', 'kilismile-payments'); ?></small>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('currency')); ?>"><?php _e('Currency:', 'kilismile-payments'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('currency')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('currency')); ?>">
                <option value="USD" <?php selected($currency, 'USD'); ?>>USD - US Dollar</option>
                <option value="TZS" <?php selected($currency, 'TZS'); ?>>TZS - Tanzanian Shilling</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php _e('Button Text:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" 
                   value="<?php echo esc_attr($button_text); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php _e('Style:', 'kilismile-payments'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="compact" <?php selected($style, 'compact'); ?>><?php _e('Compact', 'kilismile-payments'); ?></option>
                <option value="minimal" <?php selected($style, 'minimal'); ?>><?php _e('Minimal', 'kilismile-payments'); ?></option>
                <option value="modern" <?php selected($style, 'modern'); ?>><?php _e('Modern', 'kilismile-payments'); ?></option>
            </select>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? wp_kses_post($new_instance['description']) : '';
        $instance['amounts'] = (!empty($new_instance['amounts'])) ? sanitize_text_field($new_instance['amounts']) : '';
        $instance['currency'] = (!empty($new_instance['currency'])) ? sanitize_text_field($new_instance['currency']) : 'USD';
        $instance['button_text'] = (!empty($new_instance['button_text'])) ? sanitize_text_field($new_instance['button_text']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'compact';
        return $instance;
    }
}

/**
 * Progress Display Widget
 */
class KiliSmile_Progress_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kilismile_progress',
            __('KiliSmile Progress Bar', 'kilismile-payments'),
            array(
                'description' => __('Display donation progress with goal and current amount.', 'kilismile-payments'),
                'classname' => 'kilismile-progress-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Donation Progress', 'kilismile-payments');
        $goal = !empty($instance['goal']) ? floatval($instance['goal']) : 1000;
        $campaign = !empty($instance['campaign']) ? $instance['campaign'] : '';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        $show_amounts = !empty($instance['show_amounts']) ? $instance['show_amounts'] : 'yes';
        $show_percentage = !empty($instance['show_percentage']) ? $instance['show_percentage'] : 'yes';
        $color = !empty($instance['color']) ? $instance['color'] : '#4CAF50';
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        // Get current raised amount
        $database = KiliSmile_Payments_Plugin::get_instance()->get_database();
        $raised = $database->get_campaign_total($campaign);
        
        $percentage = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
        $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
        
        $widget_id = 'kilismile-progress-widget-' . wp_rand(1000, 9999);
        ?>
        
        <div class="kilismile-progress-widget-content">
            <?php if ($show_amounts === 'yes'): ?>
            <div class="kilismile-progress-amounts">
                <div class="kilismile-raised">
                    <span class="kilismile-amount"><?php echo esc_html($currency_symbol . number_format($raised, 2)); ?></span>
                    <span class="kilismile-label"><?php _e('raised', 'kilismile-payments'); ?></span>
                </div>
                <div class="kilismile-goal">
                    <span class="kilismile-amount"><?php echo esc_html($currency_symbol . number_format($goal, 2)); ?></span>
                    <span class="kilismile-label"><?php _e('goal', 'kilismile-payments'); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="kilismile-progress-bar-container" id="<?php echo esc_attr($widget_id); ?>">
                <div class="kilismile-progress-bar">
                    <div class="kilismile-progress-fill" 
                         style="width: 0%; background-color: <?php echo esc_attr($color); ?>;">
                    </div>
                </div>
            </div>
            
            <?php if ($show_percentage === 'yes'): ?>
            <div class="kilismile-progress-percentage">
                <?php echo esc_html(number_format($percentage, 1)); ?>% <?php _e('complete', 'kilismile-payments'); ?>
            </div>
            <?php endif; ?>
            
            <div class="kilismile-progress-supporters">
                <?php
                $supporters_count = $database->get_campaign_supporters_count($campaign);
                printf(_n('%d supporter', '%d supporters', $supporters_count, 'kilismile-payments'), $supporters_count);
                ?>
            </div>
        </div>
        
        <style>
        .kilismile-progress-widget-content {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        
        .kilismile-progress-amounts {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .kilismile-raised,
        .kilismile-goal {
            text-align: center;
        }
        
        .kilismile-amount {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .kilismile-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .kilismile-progress-bar {
            height: 12px;
            background: #e1e1e1;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .kilismile-progress-fill {
            height: 100%;
            transition: width 2s ease-in-out;
            border-radius: 6px;
        }
        
        .kilismile-progress-percentage {
            text-align: center;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .kilismile-progress-supporters {
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressContainer = document.getElementById('<?php echo esc_js($widget_id); ?>');
            const progressFill = progressContainer.querySelector('.kilismile-progress-fill');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            progressFill.style.width = '<?php echo esc_js($percentage); ?>%';
                        }, 500);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(progressContainer);
        });
        </script>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Donation Progress', 'kilismile-payments');
        $goal = !empty($instance['goal']) ? $instance['goal'] : '1000';
        $campaign = !empty($instance['campaign']) ? $instance['campaign'] : '';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        $show_amounts = !empty($instance['show_amounts']) ? $instance['show_amounts'] : 'yes';
        $show_percentage = !empty($instance['show_percentage']) ? $instance['show_percentage'] : 'yes';
        $color = !empty($instance['color']) ? $instance['color'] : '#4CAF50';
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('goal')); ?>"><?php _e('Goal Amount:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('goal')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('goal')); ?>" type="number" 
                   value="<?php echo esc_attr($goal); ?>" min="1" step="0.01">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('campaign')); ?>"><?php _e('Campaign ID (optional):', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('campaign')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('campaign')); ?>" type="text" 
                   value="<?php echo esc_attr($campaign); ?>">
            <small><?php _e('Leave empty to show all donations', 'kilismile-payments'); ?></small>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('currency')); ?>"><?php _e('Currency:', 'kilismile-payments'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('currency')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('currency')); ?>">
                <option value="USD" <?php selected($currency, 'USD'); ?>>USD</option>
                <option value="TZS" <?php selected($currency, 'TZS'); ?>>TZS</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('color')); ?>"><?php _e('Progress Color:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('color')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('color')); ?>" type="color" 
                   value="<?php echo esc_attr($color); ?>">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_amounts, 'yes'); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_amounts')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_amounts')); ?>" value="yes">
            <label for="<?php echo esc_attr($this->get_field_id('show_amounts')); ?>"><?php _e('Show amounts', 'kilismile-payments'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_percentage, 'yes'); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_percentage')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_percentage')); ?>" value="yes">
            <label for="<?php echo esc_attr($this->get_field_id('show_percentage')); ?>"><?php _e('Show percentage', 'kilismile-payments'); ?></label>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['goal'] = (!empty($new_instance['goal'])) ? floatval($new_instance['goal']) : 1000;
        $instance['campaign'] = (!empty($new_instance['campaign'])) ? sanitize_text_field($new_instance['campaign']) : '';
        $instance['currency'] = (!empty($new_instance['currency'])) ? sanitize_text_field($new_instance['currency']) : 'USD';
        $instance['color'] = (!empty($new_instance['color'])) ? sanitize_hex_color($new_instance['color']) : '#4CAF50';
        $instance['show_amounts'] = (!empty($new_instance['show_amounts'])) ? 'yes' : 'no';
        $instance['show_percentage'] = (!empty($new_instance['show_percentage'])) ? 'yes' : 'no';
        return $instance;
    }
}

/**
 * Recent Donations Widget
 */
class KiliSmile_Recent_Donations_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kilismile_recent_donations',
            __('KiliSmile Recent Donations', 'kilismile-payments'),
            array(
                'description' => __('Display a list of recent donations to encourage others to donate.', 'kilismile-payments'),
                'classname' => 'kilismile-recent-donations-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Donations', 'kilismile-payments');
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 5;
        $show_amount = !empty($instance['show_amount']) ? $instance['show_amount'] : 'yes';
        $show_date = !empty($instance['show_date']) ? $instance['show_date'] : 'yes';
        $campaign = !empty($instance['campaign']) ? $instance['campaign'] : '';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        // Get recent donations
        $database = KiliSmile_Payments_Plugin::get_instance()->get_database();
        $donations = $database->get_recent_donations($limit, $campaign);
        
        $currency_symbol = $currency === 'USD' ? '$' : 'TSh ';
        ?>
        
        <div class="kilismile-recent-donations-widget-content">
            <?php if (!empty($donations)): ?>
            <ul class="kilismile-donations-list">
                <?php foreach ($donations as $donation): ?>
                <li class="kilismile-donation-item">
                    <div class="kilismile-donor-avatar">
                        <?php echo esc_html(strtoupper(substr($donation->donor_name ?: 'A', 0, 1))); ?>
                    </div>
                    <div class="kilismile-donation-details">
                        <div class="kilismile-donor-name">
                            <?php 
                            echo esc_html($donation->anonymous || empty($donation->donor_name) 
                                ? __('Anonymous', 'kilismile-payments') 
                                : $donation->donor_name
                            );
                            ?>
                        </div>
                        
                        <div class="kilismile-donation-meta">
                            <?php if ($show_amount === 'yes'): ?>
                            <span class="kilismile-donation-amount">
                                <?php echo esc_html($currency_symbol . number_format($donation->amount, 2)); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($show_date === 'yes'): ?>
                            <span class="kilismile-donation-time">
                                <?php echo esc_html(human_time_diff(strtotime($donation->created_at), current_time('timestamp')) . ' ago'); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="kilismile-no-donations"><?php _e('No donations yet. Be the first!', 'kilismile-payments'); ?></p>
            <?php endif; ?>
        </div>
        
        <style>
        .kilismile-recent-donations-widget-content {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        
        .kilismile-donations-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .kilismile-donation-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e1e1e1;
        }
        
        .kilismile-donation-item:last-child {
            border-bottom: none;
        }
        
        .kilismile-donor-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #007cba;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        .kilismile-donation-details {
            flex: 1;
            min-width: 0;
        }
        
        .kilismile-donor-name {
            font-weight: 500;
            color: #333;
            font-size: 13px;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        
        .kilismile-donation-meta {
            display: flex;
            gap: 8px;
            font-size: 11px;
            color: #666;
        }
        
        .kilismile-donation-amount {
            font-weight: 600;
            color: #007cba;
        }
        
        .kilismile-no-donations {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 0;
            padding: 20px 0;
        }
        </style>
        
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Donations', 'kilismile-payments');
        $limit = !empty($instance['limit']) ? $instance['limit'] : '5';
        $show_amount = !empty($instance['show_amount']) ? $instance['show_amount'] : 'yes';
        $show_date = !empty($instance['show_date']) ? $instance['show_date'] : 'yes';
        $campaign = !empty($instance['campaign']) ? $instance['campaign'] : '';
        $currency = !empty($instance['currency']) ? $instance['currency'] : 'USD';
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Number of donations to show:', 'kilismile-payments'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" 
                   value="<?php echo esc_attr($limit); ?>" min="1" max="20">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('campaign')); ?>"><?php _e('Campaign ID (optional):', 'kilismile-payments'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('campaign')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('campaign')); ?>" type="text" 
                   value="<?php echo esc_attr($campaign); ?>">
            <small><?php _e('Leave empty to show donations from all campaigns', 'kilismile-payments'); ?></small>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('currency')); ?>"><?php _e('Currency:', 'kilismile-payments'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('currency')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('currency')); ?>">
                <option value="USD" <?php selected($currency, 'USD'); ?>>USD</option>
                <option value="TZS" <?php selected($currency, 'TZS'); ?>>TZS</option>
            </select>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_amount, 'yes'); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_amount')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_amount')); ?>" value="yes">
            <label for="<?php echo esc_attr($this->get_field_id('show_amount')); ?>"><?php _e('Show donation amounts', 'kilismile-payments'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date, 'yes'); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="yes">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>"><?php _e('Show donation dates', 'kilismile-payments'); ?></label>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? max(1, intval($new_instance['limit'])) : 5;
        $instance['campaign'] = (!empty($new_instance['campaign'])) ? sanitize_text_field($new_instance['campaign']) : '';
        $instance['currency'] = (!empty($new_instance['currency'])) ? sanitize_text_field($new_instance['currency']) : 'USD';
        $instance['show_amount'] = (!empty($new_instance['show_amount'])) ? 'yes' : 'no';
        $instance['show_date'] = (!empty($new_instance['show_date'])) ? 'yes' : 'no';
        return $instance;
    }
}

// Register widgets
function kilismile_register_widgets() {
    register_widget('KiliSmile_Quick_Donation_Widget');
    register_widget('KiliSmile_Progress_Widget');
    register_widget('KiliSmile_Recent_Donations_Widget');
}
add_action('widgets_init', 'kilismile_register_widgets');

