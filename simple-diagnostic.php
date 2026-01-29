<?php
/**
 * Simple Conflict Detector
 * Just loads WordPress and checks for conflicts
 */

// Load WordPress
$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}

echo "🔍 Loading WordPress...<br>";
require_once $wp_load_path;
echo "✅ WordPress loaded successfully<br>";

echo "<br>📋 Checking for payment class conflicts:<br>";

$classes = [
    'KiliSmile_Payment_Gateway_Base',
    'KiliSmile_Payments_Plugin',
    'KiliSmile_Payment_Processor'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        $reflection = new ReflectionClass($class);
        $file = $reflection->getFileName();
        echo "✅ $class found in: " . htmlspecialchars($file) . "<br>";
    } else {
        echo "❌ $class not found<br>";
    }
}

echo "<br>🔌 Active Plugins:<br>";
$active_plugins = get_option('active_plugins', []);
foreach ($active_plugins as $plugin) {
    if (strpos($plugin, 'kilismile') !== false || strpos($plugin, 'azampay') !== false) {
        echo "• " . htmlspecialchars($plugin) . "<br>";
    }
}

echo "<br>📁 Theme Info:<br>";
echo "Current theme: " . get_template() . "<br>";
echo "Theme directory: " . get_template_directory() . "<br>";

?>

