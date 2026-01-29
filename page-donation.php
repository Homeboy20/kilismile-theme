<?php
/**
 * Donation Page Template
 * Template Name: Donation Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<?php
// Display donation-related messages
if (isset($_GET['donation_error'])) {
    $error_type = sanitize_text_field($_GET['donation_error']);
    $error_messages = array(
        'invalid_amount' => __('Please enter a valid donation amount.', 'kilismile'),
        'invalid_currency' => __('Invalid currency selected.', 'kilismile'),
        'missing_info' => __('Please fill in all required fields.', 'kilismile'),
        'invalid_payment_method' => __('Selected payment method is not available.', 'kilismile'),
        'storage_failed' => __('Failed to store donation information. Please try again.', 'kilismile'),
        'invalid_method' => __('Invalid payment method.', 'kilismile'),
        'paypal_not_configured' => __('PayPal is not properly configured. Please contact us directly.', 'kilismile'),
        'stripe_not_configured' => __('Credit card payments are not available. Please try another method.', 'kilismile'),
        'mpesa_not_configured' => __('M-Pesa is not properly configured. Please contact us directly.', 'kilismile'),
        'selcom_not_configured' => __('Selcom Payment Gateway is not properly configured. Please contact us directly.', 'kilismile'),
        'selcom_order_failed' => __('Failed to create Selcom payment order. Please try again or contact us directly.', 'kilismile'),
        'azam_pay_not_configured' => __('Azam Pay Gateway is not properly configured. Please contact us directly.', 'kilismile'),
    );
    
    $error_message = isset($error_messages[$error_type]) ? $error_messages[$error_type] : __('An error occurred. Please try again.', 'kilismile');
    
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="notice notice-error" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0;">';
    echo '<i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>';
    echo esc_html($error_message);
    echo '</div>';
    echo '</div>';
}

if (isset($_GET['donation_success'])) {
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="notice notice-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0;">';
    echo '<i class="fas fa-check-circle" style="margin-right: 10px;"></i>';
    echo __('Thank you for your donation! You will receive a confirmation email shortly.', 'kilismile');
    echo '</div>';
    echo '</div>';
}

if (isset($_GET['donation_pending'])) {
    $payment_method = sanitize_text_field($_GET['payment_method']);
    echo '<div class="container" style="padding: 20px;">';
    echo '<div class="notice notice-info" style="background: #cce7ff; border: 1px solid #99d6ff; color: #0c5aa6; padding: 15px; border-radius: 8px; margin: 20px 0;">';
    echo '<i class="fas fa-info-circle" style="margin-right: 10px;"></i>';
    echo sprintf(__('Your donation is pending. Please complete the payment using %s and we will confirm your donation.', 'kilismile'), ucfirst(str_replace('_', ' ', $payment_method)));
    echo '</div>';
    echo '</div>';
}
?>

<main id="main" class="site-main">
    <div class="container" style="padding: 40px 20px;">
        <?php
        // Use the unified donation form component.
        $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
        if (file_exists($component_path)) {
            include $component_path;
        } else {
            echo kilismile_donation_form(array(
                'title' => __('Make Your Donation', 'kilismile'),
                'show_amounts' => true,
                'show_progress' => false
            ));
        }
        ?>
    </div>
</main>

<?php get_footer(); ?>


