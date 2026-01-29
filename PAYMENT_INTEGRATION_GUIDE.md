# Payment Integration Setup Guide

## Overview
This payment system integrates PayPal and AzamPay with automatic gateway selection based on currency:
- **USD payments** → PayPal
- **TZS payments** → AzamPay with mobile money (STK Push)

## Configuration Steps

### 1. Admin Settings
Navigate to **Admin → Enhanced Theme Settings → Payments**

#### PayPal Configuration
- Enable PayPal: ✅
- Client ID: Your PayPal sandbox/live client ID
- Client Secret: Your PayPal sandbox/live client secret
- Environment: sandbox (for testing) or live (for production)

#### AzamPay Configuration
- Enable AzamPay: ✅
- App Name: Your AzamPay app name
- Client ID: Your AzamPay client ID
- Client Secret: Your AzamPay client secret
- Environment: sandbox (for testing) or live (for production)

### 2. Using the Payment Form

#### Via Shortcode
Add to any page or post:
```
[kilismile_payment_form]
```

With custom options:
```
[kilismile_payment_form title="Support Our Cause" description="Your donation makes a difference" show_title="yes"]
```

#### Via Template
Include in PHP templates:
```php
<?php include get_template_directory() . '/template-parts/payment-form.php'; ?>
```

### 3. Test Page
Create a new page and assign the "Payment Test Page" template, or visit:
- `/payment-test/` if you create a page with slug "payment-test"

## Testing Instructions

### PayPal Testing (USD)
1. Set PayPal to sandbox mode in settings
2. Use PayPal sandbox accounts for testing
3. Select USD currency in payment form
4. PayPal option will automatically appear
5. Complete payment in PayPal sandbox

### AzamPay Testing (TZS)
1. Set AzamPay to sandbox mode in settings
2. Use test credentials from AzamPay
3. Select TZS currency in payment form
4. Choose mobile network (Vodacom M-Pesa, Airtel Money, etc.)
5. Enter test phone number
6. Complete STK Push payment

### Test Phone Numbers (Tanzania Format)
- Vodacom M-Pesa: +255712345678
- Airtel Money: +255682345678
- Tigo Pesa: +255652345678
- HaloPesa: +255622345678

## Features

### Automatic Gateway Selection
- Currency detection triggers appropriate payment gateway
- No manual gateway selection needed
- Seamless user experience

### Mobile Network Selection
- Manual selection of mobile money provider
- Proper phone number formatting
- Network-specific STK Push requests

### Transaction Tracking
- All payments stored in database
- Status updates for monitoring
- Email notifications (if configured)

### Responsive Design
- Mobile-first approach
- Clean, modern interface
- Accessibility compliant

## File Structure
```
/includes/
  ├── azampay-integration.php     # AzamPay STK Push integration
  ├── paypal-integration.php      # PayPal REST API integration
  └── payment-form-handler.php    # Shortcode and asset management

/template-parts/
  └── payment-form.php            # Payment form template

/assets/
  ├── css/payment-form.css        # Payment form styles
  └── js/payment-form.js          # Payment form JavaScript

/admin/
  └── enhanced-theme-settings.php # Payment settings configuration
```

## Troubleshooting

### Common Issues
1. **Payment form not loading**: Check if assets are enqueued properly
2. **Gateway not showing**: Verify credentials and enable status in settings
3. **STK Push failing**: Confirm phone number format and network selection
4. **PayPal redirect issues**: Check return URLs and sandbox/live mode

### Debug Mode
Enable WordPress debug mode in wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Database Tables
Payment transactions are stored in:
- Table: `wp_donations`
- Columns: amount, currency, gateway, status, transaction_id, donor_info

## Security Notes
- All sensitive data encrypted
- CSRF protection with nonces
- Input validation and sanitization
- Secure API communication (HTTPS)
- Test credentials separate from production

## Support
For technical support or customization:
1. Check WordPress error logs
2. Verify API credentials
3. Test with sandbox environments first
4. Monitor transaction status in database

