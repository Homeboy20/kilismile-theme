<?php
/**
 * Enable AzamPay and Configure Settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    header('Location: /');
    exit;
}

// Check if AzamPay is enabled
$azampay_enabled = get_option('kilismile_azampay_enabled', false);

// If not enabled, enable it
if (!$azampay_enabled) {
    update_option('kilismile_azampay_enabled', true);
    echo "✅ AzamPay has been enabled.<br>";
} else {
    echo "✅ AzamPay was already enabled.<br>";
}

// Verify credentials are set
$client_id = get_option('kilismile_azampay_client_id', '');
$client_secret = get_option('kilismile_azampay_client_secret', '');

if (empty($client_id) || empty($client_secret)) {
    echo "⚠️ AzamPay credentials are not set. Please configure them in the admin panel.<br>";
} else {
    echo "✅ AzamPay credentials are configured.<br>";
}

// Check if other gateways are enabled
$paypal_enabled = get_option('kilismile_paypal_enabled', false);
$selcom_enabled = get_option('kilismile_selcom_enabled', false);

echo "<br><strong>Payment Gateway Status:</strong><br>";
echo "• AzamPay: " . (get_option('kilismile_azampay_enabled', false) ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "• PayPal: " . ($paypal_enabled ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "• Selcom: " . ($selcom_enabled ? '✅ Enabled' : '❌ Disabled') . "<br>";

// Show a link back to admin
echo "<br><a href='" . admin_url('admin.php?page=kilismile-payment-settings') . "'>Go to Payment Settings</a><br>";
echo "<a href='" . home_url('/check-azampay-config.php') . "'>Check AzamPay Configuration</a><br>";
echo "<a href='" . home_url('/test-payment-ajax.php') . "'>Test Payment AJAX</a><br>";

?>

