<?php
/**
 * Test AzamPay Configuration with Your Credentials
 */

// Mock WordPress functions for testing
if (!function_exists('get_option')) {
    function get_option($option_name, $default = false) {
        // Your AzamPay credentials for testing
        $options = array(
            'kilismile_azampay_app_name' => 'KiliSmile', // You may need to get this from AzamPay
            'kilismile_azampay_client_id' => '684f4b03-68ea-4db3-a329-be15925b59aa',
            'kilismile_azampay_client_secret' => '8a4ca1f4-4aef-4459-8e1c-b074129917f7', // Using the token you provided
            'kilismile_azampay_sandbox' => true // Enable sandbox for testing
        );
        
        return isset($options[$option_name]) ? $options[$option_name] : $default;
    }
}

if (!function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration) {
        return true; // Mock function
    }
}

if (!function_exists('get_transient')) {
    function get_transient($transient) {
        return false; // Always get fresh token for testing
    }
}

// Include the AzamPay integration
require_once 'includes/azampay-integration.php';

// Test the class
try {
    echo "<h1>AzamPay Integration Test</h1>";
    
    $azampay = new KiliSmile_AzamPay();
    echo "✅ AzamPay class instantiated successfully<br>";
    
    // Test authentication
    echo "<h2>Testing Authentication</h2>";
    
    // Use reflection to call private method for testing
    $reflection = new ReflectionClass($azampay);
    $method = $reflection->getMethod('get_access_token');
    $method->setAccessible(true);
    
    try {
        $token = $method->invoke($azampay);
        echo "✅ Authentication successful<br>";
        echo "📝 Token received: " . substr($token, 0, 20) . "...<br>";
        
        // Test provider mapping
        echo "<h2>Testing Provider Mapping</h2>";
        $providerMethod = $reflection->getMethod('map_network_provider');
        $providerMethod->setAccessible(true);
        
        $networks = ['vodacom', 'airtel', 'tigo', 'halopesa', 'azampesa'];
        foreach ($networks as $network) {
            $provider = $providerMethod->invoke($azampay, $network);
            echo "📱 $network → $provider<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Authentication failed: " . $e->getMessage() . "<br>";
        echo "🔍 This might be because:<br>";
        echo "- The Client Secret might be different from the Token<br>";
        echo "- You may need to get the actual Client Secret (not the Token)<br>";
        echo "- The App Name might need to be registered first<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Configuration Info</h2>";
echo "🔧 <strong>Endpoints being used:</strong><br>";
echo "• Auth: https://authenticator-sandbox.azampay.co.tz<br>";
echo "• Payment: https://sandbox.azampay.co.tz<br>";
echo "<br>";
echo "📋 <strong>Your Configuration:</strong><br>";
echo "• Client ID: 684f4b03-68ea-4db3-a329-be15925b59aa<br>";
echo "• App Name: KiliSmile (may need to be registered)<br>";
echo "• Sandbox: Enabled<br>";
echo "<br>";
echo "💡 <strong>Note:</strong> If authentication fails, you may need to:<br>";
echo "1. Get the actual Client Secret (different from Token)<br>";
echo "2. Register your App Name with AzamPay<br>";
echo "3. Verify your credentials in the AzamPay dashboard<br>";
?>

