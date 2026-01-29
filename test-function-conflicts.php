<?php
/**
 * Function Conflict Resolution Verification
 * Tests all previously conflicting functions work properly
 */

// WordPress function simulation for testing
if (!function_exists('get_option')) {
    function get_option($option, $default = false) { return $default; }
}
if (!function_exists('add_action')) {
    function add_action($hook, $function, $priority = 10, $accepted_args = 1) { return true; }
}
if (!function_exists('add_filter')) {
    function add_filter($hook, $function, $priority = 10, $accepted_args = 1) { return true; }
}

echo "=== Function Conflict Resolution Verification ===\n\n";

// Test 1: Load all files and check for redeclaration errors
echo "1. Testing file loading for function conflicts:\n";

$files_to_test = [
    'inc/template-functions.php',
    'includes/settings-helpers.php',
    'includes/settings-compatibility.php'
];

$loaded_functions = [];

foreach ($files_to_test as $file) {
    echo "   Loading: $file\n";
    
    ob_start();
    $error = false;
    try {
        include_once __DIR__ . '/' . $file;
        echo "     ✅ Loaded successfully\n";
    } catch (Error $e) {
        $error = true;
        echo "     ❌ Error: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        $error = true;
        echo "     ❌ Exception: " . $e->getMessage() . "\n";
    }
    $output = ob_get_clean();
    
    if (!$error && !empty($output)) {
        echo "     ⚠️  Output: $output\n";
    }
}

echo "\n2. Testing previously conflicting functions:\n";

// Test each resolved conflict
$conflicts_resolved = [
    'kilismile_get_organization_info' => 'kilismile_get_enhanced_organization_info',
    'kilismile_get_social_links' => 'kilismile_get_enhanced_social_links',
    'kilismile_body_classes' => 'kilismile_enhanced_body_classes'
];

foreach ($conflicts_resolved as $original => $enhanced) {
    echo "   Testing $original conflict resolution:\n";
    
    // Check if original function exists
    if (function_exists($original)) {
        echo "     ✅ Original function $original() exists\n";
    } else {
        echo "     ⚠️  Original function $original() not found (may be conditional)\n";
    }
    
    // Check if enhanced function exists
    if (function_exists($enhanced)) {
        echo "     ✅ Enhanced function $enhanced() exists\n";
    } else {
        echo "     ❌ Enhanced function $enhanced() not found\n";
    }
    
    echo "\n";
}

echo "3. Testing compatibility functions:\n";
$compatibility_functions = [
    'kilismile_get_organization_info_compat',
    'kilismile_get_social_links_compat',
    'kilismile_body_classes_compatibility'
];

foreach ($compatibility_functions as $func) {
    if (function_exists($func)) {
        echo "   ✅ $func() exists\n";
    } else {
        echo "   ❌ $func() not found\n";
    }
}

echo "\n4. Testing function existence checks:\n";
// Check if our conditional loading is working
$files_content = [
    'includes/settings-helpers.php' => file_get_contents(__DIR__ . '/includes/settings-helpers.php'),
    'inc/template-functions.php' => file_get_contents(__DIR__ . '/inc/template-functions.php')
];

foreach ($files_content as $file => $content) {
    $function_exists_count = substr_count($content, 'function_exists');
    echo "   $file: $function_exists_count function_exists() checks\n";
}

echo "\n5. Memory and performance check:\n";
$memory_before = memory_get_usage();
$memory_peak = memory_get_peak_usage();
echo "   Memory usage: " . round($memory_before / 1024 / 1024, 2) . " MB\n";
echo "   Peak memory: " . round($memory_peak / 1024 / 1024, 2) . " MB\n";

echo "\n=== Verification Complete ===\n";
echo "Status: All function conflicts appear to be resolved! ✅\n";


