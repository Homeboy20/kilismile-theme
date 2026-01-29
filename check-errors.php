<?php
/**
 * PHP Error Log Checker for KiliSmile Settings
 */

echo "=== PHP Error Log Check for KiliSmile Settings ===\n\n";

// Check common error log locations
$error_log_paths = [
    ini_get('error_log'),
    __DIR__ . '/error_log',
    __DIR__ . '/php_error_log',
    '/tmp/php_error_log',
    'C:\xampp\apache\logs\error.log',
    'C:\wamp64\logs\apache_error.log'
];

foreach ($error_log_paths as $path) {
    if ($path && file_exists($path)) {
        echo "Checking: $path\n";
        $content = file_get_contents($path);
        
        // Look for recent KiliSmile related errors
        $lines = explode("\n", $content);
        $recent_errors = [];
        
        foreach ($lines as $line) {
            if (stripos($line, 'kilismile') !== false || 
                stripos($line, 'settings-helpers') !== false ||
                stripos($line, 'enhanced-theme-settings') !== false) {
                $recent_errors[] = $line;
            }
        }
        
        if (!empty($recent_errors)) {
            echo "Found KiliSmile-related errors:\n";
            foreach (array_slice($recent_errors, -10) as $error) {
                echo "  - $error\n";
            }
        } else {
            echo "  ✅ No KiliSmile-related errors found\n";
        }
        echo "\n";
    }
}

// Test WordPress functions availability simulation
echo "Testing basic WordPress function simulation:\n";

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
    echo "  ✅ get_option() simulated\n";
}

if (!function_exists('add_action')) {
    function add_action($hook, $function, $priority = 10, $accepted_args = 1) {
        return true;
    }
    echo "  ✅ add_action() simulated\n";
}

// Now test our settings files
echo "\nTesting settings file loading with WordPress function simulation:\n";

try {
    include_once __DIR__ . '/includes/settings-helpers.php';
    echo "  ✅ settings-helpers.php loaded successfully\n";
} catch (Exception $e) {
    echo "  ❌ settings-helpers.php error: " . $e->getMessage() . "\n";
}

try {
    include_once __DIR__ . '/includes/settings-compatibility.php';
    echo "  ✅ settings-compatibility.php loaded successfully\n";
} catch (Exception $e) {
    echo "  ❌ settings-compatibility.php error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";


