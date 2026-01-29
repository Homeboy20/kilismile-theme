# Gateway Save Debug Instructions

## The Issue
The "Save Gateway Settings" button is not saving changes to payment gateway toggles.

## Debugging Steps

### 1. Check Basic Functionality
1. Go to your site: `your-site.com/wp-content/themes/kilismile/test-gateway-save.php`
2. This will show current gateway settings and allow manual testing

### 2. Check Browser Console for JavaScript Errors
1. Open **Admin → Theme Settings → Gateway Integration**
2. Press **F12** to open Developer Tools
3. Go to **Console** tab
4. Copy/paste this debug script and press Enter:

```javascript
// Gateway form debug - paste this in browser console
var gatewayForm = document.querySelector('#gateway-integration form');
console.log('Gateway form:', gatewayForm ? 'Found' : 'Not found');

if (gatewayForm) {
    gatewayForm.addEventListener('submit', function(e) {
        console.log('Form submitting with data:');
        var formData = new FormData(gatewayForm);
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
    });
}
```

### 3. Test Form Submission
1. Toggle a gateway setting (enable/disable)
2. Click **"Save Gateway Settings"**
3. Watch the console for debug messages
4. Look for the success message: "Gateway integration settings saved successfully!"

### 4. Check Server Logs
Look for these debug messages in your error log:
- "Gateway save triggered. POST data: ..."
- "Selcom enabled value: ..."
- "Azam enabled value: ..."

### 5. Manual Tests
Visit: `your-site.com/wp-content/themes/kilismile/test-gateway-save.php`
- Use the manual enable/disable links
- Test the form submission

## Common Issues & Solutions

### Issue 1: JavaScript Preventing Submission
**Symptoms**: Form doesn't submit, no success message
**Solution**: Check console for JavaScript errors

### Issue 2: Form Submits but No Changes Saved  
**Symptoms**: Success message shows but settings don't change
**Solution**: Check nonce validation and POST data

### Issue 3: Checkboxes Not Sending Data
**Symptoms**: Always shows disabled regardless of checkbox state
**Solution**: Checkbox HTML issue - check the checkbox `name` attributes

### Issue 4: Cached Settings
**Symptoms**: Changes save but don't appear to take effect
**Solution**: Clear browser cache and WordPress object cache

## Quick Fix Tests

### Test 1: Direct Database Update
Run this in your browser console on the admin page:
```javascript
// Test if AJAX saves work
fetch(window.location.href, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'save_gateway_integration=1&kilismile_selcom_enabled=1&kilismile_gateway_nonce=' + 
          document.querySelector('input[name="kilismile_gateway_nonce"]').value
}).then(response => response.text()).then(data => console.log(data));
```

### Test 2: Check Current Settings
```php
// Add this to any PHP page to check current values
echo 'Selcom: ' . get_option('kilismile_selcom_enabled', 'not set') . '<br>';
echo 'Azam: ' . get_option('kilismile_azam_pay_enabled', 'not set') . '<br>';
```

## Files Modified for Debugging

1. **admin/theme-settings.php** - Added debug logging to gateway save function
2. **test-gateway-save.php** - Created test page for manual verification
3. **gateway-debug.js** - Created JavaScript debugging script

## Next Steps

1. **First**: Test with the test page to see if basic save functionality works
2. **Second**: Check browser console for JavaScript errors during save
3. **Third**: Look at error logs for the debug messages
4. **Fourth**: If still not working, there may be a deeper form structure issue

The debug logging will help identify exactly where the problem is occurring.


