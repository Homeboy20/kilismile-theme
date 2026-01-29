<?php
/**
 * Toggle Test Script
 * This will help us debug if the toggle save is working
 */

// WordPress Bootstrap
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Check if this is a POST request to test saving
if ($_POST && isset($_POST['test_toggle'])) {
    // Simulate the same logic as the admin form
    $test_enabled = isset($_POST['kilismile_paypal_enabled']) ? 1 : 0;
    update_option('kilismile_paypal_enabled', $test_enabled);
    
    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "PayPal setting updated to: " . ($test_enabled ? 'ENABLED' : 'DISABLED');
    echo "</div>";
}

$current_paypal = get_option('kilismile_paypal_enabled', 0);
$current_selcom = get_option('kilismile_selcom_enabled', 1);
$current_mpesa = get_option('kilismile_mpesa_enabled', 0);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Toggle Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .toggle-test { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 10px 0; }
        .current-status { font-weight: bold; color: #333; }
        .enabled { color: green; }
        .disabled { color: red; }
        button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Payment Toggle Test</h1>
    
    <div class="toggle-test">
        <h2>Current Status</h2>
        <p>PayPal: <span class="current-status <?php echo $current_paypal ? 'enabled' : 'disabled'; ?>">
            <?php echo $current_paypal ? 'ENABLED' : 'DISABLED'; ?> (Value: <?php echo $current_paypal; ?>)
        </span></p>
        <p>Selcom: <span class="current-status <?php echo $current_selcom ? 'enabled' : 'disabled'; ?>">
            <?php echo $current_selcom ? 'ENABLED' : 'DISABLED'; ?> (Value: <?php echo $current_selcom; ?>)
        </span></p>
        <p>M-Pesa: <span class="current-status <?php echo $current_mpesa ? 'enabled' : 'disabled'; ?>">
            <?php echo $current_mpesa ? 'ENABLED' : 'DISABLED'; ?> (Value: <?php echo $current_mpesa; ?>)
        </span></p>
    </div>
    
    <div class="toggle-test">
        <h2>Test Toggle Save</h2>
        <form method="post" action="">
            <p>
                <label>
                    <input type="checkbox" name="kilismile_paypal_enabled" value="1" <?php checked($current_paypal, 1); ?>>
                    Enable PayPal
                </label>
            </p>
            <p>
                <button type="submit" name="test_toggle" value="1">Save Test</button>
            </p>
        </form>
        <p><small>This tests if the basic checkbox save logic is working.</small></p>
    </div>
    
    <div class="toggle-test">
        <h2>Direct Database Check</h2>
        <?php
        global $wpdb;
        $options = $wpdb->get_results("
            SELECT option_name, option_value 
            FROM {$wpdb->options} 
            WHERE option_name LIKE 'kilismile_%_enabled' 
            ORDER BY option_name
        ");
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th style='padding: 8px; background: #f0f0f0;'>Option Name</th><th style='padding: 8px; background: #f0f0f0;'>Value</th><th style='padding: 8px; background: #f0f0f0;'>Status</th></tr>";
        
        foreach ($options as $option) {
            $status = ($option->option_value == '1') ? 'ENABLED' : 'DISABLED';
            $color = ($option->option_value == '1') ? 'green' : 'red';
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$option->option_name}</td>";
            echo "<td style='padding: 8px;'>{$option->option_value}</td>";
            echo "<td style='padding: 8px; color: {$color}; font-weight: bold;'>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </div>
    
    <div class="toggle-test">
        <h2>Quick Fix Actions</h2>
        <p>If the toggles aren't working, try these:</p>
        <ul>
            <li>Clear browser cache and reload the admin page</li>
            <li>Check if there are JavaScript errors in browser console</li>
            <li>Verify the correct form is being submitted (look for "Payment methods saved successfully!" message)</li>
            <li>Check if there are multiple forms interfering with each other</li>
        </ul>
        
        <p><strong>Manual Reset Options:</strong></p>
        <p>
            <a href="?reset_paypal=1" style="background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Disable PayPal</a>
            <a href="?enable_paypal=1" style="background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-left: 10px;">Enable PayPal</a>
        </p>
    </div>
    
    <?php
    // Handle manual reset options
    if (isset($_GET['reset_paypal'])) {
        update_option('kilismile_paypal_enabled', 0);
        echo "<script>alert('PayPal disabled manually'); window.location.href = window.location.pathname;</script>";
    }
    if (isset($_GET['enable_paypal'])) {
        update_option('kilismile_paypal_enabled', 1);
        echo "<script>alert('PayPal enabled manually'); window.location.href = window.location.pathname;</script>";
    }
    ?>
    
</body>
</html>


