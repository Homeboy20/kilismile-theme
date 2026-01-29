<?php
/**
 * Donation Management Dashboard
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * KiliSmile Donation Dashboard Class
 */
class KiliSmile_Donation_Dashboard {
    
    /**
     * Initialize the dashboard
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        add_menu_page(
            __('Donations', 'kilismile'),
            __('Donations', 'kilismile'),
            'manage_options',
            'kilismile-donations',
            array($this, 'donations_page'),
            'dashicons-heart',
            30
        );
        
        add_submenu_page(
            'kilismile-donations',
            __('All Donations', 'kilismile'),
            __('All Donations', 'kilismile'),
            'manage_options',
            'kilismile-donations',
            array($this, 'donations_page')
        );
        
        add_submenu_page(
            'kilismile-donations',
            __('Donation Reports', 'kilismile'),
            __('Reports', 'kilismile'),
            'manage_options',
            'kilismile-donation-reports',
            array($this, 'reports_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'kilismile-donation') !== false) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        }
    }
    
    /**
     * Donations management page
     */
    public function donations_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        // Handle actions
        if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
            $donation_id = intval($_POST['donation_id']);
            $new_status = sanitize_text_field($_POST['new_status']);
            
            $wpdb->update(
                $table_name,
                array('payment_status' => $new_status),
                array('id' => $donation_id),
                array('%s'),
                array('%d')
            );
            
