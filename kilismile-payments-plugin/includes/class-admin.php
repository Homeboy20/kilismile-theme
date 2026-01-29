<?php
/**
 * Admin Interface Class
 *
 * @package KiliSmile_Payments
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Interface
 * 
 * Handles all admin functionality including:
 * - Payment settings page
 * - Gateway configuration
 * - Transaction management
 * - System monitoring and logs
 * - Statistics and reports
 */
class KiliSmile_Payments_Admin {
    
    /**
     * Plugin instance
     */
    private $plugin;
    
    /**
     * Database instance
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->db = $plugin->get_database();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_kilismile_test_gateway', array($this, 'test_gateway_connection'));
        add_action('wp_ajax_kilismile_export_transactions', array($this, 'export_transactions'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('KiliSmile Payments', 'kilismile-payments'),
            __('KiliSmile Payments', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments',
            array($this, 'dashboard_page'),
            'dashicons-money-alt',
            30
        );
        
        // Submenu pages
        add_submenu_page(
            'kilismile-payments',
            __('Dashboard', 'kilismile-payments'),
            __('Dashboard', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Transactions', 'kilismile-payments'),
            __('Transactions', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-transactions',
            array($this, 'transactions_page')
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Gateway Settings', 'kilismile-payments'),
            __('Gateway Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-gateways',
            array($this, 'gateways_page')
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Logs', 'kilismile-payments'),
            __('Logs', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-logs',
            array($this, 'logs_page')
        );
        
        add_submenu_page(
            'kilismile-payments',
            __('Settings', 'kilismile-payments'),
            __('Settings', 'kilismile-payments'),
            'manage_options',
            'kilismile-payments-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Register individual options used by the theme settings template so they persist correctly
        register_setting('kilismile_payments_settings', 'kilismile_payments_options');
        register_setting('kilismile_payments_settings', 'kilismile_payments_mode');
        register_setting('kilismile_payments_settings', 'kilismile_payments_currency');
        register_setting('kilismile_payments_settings', 'kilismile_payments_currency_position');
        register_setting('kilismile_payments_settings', 'kilismile_payments_decimal_places');
        register_setting('kilismile_payments_settings', 'kilismile_payments_thousand_separator');
        register_setting('kilismile_payments_settings', 'kilismile_payments_decimal_separator');

        register_setting('kilismile_payments_settings', 'kilismile_payments_email_from_name');
        register_setting('kilismile_payments_settings', 'kilismile_payments_email_from_address');
        register_setting('kilismile_payments_settings', 'kilismile_payments_admin_email');
        register_setting('kilismile_payments_settings', 'kilismile_payments_send_admin_notifications');
        register_setting('kilismile_payments_settings', 'kilismile_payments_send_donor_receipts');

        register_setting('kilismile_payments_settings', 'kilismile_payments_rate_limiting');
        register_setting('kilismile_payments_settings', 'kilismile_payments_rate_limit_attempts');
        register_setting('kilismile_payments_settings', 'kilismile_payments_rate_limit_window');
        register_setting('kilismile_payments_settings', 'kilismile_payments_fraud_detection');
        register_setting('kilismile_payments_settings', 'kilismile_payments_max_amount');
        register_setting('kilismile_payments_settings', 'kilismile_payments_min_amount');

        register_setting('kilismile_payments_settings', 'kilismile_payments_gateway_order');
        register_setting('kilismile_payments_settings', 'kilismile_payments_data_retention');
        register_setting('kilismile_payments_settings', 'kilismile_payments_debug_mode');
        register_setting('kilismile_payments_settings', 'kilismile_payments_webhook_secret');
        register_setting('kilismile_payments_settings', 'kilismile_payments_custom_css');
        
        // General settings section
        add_settings_section(
            'kilismile_payments_general',
            __('General Settings', 'kilismile-payments'),
            array($this, 'general_settings_callback'),
            'kilismile_payments_settings'
        );
        
        add_settings_field(
            'test_mode',
            __('Test Mode', 'kilismile-payments'),
            array($this, 'test_mode_callback'),
            'kilismile_payments_settings',
            'kilismile_payments_general'
        );
        
        add_settings_field(
            'default_currency',
            __('Default Currency', 'kilismile-payments'),
            array($this, 'default_currency_callback'),
            'kilismile_payments_settings',
            'kilismile_payments_general'
        );
        
        add_settings_field(
            'logging_level',
            __('Logging Level', 'kilismile-payments'),
            array($this, 'logging_level_callback'),
            'kilismile_payments_settings',
            'kilismile_payments_general'
        );
        
        // Gateway settings for each gateway
        $gateways = $this->plugin->get_gateways();
        foreach ($gateways as $gateway_id => $gateway) {
            $this->register_gateway_settings($gateway_id, $gateway);
        }
    }
    
    /**
     * Register gateway specific settings
     */
    private function register_gateway_settings($gateway_id, $gateway) {
        $section_id = 'kilismile_payments_' . $gateway_id;
        
        add_settings_section(
            $section_id,
            sprintf(__('%s Settings', 'kilismile-payments'), $gateway->get_title()),
            array($this, 'gateway_settings_callback'),
            'kilismile_payments_gateways'
        );
        
        // Common gateway fields
        add_settings_field(
            $gateway_id . '_enabled',
            __('Enable Gateway', 'kilismile-payments'),
            array($this, 'gateway_enabled_callback'),
            'kilismile_payments_gateways',
            $section_id,
            array('gateway_id' => $gateway_id)
        );
        
        add_settings_field(
            $gateway_id . '_title',
            __('Title', 'kilismile-payments'),
            array($this, 'gateway_title_callback'),
            'kilismile_payments_gateways',
            $section_id,
            array('gateway_id' => $gateway_id)
        );
        
        add_settings_field(
            $gateway_id . '_description',
            __('Description', 'kilismile-payments'),
            array($this, 'gateway_description_callback'),
            'kilismile_payments_gateways',
            $section_id,
            array('gateway_id' => $gateway_id)
        );
        
        // Gateway specific fields
        if ($gateway_id === 'azampay') {
            $this->register_azampay_settings($section_id, $gateway_id);
        } elseif ($gateway_id === 'paypal') {
            $this->register_paypal_settings($section_id, $gateway_id);
        }
    }
    
