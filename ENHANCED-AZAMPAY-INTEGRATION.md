# Enhanced AzamPay Integration

This document provides information about the enhanced AzamPay integration for the KiliSmile theme, including setup instructions, features, and troubleshooting tips.

## Overview

The enhanced AzamPay integration provides improved functionality for processing mobile money payments in Tanzania Shillings (TZS). It supports both STK Push (direct mobile payment) and Checkout Page (hosted payment page) methods.

## Features

- **Improved Token Management**: Automatic token renewal with proper expiration tracking
- **Enhanced Error Handling**: Detailed error messages and logging
- **Multiple Payment Methods**: Support for both STK Push and Checkout Page
- **Phone Number Formatting**: Automatically formats phone numbers to the required format
- **Comprehensive Callback Handling**: Properly processes payment notifications
- **Detailed Transaction Status Checking**: Accurate payment status updates
- **Improved Logging**: Detailed logs for debugging and troubleshooting

## Setup Instructions

1. Go to **AzamPay Settings** in the WordPress admin dashboard
2. Toggle the **Enhanced Integration** option to enable the enhanced version
3. Enter your AzamPay credentials:
   - App Name
   - Client ID
   - Client Secret
   - Partner ID (if provided by AzamPay)
4. Choose your environment (Sandbox or Production)
5. Configure your vendor name and logo URL
6. Save the settings and test the connection

## Testing

You can test the enhanced AzamPay integration using the test page:

1. Go to `/test-enhanced-azampay.php` in your browser
2. Fill in the test form with sample data
3. Test both STK Push and Checkout Page methods
4. Check the payment status and logs

## Callback URLs

Make sure to configure the following callback URLs in your AzamPay dashboard:

- **Callback URL**: `https://yourdomain.com/wp-admin/admin-ajax.php?action=azampay_callback`
- **Success URL**: `https://yourdomain.com/donation-success/`
- **Failure URL**: `https://yourdomain.com/donation-failed/`
- **Cancel URL**: `https://yourdomain.com/donation-cancelled/`

## Supported Mobile Networks

The integration supports the following mobile money providers:

- Vodacom M-Pesa
- Airtel Money
- Tigo Pesa
- HaloPesa
- AzamPesa

## Troubleshooting

### Common Issues

1. **Authentication Failed**:
   - Check your App Name, Client ID, and Client Secret
   - Ensure you're using the correct environment (Sandbox or Production)

2. **STK Push Not Received**:
   - Verify the phone number format (should be 255XXXXXXXXX)
   - Check if the mobile network is correct
   - Ensure the phone number is registered for mobile money

3. **Checkout Page Error**:
   - Verify your callback URLs
   - Check if your vendor name is correctly configured
   - Ensure your logo URL is accessible

4. **Payment Status Not Updating**:
   - Check if the callback URL is correctly configured
   - Verify that your server accepts incoming connections
   - Check the logs for any errors

### Debugging

The enhanced integration includes detailed logging to help with troubleshooting:

- Logs are written to the WordPress debug log if WP_DEBUG is enabled
- Additional logs are written to `wp-content/azampay-debug.log`

To enable debugging, add the following to your `wp-config.php` file:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## API Documentation

For more information about the AzamPay API, refer to the official documentation:

[AzamPay Developer Documentation](https://developerdocs.azampay.co.tz/redoc#section/Introduction)

