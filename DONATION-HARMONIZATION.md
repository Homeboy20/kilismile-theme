# KiliSmile Donation System Harmonization

## Overview

This update harmonizes the KiliSmile donation system by:

1. Retaining the legacy system (`/inc/donation-functions.php`) for backward compatibility
2. Implementing a modern OOP system (`/includes/payment-system/`) as the primary implementation
3. Creating a bridge layer to connect the two systems

## Changes Made

### 1. Legacy System Updates

- Renamed legacy functions with `_kilismile_legacy_` prefix to avoid conflicts:
  - `kilismile_donation_progress_bar()` → `_kilismile_legacy_donation_progress_bar()`
  - `kilismile_donation_form()` → `_kilismile_legacy_donation_form()`
  - `kilismile_process_donation()` → `_kilismile_legacy_process_donation()`
  - `kilismile_get_available_payment_methods()` → `_kilismile_legacy_get_available_payment_methods()`
  - etc.

- Commented out action and shortcode registrations that are now handled by the bridge system

### 2. Bridge System Implementation

- Created bridge functions that map legacy function calls to new OOP methods
- Ensured fallback to legacy functions if the OOP system is unavailable
- Maintained the original function names as wrappers for the new system

### 3. New OOP System Structure

- Core classes for donation handling, database interactions, and payment processing
- Abstract gateway class for implementing various payment providers
- Specific gateway implementations for PayPal, Stripe, and Selcom
- Unified database schema for storing donation data

### 4. System Integration

- Centralized loader for initializing all system components
- Automatic data migration from old format to new format
- Registration of shortcodes and action hooks
- Asset loading for styles and scripts

## How to Use

The donation system can be used in three ways:

1. **Via shortcodes** (recommended for most use cases):
   ```
   [kilismile_donation_form]
   [kilismile_donation_progress]
   ```

2. **Via template functions** (for backward compatibility):
   ```php
   echo kilismile_donation_form();
   echo kilismile_donation_progress_bar();
   ```

3. **Via direct OOP calls** (for advanced customization):
   ```php
   global $kilismile_donation_handler;
   echo $kilismile_donation_handler->render_donation_form();
   ```

## Documentation

See the full documentation in `/themes/kilismile/DONATION-SYSTEM.md` for detailed usage instructions.

## Important Notes

- The legacy system still exists but primarily as a fallback
- All new development should use the OOP system directly
- Template files using the old functions will continue to work through the bridge layer
- Database migrations happen automatically the first time the new system is loaded


