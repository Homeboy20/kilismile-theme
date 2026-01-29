# Payment Gateway Toggle Troubleshooting Guide

## Problem Fixed: Gateway Enable/Disable Not Working

The issue with payment gateway toggles not working properly has been fixed. There were several interconnected problems:

1. **Forced Default Enablement**: Gateways were being forcefully enabled in initialization functions
2. **Form Submission Issues**: The "Save Gateway Settings" button wasn't properly saving changes
3. **Inconsistent Default Values**: Admin settings used inconsistent default values
4. **Checkbox State Management**: Toggle states weren't being properly tracked
5. **Unchecked Checkbox Problem**: HTML forms don't submit unchecked checkboxes, causing disabled states to be lost

## What Has Been Fixed

We've implemented several changes to resolve these issues:

1. **Initialization Functions**:
   - Changed all gateway defaults from `1` (enabled) to `0` (disabled)
   - Updated `kilismile_init_default_payment_settings()` to initialize all gateways as disabled
   - Modified `kilismile_force_init_payment_settings()` to ensure consistent disabled defaults

2. **Admin Settings**:
   - Updated default values in `theme-settings.php` to use `0` as default
   - Ensured consistent option retrieval with `get_option('kilismile_mpesa_enabled', 0)`
   - Fixed the form submission handler to properly process all toggle states

3. **JavaScript Enhancement (Multi-Layered Approach)**:
   - Added `gateway-toggle-fix.js` to ensure unchecked checkboxes are properly submitted
   - This script adds hidden fields for unchecked toggles to ensure they're submitted as "0"
   - Added `hidden-field-implementation.js` that pre-emptively adds hidden fields on page load
   - Created inline JavaScript in the direct fix file as an additional fail-safe
   - Improved console logging for debugging toggle state changes

4. **Diagnostic Tools**:
   - Created `payment-gateway-fix.php` to diagnose and fix toggle issues
   - Added comprehensive gateway status display and reset functionality
   - Added `gateway-toggle-diagnostic.php` for direct testing of toggle functionality
   - Added a debug panel to monitor toggle states in real-time (visible when WP_DEBUG is enabled)

5. **Form Submission Logic**:
   - Completely rewrote form submission handling to ensure all toggles are processed
   - Added explicit toggle state tracking for both checked and unchecked states
   - Created a direct-fix implementation that hooks into admin_init
   - Added error logging to track toggle state changes

## How to Test

1. **Visit the Diagnostic Page**:
   - Go to `/wp-content/themes/kilismile/gateway-toggle-diagnostic.php`
   - This shows the current status of all gateway settings
   - You can toggle gateways on/off to directly test save functionality
   - This bypasses the admin form to isolate any issues

2. **Test Admin Settings**:
   - Go to Admin → Theme Settings → Gateway Integration
   - Enable/disable gateways and save changes
   - Verify changes are properly saved by refreshing the page
   - Try disabling gateways that were previously enabled to verify this specific fix

3. **Monitor Browser Console**:
   - Open browser developer tools (F12)
   - Go to the Console tab
   - Watch for the toggle tracking messages when submitting the form
   - This confirms the JavaScript enhancement is working correctly

4. **Check the Debug Panel**:
   - If WP_DEBUG is enabled, a toggle debug panel will appear in the bottom right
   - This shows real-time status of all toggle states
   - Refresh after saving to confirm changes are properly persisted

5. **Test Donation Form**:
   - Check that only enabled payment methods appear in the donation form
   - Verify disabled payment methods are properly hidden

## Technical Details of the Fix

### The Core Issue

HTML forms do not submit unchecked checkboxes. This means when you disable a gateway by unchecking its checkbox, that value isn't sent to the server during form submission. Our fix addresses this in multiple ways:

1. **Pre-emptive Hidden Fields**: We add hidden fields for all toggles when the page loads:
   ```javascript
   $('.method-toggle').each(function() {
       const toggleName = $(this).attr('name');
       const toggleForm = $(this).closest('form');
       
       const hiddenField = $('<input>', {
           type: 'hidden',
           name: toggleName,
           value: '0'
       });
       
       toggleForm.append(hiddenField);
   });
   ```

2. **Form Submission Enhancement**: We add hidden fields when the form is submitted:
   ```javascript
   $('form:has([name="save_gateway_integration"])').on('submit', function() {
       gatewayToggles.forEach(function(toggle) {
           var $checkbox = $('input[name="' + toggle + '"]');
           if (!$checkbox.is(':checked')) {
               $(this).append('<input type="hidden" name="' + toggle + '" value="0">');
           }
       }, this);
   });
   ```

3. **Server-Side Fallback**: We've rewritten the form submission handler to explicitly check each possible toggle:
   ```php
   // Process all gateway toggles - ensuring both checked AND unchecked states are handled
   foreach ($gateway_toggles as $toggle) {
       // If toggle exists in POST, use its value (1 for checked, 0 for unchecked)
       // If toggle doesn't exist in POST (unchecked), default to 0 (disabled)
       $value = (isset($_POST[$toggle]) && $_POST[$toggle] == 1) ? 1 : 0;
       
       // Update the option and log the change
       $current_value = get_option($toggle, 0);
       update_option($toggle, $value);
       error_log("THEME SETTINGS: $toggle changed from $current_value to $value");
   }
   ```

4. **Direct Fix Implementation**: We've added a fail-safe hook that processes form submissions:
   ```php
   function kilismile_fix_gateway_toggle_submissions() {
       // Only run on admin pages
       if (!is_admin()) {
           return;
       }
       
       // Check if we're processing a gateway settings form submission
       if (isset($_POST['save_gateway_integration']) && isset($_POST['kilismile_gateway_nonce'])) {
           // Process all gateway toggles explicitly
           foreach ($gateway_toggles as $toggle) {
               // If toggle exists in POST and is set to 1, enable it; otherwise disable it
               $value = (isset($_POST[$toggle]) && $_POST[$toggle] == 1) ? 1 : 0;
               
               // Save the value
               update_option($toggle, $value);
           }
       }
   }
   add_action('admin_init', 'kilismile_fix_gateway_toggle_submissions', 5);
   ```

### Debugging Tools

We've added enhanced debugging tools to track toggle states:
1. A new diagnostic page at `/gateway-toggle-diagnostic.php` for direct toggle testing
2. JavaScript console logging to track form submission
3. PHP error logging to verify server-side processing
4. Real-time debug panel to monitor toggle states

## Comprehensive Solution Components

Our approach uses multiple redundant methods to ensure robustness:

1. **Pre-emptive Hidden Fields**: Added on page load through hidden-field-implementation.js
2. **Form Submission Enhancement**: Added through gateway-toggle-fix.js
3. **Inline JavaScript**: Added through gateway-toggle-direct-fix.php
4. **Server-Side Processing**: Updated in theme-settings.php
5. **Direct Fix Hook**: Implemented in gateway-toggle-direct-fix.php
6. **Debug Panel**: Visible when WP_DEBUG is enabled
7. **Diagnostic Tool**: Available at gateway-toggle-diagnostic.php

This multi-layered approach ensures that toggle states are correctly saved regardless of how the form is submitted or what JavaScript might be available in the environment.

## Need More Help?

If you encounter any further issues:
1. Run diagnostics at `/wp-content/themes/kilismile/gateway-toggle-diagnostic.php`
2. Check browser console for JavaScript output
3. Check server logs for error messages containing "THEME SETTINGS" or "GATEWAY FIX"
4. Enable WP_DEBUG to view the toggle debug panel
5. Contact the theme developer with specific details about the issue


