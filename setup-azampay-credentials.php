<?php
/**
 * Quick Setup Script for AzamPay Credentials
 * 
 * Run this in your WordPress admin or via WP-CLI to configure your AzamPay settings
 */

// This should be run within WordPress context
if (!function_exists('update_option')) {
    echo "This script must be run within WordPress context.\n";
    echo "Please copy these SQL commands and run them in your database:\n\n";
    
    echo "INSERT INTO wp_options (option_name, option_value) VALUES \n";
    echo "('kilismile_azampay_app_name', 'KiliSmile'),\n";
    echo "('kilismile_azampay_client_id', '684f4b03-68ea-4db3-a329-be15925b59aa'),\n";
    echo "('kilismile_azampay_client_secret', '8a4ca1f4-4aef-4459-8e1c-b074129917f7'),\n";
    echo "('kilismile_azampay_sandbox', '1'),\n";
    echo "('kilismile_azampay_enabled', '1')\n";
    echo "ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);\n\n";
    
    exit;
}

// Your AzamPay credentials
$credentials = array(
    'kilismile_azampay_app_name' => 'KiliSmile',
    'kilismile_azampay_client_id' => '684f4b03-68ea-4db3-a329-be15925b59aa',
    'kilismile_azampay_client_secret' => '8a4ca1f4-4aef-4459-8e1c-b074129917f7',
    'kilismile_azampay_sandbox' => true,
    'kilismile_azampay_enabled' => true
);

echo "🔧 Configuring AzamPay credentials...\n\n";

foreach ($credentials as $option_name => $value) {
    $result = update_option($option_name, $value);
    if ($result) {
        echo "✅ {$option_name}: Updated\n";
    } else {
        echo "ℹ️ {$option_name}: Already up to date\n";
    }
}

echo "\n🎉 Configuration completed!\n\n";

echo "📋 Summary:\n";
echo "• App Name: KiliSmile\n";
echo "• Client ID: 684f4b03-68ea-4db3-a329-be15925b59aa\n";
echo "• Client Secret: ****-****-****-****-************17f7\n";
echo "• Sandbox Mode: Enabled\n";
echo "• Gateway: Enabled\n\n";

echo "🚀 Next Steps:\n";
echo "1. Go to /wp-admin/admin.php?page=kilismile-payment-gateways to verify\n";
echo "2. Test with a small donation at /donate\n";
echo "3. Monitor the WordPress admin for donation activity\n\n";

echo "⚠️ Important Notes:\n";
echo "• Make sure 'KiliSmile' is registered as your app name with AzamPay\n";
echo "• Verify the Client Secret is correct (not just a session token)\n";
echo "• Test in sandbox mode first before going live\n";

?>

