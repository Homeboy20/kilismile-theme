<?php
/**
 * KiliSmile Settings Compatibility Layer
 * 
 * Provides backward compatibility between old and new settings functions
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Compatibility wrapper for organization info
 * This bridges the gap between the original template function and enhanced settings
 */
if (!function_exists('kilismile_get_organization_info_compat')) {
    function kilismile_get_organization_info_compat() {
        // Check if enhanced settings are available
        if (function_exists('kilismile_get_enhanced_organization_info')) {
            $enhanced_info = kilismile_get_enhanced_organization_info();
            
            // Convert enhanced format to original format for compatibility
            return array(
                'name' => $enhanced_info['organization_name'],
                'tagline' => $enhanced_info['organization_tagline'],
                'registration' => get_theme_mod('kilismile_registration', '07NGO/R/6067'),
                'founded' => 'April 25, 2024',
                'registration_date' => 'April 25, 2024',
                'phone' => $enhanced_info['contact_info']['phone'] ?: get_theme_mod('kilismile_phone', '+255763495575/+255735495575'),
                'email' => $enhanced_info['contact_info']['email'] ?: get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
                'address' => $enhanced_info['contact_info']['address'] ?: get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania'),
                'instagram' => get_theme_mod('kilismile_instagram', 'https://instagram.com/kili_smile'),
                'facebook' => get_theme_mod('kilismile_facebook', ''),
                'twitter' => get_theme_mod('kilismile_twitter', ''),
            );
        }
        
        // Fallback to original function if available
        if (function_exists('kilismile_get_organization_info')) {
            return kilismile_get_organization_info();
        }
        
        // Ultimate fallback
        return array(
            'name' => get_bloginfo('name'),
            'tagline' => get_bloginfo('description'),
            'phone' => '',
            'email' => get_option('admin_email'),
            'address' => ''
        );
    }
}

/**
 * Compatibility wrapper for social links
 */
if (!function_exists('kilismile_get_social_links_compat')) {
    function kilismile_get_social_links_compat() {
        // Check if enhanced settings are available
        if (function_exists('kilismile_get_enhanced_social_links')) {
            $enhanced_links = kilismile_get_enhanced_social_links();
            
            // Convert enhanced format to original format
            $formatted_links = array();
            foreach ($enhanced_links as $network => $url) {
                if (!empty($url)) {
                    $icons = array(
                        'facebook' => 'fab fa-facebook-f',
                        'twitter' => 'fab fa-twitter',
                        'instagram' => 'fab fa-instagram',
                        'linkedin' => 'fab fa-linkedin-in',
                        'youtube' => 'fab fa-youtube',
                        'whatsapp' => 'fab fa-whatsapp'
                    );
                    
                    $formatted_links[$network] = array(
                        'url' => $url,
                        'icon' => isset($icons[$network]) ? $icons[$network] : 'fas fa-link',
                        'label' => sprintf(__('Follow us on %s', 'kilismile'), ucfirst($network))
                    );
                }
            }
            
            return $formatted_links;
        }
        
        // Fallback to original function if available
        if (function_exists('kilismile_get_social_links')) {
            return kilismile_get_social_links();
        }
        
        return array();
    }
}

/**
 * Get the best available color scheme
 */
if (!function_exists('kilismile_get_active_colors')) {
    function kilismile_get_active_colors() {
        if (function_exists('kilismile_get_color_scheme')) {
            return kilismile_get_color_scheme();
        }
        
        // Fallback to theme customizer colors
        return array(
            'primary' => get_theme_mod('primary_color', '#2271b1'),
            'secondary' => get_theme_mod('secondary_color', '#00a32a'),
            'accent' => get_theme_mod('accent_color', '#ff6b35'),
            'text' => get_theme_mod('text_color', '#333333'),
            'background' => get_theme_mod('background_color', '#ffffff')
        );
    }
}

/**
 * Helper function to check if enhanced settings are active
 */
if (!function_exists('kilismile_is_enhanced_settings_active')) {
    function kilismile_is_enhanced_settings_active() {
        return function_exists('kilismile_get_setting') && !empty(get_option('kilismile_settings'));
    }
}

/**
 * Get setting with automatic fallback
 */
if (!function_exists('kilismile_get_option_with_fallback')) {
    function kilismile_get_option_with_fallback($enhanced_section, $enhanced_key, $fallback_option, $default = '') {
        if (kilismile_is_enhanced_settings_active()) {
            return kilismile_get_setting($enhanced_section, $enhanced_key, $default);
        }
        
        return get_option($fallback_option, $default);
    }
}

/**
 * Migrate a single setting from old to new format
 */
if (!function_exists('kilismile_migrate_single_setting')) {
    function kilismile_migrate_single_setting($old_option, $new_section, $new_key) {
        if (!kilismile_is_enhanced_settings_active()) {
            return false;
        }
        
        $old_value = get_option($old_option);
        if ($old_value !== false) {
            return kilismile_update_setting($new_section, $new_key, $old_value);
        }
        
        return false;
    }
}

/**
 * Display admin notice for settings migration if needed
 */
function kilismile_maybe_show_migration_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (kilismile_is_enhanced_settings_active()) {
        return; // Enhanced settings already active
    }
    
    // Check if old settings exist
    $has_old_settings = false;
    $old_options = array(
        'kilismile_contact_phone',
        'kilismile_contact_email',
        'kilismile_facebook_url',
        'kilismile_twitter_url',
        'kilismile_enable_donations'
    );
    
    foreach ($old_options as $option) {
        if (get_option($option)) {
            $has_old_settings = true;
            break;
        }
    }
    
    if ($has_old_settings) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<h3>' . __('KiliSmile Enhanced Settings Available', 'kilismile') . '</h3>';
        echo '<p>' . __('Your theme now supports enhanced settings with more customization options.', 'kilismile') . '</p>';
        echo '<p><a href="' . admin_url('admin.php?page=kilismile-settings') . '" class="button button-primary">' . __('Upgrade Settings', 'kilismile') . '</a></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'kilismile_maybe_show_migration_notice');

/**
 * Compatibility wrapper for body classes function
 * Bridges the gap between old and enhanced body classes
 */
if (!function_exists('kilismile_body_classes_compatibility')) {
    function kilismile_body_classes_compatibility($classes) {
        // Call enhanced body classes if available
        if (function_exists('kilismile_enhanced_body_classes')) {
            $classes = kilismile_enhanced_body_classes($classes);
        }
        
        // Call original body classes if available and different from enhanced
        if (function_exists('kilismile_body_classes') && !function_exists('kilismile_enhanced_body_classes')) {
            $classes = kilismile_body_classes($classes);
        }
        
        return $classes;
    }
}

/**
 * Hook the compatibility function for body classes
 * This ensures both old and new functionality work together
 */
add_filter('body_class', 'kilismile_body_classes_compatibility', 30);


