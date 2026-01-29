<?php
/**
 * Payment System Conflict Diagnostics
 * This script helps identify duplicate class definitions and conflicts
 */

$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}
require_once $wp_load_path;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment System Conflict Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .section { margin: 20px 0; padding: 20px; border-radius: 8px; border-left: 5px solid #007cba; background: #f8f9fa; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .file-path { font-family: monospace; font-size: 0.9em; background: #e9ecef; padding: 2px 4px; border-radius: 3px; }
        h1, h2 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Payment System Conflict Diagnostics</h1>
        
        <div class="section">
            <h2>Constants Status</h2>
            <table>
                <tr><th>Constant</th><th>Status</th><th>Value</th></tr>
                <tr>
                    <td>KILISMILE_PAYMENTS_ACTIVE</td>
                    <td><?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? '<span class="success">✅ Defined</span>' : '<span class="error">❌ Not Defined</span>'; ?></td>
                    <td><?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? (KILISMILE_PAYMENTS_ACTIVE ? 'true' : 'false') : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>KILISMILE_PAYMENTS_VERSION</td>
                    <td><?php echo defined('KILISMILE_PAYMENTS_VERSION') ? '<span class="success">✅ Defined</span>' : '<span class="error">❌ Not Defined</span>'; ?></td>
                    <td><?php echo defined('KILISMILE_PAYMENTS_VERSION') ? KILISMILE_PAYMENTS_VERSION : 'N/A'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>Class Existence Check</h2>
            <?php
            $classes_to_check = array(
                'KiliSmile_Payments_Plugin',
                'KiliSmile_Payment_Gateway_Base',
                'KiliSmile_Payment_Processor',
                'KiliSmile_Modern_Donation_System',
                'KiliSmile_PayPal_Gateway_Modern',
                'KiliSmile_Payment_Gateway_Factory',
                'AzamPay_Gateway'
            );
            ?>
            <table>
                <tr><th>Class Name</th><th>Status</th><th>Reflection Info</th></tr>
                <?php foreach ($classes_to_check as $class): ?>
                <tr>
                    <td><?php echo esc_html($class); ?></td>
                    <td><?php echo class_exists($class) ? '<span class="success">✅ Exists</span>' : '<span class="error">❌ Not Found</span>'; ?></td>
                    <td>
                        <?php 
                        if (class_exists($class)) {
                            $reflection = new ReflectionClass($class);
                            echo '<span class="file-path">' . $reflection->getFileName() . ':' . $reflection->getStartLine() . '</span>';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="section">
            <h2>Function Existence Check</h2>
            <?php
            $functions_to_check = array(
                'kilismile_payments_init',
                'kilismile_payments_is_active',
                'kilismile_payments_version',
                'kilismile_payment_debug',
                'kilismile_donation_form'
            );
            ?>
            <table>
                <tr><th>Function Name</th><th>Status</th><th>Reflection Info</th></tr>
                <?php foreach ($functions_to_check as $function): ?>
                <tr>
                    <td><?php echo esc_html($function); ?></td>
                    <td><?php echo function_exists($function) ? '<span class="success">✅ Exists</span>' : '<span class="error">❌ Not Found</span>'; ?></td>
                    <td>
                        <?php 
                        if (function_exists($function)) {
                            $reflection = new ReflectionFunction($function);
                            echo '<span class="file-path">' . $reflection->getFileName() . ':' . $reflection->getStartLine() . '</span>';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="section">
            <h2>File Existence Check</h2>
            <?php
            $files_to_check = array(
                'Theme Plugin File' => get_template_directory() . '/kilismile-payments.php',
                'Theme Payment Processor' => get_template_directory() . '/plugin-includes/payment-processor.php',
                'Theme Payment Gateways' => get_template_directory() . '/plugin-includes/payment-gateways-modern.php',
                'Theme AzamPay Integration' => get_template_directory() . '/plugin-includes/azampay-integration.php',
                'Plugin Directory' => WP_PLUGIN_DIR . '/kilismile-payments',
                'Standalone AzamPay Plugin' => WP_PLUGIN_DIR . '/azampay-payment-gateway'
            );
            ?>
            <table>
                <tr><th>Description</th><th>Path</th><th>Status</th></tr>
                <?php foreach ($files_to_check as $desc => $path): ?>
                <tr>
                    <td><?php echo esc_html($desc); ?></td>
                    <td><span class="file-path"><?php echo esc_html($path); ?></span></td>
                    <td>
                        <?php 
                        if (file_exists($path)) {
                            echo '<span class="success">✅ Exists</span>';
                            if (is_dir($path)) {
                                echo ' <span class="info">(Directory)</span>';
                            }
                        } else {
                            echo '<span class="error">❌ Not Found</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="section">
            <h2>WordPress Plugin Status</h2>
            <?php
            if (function_exists('get_plugins')) {
                $all_plugins = get_plugins();
                $active_plugins = get_option('active_plugins', array());
                
                echo '<table>';
                echo '<tr><th>Plugin</th><th>Version</th><th>Status</th><th>File</th></tr>';
                
                foreach ($all_plugins as $plugin_file => $plugin_data) {
                    if (strpos($plugin_file, 'kilismile') !== false || strpos($plugin_file, 'azampay') !== false) {
                        $is_active = in_array($plugin_file, $active_plugins);
                        echo '<tr>';
                        echo '<td>' . esc_html($plugin_data['Name']) . '</td>';
                        echo '<td>' . esc_html($plugin_data['Version']) . '</td>';
                        echo '<td>' . ($is_active ? '<span class="success">✅ Active</span>' : '<span class="warning">⚠️ Inactive</span>') . '</td>';
                        echo '<td><span class="file-path">' . esc_html($plugin_file) . '</span></td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
            } else {
                echo '<p class="error">❌ get_plugins() function not available</p>';
            }
            ?>
        </div>
        
        <div class="section">
            <h2>Diagnostic Summary</h2>
            <?php
            $issues = array();
            $recommendations = array();
            
            // Check for duplicate plugin loading
            $theme_plugin_exists = file_exists(get_template_directory() . '/kilismile-payments.php');
            $wp_plugin_exists = file_exists(WP_PLUGIN_DIR . '/kilismile-payments');
            
            if ($theme_plugin_exists && $wp_plugin_exists) {
                $issues[] = "Duplicate payment systems detected: Both theme and WordPress plugin versions exist";
                $recommendations[] = "Remove one version to prevent conflicts";
            }
            
            // Check for class conflicts
            if (class_exists('KiliSmile_Payment_Gateway_Base')) {
                $reflection = new ReflectionClass('KiliSmile_Payment_Gateway_Base');
                $source_file = $reflection->getFileName();
                if (strpos($source_file, 'plugins') !== false && strpos($source_file, 'themes') !== false) {
                    $issues[] = "Class loaded from unexpected location: " . $source_file;
                }
            }
            
            if (empty($issues)) {
                echo '<p class="success">✅ No major conflicts detected</p>';
            } else {
                echo '<h3>Issues Found:</h3>';
                echo '<ul>';
                foreach ($issues as $issue) {
                    echo '<li class="error">❌ ' . esc_html($issue) . '</li>';
                }
                echo '</ul>';
                
                if (!empty($recommendations)) {
                    echo '<h3>Recommendations:</h3>';
                    echo '<ul>';
                    foreach ($recommendations as $rec) {
                        echo '<li class="info">💡 ' . esc_html($rec) . '</li>';
                    }
                    echo '</ul>';
                }
            }
            ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="<?php echo admin_url(); ?>" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Admin</a>
        </div>
    </div>
</body>
</html>

