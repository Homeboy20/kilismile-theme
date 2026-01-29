<?php
/**
 * Migration Helper for Moving Customizer Settings to Database Options
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migrate Theme Mod Settings to Database Options
 */
function kilismile_migrate_customizer_settings() {
    // Check if migration has already been run
    if (get_option('kilismile_settings_migrated', false)) {
        return;
    }
    
    $migration_map = array(
        // Donation Settings
        'kilismile_enable_donations' => 'kilismile_enable_donations',
        'kilismile_default_currency' => 'kilismile_default_currency',
        'kilismile_donation_goal_usd' => 'kilismile_donation_goal_usd',
        'kilismile_current_donations_usd' => 'kilismile_current_donations_usd',
        'kilismile_donation_goal_tzs' => 'kilismile_donation_goal_tzs',
        'kilismile_current_donations_tzs' => 'kilismile_current_donations_tzs',
        'kilismile_exchange_rate' => 'kilismile_exchange_rate',
        
        // International Payment Methods
        'kilismile_paypal_email' => 'kilismile_paypal_email',
        'kilismile_stripe_enabled' => 'kilismile_stripe_enabled',
        'kilismile_stripe_public_key' => 'kilismile_stripe_public_key',
        'kilismile_stripe_secret_key' => 'kilismile_stripe_secret_key',
        'kilismile_wire_transfer_details' => 'kilismile_wire_transfer_details',
        
        // Local Payment Methods
        'kilismile_mpesa_enabled' => 'kilismile_mpesa_enabled',
        'kilismile_mpesa_number' => 'kilismile_mpesa_number',
        'kilismile_mpesa_name' => 'kilismile_mpesa_name',
        'kilismile_tigo_pesa_enabled' => 'kilismile_tigo_pesa_enabled',
        'kilismile_tigo_pesa_number' => 'kilismile_tigo_pesa_number',
        'kilismile_airtel_money_enabled' => 'kilismile_airtel_money_enabled',
        'kilismile_airtel_money_number' => 'kilismile_airtel_money_number',
        'kilismile_local_bank_enabled' => 'kilismile_local_bank_enabled',
        'kilismile_local_bank_details' => 'kilismile_local_bank_details',
    );
    
    $migrated_count = 0;
    
    foreach ($migration_map as $theme_mod_key => $option_key) {
        $theme_mod_value = get_theme_mod($theme_mod_key);
        
        if ($theme_mod_value !== false && $theme_mod_value !== null && $theme_mod_value !== '') {
            // Only migrate if there's actually a value set
            update_option($option_key, $theme_mod_value);
            $migrated_count++;
        }
    }
    
    // Set default values for payment method enabled flags if not set
    $payment_defaults = array(
        'kilismile_paypal_enabled' => get_option('kilismile_paypal_email') ? 1 : 0,
        'kilismile_wire_transfer_enabled' => get_option('kilismile_wire_transfer_details') ? 1 : 0,
        'kilismile_selcom_enabled' => 1,
        'kilismile_azam_pay_enabled' => 1,
    );
    
    foreach ($payment_defaults as $option_key => $default_value) {
        if (get_option($option_key) === false) {
            update_option($option_key, $default_value);
        }
    }
    
    // Mark migration as complete
    update_option('kilismile_settings_migrated', true);
    update_option('kilismile_settings_migration_count', $migrated_count);
    update_option('kilismile_settings_migration_date', current_time('mysql'));
    
    // Add admin notice about successful migration
    add_action('admin_notices', function() use ($migrated_count) {
        if ($migrated_count > 0) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>' . __('KiliSmile Settings Migration Complete!', 'kilismile') . '</strong> ';
            echo sprintf(
                __('%d settings have been successfully migrated from the WordPress Customizer to the new admin settings page. <a href="%s">Click here to review your settings</a>.', 'kilismile'),
                $migrated_count,
                admin_url('admin.php?page=kilismile-settings')
            );
            echo '</p>';
            echo '</div>';
        }
    });
}

/**
 * Run migration on theme activation or admin init
 */
function kilismile_maybe_run_migration() {
    // Only run for administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Run migration
    kilismile_migrate_customizer_settings();
}
add_action('admin_init', 'kilismile_maybe_run_migration');

/**
 * Add migration status to settings page
 */
function kilismile_show_migration_status() {
    $migration_date = get_option('kilismile_settings_migration_date');
    $migration_count = get_option('kilismile_settings_migration_count', 0);
    
    if ($migration_date) {
        echo '<div class="notice notice-info" style="margin: 20px 0;">';
        echo '<p><strong>' . __('Migration Status:', 'kilismile') . '</strong> ';
        echo sprintf(
            __('Settings migrated on %s (%d items transferred)', 'kilismile'),
            wp_date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($migration_date)),
            $migration_count
        );
        echo '</p>';
        echo '</div>';
    }
}

/**
 * Reset migration (for development/testing)
 */
