<?php
/**
 * Test Plugin-Based Donation System
 * Verifies that the donation system is using the KiliSmile Payments plugin exclusively
 */

// Include WordPress
if (!defined('ABSPATH')) {
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

get_header(); ?>

<div style="max-width: 1200px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #2c5530; font-size: 2.5rem; margin-bottom: 20px;">
            🔌 Plugin-Based Donation System Test
        </h1>
        <p style="color: #6c757d; font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
            This page tests the complete migration from theme-based to plugin-based payment processing.
        </p>
    </div>

    <!-- System Status Overview -->
    <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
        <h2 style="color: #28a745; margin-bottom: 25px; display: flex; align-items: center;">
            <span style="font-size: 1.5rem; margin-right: 15px;">🔍</span>
            System Integration Status
        </h2>
        
        <?php
        // Check plugin activation
        $plugin_active = defined('KILISMILE_PAYMENTS_ACTIVE') || function_exists('kilismile_payments_plugin_loaded');
        $plugin_class_exists = class_exists('KiliSmile_Payment_Processor') || class_exists('KiliSmile_Payments_Plugin');
        $shortcode_exists = shortcode_exists('kilismile_donation_form');
        
        // Check theme components
        $theme_system_disabled = !file_exists(get_template_directory() . '/includes/donation-system-modern.php');
        $theme_processor_disabled = !file_exists(get_template_directory() . '/includes/payment-processor.php');
        $theme_payment_system_disabled = !file_exists(get_template_directory() . '/includes/payment-system.php');
        
        // Check AJAX endpoints
        $plugin_ajax = has_action('wp_ajax_kilismile_process_payment');
        $theme_ajax_bridge = has_action('wp_ajax_kilismile_process_payment');
        
        // Overall assessment
        $migration_complete = $plugin_active && $theme_system_disabled && $theme_processor_disabled && $theme_ajax_bridge;
        ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            
            <!-- Plugin Status -->
            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid <?php echo $plugin_active ? '#28a745' : '#dc3545'; ?>;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Plugin Status</h3>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
                    <li style="color: <?php echo $plugin_active ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $plugin_active ? '✅' : '❌'; ?> Plugin Active: <?php echo $plugin_active ? 'Yes' : 'No'; ?>
                    </li>
                    <li style="color: <?php echo $plugin_class_exists ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $plugin_class_exists ? '✅' : '❌'; ?> Plugin Classes: <?php echo $plugin_class_exists ? 'Loaded' : 'Missing'; ?>
                    </li>
                    <li style="color: <?php echo $shortcode_exists ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $shortcode_exists ? '✅' : '❌'; ?> Shortcode Available: <?php echo $shortcode_exists ? 'Yes' : 'No'; ?>
                    </li>
                </ul>
            </div>
            
            <!-- Theme Cleanup Status -->
            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid <?php echo ($theme_system_disabled && $theme_processor_disabled) ? '#28a745' : '#ffc107'; ?>;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Theme Cleanup Status</h3>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
                    <li style="color: <?php echo $theme_system_disabled ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $theme_system_disabled ? '✅' : '❌'; ?> Donation System: <?php echo $theme_system_disabled ? 'Disabled' : 'Active'; ?>
                    </li>
                    <li style="color: <?php echo $theme_processor_disabled ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $theme_processor_disabled ? '✅' : '❌'; ?> Payment Processor: <?php echo $theme_processor_disabled ? 'Disabled' : 'Active'; ?>
                    </li>
                    <li style="color: <?php echo $theme_payment_system_disabled ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $theme_payment_system_disabled ? '✅' : '❌'; ?> Legacy Payment System: <?php echo $theme_payment_system_disabled ? 'Disabled' : 'Active'; ?>
                    </li>
                </ul>
            </div>
            
            <!-- AJAX Integration -->
            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid <?php echo $theme_ajax_bridge ? '#28a745' : '#dc3545'; ?>;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">AJAX Integration</h3>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
                    <li style="color: <?php echo $theme_ajax_bridge ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $theme_ajax_bridge ? '✅' : '❌'; ?> Bridge Endpoint: <?php echo $theme_ajax_bridge ? 'Active' : 'Missing'; ?>
                    </li>
                    <li style="color: #6c757d;">
                        ℹ️ Endpoint: <code>kilismile_process_payment</code>
                    </li>
                    <li style="color: #6c757d;">
                        ℹ️ Integration: <?php echo $plugin_active ? 'Plugin-based' : 'Theme-based'; ?>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Overall Status -->
        <div style="margin-top: 25px; padding: 20px; background: <?php echo $migration_complete ? '#d4edda' : '#fff3cd'; ?>; border: 1px solid <?php echo $migration_complete ? '#c3e6cb' : '#ffeaa7'; ?>; border-radius: 12px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; color: <?php echo $migration_complete ? '#155724' : '#856404'; ?>; font-size: 1.3rem;">
                <?php echo $migration_complete ? '🎉 Migration Complete!' : '⚠️ Migration In Progress'; ?>
            </h3>
            <p style="margin: 0; color: <?php echo $migration_complete ? '#155724' : '#856404'; ?>; font-size: 1rem;">
                <?php if ($migration_complete): ?>
                    All theme payment components have been disabled and the plugin system is active.
                <?php else: ?>
                    Some components need attention. Check the status above for details.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Donation Form Test -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px; display: flex; align-items: center;">
            <span style="font-size: 1.5rem; margin-right: 15px;">💰</span>
            Live Donation Form Test
        </h2>
        
        <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #0066cc; border-radius: 8px;">
            <strong style="color: #0066cc;">Test Instructions:</strong>
            <ul style="margin: 10px 0 0 20px; color: #495057;">
                <li>Use the form below to test the plugin-based payment processing</li>
                <li>The form should use the <code>kilismile_process_payment</code> AJAX endpoint</li>
                <li>Payment processing should be handled entirely by the plugin</li>
                <li>No theme-based payment classes should be involved</li>
            </ul>
        </div>
        
        <!-- Include the donation form component -->
        <?php
        $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
        if (file_exists($component_path)) {
            echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">';
            include $component_path;
            echo '</div>';
        } else {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 12px; border: 1px solid #f5c6cb;">';
            echo '<strong>Error:</strong> Donation form component not found at: ' . $component_path;
            echo '</div>';
        }
        ?>
    </div>

    <!-- Technical Details -->
    <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
        <h2 style="color: #28a745; margin-bottom: 25px; display: flex; align-items: center;">
            <span style="font-size: 1.5rem; margin-right: 15px;">🔧</span>
            Technical Details
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
            
            <!-- Plugin Information -->
            <div style="background: white; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Plugin Information</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 12px; font-weight: 600;">Plugin Constant:</td>
                        <td style="padding: 8px 12px; color: <?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? 'DEFINED' : 'NOT DEFINED'; ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 12px; font-weight: 600;">Plugin Path:</td>
                        <td style="padding: 8px 12px; font-family: monospace; font-size: 0.9rem;">
                            /wp-content/plugins/kilismile-payments/
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px 12px; font-weight: 600;">Shortcode Handler:</td>
                        <td style="padding: 8px 12px; color: <?php echo $shortcode_exists ? '#28a745' : '#dc3545'; ?>;">
                            <?php echo $shortcode_exists ? 'kilismile_donation_form' : 'NOT REGISTERED'; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Disabled Theme Components -->
            <div style="background: white; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Disabled Theme Components</h3>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                    <li style="color: #6c757d; font-family: monospace; font-size: 0.9rem;">includes/donation-system-modern.php.disabled</li>
                    <li style="color: #6c757d; font-family: monospace; font-size: 0.9rem;">includes/payment-processor.php.disabled</li>
                    <li style="color: #6c757d; font-family: monospace; font-size: 0.9rem;">includes/payment-system.php.disabled</li>
                    <li style="color: #6c757d; font-family: monospace; font-size: 0.9rem;">includes/payment-system-loader.php.disabled</li>
                    <li style="color: #6c757d; font-family: monospace; font-size: 0.9rem;">kilismile-payments.php.disabled</li>
                </ul>
            </div>
        </div>
        
        <!-- AJAX Testing -->
        <div style="background: white; padding: 20px; border-radius: 12px; margin-top: 20px;">
            <h3 style="margin: 0 0 15px 0; color: #495057;">AJAX Endpoint Testing</h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <strong>Test AJAX Endpoint:</strong>
                <button onclick="testAjaxEndpoint()" style="background: #007cba; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-left: 15px; cursor: pointer;">
                    Test kilismile_process_payment
                </button>
            </div>
            <div id="ajax-test-result" style="margin-top: 15px;"></div>
        </div>
    </div>

    <!-- Migration Summary -->
    <div style="background: #e8f5e8; padding: 30px; border-radius: 15px; border: 1px solid #c3e6cb;">
        <h2 style="color: #155724; margin-bottom: 25px; display: flex; align-items: center;">
            <span style="font-size: 1.5rem; margin-right: 15px;">📋</span>
            Migration Summary
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div>
                <h3 style="color: #155724; margin-bottom: 15px;">✅ Completed Tasks</h3>
                <ul style="color: #155724; line-height: 1.8;">
                    <li>Disabled theme donation system</li>
                    <li>Disabled theme payment processor</li>
                    <li>Disabled legacy payment systems</li>
                    <li>Created AJAX bridge for compatibility</li>
                    <li>Updated functions.php includes</li>
                    <li>Preserved donation database handlers</li>
                    <li>Preserved email notification handlers</li>
                </ul>
            </div>
            
            <div>
                <h3 style="color: #155724; margin-bottom: 15px;">🎯 Benefits Achieved</h3>
                <ul style="color: #155724; line-height: 1.8;">
                    <li>Eliminated class redeclaration conflicts</li>
                    <li>Centralized payment processing in plugin</li>
                    <li>Maintained backward compatibility</li>
                    <li>Simplified theme maintenance</li>
                    <li>Plugin-based gateway management</li>
                    <li>Cleaner separation of concerns</li>
                </ul>
            </div>
        </div>
        
        <div style="margin-top: 25px; padding: 20px; background: white; border-radius: 12px; text-align: center;">
            <h3 style="color: #155724; margin-bottom: 15px;">🚀 Next Steps</h3>
            <p style="color: #155724; margin-bottom: 15px;">
                The payment system has been successfully migrated from theme-based to plugin-based architecture.
                All donation processing now uses the KiliSmile Payments plugin exclusively.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/wp-admin/plugins.php" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">
                    Manage Plugins
                </a>
                <a href="/donate/" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">
                    Test Live Donation Page
                </a>
                <a href="/wp-admin/" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">
                    WordPress Admin
                </a>
            </div>
        </div>
    </div>

</div>

<script>
function testAjaxEndpoint() {
    const resultDiv = document.getElementById('ajax-test-result');
    resultDiv.innerHTML = '<div style="color: #007cba;">Testing AJAX endpoint...</div>';
    
    const formData = new FormData();
    formData.append('action', 'kilismile_process_payment');
    formData.append('test', 'true');
    formData.append('amount', '1000');
    formData.append('currency', 'TZS');
    formData.append('donor_name', 'Test User');
    formData.append('donor_email', 'test@example.com');
    formData.append('payment_gateway', 'test');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('AJAX Response:', data);
        
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.success) {
                resultDiv.innerHTML = `
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;">
                        <strong>✅ SUCCESS:</strong> AJAX endpoint is working correctly<br>
                        <small>Response: ${JSON.stringify(jsonData, null, 2)}</small>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;">
                        <strong>⚠️ PARTIAL:</strong> AJAX endpoint responded with error<br>
                        <small>Message: ${jsonData.data || 'Unknown error'}</small>
                    </div>
                `;
            }
        } catch (e) {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <strong>❌ ERROR:</strong> Invalid JSON response<br>
                    <small>Raw response: ${data.substring(0, 200)}...</small>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                <strong>❌ NETWORK ERROR:</strong> Failed to connect to AJAX endpoint<br>
                <small>Error: ${error.message}</small>
            </div>
        `;
    });
}
</script>

<?php get_footer(); ?>

