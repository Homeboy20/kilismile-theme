<?php
/**
 * KiliSmile Payments - Payment Success Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get transaction data
$transaction_id = isset($_GET['transaction_id']) ? sanitize_text_field($_GET['transaction_id']) : '';
$transaction = null;

if ($transaction_id) {
    $database = KiliSmile_Payments_Plugin::get_instance()->get_database();
    $transaction = $database->get_transaction($transaction_id);
}

// Default success data
$success_data = wp_parse_args($args ?? array(), array(
    'title' => __('Thank You for Your Donation!', 'kilismile-payments'),
    'message' => __('Your donation has been processed successfully.', 'kilismile-payments'),
    'show_receipt' => true,
    'show_social_share' => true,
    'return_url' => home_url(),
    'return_text' => __('Return to Home', 'kilismile-payments')
));

// Enqueue assets
wp_enqueue_style('kilismile-payments-frontend');
?>

<div class="kilismile-payment-success">
    <div class="success-container">
        
        <!-- Success Icon and Header -->
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="success-title"><?php echo esc_html($success_data['title']); ?></h1>
            <p class="success-message"><?php echo esc_html($success_data['message']); ?></p>
        </div>

        <?php if ($transaction && $success_data['show_receipt']): ?>
        <!-- Transaction Receipt -->
        <div class="transaction-receipt">
            <div class="receipt-header">
                <h3><?php _e('Donation Receipt', 'kilismile-payments'); ?></h3>
                <div class="receipt-number">
                    <?php printf(__('Receipt #%s', 'kilismile-payments'), esc_html($transaction->id)); ?>
                </div>
            </div>

            <div class="receipt-details">
                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Date:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value">
                        <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?>
                    </span>
                </div>

                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Amount:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value receipt-amount">
                        <?php 
                        $symbol = $transaction->currency === 'USD' ? '$' : 'TSh ';
                        echo esc_html($symbol . number_format($transaction->amount, 2));
                        ?>
                    </span>
                </div>

                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Payment Method:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value"><?php echo esc_html(ucfirst($transaction->gateway)); ?></span>
                </div>

                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Transaction ID:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value receipt-transaction-id"><?php echo esc_html($transaction->id); ?></span>
                </div>

                <?php if (!empty($transaction->gateway_transaction_id)): ?>
                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Gateway Reference:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value"><?php echo esc_html($transaction->gateway_transaction_id); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($transaction->donor_name)): ?>
                <div class="receipt-row">
                    <span class="receipt-label"><?php _e('Donor:', 'kilismile-payments'); ?></span>
                    <span class="receipt-value"><?php echo esc_html($transaction->donor_name); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($transaction->metadata) && is_string($transaction->metadata)): ?>
                    <?php 
                    $metadata = json_decode($transaction->metadata, true);
                    if ($metadata && isset($metadata['recurring']) && $metadata['recurring']): 
                    ?>
                    <div class="receipt-row">
                        <span class="receipt-label"><?php _e('Recurring:', 'kilismile-payments'); ?></span>
                        <span class="receipt-value">
                            <?php 
                            $interval = isset($metadata['recurring_interval']) ? $metadata['recurring_interval'] : 'monthly';
                            printf(__('Yes (%s)', 'kilismile-payments'), esc_html($interval));
                            ?>
                        </span>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="receipt-footer">
                <p class="receipt-note">
                    <?php _e('A receipt has been sent to your email address. Please keep this for your records.', 'kilismile-payments'); ?>
                </p>
                
                <div class="receipt-actions">
                    <button type="button" class="btn btn-secondary btn-print" onclick="window.print();">
                        <i class="fas fa-print"></i>
                        <?php _e('Print Receipt', 'kilismile-payments'); ?>
                    </button>
                    
                    <button type="button" class="btn btn-secondary btn-email" onclick="sendReceiptEmail();">
                        <i class="fas fa-envelope"></i>
                        <?php _e('Email Receipt', 'kilismile-payments'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Impact Message -->
        <div class="impact-message">
            <div class="impact-content">
                <h3><?php _e('Your Impact', 'kilismile-payments'); ?></h3>
                <p><?php _e('Your generous donation helps us continue our mission to support communities and create positive change. Thank you for making a difference!', 'kilismile-payments'); ?></p>
                
                <?php if ($transaction): ?>
                <div class="impact-stats">
                    <?php
                    // Calculate impact based on amount
                    $amount = floatval($transaction->amount);
                    $currency = $transaction->currency;
                    
                    // Convert to USD for impact calculation
                    $usd_amount = $currency === 'USD' ? $amount : ($amount * 0.0004); // Rough TZS to USD conversion
                    
                    if ($usd_amount >= 100) {
                        $impact = __('can provide clean water for a family for 6 months', 'kilismile-payments');
                    } elseif ($usd_amount >= 50) {
                        $impact = __('can provide school supplies for 5 children', 'kilismile-payments');
                    } elseif ($usd_amount >= 25) {
                        $impact = __('can provide meals for a family for a week', 'kilismile-payments');
                    } else {
                        $impact = __('contributes to our community programs', 'kilismile-payments');
                    }
                    ?>
                    <div class="impact-item">
                        <i class="fas fa-heart"></i>
                        <span><?php printf(__('Your donation of %s %s', 'kilismile-payments'), 
                                    esc_html($currency === 'USD' ? '$' . number_format($amount, 2) : 'TSh ' . number_format($amount)), 
                                    esc_html($impact)); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($success_data['show_social_share']): ?>
        <!-- Social Share -->
        <div class="social-share">
            <h4><?php _e('Spread the Word', 'kilismile-payments'); ?></h4>
            <p><?php _e('Help us reach more people by sharing our mission on social media.', 'kilismile-payments'); ?></p>
            
            <div class="share-buttons">
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(__('I just made a donation to KiliSmile Organization! Join me in making a difference.', 'kilismile-payments')); ?>&url=<?php echo urlencode(home_url()); ?>" 
                   target="_blank" class="share-btn twitter">
                    <i class="fab fa-twitter"></i>
                    <?php _e('Share on Twitter', 'kilismile-payments'); ?>
                </a>
                
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(home_url()); ?>" 
                   target="_blank" class="share-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                    <?php _e('Share on Facebook', 'kilismile-payments'); ?>
                </a>
                
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(home_url()); ?>" 
                   target="_blank" class="share-btn linkedin">
                    <i class="fab fa-linkedin-in"></i>
                    <?php _e('Share on LinkedIn', 'kilismile-payments'); ?>
                </a>
                
                <button type="button" class="share-btn copy-link" onclick="copyLink()">
                    <i class="fas fa-link"></i>
                    <?php _e('Copy Link', 'kilismile-payments'); ?>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Next Steps -->
        <div class="next-steps">
            <h4><?php _e('What\'s Next?', 'kilismile-payments'); ?></h4>
            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="step-content">
                        <h5><?php _e('Receipt Email', 'kilismile-payments'); ?></h5>
                        <p><?php _e('You\'ll receive a confirmation email with your donation receipt within a few minutes.', 'kilismile-payments'); ?></p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-newsletter"></i>
                    </div>
                    <div class="step-content">
                        <h5><?php _e('Stay Updated', 'kilismile-payments'); ?></h5>
                        <p><?php _e('Subscribe to our newsletter to see how your donation is making an impact.', 'kilismile-payments'); ?></p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="step-content">
                        <h5><?php _e('Get Involved', 'kilismile-payments'); ?></h5>
                        <p><?php _e('Explore other ways to support our mission through volunteering and advocacy.', 'kilismile-payments'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="success-actions">
            <a href="<?php echo esc_url($success_data['return_url']); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <?php echo esc_html($success_data['return_text']); ?>
            </a>
            
            <a href="<?php echo esc_url(home_url('/donate')); ?>" class="btn btn-secondary">
                <i class="fas fa-heart"></i>
                <?php _e('Make Another Donation', 'kilismile-payments'); ?>
            </a>
            
            <a href="<?php echo esc_url(home_url('/newsletter')); ?>" class="btn btn-outline">
                <i class="fas fa-envelope-open"></i>
                <?php _e('Subscribe to Newsletter', 'kilismile-payments'); ?>
            </a>
        </div>
    </div>
</div>

<style>
/* Print-specific styles */
@media print {
    .social-share,
    .next-steps,
    .success-actions,
    .receipt-actions {
        display: none !important;
    }
    
    .transaction-receipt {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .success-container {
        max-width: none !important;
        padding: 0 !important;
    }
}
</style>

<script>
// Copy link functionality
function copyLink() {
    const url = window.location.origin;
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        const btn = event.target.closest('.copy-link');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> <?php _e("Copied!", "kilismile-payments"); ?>';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}

// Email receipt functionality
function sendReceiptEmail() {
    const btn = event.target.closest('.btn-email');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e("Sending...", "kilismile-payments"); ?>';
    
    // AJAX request to resend receipt
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'kilismile_resend_receipt',
            transaction_id: '<?php echo esc_js($transaction_id); ?>',
            nonce: '<?php echo wp_create_nonce("kilismile_resend_receipt"); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> <?php _e("Sent!", "kilismile-payments"); ?>';
        } else {
            btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <?php _e("Failed", "kilismile-payments"); ?>';
        }
        
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 3000);
    })
    .catch(error => {
        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <?php _e("Error", "kilismile-payments"); ?>';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 3000);
    });
}

// Auto-hide success message after page load
document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animation
    const container = document.querySelector('.success-container');
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            container.style.transition = 'all 0.6s ease';
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
        }, 100);
    }
});
</script>

<?php
/**
 * Action hook for additional success page content
 * 
 * @since 1.0.0
 * @param object|null $transaction Transaction object
 * @param array $success_data Success page configuration
 */
do_action('kilismile_payments_success_page', $transaction, $success_data);
?>

