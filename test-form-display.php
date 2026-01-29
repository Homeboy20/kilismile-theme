<?php
/**
 * Simple Donation Form Test Page
 * 
 * Direct URL: /wp-content/themes/kilismile/test-form-display.php
 */

// Load WordPress
require_once '../../../wp-load.php';

// Set up WordPress environment
if (!defined('ABSPATH')) {
    die('WordPress not found');
}

// Force template loading
get_header();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Form Test - <?php bloginfo('name'); ?></title>
    
    <!-- WordPress Head -->
    <?php wp_head(); ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .test-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e1e5e9;
        }
        .test-status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>

<body <?php body_class(); ?>>

<div class="test-container">
    <div class="test-header">
        <h1><i class="fas fa-heart"></i> Donation Form Visibility Test</h1>
        <p>Testing donation form display and functionality</p>
        <small>URL: <?php echo home_url($_SERVER['REQUEST_URI']); ?></small>
    </div>

    <!-- System Status Check -->
    <div class="system-status">
        <h2>🔍 System Status</h2>
        
        <?php
        // Check WordPress status
        echo '<div class="test-status status-success">✅ WordPress Loaded: ' . get_bloginfo('name') . '</div>';
        
        // Check theme
        $theme = wp_get_theme();
        echo '<div class="test-status status-success">✅ Active Theme: ' . $theme->get('Name') . '</div>';
        
        // Check if donation form template exists
        $template_path = get_template_directory() . '/templates/donation-form.php';
        if (file_exists($template_path)) {
            echo '<div class="test-status status-success">✅ Donation Form Template Found: ' . $template_path . '</div>';
        } else {
            echo '<div class="test-status status-error">❌ Donation Form Template Missing: ' . $template_path . '</div>';
        }
        
        // Check shortcodes
        global $shortcode_tags;
        $donation_shortcodes = array();
        foreach ($shortcode_tags as $tag => $function) {
            if (strpos($tag, 'donation') !== false || strpos($tag, 'kilismile') !== false) {
                $donation_shortcodes[] = $tag;
            }
        }
        
        if (!empty($donation_shortcodes)) {
            echo '<div class="test-status status-success">✅ Available Shortcodes: ' . implode(', ', $donation_shortcodes) . '</div>';
        } else {
            echo '<div class="test-status status-error">❌ No donation shortcodes found</div>';
        }
        
        // Check payment plugin
        if (class_exists('KiliSmile_Payments_Plugin')) {
            echo '<div class="test-status status-success">✅ KiliSmile Payments Plugin Active</div>';
        } else {
            echo '<div class="test-status status-error">❌ KiliSmile Payments Plugin Not Found</div>';
        }
        ?>
    </div>

    <!-- Template Direct Include Test -->
    <div class="template-test">
        <h2>📋 Template Direct Include Test</h2>
        <div style="border: 2px solid #007cba; border-radius: 10px; padding: 20px; margin: 20px 0; background: #f8f9fa;">
            <?php
            $template_path = get_template_directory() . '/templates/donation-form.php';
            if (file_exists($template_path)) {
                echo '<div class="test-status status-info">Loading template directly from: ' . $template_path . '</div>';
                try {
                    include $template_path;
                } catch (Exception $e) {
                    echo '<div class="test-status status-error">❌ Template Error: ' . $e->getMessage() . '</div>';
                }
            } else {
                echo '<div class="test-status status-error">❌ Template file not found</div>';
            }
            ?>
        </div>
    </div>

    <!-- Shortcode Test -->
    <div class="shortcode-test">
        <h2>🔧 Shortcode Test</h2>
        <?php if (shortcode_exists('kilismile_donation_form')): ?>
            <div class="test-status status-success">✅ kilismile_donation_form shortcode exists</div>
            <div style="border: 2px solid #28a745; border-radius: 10px; padding: 20px; margin: 20px 0; background: #f8fff8;">
                <h3>Shortcode Output:</h3>
                <?php echo do_shortcode('[kilismile_donation_form]'); ?>
            </div>
        <?php else: ?>
            <div class="test-status status-error">❌ kilismile_donation_form shortcode not found</div>
        <?php endif; ?>
    </div>

    <!-- JavaScript Test -->
    <div class="js-test">
        <h2>⚡ JavaScript Test</h2>
        <div id="js-status" class="test-status status-info">Testing JavaScript...</div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('js-status');
            
            // Test jQuery
            if (typeof jQuery !== 'undefined') {
                statusDiv.innerHTML = '✅ jQuery Loaded (Version: ' + jQuery.fn.jquery + ')';
                statusDiv.className = 'test-status status-success';
                
                // Test for form elements
                setTimeout(function() {
                    const forms = document.querySelectorAll('form, .donation-form, .kilismile-donation-form');
                    if (forms.length > 0) {
                        statusDiv.innerHTML += '<br>✅ Found ' + forms.length + ' form element(s)';
                    } else {
                        statusDiv.innerHTML += '<br>❌ No form elements found in DOM';
                    }
                    
                    // Test for hidden elements
                    const hiddenElements = document.querySelectorAll('[style*="display: none"], [style*="display:none"]');
                    if (hiddenElements.length > 0) {
                        statusDiv.innerHTML += '<br>⚠️ Found ' + hiddenElements.length + ' hidden element(s)';
                    }
                }, 1000);
                
            } else {
                statusDiv.innerHTML = '❌ jQuery Not Loaded';
                statusDiv.className = 'test-status status-error';
            }
        });
        </script>
    </div>

    <!-- Manual Form Test -->
    <div class="manual-test">
        <h2>🧪 Manual Form Test</h2>
        <div style="border: 2px solid #ffc107; border-radius: 10px; padding: 20px; margin: 20px 0; background: #fffbf0;">
            <h3>Simple HTML Form Test:</h3>
            <form style="max-width: 400px; margin: 20px 0;">
                <div style="margin: 15px 0;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Donation Amount:</label>
                    <input type="number" value="25000" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px;">
                </div>
                <div style="margin: 15px 0;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Your Name:</label>
                    <input type="text" placeholder="Enter your name" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px;">
                </div>
                <button type="button" onclick="alert('Manual form test successful!')" style="width: 100%; padding: 15px; background: #007cba; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;">
                    Test Donate Button
                </button>
            </form>
        </div>
    </div>

</div>

<?php wp_footer(); ?>

</body>
</html>