    /**
     * Register AzamPay specific settings
     */
    private function register_azampay_settings($section_id, $gateway_id) {
        $fields = array(
            'app_name' => __('App Name', 'kilismile-payments'),
            'client_id' => __('Client ID', 'kilismile-payments'),
            'client_secret' => __('Client Secret', 'kilismile-payments'),
            'test_app_name' => __('Test App Name', 'kilismile-payments'),
            'test_client_id' => __('Test Client ID', 'kilismile-payments'),
            'test_client_secret' => __('Test Client Secret', 'kilismile-payments'),
        );
        
        foreach ($fields as $field_key => $field_label) {
            add_settings_field(
                $gateway_id . '_' . $field_key,
                $field_label,
                array($this, 'gateway_text_field_callback'),
                'kilismile_payments_gateways',
                $section_id,
                array('gateway_id' => $gateway_id, 'field_key' => $field_key)
            );
        }
    }
    
    /**
     * Register PayPal specific settings
     */
    private function register_paypal_settings($section_id, $gateway_id) {
        $fields = array(
            'client_id' => __('Client ID', 'kilismile-payments'),
            'client_secret' => __('Client Secret', 'kilismile-payments'),
            'webhook_id' => __('Webhook ID', 'kilismile-payments'),
            'test_client_id' => __('Test Client ID', 'kilismile-payments'),
            'test_client_secret' => __('Test Client Secret', 'kilismile-payments'),
            'test_webhook_id' => __('Test Webhook ID', 'kilismile-payments'),
        );
        
        foreach ($fields as $field_key => $field_label) {
            add_settings_field(
                $gateway_id . '_' . $field_key,
                $field_label,
                array($this, 'gateway_text_field_callback'),
                'kilismile_payments_gateways',
                $section_id,
                array('gateway_id' => $gateway_id, 'field_key' => $field_key)
            );
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'kilismile-payments') === false) {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
        
        wp_enqueue_script(
            'kilismile-payments-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('kilismile-payments-admin', 'kilismile_payments_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_payments_admin'),
            'strings' => array(
                'testing_connection' => __('Testing connection...', 'kilismile-payments'),
                'connection_successful' => __('Connection successful!', 'kilismile-payments'),
                'connection_failed' => __('Connection failed!', 'kilismile-payments'),
                'confirm_export' => __('Are you sure you want to export transactions?', 'kilismile-payments')
            )
        ));
        
        wp_enqueue_style(
            'kilismile-payments-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $stats = $this->db->get_statistics('', '30_days');
        $recent_transactions = $this->db->get_transactions(array('limit' => 10));
        $gateways = $this->plugin->get_gateways();
        
        include plugin_dir_path(dirname(__FILE__)) . 'admin/templates/dashboard.php';
    }
    
