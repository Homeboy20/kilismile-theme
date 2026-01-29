<?php
/**
 * Restore Original Functions
 */

echo "<h1>Restoring Original Functions</h1>";

$functions_path = dirname(__FILE__) . '/functions.php';
$functions_backup = dirname(__FILE__) . '/functions-backup-temp.php';

if (file_exists($functions_backup)) {
    copy($functions_backup, $functions_path);
    unlink($functions_backup);
    echo "<p>✅ Restored original functions.php</p>";
    echo "<p>✅ Removed backup file</p>";
} else {
    echo "<p>❌ No backup file found</p>";
}

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='conflict-resolution-summary.php' style='background: #007cba; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>📋 View Status</a>";
echo "</div>";

?>

