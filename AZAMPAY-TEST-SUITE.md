# AzamPay Plugin Test Suite

## Overview
This test suite provides comprehensive testing for the AzamPay payment integration within the KiliSmile payment plugin.

## Test Files Created

### 1. **Interactive Web Test** - `test-azampay-plugin.php`
**URL**: `http://kilismile.local/wp-content/themes/kilismile/test-azampay-plugin.php`

**Features**:
- ✅ System status checks (plugin active, classes loaded)
- ✅ AzamPay configuration verification
- ✅ Gateway availability testing for TZS currency
- ✅ Interactive payment form with real AJAX submission
- ✅ Complete debug information display
- ✅ Live payment testing with sample data

**Test Parameters**:
- Amount: 5000 TZS (configurable)
- Phone: +255700123456 (Tanzanian format)
- Currency: TZS (Tanzanian Shilling)
- Gateway: AzamPay / Enhanced AzamPay

### 2. **Class Direct Test** - `test-azampay-class.php`
**URL**: `http://kilismile.local/wp-content/themes/kilismile/test-azampay-class.php`

**Features**:
- ✅ Direct class instantiation testing
- ✅ Method availability verification
- ✅ Configuration status checking
- ✅ Settings table display
- ✅ WordPress environment debug info
- ✅ Instance properties inspection

### 3. **Command Line Test** - `test-azampay-cli.php`
**Usage**: `php wp-content/themes/kilismile/test-azampay-cli.php` (from WordPress root)

**Features**:
- ✅ Automated test scoring system
- ✅ Quick status overview
- ✅ Configuration recommendations
- ✅ Summary with pass/fail percentages

## Test Categories

### 🔍 **System Checks**
- Payment plugin activation status
- AzamPay class availability (standard & enhanced)
- Gateway factory functionality
- AJAX handler registration

### ⚙️ **Configuration Tests**
- Sandbox mode status
- Enhanced AzamPay mode
- API credentials verification (Client ID/Secret)
- App name and vendor ID settings

### 🏦 **Gateway Tests**
- TZS currency support verification
- Gateway availability for donations
- Instance creation and method testing
- Payment processor integration

### 💳 **Payment Flow Tests**
- Interactive payment form submission
- AJAX request/response handling
- Transaction data validation
- Error handling and debugging

## Expected Test Results

### ✅ **Successful Integration Shows**:
- Plugin Active: ✅ YES
- AzamPay Classes: ✅ Available
- AJAX Handler: ✅ Registered
- Gateway for TZS: ✅ Available
- Configuration: ✅ Credentials Set

### ⚠️ **Common Issues**:
- Missing API credentials (Client ID/Secret)
- Sandbox mode not configured
- AJAX handlers not registered
- Class loading failures

### 🔧 **Debug Information**:
- All loaded payment-related classes
- WordPress hook registrations
- Plugin settings and options
- Instance properties and methods

## Usage Instructions

### **Quick Test**:
1. Visit: `http://kilismile.local/wp-content/themes/kilismile/test-azampay-plugin.php`
2. Check system status indicators
3. Try the interactive payment test
4. Review debug information

### **Detailed Analysis**:
1. Visit: `http://kilismile.local/wp-content/themes/kilismile/test-azampay-class.php`
2. Examine class instantiation results
3. Review configuration table
4. Test payment processing functionality

### **Automated Testing**:
1. Run CLI test for quick pass/fail summary
2. Check recommendations for improvements
3. Use for CI/CD integration

## Configuration Requirements

### **Required Settings**:
- `kilismile_azampay_client_id` - AzamPay API Client ID
- `kilismile_azampay_client_secret` - AzamPay API Secret
- `kilismile_azampay_sandbox_mode` - Test/Live mode toggle

### **Optional Settings**:
- `kilismile_use_enhanced_azampay` - Enhanced integration mode
- `kilismile_azampay_app_name` - Application identifier
- `kilismile_azampay_vendor_id` - Vendor identification

## Security Notes

⚠️ **Important**: These test files display configuration information and should be:
- Used only in development environments
- Removed from production deployments
- Protected from public access if deployed

## Integration Status

The test suite verifies that:
- ✅ AzamPay classes are properly loaded through the plugin
- ✅ AJAX handlers are registered for payment processing
- ✅ Gateway factory recognizes AzamPay for TZS currency
- ✅ Configuration settings are accessible
- ✅ Payment flow can be tested end-to-end

## Next Steps

1. **Configure AzamPay API credentials** in WordPress admin
2. **Test with real AzamPay sandbox** environment
3. **Verify callback handling** for payment confirmations
4. **Test mobile money integrations** (M-Pesa, Airtel Money, etc.)
5. **Validate production readiness** with live credentials

---

**Status**: AzamPay plugin test suite is ready for comprehensive testing! 🚀

