<?php
/**
 * Test Standalone Plugin Only
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
    <title>Standalone Plugin Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Standalone Plugin Test</h1>
        
        <h2>Plugin Status</h2>
        <?php
        
        // Check if the standalone plugin classes exist
        $classes_to_check = [
            'KiliSmile_Payment_Gateway_Base',
            'KiliSmile_Payment_Processor',
            'KiliSmile_Payments_Plugin'
        ];
        
        $conflicts_found = false;
        
        foreach ($classes_to_check as $class) {
            echo "<p><strong>$class:</strong> ";
            if (class_exists($class)) {
                echo '<span class="success">✅ Exists</span>';
                
                // Check where it's loaded from
                try {
                    $reflection = new ReflectionClass($class);
                    $file = $reflection->getFileName();
                    echo "<br><small>Loaded from: " . htmlspecialchars($file) . "</small>";
                    
                    // Check if it's from theme (bad) or plugin (good)
                    if (strpos($file, 'themes/kilismile') !== false) {
                        echo ' <span class="error">❌ FROM THEME (CONFLICT!)</span>';
                        $conflicts_found = true;
                    } elseif (strpos($file, 'plugins/kilismile-payments') !== false) {
                        echo ' <span class="success">✅ FROM PLUGIN (GOOD)</span>';
                    } else {
                        echo ' <span class="warning">⚠️ UNKNOWN SOURCE</span>';
                    }
                } catch (Exception $e) {
                    echo ' <span class="error">❌ REFLECTION ERROR</span>';
                }
            } else {
                echo '<span class="error">❌ Not Found</span>';
            }
            echo "</p>";
        }
        
        if ($conflicts_found) {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;">';
            echo '<h3>❌ CONFLICTS STILL DETECTED</h3>';
            echo '<p>Classes are still being loaded from the theme. The complete reset may not have worked properly.</p>';
            echo '</div>';
        } else {
            echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;">';
            echo '<h3>✅ NO CONFLICTS DETECTED</h3>';
            echo '<p>All payment classes are loading from the standalone plugin only.</p>';
            echo '</div>';
        }
        
        echo '<h2>WordPress Plugin Status</h2>';
        if (function_exists('get_plugins')) {
            $all_plugins = get_plugins();
            $active_plugins = get_option('active_plugins', array());
            
            foreach ($all_plugins as $plugin_file => $plugin_data) {
                if (strpos($plugin_file, 'kilismile-payments') !== false) {
                    $is_active = in_array($plugin_file, $active_plugins);
                    echo '<p><strong>' . htmlspecialchars($plugin_data['Name']) . ':</strong> ';
                    echo $is_active ? '<span class="success">✅ Active</span>' : '<span class="error">❌ Inactive</span>';
                    echo '<br><small>File: ' . htmlspecialchars($plugin_file) . '</small></p>';
                }
            }
        }
        
        ?>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="restore-functions.php" style="background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;">🔄 Restore Functions</a>
        </div>
    </div>
</body>
</html>

