# KiliSmile Donation System Documentation

## Overview

The KiliSmile theme includes a dual donation system structure:

1. **Legacy System** (procedural functions in `/inc/donation-functions.php`)
2. **Modern System** (object-oriented classes in `/includes/payment-system/`)

This documentation explains how both systems work together and how to use either system in your templates.

## System Architecture

The donation system is designed with backward compatibility in mind:

- **Bridge Functions**: Connect legacy function calls to new OOP methods
- **Renamed Legacy Functions**: Original functions renamed with `_kilismile_legacy_` prefix to avoid conflicts
- **Global Instance**: Access the donation handler via `$kilismile_donation_handler` global variable

## Using the Donation System in Templates

### Shortcodes

```php
// Display a donation form
echo do_shortcode('[kilismile_donation_form currency="USD" amounts="10,25,50,100" title="Support Our Cause"]');

// Display a donation progress bar
echo do_shortcode('[kilismile_donation_progress currency="USD" goal="10000"]');
```

### Template Functions

```php
// Display a donation form
echo kilismile_donation_form([
    'currency' => 'USD',
    'title' => 'Support Our Cause',
    'show_amounts' => true,
    'show_progress' => true
]);

// Display a donation progress bar
echo kilismile_donation_progress_bar('USD');
```

### Using the Modern System Directly

```php
global $kilismile_donation_handler;

// Display a donation form
echo $kilismile_donation_handler->render_donation_form([
    'currency' => 'USD',
    'title' => 'Support Our Cause'
]);

// Display a donation progress bar
$progress_data = $kilismile_donation_handler->get_donation_progress('USD');
echo $kilismile_donation_handler->render_progress_bar($progress_data);
```

## System Components

### 1. Legacy System (`/inc/donation-functions.php`)

The original procedural system has been preserved with renamed functions. These functions are now prefixed with `_kilismile_legacy_` and act as fallbacks if the modern system fails.

### 2. Modern System (`/includes/payment-system/`)

The new object-oriented system includes:

- **Core Classes**:
  - `KiliSmile_Donation_Handler`: Main handler for donations
  - `KiliSmile_Donation_DB`: Database interactions
  - `KiliSmile_Payment_Gateway`: Abstract payment gateway class

- **Payment Gateways**:
  - Classes for each payment provider (PayPal, Stripe, Selcom)

- **Bridge System**:
  - Functions that map legacy calls to OOP methods

## Notes for Developers

- New development should use the OOP classes directly
- Avoid modifying the legacy functions except for bug fixes
- If extending the system, add new gateway classes that extend `KiliSmile_Payment_Gateway`
- All donation data is now stored in a unified format in the database