    /**
     * Transactions page
     */
    public function transactions_page() {
        // Handle bulk actions
        if (isset($_POST['action']) && $_POST['action'] === 'bulk_export') {
            $this->handle_bulk_export();
            return;
        }
        
        // Get filters
        $gateway = sanitize_text_field($_GET['gateway'] ?? '');
        $status = sanitize_text_field($_GET['status'] ?? '');
        $start_date = sanitize_text_field($_GET['start_date'] ?? '');
        $end_date = sanitize_text_field($_GET['end_date'] ?? '');
        
        // Pagination
        $per_page = 20;
        $page = intval($_GET['paged'] ?? 1);
        $offset = ($page - 1) * $per_page;
        
        // Get transactions
        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'gateway' => $gateway,
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date
        );
        
        $transactions = $this->db->get_transactions($args);
        $total_transactions = $this->db->get_transaction_count($args);
        $total_pages = ceil($total_transactions / $per_page);
        
        $gateways = $this->plugin->get_gateways();
        
        include plugin_dir_path(dirname(__FILE__)) . 'admin/templates/transactions.php';
    }
    
    /**
     * Gateways page
     */
    public function gateways_page() {
        // Handle form submission
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'kilismile_payments_gateways')) {
            $this->save_gateway_settings();
        }
        
        $gateways = $this->plugin->get_gateways();
        
        include plugin_dir_path(dirname(__FILE__)) . 'admin/templates/gateways.php';
    }
    
    /**
     * Logs page
     */
    public function logs_page() {
        // Get filters
        $gateway = sanitize_text_field($_GET['gateway'] ?? '');
        $level = sanitize_text_field($_GET['level'] ?? '');
        $start_date = sanitize_text_field($_GET['start_date'] ?? '');
        $end_date = sanitize_text_field($_GET['end_date'] ?? '');
        
        // Pagination
        $per_page = 50;
        $page = intval($_GET['paged'] ?? 1);
        $offset = ($page - 1) * $per_page;
        
        // Get logs
        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'gateway' => $gateway,
            'level' => $level,
            'start_date' => $start_date,
            'end_date' => $end_date
        );
        
        $logs = $this->db->get_logs($args);
        $gateways = $this->plugin->get_gateways();
        
        include plugin_dir_path(dirname(__FILE__)) . 'admin/templates/logs.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        include plugin_dir_path(dirname(__FILE__)) . 'admin/templates/settings.php';
    }
    
    /**
     * Save gateway settings
     */
    private function save_gateway_settings() {
        $gateways = $this->plugin->get_gateways();
        
        foreach ($gateways as $gateway_id => $gateway) {
            $gateway_data = $_POST[$gateway_id] ?? array();
            
            foreach ($gateway_data as $key => $value) {
                $this->db->save_gateway_setting($gateway_id, $key, sanitize_text_field($value));
            }
        }
        
        add_settings_error(
            'kilismile_payments_messages',
            'kilismile_payments_message',
            __('Settings saved successfully!', 'kilismile-payments'),
            'updated'
        );
    }
    
    /**
     * Test gateway connection
     */
    public function test_gateway_connection() {
        check_ajax_referer('kilismile_payments_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kilismile-payments'));
        }
        
        $gateway_id = sanitize_text_field($_POST['gateway_id'] ?? '');
        $gateways = $this->plugin->get_gateways();
        
        if (!isset($gateways[$gateway_id])) {
            wp_send_json_error(__('Invalid gateway', 'kilismile-payments'));
        }
        
        $gateway = $gateways[$gateway_id];
        
        // Test the gateway connection
        $test_result = $gateway->test_connection();
        
        if (is_wp_error($test_result)) {
            wp_send_json_error($test_result->get_error_message());
        } else {
            wp_send_json_success(__('Connection successful', 'kilismile-payments'));
        }
    }
    
    /**
     * Export transactions
     */
    public function export_transactions() {
        check_ajax_referer('kilismile_payments_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kilismile-payments'));
        }
        
        $format = sanitize_text_field($_POST['format'] ?? 'csv');
        $filters = $_POST['filters'] ?? array();
        
        $args = array(
            'gateway' => sanitize_text_field($filters['gateway'] ?? ''),
            'status' => sanitize_text_field($filters['status'] ?? ''),
            'start_date' => sanitize_text_field($filters['start_date'] ?? ''),
            'end_date' => sanitize_text_field($filters['end_date'] ?? ''),
            'limit' => 0 // No limit for export
        );
        
        $transactions = $this->db->get_transactions($args);
        
        if ($format === 'csv') {
            $this->export_transactions_csv($transactions);
        } else {
            $this->export_transactions_json($transactions);
        }
    }
    
    /**
     * Export transactions as CSV
     */
    private function export_transactions_csv($transactions) {
        $filename = 'kilismile_transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, array(
            'ID',
            'Gateway',
            'Type',
            'Reference ID',
            'Gateway Transaction ID',
            'Amount',
            'Currency',
            'Status',
            'Donor Name',
            'Donor Email',
            'Donor Phone',
            'Payment Method',
            'Recurring',
            'Created At',
            'Completed At'
        ));
        
        // Data
        foreach ($transactions as $transaction) {
            fputcsv($output, array(
                $transaction['id'],
                $transaction['gateway'],
                $transaction['transaction_type'],
                $transaction['reference_id'],
                $transaction['gateway_transaction_id'],
                $transaction['amount'],
                $transaction['currency'],
                $transaction['status'],
                $transaction['donor_name'],
                $transaction['donor_email'],
                $transaction['donor_phone'],
                $transaction['payment_method'],
                $transaction['recurring'] ? 'Yes' : 'No',
                $transaction['created_at'],
                $transaction['completed_at']
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export transactions as JSON
     */
    private function export_transactions_json($transactions) {
        $filename = 'kilismile_transactions_' . date('Y-m-d_H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($transactions, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        settings_errors('kilismile_payments_messages');
    }
    
    /**
     * General settings callback
     */
    public function general_settings_callback() {
        echo '<p>' . __('Configure general payment settings.', 'kilismile-payments') . '</p>';
    }
    
    /**
     * Gateway settings callback
     */
    public function gateway_settings_callback() {
        echo '<p>' . __('Configure gateway specific settings.', 'kilismile-payments') . '</p>';
    }
    
    /**
     * Test mode callback
     */
    public function test_mode_callback() {
        $options = get_option('kilismile_payments_options', array());
        $test_mode = isset($options['test_mode']) ? $options['test_mode'] : 0;
        
        echo '<input type="checkbox" name="kilismile_payments_options[test_mode]" value="1" ' . checked(1, $test_mode, false) . ' />';
        echo '<label>' . __('Enable test mode for all gateways', 'kilismile-payments') . '</label>';
    }
    
    /**
     * Default currency callback
     */
    public function default_currency_callback() {
        $options = get_option('kilismile_payments_options', array());
        $currency = isset($options['default_currency']) ? $options['default_currency'] : 'USD';
        
        $currencies = array(
            'USD' => 'US Dollar',
            'TZS' => 'Tanzanian Shilling',
            'EUR' => 'Euro',
            'GBP' => 'British Pound'
        );
        
        echo '<select name="kilismile_payments_options[default_currency]">';
        foreach ($currencies as $code => $name) {
            echo '<option value="' . $code . '" ' . selected($code, $currency, false) . '>' . $name . ' (' . $code . ')</option>';
        }
        echo '</select>';
    }
    
    /**
     * Logging level callback
     */
    public function logging_level_callback() {
        $options = get_option('kilismile_payments_options', array());
        $level = isset($options['logging_level']) ? $options['logging_level'] : 'info';
        
        $levels = array(
            'debug' => 'Debug',
            'info' => 'Info',
            'warning' => 'Warning',
            'error' => 'Error',
            'critical' => 'Critical'
        );
        
        echo '<select name="kilismile_payments_options[logging_level]">';
        foreach ($levels as $value => $label) {
            echo '<option value="' . $value . '" ' . selected($value, $level, false) . '>' . $label . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Gateway enabled callback
     */
    public function gateway_enabled_callback($args) {
        $gateway_id = $args['gateway_id'];
        $value = $this->db->get_gateway_setting($gateway_id, 'enabled', 0);
        
        echo '<input type="checkbox" name="' . $gateway_id . '[enabled]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    /**
     * Gateway title callback
     */
    public function gateway_title_callback($args) {
        $gateway_id = $args['gateway_id'];
        $value = $this->db->get_gateway_setting($gateway_id, 'title', '');
        
        echo '<input type="text" name="' . $gateway_id . '[title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    /**
     * Gateway description callback
     */
    public function gateway_description_callback($args) {
        $gateway_id = $args['gateway_id'];
        $value = $this->db->get_gateway_setting($gateway_id, 'description', '');
        
        echo '<textarea name="' . $gateway_id . '[description]" rows="3" cols="50">' . esc_textarea($value) . '</textarea>';
    }
    
    /**
     * Gateway text field callback
     */
    public function gateway_text_field_callback($args) {
        $gateway_id = $args['gateway_id'];
        $field_key = $args['field_key'];
        $value = $this->db->get_gateway_setting($gateway_id, $field_key, '');
        
        $type = (strpos($field_key, 'secret') !== false) ? 'password' : 'text';
        
        echo '<input type="' . $type . '" name="' . $gateway_id . '[' . $field_key . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }
}

