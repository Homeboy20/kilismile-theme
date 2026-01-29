<?php
/**
 * Donation Debug Tracker
 * Enhanced debugging system for donation transactions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Donation_Debug {
    
    private static $debug_enabled = null;
    private static $debug_logs = array();
    
    /**
     * Check if debug mode is enabled
     */
    public static function is_debug_enabled() {
        if (self::$debug_enabled === null) {
            self::$debug_enabled = get_option('kilismile_donation_debug_enabled', false) || 
                                  (defined('WP_DEBUG') && WP_DEBUG) ||
                                  isset($_GET['debug']) ||
                                  isset($_POST['debug']);
        }
        return self::$debug_enabled;
    }
    
    /**
     * Log transaction step
     */
    public static function log_transaction($step, $data = array(), $status = 'info') {
        if (!self::is_debug_enabled()) return;
        
        $log_entry = array(
            'timestamp' => microtime(true),
            'datetime' => current_time('mysql'),
            'step' => $step,
            'status' => $status, // info, success, warning, error
            'data' => $data,
            'memory_usage' => memory_get_usage(true),
            'session_id' => session_id() ?: 'no-session'
        );
        
        self::$debug_logs[] = $log_entry;
        
        // Also log to WordPress debug.log if WP_DEBUG_LOG is enabled
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('[KILISMILE DONATION DEBUG] ' . $step . ': ' . json_encode($data));
        }
        
        // Store in database for persistent debugging
        self::store_debug_log($log_entry);
    }
    
    /**
     * Store debug log in database
     */
    private static function store_debug_log($log_entry) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donation_debug';
        
        // Create table if not exists
        self::create_debug_table();
        
        $wpdb->insert(
            $table_name,
            array(
                'timestamp' => $log_entry['timestamp'],
                'datetime' => $log_entry['datetime'],
                'step' => $log_entry['step'],
                'status' => $log_entry['status'],
                'data' => json_encode($log_entry['data']),
                'memory_usage' => $log_entry['memory_usage'],
                'session_id' => $log_entry['session_id'],
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ),
            array('%f', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    /**
     * Create debug table
     */
    private static function create_debug_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_donation_debug';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            timestamp double NOT NULL,
            datetime datetime NOT NULL,
            step varchar(100) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'info',
            data longtext,
            memory_usage bigint(20),
            session_id varchar(100),
            user_ip varchar(45),
            user_agent text,
            PRIMARY KEY (id),
            KEY timestamp (timestamp),
            KEY step (step),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Get debug logs for current session
     */
    public static function get_session_logs($session_id = null) {
        if (!$session_id) {
            $session_id = session_id();
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_donation_debug';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_id = %s ORDER BY timestamp ASC",
            $session_id
        ));
    }
    
    /**
     * Get recent debug logs
     */
    public static function get_recent_logs($limit = 50) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_donation_debug';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d",
            $limit
        ));
    }
    
    /**
     * Clean old debug logs
     */
    public static function cleanup_old_logs($days = 7) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_donation_debug';
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE datetime < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }
    
    /**
     * Get current debug logs for display
     */
    public static function get_current_logs() {
        return self::$debug_logs;
    }
    
    /**
     * Format debug data for display
     */
    public static function format_debug_data($data) {
        if (is_array($data) || is_object($data)) {
            return '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';
        }
        return htmlspecialchars($data);
    }
    
    /**
     * Generate debug panel HTML
     */
    public static function render_debug_panel() {
        if (!self::is_debug_enabled()) return '';
        
        $logs = self::get_current_logs();
        $session_logs = self::get_session_logs();
        
        ob_start();
        ?>
        <div id="kilismile-debug-panel" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; max-width: 400px;">
            <div style="background: #1e1e1e; color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); overflow: hidden;">
                
                <!-- Debug Panel Header -->
                <div style="background: #333; padding: 10px 15px; cursor: pointer;" onclick="toggleDebugContent()">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; font-size: 0.9rem;">🐛 Debug Panel</span>
                        <span id="debug-toggle" style="font-size: 0.8rem;">▼</span>
                    </div>
                </div>
                
                <!-- Debug Panel Content -->
                <div id="debug-content" style="max-height: 400px; overflow-y: auto;">
                    
                    <!-- Current Session Logs -->
                    <div style="padding: 15px; border-bottom: 1px solid #444;">
                        <h4 style="margin: 0 0 10px 0; font-size: 0.85rem; color: #4CAF50;">Current Session</h4>
                        <div id="current-logs" style="font-size: 0.75rem; line-height: 1.4;">
                            <?php if (empty($logs)): ?>
                                <div style="color: #888;">No logs yet...</div>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <div style="margin-bottom: 8px; padding: 6px; background: rgba(255,255,255,0.05); border-radius: 4px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                            <span style="color: <?php echo self::get_status_color($log['status']); ?>">
                                                <?php echo htmlspecialchars($log['step']); ?>
                                            </span>
                                            <span style="color: #888; font-size: 0.7rem;">
                                                <?php echo date('H:i:s', $log['timestamp']); ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($log['data'])): ?>
                                            <div style="color: #ccc; font-size: 0.7rem; max-height: 60px; overflow: hidden;">
                                                <?php echo self::format_mini_data($log['data']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Performance Stats -->
                    <div style="padding: 15px; border-bottom: 1px solid #444;">
                        <h4 style="margin: 0 0 10px 0; font-size: 0.85rem; color: #FF9800;">Performance</h4>
                        <div style="font-size: 0.75rem; color: #ccc;">
                            <div>Memory: <?php echo self::format_bytes(memory_get_usage(true)); ?></div>
                            <div>Peak: <?php echo self::format_bytes(memory_get_peak_usage(true)); ?></div>
                            <div>Time: <?php echo number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2); ?>ms</div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div style="padding: 15px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 0.85rem; color: #2196F3;">Actions</h4>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="clearDebugLogs()" style="background: #444; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer;">
                                Clear
                            </button>
                            <button onclick="exportDebugLogs()" style="background: #444; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer;">
                                Export
                            </button>
                            <button onclick="window.open('/wp-admin/admin.php?page=kilismile-debug-logs', '_blank')" style="background: #444; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer;">
                                Full View
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <script>
        window.KiliSmileDebug = {
            logs: <?php echo json_encode($logs); ?>,
            panelOpen: false,
            
            log: function(step, data, status = 'info') {
                const logEntry = {
                    timestamp: Date.now() / 1000,
                    step: step,
                    status: status,
                    data: data
                };
                
                this.logs.push(logEntry);
                this.updateCurrentLogs();
                
                // Send to server
                fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=kilismile_debug_log&step=${encodeURIComponent(step)}&data=${encodeURIComponent(JSON.stringify(data))}&status=${status}&nonce=<?php echo wp_create_nonce("kilismile_debug_nonce"); ?>`
                }).catch(console.error);
            },
            
            updateCurrentLogs: function() {
                const container = document.getElementById('current-logs');
                if (!container) return;
                
                const html = this.logs.map(log => `
                    <div style="margin-bottom: 8px; padding: 6px; background: rgba(255,255,255,0.05); border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                            <span style="color: ${this.getStatusColor(log.status)}">
                                ${log.step}
                            </span>
                            <span style="color: #888; font-size: 0.7rem;">
                                ${new Date(log.timestamp * 1000).toLocaleTimeString()}
                            </span>
                        </div>
                        ${log.data ? `<div style="color: #ccc; font-size: 0.7rem; max-height: 60px; overflow: hidden;">${this.formatMiniData(log.data)}</div>` : ''}
                    </div>
                `).join('');
                
                container.innerHTML = html || '<div style="color: #888;">No logs yet...</div>';
            },
            
            getStatusColor: function(status) {
                const colors = {
                    'info': '#2196F3',
                    'success': '#4CAF50',
                    'warning': '#FF9800',
                    'error': '#F44336'
                };
                return colors[status] || '#ccc';
            },
            
            formatMiniData: function(data) {
                if (typeof data === 'object') {
                    const keys = Object.keys(data);
                    if (keys.length > 3) {
                        return JSON.stringify(Object.fromEntries(keys.slice(0, 3).map(k => [k, data[k]]))) + '...';
                    }
                    return JSON.stringify(data);
                }
                return String(data).substring(0, 100) + (String(data).length > 100 ? '...' : '');
            }
        };
        
        function toggleDebugContent() {
            const content = document.getElementById('debug-content');
            const toggle = document.getElementById('debug-toggle');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                toggle.textContent = '▼';
                window.KiliSmileDebug.panelOpen = true;
            } else {
                content.style.display = 'none';
                toggle.textContent = '▶';
                window.KiliSmileDebug.panelOpen = false;
            }
        }
        
        function clearDebugLogs() {
            window.KiliSmileDebug.logs = [];
            window.KiliSmileDebug.updateCurrentLogs();
        }
        
        function exportDebugLogs() {
            const logs = window.KiliSmileDebug.logs;
            const dataStr = JSON.stringify(logs, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `kilismile-debug-${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);
        }
        
        // Auto-refresh logs every 5 seconds if panel is open
        setInterval(() => {
            if (window.KiliSmileDebug.panelOpen) {
                fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=kilismile_get_debug_logs&nonce=<?php echo wp_create_nonce("kilismile_debug_nonce"); ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            window.KiliSmileDebug.logs = data.data;
                            window.KiliSmileDebug.updateCurrentLogs();
                        }
                    })
                    .catch(console.error);
            }
        }, 5000);
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Helper methods
     */
    private static function get_status_color($status) {
        $colors = array(
            'info' => '#2196F3',
            'success' => '#4CAF50',
            'warning' => '#FF9800',
            'error' => '#F44336'
        );
        return $colors[$status] ?? '#ccc';
    }
    
    private static function format_mini_data($data) {
        if (is_array($data) || is_object($data)) {
            $data = (array) $data;
            $keys = array_keys($data);
            if (count($keys) > 3) {
                $limited = array_slice($data, 0, 3, true);
                return htmlspecialchars(json_encode($limited)) . '...';
            }
            return htmlspecialchars(json_encode($data));
        }
        $str = (string) $data;
        return htmlspecialchars(substr($str, 0, 100) . (strlen($str) > 100 ? '...' : ''));
    }
    
    private static function format_bytes($size) {
        $units = array('B', 'KB', 'MB', 'GB');
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}

