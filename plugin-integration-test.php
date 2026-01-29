<?php
/**
 * AzamPay Plugin Integration Test
 * Quick test script to verify the new plugin integration is working
 */

echo "<!DOCTYPE html>\n";
echo "<html><head><title>AzamPay Plugin Test</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;} .pass{color:green;} .fail{color:red;} .section{background:#f9f9f9;padding:15px;margin:10px 0;border-radius:5px;}</style>";
echo "</head><body>\n";

echo "<h1>🧪 AzamPay Plugin Integration Test</h1>\n";

// Test 1: Plugin Files
echo "<div class='section'><h2>1. Plugin Files Check</h2>\n";
$plugin_dir = dirname(__FILE__) . '/../../../plugins/azampay-payment-gateway/';
$plugin_main = $plugin_dir . 'azampay-payment-gateway.php';

if (file_exists($plugin_main)) {
    echo "<div class='pass'>✅ Plugin main file exists</div>\n";
} else {
    echo "<div class='fail'>❌ Plugin main file missing: $plugin_main</div>\n";
}

$required_files = [
    'includes/class-azampay-api.php',
    'includes/class-azampay-payment-processor.php', 
    'includes/class-azampay-database.php',
    'admin/class-azampay-admin.php',
    'assets/frontend.css',
    'assets/frontend.js'
];

foreach ($required_files as $file) {
    $path = $plugin_dir . $file;
    if (file_exists($path)) {
        echo "<div class='pass'>✅ $file</div>\n";
    } else {
        echo "<div class='fail'>❌ $file missing</div>\n";
    }
}
echo "</div>\n";

// Test 2: Theme Integration
echo "<div class='section'><h2>2. Theme Integration Check</h2>\n";
$integration_file = dirname(__FILE__) . '/includes/azampay-theme-integration.php';

if (file_exists($integration_file)) {
    echo "<div class='pass'>✅ Theme integration file exists</div>\n";
    
    // Read the file content to check functions
    $content = file_get_contents($integration_file);
    
    if (strpos($content, 'function kilismile_is_azampay_ready') !== false) {
        echo "<div class='pass'>✅ kilismile_is_azampay_ready() function defined</div>\n";
    } else {
        echo "<div class='fail'>❌ kilismile_is_azampay_ready() function missing</div>\n";
    }
    
    if (strpos($content, 'function kilismile_display_donation_form') !== false) {
        echo "<div class='pass'>✅ kilismile_display_donation_form() function defined</div>\n";
    } else {
        echo "<div class='fail'>❌ kilismile_display_donation_form() function missing</div>\n";
    }
} else {
    echo "<div class='fail'>❌ Theme integration file missing</div>\n";
}
echo "</div>\n";

// Test 3: Functions.php Integration
echo "<div class='section'><h2>3. Functions.php Integration</h2>\n";
$functions_file = dirname(__FILE__) . '/functions.php';
$functions_content = file_get_contents($functions_file);

if (strpos($functions_content, 'azampay-theme-integration.php') !== false) {
    echo "<div class='pass'>✅ Integration file included in functions.php</div>\n";
} else {
    echo "<div class='fail'>❌ Integration file not included in functions.php</div>\n";
}
echo "</div>\n";

// Test 4: Donation Page Update
echo "<div class='section'><h2>4. Donation Page Update</h2>\n";
$donate_page = dirname(__FILE__) . '/page-donate.php';
if (file_exists($donate_page)) {
    $donate_content = file_get_contents($donate_page);
    
    if (strpos($donate_content, 'kilismile_display_donation_form()') !== false) {
        echo "<div class='pass'>✅ Donation page uses plugin integration</div>\n";
    } else {
        echo "<div class='fail'>❌ Donation page not updated for plugin</div>\n";
    }
} else {
    echo "<div class='fail'>❌ Donation page file missing</div>\n";
}
echo "</div>\n";

// Test 5: Success Page Update
echo "<div class='section'><h2>5. Success Page Update</h2>\n";
$success_page = dirname(__FILE__) . '/page-donation-success.php';
if (file_exists($success_page)) {
    $success_content = file_get_contents($success_page);
    
    if (strpos($success_content, '$_GET[\'donation_amount\']') !== false) {
        echo "<div class='pass'>✅ Success page handles plugin parameters</div>\n";
    } else {
        echo "<div class='fail'>❌ Success page not updated for plugin</div>\n";
    }
} else {
    echo "<div class='fail'>❌ Success page file missing</div>\n";
}
echo "</div>\n";

// Test Summary
echo "<div class='section'><h2>📋 Integration Summary</h2>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Go to WordPress Admin → Plugins</li>\n";
echo "<li>Activate 'AzamPay Payment Gateway' plugin</li>\n";
echo "<li>Go to AzamPay Settings in admin</li>\n";
echo "<li>Configure your API credentials</li>\n";
echo "<li>Test the connection</li>\n";
echo "<li>Visit /donate page to see the new form</li>\n";
echo "</ol>\n";

echo "<p><strong>Benefits of Plugin Architecture:</strong></p>\n";
echo "<ul>\n";
echo "<li>✅ No more 404 API endpoint errors</li>\n";
echo "<li>✅ Centralized configuration and management</li>\n";
echo "<li>✅ Better security and WordPress standards compliance</li>\n";
echo "<li>✅ Comprehensive logging and debugging</li>\n";
echo "<li>✅ Easier maintenance and updates</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
echo "</body></html>";
?>

