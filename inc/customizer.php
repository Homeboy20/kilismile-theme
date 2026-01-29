<?php
/**
 * Customizer Additions
 *
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Additional Customizer Options
 */
function kilismile_customize_register_extended($wp_customize) {
    
    // Homepage Settings Section
    $wp_customize->add_section('kilismile_homepage', array(
        'title'    => __('Homepage Settings', 'kilismile'),
        'priority' => 25,
    ));
    
    // Hero section title
    $wp_customize->add_setting('kilismile_hero_title', array(
        'default'           => 'No health without oral health',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_hero_title', array(
        'label'   => __('Hero Section Title', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'text',
    ));
    
    // Hero section subtitle
    $wp_customize->add_setting('kilismile_hero_subtitle', array(
        'default'           => 'Promoting oral and general health education to children and elderly in remote areas of Tanzania',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_hero_subtitle', array(
        'label'   => __('Hero Section Subtitle', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'textarea',
    ));
    
    // Hero section description
    $wp_customize->add_setting('kilismile_hero_description', array(
        'default'           => 'Kilismile ORGANIZATION is dedicated to improving health outcomes in underserved communities through comprehensive oral health education, teacher training, and disease screening programs.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_hero_description', array(
        'label'   => __('Hero Section Description', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'textarea',
    ));
    
    // Hero background image
    $wp_customize->add_setting('kilismile_hero_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kilismile_hero_background', array(
        'label'   => __('Hero Background Image', 'kilismile'),
        'section' => 'kilismile_homepage',
    )));
    
    // Hero Overlay Gradient Start Color
    $wp_customize->add_setting('kilismile_hero_overlay_start', array(
        'default'           => '#2D5A41',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_hero_overlay_start', array(
        'label'       => __('Hero Overlay Gradient Start', 'kilismile'),
        'description' => __('Start color for the background gradient overlay', 'kilismile'),
        'section'     => 'kilismile_homepage',
    )));
    
    // Hero Overlay Gradient End Color
    $wp_customize->add_setting('kilismile_hero_overlay_end', array(
        'default'           => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_hero_overlay_end', array(
        'label'       => __('Hero Overlay Gradient End', 'kilismile'),
        'description' => __('End color for the background gradient overlay', 'kilismile'),
        'section'     => 'kilismile_homepage',
    )));
    
    // Hero Overlay Opacity
    $wp_customize->add_setting('kilismile_hero_overlay_opacity', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_hero_overlay_opacity', array(
        'label'       => __('Hero Overlay Opacity (%)', 'kilismile'),
        'description' => __('Opacity of the background overlay (0-100)', 'kilismile'),
        'section'     => 'kilismile_homepage',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 5,
        ),
    ));
    
    // Hero Buttons Settings
    $wp_customize->add_setting('kilismile_primary_btn_text', array(
        'default'           => 'Donate Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_primary_btn_text', array(
        'label'   => __('Primary Button Text', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_primary_btn_url', array(
        'default'           => '#donate',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_primary_btn_url', array(
        'label'   => __('Primary Button URL', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'url',
    ));
    
    $wp_customize->add_setting('kilismile_secondary_btn_text', array(
        'default'           => 'Our Story',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_secondary_btn_text', array(
        'label'   => __('Secondary Button Text', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_secondary_btn_url', array(
        'default'           => '/about',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_secondary_btn_url', array(
        'label'   => __('Secondary Button URL', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'url',
    ));
    
    // Hero Elements Toggle
    $wp_customize->add_setting('kilismile_show_hero_badge', array(
        'default'           => true,
        'sanitize_callback' => 'kilismile_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('kilismile_show_hero_badge', array(
        'label'   => __('Show Impact Badge', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_show_hero_stats', array(
        'default'           => true,
        'sanitize_callback' => 'kilismile_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('kilismile_show_hero_stats', array(
        'label'   => __('Show Stats Counter', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('kilismile_show_scroll_indicator', array(
        'default'           => true,
        'sanitize_callback' => 'kilismile_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('kilismile_show_scroll_indicator', array(
        'label'   => __('Show Scroll Indicator', 'kilismile'),
        'section' => 'kilismile_homepage',
        'type'    => 'checkbox',
    ));
    
    // Health Quotes Section
    $wp_customize->add_section('kilismile_health_quotes', array(
        'title'    => __('Health Quotes Section', 'kilismile'),
        'priority' => 26,
    ));
    
    // Health Quotes title
    $wp_customize->add_setting('kilismile_health_quotes_title', array(
        'default'           => 'Inspirational Health Quotes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_health_quotes_title', array(
        'label'   => __('Section Title', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    // Health Quotes subtitle
    $wp_customize->add_setting('kilismile_health_quotes_subtitle', array(
        'default'           => 'Wisdom that guides our mission and inspires healthier communities through education and awareness.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_health_quotes_subtitle', array(
        'label'   => __('Section Subtitle', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'textarea',
    ));
    
    // Quote 1 settings
    $wp_customize->add_setting('kilismile_quote1_text', array(
        'default'           => 'A smile is a curve that sets everything straight. Oral health is not just about healthy teeth; it\'s about maintaining dignity and quality of life.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_quote1_text', array(
        'label'   => __('Quote 1 Text', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('kilismile_quote1_author', array(
        'default'           => 'World Health Organization',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote1_author', array(
        'label'   => __('Quote 1 Author', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_quote1_source', array(
        'default'           => 'Global Health',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote1_source', array(
        'label'   => __('Quote 1 Source', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    // Quote 2 settings
    $wp_customize->add_setting('kilismile_quote2_text', array(
        'default'           => 'Education is the most powerful weapon which you can use to change the world. Health education empowers communities to take control of their wellbeing.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_quote2_text', array(
        'label'   => __('Quote 2 Text', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('kilismile_quote2_author', array(
        'default'           => 'Nelson Mandela',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote2_author', array(
        'label'   => __('Quote 2 Author', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_quote2_source', array(
        'default'           => 'Humanitarian Leader',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote2_source', array(
        'label'   => __('Quote 2 Source', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    // Quote 3 settings
    $wp_customize->add_setting('kilismile_quote3_text', array(
        'default'           => 'The greatest wealth is health. When you invest in community health education, you\'re investing in the future of humanity.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_quote3_text', array(
        'label'   => __('Quote 3 Text', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('kilismile_quote3_author', array(
        'default'           => 'Virgil',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote3_author', array(
        'label'   => __('Quote 3 Author', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_quote3_source', array(
        'default'           => 'Ancient Wisdom',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_quote3_source', array(
        'label'   => __('Quote 3 Source', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    // Featured Quote settings
    $wp_customize->add_setting('kilismile_featured_quote_text', array(
        'default'           => 'Health is not valued until sickness comes. Prevention through education is the foundation of public health.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_featured_quote_text', array(
        'label'   => __('Featured Quote Text', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('kilismile_featured_quote_author', array(
        'default'           => 'Dr. Thomas Fuller',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_featured_quote_author', array(
        'label'   => __('Featured Quote Author', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'text',
    ));
    
    // Show/Hide Health Quotes Section
    $wp_customize->add_setting('kilismile_show_health_quotes', array(
        'default'           => true,
        'sanitize_callback' => 'kilismile_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('kilismile_show_health_quotes', array(
        'label'   => __('Show Health Quotes Section', 'kilismile'),
        'section' => 'kilismile_health_quotes',
        'type'    => 'checkbox',
    ));
    
    // Impact Statistics Section
    $wp_customize->add_section('kilismile_stats', array(
        'title'    => __('Impact Statistics', 'kilismile'),
        'priority' => 27,
    ));
    
    // Children reached stat
    $wp_customize->add_setting('kilismile_stat_children', array(
        'default'           => '500',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_stat_children', array(
        'label'   => __('Children Reached', 'kilismile'),
        'section' => 'kilismile_stats',
        'type'    => 'number',
    ));
    
    // Elderly served stat
    $wp_customize->add_setting('kilismile_stat_elderly', array(
        'default'           => '200',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_stat_elderly', array(
        'label'   => __('Elderly Served', 'kilismile'),
        'section' => 'kilismile_stats',
        'type'    => 'number',
    ));
    
    // Teachers trained stat
    $wp_customize->add_setting('kilismile_stat_teachers', array(
        'default'           => '50',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_stat_teachers', array(
        'label'   => __('Teachers Trained', 'kilismile'),
        'section' => 'kilismile_stats',
        'type'    => 'number',
    ));
    
    // Remote areas stat
    $wp_customize->add_setting('kilismile_stat_areas', array(
        'default'           => '10',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_stat_areas', array(
        'label'   => __('Remote Areas Served', 'kilismile'),
        'section' => 'kilismile_stats',
        'type'    => 'number',
    ));
    
    // Colors Section
    $wp_customize->add_section('kilismile_colors', array(
        'title'    => __('Theme Colors', 'kilismile'),
        'priority' => 27,
    ));
    
    // Primary color
    $wp_customize->add_setting('kilismile_primary_color', array(
        'default'           => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_primary_color', array(
        'label'   => __('Primary Color', 'kilismile'),
        'section' => 'kilismile_colors',
    )));
    
    // Secondary color
    $wp_customize->add_setting('kilismile_secondary_color', array(
        'default'           => '#2d5a41',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_secondary_color', array(
        'label'   => __('Secondary Color', 'kilismile'),
        'section' => 'kilismile_colors',
    )));
    
    // Accent color
    $wp_customize->add_setting('kilismile_accent_color', array(
        'default'           => '#81C784',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_accent_color', array(
        'label'   => __('Accent Color', 'kilismile'),
        'section' => 'kilismile_colors',
    )));
    
    // Additional Settings Section
    $wp_customize->add_section('kilismile_additional', array(
        'title'    => __('Additional Settings', 'kilismile'),
        'priority' => 50,
    ));
    
    // Footer Settings Section
    $wp_customize->add_section('kilismile_footer', array(
        'title'    => __('Footer Settings', 'kilismile'),
        'priority' => 45,
    ));
    
    // Footer logo setting
    $wp_customize->add_setting('kilismile_footer_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'kilismile_footer_logo', array(
        'label'       => __('Footer Logo', 'kilismile'),
        'description' => __('Upload a logo to display in the footer. If not set, the main site logo will be used.', 'kilismile'),
        'section'     => 'kilismile_footer',
        'mime_type'   => 'image',
    )));
    
    // Footer logo size
    $wp_customize->add_setting('kilismile_footer_logo_size', array(
        'default'           => 60,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('kilismile_footer_logo_size', array(
        'label'       => __('Footer Logo Size (px)', 'kilismile'),
        'description' => __('Set the width and height of the footer logo in pixels', 'kilismile'),
        'section'     => 'kilismile_footer',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 1000,
            'step' => 1,
        ),
    ));
    
    // Show/hide footer logo
    $wp_customize->add_setting('kilismile_show_footer_logo', array(
        'default'           => true,
        'sanitize_callback' => 'kilismile_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('kilismile_show_footer_logo', array(
        'label'   => __('Show Footer Logo', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'checkbox',
    ));
    
    // Footer description
    $wp_customize->add_setting('kilismile_footer_description', array(
        'default'           => 'Promoting oral and general health education services to children and elderly populations in Tanzania. Building healthier communities through education and care.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_footer_description', array(
        'label'   => __('Footer Description', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'textarea',
    ));
    
    // Footer organization registration number
    $wp_customize->add_setting('kilismile_registration', array(
        'default'           => '07NGO/R/6067',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_registration', array(
        'label'   => __('Registration Number', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'text',
    ));
    
    // Footer contact information
    $wp_customize->add_setting('kilismile_address', array(
        'default'           => 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_address', array(
        'label'   => __('Organization Address', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('kilismile_phone', array(
        'default'           => '+255763495575/+255735495575',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_phone', array(
        'label'   => __('Phone Number', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_email', array(
        'default'           => 'kilismile21@gmail.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('kilismile_email', array(
        'label'   => __('Email Address', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'email',
    ));
    
    // Social media links
    $wp_customize->add_setting('kilismile_instagram', array(
        'default'           => 'https://instagram.com/kili_smile',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_instagram', array(
        'label'   => __('Instagram URL', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'url',
    ));
    
    $wp_customize->add_setting('kilismile_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_facebook', array(
        'label'   => __('Facebook URL', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'url',
    ));
    
    $wp_customize->add_setting('kilismile_twitter', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_twitter', array(
        'label'   => __('Twitter URL', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'url',
    ));
    
    // Footer copyright text
    $wp_customize->add_setting('kilismile_footer_copyright', array(
        'default'           => '"No health without oral health"',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_footer_copyright', array(
        'label'   => __('Footer Tagline', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'text',
    ));
    
    // Donation URL
    $wp_customize->add_setting('kilismile_donation_url', array(
        'default'           => '#donate',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_donation_url', array(
        'label'   => __('Donation URL', 'kilismile'),
        'section' => 'kilismile_footer',
        'type'    => 'url',
    ));
    
    // Enable breadcrumbs
    $wp_customize->add_setting('kilismile_enable_breadcrumbs', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_enable_breadcrumbs', array(
        'label'   => __('Enable Breadcrumbs', 'kilismile'),
        'section' => 'kilismile_additional',
        'type'    => 'checkbox',
    ));
    
    // Enable back to top button
    $wp_customize->add_setting('kilismile_enable_back_to_top', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_enable_back_to_top', array(
        'label'   => __('Enable Back to Top Button', 'kilismile'),
        'section' => 'kilismile_additional',
        'type'    => 'checkbox',
    ));
    
    // Emergency contact bar
    $wp_customize->add_setting('kilismile_show_emergency_bar', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('kilismile_show_emergency_bar', array(
        'label'   => __('Show Emergency Contact Bar', 'kilismile'),
        'section' => 'kilismile_additional',
        'type'    => 'checkbox',
    ));
    
    // Emergency contact number
    $wp_customize->add_setting('kilismile_emergency_number', array(
        'default'           => '112',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_emergency_number', array(
        'label'   => __('Emergency Contact Number', 'kilismile'),
        'section' => 'kilismile_additional',
        'type'    => 'text',
    ));
    
    // Google Analytics
    $wp_customize->add_setting('kilismile_google_analytics', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_google_analytics', array(
        'label'       => __('Google Analytics Tracking ID', 'kilismile'),
        'description' => __('Enter your Google Analytics tracking ID (e.g., G-XXXXXXXXXX)', 'kilismile'),
        'section'     => 'kilismile_additional',
        'type'        => 'text',
    ));
    
    // Facebook Pixel
    $wp_customize->add_setting('kilismile_facebook_pixel', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_facebook_pixel', array(
        'label'       => __('Facebook Pixel ID', 'kilismile'),
        'description' => __('Enter your Facebook Pixel ID for conversion tracking', 'kilismile'),
        'section'     => 'kilismile_additional',
        'type'        => 'text',
    ));
    
}
add_action('customize_register', 'kilismile_customize_register_extended');

/**
 * Output Custom CSS based on Customizer settings
 */
function kilismile_customizer_css() {
    $primary_color = get_theme_mod('kilismile_primary_color', '#4CAF50');
    $secondary_color = get_theme_mod('kilismile_secondary_color', '#2d5a41');
    $accent_color = get_theme_mod('kilismile_accent_color', '#81C784');
    $hero_background = get_theme_mod('kilismile_hero_background', '');
    $footer_logo_size = get_theme_mod('kilismile_footer_logo_size', 60);
    
    // Hero overlay customization
    $overlay_start = get_theme_mod('kilismile_hero_overlay_start', '#2D5A41');
    $overlay_end = get_theme_mod('kilismile_hero_overlay_end', '#4CAF50');
    $overlay_opacity = get_theme_mod('kilismile_hero_overlay_opacity', 80);
    $opacity_decimal = $overlay_opacity / 100;
    
    ?>
    <style type="text/css">
        :root {
            --primary-green: <?php echo esc_attr($primary_color); ?>;
            --dark-green: <?php echo esc_attr($secondary_color); ?>;
            --light-green: <?php echo esc_attr($accent_color); ?>;
            --accent-green: <?php echo esc_attr($accent_color); ?>;
            --hero-overlay-start: <?php echo esc_attr($overlay_start); ?>;
            --hero-overlay-end: <?php echo esc_attr($overlay_end); ?>;
            --hero-overlay-opacity: <?php echo esc_attr($opacity_decimal); ?>;
            --footer-logo-size: <?php echo esc_attr($footer_logo_size); ?>px;
        }
        
        /* Footer Logo Styling */
        .footer-logo-image {
            width: var(--footer-logo-size) !important;
            height: var(--footer-logo-size) !important;
            object-fit: contain;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .footer-logo {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .footer-title {
            margin-top: 10px;
            margin-bottom: 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-green);
        }
        
        <?php if ($hero_background) : ?>
        .hero-section-redesign {
            background-image: linear-gradient(135deg, 
                <?php echo esc_attr($overlay_start); ?><?php echo esc_attr(sprintf('%02x', $overlay_opacity * 255 / 100)); ?> 0%, 
                <?php echo esc_attr($overlay_end); ?><?php echo esc_attr(sprintf('%02x', $overlay_opacity * 255 / 100)); ?> 100%), 
                url('<?php echo esc_url($hero_background); ?>') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
        }
        <?php else : ?>
        .hero-section-redesign {
            background-image: linear-gradient(135deg, 
                <?php echo esc_attr($overlay_start); ?> 0%, 
                <?php echo esc_attr($overlay_end); ?> 100%), 
                url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/hero-background.svg') !important;
        }
        <?php endif; ?>
        
        /* Custom color overrides */
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .donate-btn {
            background: var(--primary-green);
        }
        
        .donate-btn:hover {
            background: var(--dark-green);
        }
        
        .mission-icon {
            background: var(--primary-green);
        }
        
        .mission-card:hover {
            border-top-color: var(--primary-green);
        }
        
        .stat-number {
            color: var(--primary-green);
        }
        
        a {
            color: var(--primary-green);
        }
        
        a:hover {
            color: var(--dark-green);
        }
        
        .site-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .back-to-top {
            background: var(--primary-green);
        }
        
        .back-to-top:hover {
            background: var(--dark-green);
        }
    </style>
    <?php
}
add_action('wp_head', 'kilismile_customizer_css');

/**
 * Add Google Analytics
 */
function kilismile_add_google_analytics() {
    $tracking_id = get_theme_mod('kilismile_google_analytics', '');
    
    if ($tracking_id && !is_admin()) {
        ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($tracking_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            
            // Check for cookie consent
            if (localStorage.getItem('cookieConsent') === 'accepted') {
                gtag('config', '<?php echo esc_js($tracking_id); ?>');
            } else {
                gtag('consent', 'default', {
                    'analytics_storage': 'denied'
                });
                gtag('config', '<?php echo esc_js($tracking_id); ?>');
            }
        </script>
        <?php
    }
}
add_action('wp_head', 'kilismile_add_google_analytics');

/**
 * Add Facebook Pixel
 */
function kilismile_add_facebook_pixel() {
    $pixel_id = get_theme_mod('kilismile_facebook_pixel', '');
    
    if ($pixel_id && !is_admin()) {
        ?>
        <!-- Facebook Pixel -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            
            // Check for cookie consent
            if (localStorage.getItem('cookieConsent') === 'accepted') {
                fbq('init', '<?php echo esc_js($pixel_id); ?>');
                fbq('track', 'PageView');
            } else {
                fbq('consent', 'revoke');
                fbq('init', '<?php echo esc_js($pixel_id); ?>');
            }
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" 
                 src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"/>
        </noscript>
        <?php
    }
}
add_action('wp_head', 'kilismile_add_facebook_pixel');

/**
 * Customizer Live Preview JavaScript
 */
function kilismile_customize_preview_js() {
    wp_enqueue_script(
        'kilismile-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
    // Provide the template directory URL to the preview script
    wp_localize_script('kilismile-customizer-preview', 'kilismile', array(
        'templateUrl' => get_template_directory_uri()
    ));
}
add_action('customize_preview_init', 'kilismile_customize_preview_js');

/**
 * Customizer Controls JavaScript
 */
function kilismile_customize_controls_js() {
    wp_enqueue_script(
        'kilismile-customizer-controls',
        get_template_directory_uri() . '/assets/js/customizer-controls.js',
        array('customize-controls'),
        '1.0.0',
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'kilismile_customize_controls_js');

/**
 * Sanitize checkbox values
 */
function kilismile_sanitize_checkbox($checked) {
    return (isset($checked) && true == $checked) ? true : false;
}

?>


