<?php
/**
 * Header Customizer Options
 *
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Header Customizer Options
 */
function kilismile_header_customizer($wp_customize) {
    
    // Header Section
    $wp_customize->add_section('kilismile_header_section', array(
        'title'    => __('Header Settings', 'kilismile'),
        'priority' => 30,
    ));

    // Header Layout Style
    $wp_customize->add_setting('kilismile_header_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'kilismile_sanitize_select',
    ));
    
    $wp_customize->add_control('kilismile_header_layout', array(
        'label'    => __('Header Layout', 'kilismile'),
        'section'  => 'kilismile_header_section',
        'type'     => 'select',
        'choices'  => array(
            'default'     => __('Default Header', 'kilismile'),
            'transparent' => __('Transparent Header', 'kilismile'),
            'minimal'     => __('Minimal Header', 'kilismile'),
            'centered'    => __('Centered Header', 'kilismile'),
        ),
    ));

    // Header Background Type
    $wp_customize->add_setting('kilismile_header_bg_type', array(
        'default'           => 'solid',
        'sanitize_callback' => 'kilismile_sanitize_select',
    ));
    
    $wp_customize->add_control('kilismile_header_bg_type', array(
        'label'    => __('Header Background Type', 'kilismile'),
        'section'  => 'kilismile_header_section',
        'type'     => 'select',
        'choices'  => array(
            'solid'       => __('Solid Color', 'kilismile'),
            'transparent' => __('Transparent', 'kilismile'),
            'gradient'    => __('Gradient', 'kilismile'),
            'image'       => __('Background Image', 'kilismile'),
        ),
    ));

    // Header Background Color
    $wp_customize->add_setting('kilismile_header_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_header_bg_color', array(
        'label'    => __('Header Background Color', 'kilismile'),
        'section'  => 'kilismile_header_section',
    )));

    // Header Text Color
    $wp_customize->add_setting('kilismile_header_text_color', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_header_text_color', array(
        'label'    => __('Header Text Color', 'kilismile'),
        'section'  => 'kilismile_header_section',
    )));

    // Header Transparency Level
    $wp_customize->add_setting('kilismile_header_transparency', array(
        'default'           => 0.95,
        'sanitize_callback' => 'kilismile_sanitize_float',
    ));
    
    $wp_customize->add_control('kilismile_header_transparency', array(
        'label'       => __('Header Transparency', 'kilismile'),
        'section'     => 'kilismile_header_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.1,
        ),
        'description' => __('0 = fully transparent, 1 = fully opaque', 'kilismile'),
    ));

    // Header Height
    $wp_customize->add_setting('kilismile_header_height', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_header_height', array(
        'label'       => __('Header Height (px)', 'kilismile'),
        'section'     => 'kilismile_header_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 60,
            'max'  => 150,
            'step' => 5,
        ),
    ));

    // Sticky Header
    $wp_customize->add_setting('kilismile_sticky_header', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_sticky_header', array(
        'label'   => __('Enable Sticky Header', 'kilismile'),
        'section' => 'kilismile_header_section',
        'type'    => 'checkbox',
    ));

    // Header Shadow
    $wp_customize->add_setting('kilismile_header_shadow', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_header_shadow', array(
        'label'   => __('Enable Header Shadow', 'kilismile'),
        'section' => 'kilismile_header_section',
        'type'    => 'checkbox',
    ));

    // Header Border
    $wp_customize->add_setting('kilismile_header_border', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_header_border', array(
        'label'   => __('Enable Header Border', 'kilismile'),
        'section' => 'kilismile_header_section',
        'type'    => 'checkbox',
    ));

    // Header Border Color
    $wp_customize->add_setting('kilismile_header_border_color', array(
        'default'           => '#e0e0e0',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_header_border_color', array(
        'label'    => __('Header Border Color', 'kilismile'),
        'section'  => 'kilismile_header_section',
    )));

    // Logo Section
    $wp_customize->add_section('kilismile_logo_section', array(
        'title'    => __('Logo Settings', 'kilismile'),
        'priority' => 31,
    ));

    // Logo Size
    $wp_customize->add_setting('kilismile_logo_size', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_logo_size', array(
        'label'       => __('Logo Size (px)', 'kilismile'),
        'section'     => 'kilismile_logo_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 500,
            'step' => 1,
        ),
    ));

    // Logo Border
    $wp_customize->add_setting('kilismile_logo_border', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_logo_border', array(
        'label'   => __('Enable Logo Border', 'kilismile'),
        'section' => 'kilismile_logo_section',
        'type'    => 'checkbox',
    ));

    // Logo Border Color
    $wp_customize->add_setting('kilismile_logo_border_color', array(
        'default'           => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_logo_border_color', array(
        'label'    => __('Logo Border Color', 'kilismile'),
        'section'  => 'kilismile_logo_section',
    )));

    // Logo Border Radius
    $wp_customize->add_setting('kilismile_logo_border_radius', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_logo_border_radius', array(
        'label'       => __('Logo Border Radius (%)', 'kilismile'),
        'section'     => 'kilismile_logo_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 50,
            'step' => 5,
        ),
    ));

    // Navigation Section
    $wp_customize->add_section('kilismile_navigation_section', array(
        'title'    => __('Navigation Settings', 'kilismile'),
        'priority' => 32,
    ));

    // Menu Style
    $wp_customize->add_setting('kilismile_menu_style', array(
        'default'           => 'horizontal',
        'sanitize_callback' => 'kilismile_sanitize_select',
    ));
    
    $wp_customize->add_control('kilismile_menu_style', array(
        'label'    => __('Menu Style', 'kilismile'),
        'section'  => 'kilismile_navigation_section',
        'type'     => 'select',
        'choices'  => array(
            'horizontal' => __('Horizontal Menu', 'kilismile'),
            'vertical'   => __('Vertical Menu', 'kilismile'),
            'hamburger'  => __('Hamburger Menu', 'kilismile'),
        ),
    ));

    // Menu Text Color
    $wp_customize->add_setting('kilismile_menu_text_color', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_menu_text_color', array(
        'label'    => __('Menu Text Color', 'kilismile'),
        'section'  => 'kilismile_navigation_section',
    )));

    // Menu Hover Color
    $wp_customize->add_setting('kilismile_menu_hover_color', array(
        'default'           => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_menu_hover_color', array(
        'label'    => __('Menu Hover Color', 'kilismile'),
        'section'  => 'kilismile_navigation_section',
    )));

    // Dropdown Background
    $wp_customize->add_setting('kilismile_dropdown_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_dropdown_bg_color', array(
        'label'    => __('Dropdown Background Color', 'kilismile'),
        'section'  => 'kilismile_navigation_section',
    )));

    // CTA Button Section
    $wp_customize->add_section('kilismile_cta_section', array(
        'title'    => __('CTA Button Settings', 'kilismile'),
        'priority' => 33,
    ));

    // Show CTA Button
    $wp_customize->add_setting('kilismile_show_cta_button', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_show_cta_button', array(
        'label'   => __('Show CTA Button', 'kilismile'),
        'section' => 'kilismile_cta_section',
        'type'    => 'checkbox',
    ));

    // CTA Button Text
    $wp_customize->add_setting('kilismile_cta_text', array(
        'default'           => 'Donate Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_cta_text', array(
        'label'   => __('CTA Button Text', 'kilismile'),
        'section' => 'kilismile_cta_section',
        'type'    => 'text',
    ));

    // CTA Button URL
    $wp_customize->add_setting('kilismile_cta_url', array(
        'default'           => '/donations',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_cta_url', array(
        'label'   => __('CTA Button URL', 'kilismile'),
        'section' => 'kilismile_cta_section',
        'type'    => 'url',
    ));

    // CTA Button Background Color
    $wp_customize->add_setting('kilismile_cta_bg_color', array(
        'default'           => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_cta_bg_color', array(
        'label'    => __('CTA Button Background', 'kilismile'),
        'section'  => 'kilismile_cta_section',
    )));

    // CTA Button Text Color
    $wp_customize->add_setting('kilismile_cta_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_cta_text_color', array(
        'label'    => __('CTA Button Text Color', 'kilismile'),
        'section'  => 'kilismile_cta_section',
    )));

    // Contact Info Section
    $wp_customize->add_section('kilismile_header_contact_section', array(
        'title'    => __('Header Contact Info', 'kilismile'),
        'priority' => 34,
    ));

    // Show Contact Info
    $wp_customize->add_setting('kilismile_show_header_contact', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_show_header_contact', array(
        'label'   => __('Show Contact Info in Header', 'kilismile'),
        'section' => 'kilismile_header_contact_section',
        'type'    => 'checkbox',
    ));

    // Header Phone
    $wp_customize->add_setting('kilismile_header_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_header_phone', array(
        'label'   => __('Phone Number', 'kilismile'),
        'section' => 'kilismile_header_contact_section',
        'type'    => 'text',
    ));

    // Header Email
    $wp_customize->add_setting('kilismile_header_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('kilismile_header_email', array(
        'label'   => __('Email Address', 'kilismile'),
        'section' => 'kilismile_header_contact_section',
        'type'    => 'email',
    ));

    // Social Media Section
    $wp_customize->add_section('kilismile_header_social_section', array(
        'title'    => __('Header Social Media', 'kilismile'),
        'priority' => 35,
    ));

    // Show Social Icons
    $wp_customize->add_setting('kilismile_show_header_social', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_show_header_social', array(
        'label'   => __('Show Social Icons in Header', 'kilismile'),
        'section' => 'kilismile_header_social_section',
        'type'    => 'checkbox',
    ));

    // Social Media Links
    $social_networks = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'youtube'   => 'YouTube',
        'whatsapp'  => 'WhatsApp',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("kilismile_header_{$network}_url", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control("kilismile_header_{$network}_url", array(
            'label'   => sprintf(__('%s URL', 'kilismile'), $label),
            'section' => 'kilismile_header_social_section',
            'type'    => 'url',
        ));
    }
}
add_action('customize_register', 'kilismile_header_customizer');

/**
 * Sanitize select fields
 */
function kilismile_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * Sanitize float values
 */
function kilismile_sanitize_float($input) {
    return floatval($input);
}