// Initialize debug tracking
if (KiliSmile_Donation_Debug::is_debug_enabled()) {
    // Log page load
    KiliSmile_Donation_Debug::log_transaction('page_load', array(
        'url' => $_SERVER['REQUEST_URI'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? ''
    ));
}

// AJAX handlers for debug system
add_action('wp_ajax_kilismile_debug_log', 'kilismile_handle_debug_log');
add_action('wp_ajax_nopriv_kilismile_debug_log', 'kilismile_handle_debug_log');

function kilismile_handle_debug_log() {
    check_ajax_referer('kilismile_debug_nonce', 'nonce');
    
    $step = sanitize_text_field($_POST['step'] ?? '');
    $status = sanitize_text_field($_POST['status'] ?? 'info');
    $data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);
    
    KiliSmile_Donation_Debug::log_transaction($step, $data, $status);
    
    wp_send_json_success('Debug log recorded');
}

add_action('wp_ajax_kilismile_get_debug_logs', 'kilismile_get_debug_logs');
add_action('wp_ajax_nopriv_kilismile_get_debug_logs', 'kilismile_get_debug_logs');

function kilismile_get_debug_logs() {
    check_ajax_referer('kilismile_debug_nonce', 'nonce');
    
    $logs = KiliSmile_Donation_Debug::get_current_logs();
    wp_send_json_success($logs);
}

// Cleanup old logs daily
if (!wp_next_scheduled('kilismile_cleanup_debug_logs')) {
    wp_schedule_event(time(), 'daily', 'kilismile_cleanup_debug_logs');
}

add_action('kilismile_cleanup_debug_logs', function() {
    KiliSmile_Donation_Debug::cleanup_old_logs();
});
?>

