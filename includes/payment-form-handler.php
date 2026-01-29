<?php
/**
 * Payment Form Shortcode and Enqueue Handler
 * Provides shortcode to display payment form and handles asset loading
 */

if (!defined('ABSPATH')) exit;

class KiliSmile_Payment_Form_Handler {
    
    public function __construct() {
        add_shortcode('kilismile_payment_form', array($this, 'render_payment_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_payment_assets'));
    }
    
    /**
     * Render payment form shortcode
     */
    public function render_payment_form($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Make a Donation',
            'description' => 'Your donation helps us make a difference in our community.',
            'show_title' => 'yes'
        ), $atts);
        
        // Enqueue assets
        $this->enqueue_payment_assets();
        
        ob_start();
        ?>
        <div class="kilismile-payment-wrapper">
            <?php if ($atts['show_title'] === 'yes'): ?>
                <div class="payment-form-header">
                    <h2><?php echo esc_html($atts['title']); ?></h2>
                    <?php if (!empty($atts['description'])): ?>
                        <p class="payment-form-description"><?php echo esc_html($atts['description']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php include get_template_directory() . '/template-parts/payment-form.php'; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enqueue payment form assets
     */
    public function enqueue_payment_assets() {
        // Only enqueue on pages that use the payment form
        if (!is_admin() && (has_shortcode(get_post()->post_content ?? '', 'kilismile_payment_form') || is_page('donate'))) {
            
            wp_enqueue_style(
                'kilismile-payment-form',
                get_template_directory_uri() . '/assets/css/payment-form.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'kilismile-payment-form',
                get_template_directory_uri() . '/assets/js/payment-form.js',
                array('jquery'),
                '1.0.0',
                true
            );
            
            // Localize script with payment data
            wp_localize_script('kilismile-payment-form', 'kilismilePayment', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kilismile_payment_nonce'),
                'currency' => get_option('kilismile_default_currency', 'USD'),
                'paypalEnabled' => get_option('kilismile_paypal_enabled', false),
                'azampayEnabled' => get_option('kilismile_azampay_enabled', false),
                'minimumAmounts' => array(
                    'USD' => get_option('kilismile_minimum_amount_usd', 1),
                    'TZS' => get_option('kilismile_minimum_amount_tzs', 1000)
                )
            ));
        }
    }
}

// Initialize payment form handler
new KiliSmile_Payment_Form_Handler();

