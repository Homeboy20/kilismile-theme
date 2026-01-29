<?php
/**
 * AzamPay Configuration Helper
 * Run this file to configure your AzamPay credentials
 */

// This file should be run in WordPress context
if (!function_exists('update_option')) {
    die('This script must be run within WordPress context. Please access it through your WordPress admin or run it via WP-CLI.');
}

// Your AzamPay credentials
$credentials = array(
    'kilismile_azampay_app_name' => 'KiliSmile', // You may need to verify this with AzamPay
    'kilismile_azampay_client_id' => '684f4b03-68ea-4db3-a329-be15925b59aa',
    'kilismile_azampay_client_secret' => '8a4ca1f4-4aef-4459-8e1c-b074129917f7', // Using the token you provided
    'kilismile_azampay_sandbox' => true // Enable sandbox for testing
);

// Update the options
foreach ($credentials as $option_name => $value) {
    update_option($option_name, $value);
    echo "✅ Updated: $option_name\n";
}

echo "\n🎉 AzamPay credentials configured successfully!\n";
echo "\nNext steps:\n";
echo "1. Go to WordPress Admin → Payment Settings to verify\n";
echo "2. Test a small donation to ensure it works\n";
echo "3. If authentication fails, you may need to:\n";
echo "   - Get the actual Client Secret from AzamPay (different from Token)\n";
echo "   - Register your App Name with AzamPay\n";
echo "   - Verify credentials in AzamPay dashboard\n";

?>

