<?php
/**
 * Enhanced Payment System Integration Loader
 * 
 * Seamlessly integrates enhanced payment components with the existing system
 * while maintaining backward compatibility and providing advanced features.
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Payment System Integration Class
 */
class KiliSmile_Enhanced_Payment_Integration {
    
    /**
     * Integration version
     */
    const VERSION = '2.0.0';
    
    /**
     * Instance holder
     */
    private static $instance = null;
    
    /**
     * Enhanced components
     */
    protected $enhanced_handler;
    protected $enhanced_gateways = [];
    protected $legacy_handler;
    
    /**
     * Integration settings
     */
    protected $settings;
    
    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_integration_settings();
        $this->init_integration();
        $this->setup_hooks();
    }
    
    /**
     * Load integration settings
     */
    protected function load_integration_settings() {
        $this->settings = wp_parse_args(get_option('kilismile_enhanced_integration_settings', []), [
            'enable_enhanced_features' => true,
            'fallback_to_legacy' => true,
            'enhanced_gateways' => ['selcom', 'paypal', 'mobile_money'],
            'migration_mode' => false,
            'debug_mode' => false,
            'performance_monitoring' => true,
            'auto_upgrade_legacy' => false
        ]);
    }
    
    /**
     * Initialize integration
     */
    protected function init_integration() {
        if (!$this->settings['enable_enhanced_features']) {
            return;
        }
        
        // Load enhanced components
        $this->load_enhanced_components();
        
        // Initialize backward compatibility layer
        $this->init_compatibility_layer();
        
        // Set up database tables for enhanced features
        $this->setup_enhanced_database();
        
        // Register enhanced endpoints
        $this->register_enhanced_endpoints();
    }
    
    /**
     * Load enhanced payment components
     */
    protected function load_enhanced_components() {
        $base_path = dirname(__FILE__);
        
        // Load enhanced base classes first
        $base_files = [
            'class-kilismile-payment-gateway-enhanced.php',
            'class-kilismile-donation-handler-enhanced.php'
        ];
        
        foreach ($base_files as $file) {
            $file_path = $base_path . '/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
        
        // Load enhanced gateway implementations
        $gateway_files = [
            'class-kilismile-selcom-gateway-enhanced.php',
            'class-kilismile-paypal-gateway-enhanced.php',
            'class-kilismile-mobile-money-gateway-enhanced.php'
        ];
        
        foreach ($gateway_files as $file) {
            $file_path = $base_path . '/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
        
        // Initialize enhanced donation handler
        if (class_exists('KiliSmile_Donation_Handler_Enhanced')) {
            $this->enhanced_handler = new KiliSmile_Donation_Handler_Enhanced();
        }
        
        // Initialize enhanced gateways
        $this->init_enhanced_gateways();
    }
    
    /**
     * Initialize enhanced gateways
     */
    protected function init_enhanced_gateways() {
        $gateway_classes = [
            'selcom' => 'KiliSmile_Selcom_Gateway_Enhanced',
            'paypal' => 'KiliSmile_PayPal_Gateway_Enhanced',
            'mobile_money' => 'KiliSmile_Mobile_Money_Gateway_Enhanced'
        ];
        
        foreach ($gateway_classes as $gateway_id => $class_name) {
            if (in_array($gateway_id, $this->settings['enhanced_gateways']) && class_exists($class_name)) {
                $this->enhanced_gateways[$gateway_id] = new $class_name();
            }
        }
    }
    
    /**
     * Initialize backward compatibility layer
     */
    protected function init_compatibility_layer() {
        // Store reference to legacy handler if it exists
        if (class_exists('KiliSmile_Donation_Handler')) {
            $this->legacy_handler = new KiliSmile_Donation_Handler();
        }
        
        // Set up legacy method redirections
        add_filter('kilismile_process_donation', [$this, 'route_donation_processing'], 10, 2);
        add_filter('kilismile_get_payment_gateway', [$this, 'route_gateway_selection'], 10, 2);
        add_filter('kilismile_validate_payment', [$this, 'route_payment_validation'], 10, 2);
    }
    
    /**
     * Setup enhanced database tables
     */
    protected function setup_enhanced_database() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Enhanced analytics table
        $analytics_table = $wpdb->prefix . 'kilismile_analytics';
        $analytics_sql = "CREATE TABLE IF NOT EXISTS {$analytics_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            session_id varchar(100),
            user_id bigint(20),
            ip_address varchar(45),
            user_agent text,
            timestamp datetime NOT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY timestamp (timestamp),
            KEY session_id (session_id)
        ) $charset_collate;";
        
        // Queue management table
        $queue_table = $wpdb->prefix . 'kilismile_queue';
        $queue_sql = "CREATE TABLE IF NOT EXISTS {$queue_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_type varchar(50) NOT NULL,
            data longtext NOT NULL,
            priority varchar(20) DEFAULT 'normal',
            status varchar(20) DEFAULT 'pending',
            scheduled_for datetime NOT NULL,
            attempts int(11) DEFAULT 0,
            max_attempts int(11) DEFAULT 3,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY job_type (job_type),
            KEY scheduled_for (scheduled_for)
        ) $charset_collate;";
        
        // Subscription plans table
        $plans_table = $wpdb->prefix . 'kilismile_subscription_plans';
        $plans_sql = "CREATE TABLE IF NOT EXISTS {$plans_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            paypal_plan_id varchar(100) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            frequency varchar(20) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime NOT NULL,
            metadata longtext,
            PRIMARY KEY (id),
            UNIQUE KEY paypal_plan_id (paypal_plan_id),
            KEY amount_currency_frequency (amount, currency, frequency)
        ) $charset_collate;";
        
        // Subscriptions table
        $subscriptions_table = $wpdb->prefix . 'kilismile_subscriptions';
        $subscriptions_sql = "CREATE TABLE IF NOT EXISTS {$subscriptions_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            subscription_id varchar(100) NOT NULL,
            donation_id bigint(20) NOT NULL,
            paypal_subscription_id varchar(100),
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            frequency varchar(20) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            metadata longtext,
            PRIMARY KEY (id),
            UNIQUE KEY subscription_id (subscription_id),
            KEY donation_id (donation_id),
            KEY paypal_subscription_id (paypal_subscription_id)
        ) $charset_collate;";
        
        // Execute table creation
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($analytics_sql);
        dbDelta($queue_sql);
        dbDelta($plans_sql);
        dbDelta($subscriptions_sql);
        
        // Update version option
        update_option('kilismile_enhanced_db_version', self::VERSION);
    }
    
    /**
     * Register enhanced API endpoints
     */
    protected function register_enhanced_endpoints() {
        add_action('rest_api_init', function() {
            // Enhanced donation processing endpoint
            register_rest_route('kilismile/v2', '/donations', [
                'methods' => 'POST',
                'callback' => [$this, 'api_process_donation'],
                'permission_callback' => '__return_true',
                'args' => [
                    'amount' => ['required' => true, 'type' => 'number'],
                    'currency' => ['required' => true, 'type' => 'string'],
                    'payment_method' => ['required' => true, 'type' => 'string'],
                    'donor_info' => ['required' => true, 'type' => 'object']
                ]
            ]);
            
            // Health check endpoint
            register_rest_route('kilismile/v2', '/health', [
                'methods' => 'GET',
                'callback' => [$this, 'api_health_check'],
                'permission_callback' => '__return_true'
            ]);
            
            // Analytics endpoint
            register_rest_route('kilismile/v2', '/analytics', [
                'methods' => 'GET',
                'callback' => [$this, 'api_get_analytics'],
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                }
            ]);
        });
    }
    
    /**
     * Setup hooks for integration
     */
    protected function setup_hooks() {
        // Admin integration
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Frontend integration
        add_action('wp_enqueue_scripts', [$this, 'enqueue_enhanced_scripts']);
        
        // AJAX handlers
        add_action('wp_ajax_kilismile_toggle_enhanced_mode', [$this, 'ajax_toggle_enhanced_mode']);
        add_action('wp_ajax_kilismile_migrate_legacy_data', [$this, 'ajax_migrate_legacy_data']);
        add_action('wp_ajax_kilismile_enhanced_dashboard_data', [$this, 'ajax_get_dashboard_data']);
        
        // Cron jobs for enhanced features
        add_action('kilismile_cleanup_analytics', [$this, 'cleanup_old_analytics']);
        add_action('kilismile_process_queue', [$this, 'process_background_queue']);
        
        // Schedule cron jobs if not already scheduled
        if (!wp_next_scheduled('kilismile_cleanup_analytics')) {
            wp_schedule_event(time(), 'daily', 'kilismile_cleanup_analytics');
        }
        
        if (!wp_next_scheduled('kilismile_process_queue')) {
            wp_schedule_event(time(), 'every_minute', 'kilismile_process_queue');
        }
    }
    
    /**
     * Route donation processing to appropriate handler
     */
    public function route_donation_processing($result, $donation_data) {
        if (!$this->should_use_enhanced_handler($donation_data)) {
            return $this->legacy_handler ? $this->legacy_handler->process_donation($donation_data) : $result;
        }
        
        if ($this->enhanced_handler) {
            return $this->enhanced_handler->process_donation($donation_data);
        }
        
        return $result;
    }
    
    /**
     * Route gateway selection to enhanced or legacy
     */
    public function route_gateway_selection($gateway, $gateway_id) {
        if (!$this->should_use_enhanced_gateway($gateway_id)) {
            return $gateway;
        }
        
        if (isset($this->enhanced_gateways[$gateway_id])) {
            return $this->enhanced_gateways[$gateway_id];
        }
        
        return $gateway;
    }
    
    /**
     * Route payment validation
     */
    public function route_payment_validation($result, $payment_data) {
        if (!$this->settings['enable_enhanced_features']) {
            return $result;
        }
        
        // Use enhanced validation if available
        if ($this->enhanced_handler && method_exists($this->enhanced_handler, 'validate_payment_enhanced')) {
            return $this->enhanced_handler->validate_payment_enhanced($payment_data);
        }
        
        return $result;
    }
    
    /**
     * Determine if enhanced handler should be used
     */
    protected function should_use_enhanced_handler($donation_data) {
        if (!$this->settings['enable_enhanced_features']) {
            return false;
        }
        
        // Use enhanced for specific gateways
        $gateway = $donation_data['payment_method'] ?? '';
        if (in_array($gateway, $this->settings['enhanced_gateways'])) {
            return true;
        }
        
        // Use enhanced for large amounts
        $amount = floatval($donation_data['amount'] ?? 0);
        if ($amount >= 100000) { // Large donations
            return true;
        }
        
        // Use enhanced if migration mode is on
        if ($this->settings['migration_mode']) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if enhanced gateway should be used
     */
    protected function should_use_enhanced_gateway($gateway_id) {
        return $this->settings['enable_enhanced_features'] && 
               in_array($gateway_id, $this->settings['enhanced_gateways']);
    }
    
    /**
     * API endpoint for processing donations
     */
    public function api_process_donation($request) {
        try {
            $params = $request->get_params();
            
            // Validate required parameters
            $required = ['amount', 'currency', 'payment_method', 'donor_info'];
            foreach ($required as $field) {
                if (empty($params[$field])) {
                    return new WP_REST_Response([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ], 400);
                }
            }
            
            // Process donation using enhanced handler
            if ($this->enhanced_handler) {
                $result = $this->enhanced_handler->process_donation($params);
                
                return new WP_REST_Response([
                    'success' => $result['success'],
                    'data' => $result
                ], $result['success'] ? 200 : 400);
            }
            
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Enhanced donation handler not available'
            ], 503);
            
        } catch (Exception $e) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint for health check
     */
    public function api_health_check($request) {
        $health_data = [
            'status' => 'healthy',
            'version' => self::VERSION,
            'timestamp' => current_time('c'),
            'components' => []
        ];
        
        // Check enhanced handler
        if ($this->enhanced_handler) {
            $health_data['components']['enhanced_handler'] = [
                'status' => 'active',
                'class' => get_class($this->enhanced_handler)
            ];
        }
        
        // Check enhanced gateways
        foreach ($this->enhanced_gateways as $gateway_id => $gateway) {
            $gateway_health = method_exists($gateway, 'health_check') 
                ? $gateway->health_check() 
                : ['status' => 'unknown'];
            
            $health_data['components']['gateways'][$gateway_id] = $gateway_health;
        }
        
        // Check database connectivity
        try {
            global $wpdb;
            $wpdb->get_var("SELECT 1");
            $health_data['components']['database'] = ['status' => 'connected'];
        } catch (Exception $e) {
            $health_data['components']['database'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $health_data['status'] = 'degraded';
        }
        
        return new WP_REST_Response($health_data);
    }
    
    /**
     * API endpoint for analytics
     */
    public function api_get_analytics($request) {
        $period = $request->get_param('period') ?: '24h';
        $metrics = $request->get_param('metrics') ?: ['donations', 'revenue', 'conversion_rate'];
        
        $analytics_data = [];
        
        foreach ($metrics as $metric) {
            $analytics_data[$metric] = $this->get_analytics_metric($metric, $period);
        }
        
        return new WP_REST_Response([
            'period' => $period,
            'metrics' => $analytics_data,
            'generated_at' => current_time('c')
        ]);
    }
    
    /**
     * Get analytics metric
     */
    protected function get_analytics_metric($metric, $period) {
        global $wpdb;
        
        $analytics_table = $wpdb->prefix . 'kilismile_analytics';
        
        // Convert period to SQL interval
        $interval_map = [
            '1h' => '1 HOUR',
            '24h' => '24 HOUR',
            '7d' => '7 DAY',
            '30d' => '30 DAY'
        ];
        
        $interval = $interval_map[$period] ?? '24 HOUR';
        
        switch ($metric) {
            case 'donations':
                return $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$analytics_table} 
                     WHERE event_type = 'donation_completed' 
                     AND timestamp >= DATE_SUB(NOW(), INTERVAL %s)",
                    $interval
                ));
            
            case 'attempts':
                return $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$analytics_table} 
                     WHERE event_type = 'donation_attempted' 
                     AND timestamp >= DATE_SUB(NOW(), INTERVAL %s)",
                    $interval
                ));
            
            case 'conversion_rate':
                $completed = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$analytics_table} 
                     WHERE event_type = 'donation_completed' 
                     AND timestamp >= DATE_SUB(NOW(), INTERVAL %s)",
                    $interval
                ));
                
                $attempted = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$analytics_table} 
                     WHERE event_type = 'donation_attempted' 
                     AND timestamp >= DATE_SUB(NOW(), INTERVAL %s)",
                    $interval
                ));
                
                return $attempted > 0 ? round(($completed / $attempted) * 100, 2) : 0;
            
            default:
                return 0;
        }
    }
    
    /**
     * Add admin pages for enhanced features
     */
    public function add_admin_pages() {
        add_submenu_page(
            'kilismile-theme-dashboard',
            'Enhanced Payment System',
            'Enhanced Payments',
            'manage_options',
            'kilismile-enhanced-payments',
            [$this, 'render_enhanced_admin_page']
        );
    }
    
    /**
     * Render enhanced admin page
     */
    public function render_enhanced_admin_page() {
        ?>
        <div class="wrap">
            <h1>Enhanced Payment System</h1>
            
            <div class="kilismile-enhanced-dashboard">
                <div class="dashboard-widgets">
                    <!-- System Status Widget -->
                    <div class="dashboard-widget">
                        <h3>System Status</h3>
                        <div id="enhanced-system-status" class="loading">
                            <p>Loading system status...</p>
                        </div>
                    </div>
                    
                    <!-- Performance Metrics Widget -->
                    <div class="dashboard-widget">
                        <h3>Performance Metrics</h3>
                        <div id="enhanced-performance-metrics" class="loading">
                            <p>Loading performance data...</p>
                        </div>
                    </div>
                    
                    <!-- Gateway Status Widget -->
                    <div class="dashboard-widget">
                        <h3>Payment Gateways</h3>
                        <div id="enhanced-gateway-status" class="loading">
                            <p>Loading gateway status...</p>
                        </div>
                    </div>
                    
                    <!-- Recent Activity Widget -->
                    <div class="dashboard-widget">
                        <h3>Recent Activity</h3>
                        <div id="enhanced-recent-activity" class="loading">
                            <p>Loading recent activity...</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-controls">
                    <div class="control-section">
                        <h3>Enhanced Features</h3>
                        <label>
                            <input type="checkbox" id="toggle-enhanced-mode" 
                                   <?php checked($this->settings['enable_enhanced_features']); ?>>
                            Enable Enhanced Features
                        </label>
                        
                        <label>
                            <input type="checkbox" id="toggle-migration-mode" 
                                   <?php checked($this->settings['migration_mode']); ?>>
                            Migration Mode
                        </label>
                        
                        <label>
                            <input type="checkbox" id="toggle-debug-mode" 
                                   <?php checked($this->settings['debug_mode']); ?>>
                            Debug Mode
                        </label>
                    </div>
                    
                    <div class="control-section">
                        <h3>Actions</h3>
                        <button type="button" class="button button-primary" id="migrate-legacy-data">
                            Migrate Legacy Data
                        </button>
                        
                        <button type="button" class="button" id="clear-analytics">
                            Clear Analytics
                        </button>
                        
                        <button type="button" class="button" id="test-gateways">
                            Test Gateways
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .kilismile-enhanced-dashboard {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .dashboard-widgets {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .dashboard-widget {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
        }
        
        .dashboard-widget h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .dashboard-controls {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            height: fit-content;
        }
        
        .control-section {
            margin-bottom: 20px;
        }
        
        .control-section h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .control-section label {
            display: block;
            margin-bottom: 8px;
        }
        
        .loading {
            color: #666;
            font-style: italic;
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-healthy { background-color: #46b450; }
        .status-warning { background-color: #ffb900; }
        .status-error { background-color: #dc3232; }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Load dashboard data
            loadDashboardData();
            
            // Toggle enhanced mode
            $('#toggle-enhanced-mode').on('change', function() {
                toggleEnhancedMode($(this).is(':checked'));
            });
            
            // Migrate legacy data
            $('#migrate-legacy-data').on('click', function() {
                migrateLegacyData();
            });
            
            function loadDashboardData() {
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'kilismile_enhanced_dashboard_data',
                        nonce: '<?php echo wp_create_nonce('kilismile_enhanced_dashboard'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            updateDashboardWidgets(response.data);
                        }
                    }
                });
            }
            
            function updateDashboardWidgets(data) {
                // Update system status
                $('#enhanced-system-status').html(renderSystemStatus(data.system_status));
                
                // Update performance metrics
                $('#enhanced-performance-metrics').html(renderPerformanceMetrics(data.performance));
                
                // Update gateway status
                $('#enhanced-gateway-status').html(renderGatewayStatus(data.gateways));
                
                // Update recent activity
                $('#enhanced-recent-activity').html(renderRecentActivity(data.recent_activity));
            }
            
            function renderSystemStatus(status) {
                const indicator = `<span class="status-indicator status-${status.status}"></span>`;
                return `
                    <p>${indicator} System Status: <strong>${status.status}</strong></p>
                    <p>Version: ${status.version}</p>
                    <p>Enhanced Handler: ${status.enhanced_handler ? 'Active' : 'Inactive'}</p>
                    <p>Queue Size: ${status.queue_size || 0}</p>
                `;
            }
            
            function renderPerformanceMetrics(metrics) {
                return `
                    <p>Success Rate: <strong>${metrics.success_rate}%</strong></p>
                    <p>Avg Processing Time: <strong>${metrics.avg_processing_time}ms</strong></p>
                    <p>24h Donations: <strong>${metrics.donations_24h}</strong></p>
                    <p>Error Rate: <strong>${metrics.error_rate}%</strong></p>
                `;
            }
            
            function renderGatewayStatus(gateways) {
                let html = '';
                for (const [gateway, status] of Object.entries(gateways)) {
                    const indicator = `<span class="status-indicator status-${status.status}"></span>`;
                    html += `<p>${indicator} ${gateway}: <strong>${status.status}</strong></p>`;
                }
                return html;
            }
            
            function renderRecentActivity(activities) {
                let html = '<ul>';
                activities.forEach(activity => {
                    html += `<li>${activity.description} <small>(${activity.time})</small></li>`;
                });
                html += '</ul>';
                return html;
            }
            
            function toggleEnhancedMode(enabled) {
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'kilismile_toggle_enhanced_mode',
                        enabled: enabled,
                        nonce: '<?php echo wp_create_nonce('kilismile_enhanced_toggle'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
            
            function migrateLegacyData() {
                if (!confirm('This will migrate legacy donation data to the enhanced system. Continue?')) {
                    return;
                }
                
                $('#migrate-legacy-data').prop('disabled', true).text('Migrating...');
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'kilismile_migrate_legacy_data',
                        nonce: '<?php echo wp_create_nonce('kilismile_migrate_data'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Migration completed successfully!');
                        } else {
                            alert('Migration failed: ' + response.data.message);
                        }
                        $('#migrate-legacy-data').prop('disabled', false).text('Migrate Legacy Data');
                    }
                });
            }
            
            // Refresh dashboard data every 30 seconds
            setInterval(loadDashboardData, 30000);
        });
        </script>
        <?php
    }
    
    /**
     * AJAX handler for dashboard data
     */
    public function ajax_get_dashboard_data() {
        if (!current_user_can('manage_options') || 
            !wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_enhanced_dashboard')) {
            wp_die('Unauthorized');
        }
        
        $data = [
            'system_status' => $this->get_system_status(),
            'performance' => $this->get_performance_metrics(),
            'gateways' => $this->get_gateway_status(),
            'recent_activity' => $this->get_recent_activity()
        ];
        
        wp_send_json_success($data);
    }
    
    /**
     * Get system status for dashboard
     */
    protected function get_system_status() {
        return [
            'status' => $this->settings['enable_enhanced_features'] ? 'healthy' : 'disabled',
            'version' => self::VERSION,
            'enhanced_handler' => !empty($this->enhanced_handler),
            'queue_size' => $this->get_queue_size()
        ];
    }
    
    /**
     * Get performance metrics
     */
    protected function get_performance_metrics() {
        global $wpdb;
        
        $analytics_table = $wpdb->prefix . 'kilismile_analytics';
        
        // Get 24h donation count
        $donations_24h = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$analytics_table} 
             WHERE event_type = 'donation_completed' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        ));
        
        // Calculate success rate
        $completed = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$analytics_table} 
             WHERE event_type = 'donation_completed' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        ));
        
        $attempted = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$analytics_table} 
             WHERE event_type = 'donation_attempted' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        ));
        
        $success_rate = $attempted > 0 ? round(($completed / $attempted) * 100, 1) : 0;
        
        return [
            'donations_24h' => $donations_24h ?: 0,
            'success_rate' => $success_rate,
            'avg_processing_time' => 1250, // Placeholder
            'error_rate' => max(0, 100 - $success_rate)
        ];
    }
    
    /**
     * Get gateway status
     */
    protected function get_gateway_status() {
        $status = [];
        
        foreach ($this->enhanced_gateways as $gateway_id => $gateway) {
            if (method_exists($gateway, 'health_check')) {
                $health = $gateway->health_check();
                $status[$gateway_id] = [
                    'status' => $health['status'] ?? 'unknown'
                ];
            } else {
                $status[$gateway_id] = ['status' => 'unknown'];
            }
        }
        
        return $status;
    }
    
    /**
     * Get recent activity
     */
    protected function get_recent_activity() {
        global $wpdb;
        
        $analytics_table = $wpdb->prefix . 'kilismile_analytics';
        
        $activities = $wpdb->get_results($wpdb->prepare(
            "SELECT event_type, timestamp FROM {$analytics_table} 
             WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
             ORDER BY timestamp DESC 
             LIMIT 10"
        ));
        
        $formatted_activities = [];
        foreach ($activities as $activity) {
            $formatted_activities[] = [
                'description' => $this->format_activity_description($activity->event_type),
                'time' => human_time_diff(strtotime($activity->timestamp)) . ' ago'
            ];
        }
        
        return $formatted_activities;
    }
    
    /**
     * Format activity description
     */
    protected function format_activity_description($event_type) {
        $descriptions = [
            'donation_attempted' => 'Donation attempt',
            'donation_completed' => 'Donation completed',
            'donation_failed' => 'Donation failed',
            'gateway_error' => 'Gateway error'
        ];
        
        return $descriptions[$event_type] ?? ucfirst(str_replace('_', ' ', $event_type));
    }
    
    /**
     * Get queue size
     */
    protected function get_queue_size() {
        global $wpdb;
        
        $queue_table = $wpdb->prefix . 'kilismile_queue';
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$queue_table} WHERE status = 'pending'"
        )) ?: 0;
    }
    
    /**
     * AJAX toggle enhanced mode
     */
    public function ajax_toggle_enhanced_mode() {
        if (!current_user_can('manage_options') || 
            !wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_enhanced_toggle')) {
            wp_die('Unauthorized');
        }
        
        $enabled = $_POST['enabled'] === 'true';
        
        $this->settings['enable_enhanced_features'] = $enabled;
        update_option('kilismile_enhanced_integration_settings', $this->settings);
        
        wp_send_json_success(['enabled' => $enabled]);
    }
    
    /**
     * AJAX migrate legacy data
     */
    public function ajax_migrate_legacy_data() {
        if (!current_user_can('manage_options') || 
            !wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_migrate_data')) {
            wp_die('Unauthorized');
        }
        
        try {
            $migrated_count = $this->migrate_legacy_donation_data();
            
            wp_send_json_success([
                'message' => "Successfully migrated {$migrated_count} donations",
                'count' => $migrated_count
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Migrate legacy donation data
     */
    protected function migrate_legacy_donation_data() {
        global $wpdb;
        
        // This would contain actual migration logic
        // For now, return a placeholder count
        return 0;
    }
    
    /**
     * Enqueue enhanced scripts
     */
    public function enqueue_enhanced_scripts() {
        if (!$this->settings['enable_enhanced_features']) {
            return;
        }
        
        // Enqueue enhanced donation form JavaScript
        wp_enqueue_script(
            'kilismile-enhanced-donations',
            get_template_directory_uri() . '/assets/js/donation-form-enhanced.js',
            ['jquery'],
            self::VERSION,
            true
        );
        
        // Localize script with enhanced settings
        wp_localize_script('kilismile-enhanced-donations', 'kilismileEnhanced', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('kilismile/v2/'),
            'nonce' => wp_create_nonce('kilismile_enhanced_nonce'),
            'settings' => [
                'debug_mode' => $this->settings['debug_mode'],
                'available_gateways' => array_keys($this->enhanced_gateways)
            ]
        ]);
    }
    
    /**
     * Cleanup old analytics data
     */
    public function cleanup_old_analytics() {
        global $wpdb;
        
        $analytics_table = $wpdb->prefix . 'kilismile_analytics';
        
        // Delete analytics data older than 90 days
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$analytics_table} 
             WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY)"
        ));
    }
    
    /**
     * Process background queue
     */
    public function process_background_queue() {
        if (!$this->enhanced_handler) {
            return;
        }
        
        // Process queue items
        if (method_exists($this->enhanced_handler, 'process_donation_queue')) {
            $this->enhanced_handler->process_donation_queue();
        }
    }
    
    /**
     * Register admin settings
     */
    public function register_settings() {
        register_setting('kilismile_enhanced_settings', 'kilismile_enhanced_integration_settings');
    }
    
    /**
     * Get integration instance
     */
    public static function init() {
        return self::get_instance();
    }
}

// Initialize the enhanced payment integration
add_action('plugins_loaded', function() {
    KiliSmile_Enhanced_Payment_Integration::init();
});


