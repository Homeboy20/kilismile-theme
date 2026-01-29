<?php
/**
 * Test Donations Page Functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Include WordPress
    require_once('../../../wp-load.php');
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>Donations Page Debugging</h1>";

// Test 1: Check if donation form function exists
echo "<h2>1. Function Tests</h2>";
if (function_exists('kilismile_donation_form')) {
    echo "✅ kilismile_donation_form() function exists<br>";
    try {
        $form_output = kilismile_donation_form();
        echo "✅ Function executed successfully<br>";
        echo "Form output length: " . strlen($form_output) . " characters<br>";
        if (strpos($form_output, 'kilismile_donation_form not available') !== false) {
            echo "❌ Function returned 'not available' message<br>";
        }
    } catch (Exception $e) {
        echo "❌ Function threw exception: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ kilismile_donation_form() function does not exist<br>";
}

// Test 2: Check if shortcode exists
echo "<h2>2. Shortcode Tests</h2>";
if (shortcode_exists('kilismile_donation_form')) {
    echo "✅ kilismile_donation_form shortcode exists<br>";
} else {
    echo "❌ kilismile_donation_form shortcode does not exist<br>";
}

// Test 3: Check if template files exist
echo "<h2>3. Template File Tests</h2>";
$template_files = [
    'templates/donation-form.php',
    'template-parts/donation-form-component.php'
];

foreach ($template_files as $file) {
    $path = get_template_directory() . '/' . $file;
    if (file_exists($path)) {
        echo "✅ {$file} exists<br>";
        echo "File size: " . filesize($path) . " bytes<br>";
    } else {
        echo "❌ {$file} does not exist<br>";
    }
}

// Test 4: Check if payment processor classes exist
echo "<h2>4. Payment Processor Tests</h2>";
if (class_exists('KiliSmile_Payment_Processor')) {
    echo "✅ KiliSmile_Payment_Processor class exists<br>";
} else {
    echo "❌ KiliSmile_Payment_Processor class does not exist<br>";
}

if (class_exists('KiliSmile_AzamPay')) {
    echo "✅ KiliSmile_AzamPay class exists<br>";
} else {
    echo "❌ KiliSmile_AzamPay class does not exist<br>";
}

// Test 5: Check AzamPay settings
echo "<h2>5. AzamPay Configuration Tests</h2>";
$azampay_settings = [
    'kilismile_azampay_enabled' => get_option('kilismile_azampay_enabled', false),
    'kilismile_azampay_client_id' => get_option('kilismile_azampay_client_id', ''),
    'kilismile_azampay_client_secret' => get_option('kilismile_azampay_client_secret', ''),
    'kilismile_azampay_app_name' => get_option('kilismile_azampay_app_name', ''),
    'kilismile_azampay_sandbox' => get_option('kilismile_azampay_sandbox', true),
];

foreach ($azampay_settings as $key => $value) {
    $status = !empty($value) ? "✅" : "❌";
    $display_value = ($key === 'kilismile_azampay_client_secret') ? '[HIDDEN]' : $value;
    echo "{$status} {$key}: " . (is_bool($display_value) ? ($display_value ? 'true' : 'false') : $display_value) . "<br>";
}

// Test 6: Check AJAX actions
echo "<h2>6. AJAX Action Tests</h2>";
$ajax_actions = [
    'kilismile_process_payment',
    'azampay_callback',
    'kilismile_check_payment_status'
];

foreach ($ajax_actions as $action) {
    if (has_action('wp_ajax_' . $action) || has_action('wp_ajax_nopriv_' . $action)) {
        echo "✅ {$action} AJAX action is registered<br>";
    } else {
        echo "❌ {$action} AJAX action is not registered<br>";
    }
}

// Test 7: Try to load enhanced donation form directly
echo "<h2>7. Direct Template Loading Test</h2>";
$enhanced_form_path = get_template_directory() . '/templates/donation-form.php';
if (file_exists($enhanced_form_path)) {
    echo "✅ Enhanced donation form template exists<br>";
    echo "<strong>Attempting to load enhanced form:</strong><br>";
    echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0; max-height: 400px; overflow: auto;'>";
    
    try {
        // Set up the variables that the form expects
        $suggested_amounts = array(
            'TZS' => array(10000, 25000, 50000, 100000, 250000),
            'USD' => array(5, 10, 25, 50, 100)
        );
        $default_currency = 'TZS';
        $args = array(
            'class' => 'kilismile-donation-form enhanced-donation-form',
            'show_recurring' => true,
            'show_anonymous' => true,
            'submit_text' => __('Complete Donation', 'kilismile'),
            'show_amounts' => true
        );
        
        ob_start();
        include $enhanced_form_path;
        $form_content = ob_get_clean();
        
        echo "✅ Enhanced form loaded successfully<br>";
        echo "Form content length: " . strlen($form_content) . " characters<br>";
        
        if (strlen($form_content) > 100) {
            echo "<details><summary>Show form preview (click to expand)</summary>";
            echo htmlspecialchars(substr($form_content, 0, 1000));
            if (strlen($form_content) > 1000) echo "... [truncated]";
            echo "</details>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error loading enhanced form: " . $e->getMessage() . "<br>";
    }
    
    echo "</div>";
} else {
    echo "❌ Enhanced donation form template does not exist<br>";
}

echo "<h2>8. Recommendations</h2>";
echo "<p>If you're seeing issues with donations not working, check the following:</p>";
echo "<ol>";
echo "<li>Ensure AzamPay is enabled and configured with valid credentials</li>";
echo "<li>Check that all required classes and functions are loaded</li>";
echo "<li>Verify AJAX actions are properly registered</li>";
echo "<li>Test the enhanced donation form directly</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "</ol>";
?>

