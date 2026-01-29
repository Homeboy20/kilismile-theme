<?php
/**
 * Admin page for viewing donation debug logs
 * 
 * Usage: Add this to WordPress admin menu or access directly via URL
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check admin privileges
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

// Handle log clearing
if (isset($_POST['clear_logs']) && wp_verify_nonce($_POST['nonce'], 'clear_donation_logs')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_donation_debug';
    $wpdb->query("DELETE FROM $table_name");
    echo '<div class="notice notice-success"><p>Debug logs cleared successfully.</p></div>';
}

// Get logs from database
global $wpdb;
$table_name = $wpdb->prefix . 'kilismile_donation_debug';

// Pagination
$per_page = 50;
$current_page = max(1, intval($_GET['paged'] ?? 1));
$offset = ($current_page - 1) * $per_page;

// Get total count
$total_logs = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
$total_pages = ceil($total_logs / $per_page);

// Get logs for current page
$logs = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
    $per_page,
    $offset
));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Donation Debug Logs</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 20px;
            background: #f1f1f1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #0073aa;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .stats {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex: 1;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
        }
        .controls {
            padding: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .btn {
            background: #0073aa;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger {
            background: #dc3545;
        }
        .logs-container {
            padding: 0;
        }
        .log-entry {
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .log-event {
            font-weight: bold;
            color: #0073aa;
        }
        .log-timestamp {
            color: #666;
            font-size: 12px;
        }
        .log-level {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .log-level.info {
            background: #e3f2fd;
            color: #1976d2;
        }
        .log-level.success {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .log-level.error {
            background: #ffebee;
            color: #c62828;
        }
        .log-level.warning {
            background: #fff3e0;
            color: #ef6c00;
        }
        .log-data {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .pagination {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ddd;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #0073aa;
        }
        .pagination .current {
            background: #0073aa;
            color: white;
        }
        .no-logs {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐛 Donation Debug Logs</h1>
            <p>Real-time debugging and transaction tracking for the donation system</p>
        </div>
        
        <?php
        // Calculate stats
        $stats = $wpdb->get_results("
            SELECT 
                log_level,
                COUNT(*) as count,
                MAX(created_at) as latest
            FROM $table_name 
            GROUP BY log_level
        ");
        
        $error_count = 0;
        $success_count = 0;
        $info_count = 0;
        $warning_count = 0;
        
        foreach ($stats as $stat) {
            switch ($stat->log_level) {
                case 'error': $error_count = $stat->count; break;
                case 'success': $success_count = $stat->count; break;
                case 'info': $info_count = $stat->count; break;
                case 'warning': $warning_count = $stat->count; break;
            }
        }
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_logs; ?></div>
                <div>Total Logs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #28a745;"><?php echo $success_count; ?></div>
                <div>Success</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #dc3545;"><?php echo $error_count; ?></div>
                <div>Errors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ffc107;"><?php echo $warning_count; ?></div>
                <div>Warnings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #17a2b8;"><?php echo $info_count; ?></div>
                <div>Info</div>
            </div>
        </div>
        
        <div class="controls">
            <div>
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="btn">🔄 Refresh</a>
                <a href="<?php echo home_url('/donation'); ?>" class="btn">💝 View Donation Form</a>
            </div>
            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to clear all debug logs?');">
                <?php wp_nonce_field('clear_donation_logs', 'nonce'); ?>
                <button type="submit" name="clear_logs" class="btn btn-danger">🗑️ Clear Logs</button>
            </form>
        </div>
        
        <div class="logs-container">
            <?php if (empty($logs)): ?>
                <div class="no-logs">
                    <h3>No debug logs found</h3>
                    <p>Visit the <a href="<?php echo home_url('/donation'); ?>">donation form</a> to generate some debug data.</p>
                </div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <div class="log-entry">
                        <div class="log-header">
                            <div>
                                <span class="log-event"><?php echo esc_html($log->event_type); ?></span>
                                <span class="log-level <?php echo esc_attr($log->log_level); ?>"><?php echo esc_html($log->log_level); ?></span>
                            </div>
                            <span class="log-timestamp">
                                <?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?>
                                <?php if ($log->session_id): ?>
                                    | Session: <?php echo substr(esc_html($log->session_id), 0, 8); ?>...
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <?php if ($log->event_data): ?>
                            <div class="log-data"><?php echo esc_html($log->event_data); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                $base_url = remove_query_arg('paged');
                
                if ($current_page > 1) {
                    echo '<a href="' . add_query_arg('paged', $current_page - 1, $base_url) . '">&laquo; Previous</a>';
                }
                
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="' . add_query_arg('paged', $i, $base_url) . '">' . $i . '</a>';
                    }
                }
                
                if ($current_page < $total_pages) {
                    echo '<a href="' . add_query_arg('paged', $current_page + 1, $base_url) . '">Next &raquo;</a>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>

