# AzamPay Integration Setup Guide

## Overview
This theme now includes a complete AzamPay payment gateway integration for processing mobile money donations through STK Push.

## Features
- ✅ Real AzamPay API integration with official endpoints
- ✅ JWT token authentication with automatic caching
- ✅ STK Push mobile money payments (Tigo, Vodacom, Airtel, Halopesa)
- ✅ Transaction storage and callback handling
- ✅ Responsive modal design for all screen sizes
- ✅ WordPress admin settings page for easy configuration
- ✅ Sandbox and Live environment support
- ✅ Clean webhook URLs for callbacks

## Setup Instructions

### 1. Get AzamPay Credentials
1. Sign up at [AzamPay Developer Portal](https://developers.azampay.co.tz/)
2. Complete KYC process for live environment
3. Get your credentials from the dashboard:
   - **App Name**: Your application identifier
   - **Client ID**: Your client identifier
   - **Client Secret**: Your secret key
   - **API Key**: Your API access key

### 2. Configure WordPress Settings
1. Go to WordPress Admin → **Settings** → **AzamPay Settings**
2. Fill in your credentials:
   - **Sandbox Settings**: For testing
   - **Live Settings**: For production
3. Set **Test Mode**: Enable for sandbox, disable for live
4. Save settings

### 3. Configure AzamPay Dashboard
1. Login to your AzamPay dashboard
2. Go to **Webhooks/Callbacks** section
3. Add this callback URL:
   ```
   https://yourdomain.com/azampay/callback/
   ```
4. Enable callback notifications

### 4. Test the Integration
1. Enable **Test Mode** in settings
2. Use sandbox credentials
3. Test with Tanzanian phone numbers (format: 255XXXXXXXXX)
4. Check donation form for STK Push functionality

## API Endpoints Used
- **Token Generation**: `/AppRegistration/GenerateToken`
- **STK Push**: `/azampay/mno/checkout`
- **Callbacks**: `/azampay/callback/` (your site)

## Supported Mobile Networks
- **Tigo**: Use provider code `Tigo`
- **Vodacom**: Use provider code `Vodacom`  
- **Airtel**: Use provider code `Airtel`
- **Halopesa**: Use provider code `Halopesa`

## Database Tables
The integration creates a custom table `wp_kilismile_azampay_transactions` to store:
- Transaction references
- Payment status
- Callback data
- Timestamps

## Security Features
- WordPress nonce verification
- Sanitized input data
- Secure token storage with expiration
- Proper callback validation

## Troubleshooting

### Common Issues
1. **"Invalid phone number"**: Ensure format is 255XXXXXXXXX
2. **"Token failed"**: Check credentials in admin settings
3. **"No callback received"**: Verify webhook URL in AzamPay dashboard
4. **Modal not responsive**: Clear browser cache

### Debug Information
- Check WordPress debug logs for error messages
- Transaction details stored in database for reference
- Test mode indicators show in modal for sandbox testing

## Support
For AzamPay API issues, contact [AzamPay Support](https://azampay.co.tz/contact)
For integration issues, check WordPress debug logs and verify settings.

---
**Note**: This integration uses AzamPay API v1 and requires proper credentials configuration before use.