<?php
/**
 * Direct Plugin Activation Test
 * Tests plugin activation without going through WordPress admin
 */

// This test runs the plugin activation manually to check for errors

echo "Manual Plugin Activation Test\n";
echo "==============================\n\n";

// Set up minimal WordPress environment
$wp_root = dirname(__FILE__) . '/../../../';
if (!file_exists($wp_root . 'wp-config.php')) {
    echo "❌ WordPress not found\n";
    exit(1);
}

// Define constants that plugins expect
define('ABSPATH', $wp_root);
define('WP_PLUGIN_DIR', $wp_root . 'wp-content/plugins');
define('WP_PLUGIN_URL', 'http://localhost/wp-content/plugins');

// Mock some WordPress functions that the plugin might need
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        $path = str_replace(ABSPATH, '', dirname($file));
        return 'http://localhost/' . $path . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return str_replace(WP_PLUGIN_DIR . '/', '', $file);
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1) {
        // Mock function - just record the action
        echo "Action registered: $hook\n";
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $callback) {
        echo "Activation hook registered for: " . basename($file) . "\n";
        
        // Try to call the activation function manually
        if (is_array($callback)) {
            $class = $callback[0];
            $method = $callback[1];
            echo "Attempting to call {$class}::{$method}()\n";
            
            if (class_exists($class) && method_exists($class, $method)) {
                try {
                    call_user_func($callback);
                    echo "✅ Activation callback executed successfully\n";
                } catch (Exception $e) {
                    echo "❌ Activation callback failed: " . $e->getMessage() . "\n";
                } catch (Error $e) {
                    echo "❌ Activation callback error: " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ Class or method not found\n";
            }
        }
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $callback) {
        echo "Deactivation hook registered\n";
    }
}

if (!function_exists('add_option')) {
    function add_option($name, $value) {
        echo "Option added: $name\n";
    }
}

if (!function_exists('flush_rewrite_rules')) {
    function flush_rewrite_rules() {
        echo "Rewrite rules flushed\n";
    }
}

// Test loading the plugin
echo "Loading plugin file...\n";
$plugin_file = WP_PLUGIN_DIR . '/azampay-payment-gateway/azampay-payment-gateway.php';

if (!file_exists($plugin_file)) {
    echo "❌ Plugin file not found: $plugin_file\n";
    exit(1);
}

try {
    // Capture any output
    ob_start();
    include_once $plugin_file;
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "Plugin output:\n$output\n";
    }
    
    echo "✅ Plugin loaded successfully\n";
    
    // Check if main class exists
    if (class_exists('AzamPay_Gateway_Plugin')) {
        echo "✅ Main plugin class found\n";
        
        // Check activate method
        if (method_exists('AzamPay_Gateway_Plugin', 'activate')) {
            $reflection = new ReflectionMethod('AzamPay_Gateway_Plugin', 'activate');
            if ($reflection->isStatic()) {
                echo "✅ activate() method is static\n";
            } else {
                echo "❌ activate() method is not static\n";
            }
        }
    } else {
        echo "❌ Main plugin class not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception loading plugin: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ Fatal error loading plugin: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
?>

