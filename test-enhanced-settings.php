<?php
/**
 * Test Enhanced Settings System
 * Quick test to verify enhanced settings load properly
 */

// Simulate WordPress environment
define('ABSPATH', dirname(__FILE__) . '/../../../../');
define('WP_DEBUG', true);

echo "=== KiliSmile Enhanced Settings Test ===\n\n";

// Test 1: Check if enhanced settings files exist
echo "1. Testing file existence:\n";
$required_files = [
    'admin/enhanced-theme-settings.php',
    'admin/field-renderers.php', 
    'admin/settings-migration.php',
    'includes/settings-helpers.php',
    'includes/settings-compatibility.php'
];

foreach ($required_files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "   ✅ $file - EXISTS\n";
    } else {
        echo "   ❌ $file - MISSING\n";
    }
}

// Test 2: Check syntax of key files
echo "\n2. Testing file syntax:\n";
foreach ($required_files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $output = shell_exec("php -l \"$path\" 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "   ✅ $file - SYNTAX OK\n";
        } else {
            echo "   ❌ $file - SYNTAX ERROR: $output\n";
        }
    }
}

// Test 3: Check if classes and functions are properly defined
echo "\n3. Testing class/function definitions:\n";

// Include required files in order
try {
    include_once __DIR__ . '/includes/settings-helpers.php';
    echo "   ✅ settings-helpers.php loaded\n";
    
    include_once __DIR__ . '/includes/settings-compatibility.php';
    echo "   ✅ settings-compatibility.php loaded\n";
    
    include_once __DIR__ . '/admin/field-renderers.php';
    echo "   ✅ field-renderers.php loaded\n";
    
    include_once __DIR__ . '/admin/enhanced-theme-settings.php';
    echo "   ✅ enhanced-theme-settings.php loaded\n";
    
    // Check if main class exists
    if (class_exists('KiliSmile_Settings_Framework')) {
        echo "   ✅ KiliSmile_Settings_Framework class - DEFINED\n";
    } else {
        echo "   ❌ KiliSmile_Settings_Framework class - NOT FOUND\n";
    }
    
    // Check if key functions exist
    $functions = [
        'kilismile_get_setting',
        'kilismile_get_enhanced_organization_info',
        'kilismile_enhanced_body_classes'
    ];
    
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "   ✅ $func() - DEFINED\n";
        } else {
            echo "   ❌ $func() - NOT FOUND\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR loading files: " . $e->getMessage() . "\n";
}

echo "\n4. Testing settings sections:\n";
if (class_exists('KiliSmile_Settings_Framework')) {
    try {
        $framework = new KiliSmile_Settings_Framework();
        $sections = $framework->get_sections();
        
        $expected_sections = ['general', 'appearance', 'header', 'content', 'donations', 'social', 'performance', 'advanced'];
        
        foreach ($expected_sections as $section) {
            if (isset($sections[$section])) {
                echo "   ✅ $section section - CONFIGURED\n";
            } else {
                echo "   ❌ $section section - MISSING\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ ERROR testing sections: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Cannot test sections - KiliSmile_Settings_Framework not available\n";
}

echo "\n=== Test Complete ===\n";
echo "Enhanced settings system status: ";
if (class_exists('KiliSmile_Settings_Framework')) {
    echo "✅ READY\n";
} else {
    echo "❌ NOT READY\n";
}


