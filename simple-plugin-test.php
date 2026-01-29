<?php
/**
 * Simple Plugin Syntax Test
 * Tests the AzamPay plugin file for syntax errors without loading WordPress
 */

echo "Testing AzamPay Plugin Syntax\n";
echo "=============================\n\n";

// Check plugin file exists
$plugin_file = dirname(__FILE__) . '/../../../plugins/azampay-payment-gateway/azampay-payment-gateway.php';

if (!file_exists($plugin_file)) {
    echo "❌ Plugin file not found: $plugin_file\n";
    exit(1);
}

echo "✅ Plugin file found\n";

// Test PHP syntax
echo "Testing PHP syntax...\n";
$syntax_output = shell_exec("php -l \"$plugin_file\" 2>&1");

if (strpos($syntax_output, 'No syntax errors detected') !== false) {
    echo "✅ No syntax errors in main plugin file\n";
} else {
    echo "❌ Syntax errors found:\n";
    echo $syntax_output . "\n";
    exit(1);
}

// Test included files
$plugin_dir = dirname($plugin_file);
$files_to_check = [
    '/includes/class-azampay-api.php',
    '/includes/class-azampay-payment-processor.php',
    '/includes/class-azampay-database.php',
    '/includes/class-azampay-logger.php',
    '/admin/class-azampay-admin.php'
];

echo "\nChecking included files:\n";
foreach ($files_to_check as $file) {
    $full_path = $plugin_dir . $file;
    if (file_exists($full_path)) {
        $file_syntax = shell_exec("php -l \"$full_path\" 2>&1");
        if (strpos($file_syntax, 'No syntax errors detected') !== false) {
            echo "✅ $file - OK\n";
        } else {
            echo "❌ $file - Syntax Error:\n";
            echo $file_syntax . "\n";
        }
    } else {
        echo "⚠️  $file - File not found\n";
    }
}

// Read the main plugin file to check the activation hook
echo "\nChecking activation hook:\n";
$plugin_content = file_get_contents($plugin_file);

if (strpos($plugin_content, 'register_activation_hook') !== false) {
    echo "✅ Activation hook found\n";
    
    // Extract the activation hook line
    $lines = explode("\n", $plugin_content);
    foreach ($lines as $line_num => $line) {
        if (strpos($line, 'register_activation_hook') !== false) {
            echo "Line " . ($line_num + 1) . ": " . trim($line) . "\n";
            
            // Check if it's calling a static method correctly
            if (strpos($line, '::activate') !== false) {
                echo "✅ Using static method call\n";
            } else {
                echo "❌ Not using static method call - this will cause the error\n";
            }
        }
    }
} else {
    echo "⚠️  No activation hook found\n";
}

echo "\n📋 Summary:\n";
echo "If there are syntax errors above, fix them first.\n";
echo "If activation hook issues are found, those need to be corrected.\n";
echo "The original error suggests the activate() method is not static.\n\n";

echo "Original error was:\n";
echo "TypeError: call_user_func_array(): Argument #1 (\$callback) must be a valid callback, non-static method AzamPay_Gateway_Plugin::activate() cannot be called statically\n\n";

echo "To fix: Make sure the activate() method is declared as 'public static function activate()'\n";
echo "Test completed.\n";
?>

