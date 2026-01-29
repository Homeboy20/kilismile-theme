<?php
/**
 * Enhanced KiliSmile Settings Helper Functions
 * 
 * Utility functions for working with theme settings
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get KiliSmile theme setting
 * 
 * @param string $section Setting section
 * @param string $key Setting key
 * @param mixed $default Default value
 * @return mixed Setting value
 */
if (!function_exists('kilismile_get_setting')) {
    function kilismile_get_setting($section, $key, $default = null) {
        $settings = get_option('kilismile_settings', array());
        
        if (isset($settings[$section][$key])) {
            return $settings[$section][$key];
        }
        
        return $default;
    }
}

/**
 * Get all settings for a section
 * 
 * @param string $section Setting section
 * @param array $defaults Default values
 * @return array Section settings
 */
if (!function_exists('kilismile_get_section_settings')) {
    function kilismile_get_section_settings($section, $defaults = array()) {
        $settings = get_option('kilismile_settings', array());
        
        if (isset($settings[$section])) {
            return wp_parse_args($settings[$section], $defaults);
        }
        
        return $defaults;
    }
}

/**
 * Update KiliSmile theme setting
 * 
 * @param string $section Setting section
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @return bool Success status
 */
if (!function_exists('kilismile_update_setting')) {
    function kilismile_update_setting($section, $key, $value) {
        $settings = get_option('kilismile_settings', array());
        
        if (!isset($settings[$section])) {
            $settings[$section] = array();
        }
        
        $settings[$section][$key] = $value;
        
        return update_option('kilismile_settings', $settings);
    }
}

/**
 * Get organization information from enhanced settings
 * 
 * @return array Organization details
 */
if (!function_exists('kilismile_get_enhanced_organization_info')) {
    function kilismile_get_enhanced_organization_info() {
        $general_settings = kilismile_get_section_settings('general', array(
            'organization_name' => get_bloginfo('name'),
            'organization_tagline' => get_bloginfo('description'),
            'contact_info' => array(
                'phone' => '',
                'email' => get_option('admin_email'),
                'address' => ''
            )
        ));
        
        return $general_settings;
    }
}

/**
 * Get color scheme
 * 
 * @return array Color values
 */
if (!function_exists('kilismile_get_color_scheme')) {
    function kilismile_get_color_scheme() {
        $appearance_settings = kilismile_get_section_settings('appearance');
        $color_scheme = isset($appearance_settings['color_scheme']) ? $appearance_settings['color_scheme'] : 'default';
        
        if ($color_scheme === 'custom' && isset($appearance_settings['custom_colors'])) {
            return $appearance_settings['custom_colors'];
        }
        
        // Return predefined color schemes
        $schemes = array(
            'default' => array(
                'primary' => '#2271b1',
                'secondary' => '#00a32a',
                'accent' => '#ff6b35',
                'text' => '#333333',
                'background' => '#ffffff'
            ),
            'green' => array(
                'primary' => '#00a32a',
                'secondary' => '#2271b1',
                'accent' => '#ffb900',
                'text' => '#333333',
                'background' => '#ffffff'
            ),
            'red' => array(
                'primary' => '#d63638',
                'secondary' => '#00a32a',
                'accent' => '#ff6b35',
                'text' => '#333333',
                'background' => '#ffffff'
            ),
            'orange' => array(
                'primary' => '#ff6b35',
                'secondary' => '#2271b1',
                'accent' => '#00a32a',
                'text' => '#333333',
                'background' => '#ffffff'
            )
        );
        
        return isset($schemes[$color_scheme]) ? $schemes[$color_scheme] : $schemes['default'];
    }
}

/**
 * Get typography settings
 * 
 * @return array Typography values
 */
if (!function_exists('kilismile_get_typography')) {
    function kilismile_get_typography() {
        $appearance_settings = kilismile_get_section_settings('appearance');
        
        return isset($appearance_settings['typography']) ? $appearance_settings['typography'] : array(
            'headings' => 'Roboto, sans-serif',
            'body' => 'Open Sans, sans-serif'
        );
    }
}

/**
 * Get social media links from enhanced settings
 * 
 * @return array Social media URLs
 */
if (!function_exists('kilismile_get_enhanced_social_links')) {
    function kilismile_get_enhanced_social_links() {
        $social_settings = kilismile_get_section_settings('social');
        
        return isset($social_settings['social_media']) ? $social_settings['social_media'] : array();
    }
}

/**
 * Get header settings
 * 
 * @return array Header configuration
 */
function kilismile_get_header_settings() {
    return kilismile_get_section_settings('header', array(
        'header_layout' => 'standard',
        'logo_settings' => array(
            'size' => 60,
            'border_radius' => 0,
            'retina_support' => true
        )
    ));
}

/**
 * Get performance settings
 * 
 * @return array Performance optimization options
 */
