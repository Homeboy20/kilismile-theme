<?php
/**
 * Quick Fix: Enable AzamPay and Check Configuration
 */

// Include WordPress
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';

echo "<h2>🔧 Quick Payment Fix</h2>\n";

// 1. Enable AzamPay
$azampay_enabled = get_option('kilismile_azampay_enabled', false);
if (!$azampay_enabled) {
    update_option('kilismile_azampay_enabled', true);
    echo "✅ AzamPay has been enabled\n<br>";
} else {
    echo "✅ AzamPay was already enabled\n<br>";
}

// 2. Check credentials
$client_id = get_option('kilismile_azampay_client_id', '');
$client_secret = get_option('kilismile_azampay_client_secret', '');

if (empty($client_id) || empty($client_secret)) {
    echo "⚠️ Setting AzamPay credentials...\n<br>";
    update_option('kilismile_azampay_client_id', '684f4b03-68ea-4db3-a329-be15925b59aa');
    update_option('kilismile_azampay_client_secret', '8a4ca1f4-4aef-4459-8e1c-b074129917f7');
    update_option('kilismile_azampay_app_name', 'KiliSmile');
    update_option('kilismile_azampay_sandbox', true);
    echo "✅ AzamPay credentials configured\n<br>";
} else {
    echo "✅ AzamPay credentials already set\n<br>";
}

// 3. Test AJAX actions
echo "<br><strong>🔍 AJAX Actions Status:</strong><br>\n";
$actions_to_check = [
    'kilismile_process_payment',
    'process_donation',
    'get_payment_methods'
];

foreach ($actions_to_check as $action) {
    $has_action = has_action("wp_ajax_$action") || has_action("wp_ajax_nopriv_$action");
    echo "• $action: " . ($has_action ? '✅ Registered' : '❌ Not registered') . "<br>\n";
}

echo "<br><strong>📊 Current Configuration:</strong><br>\n";
echo "• AzamPay Enabled: " . (get_option('kilismile_azampay_enabled', false) ? '✅' : '❌') . "<br>\n";
echo "• PayPal Enabled: " . (get_option('kilismile_paypal_enabled', false) ? '✅' : '❌') . "<br>\n";
echo "• Client ID: " . (get_option('kilismile_azampay_client_id') ? '✅ Set' : '❌ Missing') . "<br>\n";
echo "• Sandbox Mode: " . (get_option('kilismile_azampay_sandbox', true) ? '🧪 Yes' : '🔴 Production') . "<br>\n";

echo "<br><strong>🔗 Next Steps:</strong><br>\n";
echo "1. <a href='" . home_url('/test-payment-submission.php') . "'>Test Payment Submission</a><br>\n";
echo "2. <a href='" . home_url('/check-azampay-config.php') . "'>Check Full Configuration</a><br>\n";
echo "3. <a href='" . home_url('/page-donations.php') . "'>Go to Donations Page</a><br>\n";

?>

