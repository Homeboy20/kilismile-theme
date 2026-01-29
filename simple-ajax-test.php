<?php
/**
 * Simple AJAX Test Page
 * Basic test to isolate AJAX issues
 */

// Include WordPress
if (!defined('ABSPATH')) {
    $wp_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_path . '/wp-load.php');
}

get_header(); ?>

<div style="max-width: 800px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
    
    <h1 style="color: #2c5530; text-align: center; margin-bottom: 30px;">
        🔧 Simple AJAX Test
    </h1>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3>Basic WordPress AJAX Test</h3>
        <button onclick="testBasicAjax()" style="background: #007cba; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            Test Basic AJAX
        </button>
        <div id="basic-test-result" style="margin-top: 15px; padding: 10px; background: white; border-radius: 5px; min-height: 50px;">
            <em>Click button to test...</em>
        </div>
    </div>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3>Bridge Function Test</h3>
        <button onclick="testBridgeFunction()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            Test Bridge Function
        </button>
        <div id="bridge-test-result" style="margin-top: 15px; padding: 10px; background: white; border-radius: 5px; min-height: 50px;">
            <em>Click button to test...</em>
        </div>
    </div>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h3>Plugin Status Check</h3>
        <div style="background: white; padding: 15px; border-radius: 5px;">
            <strong>WordPress Status:</strong><br>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>WordPress Version: <?php echo get_bloginfo('version'); ?></li>
                <li>Active Theme: <?php echo get_template(); ?></li>
                <li>Plugin Directory: <?php echo WP_PLUGIN_DIR; ?></li>
                <li>Debug Mode: <?php echo WP_DEBUG ? 'Enabled' : 'Disabled'; ?></li>
                <li>KiliSmile Plugin File: <?php echo file_exists(WP_PLUGIN_DIR . '/kilismile-payments/kilismile-payments.php') ? 'Found' : 'Missing'; ?></li>
            </ul>
            
            <?php
            // Check for active plugins
            $active_plugins = get_option('active_plugins', array());
            echo '<strong>Active Plugins:</strong><br>';
            echo '<ul style="margin: 10px 0; padding-left: 20px;">';
            foreach ($active_plugins as $plugin) {
                echo '<li>' . $plugin . '</li>';
            }
            echo '</ul>';
            ?>
        </div>
    </div>
    
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 10px;">
        <h3 style="color: #856404;">Error Debugging</h3>
        <p style="color: #856404;">
            If you see "Critical error" messages, check:
        </p>
        <ul style="color: #856404; margin: 10px 0; padding-left: 20px;">
            <li>WordPress error logs (wp-content/debug.log)</li>
            <li>PHP error logs</li>
            <li>Plugin conflicts</li>
            <li>Theme function syntax errors</li>
        </ul>
    </div>

</div>

<script>
function testBasicAjax() {
    const resultDiv = document.getElementById('basic-test-result');
    resultDiv.innerHTML = '<div style="color: #007cba;">Testing basic WordPress AJAX...</div>';
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=heartbeat&_nonce=<?php echo wp_create_nonce("heartbeat-nonce"); ?>'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text();
    })
    .then(data => {
        console.log('Raw response:', data);
        resultDiv.innerHTML = `
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">
                <strong>✅ Basic AJAX Working</strong><br>
                Response length: ${data.length} characters<br>
                <details style="margin-top: 10px;">
                    <summary>View Response</summary>
                    <pre style="background: white; padding: 10px; margin-top: 5px; font-size: 0.8rem; overflow-x: auto;">${data}</pre>
                </details>
            </div>
        `;
    })
    .catch(error => {
        console.error('AJAX error:', error);
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                <strong>❌ Basic AJAX Failed</strong><br>
                Error: ${error.message}
            </div>
        `;
    });
}

function testBridgeFunction() {
    const resultDiv = document.getElementById('bridge-test-result');
    resultDiv.innerHTML = '<div style="color: #28a745;">Testing bridge function...</div>';
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=kilismile_process_payment&test=true&amount=100&currency=TZS'
    })
    .then(response => {
        console.log('Bridge response status:', response.status);
        return response.text();
    })
    .then(data => {
        console.log('Bridge raw response:', data);
        
        // Check if it's a WordPress error page
        if (data.includes('There has been a critical error')) {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                    <strong>❌ Critical WordPress Error</strong><br>
                    The bridge function is causing a PHP error.<br>
                    <details style="margin-top: 10px;">
                        <summary>View Error Response</summary>
                        <div style="background: white; padding: 10px; margin-top: 5px; font-size: 0.8rem; max-height: 200px; overflow-y: auto;">${data}</div>
                    </details>
                </div>
            `;
            return;
        }
        
        try {
            const json = JSON.parse(data);
            resultDiv.innerHTML = `
                <div style="background: ${json.success ? '#d4edda' : '#fff3cd'}; color: ${json.success ? '#155724' : '#856404'}; padding: 10px; border-radius: 5px;">
                    <strong>${json.success ? '✅' : '⚠️'} Bridge Function Response</strong><br>
                    <pre style="background: white; padding: 10px; margin-top: 5px; font-size: 0.8rem;">${JSON.stringify(json, null, 2)}</pre>
                </div>
            `;
        } catch (e) {
            resultDiv.innerHTML = `
                <div style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px;">
                    <strong>⚠️ Invalid JSON Response</strong><br>
                    Parse error: ${e.message}<br>
                    <details style="margin-top: 10px;">
                        <summary>View Raw Response</summary>
                        <pre style="background: white; padding: 10px; margin-top: 5px; font-size: 0.8rem; max-height: 200px; overflow-y: auto;">${data}</pre>
                    </details>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Bridge test error:', error);
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                <strong>❌ Network Error</strong><br>
                Error: ${error.message}
            </div>
        `;
    });
}
</script>

<?php get_footer(); ?>

