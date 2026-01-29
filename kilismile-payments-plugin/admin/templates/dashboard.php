<?php
/**
 * Admin Dashboard Template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="kilismile-payments-dashboard">
        
        <!-- Statistics Cards -->
        <div class="dashboard-stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-money-alt"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_amount'] ?? 0, 2); ?></h3>
                        <p><?php _e('Total Revenue (30 days)', 'kilismile-payments'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo intval($stats['successful_transactions'] ?? 0); ?></h3>
                        <p><?php _e('Successful Transactions', 'kilismile-payments'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-warning"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo intval($stats['failed_transactions'] ?? 0); ?></h3>
                        <p><?php _e('Failed Transactions', 'kilismile-payments'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-performance"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['success_rate'] ?? 0, 1); ?>%</h3>
                        <p><?php _e('Success Rate', 'kilismile-payments'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gateway Status -->
        <div class="dashboard-section">
            <h2><?php _e('Gateway Status', 'kilismile-payments'); ?></h2>
            <div class="gateway-status-grid">
                <?php foreach ($gateways as $gateway_id => $gateway): ?>
                    <?php $status = $gateway->get_configuration_status(); ?>
                    <div class="gateway-status-card status-<?php echo esc_attr($status['status']); ?>">
                        <div class="gateway-info">
                            <h4><?php echo esc_html($gateway->get_title()); ?></h4>
                            <p><?php echo esc_html($status['message']); ?></p>
                        </div>
                        <div class="gateway-actions">
                            <?php if ($status['status'] === 'incomplete'): ?>
                                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-gateways#' . $gateway_id); ?>" class="button button-primary">
                                    <?php _e('Configure', 'kilismile-payments'); ?>
                                </a>
                            <?php elseif ($status['status'] === 'active'): ?>
                                <button type="button" class="button test-gateway" data-gateway="<?php echo esc_attr($gateway_id); ?>">
                                    <?php _e('Test Connection', 'kilismile-payments'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><?php _e('Recent Transactions', 'kilismile-payments'); ?></h2>
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions'); ?>" class="button">
                    <?php _e('View All', 'kilismile-payments'); ?>
                </a>
            </div>
            
            <?php if (!empty($recent_transactions)): ?>
                <div class="transactions-table">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'kilismile-payments'); ?></th>
                                <th><?php _e('Gateway', 'kilismile-payments'); ?></th>
                                <th><?php _e('Amount', 'kilismile-payments'); ?></th>
                                <th><?php _e('Donor', 'kilismile-payments'); ?></th>
                                <th><?php _e('Status', 'kilismile-payments'); ?></th>
                                <th><?php _e('Date', 'kilismile-payments'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo esc_html($transaction['id']); ?></td>
                                    <td>
                                        <span class="gateway-badge gateway-<?php echo esc_attr($transaction['gateway']); ?>">
                                            <?php echo esc_html(ucfirst($transaction['gateway'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo esc_html($transaction['currency']); ?> 
                                        <?php echo number_format($transaction['amount'], 2); ?>
                                    </td>
                                    <td>
                                        <div class="donor-info">
                                            <strong><?php echo esc_html($transaction['donor_name']); ?></strong>
                                            <br>
                                            <small><?php echo esc_html($transaction['donor_email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($transaction['status']); ?>">
                                            <?php echo esc_html(ucfirst($transaction['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo esc_html(date('M j, Y g:i A', strtotime($transaction['created_at']))); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-transactions">
                    <p><?php _e('No transactions found.', 'kilismile-payments'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <h2><?php _e('Quick Actions', 'kilismile-payments'); ?></h2>
            <div class="quick-actions">
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-gateways'); ?>" class="action-card">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <h4><?php _e('Configure Gateways', 'kilismile-payments'); ?></h4>
                    <p><?php _e('Set up payment gateway credentials and settings', 'kilismile-payments'); ?></p>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions'); ?>" class="action-card">
                    <span class="dashicons dashicons-list-view"></span>
                    <h4><?php _e('View Transactions', 'kilismile-payments'); ?></h4>
                    <p><?php _e('Browse and manage all payment transactions', 'kilismile-payments'); ?></p>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-logs'); ?>" class="action-card">
                    <span class="dashicons dashicons-text-page"></span>
                    <h4><?php _e('View Logs', 'kilismile-payments'); ?></h4>
                    <p><?php _e('Monitor system logs and debug issues', 'kilismile-payments'); ?></p>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-settings'); ?>" class="action-card">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <h4><?php _e('General Settings', 'kilismile-payments'); ?></h4>
                    <p><?php _e('Configure global payment settings', 'kilismile-payments'); ?></p>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Test gateway connection
    $('.test-gateway').on('click', function() {
        var button = $(this);
        var gatewayId = button.data('gateway');
        
        button.prop('disabled', true).text(kilismile_payments_admin.strings.testing_connection);
        
        $.post(kilismile_payments_admin.ajax_url, {
            action: 'kilismile_test_gateway',
            gateway_id: gatewayId,
            nonce: kilismile_payments_admin.nonce
        }, function(response) {
            if (response.success) {
                button.text(kilismile_payments_admin.strings.connection_successful)
                      .removeClass('button').addClass('button button-primary');
                setTimeout(function() {
                    button.text('Test Connection').removeClass('button-primary').addClass('button').prop('disabled', false);
                }, 3000);
            } else {
                button.text(kilismile_payments_admin.strings.connection_failed)
                      .removeClass('button').addClass('button button-secondary');
                setTimeout(function() {
                    button.text('Test Connection').removeClass('button-secondary').addClass('button').prop('disabled', false);
                }, 3000);
            }
        });
    });
});
</script>

