<?php
/**
 * Email Logs Admin Page
 * 
 * @package KiliSmile
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk actions
if (isset($_POST['action']) && $_POST['action'] === 'delete_logs' && isset($_POST['log_ids'])) {
    $ids = array_map('intval', $_POST['log_ids']);
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $ids));
        echo '<div class="notice notice-success"><p>' . __('Selected logs have been deleted.', 'kilismile') . '</p></div>';
    }
}

// Handle clear all logs
if (isset($_GET['action']) && $_GET['action'] === 'clear_all_logs' && wp_verify_nonce($_GET['_wpnonce'], 'clear_logs')) {
    $wpdb->query("TRUNCATE TABLE $table_name");
    echo '<div class="notice notice-success"><p>' . __('All email logs have been cleared.', 'kilismile') . '</p></div>';
}

// Filter options
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$type_filter = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$date_filter = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';

// Build WHERE clause
$where_clauses = array();
$where_values = array();

if ($status_filter) {
    $where_clauses[] = "status = %s";
    $where_values[] = $status_filter;
}

if ($type_filter) {
    $where_clauses[] = "email_type = %s";
    $where_values[] = $type_filter;
}

if ($date_filter) {
    $where_clauses[] = "DATE(sent_at) = %s";
    $where_values[] = $date_filter;
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Get statistics
$stats = $wpdb->get_row($wpdb->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
    FROM $table_name 
    $where_sql
", $where_values));

// Get filtered logs
$total_filtered = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name $where_sql", $where_values));

$logs_query = "SELECT * FROM $table_name $where_sql ORDER BY sent_at DESC LIMIT %d OFFSET %d";
$logs = $wpdb->get_results($wpdb->prepare($logs_query, array_merge($where_values, array($per_page, $offset))));

// Get unique email types and dates for filters
$email_types = $wpdb->get_col("SELECT DISTINCT email_type FROM $table_name ORDER BY email_type");
$recent_dates = $wpdb->get_col("SELECT DISTINCT DATE(sent_at) as date FROM $table_name ORDER BY date DESC LIMIT 30");

// Pagination
$total_pages = ceil($total_filtered / $per_page);
$pagination_args = array(
    'base' => add_query_arg('paged', '%#%'),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => $total_pages,
    'current' => $page
);
?>

<div class="wrap">
    <h1>
        <?php _e('Email Logs', 'kilismile'); ?>
        <a href="<?php echo add_query_arg(array('action' => 'clear_all_logs', '_wpnonce' => wp_create_nonce('clear_logs'))); ?>" 
           class="page-title-action" 
           onclick="return confirm('<?php _e('Are you sure you want to clear all email logs? This action cannot be undone.', 'kilismile'); ?>')">
            <?php _e('Clear All Logs', 'kilismile'); ?>
        </a>
    </h1>
    
    <!-- Statistics -->
    <div class="email-log-stats">
        <div class="stats-boxes">
            <div class="stat-box total">
                <h3><?php echo intval($stats->total); ?></h3>
                <p><?php _e('Total Emails', 'kilismile'); ?></p>
            </div>
            <div class="stat-box success">
                <h3><?php echo intval($stats->success); ?></h3>
                <p><?php _e('Successful', 'kilismile'); ?></p>
            </div>
            <div class="stat-box failed">
                <h3><?php echo intval($stats->failed); ?></h3>
                <p><?php _e('Failed', 'kilismile'); ?></p>
            </div>
            <div class="stat-box rate">
                <h3><?php echo $stats->total > 0 ? round(($stats->success / $stats->total) * 100, 1) : 0; ?>%</h3>
                <p><?php _e('Success Rate', 'kilismile'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="email-log-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="kilismile-email-logs">
            <input type="hidden" name="post_type" value="newsletter">
            
            <select name="status">
                <option value=""><?php _e('All Statuses', 'kilismile'); ?></option>
                <option value="success" <?php selected($status_filter, 'success'); ?>><?php _e('Success', 'kilismile'); ?></option>
                <option value="failed" <?php selected($status_filter, 'failed'); ?>><?php _e('Failed', 'kilismile'); ?></option>
                <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'kilismile'); ?></option>
            </select>
            
            <select name="type">
                <option value=""><?php _e('All Types', 'kilismile'); ?></option>
                <?php foreach ($email_types as $type) : ?>
                    <option value="<?php echo esc_attr($type); ?>" <?php selected($type_filter, $type); ?>>
                        <?php echo esc_html(ucwords(str_replace(array('-', '_'), ' ', $type))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="date">
                <option value=""><?php _e('All Dates', 'kilismile'); ?></option>
                <?php foreach ($recent_dates as $date) : ?>
                    <option value="<?php echo esc_attr($date); ?>" <?php selected($date_filter, $date); ?>>
                        <?php echo date('F j, Y', strtotime($date)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php submit_button(__('Filter', 'kilismile'), 'secondary', 'filter', false); ?>
            
            <?php if ($status_filter || $type_filter || $date_filter) : ?>
                <a href="<?php echo admin_url('edit.php?post_type=newsletter&page=kilismile-email-logs'); ?>" class="button">
                    <?php _e('Clear Filters', 'kilismile'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Logs Table -->
    <form method="post" action="">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="action">
                    <option value=""><?php _e('Bulk Actions', 'kilismile'); ?></option>
                    <option value="delete_logs"><?php _e('Delete', 'kilismile'); ?></option>
                </select>
                <input type="submit" class="button action" value="<?php _e('Apply', 'kilismile'); ?>">
            </div>
            
            <div class="alignright">
                <span class="displaying-num">
                    <?php printf(_n('%s item', '%s items', $total_filtered, 'kilismile'), number_format_i18n($total_filtered)); ?>
                </span>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" />
                    </td>
                    <th class="manage-column"><?php _e('Recipient', 'kilismile'); ?></th>
                    <th class="manage-column"><?php _e('Subject', 'kilismile'); ?></th>
                    <th class="manage-column"><?php _e('Type', 'kilismile'); ?></th>
                    <th class="manage-column"><?php _e('Status', 'kilismile'); ?></th>
                    <th class="manage-column"><?php _e('Date', 'kilismile'); ?></th>
                    <th class="manage-column"><?php _e('Actions', 'kilismile'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)) : ?>
                    <?php foreach ($logs as $log) : ?>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" name="log_ids[]" value="<?php echo $log->id; ?>" />
                            </th>
                            <td>
                                <strong><?php echo esc_html($log->recipient); ?></strong>
                                <?php if ($log->ip_address) : ?>
                                    <br><small class="description">IP: <?php echo esc_html($log->ip_address); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="email-subject">
                                    <?php echo esc_html($log->subject); ?>
                                </div>
                                <?php if ($log->error_message) : ?>
                                    <div class="email-error">
                                        <small class="description error-text">
                                            <?php echo esc_html($log->error_message); ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="email-type email-type-<?php echo esc_attr($log->email_type); ?>">
                                    <?php echo esc_html(ucwords(str_replace(array('-', '_'), ' ', $log->email_type))); ?>
                                </span>
                            </td>
                            <td>
                                <span class="email-status status-<?php echo esc_attr($log->status); ?>">
                                    <?php
                                    switch ($log->status) {
                                        case 'success':
                                            echo '<span class="dashicons dashicons-yes-alt"></span> ' . __('Success', 'kilismile');
                                            break;
                                        case 'failed':
                                            echo '<span class="dashicons dashicons-dismiss"></span> ' . __('Failed', 'kilismile');
                                            break;
                                        case 'pending':
                                            echo '<span class="dashicons dashicons-clock"></span> ' . __('Pending', 'kilismile');
                                            break;
                                        default:
                                            echo esc_html(ucfirst($log->status));
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo date('M j, Y', strtotime($log->sent_at)); ?></strong><br>
                                <small class="description"><?php echo date('g:i A', strtotime($log->sent_at)); ?></small>
                            </td>
                            <td>
                                <button type="button" class="button button-small view-details" 
                                        data-log-id="<?php echo $log->id; ?>">
                                    <?php _e('Details', 'kilismile'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="no-items">
                            <?php _e('No email logs found.', 'kilismile'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="action">
                    <option value=""><?php _e('Bulk Actions', 'kilismile'); ?></option>
                    <option value="delete_logs"><?php _e('Delete', 'kilismile'); ?></option>
                </select>
                <input type="submit" class="button action" value="<?php _e('Apply', 'kilismile'); ?>">
            </div>
            
            <?php if ($total_pages > 1) : ?>
                <div class="tablenav-pages">
                    <?php echo paginate_links($pagination_args); ?>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Email Log Details Modal -->
<div id="email-log-modal" class="email-log-modal" style="display: none;">
    <div class="email-log-modal-content">
        <div class="email-log-modal-header">
            <h3><?php _e('Email Log Details', 'kilismile'); ?></h3>
            <button type="button" class="email-log-modal-close">&times;</button>
        </div>
        <div class="email-log-modal-body">
            <div id="email-log-details-content">
                <?php _e('Loading...', 'kilismile'); ?>
            </div>
        </div>
    </div>
</div>

<style>
.email-log-stats {
    margin: 20px 0;
}

.stats-boxes {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-box {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    text-align: center;
    min-width: 120px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-box h3 {
    font-size: 32px;
    margin: 0 0 5px 0;
    line-height: 1;
}

.stat-box p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.stat-box.total h3 { color: #0073aa; }
.stat-box.success h3 { color: #46b450; }
.stat-box.failed h3 { color: #dc3232; }
.stat-box.rate h3 { color: #ff8c00; }

.email-log-filters {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    margin: 20px 0;
    border-radius: 4px;
}

.email-log-filters form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.email-log-filters select {
    min-width: 120px;
}

.email-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.status-success {
    color: #46b450;
}

.status-failed {
    color: #dc3232;
}

.status-pending {
    color: #ff8c00;
}

.email-type {
    background: #f0f0f1;
    color: #50575e;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.email-type-newsletter { background: #e1f5fe; color: #01579b; }
.email-type-welcome { background: #f3e5f5; color: #4a148c; }
.email-type-donation-confirmation { background: #e8f5e8; color: #1b5e20; }
.email-type-contact-form { background: #fff3e0; color: #e65100; }
.email-type-event-confirmation { background: #fce4ec; color: #880e4f; }

.email-subject {
    font-weight: 500;
}

.email-error {
    margin-top: 5px;
}

.error-text {
    color: #dc3232 !important;
}

.email-log-modal {
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.email-log-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.email-log-modal-header {
    padding: 20px;
    background: #f1f1f1;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.email-log-modal-header h3 {
    margin: 0;
}

.email-log-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.email-log-modal-close:hover {
    color: #000;
}

.email-log-modal-body {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.detail-row {
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.detail-value {
    color: #666;
    word-break: break-word;
}
</style>

<script>
jQuery(document).ready(function($) {
    // View details functionality
    $('.view-details').on('click', function() {
        var logId = $(this).data('log-id');
        var modal = $('#email-log-modal');
        var content = $('#email-log-details-content');
        
        content.html('<?php _e('Loading...', 'kilismile'); ?>');
        modal.show();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_email_log_details',
                log_id: logId,
                _wpnonce: '<?php echo wp_create_nonce('email_log_details'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    content.html(response.data);
                } else {
                    content.html('<p class="error"><?php _e('Failed to load log details.', 'kilismile'); ?></p>');
                }
            },
            error: function() {
                content.html('<p class="error"><?php _e('An error occurred while loading log details.', 'kilismile'); ?></p>');
            }
        });
    });
    
    // Close modal
    $('.email-log-modal-close, .email-log-modal').on('click', function(e) {
        if (e.target === this) {
            $('#email-log-modal').hide();
        }
    });
    
    // Bulk select functionality
    $('thead input[type="checkbox"]').on('change', function() {
        $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
    });
});
</script>

<?php
// AJAX handler for log details
add_action('wp_ajax_get_email_log_details', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Unauthorized', 'kilismile'));
    }
    
    if (!wp_verify_nonce($_POST['_wpnonce'], 'email_log_details')) {
        wp_send_json_error(__('Invalid nonce', 'kilismile'));
    }
    
    global $wpdb;
    $log_id = intval($_POST['log_id']);
    $table_name = $wpdb->prefix . 'kilismile_email_log';
    
    $log = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $log_id));
    
    if (!$log) {
        wp_send_json_error(__('Log not found', 'kilismile'));
    }
    
    ob_start();
    ?>
    <div class="detail-row">
        <div class="detail-label"><?php _e('Recipient:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo esc_html($log->recipient); ?></div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label"><?php _e('Subject:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo esc_html($log->subject); ?></div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label"><?php _e('Email Type:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo esc_html(ucwords(str_replace(array('-', '_'), ' ', $log->email_type))); ?></div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label"><?php _e('Status:', 'kilismile'); ?></div>
        <div class="detail-value">
            <span class="status-<?php echo esc_attr($log->status); ?>">
                <?php echo esc_html(ucfirst($log->status)); ?>
            </span>
        </div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label"><?php _e('Date & Time:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo date('F j, Y g:i:s A', strtotime($log->sent_at)); ?></div>
    </div>
    
    <?php if ($log->ip_address) : ?>
    <div class="detail-row">
        <div class="detail-label"><?php _e('IP Address:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo esc_html($log->ip_address); ?></div>
    </div>
    <?php endif; ?>
    
    <?php if ($log->user_agent) : ?>
    <div class="detail-row">
        <div class="detail-label"><?php _e('User Agent:', 'kilismile'); ?></div>
        <div class="detail-value"><?php echo esc_html($log->user_agent); ?></div>
    </div>
    <?php endif; ?>
    
    <?php if ($log->error_message) : ?>
    <div class="detail-row">
        <div class="detail-label"><?php _e('Error Message:', 'kilismile'); ?></div>
        <div class="detail-value error-text"><?php echo esc_html($log->error_message); ?></div>
    </div>
    <?php endif; ?>
    
    <?php
    $content = ob_get_clean();
    wp_send_json_success($content);
});
?>