function kilismile_get_performance_settings() {
    return kilismile_get_section_settings('performance', array(
        'optimization' => array('lazy_loading', 'cache_assets')
    ));
}

/**
 * Check if donation system is enabled
 * 
 * @return bool Donation system status
 */
function kilismile_is_donation_enabled() {
    return (bool) kilismile_get_setting('donations', 'donation_system', true);
}

/**
 * Get donation goals
 * 
 * @return array Donation goals
 */
function kilismile_get_donation_goals() {
    $goals = kilismile_get_setting('donations', 'donation_goals', array());
    return is_array($goals) ? $goals : array();
}

/**
 * Get custom CSS
 * 
 * @return string Custom CSS code
 */
function kilismile_get_custom_css() {
    return kilismile_get_setting('advanced', 'custom_css', '');
}

/**
 * Get custom JavaScript
 * 
 * @return string Custom JavaScript code
 */
function kilismile_get_custom_js() {
    return kilismile_get_setting('advanced', 'custom_js', '');
}

/**
 * Generate CSS variables from color scheme
 * 
 * @return string CSS custom properties
 */
function kilismile_generate_css_variables() {
    $colors = kilismile_get_color_scheme();
    $typography = kilismile_get_typography();
    
    $css = ':root {';
    
    // Color variables
    foreach ($colors as $key => $value) {
        $css .= '--kilismile-color-' . str_replace('_', '-', $key) . ': ' . $value . ';';
    }
    
    // Typography variables
    foreach ($typography as $key => $value) {
        $css .= '--kilismile-font-' . str_replace('_', '-', $key) . ': ' . $value . ';';
    }
    
    $css .= '}';
    
    return $css;
}

/**
 * Output custom styles in head
 */
function kilismile_output_custom_styles() {
    $css_variables = kilismile_generate_css_variables();
    $custom_css = kilismile_get_custom_css();
    
    if ($css_variables || $custom_css) {
        echo '<style id="kilismile-custom-styles">';
        echo $css_variables;
        if ($custom_css) {
            echo "\n" . $custom_css;
        }
        echo '</style>';
    }
}
add_action('wp_head', 'kilismile_output_custom_styles');

/**
 * Output custom JavaScript in footer
 */
function kilismile_output_custom_js() {
    $custom_js = kilismile_get_custom_js();
    
    if ($custom_js) {
        echo '<script id="kilismile-custom-js">';
        echo $custom_js;
        echo '</script>';
    }
}
add_action('wp_footer', 'kilismile_output_custom_js');

/**
 * Add body classes based on enhanced settings
 * 
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
if (!function_exists('kilismile_enhanced_body_classes')) {
    function kilismile_enhanced_body_classes($classes) {
        // Add color scheme class
        $appearance_settings = kilismile_get_section_settings('appearance');
        $color_scheme = isset($appearance_settings['color_scheme']) ? $appearance_settings['color_scheme'] : 'default';
        $classes[] = 'color-scheme-' . $color_scheme;
        
        // Add header layout class
        $header_settings = kilismile_get_header_settings();
        $header_layout = isset($header_settings['header_layout']) ? $header_settings['header_layout'] : 'standard';
        $classes[] = 'header-layout-' . $header_layout;
        
        // Add site mode class
        $site_mode = kilismile_get_setting('general', 'site_mode', 'charity');
        $classes[] = 'site-mode-' . $site_mode;
        
        // Add performance classes
        $performance_settings = kilismile_get_performance_settings();
        $optimizations = isset($performance_settings['optimization']) ? $performance_settings['optimization'] : array();
        
        if (in_array('lazy_loading', $optimizations)) {
            $classes[] = 'lazy-loading-enabled';
        }
        
        return $classes;
    }
}

// Hook the enhanced body classes function
if (!function_exists('kilismile_body_classes')) {
    // If the original function doesn't exist, use our enhanced version
    add_filter('body_class', 'kilismile_enhanced_body_classes');
} else {
    // If original exists, add our enhanced classes as additional filter
    add_filter('body_class', 'kilismile_enhanced_body_classes', 20);
}

/**
 * Add preload hints for fonts
 */
