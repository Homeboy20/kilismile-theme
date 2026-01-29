<?php
/**
 * Simple debug test - check if donation debug system loads
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #cce7ff; color: #0c5aa6; border: 1px solid #99d6ff; }
    </style>
</head>
<body>
    <h1>🐛 Debug System Test</h1>
    
    <?php
    // Include WordPress
    require_once '../../../../wp-config.php';
    
    echo '<div class="status info">✅ WordPress loaded successfully</div>';
    
    // Test debug tracker file
    $debug_tracker_path = get_template_directory() . '/includes/donation-debug-tracker.php';
    if (file_exists($debug_tracker_path)) {
        echo '<div class="status success">✅ Debug tracker file exists</div>';
        
        require_once $debug_tracker_path;
        
        if (class_exists('KiliSmile_Donation_Debug')) {
            echo '<div class="status success">✅ Debug tracker class loaded</div>';
            
            // Test logging
            try {
                KiliSmile_Donation_Debug::log_transaction('test_debug_system', array(
                    'test' => true,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ));
                echo '<div class="status success">✅ Debug logging test successful</div>';
                
                // Test database table
                global $wpdb;
                $table_name = $wpdb->prefix . 'kilismile_donation_debug';
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                echo '<div class="status info">📊 Debug table has ' . $count . ' entries</div>';
                
                // Get latest entries
                $latest = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 3");
                if ($latest) {
                    echo '<div class="status info">📝 Latest debug entries:</div>';
                    echo '<ul>';
                    foreach ($latest as $entry) {
                        echo '<li><strong>' . esc_html($entry->event_type) . '</strong> (' . esc_html($entry->log_level) . ') - ' . esc_html($entry->created_at) . '</li>';
                    }
                    echo '</ul>';
                }
                
            } catch (Exception $e) {
                echo '<div class="status error">❌ Debug logging failed: ' . esc_html($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="status error">❌ Debug tracker class not found</div>';
        }
    } else {
        echo '<div class="status error">❌ Debug tracker file not found at: ' . $debug_tracker_path . '</div>';
    }
    
    // Test donation form component
    $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
    if (file_exists($component_path)) {
        echo '<div class="status success">✅ Donation form component exists</div>';
    } else {
        echo '<div class="status error">❌ Donation form component not found</div>';
    }
    
    // Test JavaScript debug object
    echo '<div class="status info">🔧 Testing JavaScript debug integration...</div>';
    ?>
    
    <script>
        // Test if donation debug object would load
        console.log('Testing debug system...');
        
        // Simulate the donation form component JavaScript
        if (typeof window.donationDebug === 'undefined') {
            console.warn('⚠️ Donation debug object not loaded');
            document.write('<div class="status error">❌ JavaScript debug object not available</div>');
        } else {
            console.log('✅ Donation debug object loaded');
            document.write('<div class="status success">✅ JavaScript debug system available</div>');
        }
    </script>
    
    <hr>
    <p><strong>Next Steps:</strong></p>
    <ul>
        <li>Visit <a href="<?php echo home_url('/donations/'); ?>">/donations/</a> to test the full donation form</li>
        <li>Look for the 🐛 DEBUG button in the bottom-right corner</li>
        <li>Check browser console for debug messages</li>
    </ul>
    
    <hr>
    <p><strong>Troubleshooting:</strong></p>
    <ul>
        <li>Clear browser cache if debug button doesn't appear</li>
        <li>Check browser console for JavaScript errors</li>
        <li>Ensure donation form is fully loaded before looking for debug button</li>
    </ul>
    
</body>
</html>

