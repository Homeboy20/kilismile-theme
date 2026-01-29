<?php
/**
 * Plugin Compatibility Check
 * Analyzes the KiliSmile Payments plugin and provides integration recommendations
 */

// Include WordPress
if (!defined('ABSPATH')) {
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

// Check if we can access plugin files
$plugin_path = WP_PLUGIN_DIR . '/kilismile-payments/kilismile-payments.php';
$plugin_exists = file_exists($plugin_path);

// Check if plugin is active
$active_plugins = get_option('active_plugins', array());
$plugin_active = in_array('kilismile-payments/kilismile-payments.php', $active_plugins);

get_header(); ?>

<div style="max-width: 1000px; margin: 40px auto; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #2c5530; font-size: 2.5rem; margin-bottom: 20px;">
            🔌 Plugin Compatibility Analysis
        </h1>
        <p style="color: #6c757d; font-size: 1.1rem;">
            Comprehensive analysis of KiliSmile Payments plugin integration.
        </p>
    </div>

    <!-- Plugin Status -->
    <div style="background: <?php echo $plugin_active ? '#d4edda' : '#fff3cd'; ?>; padding: 30px; border-radius: 15px; margin-bottom: 30px; border: 1px solid <?php echo $plugin_active ? '#c3e6cb' : '#ffeaa7'; ?>;">
        <h2 style="color: <?php echo $plugin_active ? '#155724' : '#856404'; ?>; margin-bottom: 20px;">
            <?php echo $plugin_active ? '✅' : '⚠️'; ?> Plugin Status
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div>
                <h3 style="margin: 0 0 15px 0; color: <?php echo $plugin_active ? '#155724' : '#856404'; ?>;">Basic Information</h3>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.8; color: <?php echo $plugin_active ? '#155724' : '#856404'; ?>;">
                    <li><strong>Plugin File:</strong> <?php echo $plugin_exists ? 'Found' : 'Missing'; ?></li>
                    <li><strong>Plugin Active:</strong> <?php echo $plugin_active ? 'Yes' : 'No'; ?></li>
                    <li><strong>Plugin Path:</strong> <code>/wp-content/plugins/kilismile-payments/</code></li>
                    <li><strong>Constant Defined:</strong> <?php echo defined('KILISMILE_PAYMENTS_ACTIVE') ? 'Yes' : 'No'; ?></li>
                </ul>
            </div>
            
            <?php if ($plugin_exists): ?>
            <div>
                <h3 style="margin: 0 0 15px 0; color: <?php echo $plugin_active ? '#155724' : '#856404'; ?>;">Plugin Details</h3>
                <?php
                $plugin_data = get_plugin_data($plugin_path);
                ?>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.8; color: <?php echo $plugin_active ? '#155724' : '#856404'; ?>;">
                    <li><strong>Name:</strong> <?php echo $plugin_data['Name']; ?></li>
                    <li><strong>Version:</strong> <?php echo $plugin_data['Version']; ?></li>
                    <li><strong>Description:</strong> <?php echo substr($plugin_data['Description'], 0, 60) . '...'; ?></li>
                    <li><strong>Author:</strong> <?php echo $plugin_data['Author']; ?></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!$plugin_active): ?>
        <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 8px;">
            <strong>Action Required:</strong> 
            <a href="<?php echo admin_url('plugins.php'); ?>" style="color: #007cba; text-decoration: none;">
                Activate the KiliSmile Payments plugin in WordPress Admin
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Available Classes and Methods -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px;">🔍 Available Components</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
            
            <!-- Plugin Classes -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Plugin Classes</h3>
                <?php
                $plugin_classes = array(
                    'KiliSmile_Payment_Processor',
                    'KiliSmile_Payment_Gateway_Base', 
                    'KiliSmile_Payment_Gateway_Factory',
                    'KiliSmile_Modern_Donation_System',
                    'KiliSmile_PayPal_Gateway',
                    'KiliSmile_AzamPay_Gateway'
                );
                
                foreach ($plugin_classes as $class) {
                    $exists = class_exists($class);
                    echo '<div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6;">';
                    echo '<span style="font-family: monospace; font-size: 0.9rem;">' . $class . '</span>';
                    echo '<span style="color: ' . ($exists ? '#28a745' : '#dc3545') . ';">' . ($exists ? '✅' : '❌') . '</span>';
                    echo '</div>';
                    
                    // If class exists, show available methods
                    if ($exists) {
                        $methods = get_class_methods($class);
                        if ($methods) {
                            echo '<div style="margin-left: 20px; font-size: 0.8rem; color: #6c757d; margin-bottom: 10px;">';
                            $payment_methods = array_filter($methods, function($method) {
                                return strpos($method, 'process') !== false || 
                                       strpos($method, 'ajax') !== false ||
                                       strpos($method, 'payment') !== false;
                            });
                            if (!empty($payment_methods)) {
                                echo 'Payment methods: ' . implode(', ', array_slice($payment_methods, 0, 3));
                                if (count($payment_methods) > 3) echo '...';
                            }
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            
            <!-- Plugin Functions -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Plugin Functions</h3>
                <?php
                $plugin_functions = array(
                    'kilismile_payments_plugin_loaded',
                    'kilismile_payments_process_payment',
                    'kilismile_process_payment_request',
                    'kilismile_donation_form_shortcode',
                    'azampay_process_payment'
                );
                
                foreach ($plugin_functions as $function) {
                    $exists = function_exists($function);
                    echo '<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6;">';
                    echo '<span style="font-family: monospace; font-size: 0.9rem;">' . $function . '</span>';
                    echo '<span style="color: ' . ($exists ? '#28a745' : '#dc3545') . ';">' . ($exists ? '✅' : '❌') . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- AJAX Handlers Analysis -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px;">🎯 AJAX Integration Strategy</h2>
        
        <?php
        // Check what AJAX handlers are available
        global $wp_filter;
        $ajax_handlers = array();
        
        $ajax_actions = array(
            'wp_ajax_kilismile_process_payment',
            'wp_ajax_nopriv_kilismile_process_payment',
            'wp_ajax_azampay_process_payment',
            'wp_ajax_nopriv_azampay_process_payment'
        );
        
        foreach ($ajax_actions as $action) {
            if (isset($wp_filter[$action]) && !empty($wp_filter[$action]->callbacks)) {
                $ajax_handlers[$action] = $wp_filter[$action]->callbacks;
            }
        }
        ?>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0; color: #495057;">Current AJAX Handlers</h3>
            
            <?php if (!empty($ajax_handlers)): ?>
                <?php foreach ($ajax_handlers as $action => $callbacks): ?>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #28a745;"><?php echo $action; ?></strong>
                        <div style="margin-left: 20px; margin-top: 5px;">
                            <?php foreach ($callbacks as $priority => $handler_callbacks): ?>
                                <?php foreach ($handler_callbacks as $callback): ?>
                                    <div style="font-family: monospace; font-size: 0.9rem; color: #6c757d;">
                                        Priority <?php echo $priority; ?>: 
                                        <?php 
                                        if (is_array($callback['function'])) {
                                            if (is_object($callback['function'][0])) {
                                                echo get_class($callback['function'][0]) . '::' . $callback['function'][1];
                                            } else {
                                                echo $callback['function'][0] . '::' . $callback['function'][1];
                                            }
                                        } else {
                                            echo $callback['function'];
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="color: #dc3545;">❌ No payment-related AJAX handlers found</div>
            <?php endif; ?>
        </div>
        
        <!-- Recommended Integration -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 12px; border-left: 4px solid #0066cc;">
            <h3 style="margin: 0 0 15px 0; color: #0066cc;">💡 Recommended Integration Approach</h3>
            
            <?php if ($plugin_active && !empty($ajax_handlers)): ?>
                <div style="color: #0066cc;">
                    <strong>✅ Plugin is active with AJAX handlers</strong>
                    <p style="margin: 10px 0;">The bridge function should work correctly. If you're still getting errors, the plugin may need specific parameters or authentication.</p>
                </div>
            <?php elseif ($plugin_active): ?>
                <div style="color: #856404;">
                    <strong>⚠️ Plugin active but no AJAX handlers</strong>
                    <p style="margin: 10px 0;">The plugin may use shortcodes instead of direct AJAX. Consider updating the bridge to use shortcode processing or check plugin documentation.</p>
                </div>
            <?php else: ?>
                <div style="color: #dc3545;">
                    <strong>❌ Plugin not active</strong>
                    <p style="margin: 10px 0;">Activate the KiliSmile Payments plugin to enable payment processing functionality.</p>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 15px;">
                <strong>Implementation Options:</strong>
                <ul style="margin: 10px 0 0 20px; line-height: 1.6;">
                    <li>Direct AJAX integration (if handlers exist)</li>
                    <li>Shortcode-based processing</li>
                    <li>Class method invocation</li>
                    <li>Function-based bridge</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Test Integration -->
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #28a745; margin-bottom: 25px;">🧪 Test Plugin Integration</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <button onclick="testBridgeFunction()" style="background: #007cba; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer;">
                Test Bridge Function
            </button>
            <button onclick="testDirectAjax()" style="background: #28a745; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer;">
                Test Direct AJAX
            </button>
            <button onclick="testShortcode()" style="background: #6f42c1; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer;">
                Test Shortcode
            </button>
            <button onclick="testClassMethod()" style="background: #fd7e14; color: white; border: none; padding: 15px; border-radius: 8px; cursor: pointer;">
                Test Class Method
            </button>
        </div>
        
        <div id="integration-test-results" style="background: #f8f9fa; padding: 20px; border-radius: 8px; min-height: 100px;">
            <em style="color: #6c757d;">Click a test button to check integration methods...</em>
        </div>
    </div>

</div>

<script>
function testBridgeFunction() {
    const resultDiv = document.getElementById('integration-test-results');
    resultDiv.innerHTML = '<div style="color: #007cba;">Testing bridge function...</div>';
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=kilismile_process_payment&test=true&amount=1000&currency=TZS'
    })
    .then(response => response.text())
    .then(data => {
        console.log('Bridge test:', data);
        try {
            const json = JSON.parse(data);
            resultDiv.innerHTML = `
                <div style="background: ${json.success ? '#d4edda' : '#fff3cd'}; color: ${json.success ? '#155724' : '#856404'}; padding: 15px; border-radius: 8px;">
                    <strong>${json.success ? '✅' : '⚠️'} Bridge Function:</strong> ${json.success ? 'Working' : 'Error'}
                    <pre style="margin-top: 10px; background: white; padding: 10px; border-radius: 4px; font-size: 0.85rem;">${JSON.stringify(json, null, 2)}</pre>
                </div>
            `;
        } catch (e) {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;">
                    <strong>❌ Bridge Error:</strong> ${e.message}
                    <pre style="margin-top: 10px; background: white; padding: 10px; border-radius: 4px; font-size: 0.85rem;">${data}</pre>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<div style="color: #dc3545;">❌ Network error: ${error.message}</div>`;
    });
}

function testDirectAjax() {
    const resultDiv = document.getElementById('integration-test-results');
    resultDiv.innerHTML = '<div style="color: #28a745;">Testing direct AJAX...</div>';
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=azampay_process_payment&amount=1000&currency=TZS'
    })
    .then(response => response.text())
    .then(data => {
        console.log('Direct AJAX test:', data);
        resultDiv.innerHTML = `
            <div style="background: #e7f3ff; color: #0066cc; padding: 15px; border-radius: 8px;">
                <strong>📡 Direct AJAX Response:</strong>
                <pre style="margin-top: 10px; background: white; padding: 10px; border-radius: 4px; font-size: 0.85rem;">${data}</pre>
            </div>
        `;
    })
    .catch(error => {
        resultDiv.innerHTML = `<div style="color: #dc3545;">❌ Network error: ${error.message}</div>`;
    });
}

function testShortcode() {
    const resultDiv = document.getElementById('integration-test-results');
    resultDiv.innerHTML = '<div style="color: #6f42c1;">Testing shortcode availability...</div>';
    
    // This is a client-side test for shortcode existence
    const shortcodeExists = <?php echo shortcode_exists('kilismile_donation_form') ? 'true' : 'false'; ?>;
    
    resultDiv.innerHTML = `
        <div style="background: ${shortcodeExists ? '#d4edda' : '#f8d7da'}; color: ${shortcodeExists ? '#155724' : '#721c24'}; padding: 15px; border-radius: 8px;">
            <strong>${shortcodeExists ? '✅' : '❌'} Shortcode Test:</strong> kilismile_donation_form ${shortcodeExists ? 'is available' : 'not found'}
            ${shortcodeExists ? '<div style="margin-top: 10px;">You can use [kilismile_donation_form] to display the donation form.</div>' : ''}
        </div>
    `;
}

function testClassMethod() {
    const resultDiv = document.getElementById('integration-test-results');
    resultDiv.innerHTML = '<div style="color: #fd7e14;">Testing class availability...</div>';
    
    const classExists = <?php echo class_exists('KiliSmile_Payment_Processor') ? 'true' : 'false'; ?>;
    
    resultDiv.innerHTML = `
        <div style="background: ${classExists ? '#d4edda' : '#f8d7da'}; color: ${classExists ? '#155724' : '#721c24'}; padding: 15px; border-radius: 8px;">
            <strong>${classExists ? '✅' : '❌'} Class Test:</strong> KiliSmile_Payment_Processor ${classExists ? 'is available' : 'not found'}
            ${classExists ? '<div style="margin-top: 10px;">Bridge can instantiate the class and call payment methods.</div>' : ''}
        </div>
    `;
}
</script>

<?php get_footer(); ?>

