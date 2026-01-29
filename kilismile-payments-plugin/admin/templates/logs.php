<?php
/**
 * KiliSmile Payments - Logs Viewing Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get logger instance
$logger = KiliSmile_Payments_Plugin::get_instance()->get_logger();

// Handle log actions
if (isset($_GET['action'])) {
    $action = sanitize_text_field($_GET['action']);
    
    switch ($action) {
        case 'clear':
            if (wp_verify_nonce($_GET['_wpnonce'], 'clear_logs')) {
                $logger->clear_logs();
                echo '<div class="notice notice-success"><p>' . __('Logs cleared successfully.', 'kilismile-payments') . '</p></div>';
            }
            break;
            
        case 'download':
            if (wp_verify_nonce($_GET['_wpnonce'], 'download_logs')) {
                $logger->download_logs();
                exit;
            }
            break;
    }
}

// Get filter parameters
$level_filter = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : '';
$gateway_filter = isset($_GET['gateway']) ? sanitize_text_field($_GET['gateway']) : '';
$date_filter = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Pagination
$per_page = 50;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Get logs with filters
$filters = array();
if ($level_filter) $filters['level'] = $level_filter;
if ($gateway_filter) $filters['gateway'] = $gateway_filter;
if ($search) $filters['search'] = $search;
if ($date_filter) $filters['date_range'] = $date_filter;

$logs = $logger->get_logs($filters, $per_page, $offset);
$total_logs = $logger->get_logs_count($filters);

// Calculate pagination
$total_pages = ceil($total_logs / $per_page);

// Get available gateways for filter
$gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();

// Log levels
$log_levels = array(
    'error' => __('Error', 'kilismile-payments'),
    'warning' => __('Warning', 'kilismile-payments'),
    'info' => __('Info', 'kilismile-payments'),
    'debug' => __('Debug', 'kilismile-payments')
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Payment Logs', 'kilismile-payments'); ?></h1>
    
    <!-- Page Actions -->
    <div class="page-title-action">
        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=kilismile-payments-logs&action=download'), 'download_logs'); ?>" class="page-title-action">
            <?php _e('Download Logs', 'kilismile-payments'); ?>
        </a>
        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=kilismile-payments-logs&action=clear'), 'clear_logs'); ?>" 
           class="page-title-action" 
           onclick="return confirm('<?php esc_attr_e('Are you sure you want to clear all logs? This action cannot be undone.', 'kilismile-payments'); ?>');">
            <?php _e('Clear Logs', 'kilismile-payments'); ?>
        </a>
    </div>
    
    <hr class="wp-header-end">

    <!-- Filters -->
    <div class="tablenav top">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="kilismile-payments-logs">
            
            <div class="alignleft actions">
                <!-- Level Filter -->
                <select name="level" class="postform">
                    <option value=""><?php _e('All Levels', 'kilismile-payments'); ?></option>
                    <?php foreach ($log_levels as $level => $label): ?>
                    <option value="<?php echo esc_attr($level); ?>" <?php selected($level_filter, $level); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Gateway Filter -->
                <select name="gateway" class="postform">
                    <option value=""><?php _e('All Gateways', 'kilismile-payments'); ?></option>
                    <?php foreach ($gateways as $gateway_id => $gateway): ?>
                    <option value="<?php echo esc_attr($gateway_id); ?>" <?php selected($gateway_filter, $gateway_id); ?>>
                        <?php echo esc_html($gateway['title']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Date Filter -->
                <select name="date_range" class="postform">
                    <option value=""><?php _e('All Dates', 'kilismile-payments'); ?></option>
                    <option value="today" <?php selected($date_filter, 'today'); ?>><?php _e('Today', 'kilismile-payments'); ?></option>
                    <option value="yesterday" <?php selected($date_filter, 'yesterday'); ?>><?php _e('Yesterday', 'kilismile-payments'); ?></option>
                    <option value="week" <?php selected($date_filter, 'week'); ?>><?php _e('Last 7 Days', 'kilismile-payments'); ?></option>
                    <option value="month" <?php selected($date_filter, 'month'); ?>><?php _e('Last 30 Days', 'kilismile-payments'); ?></option>
                </select>
                
                <?php submit_button(__('Filter', 'kilismile-payments'), 'secondary', 'filter_action', false); ?>
                
                <?php if ($level_filter || $gateway_filter || $date_filter || $search): ?>
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-logs'); ?>" class="button">
                    <?php _e('Clear Filters', 'kilismile-payments'); ?>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Search -->
            <div class="alignright">
                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" 
                       placeholder="<?php _e('Search logs...', 'kilismile-payments'); ?>">
                <?php submit_button(__('Search', 'kilismile-payments'), 'secondary', 'search_submit', false); ?>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <table class="wp-list-table widefat fixed striped logs">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-timestamp">
                    <?php _e('Timestamp', 'kilismile-payments'); ?>
                </th>
                <th scope="col" class="manage-column column-level">
                    <?php _e('Level', 'kilismile-payments'); ?>
                </th>
                <th scope="col" class="manage-column column-gateway">
                    <?php _e('Gateway', 'kilismile-payments'); ?>
                </th>
                <th scope="col" class="manage-column column-message">
                    <?php _e('Message', 'kilismile-payments'); ?>
                </th>
                <th scope="col" class="manage-column column-context">
                    <?php _e('Context', 'kilismile-payments'); ?>
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($logs)): ?>
            <tr class="no-items">
                <td class="colspanchange" colspan="5">
                    <?php _e('No logs found.', 'kilismile-payments'); ?>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr class="log-entry log-level-<?php echo esc_attr($log->level); ?>">
                    <td class="column-timestamp">
                        <span title="<?php echo esc_attr(date_i18n('Y-m-d H:i:s', strtotime($log->timestamp))); ?>">
                            <?php echo esc_html(human_time_diff(strtotime($log->timestamp), current_time('timestamp')) . ' ago'); ?>
                        </span>
                    </td>
                    
                    <td class="column-level">
                        <span class="log-level-badge level-<?php echo esc_attr($log->level); ?>">
                            <?php echo esc_html(strtoupper($log->level)); ?>
                        </span>
                    </td>
                    
                    <td class="column-gateway">
                        <?php if ($log->gateway): ?>
                        <span class="gateway-badge">
                            <?php echo esc_html(ucfirst(str_replace('_', ' ', $log->gateway))); ?>
                        </span>
                        <?php else: ?>
                        <span class="no-gateway">—</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-message">
                        <div class="log-message">
                            <?php echo esc_html($log->message); ?>
                        </div>
                        
                        <?php if (!empty($log->transaction_id)): ?>
                        <div class="log-meta">
                            <strong><?php _e('Transaction:', 'kilismile-payments'); ?></strong>
                            <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions&action=view&id=' . $log->transaction_id); ?>">
                                #<?php echo esc_html($log->transaction_id); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </td>
                    
                    <td class="column-context">
                        <?php if (!empty($log->context)): ?>
                        <button type="button" class="button button-small toggle-context" data-log-id="<?php echo esc_attr($log->id); ?>">
                            <?php _e('Show Context', 'kilismile-payments'); ?>
                        </button>
                        <div class="log-context" id="context-<?php echo esc_attr($log->id); ?>" style="display: none;">
                            <pre><?php echo esc_html(json_encode(json_decode($log->context), JSON_PRETTY_PRINT)); ?></pre>
                        </div>
                        <?php else: ?>
                        <span class="no-context">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php printf(_n('%s item', '%s items', $total_logs, 'kilismile-payments'), number_format_i18n($total_logs)); ?>
            </span>
            
            <?php
            $page_links = paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $current_page,
                'type' => 'array'
            ));
            
            if ($page_links) {
                echo '<span class="pagination-links">' . implode('', $page_links) . '</span>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Log Statistics -->
    <div class="log-stats-widget">
        <h3><?php _e('Log Statistics', 'kilismile-payments'); ?></h3>
        <div class="stats-grid">
            <?php
            // Get log statistics
            $stats = $logger->get_log_statistics();
            ?>
            <div class="stat-card error">
                <div class="stat-number"><?php echo number_format_i18n($stats['error'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Errors', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-number"><?php echo number_format_i18n($stats['warning'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Warnings', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-number"><?php echo number_format_i18n($stats['info'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Info', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card debug">
                <div class="stat-number"><?php echo number_format_i18n($stats['debug'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Debug', 'kilismile-payments'); ?></div>
            </div>
        </div>
    </div>

    <!-- Log Level Configuration -->
    <div class="log-config-widget">
        <h3><?php _e('Log Configuration', 'kilismile-payments'); ?></h3>
        <form method="post" action="options.php">
            <?php settings_fields('kilismile_payments_logs'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Log Level', 'kilismile-payments'); ?></th>
                    <td>
                        <select name="kilismile_payments_log_level">
                            <?php $current_level = get_option('kilismile_payments_log_level', 'info'); ?>
                            <option value="error" <?php selected($current_level, 'error'); ?>><?php _e('Error Only', 'kilismile-payments'); ?></option>
                            <option value="warning" <?php selected($current_level, 'warning'); ?>><?php _e('Warning & Above', 'kilismile-payments'); ?></option>
                            <option value="info" <?php selected($current_level, 'info'); ?>><?php _e('Info & Above', 'kilismile-payments'); ?></option>
                            <option value="debug" <?php selected($current_level, 'debug'); ?>><?php _e('All Levels', 'kilismile-payments'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select the minimum log level to record.', 'kilismile-payments'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Log Retention', 'kilismile-payments'); ?></th>
                    <td>
                        <input type="number" name="kilismile_payments_log_retention" 
                               value="<?php echo esc_attr(get_option('kilismile_payments_log_retention', 30)); ?>" 
                               min="1" max="365" class="small-text"> 
                        <?php _e('days', 'kilismile-payments'); ?>
                        <p class="description"><?php _e('Number of days to keep log entries before automatic cleanup.', 'kilismile-payments'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Email Notifications', 'kilismile-payments'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="kilismile_payments_log_email_errors" value="1" 
                                   <?php checked(get_option('kilismile_payments_log_email_errors', 0)); ?>>
                            <?php _e('Email admin when errors occur', 'kilismile-payments'); ?>
                        </label>
                        <br>
                        <input type="email" name="kilismile_payments_log_email_address" 
                               value="<?php echo esc_attr(get_option('kilismile_payments_log_email_address', get_option('admin_email'))); ?>" 
                               placeholder="<?php esc_attr_e('Email address', 'kilismile-payments'); ?>" class="regular-text">
                        <p class="description"><?php _e('Email address to receive error notifications.', 'kilismile-payments'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Save Configuration', 'kilismile-payments')); ?>
        </form>
    </div>
</div>

<style>
.log-stats-widget,
.log-config-widget {
    margin-top: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-card {
    text-align: center;
    padding: 15px;
    border-radius: 4px;
}

.stat-card.error {
    background: #fee;
    color: #d63384;
}

.stat-card.warning {
    background: #fff3cd;
    color: #856404;
}

.stat-card.info {
    background: #d1ecf1;
    color: #0c5460;
}

.stat-card.debug {
    background: #e2e3e5;
    color: #383d41;
}

.stat-number {
    font-size: 24px;
    font-weight: 600;
}

.stat-label {
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 500;
    margin-top: 5px;
}

.log-level-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.level-error {
    background: #f8d7da;
    color: #721c24;
}

.level-warning {
    background: #fff3cd;
    color: #856404;
}

.level-info {
    background: #d1ecf1;
    color: #0c5460;
}

.level-debug {
    background: #e2e3e5;
    color: #383d41;
}

.gateway-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 500;
    background: #f0f0f1;
    color: #50575e;
}

.log-message {
    font-weight: 500;
    margin-bottom: 5px;
}

.log-meta {
    font-size: 12px;
    color: #646970;
}

.log-context pre {
    background: #f6f7f7;
    padding: 10px;
    border-radius: 3px;
    font-size: 11px;
    white-space: pre-wrap;
    word-break: break-word;
    max-height: 200px;
    overflow: auto;
    margin: 10px 0 0 0;
}

.toggle-context {
    font-size: 11px;
}

.no-gateway,
.no-context {
    color: #646970;
    font-style: italic;
}

.log-entry.log-level-error {
    background-color: #fef2f2;
}

.log-entry.log-level-warning {
    background-color: #fffbf0;
}

.search-form {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.search-form .alignleft {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.search-form .alignright {
    display: flex;
    align-items: center;
    gap: 5px;
}

@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form .alignleft,
    .search-form .alignright {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle context display
    $('.toggle-context').on('click', function() {
        var logId = $(this).data('log-id');
        var context = $('#context-' + logId);
        
        if (context.is(':visible')) {
            context.hide();
            $(this).text('<?php esc_js(_e('Show Context', 'kilismile-payments')); ?>');
        } else {
            context.show();
            $(this).text('<?php esc_js(_e('Hide Context', 'kilismile-payments')); ?>');
        }
    });
    
    // Auto-refresh every 30 seconds for new logs
    setTimeout(function() {
        // Only refresh if on first page and no filters applied
        var urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('paged') && !urlParams.has('level') && !urlParams.has('gateway') && !urlParams.has('s')) {
            location.reload();
        }
    }, 30000);
    
    // Highlight recent error logs
    $('.log-level-error').each(function() {
        var timestamp = $(this).find('.column-timestamp span').attr('title');
        var logTime = new Date(timestamp);
        var now = new Date();
        var diffMinutes = (now - logTime) / (1000 * 60);
        
        if (diffMinutes <= 5) {
            $(this).addClass('recent-error');
        }
    });
});
</script>

<style>
.recent-error {
    animation: highlight-error 2s ease-in-out;
    border-left: 4px solid #d63384;
}

@keyframes highlight-error {
    0% { background-color: #f8d7da; }
    100% { background-color: inherit; }
}
</style>