            echo '<div class="notice notice-success"><p>' . __('Donation status updated.', 'kilismile') . '</p></div>';
        }
        
        // Get donations with pagination
        $per_page = 20;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;
        
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        $where_conditions = array();
        $where_values = array();
        
        if (!empty($search)) {
            $where_conditions[] = "(donor_name LIKE %s OR donor_email LIKE %s OR transaction_id LIKE %s)";
            $where_values[] = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = '%' . $wpdb->esc_like($search) . '%';
        }
        
        if (!empty($status_filter)) {
            $where_conditions[] = "payment_status = %s";
            $where_values[] = $status_filter;
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $donations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d",
            array_merge($where_values, array($per_page, $offset))
        ));
        
        $total_donations = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name $where_clause",
            $where_values
        ));
        
        $total_pages = ceil($total_donations / $per_page);
        
        // Get statistics
        $stats = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount,
                SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_count
            FROM $table_name"
        );
        ?>
        
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Donations', 'kilismile'); ?></h1>
            
            <!-- Statistics Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="postbox" style="padding: 20px; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; color: #28a745;"><?php _e('Total Raised', 'kilismile'); ?></h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #28a745;">
                        $<?php echo number_format($stats->total_amount, 2); ?>
                    </div>
                </div>
                
                <div class="postbox" style="padding: 20px; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; color: #007cba;"><?php _e('Total Donations', 'kilismile'); ?></h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #007cba;">
                        <?php echo number_format($stats->total_count); ?>
                    </div>
                </div>
                
                <div class="postbox" style="padding: 20px; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; color: #28a745;"><?php _e('Completed', 'kilismile'); ?></h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #28a745;">
                        <?php echo number_format($stats->completed_count); ?>
                    </div>
                </div>
                
                <div class="postbox" style="padding: 20px; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; color: #ffc107;"><?php _e('Pending', 'kilismile'); ?></h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #ffc107;">
                        <?php echo number_format($stats->pending_count); ?>
                    </div>
                </div>
                
                <div class="postbox" style="padding: 20px; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; color: #dc3545;"><?php _e('Failed', 'kilismile'); ?></h3>
                    <div style="font-size: 2rem; font-weight: bold; color: #dc3545;">
                        <?php echo number_format($stats->failed_count); ?>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="tablenav top">
                <form method="get" action="">
                    <input type="hidden" name="page" value="kilismile-donations">
                    
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Search donations...', 'kilismile'); ?>" style="min-width: 200px;">
                        
                        <select name="status">
                            <option value=""><?php _e('All Statuses', 'kilismile'); ?></option>
                            <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'kilismile'); ?></option>
                            <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php _e('Completed', 'kilismile'); ?></option>
                            <option value="failed" <?php selected($status_filter, 'failed'); ?>><?php _e('Failed', 'kilismile'); ?></option>
                        </select>
                        
                        <button type="submit" class="button"><?php _e('Filter', 'kilismile'); ?></button>
                        
                        <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="<?php echo admin_url('admin.php?page=kilismile-donations'); ?>" class="button"><?php _e('Clear', 'kilismile'); ?></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Donations Table -->
            <?php if (!empty($donations)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Transaction ID', 'kilismile'); ?></th>
                        <th><?php _e('Donor', 'kilismile'); ?></th>
                        <th><?php _e('Amount', 'kilismile'); ?></th>
                        <th><?php _e('Payment Method', 'kilismile'); ?></th>
                        <th><?php _e('Status', 'kilismile'); ?></th>
                        <th><?php _e('Date', 'kilismile'); ?></th>
                        <th><?php _e('Actions', 'kilismile'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($donation->transaction_id); ?></strong>
                            <?php if ($donation->is_recurring): ?>
                            <br><span class="dashicons dashicons-update" title="<?php _e('Recurring', 'kilismile'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($donation->is_anonymous): ?>
                                <em><?php _e('Anonymous', 'kilismile'); ?></em>
                            <?php else: ?>
                                <?php echo esc_html($donation->donor_name); ?>
                            <?php endif; ?>
                            <br><small><?php echo esc_html($donation->donor_email); ?></small>
                        </td>
                        <td>
                            <strong><?php echo esc_html($donation->currency . ' ' . number_format($donation->amount, 2)); ?></strong>
                        </td>
                        <td>
                            <?php echo esc_html(ucfirst($donation->payment_method)); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($donation->payment_status); ?>" style="
                                padding: 4px 8px; 
                                border-radius: 4px; 
                                font-size: 11px; 
                                font-weight: bold; 
                                text-transform: uppercase;
                                <?php
                                switch ($donation->payment_status) {
                                    case 'completed':
                                        echo 'background: #d4edda; color: #155724;';
                                        break;
                                    case 'pending':
                                        echo 'background: #fff3cd; color: #856404;';
                                        break;
                                    case 'failed':
                                        echo 'background: #f8d7da; color: #721c24;';
                                        break;
                                    default:
                                        echo 'background: #e2e3e5; color: #495057;';
                                }
                                ?>
                            ">
                                <?php echo esc_html($donation->payment_status); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('M j, Y \a\t g:i A', strtotime($donation->created_at)); ?>
                        </td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="donation_id" value="<?php echo $donation->id; ?>">
                                <select name="new_status" onchange="this.form.submit()" style="font-size: 11px;">
                                    <option value="pending" <?php selected($donation->payment_status, 'pending'); ?>><?php _e('Pending', 'kilismile'); ?></option>
                                    <option value="completed" <?php selected($donation->payment_status, 'completed'); ?>><?php _e('Completed', 'kilismile'); ?></option>
                                    <option value="failed" <?php selected($donation->payment_status, 'failed'); ?>><?php _e('Failed', 'kilismile'); ?></option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    $page_links = paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $page
                    ));
                    echo $page_links;
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;">
                <div style="font-size: 4rem; color: #ccc; margin-bottom: 20px;">
                    <span class="dashicons dashicons-heart"></span>
                </div>
                <h3 style="color: #666;"><?php _e('No donations found', 'kilismile'); ?></h3>
                <p style="color: #999;"><?php _e('Donations will appear here once people start donating through your website.', 'kilismile'); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Reports page
     */
    public function reports_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donations';
        
        // Get monthly data for charts
        $monthly_data = $wpdb->get_results(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as donations_count,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount
            FROM $table_name 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month"
        );
        
        // Get payment method breakdown
        $payment_methods = $wpdb->get_results(
            "SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount
            FROM $table_name 
            WHERE payment_status = 'completed'
            GROUP BY payment_method"
        );
        ?>
        
        <div class="wrap">
            <h1><?php _e('Donation Reports', 'kilismile'); ?></h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
                <!-- Monthly Chart -->
                <div class="postbox">
                    <div style="padding: 20px;">
                        <h3><?php _e('Monthly Donations', 'kilismile'); ?></h3>
                        <canvas id="monthlyChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <!-- Payment Methods Chart -->
                <div class="postbox">
                    <div style="padding: 20px;">
                        <h3><?php _e('Payment Methods', 'kilismile'); ?></h3>
                        <canvas id="paymentMethodsChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Payment Methods Table -->
            <div class="postbox" style="margin-top: 30px;">
                <div style="padding: 20px;">
                    <h3><?php _e('Payment Method Breakdown', 'kilismile'); ?></h3>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Payment Method', 'kilismile'); ?></th>
                                <th><?php _e('Donations', 'kilismile'); ?></th>
                                <th><?php _e('Total Amount', 'kilismile'); ?></th>
                                <th><?php _e('Average', 'kilismile'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payment_methods as $method): ?>
                            <tr>
                                <td><?php echo esc_html(ucfirst($method->payment_method)); ?></td>
                                <td><?php echo number_format($method->count); ?></td>
                                <td>$<?php echo number_format($method->total_amount, 2); ?></td>
                                <td>$<?php echo number_format($method->total_amount / $method->count, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <script>
        // Monthly donations chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . date('M Y', strtotime($item->month . '-01')) . '"'; }, $monthly_data)); ?>],
                datasets: [{
                    label: '<?php _e('Donations', 'kilismile'); ?>',
                    data: [<?php echo implode(',', array_column($monthly_data, 'donations_count')); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }, {
                    label: '<?php _e('Amount ($)', 'kilismile'); ?>',
                    data: [<?php echo implode(',', array_column($monthly_data, 'total_amount')); ?>],
                    borderColor: '#007cba',
                    backgroundColor: 'rgba(0, 124, 186, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
        
        // Payment methods chart
        const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . ucfirst($item->payment_method) . '"'; }, $payment_methods)); ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_column($payment_methods, 'count')); ?>],
                    backgroundColor: ['#28a745', '#007cba', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        </script>
        <?php
    }
}

// Initialize the dashboard
new KiliSmile_Donation_Dashboard();