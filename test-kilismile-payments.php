<?php
/**
 * Test KiliSmile Payments Plugin Loading
 */

// Include WordPress
require_once '../../../../wp-config.php';

echo "<h1>🔌 KiliSmile Payments Plugin Test</h1>\n";

// Test if plugin class exists
if (class_exists('KiliSmile_Payments_Plugin')) {
    echo "✅ Plugin class found\n<br>";
    
    try {
        $plugin = KiliSmile_Payments_Plugin::get_instance();
        echo "✅ Plugin instance created successfully\n<br>";
        
        // Check if constants are defined
        if (defined('KILISMILE_PAYMENTS_ACTIVE')) {
            echo "✅ Plugin constants defined\n<br>";
        } else {
            echo "⚠️ Plugin constants not defined\n<br>";
        }
        
        // Check if admin class is loaded
        if (class_exists('KiliSmile_Payments_Admin')) {
            echo "✅ Admin class loaded\n<br>";
        } else {
            echo "⚠️ Admin class not loaded\n<br>";
        }
        
        // Check if shortcode exists
        if (shortcode_exists('kilismile_donation_form')) {
            echo "✅ Donation form shortcode registered\n<br>";
        } else {
            echo "⚠️ Donation form shortcode not registered\n<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error creating plugin instance: " . $e->getMessage() . "\n<br>";
    }
    
} else {
    echo "❌ Plugin class not found\n<br>";
    echo "Checking plugin file...\n<br>";
    
    $plugin_file = get_template_directory() . '/kilismile-payments.php';
    if (file_exists($plugin_file)) {
        echo "✅ Plugin file exists at: $plugin_file\n<br>";
        echo "Attempting to load...\n<br>";
        
        try {
            include_once $plugin_file;
            
            if (class_exists('KiliSmile_Payments_Plugin')) {
                echo "✅ Plugin loaded successfully after manual include\n<br>";
            } else {
                echo "❌ Plugin class still not found after include\n<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error loading plugin: " . $e->getMessage() . "\n<br>";
        }
    } else {
        echo "❌ Plugin file not found at: $plugin_file\n<br>";
    }
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li>If plugin is working, go to <a href='" . admin_url('admin.php?page=kilismile-payments') . "'>WordPress Admin → KiliSmile Payments</a></li>";
echo "<li>Configure your PayPal and AzamPay settings</li>";
echo "<li>Test the donation form at <a href='" . home_url('/donations/') . "'>/donations/</a></li>";
echo "</ul>";

?>

