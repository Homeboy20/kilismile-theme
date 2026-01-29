# AzamPay WordPress Payment Gateway Integration Guide

This document provides a comprehensive guide for integrating AzamPay as a payment gateway within a WordPress environment. It covers the necessary API interactions, best practices for WordPress plugin development, and handling payment callbacks.

## 1. Introduction to AzamPay and WordPress Integration

AzamPay offers a robust payment gateway solution for businesses operating in East Africa, facilitating online payments through various channels including mobile money and bank transfers. Integrating AzamPay with WordPress allows e-commerce sites and other online platforms built on WordPress to accept payments securely and efficiently.

This guide will walk you through the technical steps required to develop a custom WordPress plugin or extend an existing e-commerce plugin (like WooCommerce) to utilize AzamPay's API.

## 2. Key AzamPay API Endpoints for WordPress Integration

Based on the AzamPay API documentation, the following endpoints are crucial for a WordPress payment integration:

### 2.1. Authentication (Token Generation)

Before interacting with any other AzamPay API, an access token must be generated. This token is used for authenticating subsequent API calls.

*   **Endpoint:** `POST /AppRegistration/GenerateToken`
*   **Purpose:** Obtain a Bearer Token using `appName`, `clientId`, and `clientSecret`.
*   **WordPress Context:** This should typically be handled server-side within your WordPress plugin, storing the token securely and refreshing it as needed.

### 2.2. Checkout API

AzamPay provides different checkout options, including Mobile Network Operator (MNO) checkout and Bank checkout.

#### 2.2.1. MNO Checkout

*   **Endpoint:** `POST /azampay/mno/checkout`
*   **Purpose:** Facilitate payments directly from mobile money accounts (Airtel, Tigo, Halopesa, Azampesa, Mpesa).
*   **WordPress Context:** This would be used when a user selects a mobile money option during checkout. The plugin would collect the user's mobile number (`accountNumber`) and the selected provider, then initiate the payment.

#### 2.2.2. Bank Checkout

*   **Endpoint:** `POST /azampay/bank/checkout`
*   **Purpose:** Facilitate payments directly from bank accounts (CRDB, NMB).
*   **WordPress Context:** Similar to MNO checkout, this is used when a user selects a bank payment option. It requires `merchantAccountNumber`, `merchantMobileNumber`, `otp`, and the `provider`.

### 2.3. Checkout Pages (Initiate Checkout Session)

This endpoint is suitable for web-based checkouts where AzamPay handles the payment page redirection.

*   **Endpoint:** `POST /azampay/checkout/json`
*   **Purpose:** Initiate a checkout session and receive a `pgUrl` (payment gateway URL) to redirect the user for payment completion.
*   **WordPress Context:** This is ideal for standard e-commerce flows. The WordPress plugin would call this API, then redirect the user's browser to the `pgUrl` returned by AzamPay. Upon completion, AzamPay redirects the user back to `redirectSuccessURL` or `redirectFailURL`.

### 2.4. Callback API

Crucial for receiving real-time payment status updates from AzamPay.

*   **Endpoint:** `POST /api/v1/Checkout/Callback`
*   **Purpose:** AzamPay sends transaction completion status to the merchant's application.
*   **WordPress Context:** Your WordPress plugin must expose a publicly accessible endpoint (URL) that AzamPay can call. This endpoint will receive the payment status and update the order status in WordPress accordingly. This is vital for confirming successful payments and handling failed transactions.

## 3. WordPress Plugin Development Considerations

To integrate AzamPay, you will typically develop a custom WordPress plugin or extend an existing e-commerce plugin like WooCommerce. Here are key considerations:

### 3.1. Plugin Structure

A standard WordPress plugin structure should be followed. For a payment gateway, this often involves:

*   **Main Plugin File:** Contains plugin metadata and hooks.
*   **Includes/Classes:** Separate files for API interaction, settings, and payment processing logic.
*   **Templates/Views:** For any custom UI elements (e.g., payment forms).
*   **Assets:** CSS, JavaScript, images.

### 3.2. Settings and Configuration

The plugin should provide an administrative interface for merchants to configure their AzamPay credentials (appName, clientId, clientSecret) and other settings (e.g., sandbox/production mode, callback URL).

### 3.3. Security Best Practices

