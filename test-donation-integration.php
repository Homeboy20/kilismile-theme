<?php
/**
 * Test Donation Integration
 * Tests the AzamPay payment collection functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

get_header(); ?>

<div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #2c5530; font-size: 2.5rem; margin-bottom: 20px;">Donation Integration Test</h1>
        <p style="color: #6c757d; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
            Test the complete donation flow with AzamPay integration using the provided credentials.
        </p>
    </div>
    
    <!-- Integration Status -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #2c5530; margin-bottom: 20px;">Integration Status</h2>
        
        <?php
        // Check if required files exist
        $azampay_file = get_template_directory() . '/includes/azampay-integration.php';
        $payment_processor_file = get_template_directory() . '/includes/payment-processor.php';
        $donation_form_file = get_template_directory() . '/templates/donation-form.php';
        
        $files_status = array(
            'AzamPay Integration' => file_exists($azampay_file),
            'Payment Processor' => file_exists($payment_processor_file),
            'Donation Form Template' => file_exists($donation_form_file)
        );
        
        // Check if classes are loaded
        $classes_status = array(
            'KiliSmile_AzamPay' => class_exists('KiliSmile_AzamPay'),
            'KiliSmile_Payment_Processor' => class_exists('KiliSmile_Payment_Processor'),
            'KiliSmile_Donation_Database' => class_exists('KiliSmile_Donation_Database')
        );
        
        // Check AJAX actions
        $ajax_status = array(
            'kilismile_process_payment' => has_action('wp_ajax_kilismile_process_payment'),
            'kilismile_check_payment_status' => has_action('wp_ajax_kilismile_check_payment_status')
        );
        
        // Check AzamPay configuration
        $azampay_client_id = get_option('azampay_client_id', '');
        $azampay_client_secret = get_option('azampay_client_secret', '');
        $azampay_app_name = get_option('azampay_app_name', '');
        
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">';
        
        // Files Status
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">';
        echo '<h3 style="margin-top: 0; color: #495057;">Required Files</h3>';
        foreach ($files_status as $file => $exists) {
            $status = $exists ? '✅' : '❌';
            $color = $exists ? '#28a745' : '#dc3545';
            echo "<div style='margin-bottom: 8px; color: {$color};'>{$status} {$file}</div>";
        }
        echo '</div>';
        
        // Classes Status
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">';
        echo '<h3 style="margin-top: 0; color: #495057;">PHP Classes</h3>';
        foreach ($classes_status as $class => $loaded) {
            $status = $loaded ? '✅' : '❌';
            $color = $loaded ? '#28a745' : '#dc3545';
            echo "<div style='margin-bottom: 8px; color: {$color};'>{$status} {$class}</div>";
        }
        echo '</div>';
        
        // AJAX Status
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">';
        echo '<h3 style="margin-top: 0; color: #495057;">AJAX Handlers</h3>';
        foreach ($ajax_status as $action => $registered) {
            $status = $registered ? '✅' : '❌';
            $color = $registered ? '#28a745' : '#dc3545';
            echo "<div style='margin-bottom: 8px; color: {$color};'>{$status} {$action}</div>";
        }
        echo '</div>';
        
        // Configuration Status
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">';
        echo '<h3 style="margin-top: 0; color: #495057;">AzamPay Configuration</h3>';
        
        $config_items = array(
            'Client ID' => !empty($azampay_client_id),
            'Client Secret' => !empty($azampay_client_secret), 
            'App Name' => !empty($azampay_app_name)
        );
        
        foreach ($config_items as $item => $configured) {
            $status = $configured ? '✅' : '❌';
            $color = $configured ? '#28a745' : '#dc3545';
            echo "<div style='margin-bottom: 8px; color: {$color};'>{$status} {$item}</div>";
        }
        
        echo '</div>';
        echo '</div>';
        ?>
    </div>
    
    <!-- Configuration Details -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #2c5530; margin-bottom: 20px;">Current Configuration</h2>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; font-family: monospace;">
            <div style="margin-bottom: 10px;"><strong>Client ID:</strong> <?php echo $azampay_client_id ?: 'Not configured'; ?></div>
            <div style="margin-bottom: 10px;"><strong>App Name:</strong> <?php echo $azampay_app_name ?: 'Not configured'; ?></div>
            <div style="margin-bottom: 10px;"><strong>Environment:</strong> <?php echo get_option('azampay_sandbox_mode', '1') ? 'Sandbox' : 'Production'; ?></div>
            <div style="margin-bottom: 10px;"><strong>API Base URL:</strong> <?php echo get_option('azampay_sandbox_mode', '1') ? 'https://sandbox.azampay.co.tz' : 'https://api.azampay.co.tz'; ?></div>
        </div>
        
        <?php if (empty($azampay_client_id) || empty($azampay_client_secret)): ?>
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; color: #856404;">
            <strong>⚠️ Configuration Required:</strong> Please configure your AzamPay credentials in the 
            <a href="<?php echo admin_url('admin.php?page=payment-settings'); ?>" style="color: #007bff;">Payment Settings</a> page.
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Test Actions -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #2c5530; margin-bottom: 20px;">Test Actions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <button onclick="testAzamPayConnection()" style="background: #28a745; color: white; border: none; padding: 15px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                Test AzamPay Connection
            </button>
            
            <button onclick="testPaymentForm()" style="background: #007bff; color: white; border: none; padding: 15px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                Test Payment Form
            </button>
            
            <a href="<?php echo home_url('/donate/'); ?>" style="background: #17a2b8; color: white; text-decoration: none; padding: 15px 20px; border-radius: 8px; font-weight: 600; text-align: center; display: block; transition: all 0.3s ease;">
                View Donation Page
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=payment-settings'); ?>" style="background: #6c757d; color: white; text-decoration: none; padding: 15px 20px; border-radius: 8px; font-weight: 600; text-align: center; display: block; transition: all 0.3s ease;">
                Payment Settings
            </a>
        </div>
    </div>
    
    <!-- Test Results -->
    <div id="test-results" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: none;">
        <h2 style="color: #2c5530; margin-bottom: 20px;">Test Results</h2>
        <div id="test-output" style="background: #f8f9fa; padding: 20px; border-radius: 8px; font-family: monospace; white-space: pre-wrap;"></div>
    </div>
    
</div>

<script>
function testAzamPayConnection() {
    showTestResults();
    updateTestOutput('Testing AzamPay connection...\n');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'test_azampay_connection',
            nonce: '<?php echo wp_create_nonce('test_azampay'); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTestOutput('✅ AzamPay connection successful!\n');
            updateTestOutput('Response: ' + JSON.stringify(data.data, null, 2) + '\n');
        } else {
            updateTestOutput('❌ AzamPay connection failed!\n');
            updateTestOutput('Error: ' + data.data + '\n');
        }
    })
    .catch(error => {
        updateTestOutput('❌ Test failed with error: ' + error.message + '\n');
    });
}

function testPaymentForm() {
    showTestResults();
    updateTestOutput('Testing payment form AJAX endpoint...\n');
    
    const testData = {
        action: 'kilismile_process_payment',
        nonce: '<?php echo wp_create_nonce('kilismile_payment_nonce'); ?>',
        currency: 'TZS',
        amount: '10000',
        donor_name: 'Test Donor',
        donor_email: 'test@example.com',
        donor_phone: '+255712345678',
        payment_method: 'azampay',
        mobile_network: 'Mpesa'
    };
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(testData)
    })
    .then(response => response.text())
    .then(text => {
        updateTestOutput('Payment form endpoint response:\n');
        updateTestOutput(text + '\n');
        
        try {
            const data = JSON.parse(text);
            if (data.success) {
                updateTestOutput('✅ Payment form test successful!\n');
            } else {
                updateTestOutput('❌ Payment form test failed: ' + data.message + '\n');
            }
        } catch (e) {
            updateTestOutput('❌ Invalid JSON response\n');
        }
    })
    .catch(error => {
        updateTestOutput('❌ Test failed with error: ' + error.message + '\n');
    });
}

function showTestResults() {
    document.getElementById('test-results').style.display = 'block';
    document.getElementById('test-output').innerHTML = '';
}

function updateTestOutput(text) {
    const output = document.getElementById('test-output');
    output.innerHTML += text;
    output.scrollTop = output.scrollHeight;
}
</script>

<?php get_footer(); ?>

