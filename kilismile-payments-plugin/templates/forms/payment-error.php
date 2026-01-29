<?php
/**
 * KiliSmile Payments - Payment Error Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get error data
$error_code = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : 'general';
$transaction_id = isset($_GET['transaction_id']) ? sanitize_text_field($_GET['transaction_id']) : '';
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';

// Default error data
$error_data = wp_parse_args($args ?? array(), array(
    'title' => __('Payment Error', 'kilismile-payments'),
    'message' => $message ?: __('There was an issue processing your payment.', 'kilismile-payments'),
    'show_retry' => true,
    'show_contact' => true,
    'return_url' => home_url('/donate'),
    'return_text' => __('Try Again', 'kilismile-payments')
));

// Get transaction if available
$transaction = null;
if ($transaction_id) {
    $database = KiliSmile_Payments_Plugin::get_instance()->get_database();
    $transaction = $database->get_transaction($transaction_id);
}

// Define error types and messages
$error_types = array(
    'payment_failed' => array(
        'title' => __('Payment Failed', 'kilismile-payments'),
        'message' => __('Your payment could not be processed at this time.', 'kilismile-payments'),
        'icon' => 'fas fa-times-circle',
        'suggestions' => array(
            __('Check your payment details and try again', 'kilismile-payments'),
            __('Ensure you have sufficient funds', 'kilismile-payments'),
            __('Try a different payment method', 'kilismile-payments')
        )
    ),
    'payment_cancelled' => array(
        'title' => __('Payment Cancelled', 'kilismile-payments'),
        'message' => __('You cancelled the payment process.', 'kilismile-payments'),
        'icon' => 'fas fa-ban',
        'suggestions' => array(
            __('You can try again at any time', 'kilismile-payments'),
            __('Choose a different payment method if needed', 'kilismile-payments'),
            __('Contact us if you need assistance', 'kilismile-payments')
        )
    ),
    'gateway_error' => array(
        'title' => __('Gateway Error', 'kilismile-payments'),
        'message' => __('The payment gateway is temporarily unavailable.', 'kilismile-payments'),
        'icon' => 'fas fa-exclamation-triangle',
        'suggestions' => array(
            __('Try again in a few minutes', 'kilismile-payments'),
            __('Use an alternative payment method', 'kilismile-payments'),
            __('Contact us if the problem persists', 'kilismile-payments')
        )
    ),
    'validation_error' => array(
        'title' => __('Validation Error', 'kilismile-payments'),
        'message' => __('Some required information is missing or invalid.', 'kilismile-payments'),
        'icon' => 'fas fa-exclamation-circle',
        'suggestions' => array(
            __('Check all required fields are filled', 'kilismile-payments'),
            __('Verify your email address format', 'kilismile-payments'),
            __('Ensure phone number is valid', 'kilismile-payments')
        )
    ),
    'network_error' => array(
        'title' => __('Connection Error', 'kilismile-payments'),
        'message' => __('There was a problem connecting to the payment service.', 'kilismile-payments'),
        'icon' => 'fas fa-wifi',
        'suggestions' => array(
            __('Check your internet connection', 'kilismile-payments'),
            __('Try refreshing the page', 'kilismile-payments'),
            __('Try again in a few moments', 'kilismile-payments')
        )
    ),
    'general' => array(
        'title' => __('Payment Error', 'kilismile-payments'),
        'message' => __('An unexpected error occurred.', 'kilismile-payments'),
        'icon' => 'fas fa-exclamation-triangle',
        'suggestions' => array(
            __('Try the payment again', 'kilismile-payments'),
            __('Use a different payment method', 'kilismile-payments'),
            __('Contact us for assistance', 'kilismile-payments')
        )
    )
);

// Get error details
$error_info = isset($error_types[$error_code]) ? $error_types[$error_code] : $error_types['general'];
if (!empty($error_data['title'])) $error_info['title'] = $error_data['title'];
if (!empty($error_data['message'])) $error_info['message'] = $error_data['message'];

// Enqueue assets
wp_enqueue_style('kilismile-payments-frontend');
?>

<div class="kilismile-payment-error">
    <div class="error-container">
        
        <!-- Error Icon and Header -->
        <div class="error-header">
            <div class="error-icon">
                <i class="<?php echo esc_attr($error_info['icon']); ?>"></i>
            </div>
            <h1 class="error-title"><?php echo esc_html($error_info['title']); ?></h1>
            <p class="error-message"><?php echo esc_html($error_info['message']); ?></p>
        </div>

        <?php if ($transaction): ?>
        <!-- Transaction Information -->
        <div class="transaction-info">
            <h3><?php _e('Transaction Details', 'kilismile-payments'); ?></h3>
            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Transaction ID:', 'kilismile-payments'); ?></span>
                    <span class="detail-value"><?php echo esc_html($transaction->id); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Amount:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <?php 
                        $symbol = $transaction->currency === 'USD' ? '$' : 'TSh ';
                        echo esc_html($symbol . number_format($transaction->amount, 2));
                        ?>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Status:', 'kilismile-payments'); ?></span>
                    <span class="detail-value status-<?php echo esc_attr($transaction->status); ?>">
                        <?php echo esc_html(ucfirst($transaction->status)); ?>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Date:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Suggestions -->
        <?php if (!empty($error_info['suggestions'])): ?>
        <div class="error-suggestions">
            <h3><?php _e('What You Can Do', 'kilismile-payments'); ?></h3>
            <ul class="suggestions-list">
                <?php foreach ($error_info['suggestions'] as $suggestion): ?>
                <li>
                    <i class="fas fa-lightbulb"></i>
                    <?php echo esc_html($suggestion); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Common Issues -->
        <div class="common-issues">
            <h3><?php _e('Common Issues & Solutions', 'kilismile-payments'); ?></h3>
            <div class="issues-grid">
                <div class="issue-item">
                    <div class="issue-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="issue-content">
                        <h4><?php _e('Card Issues', 'kilismile-payments'); ?></h4>
                        <ul>
                            <li><?php _e('Check card expiry date', 'kilismile-payments'); ?></li>
                            <li><?php _e('Verify CVV code', 'kilismile-payments'); ?></li>
                            <li><?php _e('Ensure sufficient funds', 'kilismile-payments'); ?></li>
                        </ul>
                    </div>
                </div>
                
                <div class="issue-item">
                    <div class="issue-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="issue-content">
                        <h4><?php _e('Mobile Money', 'kilismile-payments'); ?></h4>
                        <ul>
                            <li><?php _e('Check phone number format', 'kilismile-payments'); ?></li>
                            <li><?php _e('Ensure mobile money account is active', 'kilismile-payments'); ?></li>
                            <li><?php _e('Check account balance', 'kilismile-payments'); ?></li>
                        </ul>
                    </div>
                </div>
                
                <div class="issue-item">
                    <div class="issue-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="issue-content">
                        <h4><?php _e('Connection', 'kilismile-payments'); ?></h4>
                        <ul>
                            <li><?php _e('Check internet connection', 'kilismile-payments'); ?></li>
                            <li><?php _e('Disable VPN if using one', 'kilismile-payments'); ?></li>
                            <li><?php _e('Try a different browser', 'kilismile-payments'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <?php if ($error_data['show_contact']): ?>
        <div class="support-section">
            <h3><?php _e('Need Help?', 'kilismile-payments'); ?></h3>
            <p><?php _e('If you continue to experience issues, our support team is here to help.', 'kilismile-payments'); ?></p>
            
            <div class="contact-options">
                <div class="contact-option">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info">
                        <h4><?php _e('Email Support', 'kilismile-payments'); ?></h4>
                        <p>
                            <a href="mailto:support@kilismile.org">support@kilismile.org</a><br>
                            <small><?php _e('Response within 24 hours', 'kilismile-payments'); ?></small>
                        </p>
                    </div>
                </div>
                
                <div class="contact-option">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-info">
                        <h4><?php _e('Phone Support', 'kilismile-payments'); ?></h4>
                        <p>
                            <a href="tel:+255763495575">+255763495575/+255735495575</a><br>
                            <small><?php _e('Monday - Friday, 9AM - 5PM EAT', 'kilismile-payments'); ?></small>
                        </p>
                    </div>
                </div>
                
                <div class="contact-option">
                    <div class="contact-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="contact-info">
                        <h4><?php _e('Live Chat', 'kilismile-payments'); ?></h4>
                        <p>
                            <button type="button" class="btn-link" onclick="openLiveChat()">
                                <?php _e('Start Live Chat', 'kilismile-payments'); ?>
                            </button><br>
                            <small><?php _e('Available during business hours', 'kilismile-payments'); ?></small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Include transaction details for support -->
            <?php if ($transaction_id): ?>
            <div class="support-reference">
                <p class="reference-note">
                    <i class="fas fa-info-circle"></i>
                    <?php printf(__('When contacting support, please reference transaction ID: %s', 'kilismile-payments'), 
                                '<strong>' . esc_html($transaction_id) . '</strong>'); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Alternative Payment Methods -->
        <div class="alternative-methods">
            <h3><?php _e('Alternative Payment Options', 'kilismile-payments'); ?></h3>
            <p><?php _e('Consider these alternative ways to complete your donation:', 'kilismile-payments'); ?></p>
            
            <div class="alternatives-grid">
                <div class="alternative-item">
                    <div class="alternative-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="alternative-content">
                        <h4><?php _e('Bank Transfer', 'kilismile-payments'); ?></h4>
                        <p><?php _e('Transfer directly to our bank account', 'kilismile-payments'); ?></p>
                        <a href="<?php echo esc_url(home_url('/bank-details')); ?>" class="btn btn-sm btn-outline">
                            <?php _e('Get Bank Details', 'kilismile-payments'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="alternative-item">
                    <div class="alternative-icon">
                        <i class="fas fa-money-check"></i>
                    </div>
                    <div class="alternative-content">
                        <h4><?php _e('Check/Cheque', 'kilismile-payments'); ?></h4>
                        <p><?php _e('Mail a check to our office', 'kilismile-payments'); ?></p>
                        <a href="<?php echo esc_url(home_url('/mailing-address')); ?>" class="btn btn-sm btn-outline">
                            <?php _e('Mailing Address', 'kilismile-payments'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="alternative-item">
                    <div class="alternative-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="alternative-content">
                        <h4><?php _e('In-Person', 'kilismile-payments'); ?></h4>
                        <p><?php _e('Visit our office to donate in person', 'kilismile-payments'); ?></p>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-sm btn-outline">
                            <?php _e('Visit Us', 'kilismile-payments'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="error-actions">
            <?php if ($error_data['show_retry']): ?>
            <a href="<?php echo esc_url($error_data['return_url']); ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i>
                <?php echo esc_html($error_data['return_text']); ?>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                <?php _e('Return Home', 'kilismile-payments'); ?>
            </a>
            
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-outline">
                <i class="fas fa-envelope"></i>
                <?php _e('Contact Support', 'kilismile-payments'); ?>
            </a>
        </div>

        <!-- Error Reporting -->
        <div class="error-reporting">
            <details>
                <summary><?php _e('Technical Details', 'kilismile-payments'); ?></summary>
                <div class="technical-details">
                    <p><strong><?php _e('Error Code:', 'kilismile-payments'); ?></strong> <?php echo esc_html($error_code); ?></p>
                    <p><strong><?php _e('Timestamp:', 'kilismile-payments'); ?></strong> <?php echo esc_html(current_time('Y-m-d H:i:s T')); ?></p>
                    <p><strong><?php _e('User Agent:', 'kilismile-payments'); ?></strong> <?php echo esc_html($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'); ?></p>
                    <?php if ($transaction_id): ?>
                    <p><strong><?php _e('Transaction ID:', 'kilismile-payments'); ?></strong> <?php echo esc_html($transaction_id); ?></p>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-sm btn-secondary" onclick="reportError()">
                        <i class="fas fa-bug"></i>
                        <?php _e('Report This Error', 'kilismile-payments'); ?>
                    </button>
                </div>
            </details>
        </div>
    </div>
</div>

<script>
// Live chat functionality (placeholder)
function openLiveChat() {
    // Implement live chat integration
    alert('<?php esc_html_e("Live chat will open here", "kilismile-payments"); ?>');
}

// Error reporting functionality
function reportError() {
    const errorData = {
        error_code: '<?php echo esc_js($error_code); ?>',
        transaction_id: '<?php echo esc_js($transaction_id); ?>',
        timestamp: '<?php echo esc_js(current_time('c')); ?>',
        user_agent: navigator.userAgent,
        page_url: window.location.href
    };
    
    // Send error report
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'kilismile_report_error',
            error_data: JSON.stringify(errorData),
            nonce: '<?php echo wp_create_nonce("kilismile_error_report"); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('<?php esc_html_e("Error report sent. Thank you!", "kilismile-payments"); ?>');
        } else {
            alert('<?php esc_html_e("Failed to send error report", "kilismile-payments"); ?>');
        }
    });
}

// Copy transaction ID functionality
function copyTransactionId() {
    const transactionId = '<?php echo esc_js($transaction_id); ?>';
    if (transactionId) {
        navigator.clipboard.writeText(transactionId).then(function() {
            alert('<?php esc_html_e("Transaction ID copied to clipboard", "kilismile-payments"); ?>');
        });
    }
}

// Add entrance animation
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.error-container');
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
 * Action hook for additional error page content
 * 
 * @since 1.0.0
 * @param string $error_code Error code
 * @param object|null $transaction Transaction object
 * @param array $error_data Error page configuration
 */
do_action('kilismile_payments_error_page', $error_code, $transaction, $error_data);
?>

