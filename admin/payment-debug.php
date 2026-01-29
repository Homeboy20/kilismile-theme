<?php
/**
 * Payment Debug Log Viewer
 */
if (!defined('ABSPATH')) exit;

class KiliSmile_Payment_Debug_Admin_Page {
    public function __construct() {
        add_action('admin_menu', [$this,'register']);
        add_action('admin_post_kilismile_toggle_payment_debug', [$this,'toggle']);
        add_action('admin_post_kilismile_clear_payment_debug', [$this,'clear']);
    }

    public function register() {
        add_submenu_page(
            'kilismile-dashboard',
            'Payment Debug Log',
            'Payment Debug',
            'manage_options',
            'kilismile-payment-debug',
            [$this,'render']
        );
    }

    public function toggle() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        check_admin_referer('kilismile_toggle_payment_debug');
        $enabled = get_option('kilismile_payment_debug_enabled', 0) ? 0 : 1;
        update_option('kilismile_payment_debug_enabled', $enabled);
        wp_redirect(admin_url('admin.php?page=kilismile-payment-debug&debug=' . $enabled));
        exit;
    }

    public function clear() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        check_admin_referer('kilismile_clear_payment_debug');
        $file = WP_CONTENT_DIR . '/payment-debug.log';
        if (file_exists($file)) unlink($file);
        wp_redirect(admin_url('admin.php?page=kilismile-payment-debug&cleared=1'));
        exit;
    }

    public function render() {
        if (!current_user_can('manage_options')) return;
        $enabled = get_option('kilismile_payment_debug_enabled', 0);
        $lines = kilismile_payment_debug_tail(300);
        ?>
        <div class="wrap kilismile-admin-page">
            <h1><span class="dashicons dashicons-admin-generic"></span> Payment Debug Log</h1>
            <p>Centralized structured log for payment events (AzamPay + PayPal). Toggle below. Only writes when WP_DEBUG is true.</p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline-block;margin-right:10px;">
                <?php wp_nonce_field('kilismile_toggle_payment_debug'); ?>
                <input type="hidden" name="action" value="kilismile_toggle_payment_debug" />
                <button class="button <?php echo $enabled ? 'button-secondary' : 'button-primary'; ?>">
                    <?php echo $enabled ? 'Disable Debug Logging' : 'Enable Debug Logging'; ?>
                </button>
            </form>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline-block;">
                <?php wp_nonce_field('kilismile_clear_payment_debug'); ?>
                <input type="hidden" name="action" value="kilismile_clear_payment_debug" />
                <button class="button">Clear Log File</button>
            </form>
            <p style="margin-top:15px;">Log file: <code>wp-content/payment-debug.log</code></p>
            <table class="widefat fixed striped" style="margin-top:15px;">
                <thead><tr><th>Time</th><th>Level</th><th>Event</th><th>Data</th></tr></thead>
                <tbody>
                <?php if (!$lines): ?>
                    <tr><td colspan="4">No log entries yet.</td></tr>
                <?php else:
                    foreach ($lines as $line):
                        $decoded = json_decode($line, true);
                        if (!$decoded) continue;
                        $data_short = $decoded['data'];
                        if (is_array($data_short)) {
                            $json = wp_json_encode($data_short);
                            if (strlen($json) > 180) $json = substr($json,0,180) . '…';
                        } else {
                            $json = is_string($data_short) ? $data_short : ''; 
                        }
                        ?>
                        <tr>
                            <td><?php echo esc_html($decoded['time']); ?></td>
                            <td><?php echo esc_html($decoded['level']); ?></td>
                            <td><?php echo esc_html($decoded['event']); ?></td>
                            <td><code style="white-space:pre-wrap;word-break:break-word;display:block;max-width:600px;"><?php echo esc_html($json); ?></code></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

new KiliSmile_Payment_Debug_Admin_Page();