function kilismile_add_font_preloads() {
    $typography = kilismile_get_typography();
    
    foreach ($typography as $font_family) {
        if (strpos($font_family, 'serif') === false && strpos($font_family, 'sans-serif') === false && strpos($font_family, 'monospace') === false) {
            // This is likely a Google Font
            $font_name = explode(',', $font_family)[0];
            $font_url = 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', trim($font_name)) . ':wght@400;600&display=swap';
            echo '<link rel="preload" href="' . esc_url($font_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        }
    }
}
add_action('wp_head', 'kilismile_add_font_preloads', 1);

/**
 * Add logo styling based on settings
 */
function kilismile_logo_styles() {
    $header_settings = kilismile_get_header_settings();
    $logo_settings = isset($header_settings['logo_settings']) ? $header_settings['logo_settings'] : array();
    
    $size = isset($logo_settings['size']) ? $logo_settings['size'] : 60;
    $border_radius = isset($logo_settings['border_radius']) ? $logo_settings['border_radius'] : 0;
    
    echo '<style>';
    echo '.custom-logo { max-height: ' . intval($size) . 'px; border-radius: ' . intval($border_radius) . '%; }';
    echo '</style>';
}
add_action('wp_head', 'kilismile_logo_styles');

/**
 * Add structured data for organization
 */
function kilismile_add_organization_schema() {
    // Try enhanced settings first, fallback to original function if available
    if (function_exists('kilismile_get_enhanced_organization_info')) {
        $enhanced_info = kilismile_get_enhanced_organization_info();
        $org_info = array(
            'organization_name' => $enhanced_info['organization_name'],
            'organization_tagline' => $enhanced_info['organization_tagline'],
            'contact_info' => $enhanced_info['contact_info']
        );
    } else {
        // Fallback to original function
        $org_info = array(
            'organization_name' => get_bloginfo('name'),
            'organization_tagline' => get_bloginfo('description'),
            'contact_info' => array(
                'phone' => '',
                'email' => get_option('admin_email'),
                'address' => ''
            )
        );
    }
    
    $social_links = function_exists('kilismile_get_enhanced_social_links') ? kilismile_get_enhanced_social_links() : array();
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $org_info['organization_name'],
        'description' => $org_info['organization_tagline'],
        'url' => home_url(),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full')
        )
    );
    
    if (!empty($org_info['contact_info']['phone'])) {
        $schema['telephone'] = $org_info['contact_info']['phone'];
    }
    
    if (!empty($org_info['contact_info']['email'])) {
        $schema['email'] = $org_info['contact_info']['email'];
    }
    
    if (!empty($org_info['contact_info']['address'])) {
        $schema['address'] = array(
            '@type' => 'PostalAddress',
            'streetAddress' => $org_info['contact_info']['address']
        );
    }
    
    if (!empty($social_links)) {
        $schema['sameAs'] = array_values(array_filter($social_links));
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('wp_head', 'kilismile_add_organization_schema');

/**
 * Enqueue Google Fonts
 */
function kilismile_enqueue_google_fonts() {
    $typography = kilismile_get_typography();
    $fonts_to_load = array();
    
    foreach ($typography as $font_family) {
        // Skip system fonts
        if (strpos($font_family, 'serif') === false && strpos($font_family, 'sans-serif') === false && strpos($font_family, 'monospace') === false) {
            $font_name = explode(',', $font_family)[0];
            $fonts_to_load[] = str_replace(' ', '+', trim($font_name)) . ':400,600';
        }
    }
    
    if (!empty($fonts_to_load)) {
        $font_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $fonts_to_load) . '&display=swap';
        wp_enqueue_style('kilismile-google-fonts', $font_url, array(), null);
    }
}
add_action('wp_enqueue_scripts', 'kilismile_enqueue_google_fonts');

/**
 * Add theme settings to REST API for live preview
 */
function kilismile_register_rest_settings() {
    register_rest_field('settings', 'kilismile_settings', array(
        'get_callback' => function() {
            return get_option('kilismile_settings', array());
        },
        'update_callback' => function($value) {
            return update_option('kilismile_settings', $value);
        },
        'schema' => array(
            'type' => 'object',
            'description' => 'KiliSmile theme settings',
        ),
    ));
}
add_action('rest_api_init', 'kilismile_register_rest_settings');

/**
 * Performance optimizations based on settings
 */
function kilismile_performance_optimizations() {
    $performance_settings = kilismile_get_performance_settings();
    $optimizations = isset($performance_settings['optimization']) ? $performance_settings['optimization'] : array();
    
    // Lazy loading for images
    if (in_array('lazy_loading', $optimizations)) {
        add_filter('wp_get_attachment_image_attributes', function($attr) {
            $attr['loading'] = 'lazy';
            return $attr;
        });
    }
    
    // Disable emoji scripts if optimization is enabled
    if (in_array('minify_js', $optimizations)) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }
    
    // Remove unnecessary WordPress features
    if (in_array('cache_assets', $optimizations)) {
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_generator');
    }
}
add_action('init', 'kilismile_performance_optimizations');

/**
 * Add settings import/export to admin bar
 */
function kilismile_admin_bar_settings($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'kilismile-settings',
        'title' => '<span class="ab-icon dashicons dashicons-heart"></span> KiliSmile Settings',
        'href' => admin_url('admin.php?page=kilismile-settings'),
        'meta' => array(
            'class' => 'kilismile-admin-bar-link'
        )
    ));
}
add_action('admin_bar_menu', 'kilismile_admin_bar_settings', 100);


