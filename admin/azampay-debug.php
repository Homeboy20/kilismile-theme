<?php
/**
 * Admin page to list AzamPay checkout debug packets.
 */
if (!defined('ABSPATH')) exit;

class KiliSmile_AzamPay_Debug_Page {
    public function __construct() {
        add_action('admin_menu', [$this,'register_page']);
        add_action('admin_post_kilismile_purge_azampay_debug', [$this,'handle_manual_purge']);
        add_action('wp_ajax_kilismile_delete_azampay_debug', [$this,'ajax_delete_packet']);
    }

    public function register_page() {
        add_submenu_page(
            'kilismile-dashboard',
            'AzamPay Debug Logs',
            'AzamPay Debug',
            'manage_options',
            'kilismile-azampay-debug',
            [$this,'render_page']
        );
    }

    private function get_debug_packets($limit = 10) {
        global $wpdb;
        $like = 'kilismile_azampay_checkout_debug_%';
        $rows = $wpdb->get_results($wpdb->prepare("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $like));
        $packets = [];
        foreach ($rows as $row) {
            $ref = str_replace('kilismile_azampay_checkout_debug_', '', $row->option_name);
            $data = json_decode($row->option_value, true);
            $ts = isset($data['timestamp']) ? strtotime($data['timestamp']) : 0;
            $packets[] = [
                'reference' => $ref,
                'option' => $row->option_name,
                'timestamp' => $ts,
                'raw' => $data
            ];
        }
        usort($packets, function($a,$b){ return $b['timestamp'] <=> $a['timestamp']; });
        return array_slice($packets,0,$limit);
    }

    public function render_page() {
        if (!current_user_can('manage_options')) return;
        $packets = $this->get_debug_packets();
        $nonce = wp_create_nonce('kilismile_azampay_debug_nonce');
        ?>
        <div class="wrap kilismile-admin-page">
            <h1><span class="dashicons dashicons-admin-tools"></span> AzamPay Checkout Debug</h1>
            <p>Showing the most recent debug packets captured when creating checkout sessions. These help diagnose API failures.</p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-bottom:15px;">
                <?php wp_nonce_field('kilismile_azampay_debug_purge','kilismile_debug_purge_nonce'); ?>
                <input type="hidden" name="action" value="kilismile_purge_azampay_debug" />
                <button class="button">Manual Cleanup (respect retention rules)</button>
            </form>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Timestamp</th>
                        <th>Status Code</th>
                        <th>Checkout URL</th>
                        <th>Message / Error</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$packets): ?>
                    <tr><td colspan="6">No debug packets found.</td></tr>
                <?php else: foreach ($packets as $p):
                    $raw = $p['raw'];
                    $code = $raw['response_code'] ?? '';
                    $url = esc_url($raw['parsed_checkout_url'] ?? '');
                    $msg = '';
                    if (!empty($raw['response_body'])) {
                        $decoded = json_decode($raw['response_body'], true);
                        if (is_array($decoded)) {
                            foreach (['message','error','error_description','detail'] as $k) {
                                if (!empty($decoded[$k])) { $msg = $decoded[$k]; break; }
                            }
                        }
                    }
                    $time = $p['timestamp'] ? date('Y-m-d H:i:s', $p['timestamp']) : '—';
                ?>
                    <tr data-option="<?php echo esc_attr($p['option']); ?>">
                        <td><code><?php echo esc_html($p['reference']); ?></code></td>
                        <td><?php echo esc_html($time); ?></td>
                        <td><?php echo esc_html($code); ?></td>
                        <td style="max-width:200px; word-break:break-all;">
                            <?php if ($url): ?><a href="<?php echo $url; ?>" target="_blank">Open</a><?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?php echo esc_html($msg ?: '—'); ?></td>
                        <td>
                            <button class="button view-payload" data-ref="<?php echo esc_attr($p['reference']); ?>">View</button>
                            <button class="button delete-packet" data-option="<?php echo esc_attr($p['option']); ?>">Delete</button>
                        </td>
                    </tr>
                    <tr class="payload-row" style="display:none;">
                        <td colspan="6">
                            <textarea style="width:100%; height:160px; font-family:monospace;" readonly><?php echo esc_textarea(wp_json_encode($raw, JSON_PRETTY_PRINT)); ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
            <p style="margin-top:10px;">Retention: Keep last 50 packets and those not older than 7 days. Cron: daily. Manual cleanup applies same rules.</p>
        </div>
        <script>
        jQuery(function($){
            $('.view-payload').on('click', function(){
                var $tr = $(this).closest('tr');
                $tr.next('.payload-row').toggle();
            });
            $('.delete-packet').on('click', function(){
                if(!confirm('Delete this debug packet?')) return;
                var option = $(this).data('option');
                $.post(ajaxurl, {action:'kilismile_delete_azampay_debug', option: option, nonce: '<?php echo $nonce; ?>'}, function(resp){
                    if(resp.success){
                        var row = $('tr[data-option="'+option+'"]').next('.payload-row').addBack();
                        row.fadeOut(300, function(){ $(this).remove(); });
                    } else {
                        alert(resp.data || 'Delete failed');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_delete_packet() {
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
        $nonce = $_POST['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'kilismile_azampay_debug_nonce')) wp_send_json_error('Bad nonce');
        $option = sanitize_text_field($_POST['option'] ?? '');
        if (!$option || strpos($option, 'kilismile_azampay_checkout_debug_') !== 0) wp_send_json_error('Invalid option');
        delete_option($option);
        wp_send_json_success();
    }

    public function handle_manual_purge() {
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        if (!isset($_POST['kilismile_debug_purge_nonce']) || !wp_verify_nonce($_POST['kilismile_debug_purge_nonce'], 'kilismile_azampay_debug_purge')) wp_die('Bad nonce');
        kilismile_run_azampay_debug_cleanup();
        wp_redirect(admin_url('admin.php?page=kilismile-azampay-debug&purged=1'));
        exit;
    }
}

new KiliSmile_AzamPay_Debug_Page();


