<?php
/**
 * Donation Management Admin Dashboard
 * 
 * Provides a comprehensive admin interface for managing donations,
 * viewing analytics, configuring payment gateways, and monitoring
 * the donation system.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Donation_Admin {
    
    private $db_handler;
    private $email_handler;
    private $donation_system;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_admin_actions'));
        add_action('wp_ajax_kilismile_test_azampay_token', array($this, 'ajax_test_azampay_token'));
        
        // Initialize handlers (theme payment system is canonical)
        if (!class_exists('KiliSmile_Donation_Database') || !class_exists('KiliSmile_Donation_Email_Handler')) {
            $this->db_handler = null;
            $this->email_handler = null;
            $this->donation_system = null;

            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>KiliSmile Donations Admin:</strong> Required donation classes are missing. Please ensure the KiliSmile theme files are present and up to date.</p></div>';
            });
            return;
        }

        $this->db_handler = new KiliSmile_Donation_Database();
        $this->email_handler = new KiliSmile_Donation_Email_Handler();

        // Optional integration point (not required)
        $this->donation_system = null;
    }

    /**
     * Admin AJAX: Test AzamPay token generation
     */
    public function ajax_test_azampay_token() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'kilismile')));
            return;
        }

        check_ajax_referer('kilismile_admin_nonce', 'nonce');

        if (!function_exists('kilismile_get_azampay_token')) {
            wp_send_json_error(array('message' => __('AzamPay functions are not available in the theme.', 'kilismile')));
            return;
        }

        $token = kilismile_get_azampay_token();
        if (!$token) {
            wp_send_json_error(array(
                'message' => __('Failed to obtain AzamPay token. Check credentials and connectivity (see debug logs).', 'kilismile')
            ));
            return;
        }

        $test_mode = get_option('kilismile_azampay_test_mode', 'yes') === 'yes';

        wp_send_json_success(array(
            'message' => __('AzamPay token generated successfully.', 'kilismile'),
            'environment' => $test_mode ? 'sandbox' : 'live'
        ));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main donations menu
        add_menu_page(
            'Donations',
            'Donations',
            'manage_options',
            'kilismile-donations',
            array($this, 'donations_dashboard_page'),
            'dashicons-heart',
            25
        );
        
        // Submenu pages
        add_submenu_page(
            'kilismile-donations',
            'All Donations',
            'All Donations',
            'manage_options',
            'kilismile-donations',
            array($this, 'donations_dashboard_page')
        );
        
        add_submenu_page(
            'kilismile-donations',
            'Analytics',
            'Analytics',
            'manage_options',
            'kilismile-donations-analytics',
            array($this, 'donations_analytics_page')
        );
        
        add_submenu_page(
            'kilismile-donations',
            'Payment Gateways',
            'Payment Gateways',
            'manage_options',
            'kilismile-payment-gateways',
            array($this, 'payment_gateways_page')
        );
        
        add_submenu_page(
            'kilismile-donations',
            'Settings',
            'Settings',
            'manage_options',
            'kilismile-donation-settings',
            array($this, 'donation_settings_page')
        );
        
        add_submenu_page(
            'kilismile-donations',
            'Export',
            'Export',
            'manage_options',
            'kilismile-donation-export',
            array($this, 'donation_export_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'kilismile-donations') === false) {
            return;
        }
        
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        wp_enqueue_script('kilismile-donation-admin', get_template_directory_uri() . '/assets/js/donation-admin.js', array('jquery', 'chart-js'), '2.0.0', true);
        
        wp_localize_script('kilismile-donation-admin', 'kilismileAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_admin_nonce')
        ));
        
        // Add admin styles
        wp_add_inline_style('wp-admin', $this->get_admin_css());
    }
    
    /**
     * Handle admin actions
     */
    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle donation status updates
        if (isset($_POST['update_donation_status']) && wp_verify_nonce($_POST['_wpnonce'], 'update_donation_status')) {
            $donation_id = intval($_POST['donation_id']);
            $new_status = sanitize_text_field($_POST['new_status']);
            
            if ($this->db_handler->update_donation_status($donation_id, $new_status)) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>Donation status updated successfully.</p></div>';
                });
            }
        }
        
        // Handle payment gateway settings
        if (isset($_POST['save_gateway_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'save_gateway_settings')) {
            $this->save_gateway_settings($_POST);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Payment gateway settings saved successfully.</p></div>';
            });
        }
    }
    
    /**
     * Main donations dashboard page
     */
    public function donations_dashboard_page() {
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
        
        $filters = array(
            'search' => $search,
            'status' => $status_filter,
            'date_from' => $date_from,
            'date_to' => $date_to
        );
        
        $donations = $this->db_handler->get_donations($per_page, $offset, $filters);
        $total_donations = $this->db_handler->count_donations($filters);
        $total_pages = ceil($total_donations / $per_page);
        
        // Get summary stats
        $stats = $this->db_handler->get_donation_statistics();
        
        ?>
        <div class="wrap">
            <h1>Donations Dashboard</h1>
            
            <!-- Summary Cards -->
            <div class="donation-stats-grid">
                <div class="stat-card">
                    <h3>Total Donations</h3>
                    <p class="stat-number"><?php echo number_format($stats['total_donations']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Amount (USD)</h3>
                    <p class="stat-number">$<?php echo number_format($stats['total_amount_usd'], 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Amount (TZS)</h3>
                    <p class="stat-number">TSh <?php echo number_format($stats['total_amount_tzs']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>This Month</h3>
                    <p class="stat-number"><?php echo number_format($stats['monthly_count']); ?></p>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="donation-filters">
                <form method="GET">
                    <input type="hidden" name="page" value="kilismile-donations">
                    
                    <input type="text" name="search" placeholder="Search donations..." value="<?php echo esc_attr($search); ?>">
                    
                    <select name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending</option>
                        <option value="completed" <?php selected($status_filter, 'completed'); ?>>Completed</option>
                        <option value="failed" <?php selected($status_filter, 'failed'); ?>>Failed</option>
                        <option value="refunded" <?php selected($status_filter, 'refunded'); ?>>Refunded</option>
                    </select>
                    
                    <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>" placeholder="From Date">
                    <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>" placeholder="To Date">
                    
                    <button type="submit" class="button">Filter</button>
                    <a href="<?php echo admin_url('admin.php?page=kilismile-donations'); ?>" class="button">Clear</a>
                </form>
            </div>
            
            <!-- Donations Table -->
            <div class="donations-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No donations found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?php echo esc_html($donation->id); ?></td>
                                    <td>
                                        <?php if ($donation->anonymous): ?>
                                            <em>Anonymous</em>
                                        <?php else: ?>
                                            <?php echo esc_html($donation->first_name . ' ' . $donation->last_name); ?>
                                            <br><small><?php echo esc_html($donation->email); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($donation->amount, 2); ?></td>
                                    <td><?php echo esc_html(strtoupper($donation->currency)); ?></td>
                                    <td><?php echo esc_html(ucfirst($donation->payment_method)); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($donation->status); ?>">
                                            <?php echo esc_html(ucfirst($donation->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y H:i', strtotime($donation->created_at)); ?></td>
                                    <td>
                                        <a href="#" class="button button-small view-donation" data-id="<?php echo $donation->id; ?>">View</a>
                                        <select class="status-update" data-id="<?php echo $donation->id; ?>">
                                            <option value="">Change Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                            <option value="failed">Failed</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $pagination_args = array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'current' => $current_page,
                            'total' => $total_pages
                        );
                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Analytics page
     */
    public function donations_analytics_page() {
        $analytics = $this->db_handler->get_analytics_data();
        ?>
        <div class="wrap">
            <h1>Donation Analytics</h1>
            
            <div class="analytics-grid">
                <div class="analytics-card">
                    <h3>Monthly Donations</h3>
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
                
                <div class="analytics-card">
                    <h3>Payment Methods</h3>
                    <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                </div>
                
                <div class="analytics-card">
                    <h3>Currency Distribution</h3>
                    <canvas id="currencyChart" width="400" height="200"></canvas>
                </div>
                
                <div class="analytics-card">
                    <h3>Status Overview</h3>
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <script>
                const analyticsData = <?php echo json_encode($analytics); ?>;
            </script>
        </div>
        <?php
    }
    
    /**
     * Payment gateways configuration page
     */
    public function payment_gateways_page() {
        $az_test_mode = get_option('kilismile_azampay_test_mode', 'yes') === 'yes';
        $callback_url = home_url('/azampay/callback/');

        // Manual transfer settings (used by theme donation handler)
        $local_bank_enabled = (int) get_option('kilismile_local_bank_enabled', 1);
        $local_bank_details = (string) get_option('kilismile_local_bank_details', '');
        $mpesa_number = (string) get_option('kilismile_mpesa_number', '');

        // AzamPay settings (used by theme AzamPay functions)
        $az_sandbox_app_name = (string) get_option('kilismile_azampay_sandbox_app_name', 'KiliSmile-Sandbox');
        $az_sandbox_client_id = (string) get_option('kilismile_azampay_sandbox_client_id', '');
        $az_sandbox_client_secret = (string) get_option('kilismile_azampay_sandbox_client_secret', '');
        $az_sandbox_api_key = (string) get_option('kilismile_azampay_sandbox_api_key', '');

        $az_live_app_name = (string) get_option('kilismile_azampay_live_app_name', '');
        $az_live_client_id = (string) get_option('kilismile_azampay_live_client_id', '');
        $az_live_client_secret = (string) get_option('kilismile_azampay_live_client_secret', '');
        $az_live_api_key = (string) get_option('kilismile_azampay_live_api_key', '');

        // PayPal settings (kept for future use)
        $paypal_enabled = (bool) get_option('kilismile_paypal_enabled', false);
        $paypal_client_id = (string) get_option('kilismile_paypal_client_id', '');
        $paypal_client_secret = (string) get_option('kilismile_paypal_client_secret', '');
        $paypal_sandbox = (bool) get_option('kilismile_paypal_sandbox', true);
        ?>
        <div class="wrap">
            <h1>Payment Gateways</h1>
            <p>Configure the theme payment system for donations (AzamPay, PayPal, Manual Transfer). Callback URL: <code><?php echo esc_html($callback_url); ?></code></p>
            
            <form method="POST" action="">
                <?php wp_nonce_field('save_gateway_settings'); ?>

                <h2 class="title">AzamPay</h2>
                <div class="gateway-card">
                    <p>Official AzamPay Mobile Money integration (TZS). Supports Airtel, Tigo, Halopesa, Vodacom.</p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">Environment</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="azampay_test_mode" value="1" <?php checked($az_test_mode); ?>>
                                    Use Sandbox/Test Mode
                                </label>
                                <p class="description">Sandbox uses <code>authenticator-sandbox.azampay.co.tz</code> and <code>sandbox.azampay.co.tz</code>.</p>
                            </td>
                        </tr>
                    </table>

                    <h3>Sandbox Credentials</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">App Name</th>
                            <td><input type="text" name="azampay_sandbox_app_name" value="<?php echo esc_attr($az_sandbox_app_name); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row">Client ID</th>
                            <td><input type="text" name="azampay_sandbox_client_id" value="<?php echo esc_attr($az_sandbox_client_id); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row">Client Secret</th>
                            <td><input type="password" name="azampay_sandbox_client_secret" value="<?php echo esc_attr($az_sandbox_client_secret); ?>" class="regular-text" autocomplete="new-password"></td>
                        </tr>
                        <tr>
                            <th scope="row">API Key (optional)</th>
                            <td><input type="password" name="azampay_sandbox_api_key" value="<?php echo esc_attr($az_sandbox_api_key); ?>" class="regular-text" autocomplete="new-password"></td>
                        </tr>
                    </table>

                    <h3>Live Credentials</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">App Name</th>
                            <td><input type="text" name="azampay_live_app_name" value="<?php echo esc_attr($az_live_app_name); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row">Client ID</th>
                            <td><input type="text" name="azampay_live_client_id" value="<?php echo esc_attr($az_live_client_id); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row">Client Secret</th>
                            <td><input type="password" name="azampay_live_client_secret" value="<?php echo esc_attr($az_live_client_secret); ?>" class="regular-text" autocomplete="new-password"></td>
                        </tr>
                        <tr>
                            <th scope="row">API Key (optional)</th>
                            <td><input type="password" name="azampay_live_api_key" value="<?php echo esc_attr($az_live_api_key); ?>" class="regular-text" autocomplete="new-password"></td>
                        </tr>
                    </table>

                    <h3>Callback</h3>
                    <p>Set this callback URL in AzamPay dashboard: <code><?php echo esc_html($callback_url); ?></code></p>
                    <p class="description">If callbacks 404, go to Settings → Permalinks and click Save once to flush rewrite rules.</p>

                    <p>
                        <button type="button" class="button" id="kilismile-test-azampay-token">Test AzamPay Token</button>
                        <span id="kilismile-azampay-test-result" style="margin-left: 10px;"></span>
                    </p>
                </div>

                <h2 class="title">Manual Transfer</h2>
                <div class="gateway-card">
                    <p>Enables manual bank/mobile-money transfers with emailed instructions (used by the theme donation handler).</p>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enable Bank Details</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="local_bank_enabled" value="1" <?php checked($local_bank_enabled, 1); ?>>
                                    Show bank transfer details in donor instructions
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Bank Details</th>
                            <td>
                                <textarea name="local_bank_details" rows="6" class="large-text code" placeholder="Bank name\nAccount name\nAccount number\nBranch\nSWIFT..."><?php echo esc_textarea($local_bank_details); ?></textarea>
                                <p class="description">One item per line. This is emailed to donors for manual transfers.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Mobile Money Number</th>
                            <td>
                                <input type="text" name="mpesa_number" value="<?php echo esc_attr($mpesa_number); ?>" class="regular-text" placeholder="+255763495575/+255735495575">
                                <p class="description">Used in manual transfer instructions (fallbacks to public contact if blank).</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <h2 class="title">PayPal</h2>
                <div class="gateway-card">
                    <p>PayPal configuration for USD donations. (Current theme flow may redirect using a stub; full integration can be wired up next.)</p>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enable PayPal</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="paypal_enabled" value="1" <?php checked($paypal_enabled); ?>>
                                    Enable PayPal
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Client ID</th>
                            <td><input type="text" name="paypal_client_id" value="<?php echo esc_attr($paypal_client_id); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row">Client Secret</th>
                            <td><input type="password" name="paypal_client_secret" value="<?php echo esc_attr($paypal_client_secret); ?>" class="regular-text" autocomplete="new-password"></td>
                        </tr>
                        <tr>
                            <th scope="row">Sandbox Mode</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="paypal_sandbox_mode" value="1" <?php checked($paypal_sandbox); ?>>
                                    Use PayPal Sandbox
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p class="submit">
                    <input type="submit" name="save_gateway_settings" class="button-primary" value="Save Settings">
                </p>
            </form>
            
            <!-- Test Payment Section -->
            <div class="gateway-card">
                <h3>Quick Links</h3>
                <p>Open the public donation form to validate end-to-end flows.</p>
                <div class="test-buttons">
                    <a href="<?php echo esc_url(home_url('/donation/')); ?>" class="button button-secondary" target="_blank">Open Donation Form</a>
                </div>
            </div>
        </div>
        
        <script>
        (function() {
            const btn = document.getElementById('kilismile-test-azampay-token');
            const out = document.getElementById('kilismile-azampay-test-result');
            if (!btn || !out) return;

            btn.addEventListener('click', function() {
                out.textContent = 'Testing...';
                out.style.color = '#666';

                const body = new URLSearchParams();
                body.append('action', 'kilismile_test_azampay_token');
                body.append('nonce', '<?php echo esc_js(wp_create_nonce('kilismile_admin_nonce')); ?>');

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: body.toString()
                })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        out.textContent = (data.data && data.data.message) ? data.data.message : 'OK';
                        out.style.color = '#0a7';
                    } else {
                        const msg = (data && data.data && data.data.message) ? data.data.message : 'Test failed';
                        out.textContent = msg;
                        out.style.color = '#c00';
                    }
                })
                .catch(() => {
                    out.textContent = 'Test failed (network error)';
                    out.style.color = '#c00';
                });
            });
        })();
        </script>
        <?php
    }
    
    /**
     * Donation settings page
     */
    public function donation_settings_page() {
        if (isset($_POST['save_settings'])) {
            $this->save_donation_settings($_POST);
            echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
        }
        
        $settings = array(
            'default_currency' => get_option('kilismile_default_currency', 'USD'),
            'minimum_amount_usd' => get_option('kilismile_minimum_amount_usd', 5),
            'minimum_amount_tzs' => get_option('kilismile_minimum_amount_tzs', 10000),
            'enable_recurring' => get_option('kilismile_enable_recurring', true),
            'enable_anonymous' => get_option('kilismile_enable_anonymous', true),
            'email_notifications' => get_option('kilismile_email_notifications', true),
            'admin_email' => get_option('kilismile_donation_admin_email', get_option('admin_email'))
        );
        ?>
        <div class="wrap">
            <h1>Donation Settings</h1>
            
            <form method="POST" action="">
                <?php wp_nonce_field('save_donation_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Default Currency</th>
                        <td>
                            <select name="default_currency">
                                <option value="USD" <?php selected($settings['default_currency'], 'USD'); ?>>USD</option>
                                <option value="TZS" <?php selected($settings['default_currency'], 'TZS'); ?>>TZS</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Minimum Amount (USD)</th>
                        <td>
                            <input type="number" name="minimum_amount_usd" value="<?php echo esc_attr($settings['minimum_amount_usd']); ?>" min="1" step="0.01">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Minimum Amount (TZS)</th>
                        <td>
                            <input type="number" name="minimum_amount_tzs" value="<?php echo esc_attr($settings['minimum_amount_tzs']); ?>" min="1" step="1">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Enable Recurring Donations</th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_recurring" value="1" <?php checked($settings['enable_recurring']); ?>>
                                Allow donors to set up recurring donations
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Enable Anonymous Donations</th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_anonymous" value="1" <?php checked($settings['enable_anonymous']); ?>>
                                Allow donors to donate anonymously
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Email Notifications</th>
                        <td>
                            <label>
                                <input type="checkbox" name="email_notifications" value="1" <?php checked($settings['email_notifications']); ?>>
                                Send email notifications for new donations
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Admin Email</th>
                        <td>
                            <input type="email" name="admin_email" value="<?php echo esc_attr($settings['admin_email']); ?>" class="regular-text">
                            <p class="description">Email address to receive donation notifications</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="save_settings" class="button-primary" value="Save Settings">
                </p>
            </form>
        </div>
        <?php
    }
    
    /**
     * Export page
     */
    public function donation_export_page() {
        ?>
        <div class="wrap">
            <h1>Export Donations</h1>
            
            <div class="export-options">
                <form method="POST" action="">
                    <?php wp_nonce_field('export_donations'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">Export Format</th>
                            <td>
                                <select name="export_format">
                                    <option value="csv">CSV</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Date Range</th>
                            <td>
                                <input type="date" name="export_date_from" placeholder="From Date">
                                <input type="date" name="export_date_to" placeholder="To Date">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Status Filter</th>
                            <td>
                                <select name="export_status">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Include Anonymous</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="include_anonymous" value="1" checked>
                                    Include anonymous donations
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="export_donations" class="button-primary" value="Export Donations">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save gateway settings
     */
    private function save_gateway_settings($data) {
        // PayPal
        update_option('kilismile_paypal_enabled', isset($data['paypal_enabled']));
        update_option('kilismile_paypal_client_id', sanitize_text_field($data['paypal_client_id'] ?? ''));
        update_option('kilismile_paypal_client_secret', sanitize_text_field($data['paypal_client_secret'] ?? ''));
        update_option('kilismile_paypal_sandbox', isset($data['paypal_sandbox_mode']));

        // AzamPay (theme canonical options)
        update_option('kilismile_azampay_test_mode', isset($data['azampay_test_mode']) ? 'yes' : 'no');

        update_option('kilismile_azampay_sandbox_app_name', sanitize_text_field($data['azampay_sandbox_app_name'] ?? 'KiliSmile-Sandbox'));
        update_option('kilismile_azampay_sandbox_client_id', sanitize_text_field($data['azampay_sandbox_client_id'] ?? ''));
        update_option('kilismile_azampay_sandbox_client_secret', sanitize_text_field($data['azampay_sandbox_client_secret'] ?? ''));
        update_option('kilismile_azampay_sandbox_api_key', sanitize_text_field($data['azampay_sandbox_api_key'] ?? ''));

        update_option('kilismile_azampay_live_app_name', sanitize_text_field($data['azampay_live_app_name'] ?? ''));
        update_option('kilismile_azampay_live_client_id', sanitize_text_field($data['azampay_live_client_id'] ?? ''));
        update_option('kilismile_azampay_live_client_secret', sanitize_text_field($data['azampay_live_client_secret'] ?? ''));
        update_option('kilismile_azampay_live_api_key', sanitize_text_field($data['azampay_live_api_key'] ?? ''));

        // Manual transfer
        update_option('kilismile_local_bank_enabled', isset($data['local_bank_enabled']) ? 1 : 0);
        update_option('kilismile_local_bank_details', sanitize_textarea_field($data['local_bank_details'] ?? ''));
        update_option('kilismile_mpesa_number', sanitize_text_field($data['mpesa_number'] ?? ''));

        // Back-compat: keep old AzamPay option keys populated to avoid regressions in any legacy code.
        update_option('kilismile_azampay_enabled', true);
        update_option('kilismile_azampay_app_name', sanitize_text_field($data['azampay_sandbox_app_name'] ?? $data['azampay_live_app_name'] ?? ''));
        update_option('kilismile_azampay_client_id', sanitize_text_field($data['azampay_sandbox_client_id'] ?? $data['azampay_live_client_id'] ?? ''));
        update_option('kilismile_azampay_client_secret', sanitize_text_field($data['azampay_sandbox_client_secret'] ?? $data['azampay_live_client_secret'] ?? ''));
        update_option('kilismile_azampay_sandbox', isset($data['azampay_test_mode']));
    }
    
    /**
     * Save donation settings
     */
    private function save_donation_settings($data) {
        update_option('kilismile_default_currency', sanitize_text_field($data['default_currency']));
        update_option('kilismile_minimum_amount_usd', floatval($data['minimum_amount_usd']));
        update_option('kilismile_minimum_amount_tzs', intval($data['minimum_amount_tzs']));
        update_option('kilismile_enable_recurring', isset($data['enable_recurring']));
        update_option('kilismile_enable_anonymous', isset($data['enable_anonymous']));
        update_option('kilismile_email_notifications', isset($data['email_notifications']));
        update_option('kilismile_donation_admin_email', sanitize_email($data['admin_email']));
    }
    
    /**
     * Get admin CSS
     */
    private function get_admin_css() {
        return '
            .donation-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .stat-card {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                text-align: center;
            }
            .stat-card h3 {
                margin: 0 0 10px 0;
                color: #666;
                font-size: 14px;
                font-weight: 500;
            }
            .stat-number {
                font-size: 28px;
                font-weight: bold;
                color: #333;
                margin: 0;
            }
            .donation-filters {
                background: white;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .donation-filters form {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }
            .status-badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                text-transform: uppercase;
            }
            .status-pending { background: #fff3cd; color: #856404; }
            .status-completed { background: #d4edda; color: #155724; }
            .status-failed { background: #f8d7da; color: #721c24; }
            .status-refunded { background: #e2e3e5; color: #383d41; }
            .gateway-card {
                background: white;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-left: 4px solid #007cba;
            }
            .gateway-header h3 {
                margin-top: 0;
                color: #007cba;
            }
            .gateway-description {
                color: #666;
                font-style: italic;
                margin: 5px 0 15px 0;
            }
            .gateway-info {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 6px;
                border: 1px solid #dee2e6;
            }
            .gateway-info h4 {
                margin-top: 0;
                margin-bottom: 10px;
                color: #495057;
            }
            .config-checklist {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .config-checklist li {
                padding: 5px 0;
                font-family: monospace;
            }
            .config-checklist li.configured {
                color: #28a745;
            }
            .config-checklist li.not-configured {
                color: #dc3545;
            }
            .config-checklist li.environment {
                color: #007cba;
                font-weight: bold;
            }
            .supported-networks {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin: 10px 0;
            }
            .network-badge {
                background: #007cba;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
            }
            .api-endpoints {
                margin: 10px 0;
            }
            .api-endpoints code {
                background: #f1f3f4;
                padding: 2px 4px;
                border-radius: 3px;
                font-size: 12px;
            }
            .api-endpoints ul {
                margin: 5px 0 15px 20px;
            }
            .test-buttons {
                display: flex;
                gap: 10px;
                margin-top: 15px;
            }
            .analytics-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .analytics-card {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .export-options {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
        ';
    }
}

// Initialize the admin dashboard
new KiliSmile_Donation_Admin();