*   **Secure Storage:** AzamPay API credentials (`clientId`, `clientSecret`) should be stored securely, preferably encrypted and not directly in the database in plain text. WordPress's `wp_options` table can be used, but ensure proper sanitization and validation.
*   **HTTPS:** All communication with AzamPay APIs and your callback URL MUST use HTTPS.
*   **Input Validation & Sanitization:** Always validate and sanitize all user inputs and API responses to prevent security vulnerabilities.
*   **Nonce Verification:** For any forms or actions initiated from the WordPress admin or frontend, use WordPress nonces to protect against CSRF attacks.

### 3.4. Error Handling and Logging

Implement robust error handling for all API calls. Log errors and transaction details to assist with debugging and support. WordPress provides its own logging mechanisms or you can use a dedicated logging library.

## 4. Integrating with WooCommerce (Example Scenario)

If your WordPress site uses WooCommerce, the integration will typically involve creating a custom WooCommerce payment gateway. This involves extending the `WC_Payment_Gateway` class.

### 4.1. Basic WooCommerce Gateway Structure

```php
<?php
/*
Plugin Name: AzamPay WooCommerce Gateway
Description: Integrates AzamPay payment gateway with WooCommerce.
Version: 1.0.0
Author: Your Name
*/

add_action('plugins_loaded', 'init_azampay_gateway_class');

function init_azampay_gateway_class() {
    class WC_Gateway_AzamPay extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'azampay';
            $this->icon               = apply_filters('woocommerce_azampay_icon', ''); // Optional icon
            $this->has_fields         = false; // True if you need custom fields on checkout
            $this->method_title       = 'AzamPay';
            $this->method_description = 'Accept payments via AzamPay.';

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user setting variables.
            $this->title        = $this->get_option('title');
            $this->description  = $this->get_option('description');
            $this->enabled      = $this->get_option('enabled');
            $this->app_name     = $this->get_option('app_name');
            $this->client_id    = $this->get_option('client_id');
            $this->client_secret = $this->get_option('client_secret');
            $this->testmode     = 'yes' === $this->get_option('testmode');
            $this->debug        = 'yes' === $this->get_option('debug');

            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_api_wc_gateway_azampay', array($this, 'handle_azampay_callback')); // Custom callback handler

            if ($this->testmode) {
                $this->description .= ' TEST MODE ENABLED.';
                $this->description  = trim($this->description);
            }
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => 'Enable/Disable',
                    'type'    => 'checkbox',
                    'label'   => 'Enable AzamPay Payment Gateway',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'AzamPay',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay securely via AzamPay using Mobile Money or Bank Transfer.',
                    'desc_tip'    => true,
                ),
                'app_name' => array(
                    'title'       => 'AzamPay App Name',
                    'type'        => 'text',
                    'description' => 'Your AzamPay Application Name.',
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'client_id' => array(
                    'title'       => 'AzamPay Client ID',
                    'type'        => 'text',
                    'description' => 'Your AzamPay Client ID.',
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'client_secret' => array(
                    'title'       => 'AzamPay Client Secret',
                    'type'        => 'password',
                    'description' => 'Your AzamPay Client Secret.',
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'testmode' => array(
                    'title'       => 'Test Mode',
                    'type'        => 'checkbox',
                    'label'       => 'Enable Test Mode',
                    'default'     => 'no',
                    'description' => 'Place the gateway in test mode using sandbox API credentials.',
                ),
                'debug' => array(
                    'title'       => 'Debug Log',
                    'type'        => 'checkbox',
                    'label'       => 'Enable logging',
                    'default'     => 'no',
                    'description' => 'Log AzamPay events, such as API requests, inside WooCommerce > Status > Logs',
                )
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Get AzamPay token
            $token_data = $this->get_azampay_token();
            if (is_wp_error($token_data)) {
                wc_add_notice($token_data->get_error_message(), 'error');
                return;
            }
            $access_token = $token_data['accessToken'];

            // Initiate AzamPay Checkout Session
            $redirect_url = $this->initiate_azampay_checkout_session($order, $access_token);

            if (is_wp_error($redirect_url)) {
                wc_add_notice($redirect_url->get_error_message(), 'error');
                return;
            }

            // Redirect to AzamPay payment page
            return array(
                'result'   => 'success',
                'redirect' => $redirect_url,
            );
        }

        private function get_azampay_token() {
            // Implement API call to POST /AppRegistration/GenerateToken
            // Use $this->app_name, $this->client_id, $this->client_secret
            // Handle success and error responses
            // Return access token or WP_Error
            return array('accessToken' => 'YOUR_GENERATED_TOKEN'); // Placeholder
        }

        private function initiate_azampay_checkout_session($order, $access_token) {
            // Implement API call to POST /azampay/checkout/json
            // Use $order->get_total(), $order->get_currency(), $order->get_id() as externalId
            // Construct redirectSuccessURL and redirectFailURL using WooCommerce API callback URL
            // Handle success and error responses
            // Return pgUrl or WP_Error
            return 'https://sandbox.azampay.co.tz/checkout?session=123'; // Placeholder
        }

        public function handle_azampay_callback() {
            // Verify callback request (e.g., IP whitelist, secret key if provided by AzamPay)
            // Parse the incoming JSON payload from AzamPay
            // Extract transactionId, success status, amount, externalId (order_id)
            // Retrieve the WooCommerce order using externalId
            // Update order status based on AzamPay's success status
            // Add order notes for transaction details
            // Send appropriate HTTP response back to AzamPay
            error_log('AzamPay Callback Received: ' . print_r($_REQUEST, true));
            // Example: Update order status
            // $order = wc_get_order($order_id);
            // $order->payment_complete($transaction_id);
            // $order->add_order_note('AzamPay payment completed. Transaction ID: ' . $transaction_id);
            // wp_send_json_success();
        }
    }

    function add_azampay_gateway($methods) {
        $methods[] = 'WC_Gateway_AzamPay';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'add_azampay_gateway');
}
```

