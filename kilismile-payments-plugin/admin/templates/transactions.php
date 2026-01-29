<?php
/**
 * KiliSmile Payments - Transactions Management Template
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get database instance
$database = KiliSmile_Payments_Plugin::get_instance()->get_database();

// Handle bulk actions
if (isset($_POST['action']) && $_POST['action'] !== '-1' && isset($_POST['transaction']) && !empty($_POST['transaction'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-transactions')) {
        wp_die(__('Security check failed', 'kilismile-payments'));
    }
    
    $action = sanitize_text_field($_POST['action']);
    $transaction_ids = array_map('sanitize_text_field', $_POST['transaction']);
    
    switch ($action) {
        case 'delete':
            foreach ($transaction_ids as $id) {
                $database->delete_transaction($id);
            }
            echo '<div class="notice notice-success"><p>' . sprintf(_n('%d transaction deleted.', '%d transactions deleted.', count($transaction_ids), 'kilismile-payments'), count($transaction_ids)) . '</p></div>';
            break;
            
        case 'retry':
            foreach ($transaction_ids as $id) {
                // Retry failed transactions
                $transaction = $database->get_transaction($id);
                if ($transaction && $transaction->status === 'failed') {
                    // Mark for retry
                    $database->update_transaction($id, array('status' => 'pending'));
                }
            }
            echo '<div class="notice notice-success"><p>' . sprintf(_n('%d transaction marked for retry.', '%d transactions marked for retry.', count($transaction_ids), 'kilismile-payments'), count($transaction_ids)) . '</p></div>';
            break;
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$gateway_filter = isset($_GET['gateway']) ? sanitize_text_field($_GET['gateway']) : '';
$date_filter = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Pagination
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Get transactions with filters
$where_conditions = array();
$where_values = array();

if ($status_filter) {
    $where_conditions[] = 'status = %s';
    $where_values[] = $status_filter;
}

if ($gateway_filter) {
    $where_conditions[] = 'gateway = %s';
    $where_values[] = $gateway_filter;
}

if ($search) {
    $where_conditions[] = '(donor_name LIKE %s OR donor_email LIKE %s OR id LIKE %s)';
    $search_term = '%' . $search . '%';
    $where_values[] = $search_term;
    $where_values[] = $search_term;
    $where_values[] = $search_term;
}

if ($date_filter) {
    switch ($date_filter) {
        case 'today':
            $where_conditions[] = 'DATE(created_at) = CURDATE()';
            break;
        case 'yesterday':
            $where_conditions[] = 'DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            break;
        case 'week':
            $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
            break;
        case 'month':
            $where_conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
            break;
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get transactions
$transactions = $database->get_transactions_with_filters($where_clause, $where_values, $per_page, $offset);
$total_transactions = $database->get_transactions_count($where_clause, $where_values);

// Calculate pagination
$total_pages = ceil($total_transactions / $per_page);

// Get available gateways for filter
$gateways = KiliSmile_Payments_Plugin::get_instance()->get_available_gateways();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Transactions', 'kilismile-payments'); ?></h1>
    
    <!-- Page Actions -->
    <div class="page-title-action">
        <a href="<?php echo admin_url('admin.php?page=kilismile-payments-export'); ?>" class="page-title-action">
            <?php _e('Export Transactions', 'kilismile-payments'); ?>
        </a>
    </div>
    
    <hr class="wp-header-end">

    <!-- Filters -->
    <div class="tablenav top">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="kilismile-payments-transactions">
            
            <div class="alignleft actions">
                <!-- Status Filter -->
                <select name="status" class="postform">
                    <option value=""><?php _e('All Statuses', 'kilismile-payments'); ?></option>
                    <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php _e('Completed', 'kilismile-payments'); ?></option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'kilismile-payments'); ?></option>
                    <option value="failed" <?php selected($status_filter, 'failed'); ?>><?php _e('Failed', 'kilismile-payments'); ?></option>
                    <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php _e('Cancelled', 'kilismile-payments'); ?></option>
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
                
                <?php if ($status_filter || $gateway_filter || $date_filter || $search): ?>
                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions'); ?>" class="button">
                    <?php _e('Clear Filters', 'kilismile-payments'); ?>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Search -->
            <div class="alignright">
                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" 
                       placeholder="<?php _e('Search transactions...', 'kilismile-payments'); ?>">
                <?php submit_button(__('Search', 'kilismile-payments'), 'secondary', 'search_submit', false); ?>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <form method="post" id="transactions-filter">
        <?php wp_nonce_field('bulk-transactions'); ?>
        
        <table class="wp-list-table widefat fixed striped transactions">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-1">
                    </td>
                    <th scope="col" class="manage-column column-id sortable">
                        <a href="#"><span><?php _e('ID', 'kilismile-payments'); ?></span></a>
                    </th>
                    <th scope="col" class="manage-column column-donor">
                        <?php _e('Donor', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-amount sortable">
                        <a href="#"><span><?php _e('Amount', 'kilismile-payments'); ?></span></a>
                    </th>
                    <th scope="col" class="manage-column column-gateway">
                        <?php _e('Gateway', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-status">
                        <?php _e('Status', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-date sortable">
                        <a href="#"><span><?php _e('Date', 'kilismile-payments'); ?></span></a>
                    </th>
                    <th scope="col" class="manage-column column-actions">
                        <?php _e('Actions', 'kilismile-payments'); ?>
                    </th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (empty($transactions)): ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="8">
                        <?php _e('No transactions found.', 'kilismile-payments'); ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="transaction[]" value="<?php echo esc_attr($transaction->id); ?>">
                        </th>
                        
                        <td class="column-id">
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions&action=view&id=' . $transaction->id); ?>">
                                    #<?php echo esc_html($transaction->id); ?>
                                </a>
                            </strong>
                        </td>
                        
                        <td class="column-donor">
                            <div class="donor-info">
                                <strong><?php echo esc_html($transaction->donor_name ?: __('Anonymous', 'kilismile-payments')); ?></strong>
                                <?php if ($transaction->donor_email): ?>
                                <br><a href="mailto:<?php echo esc_attr($transaction->donor_email); ?>">
                                    <?php echo esc_html($transaction->donor_email); ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="column-amount">
                            <span class="amount">
                                <?php 
                                $symbol = $transaction->currency === 'USD' ? '$' : 'TSh ';
                                echo esc_html($symbol . number_format($transaction->amount, 2));
                                ?>
                            </span>
                            <br><small class="currency"><?php echo esc_html($transaction->currency); ?></small>
                        </td>
                        
                        <td class="column-gateway">
                            <span class="gateway-badge gateway-<?php echo esc_attr($transaction->gateway); ?>">
                                <?php echo esc_html(ucfirst(str_replace('_', ' ', $transaction->gateway))); ?>
                            </span>
                        </td>
                        
                        <td class="column-status">
                            <span class="status-badge status-<?php echo esc_attr($transaction->status); ?>">
                                <?php echo esc_html(ucfirst($transaction->status)); ?>
                            </span>
                        </td>
                        
                        <td class="column-date">
                            <abbr title="<?php echo esc_attr(date_i18n('Y/m/d g:i:s A', strtotime($transaction->created_at))); ?>">
                                <?php echo esc_html(human_time_diff(strtotime($transaction->created_at), current_time('timestamp')) . ' ago'); ?>
                            </abbr>
                        </td>
                        
                        <td class="column-actions">
                            <div class="row-actions">
                                <span class="view">
                                    <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions&action=view&id=' . $transaction->id); ?>">
                                        <?php _e('View', 'kilismile-payments'); ?>
                                    </a>
                                </span>
                                
                                <?php if ($transaction->status === 'failed'): ?>
                                | <span class="retry">
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=kilismile-payments-transactions&action=retry&id=' . $transaction->id), 'retry_transaction_' . $transaction->id); ?>">
                                        <?php _e('Retry', 'kilismile-payments'); ?>
                                    </a>
                                </span>
                                <?php endif; ?>
                                
                                | <span class="receipt">
                                    <a href="<?php echo admin_url('admin.php?page=kilismile-payments-transactions&action=receipt&id=' . $transaction->id); ?>" target="_blank">
                                        <?php _e('Receipt', 'kilismile-payments'); ?>
                                    </a>
                                </span>
                                
                                | <span class="delete">
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=kilismile-payments-transactions&action=delete&id=' . $transaction->id), 'delete_transaction_' . $transaction->id); ?>" 
                                       onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this transaction?', 'kilismile-payments'); ?>');" class="submitdelete">
                                        <?php _e('Delete', 'kilismile-payments'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            
            <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-2">
                    </td>
                    <th scope="col" class="manage-column column-id">
                        <?php _e('ID', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-donor">
                        <?php _e('Donor', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-amount">
                        <?php _e('Amount', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-gateway">
                        <?php _e('Gateway', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-status">
                        <?php _e('Status', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-date">
                        <?php _e('Date', 'kilismile-payments'); ?>
                    </th>
                    <th scope="col" class="manage-column column-actions">
                        <?php _e('Actions', 'kilismile-payments'); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
        
        <!-- Bulk Actions -->
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="action" id="bulk-action-selector-bottom">
                    <option value="-1"><?php _e('Bulk actions', 'kilismile-payments'); ?></option>
                    <option value="delete"><?php _e('Delete', 'kilismile-payments'); ?></option>
                    <option value="retry"><?php _e('Retry Failed', 'kilismile-payments'); ?></option>
                </select>
                <?php submit_button(__('Apply', 'kilismile-payments'), 'action', 'doaction2', false); ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php printf(_n('%s item', '%s items', $total_transactions, 'kilismile-payments'), number_format_i18n($total_transactions)); ?>
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
            <?php endif; ?>
        </div>
    </form>

    <!-- Transaction Statistics -->
    <div class="transaction-stats-widget">
        <h3><?php _e('Transaction Statistics', 'kilismile-payments'); ?></h3>
        <div class="stats-grid">
            <?php
            // Get statistics
            $stats = $database->get_transaction_statistics();
            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format_i18n($stats['total_transactions'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Total Transactions', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($stats['total_amount'] ?? 0, 2); ?></div>
                <div class="stat-label"><?php _e('Total Amount', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format_i18n($stats['completed_transactions'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Completed', 'kilismile-payments'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format_i18n($stats['failed_transactions'] ?? 0); ?></div>
                <div class="stat-label"><?php _e('Failed', 'kilismile-payments'); ?></div>
            </div>
        </div>
    </div>
</div>

<style>
.transaction-stats-widget {
    margin-top: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-card {
    text-align: center;
    padding: 15px;
    background: #f6f7f7;
    border-radius: 4px;
}

.stat-number {
    font-size: 24px;
    font-weight: 600;
    color: #1d2327;
}

.stat-label {
    font-size: 12px;
    color: #646970;
    text-transform: uppercase;
    font-weight: 500;
    margin-top: 5px;
}

.status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.status-cancelled {
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

.donor-info strong {
    display: block;
}

.amount {
    font-weight: 600;
    color: #1d2327;
}

.currency {
    color: #646970;
    font-size: 11px;
    text-transform: uppercase;
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
    // Select all checkboxes
    $('#cb-select-all-1, #cb-select-all-2').on('change', function() {
        var checked = $(this).prop('checked');
        $('input[name="transaction[]"]').prop('checked', checked);
    });
    
    // Individual checkbox change
    $('input[name="transaction[]"]').on('change', function() {
        var totalCheckboxes = $('input[name="transaction[]"]').length;
        var checkedCheckboxes = $('input[name="transaction[]"]:checked').length;
        
        $('#cb-select-all-1, #cb-select-all-2').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
    
    // Auto-refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
});
</script>

