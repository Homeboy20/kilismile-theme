<?php
/**
 * Contact Page Customizer Options
 *
 * @package KiliSmile
 * @version 1.0.0
 */

/**
 * Add contact page options to customizer
 */
function kilismile_contact_customizer($wp_customize) {
    
    // Add Contact Section
    $wp_customize->add_section('kilismile_contact_section', array(
        'title'    => __('Contact Page Settings', 'kilismile'),
        'priority' => 130,
        'description' => __('Customize the contact page information and social media links.', 'kilismile'),
    ));

    // Primary Contact Details
    $wp_customize->add_setting('contact_details_email', array(
        'default'           => 'kilismile21@gmail.com',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('contact_details_email', array(
        'label'    => __('Contact Email (Contact Page)', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'email',
        'priority' => 5,
    ));

    $wp_customize->add_setting('contact_details_phone', array(
        'default'           => '+255763495575/+255735495575',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('contact_details_phone', array(
        'label'    => __('Contact Phone (Contact Page)', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 6,
    ));

    $wp_customize->add_setting('contact_details_address', array(
        'default'           => 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('contact_details_address', array(
        'label'    => __('Contact Address (Contact Page)', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'textarea',
        'priority' => 7,
    ));

    // Office Contact Settings
    $wp_customize->add_setting('contact_office_title', array(
        'default'           => 'Visit Office',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_office_title', array(
        'label'    => __('Office Card Title', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 10,
    ));

    $wp_customize->add_setting('contact_office_content', array(
        'default'           => 'Moshi, Kilimanjaro<br>Mon-Fri, 8AM-5PM',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_office_content', array(
        'label'       => __('Office Card Content', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'textarea',
        'description' => __('Use &lt;br&gt; for line breaks', 'kilismile'),
        'priority'    => 11,
    ));

    $wp_customize->add_setting('contact_office_link', array(
        'default'           => '#map-section',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_office_link', array(
        'label'    => __('Office Link URL', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'url',
        'priority' => 12,
    ));

    $wp_customize->add_setting('contact_office_link_text', array(
        'default'           => 'Location',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_office_link_text', array(
        'label'    => __('Office Link Text', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 13,
    ));

    // Phone Contact Settings
    $wp_customize->add_setting('contact_phone_title', array(
        'default'           => 'Call Us',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_phone_title', array(
        'label'    => __('Phone Card Title', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 20,
    ));

    $wp_customize->add_setting('contact_phone_content', array(
        'default'           => '+255763495575/+255735495575<br>24/7 for emergencies',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_phone_content', array(
        'label'       => __('Phone Card Content', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'textarea',
        'description' => __('Use &lt;br&gt; for line breaks', 'kilismile'),
        'priority'    => 21,
    ));

    $wp_customize->add_setting('contact_phone_link', array(
        'default'           => 'tel:+255763495575',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_phone_link', array(
        'label'       => __('Phone Link URL', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'text',
        'description' => __('Use tel: prefix for phone numbers', 'kilismile'),
        'priority'    => 22,
    ));

    $wp_customize->add_setting('contact_phone_link_text', array(
        'default'           => 'Call Now',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_phone_link_text', array(
        'label'    => __('Phone Link Text', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 23,
    ));

    // Email Contact Settings
    $wp_customize->add_setting('contact_email_title', array(
        'default'           => 'Email Us',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_email_title', array(
        'label'    => __('Email Card Title', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 30,
    ));

    $wp_customize->add_setting('contact_email_content', array(
        'default'           => 'kilismile21@gmail.com<br>Response within 24hrs',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_email_content', array(
        'label'       => __('Email Card Content', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'textarea',
        'description' => __('Use &lt;br&gt; for line breaks', 'kilismile'),
        'priority'    => 31,
    ));

    $wp_customize->add_setting('contact_email_link', array(
        'default'           => 'mailto:kilismile21@gmail.com',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_email_link', array(
        'label'       => __('Email Link URL', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'text',
        'description' => __('Use mailto: prefix for email addresses', 'kilismile'),
        'priority'    => 32,
    ));

    $wp_customize->add_setting('contact_email_link_text', array(
        'default'           => 'Send Email',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_email_link_text', array(
        'label'    => __('Email Link Text', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 33,
    ));

    // Social Media Settings
    $wp_customize->add_setting('contact_social_title', array(
        'default'           => 'Follow Us',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_social_title', array(
        'label'    => __('Social Media Card Title', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 40,
    ));

    $wp_customize->add_setting('contact_social_content', array(
        'default'           => 'Latest news & updates<br>on social media',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_social_content', array(
        'label'       => __('Social Media Card Content', 'kilismile'),
        'section'     => 'kilismile_contact_section',
        'type'        => 'textarea',
        'description' => __('Use &lt;br&gt; for line breaks', 'kilismile'),
        'priority'    => 41,
    ));

    // Social Media Links
    $wp_customize->add_setting('contact_facebook_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_facebook_url', array(
        'label'    => __('Facebook URL', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'url',
        'priority' => 42,
    ));

    $wp_customize->add_setting('contact_twitter_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_twitter_url', array(
        'label'    => __('Twitter URL', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'url',
        'priority' => 43,
    ));

    $wp_customize->add_setting('contact_instagram_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_instagram_url', array(
        'label'    => __('Instagram URL', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'url',
        'priority' => 44,
    ));

    $wp_customize->add_setting('contact_linkedin_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_linkedin_url', array(
        'label'    => __('LinkedIn URL', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'url',
        'priority' => 45,
    ));

    // Hero Section Settings
    $wp_customize->add_setting('contact_hero_title', array(
        'default'           => 'Get In Touch',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_hero_title', array(
        'label'    => __('Hero Section Title', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 50,
    ));

    $wp_customize->add_setting('contact_hero_description', array(
        'default'           => 'Ready to make a difference? We\'d love to hear from you. Whether you want to volunteer, partner with us, or learn more about our programs, we\'re here to help.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_hero_description', array(
        'label'    => __('Hero Section Description', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'textarea',
        'priority' => 51,
    ));

    // Quick Contact Section Settings
    $wp_customize->add_setting('quick_contact_phone', array(
        'default'           => '+255763495575/+255735495575',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('quick_contact_phone', array(
        'label'    => __('Quick Contact Phone', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'text',
        'priority' => 60,
    ));

    $wp_customize->add_setting('quick_contact_email', array(
        'default'           => 'kilismile21@gmail.com',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('quick_contact_email', array(
        'label'    => __('Quick Contact Email', 'kilismile'),
        'section'  => 'kilismile_contact_section',
        'type'     => 'email',
        'priority' => 61,
    ));
}

add_action('customize_register', 'kilismile_contact_customizer');


