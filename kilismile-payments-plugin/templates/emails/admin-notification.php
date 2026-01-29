<?php
/**
 * KiliSmile Payments - Admin Notification Email Template
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
$notification_type = $args['type'] ?? 'new_donation';
$organization_name = get_bloginfo('name');
$admin_url = admin_url('admin.php?page=kilismile-payments');

// Set content type to HTML
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

// Define notification types
$notification_config = array(
    'new_donation' => array(
        'subject' => __('New Donation Received', 'kilismile-payments'),
        'title' => __('New Donation Alert', 'kilismile-payments'),
        'icon' => '💚',
        'color' => '#28a745'
    ),
    'payment_failed' => array(
        'subject' => __('Payment Failed Alert', 'kilismile-payments'),
        'title' => __('Payment Failure Notification', 'kilismile-payments'),
        'icon' => '⚠️',
        'color' => '#dc3545'
    ),
    'recurring_payment' => array(
        'subject' => __('Recurring Payment Processed', 'kilismile-payments'),
        'title' => __('Recurring Donation Alert', 'kilismile-payments'),
        'icon' => '🔄',
        'color' => '#007cba'
    ),
    'high_value_donation' => array(
        'subject' => __('High Value Donation Received', 'kilismile-payments'),
        'title' => __('Major Gift Alert', 'kilismile-payments'),
        'icon' => '🌟',
        'color' => '#ffc107'
    )
);

$config = $notification_config[$notification_type] ?? $notification_config['new_donation'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($config['subject']); ?></title>
    <style>
        /* Email-safe CSS styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .header {
            background: <?php echo esc_attr($config['color']); ?>;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header .timestamp {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .alert-summary {
            background-color: #f8f9fa;
            border-left: 4px solid <?php echo esc_attr($config['color']); ?>;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .summary-amount {
            font-size: 32px;
            font-weight: 700;
            color: <?php echo esc_attr($config['color']); ?>;
            text-align: center;
            margin: 15px 0;
        }
        
        .transaction-details {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .transaction-details h3 {
            margin: 0 0 20px 0;
            color: #495057;
            font-size: 18px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        
        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        
        .detail-label {
            display: table-cell;
            font-weight: 600;
            width: 35%;
            padding-right: 15px;
            color: #6c757d;
            vertical-align: top;
        }
        
        .detail-value {
            display: table-cell;
            color: #212529;
            vertical-align: top;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #007cba;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .statistics {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .statistics h4 {
            margin: 0 0 15px 0;
            color: #495057;
            text-align: center;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 0 15px;
        }
        
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: #007cba;
            margin-bottom: 5px;
        }
        
        .stat-label {
            display: block;
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
        
        .footer a {
            color: #007cba;
            text-decoration: none;
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
            
            .detail-row {
                display: block;
                margin-bottom: 15px;
            }
            
            .detail-label {
                display: block;
                width: 100%;
                margin-bottom: 5px;
            }
            
            .detail-value {
                display: block;
                font-weight: 600;
            }
            
            .stats-grid {
                display: block;
            }
            
            .stat-item {
                display: block;
                margin-bottom: 15px;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <span class="icon"><?php echo $config['icon']; ?></span>
            <h1><?php echo esc_html($config['title']); ?></h1>
            <div class="timestamp">
                <?php echo esc_html(current_time('F j, Y \a\t g:i A T')); ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <?php if ($transaction): ?>
            
            <!-- Alert Summary -->
            <div class="alert-summary">
                <?php if ($notification_type === 'new_donation'): ?>
                    <p style="margin: 0; text-align: center; font-size: 16px;">
                        <?php _e('A new donation has been received!', 'kilismile-payments'); ?>
                    </p>
                    
                <?php elseif ($notification_type === 'payment_failed'): ?>
                    <p style="margin: 0; text-align: center; font-size: 16px;">
                        <?php _e('A payment attempt has failed and may require attention.', 'kilismile-payments'); ?>
                    </p>
                    
                <?php elseif ($notification_type === 'recurring_payment'): ?>
                    <p style="margin: 0; text-align: center; font-size: 16px;">
                        <?php _e('A recurring donation has been processed successfully.', 'kilismile-payments'); ?>
                    </p>
                    
                <?php elseif ($notification_type === 'high_value_donation'): ?>
                    <p style="margin: 0; text-align: center; font-size: 16px;">
                        <?php _e('A high-value donation has been received that may warrant special attention.', 'kilismile-payments'); ?>
                    </p>
                <?php endif; ?>

                <div class="summary-amount">
                    <?php 
                    $symbol = $transaction->currency === 'USD' ? '$' : 'TSh ';
                    echo esc_html($symbol . number_format($transaction->amount, 2));
                    ?>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="transaction-details">
                <h3><?php _e('Transaction Details', 'kilismile-payments'); ?></h3>
                
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Transaction ID:', 'kilismile-payments'); ?></span>
                    <span class="detail-value"><?php echo esc_html($transaction->id); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><?php _e('Amount:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <?php echo esc_html($symbol . number_format($transaction->amount, 2) . ' ' . $transaction->currency); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><?php _e('Status:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo esc_attr($transaction->status); ?>">
                            <?php echo esc_html(ucfirst($transaction->status)); ?>
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><?php _e('Payment Gateway:', 'kilismile-payments'); ?></span>
                    <span class="detail-value"><?php echo esc_html(ucfirst(str_replace('_', ' ', $transaction->gateway))); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label"><?php _e('Date & Time:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?>
                    </span>
                </div>

                <?php if (!empty($transaction->donor_name)): ?>
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Donor Name:', 'kilismile-payments'); ?></span>
                    <span class="detail-value"><?php echo esc_html($transaction->donor_name); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($transaction->donor_email)): ?>
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Donor Email:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <a href="mailto:<?php echo esc_attr($transaction->donor_email); ?>">
                            <?php echo esc_html($transaction->donor_email); ?>
                        </a>
                    </span>
                </div>
                <?php endif; ?>

                <?php if (!empty($transaction->donor_phone)): ?>
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Donor Phone:', 'kilismile-payments'); ?></span>
                    <span class="detail-value">
                        <a href="tel:<?php echo esc_attr($transaction->donor_phone); ?>">
                            <?php echo esc_html($transaction->donor_phone); ?>
                        </a>
                    </span>
                </div>
                <?php endif; ?>

                <?php if (!empty($transaction->gateway_transaction_id)): ?>
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Gateway Reference:', 'kilismile-payments'); ?></span>
                    <span class="detail-value"><?php echo esc_html($transaction->gateway_transaction_id); ?></span>
                </div>
                <?php endif; ?>

                <?php
                // Check for metadata
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

                <?php if (!empty($transaction->failure_reason) && $transaction->status === 'failed'): ?>
                <div class="detail-row">
                    <span class="detail-label"><?php _e('Failure Reason:', 'kilismile-payments'); ?></span>
                    <span class="detail-value" style="color: #dc3545;">
                        <?php echo esc_html($transaction->failure_reason); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?php echo esc_url($admin_url . '&view=transaction&id=' . $transaction->id); ?>" class="btn btn-primary">
                    <?php _e('View Full Details', 'kilismile-payments'); ?>
                </a>
                
                <?php if ($transaction->status === 'failed'): ?>
                <a href="<?php echo esc_url($admin_url . '&action=retry&id=' . $transaction->id); ?>" class="btn btn-secondary">
                    <?php _e('Retry Payment', 'kilismile-payments'); ?>
                </a>
                <?php endif; ?>
                
                <a href="<?php echo esc_url($admin_url); ?>" class="btn btn-secondary">
                    <?php _e('View Dashboard', 'kilismile-payments'); ?>
                </a>
            </div>

            <?php endif; ?>

            <!-- Today's Statistics -->
            <div class="statistics">
                <h4><?php _e('Today\'s Activity', 'kilismile-payments'); ?></h4>
                <div class="stats-grid">
                    <?php
                    // Get today's stats (would be populated by actual data)
                    $today_stats = array(
                        'donations' => 5,  // Replace with actual data
                        'amount' => 1250,  // Replace with actual data
                        'failed' => 1      // Replace with actual data
                    );
                    ?>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo esc_html($today_stats['donations']); ?></span>
                        <span class="stat-label"><?php _e('Donations', 'kilismile-payments'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">$<?php echo esc_html(number_format($today_stats['amount'])); ?></span>
                        <span class="stat-label"><?php _e('Total Amount', 'kilismile-payments'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo esc_html($today_stats['failed']); ?></span>
                        <span class="stat-label"><?php _e('Failed', 'kilismile-payments'); ?></span>
                    </div>
                </div>
            </div>

            <?php if ($notification_type === 'new_donation' || $notification_type === 'high_value_donation'): ?>
            <p style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong><?php _e('Next Steps:', 'kilismile-payments'); ?></strong><br>
                <?php _e('Consider sending a personalized thank you message to the donor, especially for high-value donations. You may also want to invite them to learn more about specific programs their donation will support.', 'kilismile-payments'); ?>
            </p>
            <?php endif; ?>

            <?php if ($notification_type === 'payment_failed'): ?>
            <p style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong><?php _e('Action Required:', 'kilismile-payments'); ?></strong><br>
                <?php _e('Review the failure reason and consider reaching out to the donor if appropriate. Check if there are any technical issues that need to be resolved.', 'kilismile-payments'); ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <?php printf(__('This notification was sent from %s Payment System', 'kilismile-payments'), esc_html($organization_name)); ?><br>
                <a href="<?php echo esc_url($admin_url); ?>"><?php _e('Manage Payments', 'kilismile-payments'); ?></a> | 
                <a href="<?php echo esc_url(admin_url('admin.php?page=kilismile-payments-settings')); ?>"><?php _e('Notification Settings', 'kilismile-payments'); ?></a>
            </p>
            <p style="margin-top: 10px; font-size: 11px;">
                <?php _e('To stop receiving these notifications, please update your admin notification preferences.', 'kilismile-payments'); ?>
            </p>
        </div>
    </div>
</body>
</html>

