# Selcom Payment Gateway Integration

This document explains the complete Selcom Payment Gateway integration for the Kilismile WordPress donation system.

## Overview

The integration uses Selcom's official Checkout API to process donations through:
- Credit/Debit Cards (Visa, Mastercard, Amex)
- Mobile Money (M-Pesa, Tigo Pesa, Airtel Money, etc.)
- Bank transfers
- Other local payment methods

## Configuration

### 1. Selcom Account Setup
1. Create a merchant account at [Selcom](https://selcommobile.com/)
2. Contact Selcom support (info@selcom.net) to get your API credentials:
   - API Key
   - API Secret  
   - Vendor/Merchant ID

### 2. WordPress Configuration
1. Go to WordPress Admin → Appearance → Theme Settings
2. Click on "Gateway Integration" tab
3. Enable "Selcom Payment Gateway"
4. Enter your credentials:
   - **Selcom API Key**: Your API key from Selcom dashboard
   - **Selcom API Secret**: Your API secret for secure transactions
   - **Selcom Vendor ID**: Your unique merchant/vendor ID
5. Save settings

### 3. Webhook Configuration
Configure this webhook URL in your Selcom merchant dashboard:
```
https://yourdomain.com/wp-admin/admin-post.php?action=kilismile_selcom_webhook
```

## How It Works

### 1. Payment Flow
1. User fills donation form and selects Selcom as payment method
2. System creates order using Selcom Checkout API
3. User is redirected to Selcom's secure payment gateway
4. User completes payment using their preferred method
5. Selcom sends webhook notification with payment status
6. System processes the webhook and updates donation status
7. Confirmation email is sent to donor

### 2. API Integration Details

#### Order Creation
- **Endpoint**: `/v1/checkout/create-order-minimal`
- **Method**: POST
- **Authentication**: HMAC SHA256 signature

#### Webhook Handling
- **URL**: `/wp-admin/admin-post.php?action=kilismile_selcom_webhook`
- **Method**: POST
- **Purpose**: Receive payment status updates

## Security Features

### 1. Authentication
- Base64 encoded API key
- HMAC SHA256 signature verification
- Timestamp validation
- Signed fields verification

### 2. Data Protection
- Sensitive data stored securely in WordPress options
- Payment processing happens on Selcom's secure servers
- No card details stored locally

### 3. Webhook Security
- Order ID validation (must start with 'KILI_')
- Payment status verification
- Donation record validation

## Error Handling

### Configuration Errors
- `selcom_not_configured`: Missing API credentials
- `selcom_order_failed`: Order creation failed

### Payment Errors
- Handled by Selcom's payment gateway
- Users redirected appropriately based on payment result

## Testing

### 1. Test Mode
- Use Selcom's test API credentials for testing
- Test with different payment methods
- Verify webhook notifications

### 2. Test File
Run the test file to verify configuration:
```
https://yourdomain.com/wp-content/themes/kilismile/test-selcom-integration.php
```

## Database Structure

### Donations Table
```sql
CREATE TABLE wp_kilismile_donations (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    donation_id varchar(100) NOT NULL,
    amount decimal(10,2) NOT NULL,
    currency varchar(10) NOT NULL,
    payment_method varchar(50) NOT NULL,
    transaction_id varchar(255) DEFAULT '',
    reference varchar(255) DEFAULT '',
    status varchar(50) NOT NULL,
    donor_name varchar(255) NOT NULL,
    donor_email varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    completed_at datetime DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY donation_id (donation_id)
);
```

## Supported Features

### Payment Methods
- ✅ Credit/Debit Cards
- ✅ Mobile Money (All Tanzania operators)
- ✅ Bank transfers
- ✅ Masterpass QR
- ✅ Local payment methods

### Currencies
- ✅ TZS (Tanzanian Shilling)
- ✅ USD (US Dollar)

### Additional Features
- ✅ Custom branding (header/button colors)
- ✅ Order expiration (30 minutes)
- ✅ Success/Cancel redirects
- ✅ Email confirmations
- ✅ Transaction logging
- ✅ Webhook notifications

## Troubleshooting

### Common Issues

1. **404 Redirection Errors**: Fixed with robust URL generation
2. **Webhook Not Receiving**: Check webhook URL configuration
3. **Authentication Errors**: Verify API credentials
4. **Order Creation Failed**: Check API limits and data format

### Debug Information
- Check WordPress error logs
- Review Selcom API responses in logs
- Verify webhook payload format
- Test with Selcom's API documentation

## Support

### Selcom Support
- Email: info@selcom.net
- Documentation: https://developers.selcommobile.com/

### Integration Support
- Check WordPress error logs for detailed error messages
- Use the test file to verify configuration
- Review webhook notifications in logs

## Security Notes

1. **Remove Test File**: Delete `test-selcom-integration.php` after testing
2. **Secure Credentials**: Keep API keys secure and never expose in frontend
3. **HTTPS Required**: Ensure your site uses HTTPS for secure transactions
4. **Regular Updates**: Keep WordPress and theme updated

## Compliance

This integration follows:
- Selcom API best practices
- WordPress security standards
- PCI DSS requirements (through Selcom)
- Data protection regulations


