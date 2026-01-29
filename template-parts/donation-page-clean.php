<?php
/**
 * Clean Donation Page Layout
 *
 * Shared layout used by donation page templates.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Notices from redirect/query params
$notice_html = '';
if (isset($_GET['donation_error'])) {
    $error_type = sanitize_text_field(wp_unslash($_GET['donation_error']));
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

    $notice_html .= '<div class="ks-donation-notice ks-donation-notice--error">'
        . '<i class="fas fa-exclamation-triangle" aria-hidden="true"></i>'
        . '<div>' . esc_html($error_message) . '</div>'
        . '</div>';
}

if (isset($_GET['donation_success'])) {
    $notice_html .= '<div class="ks-donation-notice ks-donation-notice--success">'
        . '<i class="fas fa-check-circle" aria-hidden="true"></i>'
        . '<div>' . esc_html__('Thank you for your donation! You will receive a confirmation email shortly.', 'kilismile') . '</div>'
        . '</div>';
}

if (isset($_GET['donation_pending'])) {
    $payment_method = isset($_GET['payment_method']) ? sanitize_text_field(wp_unslash($_GET['payment_method'])) : '';
    $notice_html .= '<div class="ks-donation-notice ks-donation-notice--info">'
        . '<i class="fas fa-info-circle" aria-hidden="true"></i>'
        . '<div>'
        . sprintf(
            esc_html__('Your donation is pending. Please complete the payment using %s and we will confirm your donation.', 'kilismile'),
            esc_html(ucfirst(str_replace('_', ' ', $payment_method)))
        )
        . '</div>'
        . '</div>';
}

$component_path = get_template_directory() . '/template-parts/donation-form-component.php';

$support_email = (string) get_theme_mod('kilismile_email', 'kilismile21@gmail.com');
$support_phone = (string) get_theme_mod('kilismile_phone', '0763495575');
$support_phone_href = preg_replace('/[^0-9+]/', '', $support_phone);
?>

<main id="main" class="site-main ks-donate">
    <a class="ks-donate__skip" href="#ks-donate-form"><?php echo esc_html__('Skip to donation form', 'kilismile'); ?></a>

    <header class="ks-donate-hero" aria-label="Donation">
        <div class="ks-donate__container">
            <div class="ks-donate-hero__grid">
                <div class="ks-donate-hero__copy">
                    <div class="ks-donate-hero__pill">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <span><?php echo esc_html__('Secure & Trusted', 'kilismile'); ?></span>
                    </div>

                    <h1 class="ks-donate-hero__title"><?php echo esc_html__('Make a Donation Today', 'kilismile'); ?></h1>
                    <p class="ks-donate-hero__desc"><?php echo esc_html__('Every contribution delivers healthcare, education, and hope. Your gift is processed securely in under three minutes.', 'kilismile'); ?></p>

                    <div class="ks-donate-hero__cta">
                        <a class="ks-btn ks-btn--primary" href="#ks-donate-form">
                            <span><?php echo esc_html__('Start Donation', 'kilismile'); ?></span>
                            <i class="fas fa-arrow-down" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="ks-donate-hero__trust" aria-label="Donation assurances">
                        <div class="ks-chip"><i class="fas fa-check-circle" aria-hidden="true"></i><span><?php echo esc_html__('Verified nonprofit', 'kilismile'); ?></span></div>
                        <div class="ks-chip"><i class="fas fa-shield-alt" aria-hidden="true"></i><span><?php echo esc_html__('Secure payments', 'kilismile'); ?></span></div>
                        <div class="ks-chip"><i class="fas fa-receipt" aria-hidden="true"></i><span><?php echo esc_html__('Instant receipt', 'kilismile'); ?></span></div>
                    </div>
                </div>

                <div class="ks-donate-hero__panel" aria-label="Support">
                    <div class="ks-panel">
                        <div class="ks-panel__title"><?php echo esc_html__('Your gift goes straight to work', 'kilismile'); ?></div>
                        <div class="ks-panel__text"><?php echo esc_html__('We prioritize direct program support, so your donation funds healthcare access, outreach, and education where it is needed most.', 'kilismile'); ?></div>
                    </div>

                    <div class="ks-panel ks-panel--soft">
                        <div class="ks-panel__title"><?php echo esc_html__('In three simple steps', 'kilismile'); ?></div>
                        <ul class="ks-bullets">
                            <li><?php echo esc_html__('Choose a meaningful amount', 'kilismile'); ?></li>
                            <li><?php echo esc_html__('Enter your details securely', 'kilismile'); ?></li>
                            <li><?php echo esc_html__('Pick your preferred payment method', 'kilismile'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="ks-donate-section ks-donate-section--impact" aria-label="Why Donate">
        <div class="ks-donate__container">
            <div class="ks-sectionHead">
                <h2 class="ks-sectionHead__title"><?php echo esc_html__('Why Your Donation Matters', 'kilismile'); ?></h2>
                <p class="ks-sectionHead__desc"><?php echo esc_html__('Your contribution creates immediate, measurable change. We keep the process clear so you can give with confidence.', 'kilismile'); ?></p>
            </div>

            <div class="ks-stats" role="list">
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-hand-holding-heart" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Direct impact', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('Donations fund essential care, community outreach, and health education services.', 'kilismile'); ?></div>
                </div>
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Transparency', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('You receive a confirmation email and a clear reference for every donation.', 'kilismile'); ?></div>
                </div>
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-users" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Trusted by donors', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('Supporters choose KiliSmile for reliable, secure giving that reaches communities.', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <section id="ks-donate-form" class="ks-donate-section ks-donate-section--form" aria-label="Donation Form">
        <div class="ks-donate__container">
            <?php if ($notice_html) : ?>
                <div class="ks-donate-notices"><?php echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            <?php endif; ?>

            <div class="ks-donate-layout">
                <div class="ks-donate-formWrap">
                    <?php
                    if (!empty($component_path) && file_exists($component_path)) {
                        include $component_path;
                    } else {
                        echo '<div class="ks-donate-unavailable">'
                            . '<h3>' . esc_html__('Donation Form Unavailable', 'kilismile') . '</h3>'
                            . '<p>' . esc_html__('The donation form could not be loaded. Please try again later.', 'kilismile') . '</p>'
                            . '</div>';
                    }
                    ?>
                </div>

                <aside class="ks-donate-aside" aria-label="Donation Info">
                    <div class="ks-card">
                        <h3 class="ks-card__title"><?php echo esc_html__('Payment Security', 'kilismile'); ?></h3>
                        <ul class="ks-bullets">
                            <li><?php echo esc_html__('SSL encrypted connection', 'kilismile'); ?></li>
                            <li><?php echo esc_html__('Secure payment processing', 'kilismile'); ?></li>
                            <li><?php echo esc_html__('PCI compliant systems', 'kilismile'); ?></li>
                            <li><?php echo esc_html__('No card details stored', 'kilismile'); ?></li>
                        </ul>
                    </div>

                    <div class="ks-card">
                        <h3 class="ks-card__title"><?php echo esc_html__('Supported Currencies', 'kilismile'); ?></h3>
                        <ul class="ks-bullets">
                            <li><strong>TZS</strong> - <?php echo esc_html__('Tanzanian Shilling (Mobile Money & Bank)', 'kilismile'); ?></li>
                            <li><strong>USD</strong> - <?php echo esc_html__('US Dollar (PayPal & Bank)', 'kilismile'); ?></li>
                        </ul>
                    </div>

                    <div class="ks-card">
                        <h3 class="ks-card__title"><?php echo esc_html__('Need Assistance?', 'kilismile'); ?></h3>
                        <div class="ks-panel__text" style="margin-bottom: 16px;"><?php echo esc_html__('Our team is here to help with any questions about the donation process.', 'kilismile'); ?></div>
                        <div class="ks-panel__actions">
                            <?php if (!empty($support_email)) : ?>
                                <a class="ks-link" href="mailto:<?php echo esc_attr($support_email); ?>">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                    <span><?php echo esc_html__('Email Support', 'kilismile'); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($support_phone_href)) : ?>
                                <a class="ks-link" href="tel:<?php echo esc_attr($support_phone_href); ?>">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <span><?php echo esc_html__('Call Us', 'kilismile'); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="ks-card">
                        <h3 class="ks-card__title"><?php echo esc_html__('Frequently Asked Questions', 'kilismile'); ?></h3>
                        <details class="ks-faq">
                            <summary><?php echo esc_html__('How long does processing take?', 'kilismile'); ?></summary>
                            <div class="ks-faq__body"><?php echo esc_html__('Mobile Money and PayPal donations are processed instantly. Bank transfers may take 1-3 business days to reflect.', 'kilismile'); ?></div>
                        </details>
                        <details class="ks-faq">
                            <summary><?php echo esc_html__('Will I receive a receipt?', 'kilismile'); ?></summary>
                            <div class="ks-faq__body"><?php echo esc_html__('Yes. A confirmation email with receipt will be sent immediately to the email address you provide.', 'kilismile'); ?></div>
                        </details>
                        <details class="ks-faq">
                            <summary><?php echo esc_html__('Can I donate anonymously?', 'kilismile'); ?></summary>
                            <div class="ks-faq__body"><?php echo esc_html__('Yes. Check the "Make this donation anonymous" option in Step 2 of the form to keep your donation private.', 'kilismile'); ?></div>
                        </details>
                        <details class="ks-faq">
                            <summary><?php echo esc_html__('What if I encounter an error?', 'kilismile'); ?></summary>
                            <div class="ks-faq__body"><?php echo esc_html__('If you experience any issues, please contact our support team. Your donation will not be charged until successfully completed.', 'kilismile'); ?></div>
                        </details>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="ks-donate-section ks-donate-section--impact" aria-label="Donation Details">
        <div class="ks-donate__container">
            <div class="ks-sectionHead">
                <h2 class="ks-sectionHead__title"><?php echo esc_html__('Donation Details & Transparency', 'kilismile'); ?></h2>
                <p class="ks-sectionHead__desc"><?php echo esc_html__('We believe donors deserve clarity. Here is what to expect before and after you give.', 'kilismile'); ?></p>
            </div>

            <div class="ks-stats" role="list">
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-clipboard-list" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Where your gift goes', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('Donations support health outreach, education, and essential care. We prioritize direct program services.', 'kilismile'); ?></div>
                </div>
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Secure processing', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('Your payment is encrypted end‑to‑end. We do not store full card details.', 'kilismile'); ?></div>
                </div>
                <div class="ks-stat" role="listitem">
                    <div class="ks-stat__icon"><i class="fas fa-receipt" aria-hidden="true"></i></div>
                    <div class="ks-stat__title"><?php echo esc_html__('Instant confirmation', 'kilismile'); ?></div>
                    <div class="ks-stat__text"><?php echo esc_html__('You receive a confirmation email with a reference ID for your records.', 'kilismile'); ?></div>
                </div>
            </div>

            <div class="ks-donate-layout" style="margin-top: 32px;">
                <div class="ks-card">
                    <h3 class="ks-card__title"><?php echo esc_html__('Bank Transfer Guidance', 'kilismile'); ?></h3>
                    <ul class="ks-bullets">
                        <li><?php echo esc_html__('Select Bank Transfer on Step 3 of the form.', 'kilismile'); ?></li>
                        <li><?php echo esc_html__('You will receive bank details and a reference ID.', 'kilismile'); ?></li>
                        <li><?php echo esc_html__('Include the reference in your transfer to ensure fast verification.', 'kilismile'); ?></li>
                    </ul>
                </div>
                <div class="ks-card">
                    <h3 class="ks-card__title"><?php echo esc_html__('Need assistance?', 'kilismile'); ?></h3>
                    <p class="ks-panel__text" style="margin-top: 0;"><?php echo esc_html__('Questions about your donation? Our team is ready to help.', 'kilismile'); ?></p>
                    <div class="ks-panel__actions">
                        <?php if (!empty($support_email)) : ?>
                            <a class="ks-link" href="mailto:<?php echo esc_attr($support_email); ?>">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <span><?php echo esc_html__('Email Support', 'kilismile'); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($support_phone_href)) : ?>
                            <a class="ks-link" href="tel:<?php echo esc_attr($support_phone_href); ?>">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <span><?php echo esc_html__('Call Us', 'kilismile'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