## 5. Handling Callbacks in WordPress

The AzamPay Callback API (`POST /api/v1/Checkout/Callback`) is critical for updating order statuses in real-time. Your WordPress plugin needs to expose an endpoint that AzamPay can reach.

### 5.1. WooCommerce Callback URL

WooCommerce provides a standard way to handle callbacks for payment gateways using `WC_API_Manager`. The URL typically looks like: `yourdomain.com/?wc-api=WC_Gateway_AzamPay` (where `WC_Gateway_AzamPay` is your gateway's ID).

Within your `WC_Gateway_AzamPay` class, you would define a method (e.g., `handle_azampay_callback`) and hook it to `woocommerce_api_{your_gateway_id}`:

```php
add_action('woocommerce_api_wc_gateway_azampay', array($this, 'handle_azampay_callback'));
```

### 5.2. Callback Logic

Inside `handle_azampay_callback`:

1.  **Security Check:** Verify the request is legitimate (e.g., check source IP, or if AzamPay provides a secret key for callback verification, use that).
2.  **Parse Payload:** Read the JSON payload sent by AzamPay.
3.  **Extract Data:** Get `transactionId`, `success` status, `amount`, and `externalId` (which should correspond to your WooCommerce `order_id`).
4.  **Retrieve Order:** Load the corresponding WooCommerce order using `wc_get_order($externalId)`.
5.  **Update Order Status:**
    *   If `success` is `true`, mark the order as `processing` or `completed` using `$order->payment_complete($transactionId);`.
    *   If `success` is `false`, mark the order as `failed` using `$order->update_status('failed');`.
6.  **Add Order Notes:** Add details about the AzamPay transaction to the order notes for auditing.
7.  **Respond to AzamPay:** Send a `200 OK` HTTP response to AzamPay to acknowledge receipt of the callback. Failure to do so might cause AzamPay to retry sending the callback.

## 6. Testing and Deployment

### 6.1. Sandbox Environment

Always develop and test your integration in AzamPay's Sandbox environment first. Use the provided Sandbox Base URLs:

*   **Authenticator Sandbox Base Url:** `https://authenticator-sandbox.azampay.co.tz`
*   **Azampay Sandbox Checkout Base Url:** `https://sandbox.azampay.co.tz`

Ensure your plugin settings allow switching between sandbox and production modes.

### 6.2. Production Deployment

Once thoroughly tested in the sandbox:

1.  Switch your plugin to use production AzamPay API credentials and URLs.
2.  Ensure your callback URL is publicly accessible and configured with AzamPay.
3.  Perform live transactions with small amounts to verify functionality.

## 7. Conclusion

By following this guide, developers can successfully integrate AzamPay into their WordPress websites, providing a seamless and secure payment experience for their users. Remember to adhere to WordPress and WooCommerce development best practices, especially regarding security and error handling.



