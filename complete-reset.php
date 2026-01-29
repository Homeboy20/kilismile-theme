<?php
/**
 * Complete Conflict Elimination
 * This file will temporarily disable ALL payment-related loading to eliminate conflicts
 */

// Path to WordPress
$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__FILE__, 4) . '/wp-load.php';
}

echo "<h1>Complete Payment System Reset</h1>";

// 1. Temporarily rename functions.php to prevent any loading
$functions_path = dirname(__FILE__) . '/functions.php';
$functions_backup = dirname(__FILE__) . '/functions-backup-temp.php';

if (file_exists($functions_path) && !file_exists($functions_backup)) {
    copy($functions_path, $functions_backup);
    echo "<p>✅ Backed up functions.php</p>";
}

// 2. Create a minimal functions.php
$minimal_functions = '<?php
/**
 * Minimal functions.php to test for conflicts
 */
 
// Prevent direct access
if (!defined("ABSPATH")) {
    exit;
}

// Only basic theme setup - NO payment includes
function kilismile_setup() {
    add_theme_support("post-thumbnails");
    add_theme_support("title-tag");
}
add_action("after_setup_theme", "kilismile_setup");

echo "<!-- MINIMAL FUNCTIONS LOADED - NO PAYMENTS -->";
';

file_put_contents($functions_path, $minimal_functions);
echo "<p>✅ Created minimal functions.php</p>";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>⚠️ TEMPORARY SETUP ACTIVE</h2>";
echo "<p><strong>What this does:</strong></p>";
echo "<ul>";
echo "<li>Replaces functions.php with minimal version</li>";
echo "<li>Eliminates ALL payment system loading from theme</li>";
echo "<li>Allows testing if standalone plugin works alone</li>";
echo "</ul>";
echo "<p><strong>To restore:</strong> Run the restore script or manually replace functions.php with functions-backup-temp.php</p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='test-standalone-plugin.php' style='background: #007cba; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>🧪 Test Standalone Plugin</a>";
echo "<a href='restore-functions.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔄 Restore Original</a>";
echo "</div>";

?>