function kilismile_reset_migration() {
    // Only allow for administrators in development
    if (!current_user_can('manage_options') || !defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    if (isset($_GET['kilismile_reset_migration']) && wp_verify_nonce($_GET['nonce'], 'kilismile_reset_migration')) {
        delete_option('kilismile_settings_migrated');
        delete_option('kilismile_settings_migration_count');
        delete_option('kilismile_settings_migration_date');
        
        // Redirect to prevent accidental re-runs
        wp_redirect(admin_url('admin.php?page=kilismile-settings&migration_reset=1'));
        exit;
    }
}
add_action('admin_init', 'kilismile_reset_migration');

/**
 * Add reset migration link for development
 */
function kilismile_add_migration_tools() {
    if (!defined('WP_DEBUG') || !WP_DEBUG || !current_user_can('manage_options')) {
        return;
    }
    
    if (get_option('kilismile_settings_migrated')) {
        $reset_url = wp_nonce_url(
            admin_url('admin.php?page=kilismile-settings&kilismile_reset_migration=1'),
            'kilismile_reset_migration',
            'nonce'
        );
        
        echo '<div class="notice notice-warning" style="margin: 20px 0;">';
        echo '<p><strong>' . __('Development Tools:', 'kilismile') . '</strong> ';
        echo '<a href="' . esc_url($reset_url) . '" onclick="return confirm(\'' . esc_js(__('Are you sure you want to reset the migration? This is for development only.', 'kilismile')) . '\')">';
        echo __('Reset Migration Status', 'kilismile');
        echo '</a>';
        echo '</p>';
        echo '</div>';
    }
}

/**
 * Backward compatibility functions for theme_mod calls
 */
function kilismile_get_payment_option($key, $default = '') {
    // Map old theme_mod keys to new option keys
    $option_map = array(
        'kilismile_enable_donations' => 'kilismile_enable_donations',
        'kilismile_default_currency' => 'kilismile_default_currency',
        'kilismile_donation_goal_usd' => 'kilismile_donation_goal_usd',
        'kilismile_current_donations_usd' => 'kilismile_current_donations_usd',
        'kilismile_donation_goal_tzs' => 'kilismile_donation_goal_tzs',
        'kilismile_current_donations_tzs' => 'kilismile_current_donations_tzs',
        'kilismile_exchange_rate' => 'kilismile_exchange_rate',
        'kilismile_paypal_email' => 'kilismile_paypal_email',
        'kilismile_stripe_enabled' => 'kilismile_stripe_enabled',
        'kilismile_stripe_public_key' => 'kilismile_stripe_public_key',
        'kilismile_stripe_secret_key' => 'kilismile_stripe_secret_key',
        'kilismile_wire_transfer_details' => 'kilismile_wire_transfer_details',
        'kilismile_mpesa_enabled' => 'kilismile_mpesa_enabled',
        'kilismile_mpesa_number' => 'kilismile_mpesa_number',
        'kilismile_mpesa_name' => 'kilismile_mpesa_name',
        'kilismile_tigo_pesa_enabled' => 'kilismile_tigo_pesa_enabled',
        'kilismile_tigo_pesa_number' => 'kilismile_tigo_pesa_number',
        'kilismile_airtel_money_enabled' => 'kilismile_airtel_money_enabled',
        'kilismile_airtel_money_number' => 'kilismile_airtel_money_number',
        'kilismile_local_bank_enabled' => 'kilismile_local_bank_enabled',
        'kilismile_local_bank_details' => 'kilismile_local_bank_details',
    );
    
    if (isset($option_map[$key])) {
        return get_option($option_map[$key], $default);
    }
    
    // Fallback to theme_mod for unmapped keys
    return get_theme_mod($key, $default);
}

/**
 * Hook to replace theme_mod calls in templates
 */
function kilismile_override_theme_mod($default, $theme_mod) {
    // Override specific theme_mod calls for payment settings
    $payment_keys = array(
        'kilismile_enable_donations',
        'kilismile_default_currency',
        'kilismile_donation_goal_usd',
        'kilismile_current_donations_usd',
        'kilismile_donation_goal_tzs',
        'kilismile_current_donations_tzs',
        'kilismile_exchange_rate',
        'kilismile_paypal_email',
        'kilismile_stripe_enabled',
        'kilismile_stripe_public_key',
        'kilismile_stripe_secret_key',
        'kilismile_wire_transfer_details',
        'kilismile_mpesa_enabled',
        'kilismile_mpesa_number',
        'kilismile_mpesa_name',
        'kilismile_tigo_pesa_enabled',
        'kilismile_tigo_pesa_number',
        'kilismile_airtel_money_enabled',
        'kilismile_airtel_money_number',
        'kilismile_local_bank_enabled',
        'kilismile_local_bank_details',
    );
    
    if (in_array($theme_mod, $payment_keys)) {
        return get_option($theme_mod, $default);
    }
    
    return $default;
}
// Note: WordPress doesn't have a theme_mod filter, so we handle this in the functions above

?>


