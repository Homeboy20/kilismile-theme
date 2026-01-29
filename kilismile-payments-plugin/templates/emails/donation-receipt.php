<?php
/**
 * KiliSmile Payments - Donation Receipt Email Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Email data (passed to template)
$transaction = $args['transaction'] ?? null;
$donor_name = $args['donor_name'] ?? '';
$organization_name = get_bloginfo('name');
$organization_logo = get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'medium') : '';
$organization_address = get_option('kilismile_address', '123 Main Street, Dar es Salaam, Tanzania');
$organization_phone = get_option('kilismile_phone', '+255763495575/+255735495575');
$organization_email = get_option('kilismile_email', 'info@kilismile.org');
$tax_id = get_option('kilismile_tax_id', 'TIN: 123-456-789');

// Set content type to HTML
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php printf(__('Donation Receipt - %s', 'kilismile-payments'), esc_html($organization_name)); ?></title>
    <style>
        /* Email-safe CSS styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #007cba, #005a87);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #007cba;
        }
        
        .receipt-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #007cba;
            padding-bottom: 15px;
        }
        
        .receipt-header h2 {
            margin: 0;
            font-size: 20px;
            color: #007cba;
        }
        
        .receipt-number {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .receipt-details {
            margin-top: 20px;
        }
        
        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        
        .detail-label {
            display: table-cell;
            font-weight: 600;
            width: 40%;
            padding-right: 15px;
            color: #495057;
        }
        
        .detail-value {
            display: table-cell;
            color: #212529;
        }
        
        .amount-highlight {
            font-size: 24px;
            font-weight: 700;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #d4edda;
            border-radius: 6px;
        }
        
        .impact-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        
        .impact-section h3 {
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        
        .impact-section p {
            margin: 0;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .tax-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .tax-info h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        
        .tax-info p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .organization-info {
            margin-bottom: 20px;
        }
        
        .organization-info h4 {
            margin: 0 0 10px 0;
            color: #007cba;
            font-size: 16px;
        }
        
        .organization-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            padding: 8px 12px;
            background-color: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .unsubscribe {
            font-size: 12px;
            color: #6c757d;
            margin-top: 20px;
        }
        
        .unsubscribe a {
            color: #6c757d;
        }
        
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .receipt-card {
                padding: 20px 15px;
            }
            
            .detail-row {
                display: block;
            }
            
            .detail-label {
                display: block;
                width: 100%;
                margin-bottom: 5px;
            }
            
            .detail-value {
                display: block;
                font-weight: 600;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <?php if ($organization_logo): ?>
            <img src="<?php echo esc_url($organization_logo); ?>" alt="<?php echo esc_attr($organization_name); ?>" />
            <?php endif; ?>
            <h1><?php _e('Donation Receipt', 'kilismile-payments'); ?></h1>
            <p><?php printf(__('Thank you for supporting %s', 'kilismile-payments'), esc_html($organization_name)); ?></p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                <?php printf(__('Dear %s,', 'kilismile-payments'), esc_html($donor_name ?: __('Valued Supporter', 'kilismile-payments'))); ?>
            </div>

            <p><?php _e('Thank you for your generous donation! Your support makes a significant difference in our mission to create positive change in our communities.', 'kilismile-payments'); ?></p>

            <?php if ($transaction): ?>
            <!-- Receipt Card -->
            <div class="receipt-card">
                <div class="receipt-header">
                    <h2><?php _e('Official Donation Receipt', 'kilismile-payments'); ?></h2>
                    <div class="receipt-number">
                        <?php printf(__('Receipt #%s', 'kilismile-payments'), esc_html($transaction->id)); ?>
                    </div>
                </div>

                <!-- Amount Highlight -->
                <div class="amount-highlight">
                    <?php 
                    $symbol = $transaction->currency === 'USD' ? '$' : 'TSh ';
                    echo esc_html($symbol . number_format($transaction->amount, 2));
                    ?>
                </div>

                <div class="receipt-details">
                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Date:', 'kilismile-payments'); ?></span>
                        <span class="detail-value">
                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Amount:', 'kilismile-payments'); ?></span>
                        <span class="detail-value">
                            <?php echo esc_html($symbol . number_format($transaction->amount, 2) . ' ' . $transaction->currency); ?>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Payment Method:', 'kilismile-payments'); ?></span>
                        <span class="detail-value"><?php echo esc_html(ucfirst(str_replace('_', ' ', $transaction->gateway))); ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Transaction ID:', 'kilismile-payments'); ?></span>
                        <span class="detail-value"><?php echo esc_html($transaction->id); ?></span>
                    </div>

                    <?php if (!empty($transaction->gateway_transaction_id)): ?>
                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Gateway Reference:', 'kilismile-payments'); ?></span>
                        <span class="detail-value"><?php echo esc_html($transaction->gateway_transaction_id); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php 
                    // Check for recurring donation
                    $metadata = null;
                    if (!empty($transaction->metadata) && is_string($transaction->metadata)) {
                        $metadata = json_decode($transaction->metadata, true);
                    }
                    
                    if ($metadata && isset($metadata['recurring']) && $metadata['recurring']):
                    ?>
                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Recurring:', 'kilismile-payments'); ?></span>
                        <span class="detail-value">
                            <?php 
                            $interval = isset($metadata['recurring_interval']) ? $metadata['recurring_interval'] : 'monthly';
                            printf(__('Yes (%s)', 'kilismile-payments'), esc_html(ucfirst($interval)));
                            ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($donor_name)): ?>
                    <div class="detail-row">
                        <span class="detail-label"><?php _e('Donor:', 'kilismile-payments'); ?></span>
                        <span class="detail-value"><?php echo esc_html($donor_name); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Impact Section -->
            <div class="impact-section">
                <h3><?php _e('Your Impact', 'kilismile-payments'); ?></h3>
                <p>
                    <?php
                    if ($transaction) {
                        $amount = floatval($transaction->amount);
                        $currency = $transaction->currency;
                        
                        // Convert to USD for impact calculation
                        $usd_amount = $currency === 'USD' ? $amount : ($amount * 0.0004);
                        
                        if ($usd_amount >= 100) {
                            echo __('Your generous donation can provide clean water access for a family for 6 months, or school supplies for 10 children.', 'kilismile-payments');
                        } elseif ($usd_amount >= 50) {
                            echo __('Your donation can provide nutritious meals for a family for two weeks, or educational materials for 5 children.', 'kilismile-payments');
                        } elseif ($usd_amount >= 25) {
                            echo __('Your support can provide essential supplies for our community programs and help us reach more families in need.', 'kilismile-payments');
                        } else {
                            echo __('Every donation makes a difference! Your contribution helps us continue our vital community programs.', 'kilismile-payments');
                        }
                    } else {
                        echo __('Your generous donation helps us continue our mission to support communities and create positive change. Thank you for making a difference!', 'kilismile-payments');
                    }
                    ?>
                </p>
            </div>

            <!-- Tax Information -->
            <div class="tax-info">
                <h4><?php _e('Tax Information', 'kilismile-payments'); ?></h4>
                <p>
                    <?php 
                    printf(
                        __('%s is a registered non-profit organization (%s). This receipt serves as official documentation for tax purposes. Please consult with your tax advisor regarding the deductibility of your donation.', 'kilismile-payments'),
                        esc_html($organization_name),
                        esc_html($tax_id)
                    );
                    ?>
                </p>
            </div>

            <p><?php _e('We are incredibly grateful for your support. Your donation enables us to continue our important work in the community.', 'kilismile-payments'); ?></p>

            <p><?php _e('If you have any questions about your donation or would like to learn more about our programs, please don\'t hesitate to contact us.', 'kilismile-payments'); ?></p>

            <p>
                <?php _e('With gratitude,', 'kilismile-payments'); ?><br>
                <strong><?php printf(__('The %s Team', 'kilismile-payments'), esc_html($organization_name)); ?></strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="organization-info">
                <h4><?php echo esc_html($organization_name); ?></h4>
                <p><?php echo esc_html($organization_address); ?></p>
                <p>
                    <?php _e('Phone:', 'kilismile-payments'); ?> <?php echo esc_html($organization_phone); ?> | 
                    <?php _e('Email:', 'kilismile-payments'); ?> <?php echo esc_html($organization_email); ?>
                </p>
                <p><?php echo esc_html($tax_id); ?></p>
            </div>

            <div class="social-links">
                <a href="<?php echo esc_url(home_url('/newsletter')); ?>"><?php _e('Newsletter', 'kilismile-payments'); ?></a>
                <a href="<?php echo esc_url(home_url('/programs')); ?>"><?php _e('Our Programs', 'kilismile-payments'); ?></a>
                <a href="<?php echo esc_url(home_url('/get-involved')); ?>"><?php _e('Get Involved', 'kilismile-payments'); ?></a>
            </div>

            <div class="unsubscribe">
                <p>
                    <?php _e('You received this email because you made a donation to our organization.', 'kilismile-payments'); ?>
                    <br>
                    <a href="<?php echo esc_url(home_url('/unsubscribe')); ?>"><?php _e('Unsubscribe from future emails', 'kilismile-payments'); ?></a> | 
                    <a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact Us', 'kilismile-payments'); ?></a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

