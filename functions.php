<?php
/**
 * Kili Smile Organization Theme Functions
 *
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Core theme includes for donation functionality (non-payment specific)
require_once get_template_directory() . '/includes/donation-database.php';
require_once get_template_directory() . '/includes/donation-email-handler.php';
require_once get_template_directory() . '/includes/settings-helpers.php';
require_once get_template_directory() . '/includes/settings-compatibility.php';

/**
 * Plugin Integration Hooks for External Payment Processors
 * These hooks allow payment plugins to integrate with the theme
 */

// Hook for external payment plugins to register their donation forms
function kilismile_get_donation_form($args = array()) {
    // Allow plugins to provide their own donation form
    $form = apply_filters('kilismile_donation_form', '', $args);
    
    if (empty($form)) {
        // Fallback: Use theme donation form component (non-payment processing)
        $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
        if (file_exists($component_path)) {
            ob_start();
            include $component_path;
            return ob_get_clean();
        }
        
        return '<div class="kilismile-donation-notice">
            <p>To enable donation processing, please install and activate a compatible payment plugin.</p>
        </div>';
    }
    
    return $form;
}

// Fallback donation processing when no payment plugin is active
function kilismile_handle_donation_fallback() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['donation_nonce'] ?? '', 'kilismile_donation_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security verification failed. Please refresh the page and try again.', 'kilismile')
        ));
        return;
    }
    
    // Log the attempt for debugging
    if (function_exists('KiliSmile_Donation_Debug::log_transaction')) {
        KiliSmile_Donation_Debug::log_transaction('fallback_payment_attempted', array(
            'amount' => sanitize_text_field($_POST['amount'] ?? ''),
            'currency' => sanitize_text_field($_POST['currency'] ?? ''),
            'payment_gateway' => sanitize_text_field($_POST['payment_gateway'] ?? '')
        ));
    }
    
    // Save donation data to database without processing payment
    $donation_data = array(
        'donor_name' => sanitize_text_field($_POST['donor_name'] ?? ''),
        'donor_email' => sanitize_email($_POST['donor_email'] ?? ''),
        'donor_phone' => sanitize_text_field($_POST['donor_phone'] ?? ''),
        'amount' => floatval($_POST['amount'] ?? 0),
        'currency' => sanitize_text_field($_POST['currency'] ?? 'TZS'),
        'payment_method' => sanitize_text_field($_POST['payment_gateway'] ?? 'pending'),
        'status' => 'pending',
        'is_recurring' => !empty($_POST['recurring']),
        'anonymous' => !empty($_POST['anonymous']),
        'created_at' => current_time('mysql')
    );
    
    // Store in database if available
    if (class_exists('KiliSmile_Donation_Database')) {
        $db = new KiliSmile_Donation_Database();
        $donation_id = $db->save_donation($donation_data);
        
        wp_send_json_success(array(
            'message' => __('Donation information saved. A payment plugin is required to process payments. Please contact us to complete your donation.', 'kilismile'),
            'donation_id' => $donation_id,
            'requires_plugin' => true
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Payment processing is not available. Please install a compatible payment plugin.', 'kilismile')
        ));
    }
}

// Load enhanced admin system
if (is_admin()) {
    require_once get_template_directory() . '/admin/field-renderers.php';
    require_once get_template_directory() . '/admin/enhanced-theme-settings.php';
    require_once get_template_directory() . '/admin/settings-migration.php';
    require_once get_template_directory() . '/admin/donation-admin.php';
}



/**
 * Theme Setup
 */
function kilismile_setup() {
    // Add theme support for various WordPress features
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style'
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('align-wide');
    
    // Add custom logo support
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Add custom header support
    add_theme_support('custom-header', array(
        'default-image'          => '',
        'random-default'         => false,
        'width'                  => 1920,
        'height'                 => 600,
        'flex-height'            => true,
        'flex-width'             => true,
        'default-text-color'     => '333333',
        'header-text'            => true,
        'uploads'                => true,
    ));
    
    // Navigation menus moved to init (contain translatable strings)
    
    // Translation loading moved to init (WP 6.7+ recommends init or later)
    
    // Set content width
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'kilismile_setup');

// Load translations on init to comply with timing requirements
function kilismile_load_translations_late() {
    load_theme_textdomain('kilismile', get_template_directory() . '/languages');
    
    // Register navigation menus after textdomain is loaded
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'kilismile'),
        'footer'  => __('Footer Menu', 'kilismile'),
        'social'  => __('Social Links', 'kilismile'),
    ));
}
add_action('init', 'kilismile_load_translations_late');

// Force HTTPS for theme-generated asset URLs when SSL is active
function kilismile_force_https_url($url) {
    if (is_ssl() && is_string($url)) {
        return set_url_scheme($url, 'https');
    }
    return $url;
}

add_filter('site_icon_url', 'kilismile_force_https_url');
add_filter('wp_get_attachment_url', 'kilismile_force_https_url');

function kilismile_force_https_in_content($content) {
    if (!is_ssl() || !is_string($content)) {
        return $content;
    }

    $http_base = 'http://kilismile.local/wp-content/uploads';
    $https_base = 'https://kilismile.local/wp-content/uploads';

    if (strpos($content, $http_base) === false) {
        return $content;
    }

    return str_replace($http_base, $https_base, $content);
}

add_filter('the_content', 'kilismile_force_https_in_content');
add_filter('widget_text', 'kilismile_force_https_in_content');
add_filter('widget_text_content', 'kilismile_force_https_in_content');

/**
 * Enqueue Scripts and Styles
 */
function kilismile_scripts() {
    // Enqueue styles
    wp_enqueue_style('kilismile-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('kilismile-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('kilismile-no-gradients', get_template_directory_uri() . '/assets/css/no-gradients.css', array('kilismile-style'), '1.0.0');
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://use.fontawesome.com/releases/v5.15.4/css/all.css', array(), '5.15.4');
    
    // Newsletter CSS - load only on newsletter page
    if (is_page('newsletter') || is_post_type_archive('newsletter') || is_singular('newsletter')) {
        wp_enqueue_style('kilismile-newsletter', get_template_directory_uri() . '/assets/css/newsletter.css', array(), '1.0.0');
        wp_enqueue_script('kilismile-newsletter-js', get_template_directory_uri() . '/assets/js/newsletter.js', array('jquery'), '1.0.0', true);
    }
    
    // Modern donation system CSS and JS - load on donation pages
    if (is_page('donate') || is_page('donation') || is_page('donations')) {
        // Donation page layout styles
        wp_enqueue_style(
            'kilismile-donation-page',
            get_template_directory_uri() . '/assets/css/donation-page.css',
            array('kilismile-style'),
            '1.0.0'
        );

        // Multi-step donation form styles
        wp_enqueue_style(
            'kilismile-donation-form-multistep',
            get_template_directory_uri() . '/assets/css/donation-form-multistep.css',
            array('kilismile-style'),
            '1.0.0'
        );

        // Enqueue WordPress utilities for better compatibility
        wp_enqueue_script('wp-util');
        
        // Temporarily disabled to prevent conflicts with optimized template
        // wp_enqueue_script('kilismile-donation-modern', get_template_directory_uri() . '/assets/js/donation-modern.js', array('jquery', 'wp-util'), '2.0.0', true);
        
        // Localize script with AJAX and settings data
        wp_localize_script('kilismile-donation-modern', 'kilismileDonation', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donation_nonce'),
            'currency_rates' => array(
                'USD_to_TZS' => 2350,
                'TZS_to_USD' => 0.000426
            ),
            'strings' => array(
                'required_field' => __('This field is required.', 'kilismile'),
                'invalid_email' => __('Please enter a valid email address.', 'kilismile'),
                'invalid_amount' => __('Please enter a valid donation amount.', 'kilismile'),
                'processing' => __('Processing...', 'kilismile'),
                'success' => __('Thank you for your donation!', 'kilismile'),
                'error' => __('An error occurred. Please try again.', 'kilismile')
            )
        ));
    }
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('kilismile-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    // Add Ajax URL for newsletter form
    wp_localize_script('kilismile-main', 'kilismile_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kilismile_nonce')
    ));
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'kilismile_scripts');

/**
 * Customize Logo Settings
 */
function kilismile_customize_logo_settings($wp_customize) {
    // Logo Size Setting
    $wp_customize->add_setting('logo_size', array(
        'default' => 45,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('logo_size', array(
        'label' => __('Logo Size (px)', 'kilismile'),
        'description' => __('Adjust the size of your logo in pixels.', 'kilismile'),
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 20,
            'max' => 100,
            'step' => 1,
        ),
        'priority' => 65,
    ));

    // Logo Border Setting
    $wp_customize->add_setting('logo_border', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('logo_border', array(
        'label' => __('Add Logo Border', 'kilismile'),
        'description' => __('Add a circular border around the logo.', 'kilismile'),
        'section' => 'title_tagline',
        'type' => 'checkbox',
        'priority' => 66,
    ));

    // Logo Border Radius Setting
    $wp_customize->add_setting('logo_border_radius', array(
        'default' => 50,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('logo_border_radius', array(
        'label' => __('Logo Border Radius (%)', 'kilismile'),
        'description' => __('Adjust the roundness of the logo. 50% = circle, 0% = square.', 'kilismile'),
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 0,
            'max' => 50,
            'step' => 1,
        ),
        'priority' => 67,
        'active_callback' => function() {
            return get_theme_mod('logo_border', false);
        },
    ));

    // Logo Border Color Setting
    $wp_customize->add_setting('logo_border_color', array(
        'default' => '#4CAF50',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'logo_border_color', array(
        'label' => __('Logo Border Color', 'kilismile'),
        'description' => __('Choose the color for the logo border.', 'kilismile'),
        'section' => 'title_tagline',
        'priority' => 68,
        'active_callback' => function() {
            return get_theme_mod('logo_border', false);
        },
    )));
}
add_action('customize_register', 'kilismile_customize_logo_settings');

/**
 * Generate Custom CSS for Logo Settings
 */
function kilismile_logo_custom_css() {
    // Get logo size from both possible sources
    $logo_size = get_theme_mod('logo_size', 45);
    $test_logo_size = get_theme_mod('kilismile_test_logo_size', 50);
    $header_logo_size = get_theme_mod('kilismile_logo_size', 50);
    
    // Use the highest priority logo size setting
    if ($header_logo_size) {
        $final_logo_size = $header_logo_size;
    } elseif ($test_logo_size) {
        $final_logo_size = $test_logo_size;
    } else {
        $final_logo_size = $logo_size;
    }
    
    $logo_border = get_theme_mod('logo_border', false);
    $logo_border_radius = get_theme_mod('logo_border_radius', 50);
    $logo_border_color = get_theme_mod('logo_border_color', '#4CAF50');

    $css = "
    :root {
        --logo-size: {$final_logo_size}px;
    }
    
    .site-logo img,
    .custom-logo-link img,
    #siteLogo,
    .custom-logo {
        width: {$final_logo_size}px !important;
        height: {$final_logo_size}px !important;
        transition: all 0.3s ease;
        object-fit: contain;
    }";

    if ($logo_border) {
        $css .= "
        .site-logo img,
        .custom-logo-link img,
        #siteLogo,
        .custom-logo {
            border: 2px solid {$logo_border_color} !important;
            border-radius: {$logo_border_radius}% !important;
        }";
    } else {
        $css .= "
        .site-logo img,
        .custom-logo-link img,
        #siteLogo,
        .custom-logo {
            border: none !important;
            border-radius: 0 !important;
        }";
    }

    // Responsive adjustments
    $css .= "
    @media (max-width: 768px) {
        .site-logo img,
        .custom-logo-link img,
        #siteLogo,
        .custom-logo {
            width: " . max(30, $final_logo_size * 0.8) . "px !important;
            height: " . max(30, $final_logo_size * 0.8) . "px !important;
        }
    }
    
    @media (max-width: 480px) {
        .site-logo img,
        .custom-logo-link img,
        #siteLogo,
        .custom-logo {
            width: " . max(25, $final_logo_size * 0.7) . "px !important;
            height: " . max(25, $final_logo_size * 0.7) . "px !important;
        }
    }";

    return $css;
}

/**
 * Add Custom CSS to Head
 */
function kilismile_add_logo_custom_css() {
    echo '<style type="text/css" id="kilismile-logo-styles">' . kilismile_logo_custom_css() . '</style>';
}
add_action('wp_head', 'kilismile_add_logo_custom_css', 999);

/**
 * Add favicon to prevent 404 errors
 */
function kilismile_add_favicon() {
    $favicon_path = get_template_directory() . '/assets/images/favicon.ico';
    $favicon_url = get_template_directory_uri() . '/assets/images/favicon.ico';
    
    // Check if favicon exists, if not use a default or site icon
    if (file_exists($favicon_path)) {
        echo '<link rel="shortcut icon" href="' . esc_url($favicon_url) . '" type="image/x-icon" />';
    } elseif (has_site_icon()) {
        // WordPress site icon is already handled by WP
        return;
    } else {
        // Use a data URI for a simple default favicon to prevent 404
        echo '<link rel="shortcut icon" href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAP///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" type="image/x-icon" />';
    }
}
add_action('wp_head', 'kilismile_add_favicon', 5);

/**
 * Add High Priority Logo CSS to override any conflicting styles
 */
function kilismile_add_priority_logo_css() {
    $logo_size = get_theme_mod('kilismile_logo_size', get_theme_mod('logo_size', 50));
    ?>
    <style type="text/css" id="kilismile-priority-logo">
    /* High Priority Logo Sizing */
    .site-logo img,
    .custom-logo-link img,
    #siteLogo,
    .custom-logo {
        width: <?php echo $logo_size; ?>px !important;
        height: <?php echo $logo_size; ?>px !important;
        max-width: <?php echo $logo_size; ?>px !important;
        max-height: <?php echo $logo_size; ?>px !important;
        object-fit: contain !important;
    }
    
    /* Ensure CSS variable is set */
    :root {
        --logo-size: <?php echo $logo_size; ?>px !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'kilismile_add_priority_logo_css', 1000);

/**
 * Enqueue Customizer Scripts
 */
function kilismile_customizer_preview_scripts() {
    wp_enqueue_script('kilismile-customizer-preview', 
        get_template_directory_uri() . '/assets/js/customizer-preview.js', 
        array('customize-preview', 'jquery'), 
        wp_get_theme()->get('Version'), 
        true
    );
}
add_action('customize_preview_init', 'kilismile_customizer_preview_scripts');

/**
 * Create Customizer Preview JavaScript inline
 */
function kilismile_customizer_live_preview() {
    // Only load in customizer or if we know wp.customize is available
    if (!is_customize_preview()) {
        return;
    }
    ?>
    <script type="text/javascript">
    (function($) {
        // Check if wp.customize is available
        if (typeof wp === 'undefined' || !wp.customize) {
            return;
        }
        
        // Logo Size
        wp.customize('logo_size', function(value) {
            value.bind(function(to) {
                $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css({
                    'width': to + 'px',
                    'height': to + 'px'
                });
            });
        });
        
        // Logo Border
        wp.customize('logo_border', function(value) {
            value.bind(function(to) {
                var borderColor = wp.customize.value('logo_border_color')();
                var borderRadius = wp.customize.value('logo_border_radius')();
                if (to) {
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css({
                        'border': '2px solid ' + borderColor,
                        'border-radius': borderRadius + '%'
                    });
                } else {
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css({
                        'border': 'none',
                        'border-radius': '0'
                    });
                }
            });
        });
        
        // Border Radius
        wp.customize('logo_border_radius', function(value) {
            value.bind(function(to) {
                var hasBorder = wp.customize.value('logo_border')();
                if (hasBorder) {
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css('border-radius', to + '%');
                }
            });
        });
        
        // Border Color
        wp.customize('logo_border_color', function(value) {
            value.bind(function(to) {
                var hasBorder = wp.customize.value('logo_border')();
                if (hasBorder) {
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css('border-color', to);
                }
            });
        });
    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'kilismile_customizer_live_preview');

/**
 * Register Widget Areas
 */
function kilismile_widgets_init() {
    register_sidebar(array(
        'name'          => __('Primary Sidebar', 'kilismile'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'kilismile'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'kilismile'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'kilismile'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'kilismile'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here to appear in your footer.', 'kilismile'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'kilismile'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here to appear in your footer.', 'kilismile'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 4', 'kilismile'),
        'id'            => 'footer-4',
        'description'   => __('Add widgets here to appear in your footer.', 'kilismile'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Homepage Hero Section', 'kilismile'),
        'id'            => 'hero-section',
        'description'   => __('Add widgets here to appear in the hero section.', 'kilismile'),
        'before_widget' => '<div id="%1$s" class="hero-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="hero-widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'kilismile_widgets_init');

/**
 * Custom Post Types
 */
function kilismile_custom_post_types() {
    // Programs Post Type
    register_post_type('programs', array(
        'labels' => array(
            'name'               => __('Programs', 'kilismile'),
            'singular_name'      => __('Program', 'kilismile'),
            'add_new'            => __('Add New Program', 'kilismile'),
            'add_new_item'       => __('Add New Program', 'kilismile'),
            'edit_item'          => __('Edit Program', 'kilismile'),
            'new_item'           => __('New Program', 'kilismile'),
            'view_item'          => __('View Program', 'kilismile'),
            'search_items'       => __('Search Programs', 'kilismile'),
            'not_found'          => __('No programs found', 'kilismile'),
            'not_found_in_trash' => __('No programs found in trash', 'kilismile'),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-heart',
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'       => array('slug' => 'programs'),
        'show_in_rest'  => true,
    ));
    
    // Team Members Post Type
    register_post_type('team', array(
        'labels' => array(
            'name'               => __('Team Members', 'kilismile'),
            'singular_name'      => __('Team Member', 'kilismile'),
            'add_new'            => __('Add New Team Member', 'kilismile'),
            'add_new_item'       => __('Add New Team Member', 'kilismile'),
            'edit_item'          => __('Edit Team Member', 'kilismile'),
            'new_item'           => __('New Team Member', 'kilismile'),
            'view_item'          => __('View Team Member', 'kilismile'),
            'search_items'       => __('Search Team Members', 'kilismile'),
            'not_found'          => __('No team members found', 'kilismile'),
            'not_found_in_trash' => __('No team members found in trash', 'kilismile'),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite'       => array('slug' => 'team'),
        'show_in_rest'  => true,
    ));
    
    // Testimonials Post Type
    register_post_type('testimonials', array(
        'labels' => array(
            'name'               => __('Testimonials', 'kilismile'),
            'singular_name'      => __('Testimonial', 'kilismile'),
            'add_new'            => __('Add New Testimonial', 'kilismile'),
            'add_new_item'       => __('Add New Testimonial', 'kilismile'),
            'edit_item'          => __('Edit Testimonial', 'kilismile'),
            'new_item'           => __('New Testimonial', 'kilismile'),
            'view_item'          => __('View Testimonial', 'kilismile'),
            'search_items'       => __('Search Testimonials', 'kilismile'),
            'not_found'          => __('No testimonials found', 'kilismile'),
            'not_found_in_trash' => __('No testimonials found in trash', 'kilismile'),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-format-quote',
        'supports'      => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite'       => array('slug' => 'testimonials'),
        'show_in_rest'  => true,
    ));
    
    // Events Post Type
    register_post_type('events', array(
        'labels' => array(
            'name'               => __('Events', 'kilismile'),
            'singular_name'      => __('Event', 'kilismile'),
            'add_new'            => __('Add New Event', 'kilismile'),
            'add_new_item'       => __('Add New Event', 'kilismile'),
            'edit_item'          => __('Edit Event', 'kilismile'),
            'new_item'           => __('New Event', 'kilismile'),
            'view_item'          => __('View Event', 'kilismile'),
            'search_items'       => __('Search Events', 'kilismile'),
            'not_found'          => __('No events found', 'kilismile'),
            'not_found_in_trash' => __('No events found in trash', 'kilismile'),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-calendar-alt',
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'       => array('slug' => 'events'),
        'show_in_rest'  => true,
    ));
    
    // Newsletter Post Type
    register_post_type('newsletter', array(
        'labels' => array(
            'name'               => __('Newsletters', 'kilismile'),
            'singular_name'      => __('Newsletter', 'kilismile'),
            'add_new'            => __('Add New Newsletter', 'kilismile'),
            'add_new_item'       => __('Add New Newsletter', 'kilismile'),
            'edit_item'          => __('Edit Newsletter', 'kilismile'),
            'new_item'           => __('New Newsletter', 'kilismile'),
            'view_item'          => __('View Newsletter', 'kilismile'),
            'search_items'       => __('Search Newsletters', 'kilismile'),
            'not_found'          => __('No newsletters found', 'kilismile'),
            'not_found_in_trash' => __('No newsletters found in trash', 'kilismile'),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-email-alt',
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'       => array('slug' => 'newsletters'),
        'show_in_rest'  => true,
    ));
}
add_action('init', 'kilismile_custom_post_types');

/**
 * Team member admin fields
 */
function kilismile_add_team_meta_boxes() {
    add_meta_box(
        'kilismile_team_details',
        __('Team Details', 'kilismile'),
        'kilismile_render_team_meta_box',
        'team',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'kilismile_add_team_meta_boxes');

function kilismile_render_team_meta_box($post) {
    wp_nonce_field('kilismile_team_meta_box', 'kilismile_team_meta_box_nonce');
    $position = get_post_meta($post->ID, '_team_position', true);
    $bio = get_post_meta($post->ID, '_team_bio', true);
    $is_leadership = get_post_meta($post->ID, '_team_is_leadership', true);
    ?>
    <div style="display: grid; gap: 16px; max-width: 720px;">
        <div>
            <label for="team_position" style="display:block; font-weight:600; margin-bottom:6px;">
                <?php esc_html_e('Position / Title', 'kilismile'); ?>
            </label>
            <input type="text" id="team_position" name="team_position" value="<?php echo esc_attr($position); ?>" style="width:100%; padding:8px 10px;">
        </div>
        <div>
            <label for="team_bio" style="display:block; font-weight:600; margin-bottom:6px;">
                <?php esc_html_e('Short Bio', 'kilismile'); ?>
            </label>
            <textarea id="team_bio" name="team_bio" rows="4" style="width:100%; padding:8px 10px;"><?php echo esc_textarea($bio); ?></textarea>
            <p style="margin:6px 0 0; color:#666;">
                <?php esc_html_e('Shown on the About page carousel (recommended 20–40 words).', 'kilismile'); ?>
            </p>
        </div>
        <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="team_is_leadership" value="yes" <?php checked($is_leadership, 'yes'); ?> />
            <?php esc_html_e('Mark as Leadership', 'kilismile'); ?>
        </label>
    </div>
    <?php
}

function kilismile_save_team_meta($post_id) {
    if (!isset($_POST['kilismile_team_meta_box_nonce']) || !wp_verify_nonce($_POST['kilismile_team_meta_box_nonce'], 'kilismile_team_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $position = isset($_POST['team_position']) ? sanitize_text_field($_POST['team_position']) : '';
    $bio = isset($_POST['team_bio']) ? sanitize_textarea_field($_POST['team_bio']) : '';
    $is_leadership = isset($_POST['team_is_leadership']) ? 'yes' : 'no';

    update_post_meta($post_id, '_team_position', $position);
    update_post_meta($post_id, '_team_bio', $bio);
    update_post_meta($post_id, '_team_is_leadership', $is_leadership);
}
add_action('save_post_team', 'kilismile_save_team_meta');

function kilismile_team_columns($columns) {
    $columns['team_position'] = __('Position', 'kilismile');
    $columns['team_leadership'] = __('Leadership', 'kilismile');
    return $columns;
}
add_filter('manage_team_posts_columns', 'kilismile_team_columns');

function kilismile_team_column_content($column, $post_id) {
    if ($column === 'team_position') {
        echo esc_html(get_post_meta($post_id, '_team_position', true));
    }
    if ($column === 'team_leadership') {
        $is_leadership = get_post_meta($post_id, '_team_is_leadership', true);
        echo $is_leadership === 'yes' ? esc_html__('Yes', 'kilismile') : esc_html__('No', 'kilismile');
    }
}
add_action('manage_team_posts_custom_column', 'kilismile_team_column_content', 10, 2);

/**
 * Team Directory Admin UI (simple, non-post based)
 */
function kilismile_register_team_directory_admin() {
    add_menu_page(
        __('Team Directory', 'kilismile'),
        __('Team Directory', 'kilismile'),
        'manage_options',
        'kilismile-team-directory',
        'kilismile_render_team_directory_page',
        'dashicons-groups',
        26
    );
}
add_action('admin_menu', 'kilismile_register_team_directory_admin');

function kilismile_team_directory_admin_assets($hook) {
    if ($hook !== 'toplevel_page_kilismile-team-directory') {
        return;
    }
    wp_enqueue_media();
    $script = <<<JS
        jQuery(function($){
            function bindImageButtons(container){
                container.find('.team-image-select').off('click').on('click', function(e){
                    e.preventDefault();
                    var button = $(this);
                    var frame = wp.media({
                        title: 'Select Team Photo',
                        button: { text: 'Use this image' },
                        multiple: false
                    });
                    frame.on('select', function(){
                        var attachment = frame.state().get('selection').first().toJSON();
                        button.closest('.team-row').find('.team-image-id').val(attachment.id);
                        button.closest('.team-row').find('.team-image-preview').html('<img src="'+attachment.url+'" style="width:70px;height:70px;object-fit:cover;border-radius:6px;" />');
                    });
                    frame.open();
                });

                container.find('.team-image-remove').off('click').on('click', function(e){
                    e.preventDefault();
                    var row = $(this).closest('.team-row');
                    row.find('.team-image-id').val('');
                    row.find('.team-image-preview').html('<span style="color:#777;">No image</span>');
                });

                container.find('.team-row-remove').off('click').on('click', function(e){
                    e.preventDefault();
                    $(this).closest('.team-row').remove();
                });
            }

            var table = $('#kilismile-team-directory');
            bindImageButtons(table);

            $('#team-row-add').on('click', function(e){
                e.preventDefault();
                var index = table.find('.team-row').length;
                var row = $(
                    '<div class="team-row">'
                    + '<div class="team-cell">'
                        + '<div class="team-image-preview"><span style="color:#777;">No image</span></div>'
                        + '<input type="hidden" name="team_members['+index+'][image_id]" class="team-image-id" />'
                        + '<div class="team-image-actions">'
                            + '<button class="button team-image-select">Select Image</button>'
                            + '<button class="button-link-delete team-image-remove">Remove</button>'
                        + '</div>'
                    + '</div>'
                    + '<div class="team-cell"><input type="text" name="team_members['+index+'][name]" class="regular-text" placeholder="Full name" /></div>'
                    + '<div class="team-cell"><input type="text" name="team_members['+index+'][role]" class="regular-text" placeholder="Role / Title" /></div>'
                    + '<div class="team-cell"><textarea name="team_members['+index+'][bio]" rows="3" placeholder="Short bio"></textarea></div>'
                    + '<div class="team-cell">'
                        + '<select name="team_members['+index+'][category]">'
                            + '<option value="leadership">Leadership</option>'
                            + '<option value="team">Team</option>'
                        + '</select>'
                    + '</div>'
                    + '<div class="team-cell team-cell--actions">'
                        + '<button class="button-link-delete team-row-remove">Remove</button>'
                    + '</div>'
                    + '</div>'
                );
                table.find('.team-rows').append(row);
                bindImageButtons(table);
            });
        });
JS;
    wp_add_inline_script('jquery', $script);
}
add_action('admin_enqueue_scripts', 'kilismile_team_directory_admin_assets');

function kilismile_render_team_directory_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['kilismile_team_directory_nonce']) && wp_verify_nonce($_POST['kilismile_team_directory_nonce'], 'kilismile_team_directory_save')) {
        $members = array();
        if (!empty($_POST['team_members']) && is_array($_POST['team_members'])) {
            foreach ($_POST['team_members'] as $member) {
                $name = isset($member['name']) ? sanitize_text_field($member['name']) : '';
                $role = isset($member['role']) ? sanitize_text_field($member['role']) : '';
                $bio = isset($member['bio']) ? sanitize_textarea_field($member['bio']) : '';
                $category = isset($member['category']) && $member['category'] === 'leadership' ? 'leadership' : 'team';
                $image_id = isset($member['image_id']) ? absint($member['image_id']) : 0;
                if ($name !== '') {
                    $members[] = array(
                        'name' => $name,
                        'role' => $role,
                        'bio' => $bio,
                        'category' => $category,
                        'image_id' => $image_id,
                    );
                }
            }
        }
        update_option('kilismile_team_directory', $members);
        echo '<div class="updated"><p>' . esc_html__('Team directory updated.', 'kilismile') . '</p></div>';
    }

    $members = get_option('kilismile_team_directory', array());
    ?>
    <div class="wrap" id="kilismile-team-directory">
        <h1><?php esc_html_e('Team Directory', 'kilismile'); ?></h1>
        <p><?php esc_html_e('Add leaders and team members with photo, role, and short bio. These entries power the About page carousels.', 'kilismile'); ?></p>

        <form method="post">
            <?php wp_nonce_field('kilismile_team_directory_save', 'kilismile_team_directory_nonce'); ?>

            <div class="team-table">
                <div class="team-header">
                    <div>Photo</div>
                    <div>Name</div>
                    <div>Role</div>
                    <div>Bio</div>
                    <div>Section</div>
                    <div></div>
                </div>
                <div class="team-rows">
                    <?php foreach ($members as $index => $member) :
                        $image_url = !empty($member['image_id']) ? wp_get_attachment_image_url($member['image_id'], 'thumbnail') : '';
                        ?>
                        <div class="team-row">
                            <div class="team-cell">
                                <div class="team-image-preview">
                                    <?php if ($image_url) : ?>
                                        <img src="<?php echo esc_url($image_url); ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px;" />
                                    <?php else : ?>
                                        <span style="color:#777;">No image</span>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="team_members[<?php echo esc_attr($index); ?>][image_id]" class="team-image-id" value="<?php echo esc_attr($member['image_id']); ?>" />
                                <div class="team-image-actions">
                                    <button class="button team-image-select">Select Image</button>
                                    <button class="button-link-delete team-image-remove">Remove</button>
                                </div>
                            </div>
                            <div class="team-cell">
                                <input type="text" name="team_members[<?php echo esc_attr($index); ?>][name]" class="regular-text" value="<?php echo esc_attr($member['name']); ?>" />
                            </div>
                            <div class="team-cell">
                                <input type="text" name="team_members[<?php echo esc_attr($index); ?>][role]" class="regular-text" value="<?php echo esc_attr($member['role']); ?>" />
                            </div>
                            <div class="team-cell">
                                <textarea name="team_members[<?php echo esc_attr($index); ?>][bio]" rows="3"><?php echo esc_textarea($member['bio']); ?></textarea>
                            </div>
                            <div class="team-cell">
                                <select name="team_members[<?php echo esc_attr($index); ?>][category]">
                                    <option value="leadership" <?php selected($member['category'], 'leadership'); ?>><?php esc_html_e('Leadership', 'kilismile'); ?></option>
                                    <option value="team" <?php selected($member['category'], 'team'); ?>><?php esc_html_e('Team', 'kilismile'); ?></option>
                                </select>
                            </div>
                            <div class="team-cell team-cell--actions">
                                <button class="button-link-delete team-row-remove">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <p>
                <button id="team-row-add" class="button">Add Member</button>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Changes</button>
            </p>
        </form>
    </div>

    <style>
        #kilismile-team-directory .team-table {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 20px;
        }
        #kilismile-team-directory .team-header,
        #kilismile-team-directory .team-row {
            display: grid;
            grid-template-columns: 140px 1fr 1fr 2fr 160px 80px;
            gap: 12px;
            padding: 12px;
            align-items: start;
        }
        #kilismile-team-directory .team-header {
            background: #f6f7f7;
            font-weight: 600;
        }
        #kilismile-team-directory .team-row {
            border-top: 1px solid #eee;
        }
        #kilismile-team-directory .team-cell textarea {
            width: 100%;
            min-height: 70px;
        }
        #kilismile-team-directory .team-image-actions {
            margin-top: 6px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        #kilismile-team-directory .team-cell--actions {
            display: flex;
            align-items: center;
        }
        @media (max-width: 1200px) {
            #kilismile-team-directory .team-header,
            #kilismile-team-directory .team-row {
                grid-template-columns: 120px 1fr 1fr 1.5fr 140px 70px;
            }
        }
        @media (max-width: 960px) {
            #kilismile-team-directory .team-header {
                display: none;
            }
            #kilismile-team-directory .team-row {
                grid-template-columns: 1fr;
            }
            #kilismile-team-directory .team-cell--actions {
                justify-content: flex-start;
            }
        }
    </style>
    <?php
}

/**
 * Custom Taxonomies
 */
function kilismile_custom_taxonomies() {
    // Program Categories
    register_taxonomy('program_category', 'programs', array(
        'labels' => array(
            'name'              => __('Program Categories', 'kilismile'),
            'singular_name'     => __('Program Category', 'kilismile'),
            'search_items'      => __('Search Program Categories', 'kilismile'),
            'all_items'         => __('All Program Categories', 'kilismile'),
            'parent_item'       => __('Parent Program Category', 'kilismile'),
            'parent_item_colon' => __('Parent Program Category:', 'kilismile'),
            'edit_item'         => __('Edit Program Category', 'kilismile'),
            'update_item'       => __('Update Program Category', 'kilismile'),
            'add_new_item'      => __('Add New Program Category', 'kilismile'),
            'new_item_name'     => __('New Program Category Name', 'kilismile'),
        ),
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => array('slug' => 'program-category'),
        'show_in_rest'      => true,
    ));
    
    // Event Categories
    register_taxonomy('event_category', 'events', array(
        'labels' => array(
            'name'              => __('Event Categories', 'kilismile'),
            'singular_name'     => __('Event Category', 'kilismile'),
            'search_items'      => __('Search Event Categories', 'kilismile'),
            'all_items'         => __('All Event Categories', 'kilismile'),
            'parent_item'       => __('Parent Event Category', 'kilismile'),
            'parent_item_colon' => __('Parent Event Category:', 'kilismile'),
            'edit_item'         => __('Edit Event Category', 'kilismile'),
            'update_item'       => __('Update Event Category', 'kilismile'),
            'add_new_item'      => __('Add New Event Category', 'kilismile'),
            'new_item_name'     => __('New Event Category Name', 'kilismile'),
        ),
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => array('slug' => 'event-category'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'kilismile_custom_taxonomies');

/**
 * Custom Image Sizes
 */
function kilismile_image_sizes() {
    add_image_size('hero-image', 1920, 600, true);
    add_image_size('program-thumbnail', 400, 300, true);
    add_image_size('team-member', 300, 400, true);
    add_image_size('news-thumbnail', 350, 200, true);
    add_image_size('gallery-thumb', 300, 300, true);
}
add_action('after_setup_theme', 'kilismile_image_sizes');

/**
 * Customizer Options
 */
function kilismile_customize_register($wp_customize) {
    // Organization Info Section
    $wp_customize->add_section('kilismile_organization', array(
        'title'    => __('Organization Information', 'kilismile'),
        'priority' => 30,
    ));
    
    // Organization tagline
    $wp_customize->add_setting('kilismile_tagline', array(
        'default'           => 'No health without oral health',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_tagline', array(
        'label'   => __('Organization Tagline', 'kilismile'),
        'section' => 'kilismile_organization',
        'type'    => 'text',
    ));
    
    // Registration number
    $wp_customize->add_setting('kilismile_registration', array(
        'default'           => '07NGO/R/6067',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_registration', array(
        'label'   => __('Registration Number', 'kilismile'),
        'section' => 'kilismile_organization',
        'type'    => 'text',
    ));
    
    // Contact information
    $wp_customize->add_setting('kilismile_phone', array(
        'default'           => '+255763495575/+255735495575',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_phone', array(
        'label'   => __('Phone Number', 'kilismile'),
        'section' => 'kilismile_organization',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_email', array(
        'default'           => 'kilismile21@gmail.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('kilismile_email', array(
        'label'   => __('Email Address', 'kilismile'),
        'section' => 'kilismile_organization',
        'type'    => 'email',
    ));
    
    $wp_customize->add_setting('kilismile_address', array(
        'default'           => 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('kilismile_address', array(
        'label'   => __('Address', 'kilismile'),
        'section' => 'kilismile_organization',
        'type'    => 'textarea',
    ));
    
    // Social Media Section
    $wp_customize->add_section('kilismile_social', array(
        'title'    => __('Social Media', 'kilismile'),
        'priority' => 35,
    ));
    
    // Instagram
    $wp_customize->add_setting('kilismile_instagram', array(
        'default'           => 'https://instagram.com/kili_smile',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_instagram', array(
        'label'   => __('Instagram URL', 'kilismile'),
        'section' => 'kilismile_social',
        'type'    => 'url',
    ));
    
    // Facebook
    $wp_customize->add_setting('kilismile_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_facebook', array(
        'label'   => __('Facebook URL', 'kilismile'),
        'section' => 'kilismile_social',
        'type'    => 'url',
    ));
    
    // Twitter
    $wp_customize->add_setting('kilismile_twitter', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_twitter', array(
        'label'   => __('Twitter URL', 'kilismile'),
        'section' => 'kilismile_social',
        'type'    => 'url',
    ));
    
    // Donation Section
    $wp_customize->add_section('kilismile_donation', array(
        'title'    => __('Donation Settings', 'kilismile'),
        'priority' => 40,
    ));
    
    // Donation URL
    $wp_customize->add_setting('kilismile_donation_url', array(
        'default'           => '#donate',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('kilismile_donation_url', array(
        'label'   => __('Donation Page URL', 'kilismile'),
        'section' => 'kilismile_donation',
        'type'    => 'url',
    ));
    
    // Donation button text
    $wp_customize->add_setting('kilismile_donation_text', array(
        'default'           => 'Donate Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_donation_text', array(
        'label'   => __('Donation Button Text', 'kilismile'),
        'section' => 'kilismile_donation',
        'type'    => 'text',
    ));
    
    // Bank Transfer Details Section
    $wp_customize->add_setting('kilismile_bank_name', array(
        'default'           => 'CRDB Bank',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_bank_name', array(
        'label'       => __('Bank Name', 'kilismile'),
        'description' => __('Bank name for manual transfer donations', 'kilismile'),
        'section'     => 'kilismile_donation',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_bank_account_name', array(
        'default'           => 'Kilimanjaro Smile Foundation',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_bank_account_name', array(
        'label'       => __('Bank Account Name', 'kilismile'),
        'description' => __('Account holder name', 'kilismile'),
        'section'     => 'kilismile_donation',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_bank_account_number', array(
        'default'           => '0150414479200',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_bank_account_number', array(
        'label'       => __('Bank Account Number', 'kilismile'),
        'description' => __('Account number for receiving donations', 'kilismile'),
        'section'     => 'kilismile_donation',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_bank_swift_code', array(
        'default'           => 'CORUTZTZ',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_bank_swift_code', array(
        'label'       => __('Bank SWIFT Code', 'kilismile'),
        'description' => __('SWIFT/BIC code for international transfers', 'kilismile'),
        'section'     => 'kilismile_donation',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('kilismile_bank_branch', array(
        'default'           => 'Moshi, Kilimanjaro',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kilismile_bank_branch', array(
        'label'       => __('Bank Branch (Optional)', 'kilismile'),
        'description' => __('Branch location for local transfers', 'kilismile'),
        'section'     => 'kilismile_donation',
        'type'        => 'text',
    ));
    
}
add_action('customize_register', 'kilismile_customize_register');

/**
 * Test Customizer Function - Header Logo Settings
 */
function kilismile_test_customizer($wp_customize) {
    
    // Test Header Section
    $wp_customize->add_section('kilismile_test_header', array(
        'title'    => __('Header Logo Settings', 'kilismile'),
        'priority' => 25,
        'description' => __('Customize your header logo appearance and size.', 'kilismile'),
    ));
    
    // Logo Size Setting (No Limits) - Override other logo size settings
    $wp_customize->add_setting('kilismile_logo_size', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('kilismile_logo_size', array(
        'label'       => __('Logo Size (px)', 'kilismile'),
        'description' => __('Set the logo size in pixels. No size restrictions. Changes apply instantly.', 'kilismile'),
        'section'     => 'kilismile_test_header',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 500,
            'step' => 1,
        ),
    ));
    
    // Footer Logo Setting
    $wp_customize->add_setting('kilismile_test_footer_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'kilismile_test_footer_logo', array(
        'label'       => __('Footer Logo', 'kilismile'),
        'description' => __('Upload a custom footer logo.', 'kilismile'),
        'section'     => 'kilismile_test_header',
        'mime_type'   => 'image',
    )));
    
}
add_action('customize_register', 'kilismile_test_customizer');

/**
 * Live Preview JavaScript for Logo Size
 */
function kilismile_logo_customizer_live_preview() {
    if (is_customize_preview()) {
        ?>
        <script type="text/javascript">
        (function($) {
            // Check if wp.customize is available
            if (typeof wp === 'undefined' || !wp.customize) {
                return;
            }
            
            // Logo Size Live Preview
            wp.customize('kilismile_logo_size', function(value) {
                value.bind(function(to) {
                    // Update CSS custom property
                    $(':root').css('--logo-size', to + 'px');
                    
                    // Update logo elements directly
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css({
                        'width': to + 'px',
                        'height': to + 'px'
                    });
                });
            });
            
            // Also handle the original logo_size setting
            wp.customize('logo_size', function(value) {
                value.bind(function(to) {
                    $(':root').css('--logo-size', to + 'px');
                    $('.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo').css({
                        'width': to + 'px',
                        'height': to + 'px'
                    });
                });
            });
            
        })(jQuery);
        </script>
        <?php
    }
}
add_action('wp_footer', 'kilismile_logo_customizer_live_preview');

/**
 * Add Custom Meta Boxes
 */
function kilismile_add_meta_boxes() {
    // Team member meta box
    add_meta_box(
        'team_member_details',
        __('Team Member Details', 'kilismile'),
        'kilismile_team_member_meta_box',
        'team',
        'normal',
        'high'
    );
    
    // Event meta box
    add_meta_box(
        'event_details',
        __('Event Details', 'kilismile'),
        'kilismile_event_meta_box',
        'events',
        'normal',
        'high'
    );
    
    // Program meta box
    add_meta_box(
        'program_details',
        __('Program Details', 'kilismile'),
        'kilismile_program_meta_box',
        'programs',
        'normal',
        'high'
    );
    
    // Newsletter meta box
    add_meta_box(
        'newsletter_details',
        __('Newsletter Details', 'kilismile'),
        'kilismile_newsletter_meta_box',
        'newsletter',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'kilismile_add_meta_boxes');

/**
 * Team Member Meta Box Callback
 */
function kilismile_team_member_meta_box($post) {
    wp_nonce_field('kilismile_team_member_nonce', 'kilismile_team_member_nonce');
    
    $position = get_post_meta($post->ID, '_team_position', true);
    $bio = get_post_meta($post->ID, '_team_bio', true);
    $email = get_post_meta($post->ID, '_team_email', true);
    $phone = get_post_meta($post->ID, '_team_phone', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="team_position"><?php _e('Position', 'kilismile'); ?></label></th>
            <td><input type="text" id="team_position" name="team_position" value="<?php echo esc_attr($position); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_bio"><?php _e('Short Bio', 'kilismile'); ?></label></th>
            <td><textarea id="team_bio" name="team_bio" rows="4" class="large-text"><?php echo esc_textarea($bio); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="team_email"><?php _e('Email', 'kilismile'); ?></label></th>
            <td><input type="email" id="team_email" name="team_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_phone"><?php _e('Phone', 'kilismile'); ?></label></th>
            <td><input type="text" id="team_phone" name="team_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Event Meta Box Callback
 */
function kilismile_event_meta_box($post) {
    wp_nonce_field('kilismile_event_nonce', 'kilismile_event_nonce');
    
    $date = get_post_meta($post->ID, '_event_date', true);
    $time = get_post_meta($post->ID, '_event_time', true);
    $location = get_post_meta($post->ID, '_event_location', true);
    $capacity = get_post_meta($post->ID, '_event_capacity', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="event_date"><?php _e('Event Date', 'kilismile'); ?></label></th>
            <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($date); ?>" /></td>
        </tr>
        <tr>
            <th><label for="event_time"><?php _e('Event Time', 'kilismile'); ?></label></th>
            <td><input type="time" id="event_time" name="event_time" value="<?php echo esc_attr($time); ?>" /></td>
        </tr>
        <tr>
            <th><label for="event_location"><?php _e('Location', 'kilismile'); ?></label></th>
            <td><input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($location); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="event_capacity"><?php _e('Capacity', 'kilismile'); ?></label></th>
            <td><input type="number" id="event_capacity" name="event_capacity" value="<?php echo esc_attr($capacity); ?>" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Program Meta Box Callback
 */
function kilismile_program_meta_box($post) {
    wp_nonce_field('kilismile_program_nonce', 'kilismile_program_nonce');
    
    $target_audience = get_post_meta($post->ID, '_program_target_audience', true);
    $duration = get_post_meta($post->ID, '_program_duration', true);
    $status = get_post_meta($post->ID, '_program_status', true);
    $beneficiaries = get_post_meta($post->ID, '_program_beneficiaries', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="program_target_audience"><?php _e('Target Audience', 'kilismile'); ?></label></th>
            <td>
                <select id="program_target_audience" name="program_target_audience">
                    <option value="children" <?php selected($target_audience, 'children'); ?>><?php _e('Children', 'kilismile'); ?></option>
                    <option value="elderly" <?php selected($target_audience, 'elderly'); ?>><?php _e('Elderly', 'kilismile'); ?></option>
                    <option value="teachers" <?php selected($target_audience, 'teachers'); ?>><?php _e('Teachers', 'kilismile'); ?></option>
                    <option value="community" <?php selected($target_audience, 'community'); ?>><?php _e('Community', 'kilismile'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="program_duration"><?php _e('Duration', 'kilismile'); ?></label></th>
            <td><input type="text" id="program_duration" name="program_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="program_status"><?php _e('Status', 'kilismile'); ?></label></th>
            <td>
                <select id="program_status" name="program_status">
                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'kilismile'); ?></option>
                    <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'kilismile'); ?></option>
                    <option value="planned" <?php selected($status, 'planned'); ?>><?php _e('Planned', 'kilismile'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="program_beneficiaries"><?php _e('Number of Beneficiaries', 'kilismile'); ?></label></th>
            <td><input type="number" id="program_beneficiaries" name="program_beneficiaries" value="<?php echo esc_attr($beneficiaries); ?>" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Newsletter Meta Box Callback
 */
function kilismile_newsletter_meta_box($post) {
    wp_nonce_field('kilismile_newsletter_nonce', 'kilismile_newsletter_nonce');
    
    $newsletter_date = get_post_meta($post->ID, '_newsletter_date', true);
    $newsletter_issue = get_post_meta($post->ID, '_newsletter_issue', true);
    $newsletter_pdf = get_post_meta($post->ID, '_newsletter_pdf', true);
    $newsletter_subject = get_post_meta($post->ID, '_newsletter_subject', true);
    $newsletter_sent_date = get_post_meta($post->ID, '_newsletter_sent_date', true);
    $newsletter_recipients = get_post_meta($post->ID, '_newsletter_recipients', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="newsletter_issue"><?php _e('Issue Number', 'kilismile'); ?></label></th>
            <td><input type="number" id="newsletter_issue" name="newsletter_issue" value="<?php echo esc_attr($newsletter_issue); ?>" min="1" /></td>
        </tr>
        <tr>
            <th><label for="newsletter_date"><?php _e('Newsletter Date', 'kilismile'); ?></label></th>
            <td><input type="date" id="newsletter_date" name="newsletter_date" value="<?php echo esc_attr($newsletter_date); ?>" /></td>
        </tr>
        <tr>
            <th><label for="newsletter_subject"><?php _e('Email Subject Line', 'kilismile'); ?></label></th>
            <td><input type="text" id="newsletter_subject" name="newsletter_subject" value="<?php echo esc_attr($newsletter_subject); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="newsletter_pdf"><?php _e('PDF Download URL', 'kilismile'); ?></label></th>
            <td>
                <input type="url" id="newsletter_pdf" name="newsletter_pdf" value="<?php echo esc_attr($newsletter_pdf); ?>" class="regular-text" />
                <p class="description"><?php _e('Optional: Link to downloadable PDF version of the newsletter.', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="newsletter_sent_date"><?php _e('Sent Date', 'kilismile'); ?></label></th>
            <td>
                <input type="datetime-local" id="newsletter_sent_date" name="newsletter_sent_date" value="<?php echo esc_attr($newsletter_sent_date); ?>" />
                <p class="description"><?php _e('When was this newsletter sent to subscribers?', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="newsletter_recipients"><?php _e('Number of Recipients', 'kilismile'); ?></label></th>
            <td>
                <input type="number" id="newsletter_recipients" name="newsletter_recipients" value="<?php echo esc_attr($newsletter_recipients); ?>" min="0" />
                <p class="description"><?php _e('How many subscribers received this newsletter?', 'kilismile'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Meta Box Data
 */
function kilismile_save_meta_boxes($post_id) {
    // Check if nonce is valid
    if (!isset($_POST['kilismile_team_member_nonce']) && 
        !isset($_POST['kilismile_event_nonce']) && 
        !isset($_POST['kilismile_program_nonce']) &&
        !isset($_POST['kilismile_newsletter_nonce'])) {
        return;
    }
    
    // Check if user has permissions to save data
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save team member meta
    if (isset($_POST['kilismile_team_member_nonce']) && wp_verify_nonce($_POST['kilismile_team_member_nonce'], 'kilismile_team_member_nonce')) {
        if (isset($_POST['team_position'])) {
            update_post_meta($post_id, '_team_position', sanitize_text_field($_POST['team_position']));
        }
        if (isset($_POST['team_bio'])) {
            update_post_meta($post_id, '_team_bio', sanitize_textarea_field($_POST['team_bio']));
        }
        if (isset($_POST['team_email'])) {
            update_post_meta($post_id, '_team_email', sanitize_email($_POST['team_email']));
        }
        if (isset($_POST['team_phone'])) {
            update_post_meta($post_id, '_team_phone', sanitize_text_field($_POST['team_phone']));
        }
    }
    
    // Save event meta
    if (isset($_POST['kilismile_event_nonce']) && wp_verify_nonce($_POST['kilismile_event_nonce'], 'kilismile_event_nonce')) {
        if (isset($_POST['event_date'])) {
            update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
        }
        if (isset($_POST['event_time'])) {
            update_post_meta($post_id, '_event_time', sanitize_text_field($_POST['event_time']));
        }
        if (isset($_POST['event_location'])) {
            update_post_meta($post_id, '_event_location', sanitize_text_field($_POST['event_location']));
        }
        if (isset($_POST['event_capacity'])) {
            update_post_meta($post_id, '_event_capacity', intval($_POST['event_capacity']));
        }
    }
    
    // Save program meta
    if (isset($_POST['kilismile_program_nonce']) && wp_verify_nonce($_POST['kilismile_program_nonce'], 'kilismile_program_nonce')) {
        if (isset($_POST['program_target_audience'])) {
            update_post_meta($post_id, '_program_target_audience', sanitize_text_field($_POST['program_target_audience']));
        }
        if (isset($_POST['program_duration'])) {
            update_post_meta($post_id, '_program_duration', sanitize_text_field($_POST['program_duration']));
        }
        if (isset($_POST['program_status'])) {
            update_post_meta($post_id, '_program_status', sanitize_text_field($_POST['program_status']));
        }
        if (isset($_POST['program_beneficiaries'])) {
            update_post_meta($post_id, '_program_beneficiaries', intval($_POST['program_beneficiaries']));
        }
    }
    
    // Save newsletter meta
    if (isset($_POST['kilismile_newsletter_nonce']) && wp_verify_nonce($_POST['kilismile_newsletter_nonce'], 'kilismile_newsletter_nonce')) {
        if (isset($_POST['newsletter_issue'])) {
            update_post_meta($post_id, '_newsletter_issue', intval($_POST['newsletter_issue']));
        }
        if (isset($_POST['newsletter_date'])) {
            update_post_meta($post_id, '_newsletter_date', sanitize_text_field($_POST['newsletter_date']));
        }
        if (isset($_POST['newsletter_subject'])) {
            update_post_meta($post_id, '_newsletter_subject', sanitize_text_field($_POST['newsletter_subject']));
        }
        if (isset($_POST['newsletter_pdf'])) {
            update_post_meta($post_id, '_newsletter_pdf', esc_url_raw($_POST['newsletter_pdf']));
        }
        if (isset($_POST['newsletter_sent_date'])) {
            update_post_meta($post_id, '_newsletter_sent_date', sanitize_text_field($_POST['newsletter_sent_date']));
        }
        if (isset($_POST['newsletter_recipients'])) {
            update_post_meta($post_id, '_newsletter_recipients', intval($_POST['newsletter_recipients']));
        }
    }
}
add_action('save_post', 'kilismile_save_meta_boxes');

/**
 * Custom Excerpt Length
 */
function kilismile_excerpt_length($length) {
    if (is_home() || is_archive()) {
        return 30;
    }
    return $length;
}
add_filter('excerpt_length', 'kilismile_excerpt_length');

/**
 * Custom Excerpt More
 */
function kilismile_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'kilismile_excerpt_more');

/**
 * Security Enhancements
 */
// Remove WordPress version number
function kilismile_remove_version() {
    return '';
}
add_filter('the_generator', 'kilismile_remove_version');

// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Remove unnecessary meta tags
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

/**
 * Performance Optimizations
 */
// Remove emoji scripts
function kilismile_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'kilismile_disable_emojis');

/**
 * Auto-create required pages on theme activation
 */
function kilismile_create_required_pages() {
    $required_pages = array(
        'about' => array(
            'title' => 'About Us',
            'content' => 'Learn about Kilismile Organization, our mission, vision, and the team dedicated to improving oral health in Tanzania.',
            'template' => 'page-about.php'
        ),
        'programs' => array(
            'title' => 'Our Programs',
            'content' => 'Discover our comprehensive health education and screening programs designed to serve remote communities in Tanzania.',
            'template' => 'page-programs.php'
        ),
        'volunteer' => array(
            'title' => 'Volunteer',
            'content' => 'Join our mission and make a difference in the lives of people in remote communities. Find volunteer opportunities that match your skills.',
            'template' => 'page-volunteer.php'
        ),
        'donations' => array(
            'title' => 'Donate',
            'content' => 'Support our mission with a donation. Every contribution helps us provide essential health services to those who need it most.',
            'template' => 'page-donations.php'
        ),
        'contact' => array(
            'title' => 'Contact Us',
            'content' => 'Get in touch with Kilismile Organization. We would love to hear from you and answer any questions you may have.',
            'template' => 'page-contact.php'
        ),
        'news' => array(
            'title' => 'News & Events',
            'content' => 'Stay updated with the latest news, events, and success stories from our work in Tanzania.',
            'template' => 'page-news.php'
        ),
        'gallery' => array(
            'title' => 'Photo Gallery',
            'content' => 'View photos from our field work, community outreach programs, and the impact we are making in Tanzania.',
            'template' => 'page-gallery.php'
        ),
        'resources' => array(
            'title' => 'Resources',
            'content' => 'Access health education materials, downloads, FAQs, and other helpful resources.',
            'template' => 'page-resources.php'
        ),
        'partnerships' => array(
            'title' => 'Partnerships',
            'content' => 'Learn about our partnerships and how organizations can collaborate with us to expand our impact.',
            'template' => 'page-partnerships.php'
        ),
        'fundraising' => array(
            'title' => 'Fundraising',
            'content' => 'Start your own fundraising campaign to support Kilismile Organization and help us reach more communities.',
            'template' => 'page-fundraising.php'
        ),
        'newsletter' => array(
            'title' => 'Newsletter',
            'content' => 'Subscribe to our newsletter to stay updated with our latest programs, success stories, and ways to get involved.',
            'template' => 'page-newsletter.php'
        )
    );

    foreach ($required_pages as $slug => $page_data) {
        // Check if page already exists
        $page = get_page_by_path($slug);
        
        if (!$page) {
            // Create the page
            $page_id = wp_insert_post(array(
                'post_title'    => $page_data['title'],
                'post_content'  => $page_data['content'],
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_name'     => $slug,
                'post_author'   => 1,
                'comment_status' => 'closed',
                'ping_status'   => 'closed'
            ));

            if ($page_id && !is_wp_error($page_id)) {
                // Set page template if specified
                if (isset($page_data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }
                
                // Set featured image if available
                if (isset($page_data['featured_image'])) {
                    set_post_thumbnail($page_id, $page_data['featured_image']);
                }

                error_log("Kilismile: Created page '{$page_data['title']}' with slug '{$slug}'");
            }
        }
    }
    
    // Create a custom menu if it doesn't exist
    $menu_name = 'Primary Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add menu items
            $menu_items = array(
                array('title' => 'Home', 'url' => home_url('/')),
                array('title' => 'About Us', 'url' => home_url('/about')),
                array('title' => 'Our Programs', 'url' => home_url('/programs')),
                array('title' => 'Volunteer', 'url' => home_url('/volunteer')),
                array('title' => 'News & Events', 'url' => home_url('/news')),
                array('title' => 'Gallery', 'url' => home_url('/gallery')),
                array('title' => 'Contact', 'url' => home_url('/contact'))
            );
            
            foreach ($menu_items as $item) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => $item['title'],
                    'menu-item-url' => $item['url'],
                    'menu-item-status' => 'publish'
                ));
            }
            
            // Assign menu to primary location
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}

// Hook into theme activation
add_action('after_switch_theme', 'kilismile_create_required_pages');

// Also create pages on theme setup for new installations
add_action('after_setup_theme', function() {
    if (get_option('kilismile_pages_created') !== 'yes') {
        kilismile_create_required_pages();
        update_option('kilismile_pages_created', 'yes');
    }
});

/**
 * Fix and Enhance Newsletter Page 
 * 
 * This function ensures the newsletter page exists and has the correct template
 * Also adds enhanced content and features to the newsletter page
 */
function kilismile_fix_newsletter_page() {
    // Check if the newsletter page exists
    $newsletter_page = get_page_by_path('newsletter');
    
    // Enhanced content for the newsletter page
    $enhanced_content = '<!-- wp:paragraph {"className":"newsletter-intro"} -->
<p class="newsletter-intro">Stay connected with Kilismile Organization and our mission to improve oral health in Tanzania. Subscribe to our newsletter to receive regular updates about our programs, success stories, upcoming events, and ways you can make a difference.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[kilismile_newsletter_form]
<!-- /wp:shortcode -->

<!-- wp:heading {"level":3} -->
<h3>Recent Newsletters</h3>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[kilismile_recent_newsletters limit="4"]
<!-- /wp:shortcode -->';

    if (!$newsletter_page) {
        // Create the newsletter page with enhanced content
        $page_id = wp_insert_post(array(
            'post_title'    => 'Newsletter',
            'post_content'  => $enhanced_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'newsletter',
            'post_author'   => 1,
            'comment_status' => 'closed',
            'ping_status'   => 'closed'
        ));

        if ($page_id && !is_wp_error($page_id)) {
            // Set page template
            update_post_meta($page_id, '_wp_page_template', 'page-newsletter.php');
            
            // Add featured image if available
            $default_image_id = get_option('kilismile_default_newsletter_image');
            if ($default_image_id) {
                set_post_thumbnail($page_id, $default_image_id);
            }
            
            error_log("Kilismile: Created enhanced newsletter page with ID: $page_id");
        }
    } else {
        // Ensure template is set correctly
        $template = get_post_meta($newsletter_page->ID, '_wp_page_template', true);
        
        if ($template !== 'page-newsletter.php') {
            update_post_meta($newsletter_page->ID, '_wp_page_template', 'page-newsletter.php');
            error_log("Kilismile: Updated newsletter page template to 'page-newsletter.php'");
        }
        
        // Only update content if it's the default basic content
        $current_content = $newsletter_page->post_content;
        if (trim($current_content) === 'Subscribe to our newsletter to stay updated with our latest programs, success stories, and ways to get involved.') {
            wp_update_post(array(
                'ID' => $newsletter_page->ID,
                'post_content' => $enhanced_content
            ));
            error_log("Kilismile: Enhanced newsletter page content");
        }
    }
}

// Run the fix on init to ensure the newsletter page exists
add_action('init', 'kilismile_fix_newsletter_page');

/**
 * Theme Dashboard Options - Gallery Management
 */

// Add admin menu for theme options
function kilismile_add_theme_options_menu() {
    add_menu_page(
        __('KiliSmile Theme Options', 'kilismile'),
        __('KiliSmile Options', 'kilismile'),
        'manage_options',
        'kilismile-options',
        'kilismile_theme_options_page',
        'dashicons-images-alt2',
        30
    );
    
    add_submenu_page(
        'kilismile-options',
        __('Gallery Management', 'kilismile'),
        __('Gallery Management', 'kilismile'),
        'manage_options',
        'kilismile-gallery',
        'kilismile_gallery_management_page'
    );
}
add_action('admin_menu', 'kilismile_add_theme_options_menu');

// Theme Options Main Page
function kilismile_theme_options_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('KiliSmile Theme Options', 'kilismile'); ?></h1>
        <div class="kilismile-options-dashboard">
            <div class="kilismile-option-card" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2><i class="dashicons dashicons-images-alt2"></i> <?php _e('Gallery Management', 'kilismile'); ?></h2>
                <p><?php _e('Manage your photo gallery content, categories, and display settings.', 'kilismile'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=kilismile-gallery'); ?>" class="button button-primary">
                    <?php _e('Manage Gallery', 'kilismile'); ?>
                </a>
            </div>
            
            <div class="kilismile-option-card" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2><i class="dashicons dashicons-admin-customizer"></i> <?php _e('Theme Customizer', 'kilismile'); ?></h2>
                <p><?php _e('Customize colors, fonts, logos, and other appearance settings.', 'kilismile'); ?></p>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                    <?php _e('Open Customizer', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </div>
    
    <style>
    .kilismile-options-dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .kilismile-option-card h2 {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #2271b1;
        margin-bottom: 15px;
    }
    .kilismile-option-card .dashicons {
        font-size: 24px;
    }
    </style>
    <?php
}

// Gallery Management Page
function kilismile_gallery_management_page() {
    // Handle form submission
    if (isset($_POST['kilismile_gallery_save']) && wp_verify_nonce($_POST['kilismile_gallery_nonce'], 'kilismile_gallery_save')) {
        kilismile_save_gallery_options();
        echo '<div class="notice notice-success"><p>' . __('Gallery settings saved successfully!', 'kilismile') . '</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('Gallery Management', 'kilismile'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kilismile_gallery_save', 'kilismile_gallery_nonce'); ?>
            
            <div class="kilismile-gallery-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#gallery-items" class="nav-tab nav-tab-active" onclick="switchTab(event, 'gallery-items')"><?php _e('Gallery Items', 'kilismile'); ?></a>
                    <a href="#gallery-settings" class="nav-tab" onclick="switchTab(event, 'gallery-settings')"><?php _e('Gallery Settings', 'kilismile'); ?></a>
                </nav>
                
                <!-- Gallery Items Tab -->
                <div id="gallery-items" class="tab-content active">
                    <h2><?php _e('Gallery Images', 'kilismile'); ?></h2>
                    <p><?php _e('Add up to 20 images to your gallery. Each image can have a title, description, category, and featured status.', 'kilismile'); ?></p>
                    
                    <!-- Bulk Upload Section -->
                    <div class="bulk-upload-section" style="background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px dashed #ccc;">
                        <h3><i class="dashicons dashicons-upload"></i> <?php _e('Bulk Upload', 'kilismile'); ?></h3>
                        <p><?php _e('Upload multiple images at once and they will be automatically added to available gallery slots.', 'kilismile'); ?></p>
                        
                        <div class="bulk-upload-controls">
                            <button type="button" id="bulk-upload-btn" class="button button-primary" style="margin-right: 10px;">
                                <i class="dashicons dashicons-images-alt2"></i> <?php _e('Select Multiple Images', 'kilismile'); ?>
                            </button>
                            
                            <label style="margin-left: 20px;">
                                <?php _e('Default Category:', 'kilismile'); ?>
                                <select id="bulk-category" style="margin-left: 10px;">
                                    <option value="healthcare"><?php _e('Healthcare', 'kilismile'); ?></option>
                                    <option value="education"><?php _e('Education', 'kilismile'); ?></option>
                                    <option value="community"><?php _e('Community', 'kilismile'); ?></option>
                                    <option value="events"><?php _e('Events', 'kilismile'); ?></option>
                                    <option value="volunteers"><?php _e('Volunteers', 'kilismile'); ?></option>
                                    <option value="outreach"><?php _e('Outreach', 'kilismile'); ?></option>
                                    <option value="training"><?php _e('Training', 'kilismile'); ?></option>
                                    <option value="awareness"><?php _e('Awareness', 'kilismile'); ?></option>
                                </select>
                            </label>
                            
                            <label style="margin-left: 20px;">
                                <input type="checkbox" id="bulk-featured"> <?php _e('Mark all as featured', 'kilismile'); ?>
                            </label>
                        </div>
                        
                        <div id="bulk-upload-progress" style="display: none; margin-top: 15px;">
                            <div class="progress-bar" style="background: #e0e0e0; height: 20px; border-radius: 10px; overflow: hidden;">
                                <div class="progress-fill" style="background: #4CAF50; height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                            <p id="bulk-progress-text" style="margin-top: 10px;"></p>
                        </div>
                    </div>
                    
                    <!-- Quick Add Section -->
                    <div class="quick-add-section" style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; border: 1px solid #ddd;">
                        <h3><i class="dashicons dashicons-plus-alt"></i> <?php _e('Quick Add Single Image', 'kilismile'); ?></h3>
                        <p><?php _e('Quickly add a single image to the next available slot with basic information.', 'kilismile'); ?></p>
                        
                        <div class="quick-add-form" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                            <div>
                                <label><?php _e('Image', 'kilismile'); ?></label><br>
                                <button type="button" id="quick-add-image-btn" class="button">
                                    <?php _e('Choose Image', 'kilismile'); ?>
                                </button>
                                <input type="hidden" id="quick-add-image-id">
                                <div id="quick-add-preview" style="margin-top: 10px;"></div>
                            </div>
                            
                            <div>
                                <label for="quick-add-title"><?php _e('Title', 'kilismile'); ?></label><br>
                                <input type="text" id="quick-add-title" class="regular-text" placeholder="<?php _e('Image title...', 'kilismile'); ?>">
                            </div>
                            
                            <div>
                                <label for="quick-add-category"><?php _e('Category', 'kilismile'); ?></label><br>
                                <select id="quick-add-category">
                                    <option value="healthcare"><?php _e('Healthcare', 'kilismile'); ?></option>
                                    <option value="education"><?php _e('Education', 'kilismile'); ?></option>
                                    <option value="community"><?php _e('Community', 'kilismile'); ?></option>
                                    <option value="events"><?php _e('Events', 'kilismile'); ?></option>
                                    <option value="volunteers"><?php _e('Volunteers', 'kilismile'); ?></option>
                                    <option value="outreach"><?php _e('Outreach', 'kilismile'); ?></option>
                                    <option value="training"><?php _e('Training', 'kilismile'); ?></option>
                                    <option value="awareness"><?php _e('Awareness', 'kilismile'); ?></option>
                                </select>
                            </div>
                            
                            <div>
                                <button type="button" id="quick-add-btn" class="button button-primary" disabled>
                                    <?php _e('Add to Gallery', 'kilismile'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <textarea id="quick-add-description" class="large-text" rows="2" placeholder="<?php _e('Optional description...', 'kilismile'); ?>"></textarea>
                        </div>
                        
                        <div style="margin-top: 10px;">
                            <label>
                                <input type="checkbox" id="quick-add-featured"> <?php _e('Mark as featured', 'kilismile'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="kilismile-gallery-items">
                        <?php for ($i = 1; $i <= 20; $i++) : 
                            $image_id = get_option("kilismile_gallery_image_{$i}", '');
                            $title = get_option("kilismile_gallery_title_{$i}", '');
                            $description = get_option("kilismile_gallery_description_{$i}", '');
                            $category = get_option("kilismile_gallery_category_{$i}", 'healthcare');
                            $featured = get_option("kilismile_gallery_featured_{$i}", false);
                        ?>
                        <div class="gallery-item-panel" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #2271b1;">
                            <h3><?php printf(__('Gallery Item %d', 'kilismile'), $i); ?></h3>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e('Image', 'kilismile'); ?></th>
                                    <td>
                                        <div class="image-upload-container">
                                            <input type="hidden" name="kilismile_gallery_image_<?php echo $i; ?>" id="kilismile_gallery_image_<?php echo $i; ?>" value="<?php echo esc_attr($image_id); ?>" />
                                            <button type="button" class="button upload-image-button" data-target="kilismile_gallery_image_<?php echo $i; ?>">
                                                <?php _e('Choose Image', 'kilismile'); ?>
                                            </button>
                                            <button type="button" class="button remove-image-button" data-target="kilismile_gallery_image_<?php echo $i; ?>" style="<?php echo empty($image_id) ? 'display: none;' : ''; ?>">
                                                <?php _e('Remove Image', 'kilismile'); ?>
                                            </button>
                                            <div class="image-preview" id="preview_kilismile_gallery_image_<?php echo $i; ?>">
                                                <?php if ($image_id) : 
                                                    $image_url = wp_get_attachment_image_url($image_id, 'medium');
                                                    if ($image_url) : ?>
                                                        <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto; margin-top: 10px; border-radius: 4px;" />
                                                    <?php endif;
                                                endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('Title', 'kilismile'); ?></th>
                                    <td>
                                        <input type="text" name="kilismile_gallery_title_<?php echo $i; ?>" value="<?php echo esc_attr($title); ?>" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('Description', 'kilismile'); ?></th>
                                    <td>
                                        <textarea name="kilismile_gallery_description_<?php echo $i; ?>" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('Category', 'kilismile'); ?></th>
                                    <td>
                                        <select name="kilismile_gallery_category_<?php echo $i; ?>">
                                            <option value="healthcare" <?php selected($category, 'healthcare'); ?>><?php _e('Healthcare', 'kilismile'); ?></option>
                                            <option value="education" <?php selected($category, 'education'); ?>><?php _e('Education', 'kilismile'); ?></option>
                                            <option value="community" <?php selected($category, 'community'); ?>><?php _e('Community', 'kilismile'); ?></option>
                                            <option value="events" <?php selected($category, 'events'); ?>><?php _e('Events', 'kilismile'); ?></option>
                                            <option value="volunteers" <?php selected($category, 'volunteers'); ?>><?php _e('Volunteers', 'kilismile'); ?></option>
                                            <option value="outreach" <?php selected($category, 'outreach'); ?>><?php _e('Outreach', 'kilismile'); ?></option>
                                            <option value="training" <?php selected($category, 'training'); ?>><?php _e('Training', 'kilismile'); ?></option>
                                            <option value="awareness" <?php selected($category, 'awareness'); ?>><?php _e('Awareness', 'kilismile'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('Featured', 'kilismile'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="kilismile_gallery_featured_<?php echo $i; ?>" value="1" <?php checked($featured, true); ?> />
                                            <?php _e('Mark as featured image', 'kilismile'); ?>
                                        </label>
                                        <p class="description"><?php _e('Featured images are displayed prominently in the gallery.', 'kilismile'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Gallery Settings Tab -->
                <div id="gallery-settings" class="tab-content">
                    <h2><?php _e('Gallery Display Settings', 'kilismile'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Items per Page', 'kilismile'); ?></th>
                            <td>
                                <input type="number" name="kilismile_gallery_items_per_page" value="<?php echo esc_attr(get_option('kilismile_gallery_items_per_page', 12)); ?>" min="6" max="30" step="6" />
                                <p class="description"><?php _e('Number of gallery items to show per page', 'kilismile'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Enable Lightbox', 'kilismile'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="kilismile_gallery_lightbox_enabled" value="1" <?php checked(get_option('kilismile_gallery_lightbox_enabled', true), true); ?> />
                                    <?php _e('Enable lightbox popup for gallery images', 'kilismile'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Gallery Layout', 'kilismile'); ?></th>
                            <td>
                                <select name="kilismile_gallery_layout">
                                    <option value="grid" <?php selected(get_option('kilismile_gallery_layout', 'grid'), 'grid'); ?>><?php _e('Grid Layout', 'kilismile'); ?></option>
                                    <option value="masonry" <?php selected(get_option('kilismile_gallery_layout'), 'masonry'); ?>><?php _e('Masonry Layout', 'kilismile'); ?></option>
                                    <option value="carousel" <?php selected(get_option('kilismile_gallery_layout'), 'carousel'); ?>><?php _e('Carousel Layout', 'kilismile'); ?></option>
                                </select>
                                <p class="description"><?php _e('Choose how to display gallery images', 'kilismile'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php submit_button(__('Save Gallery Settings', 'kilismile'), 'primary', 'kilismile_gallery_save'); ?>
        </form>
    </div>
    
    <style>
    .kilismile-gallery-tabs .nav-tab-wrapper {
        margin-bottom: 20px;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .gallery-item-panel h3 {
        margin-top: 0;
        color: #2271b1;
    }
    .image-upload-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .image-upload-container button {
        margin-right: 10px;
    }
    </style>
    
    <script>
    function switchTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("nav-tab-active");
        }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("nav-tab-active");
    }
    </script>
    <?php
}

// Save gallery options
function kilismile_save_gallery_options() {
    // Save gallery items
    for ($i = 1; $i <= 20; $i++) {
        update_option("kilismile_gallery_image_{$i}", sanitize_text_field($_POST["kilismile_gallery_image_{$i}"] ?? ''));
        update_option("kilismile_gallery_title_{$i}", sanitize_text_field($_POST["kilismile_gallery_title_{$i}"] ?? ''));
        update_option("kilismile_gallery_description_{$i}", sanitize_textarea_field($_POST["kilismile_gallery_description_{$i}"] ?? ''));
        update_option("kilismile_gallery_category_{$i}", sanitize_text_field($_POST["kilismile_gallery_category_{$i}"] ?? 'healthcare'));
        update_option("kilismile_gallery_featured_{$i}", isset($_POST["kilismile_gallery_featured_{$i}"]));
    }
    
    // Save gallery settings
    update_option('kilismile_gallery_items_per_page', absint($_POST['kilismile_gallery_items_per_page'] ?? 12));
    update_option('kilismile_gallery_lightbox_enabled', isset($_POST['kilismile_gallery_lightbox_enabled']));
    update_option('kilismile_gallery_layout', sanitize_text_field($_POST['kilismile_gallery_layout'] ?? 'grid'));
}

// Enqueue admin scripts for media uploader
function kilismile_admin_scripts($hook) {
    if ($hook !== 'kilismile-options_page_kilismile-gallery') {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script('kilismile-admin-gallery', get_template_directory_uri() . '/assets/js/admin-gallery.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'kilismile_admin_scripts');

// Migration function to move gallery data from theme_mod to options
function kilismile_migrate_gallery_data() {
    // Check if migration is needed
    if (get_option('kilismile_gallery_migrated', false)) {
        return;
    }
    
    // First try to migrate from theme_mod
    $migrated_any = false;
    for ($i = 1; $i <= 20; $i++) {
        $image_id = get_theme_mod("kilismile_gallery_image_{$i}");
        $title = get_theme_mod("kilismile_gallery_title_{$i}", '');
        $description = get_theme_mod("kilismile_gallery_description_{$i}", '');
        $category = get_theme_mod("kilismile_gallery_category_{$i}", 'healthcare');
        $featured = get_theme_mod("kilismile_gallery_featured_{$i}", false);
        
        if ($image_id || $title || $description) {
            update_option("kilismile_gallery_image_{$i}", $image_id);
            update_option("kilismile_gallery_title_{$i}", $title);
            update_option("kilismile_gallery_description_{$i}", $description);
            update_option("kilismile_gallery_category_{$i}", $category);
            update_option("kilismile_gallery_featured_{$i}", $featured);
            $migrated_any = true;
        }
    }
    
    // If no data was migrated, add some sample data for demonstration
    if (!$migrated_any) {
        // Add sample gallery items with placeholder images
        $sample_items = array(
            1 => array(
                'title' => 'Healthcare Outreach Program',
                'description' => 'Providing essential healthcare services to underserved communities through our mobile clinic program.',
                'category' => 'healthcare',
                'featured' => true
            ),
            2 => array(
                'title' => 'Community Education Workshop',
                'description' => 'Teaching literacy and vocational skills to empower local community members.',
                'category' => 'education',
                'featured' => false
            ),
            3 => array(
                'title' => 'Volunteer Training Session',
                'description' => 'Preparing dedicated volunteers to make a meaningful impact in their communities.',
                'category' => 'volunteers',
                'featured' => false
            ),
            4 => array(
                'title' => 'Community Health Festival',
                'description' => 'Annual celebration bringing together families and promoting health awareness.',
                'category' => 'events',
                'featured' => true
            ),
            5 => array(
                'title' => 'School Health Program',
                'description' => 'Implementing comprehensive health programs in local schools.',
                'category' => 'education',
                'featured' => false
            ),
            6 => array(
                'title' => 'Water Well Project',
                'description' => 'Providing clean water access to remote communities.',
                'category' => 'community',
                'featured' => false
            )
        );
        
        foreach ($sample_items as $i => $item) {
            // Use placeholder image service or default WordPress placeholder
            update_option("kilismile_gallery_image_{$i}", ''); // Will be handled by fallback
            update_option("kilismile_gallery_title_{$i}", $item['title']);
            update_option("kilismile_gallery_description_{$i}", $item['description']);
            update_option("kilismile_gallery_category_{$i}", $item['category']);
            update_option("kilismile_gallery_featured_{$i}", $item['featured']);
        }
    }
    
    // Migrate gallery settings
    $items_per_page = get_theme_mod('kilismile_gallery_items_per_page', 12);
    $lightbox = get_theme_mod('kilismile_gallery_lightbox', true);
    $layout = get_theme_mod('kilismile_gallery_layout', 'grid');
    
    update_option('kilismile_gallery_items_per_page', $items_per_page);
    update_option('kilismile_gallery_lightbox_enabled', $lightbox);
    update_option('kilismile_gallery_layout', $layout);
    
    // Mark migration as complete
    update_option('kilismile_gallery_migrated', true);
}
add_action('admin_init', 'kilismile_migrate_gallery_data');

// Force migration on next page load (for development)
function kilismile_force_gallery_migration() {
    if (isset($_GET['force_gallery_migration']) && current_user_can('manage_options')) {
        delete_option('kilismile_gallery_migrated');
        kilismile_migrate_gallery_data();
        wp_redirect(remove_query_arg('force_gallery_migration'));
        exit;
    }
}
add_action('init', 'kilismile_force_gallery_migration');

/**
 * Gallery Management Functions
 */

// Get all gallery items from theme options (updated from customizer)
function kilismile_get_gallery_items($category = 'all', $featured_only = false, $limit = -1) {
    $gallery_items = array();
    
    for ($i = 1; $i <= 20; $i++) {
        $image_id = get_option("kilismile_gallery_image_{$i}");
        $title = get_option("kilismile_gallery_title_{$i}", '');
        $description = get_option("kilismile_gallery_description_{$i}", '');
        $item_category = get_option("kilismile_gallery_category_{$i}", 'healthcare');
        $featured = get_option("kilismile_gallery_featured_{$i}", false);
        
        // Skip if no image and no title
        if (empty($image_id) && empty($title)) {
            continue;
        }
        
        // Filter by category
        if ($category !== 'all' && $item_category !== $category) {
            continue;
        }
        
        // Filter by featured
        if ($featured_only && !$featured) {
            continue;
        }
        
        // Get image URLs or use placeholder
        $image_url = '';
        $image_url_full = '';
        $image_alt = '';
        $upload_date = current_time('mysql');
        
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'large');
            $image_url_full = wp_get_attachment_image_url($image_id, 'full');
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            $upload_date = get_post_field('post_date', $image_id);
        }
        
        // Use placeholder if no image found
        if (!$image_url) {
            $category_colors = array(
                'healthcare' => '#4CAF50',
                'education' => '#2196F3', 
                'community' => '#FF9800',
                'events' => '#9C27B0',
                'volunteers' => '#4CAF50',
                'outreach' => '#FF5722',
                'training' => '#607D8B',
                'awareness' => '#E91E63'
            );
            
            $color = $category_colors[$item_category] ?? '#4CAF50';
            $encoded_color = urlencode($color);
            $category_text = ucfirst($item_category);
            
            $image_url = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23f5f5f5' width='400' height='300'/%3E%3Ccircle fill='{$encoded_color}' cx='200' cy='120' r='40'/%3E%3Ctext x='200' y='180' text-anchor='middle' fill='%23666' font-family='Arial, sans-serif' font-size='16' font-weight='bold'%3E{$category_text}%3C/text%3E%3Ctext x='200' y='200' text-anchor='middle' fill='%23999' font-family='Arial, sans-serif' font-size='12'%3EKiliSmile Gallery%3C/text%3E%3C/svg%3E";
            $image_url_full = $image_url;
        }
        
        $gallery_items[] = array(
            'id' => $i,
            'image_id' => $image_id,
            'title' => $title ?: __('Gallery Image', 'kilismile'),
            'description' => $description ?: __('Gallery image description', 'kilismile'),
            'category' => $item_category,
            'featured' => $featured,
            'image_url' => $image_url,
            'image_url_full' => $image_url_full,
            'image_alt' => $image_alt ?: $title,
            'upload_date' => $upload_date,
        );
    }
    
    // Sort by featured first, then by upload date
    usort($gallery_items, function($a, $b) {
        if ($a['featured'] && !$b['featured']) return -1;
        if (!$a['featured'] && $b['featured']) return 1;
        return strtotime($b['upload_date']) - strtotime($a['upload_date']);
    });
    
    // Apply limit
    if ($limit > 0) {
        $gallery_items = array_slice($gallery_items, 0, $limit);
    }
    
    return $gallery_items;
}

// Get gallery categories with counts
function kilismile_get_gallery_categories() {
    $categories = array();
    $all_items = kilismile_get_gallery_items();
    
    foreach ($all_items as $item) {
        $cat = $item['category'];
        if (!isset($categories[$cat])) {
            $categories[$cat] = array(
                'name' => $cat,
                'label' => ucfirst($cat),
                'count' => 0
            );
        }
        $categories[$cat]['count']++;
    }
    
    // Add localized labels
    $labels = array(
        'healthcare'  => __('Healthcare', 'kilismile'),
        'education'   => __('Education', 'kilismile'),
        'community'   => __('Community', 'kilismile'),
        'events'      => __('Events', 'kilismile'),
        'volunteers'  => __('Volunteers', 'kilismile'),
        'outreach'    => __('Outreach', 'kilismile'),
        'training'    => __('Training', 'kilismile'),
        'awareness'   => __('Awareness', 'kilismile'),
    );
    
    foreach ($categories as $key => $category) {
        if (isset($labels[$key])) {
            $categories[$key]['label'] = $labels[$key];
        }
    }
    
    return $categories;
}

// Get gallery statistics
function kilismile_get_gallery_stats() {
    $all_items = kilismile_get_gallery_items();
    $categories = kilismile_get_gallery_categories();
    
    return array(
        'total_images' => count($all_items),
        'total_categories' => count($categories),
        'featured_images' => count(array_filter($all_items, function($item) {
            return $item['featured'];
        })),
        'categories' => $categories,
    );
}

// AJAX handler for loading more gallery items
function kilismile_load_more_gallery() {
    check_ajax_referer('kilismile_gallery_nonce', 'nonce');
    
    $page = intval($_POST['page'] ?? 1);
    $category = sanitize_text_field($_POST['category'] ?? 'all');
    $per_page = get_theme_mod('kilismile_gallery_items_per_page', 12);
    
    $all_items = kilismile_get_gallery_items($category);
    $offset = ($page - 1) * $per_page;
    $items = array_slice($all_items, $offset, $per_page);
    
    $has_more = count($all_items) > ($offset + $per_page);
    
    wp_send_json_success(array(
        'items' => $items,
        'has_more' => $has_more,
        'total' => count($all_items),
    ));
}
add_action('wp_ajax_kilismile_load_more_gallery', 'kilismile_load_more_gallery');
add_action('wp_ajax_nopriv_kilismile_load_more_gallery', 'kilismile_load_more_gallery');

// Shortcode for displaying gallery
function kilismile_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => 'all',
        'limit' => -1,
        'featured_only' => false,
        'layout' => 'grid',
        'columns' => 3,
    ), $atts);
    
    $items = kilismile_get_gallery_items(
        $atts['category'], 
        $atts['featured_only'] === 'true', 
        intval($atts['limit'])
    );
    
    if (empty($items)) {
        return '<p>' . __('No gallery images found.', 'kilismile') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="kilismile-gallery-shortcode" data-layout="<?php echo esc_attr($atts['layout']); ?>" data-columns="<?php echo esc_attr($atts['columns']); ?>">
        <?php foreach ($items as $item) : ?>
            <div class="gallery-item" data-category="<?php echo esc_attr($item['category']); ?>">
                <img src="<?php echo esc_url($item['image_url']); ?>" 
                     alt="<?php echo esc_attr($item['image_alt']); ?>"
                     title="<?php echo esc_attr($item['title']); ?>">
                <div class="gallery-item-info">
                    <h4><?php echo esc_html($item['title']); ?></h4>
                    <p><?php echo esc_html($item['description']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    .kilismile-gallery-shortcode {
        display: grid;
        gap: 20px;
        margin: 20px 0;
    }
    .kilismile-gallery-shortcode[data-layout="grid"] {
        grid-template-columns: repeat(<?php echo esc_attr($atts['columns']); ?>, 1fr);
    }
    .kilismile-gallery-shortcode .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .kilismile-gallery-shortcode .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .kilismile-gallery-shortcode .gallery-item:hover img {
        transform: scale(1.05);
    }
    .kilismile-gallery-shortcode .gallery-item-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .kilismile-gallery-shortcode .gallery-item:hover .gallery-item-info {
        transform: translateY(0);
    }
    @media (max-width: 768px) {
        .kilismile-gallery-shortcode {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .kilismile-gallery-shortcode {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('kilismile_gallery', 'kilismile_gallery_shortcode');

/**
 * Include additional functionality files
 */
$inc_files = array(
    'inc/customizer.php',
    'inc/customizer-contact.php', 
    'inc/customizer-header.php',
    'inc/template-functions.php',
    'inc/donation-functions.php',
    'inc/newsletter-functions.php',
    'inc/contact-functions.php',
    'inc/contact-email-templates.php'
);

/**
 * Partner Database Management
 */

// Create Enhanced Partners Database Table
function kilismile_create_partners_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description text,
        short_description varchar(500),
        logo_url varchar(500),
        logo_id mediumint(9),
        logo_alt_url varchar(500),
        logo_alt_id mediumint(9),
        website varchar(500),
        email varchar(255),
        phone varchar(50),
        contact_person varchar(255),
        category enum('corporate','community','strategic','government','international','academic') DEFAULT 'corporate',
        partnership_type enum('sponsor','collaborator','supporter','vendor','affiliate') DEFAULT 'supporter',
        partnership_level enum('platinum','gold','silver','bronze','basic') DEFAULT 'basic',
        featured tinyint(1) DEFAULT 0,
        priority_level int(11) DEFAULT 5,
        display_order int(11) DEFAULT 0,
        status enum('active','inactive','pending','expired') DEFAULT 'active',
        start_date date,
        end_date date,
        logo_position enum('homepage','footer','sidebar','all','none') DEFAULT 'all',
        social_media_links json,
        partnership_benefits text,
        partnership_value decimal(10,2),
        tags varchar(500),
        notes text,
        click_count int(11) DEFAULT 0,
        last_click timestamp NULL,
        created_by int(11),
        updated_by int(11),
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_category (category),
        KEY idx_partnership_type (partnership_type),
        KEY idx_partnership_level (partnership_level),
        KEY idx_featured (featured),
        KEY idx_priority_level (priority_level),
        KEY idx_status (status),
        KEY idx_display_order (display_order),
        KEY idx_logo_position (logo_position),
        KEY idx_start_date (start_date),
        KEY idx_end_date (end_date),
        FULLTEXT KEY idx_search (name, description, tags)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Create table on theme activation
add_action('after_switch_theme', 'kilismile_create_partners_table');
add_action('after_switch_theme', 'kilismile_create_donation_tables');

/**
 * Create donation database tables on theme activation
 */
function kilismile_create_donation_tables() {
    if (class_exists('KiliSmile_Donation_Database')) {
        $database = new KiliSmile_Donation_Database();
        $database->create_tables();
        
        // Log the table creation
        error_log('KiliSmile: Donation database tables created during theme activation');
    }
}

// Enhanced Core Partner Management Functions
function kilismile_get_partners($args = array()) {
    global $wpdb;
    
    $defaults = array(
        'category' => 'all',
        'partnership_type' => 'all',
        'partnership_level' => 'all',
        'featured_only' => false,
        'limit' => -1,
        'offset' => 0,
        'orderby' => 'priority_level',
        'order' => 'DESC',
        'status' => 'active',
        'search' => '',
        'logo_position' => 'all',
        'include_expired' => false,
        'tags' => ''
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    $where_conditions = array();
    
    // Status filter
    if ($args['status'] !== 'all') {
        $where_conditions[] = $wpdb->prepare("status = %s", $args['status']);
    } else {
        $where_conditions[] = "status IN ('active', 'pending')";
    }
    
    // Category filter
    if ($args['category'] !== 'all') {
        $where_conditions[] = $wpdb->prepare("category = %s", $args['category']);
    }
    
    // Partnership type filter
    if ($args['partnership_type'] !== 'all') {
        $where_conditions[] = $wpdb->prepare("partnership_type = %s", $args['partnership_type']);
    }
    
    // Partnership level filter
    if ($args['partnership_level'] !== 'all') {
        $where_conditions[] = $wpdb->prepare("partnership_level = %s", $args['partnership_level']);
    }
    
    // Featured filter
    if ($args['featured_only']) {
        $where_conditions[] = "featured = 1";
    }
    
    // Logo position filter
    if ($args['logo_position'] !== 'all') {
        $where_conditions[] = $wpdb->prepare("(logo_position = %s OR logo_position = 'all')", $args['logo_position']);
    }
    
    // Exclude expired partnerships unless specifically included
    if (!$args['include_expired']) {
        $where_conditions[] = "(end_date IS NULL OR end_date >= CURDATE())";
    }
    
    // Search functionality
    if (!empty($args['search'])) {
        $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
        $where_conditions[] = $wpdb->prepare(
            "(name LIKE %s OR description LIKE %s OR tags LIKE %s)",
            $search_term, $search_term, $search_term
        );
    }
    
    // Tags filter
    if (!empty($args['tags'])) {
        $tags_array = explode(',', $args['tags']);
        $tag_conditions = array();
        foreach ($tags_array as $tag) {
            $tag = trim($tag);
            $tag_conditions[] = $wpdb->prepare("tags LIKE %s", '%' . $wpdb->esc_like($tag) . '%');
        }
        if (!empty($tag_conditions)) {
            $where_conditions[] = '(' . implode(' OR ', $tag_conditions) . ')';
        }
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Order by clause
    $allowed_orderby = array('priority_level', 'display_order', 'name', 'created_at', 'partnership_level', 'click_count');
    $orderby = in_array($args['orderby'], $allowed_orderby) ? $args['orderby'] : 'priority_level';
    $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Add secondary sorting
    $order_clause = "ORDER BY $orderby $order, display_order ASC, name ASC";
    
    // Limit and offset
    $limit_clause = '';
    if ($args['limit'] > 0) {
        $limit_clause = $wpdb->prepare("LIMIT %d", $args['limit']);
        if ($args['offset'] > 0) {
            $limit_clause = $wpdb->prepare("LIMIT %d, %d", $args['offset'], $args['limit']);
        }
    }
    
    $sql = "SELECT * FROM $table_name $where_clause $order_clause $limit_clause";
    
    return $wpdb->get_results($sql, ARRAY_A);
}

function kilismile_get_partner_by_id($id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
}

function kilismile_save_partner($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    $current_user_id = get_current_user_id();
    
    $partner_data = array(
        'name' => sanitize_text_field($data['name']),
        'description' => wp_kses_post($data['description']),
        'short_description' => sanitize_textarea_field($data['short_description']),
        'website' => esc_url_raw($data['website']),
        'email' => sanitize_email($data['email']),
        'phone' => sanitize_text_field($data['phone']),
        'contact_person' => sanitize_text_field($data['contact_person']),
        'category' => sanitize_text_field($data['category']),
        'partnership_type' => sanitize_text_field($data['partnership_type']),
        'partnership_level' => sanitize_text_field($data['partnership_level']),
        'featured' => isset($data['featured']) ? 1 : 0,
        'priority_level' => intval($data['priority_level']),
        'display_order' => intval($data['display_order']),
        'status' => sanitize_text_field($data['status']),
        'start_date' => !empty($data['start_date']) ? sanitize_text_field($data['start_date']) : null,
        'end_date' => !empty($data['end_date']) ? sanitize_text_field($data['end_date']) : null,
        'logo_position' => sanitize_text_field($data['logo_position']),
        'partnership_benefits' => wp_kses_post($data['partnership_benefits']),
        'partnership_value' => !empty($data['partnership_value']) ? floatval($data['partnership_value']) : 0,
        'tags' => sanitize_text_field($data['tags']),
        'notes' => wp_kses_post($data['notes']),
        'created_by' => $current_user_id
    );
    
    // Handle logo uploads
    if (!empty($data['logo_url'])) {
        $partner_data['logo_url'] = esc_url_raw($data['logo_url']);
    }
    
    if (!empty($data['logo_id'])) {
        $partner_data['logo_id'] = intval($data['logo_id']);
    }
    
    if (!empty($data['logo_alt_url'])) {
        $partner_data['logo_alt_url'] = esc_url_raw($data['logo_alt_url']);
    }
    
    if (!empty($data['logo_alt_id'])) {
        $partner_data['logo_alt_id'] = intval($data['logo_alt_id']);
    }
    
    // Handle social media links
    if (!empty($data['social_media_links'])) {
        $social_links = array();
        foreach ($data['social_media_links'] as $platform => $url) {
            if (!empty($url)) {
                $social_links[$platform] = esc_url_raw($url);
            }
        }
        $partner_data['social_media_links'] = json_encode($social_links);
    }
    
    $result = $wpdb->insert($table_name, $partner_data);
    
    if ($result !== false) {
        return $wpdb->insert_id;
    }
    
    return false;
}

function kilismile_update_partner($id, $data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    $current_user_id = get_current_user_id();
    
    $partner_data = array(
        'name' => sanitize_text_field($data['name']),
        'description' => wp_kses_post($data['description']),
        'short_description' => sanitize_textarea_field($data['short_description']),
        'website' => esc_url_raw($data['website']),
        'email' => sanitize_email($data['email']),
        'phone' => sanitize_text_field($data['phone']),
        'contact_person' => sanitize_text_field($data['contact_person']),
        'category' => sanitize_text_field($data['category']),
        'partnership_type' => sanitize_text_field($data['partnership_type']),
        'partnership_level' => sanitize_text_field($data['partnership_level']),
        'featured' => isset($data['featured']) ? 1 : 0,
        'priority_level' => intval($data['priority_level']),
        'display_order' => intval($data['display_order']),
        'status' => sanitize_text_field($data['status']),
        'start_date' => !empty($data['start_date']) ? sanitize_text_field($data['start_date']) : null,
        'end_date' => !empty($data['end_date']) ? sanitize_text_field($data['end_date']) : null,
        'logo_position' => sanitize_text_field($data['logo_position']),
        'partnership_benefits' => wp_kses_post($data['partnership_benefits']),
        'partnership_value' => !empty($data['partnership_value']) ? floatval($data['partnership_value']) : 0,
        'tags' => sanitize_text_field($data['tags']),
        'notes' => wp_kses_post($data['notes']),
        'updated_by' => $current_user_id
    );
    
    // Handle logo uploads
    if (isset($data['logo_url'])) {
        $partner_data['logo_url'] = esc_url_raw($data['logo_url']);
    }
    
    if (isset($data['logo_id'])) {
        $partner_data['logo_id'] = intval($data['logo_id']);
    }
    
    if (isset($data['logo_alt_url'])) {
        $partner_data['logo_alt_url'] = esc_url_raw($data['logo_alt_url']);
    }
    
    if (isset($data['logo_alt_id'])) {
        $partner_data['logo_alt_id'] = intval($data['logo_alt_id']);
    }
    
    // Handle social media links
    if (isset($data['social_media_links'])) {
        $social_links = array();
        foreach ($data['social_media_links'] as $platform => $url) {
            if (!empty($url)) {
                $social_links[$platform] = esc_url_raw($url);
            }
        }
        $partner_data['social_media_links'] = json_encode($social_links);
    }
    
    return $wpdb->update($table_name, $partner_data, array('id' => intval($id)));
}

function kilismile_delete_partner($id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    // Get partner data to clean up logos if needed
    $partner = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    
    if ($partner) {
        // Clean up logo attachments
        if (!empty($partner['logo_id'])) {
            wp_delete_attachment($partner['logo_id'], true);
        }
        if (!empty($partner['logo_alt_id'])) {
            wp_delete_attachment($partner['logo_alt_id'], true);
        }
    }
    
    return $wpdb->delete($table_name, array('id' => intval($id)));
}

// Track partner logo clicks
function kilismile_track_partner_click($partner_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET click_count = click_count + 1, last_click = NOW() WHERE id = %d",
        $partner_id
    ));
}

// Get partner statistics
function kilismile_get_partner_stats() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    $stats = array();
    
    // Total partners
    $stats['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active'");
    
    // By category
    $categories = $wpdb->get_results("SELECT category, COUNT(*) as count FROM $table_name WHERE status = 'active' GROUP BY category", ARRAY_A);
    $stats['by_category'] = array();
    foreach ($categories as $cat) {
        $stats['by_category'][$cat['category']] = $cat['count'];
    }
    
    // By partnership level
    $levels = $wpdb->get_results("SELECT partnership_level, COUNT(*) as count FROM $table_name WHERE status = 'active' GROUP BY partnership_level", ARRAY_A);
    $stats['by_level'] = array();
    foreach ($levels as $level) {
        $stats['by_level'][$level['partnership_level']] = $level['count'];
    }
    
    // Featured partners
    $stats['featured'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active' AND featured = 1");
    
    // Most clicked
    $stats['most_clicked'] = $wpdb->get_results("SELECT name, click_count FROM $table_name WHERE status = 'active' ORDER BY click_count DESC LIMIT 5", ARRAY_A);
    
    // Expiring soon (within 30 days)
    $stats['expiring_soon'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active' AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
    
    return $stats;
}

// Advanced AJAX Handlers
add_action('wp_ajax_kilismile_partner_bulk_action', 'kilismile_partner_bulk_action_handler');
function kilismile_partner_bulk_action_handler() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $action = sanitize_text_field($_POST['bulk_action']);
    $partner_ids = array_map('intval', $_POST['partner_ids']);
    
    $success_count = 0;
    
    foreach ($partner_ids as $partner_id) {
        switch ($action) {
            case 'activate':
                if (kilismile_update_partner($partner_id, array('status' => 'active'))) {
                    $success_count++;
                }
                break;
            case 'deactivate':
                if (kilismile_update_partner($partner_id, array('status' => 'inactive'))) {
                    $success_count++;
                }
                break;
            case 'feature':
                if (kilismile_update_partner($partner_id, array('featured' => 1))) {
                    $success_count++;
                }
                break;
            case 'unfeature':
                if (kilismile_update_partner($partner_id, array('featured' => 0))) {
                    $success_count++;
                }
                break;
            case 'delete':
                if (kilismile_delete_partner($partner_id)) {
                    $success_count++;
                }
                break;
        }
    }
    
    wp_send_json_success(array(
        'message' => sprintf('%d partners updated successfully', $success_count),
        'count' => $success_count
    ));
}

add_action('wp_ajax_kilismile_partner_search', 'kilismile_partner_search_handler');
function kilismile_partner_search_handler() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search']);
    $category = sanitize_text_field($_POST['category']);
    $partnership_level = sanitize_text_field($_POST['partnership_level']);
    
    $args = array(
        'search' => $search_term,
        'category' => $category,
        'partnership_level' => $partnership_level,
        'limit' => 20
    );
    
    $partners = kilismile_get_partners($args);
    
    wp_send_json_success(array(
        'partners' => $partners,
        'count' => count($partners)
    ));
}

add_action('wp_ajax_kilismile_partner_stats', 'kilismile_partner_stats_handler');
function kilismile_partner_stats_handler() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $stats = kilismile_get_partner_stats();
    
    wp_send_json_success($stats);
}

add_action('wp_ajax_kilismile_update_partner_order', 'kilismile_update_partner_order_handler');
function kilismile_update_partner_order_handler() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $partner_orders = $_POST['partner_orders'];
    
    foreach ($partner_orders as $partner_data) {
        $partner_id = intval($partner_data['id']);
        $order = intval($partner_data['order']);
        
        kilismile_update_partner($partner_id, array('display_order' => $order));
    }
    
    wp_send_json_success(array('message' => 'Partner order updated successfully'));
}

// Track partner clicks (frontend AJAX)
add_action('wp_ajax_kilismile_track_partner_click', 'kilismile_track_partner_click_handler');
add_action('wp_ajax_nopriv_kilismile_track_partner_click', 'kilismile_track_partner_click_handler');
function kilismile_track_partner_click_handler() {
    $partner_id = intval($_POST['partner_id']);
    
    if ($partner_id > 0) {
        kilismile_track_partner_click($partner_id);
        wp_send_json_success();
    }
    
    wp_send_json_error();
}

// Enhanced Logo Upload Handler with Multiple Image Support
add_action('wp_ajax_kilismile_partner_logo_upload', 'kilismile_enhanced_partner_logo_upload_handler');
function kilismile_enhanced_partner_logo_upload_handler() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    
    $logo_type = sanitize_text_field($_POST['logo_type']); // 'primary' or 'alternative'
    
    $uploaded_file = $_FILES['logo'];
    
    // Validate file type
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp');
    if (!in_array($uploaded_file['type'], $allowed_types)) {
        wp_send_json_error('Invalid file type. Please upload JPG, PNG, GIF, SVG, or WebP files.');
    }
    
    // Handle the upload
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
    
    if ($movefile && !isset($movefile['error'])) {
        // Create attachment
        $attachment = array(
            'post_mime_type' => $movefile['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        
        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
        
        if (!is_wp_error($attach_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            
            // Generate different sizes for responsive display
            $image_sizes = array(
                'thumbnail' => wp_get_attachment_image_url($attach_id, 'thumbnail'),
                'medium' => wp_get_attachment_image_url($attach_id, 'medium'),
                'large' => wp_get_attachment_image_url($attach_id, 'large'),
                'full' => wp_get_attachment_image_url($attach_id, 'full')
            );
            
            wp_send_json_success(array(
                'attachment_id' => $attach_id,
                'url' => $movefile['url'],
                'sizes' => $image_sizes,
                'logo_type' => $logo_type
            ));
        }
    }
    
    wp_send_json_error('Upload failed: ' . $movefile['error']);
}

// Enhanced Partner Display Functions
function display_enhanced_partner_showcase($layout = 'premium_grid', $args = array()) {
    $defaults = array(
        'category' => 'all',
        'partnership_level' => 'all',
        'featured_only' => false,
        'limit' => -1,
        'show_descriptions' => true,
        'show_social_links' => true,
        'enable_animations' => true,
        'logo_position' => 'homepage'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $partners = kilismile_get_partners($args);
    
    if (empty($partners)) {
        return '<div class="no-partners-message" style="text-align: center; padding: 60px 20px; background: rgba(76, 175, 80, 0.05); border-radius: 12px; border: 2px dashed rgba(76, 175, 80, 0.3);"><i class="fas fa-handshake" style="font-size: 3rem; color: rgba(76, 175, 80, 0.4); margin-bottom: 20px;"></i><h3 style="color: var(--dark-green); margin-bottom: 10px;">No Partners Found</h3><p style="color: var(--text-secondary);">We\'re actively building partnerships to expand our impact.</p></div>';
    }
    
    switch ($layout) {
        case 'premium_grid':
            return kilismile_render_premium_partner_grid($partners, $args);
        case 'logo_showcase':
            return kilismile_render_logo_showcase($partners, $args);
        case 'interactive_cards':
            return kilismile_render_interactive_partner_cards($partners, $args);
        case 'timeline':
            return kilismile_render_partner_timeline($partners, $args);
        case 'masonry':
            return kilismile_render_partner_masonry($partners, $args);
        default:
            return kilismile_render_premium_partner_grid($partners, $args);
    }
}

function kilismile_render_premium_partner_grid($partners, $args) {
    $animation_class = $args['enable_animations'] ? 'aos-animate' : '';
    
    ob_start();
    ?>
    <div class="premium-partner-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 40px; margin: 40px 0;">
        <?php foreach ($partners as $index => $partner): 
            $level_colors = array(
                'platinum' => array('primary' => '#e5e5e5', 'secondary' => '#c0c0c0'),
                'gold' => array('primary' => '#ffd700', 'secondary' => '#ffed4e'),
                'silver' => array('primary' => '#c0c0c0', 'secondary' => '#e5e5e5'),
                'bronze' => array('primary' => '#cd7f32', 'secondary' => '#d4956b'),
                'basic' => array('primary' => '#4CAF50', 'secondary' => '#66bb6a')
            );
            
            $colors = $level_colors[$partner['partnership_level']] ?? $level_colors['basic'];
            $logo_url = !empty($partner['logo_url']) ? $partner['logo_url'] : '';
            $has_logo = !empty($logo_url);
            $social_links = !empty($partner['social_media_links']) ? json_decode($partner['social_media_links'], true) : array();
        ?>
        <div class="premium-partner-card <?php echo $animation_class; ?>" 
             data-aos="fade-up" 
             data-aos-delay="<?php echo $index * 100; ?>"
             onclick="trackPartnerClick(<?php echo $partner['id']; ?>)"
             style="background: linear-gradient(145deg, #ffffff, #f8f9fa); 
                    border-radius: 24px; 
                    overflow: hidden; 
                    box-shadow: 0 20px 60px rgba(0,0,0,0.08); 
                    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
                    position: relative;
                    cursor: pointer;
                    border: 1px solid rgba(255,255,255,0.8);"
             onmouseover="this.style.transform='translateY(-15px) scale(1.02)'; this.style.boxShadow='0 30px 80px rgba(0,0,0,0.15)'"
             onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 20px 60px rgba(0,0,0,0.08)'">
            
            <!-- Partnership Level Badge -->
            <div style="position: absolute; top: 20px; right: 20px; background: linear-gradient(135deg, <?php echo $colors['primary']; ?>, <?php echo $colors['secondary']; ?>); 
                        color: <?php echo $partner['partnership_level'] === 'platinum' || $partner['partnership_level'] === 'silver' ? '#333' : '#fff'; ?>; 
                        padding: 8px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; 
                        letter-spacing: 1px; z-index: 10; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <?php echo esc_html(ucfirst($partner['partnership_level'])); ?>
            </div>
            
            <!-- Background Pattern -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                        background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"<?php echo urlencode($colors['primary']); ?>\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>'); 
                        opacity: 0.3;"></div>
            
            <!-- Logo Section -->
            <div style="position: relative; padding: 40px 30px 20px; text-align: center; z-index: 2;">
                <?php if ($has_logo): ?>
                    <div style="background: white; border-radius: 20px; padding: 30px; margin-bottom: 25px; 
                                box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: 3px solid #f8f9fa; 
                                min-height: 160px; display: flex; align-items: center; justify-content: center;
                                transition: all 0.3s ease;">
                        <img src="<?php echo esc_url($logo_url); ?>" 
                             alt="<?php echo esc_attr($partner['name']); ?>" 
                             style="max-width: 200px; max-height: 140px; object-fit: contain; 
                                    filter: drop-shadow(0 4px 20px rgba(0,0,0,0.1)); 
                                    transition: all 0.4s ease;"
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </div>
                <?php else: ?>
                    <div style="background: linear-gradient(135deg, <?php echo $colors['primary']; ?>, <?php echo $colors['secondary']; ?>); 
                                border-radius: 20px; padding: 40px; margin-bottom: 25px; min-height: 160px; 
                                display: flex; align-items: center; justify-content: center; color: <?php echo $partner['partnership_level'] === 'platinum' || $partner['partnership_level'] === 'silver' ? '#333' : '#fff'; ?>; 
                                box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
                        <div style="text-align: center;">
                            <i class="fas fa-building" style="font-size: 4rem; margin-bottom: 15px; opacity: 0.9;"></i>
                            <div style="font-size: 1.3rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">
                                <?php echo esc_html(substr($partner['name'], 0, 12)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Partner Name -->
                <h3 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.6rem; font-weight: 700; line-height: 1.3;">
                    <?php echo esc_html($partner['name']); ?>
                </h3>
                
                <!-- Partnership Type -->
                <div style="margin-bottom: 20px;">
                    <span style="background: rgba(76, 175, 80, 0.1); color: var(--primary-green); 
                                 padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; 
                                 text-transform: uppercase; letter-spacing: 0.5px;">
                        <?php echo esc_html(str_replace('_', ' ', $partner['partnership_type'])); ?>
                    </span>
                </div>
            </div>
            
            <!-- Content Section -->
            <div style="padding: 0 30px 30px; position: relative; z-index: 2;">
                <?php if ($args['show_descriptions'] && !empty($partner['short_description'])): ?>
                <p style="color: #7f8c8d; font-size: 1rem; line-height: 1.6; margin-bottom: 25px; text-align: center;">
                    <?php echo esc_html($partner['short_description']); ?>
                </p>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px; justify-content: center; align-items: center; margin-bottom: 20px;">
                    <?php if (!empty($partner['website'])): ?>
                    <a href="<?php echo esc_url($partner['website']); ?>" 
                       target="_blank" 
                       rel="noopener"
                       style="background: linear-gradient(135deg, var(--primary-green), var(--accent-green)); 
                              color: white; padding: 12px 20px; border-radius: 25px; text-decoration: none; 
                              font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; 
                              gap: 8px; transition: all 0.3s ease; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(76, 175, 80, 0.4)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(76, 175, 80, 0.3)'">
                        <i class="fas fa-external-link-alt"></i>
                        Visit Website
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($partner['email'])): ?>
                    <a href="mailto:<?php echo esc_attr($partner['email']); ?>" 
                       style="background: rgba(74, 144, 226, 0.1); color: #4a90e2; padding: 12px 16px; 
                              border-radius: 50%; text-decoration: none; transition: all 0.3s ease; 
                              display: inline-flex; align-items: center; justify-content: center;"
                       onmouseover="this.style.background='#4a90e2'; this.style.color='white'"
                       onmouseout="this.style.background='rgba(74, 144, 226, 0.1)'; this.style.color='#4a90e2'">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Social Media Links -->
                <?php if ($args['show_social_links'] && !empty($social_links)): ?>
                <div style="display: flex; gap: 8px; justify-content: center; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.05);">
                    <?php 
                    $social_icons = array(
                        'facebook' => 'fab fa-facebook-f',
                        'twitter' => 'fab fa-twitter',
                        'linkedin' => 'fab fa-linkedin-in',
                        'instagram' => 'fab fa-instagram',
                        'youtube' => 'fab fa-youtube'
                    );
                    
                    foreach ($social_links as $platform => $url): 
                        if (!empty($url) && isset($social_icons[$platform])):
                    ?>
                    <a href="<?php echo esc_url($url); ?>" 
                       target="_blank" 
                       rel="noopener"
                       style="width: 36px; height: 36px; background: rgba(0,0,0,0.05); border-radius: 50%; 
                              display: flex; align-items: center; justify-content: center; color: #7f8c8d; 
                              text-decoration: none; transition: all 0.3s ease; font-size: 0.9rem;"
                       onmouseover="this.style.background='var(--primary-green)'; this.style.color='white'; this.style.transform='scale(1.1)'"
                       onmouseout="this.style.background='rgba(0,0,0,0.05)'; this.style.color='#7f8c8d'; this.style.transform='scale(1)'">
                        <i class="<?php echo esc_attr($social_icons[$platform]); ?>"></i>
                    </a>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    @media (max-width: 768px) {
        .premium-partner-grid {
            grid-template-columns: 1fr !important;
            gap: 30px !important;
        }
        
        .premium-partner-card {
            margin: 0 10px;
        }
    }
    </style>
    
    <script>
    function trackPartnerClick(partnerId) {
        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'kilismile_track_partner_click',
                    partner_id: partnerId
                }
            });
        }
    }
    </script>
    <?php
    
    return ob_get_clean();
}

function kilismile_render_logo_showcase($partners, $args) {
    ob_start();
    ?>
    <div class="logo-showcase-container" style="padding: 60px 0; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 15px; font-weight: 700;">
                Our Trusted Partners
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Building stronger communities through meaningful partnerships and collaborations
            </p>
        </div>
        
        <div class="logo-carousel" style="overflow: hidden; position: relative;">
            <div class="logo-track" style="display: flex; animation: logoSlide 30s linear infinite; gap: 60px; align-items: center;">
                <?php foreach (array_merge($partners, $partners) as $partner): // Duplicate for smooth loop ?>
                    <div class="partner-logo-item" style="flex-shrink: 0; padding: 20px 30px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s ease; min-width: 200px; text-align: center;">
                        <?php if (!empty($partner['logo_url'])): ?>
                            <img src="<?php echo esc_url($partner['logo_url']); ?>" 
                                 alt="<?php echo esc_attr($partner['name']); ?>" 
                                 style="max-width: 150px; max-height: 80px; object-fit: contain; filter: grayscale(100%); transition: all 0.3s ease;"
                                 onmouseover="this.style.filter='grayscale(0)'; this.parentElement.style.transform='scale(1.05)'"
                                 onmouseout="this.style.filter='grayscale(100%)'; this.parentElement.style.transform='scale(1)'">
                        <?php else: ?>
                            <div style="padding: 20px; color: var(--primary-green); font-weight: 600;">
                                <?php echo esc_html($partner['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <style>
    @keyframes logoSlide {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    .logo-carousel:hover .logo-track {
        animation-play-state: paused;
    }
    </style>
    <?php
    
    return ob_get_clean();
}

/**
 * Partner Showcase Functions
 */

// Register Partner Showcase Customizer Options
add_action('customize_register', 'kilismile_partner_showcase_customizer');
function kilismile_partner_showcase_customizer($wp_customize) {
    // Partner Showcase Section
    $wp_customize->add_section('kilismile_partner_showcase', array(
        'title'    => __('Partner Showcase', 'kilismile'),
        'priority' => 35,
    ));

    // Show Partner Showcase
    $wp_customize->add_setting('kilismile_show_partner_showcase', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kilismile_show_partner_showcase', array(
        'label'   => __('Show Partner Showcase', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'checkbox',
    ));

    // Partner Showcase Title
    $wp_customize->add_setting('kilismile_partner_showcase_title', array(
        'default'           => __('Our Trusted Partners', 'kilismile'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kilismile_partner_showcase_title', array(
        'label'   => __('Partner Showcase Title', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'text',
    ));

    // Partner Showcase Subtitle
    $wp_customize->add_setting('kilismile_partner_showcase_subtitle', array(
        'default'           => __('Working together to create meaningful impact in global health education', 'kilismile'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kilismile_partner_showcase_subtitle', array(
        'label'   => __('Partner Showcase Subtitle', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'textarea',
    ));

    // Partner Showcase Layout
    $wp_customize->add_setting('kilismile_partner_showcase_layout', array(
        'default'           => 'grid',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kilismile_partner_showcase_layout', array(
        'label'   => __('Partner Showcase Layout', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'select',
        'choices' => array(
            'grid'     => __('Grid Layout', 'kilismile'),
            'carousel' => __('Carousel Layout', 'kilismile'),
            'logos'    => __('Logo Grid', 'kilismile'),
            'featured' => __('Featured Partners', 'kilismile'),
        ),
    ));

    // Show Partner Categories
    $wp_customize->add_setting('kilismile_show_partner_categories', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kilismile_show_partner_categories', array(
        'label'   => __('Show Partner Categories', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'checkbox',
    ));

    // Partner Categories to Display
    $wp_customize->add_setting('kilismile_partner_categories_display', array(
        'default'           => 'all',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kilismile_partner_categories_display', array(
        'label'   => __('Partner Categories to Display', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
        'type'    => 'select',
        'choices' => array(
            'all'           => __('All Partners', 'kilismile'),
            'corporate'     => __('Corporate Partners Only', 'kilismile'),
            'sponsors'      => __('Sponsors Only', 'kilismile'),
            'strategic'     => __('Strategic Partners Only', 'kilismile'),
            'community'     => __('Community Partners Only', 'kilismile'),
        ),
    ));

    // Partner Background Color
    $wp_customize->add_setting('kilismile_partner_showcase_bg', array(
        'default'           => '#f8f9fa',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kilismile_partner_showcase_bg', array(
        'label'   => __('Partner Showcase Background', 'kilismile'),
        'section' => 'kilismile_partner_showcase',
    )));
}

// Get Partner Showcase Data
function kilismile_get_partner_showcase_data() {
    $corporate_partners = get_option('kilismile_corporate_partners', '');
    $community_partners = get_option('kilismile_community_partners', '');
    $strategic_partners = get_option('kilismile_strategic_partners', '');
    
    $all_partners = array();
    
    // Process corporate partners
    if ($corporate_partners) {
        $partners = array_filter(array_map('trim', explode("\n", $corporate_partners)));
        foreach ($partners as $partner) {
            $parts = explode('|', $partner, 4);
            if (count($parts) >= 3) {
                $all_partners[] = array(
                    'name' => trim($parts[0]),
                    'level' => trim($parts[1]),
                    'icon' => trim($parts[2]),
                    'logo' => isset($parts[3]) ? trim($parts[3]) : '',
                    'category' => 'corporate',
                    'type' => 'Corporate Partner'
                );
            }
        }
    }
    
    // Process community partners
    if ($community_partners) {
        $partners = array_filter(array_map('trim', explode("\n", $community_partners)));
        foreach ($partners as $partner) {
            $parts = explode('|', $partner, 4);
            if (count($parts) >= 2) {
                $all_partners[] = array(
                    'name' => trim($parts[0]),
                    'level' => isset($parts[1]) ? trim($parts[1]) : 'Community Partner',
                    'icon' => isset($parts[2]) ? trim($parts[2]) : 'fas fa-hands-helping',
                    'logo' => isset($parts[3]) ? trim($parts[3]) : '',
                    'category' => 'community',
                    'type' => 'Community Partner'
                );
            }
        }
    }
    
    // Process strategic partners
    if ($strategic_partners) {
        $partners = array_filter(array_map('trim', explode("\n", $strategic_partners)));
        foreach ($partners as $partner) {
            $parts = explode('|', $partner, 4);
            if (count($parts) >= 2) {
                $all_partners[] = array(
                    'name' => trim($parts[0]),
                    'level' => isset($parts[1]) ? trim($parts[1]) : 'Strategic Partner',
                    'icon' => isset($parts[2]) ? trim($parts[2]) : 'fas fa-handshake',
                    'logo' => isset($parts[3]) ? trim($parts[3]) : '',
                    'category' => 'strategic',
                    'type' => 'Strategic Partner'
                );
            }
        }
    }
    
    return $all_partners;
}

// Render Partner Showcase
function kilismile_render_partner_showcase($layout = 'grid', $category = 'all', $limit = -1) {
    if (!get_theme_mod('kilismile_show_partner_showcase', true)) {
        return '';
    }
    
    $partners = kilismile_get_partner_showcase_data();
    
    // Filter by category
    if ($category !== 'all') {
        $partners = array_filter($partners, function($partner) use ($category) {
            return $partner['category'] === $category;
        });
    }
    
    // Limit partners if specified
    if ($limit > 0) {
        $partners = array_slice($partners, 0, $limit);
    }
    
    if (empty($partners)) {
        return '<div style="text-align: center; padding: 40px; color: #7f8c8d; font-style: italic;">No partners to display.</div>';
    }
    
    $title = get_theme_mod('kilismile_partner_showcase_title', __('Our Trusted Partners', 'kilismile'));
    $subtitle = get_theme_mod('kilismile_partner_showcase_subtitle', __('Working together to create meaningful impact in global health education', 'kilismile'));
    $bg_color = get_theme_mod('kilismile_partner_showcase_bg', '#f8f9fa');
    
    ob_start();
    ?>
    <section class="partner-showcase" style="padding: 80px 0; background: <?php echo esc_attr($bg_color); ?>;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <?php if ($title || $subtitle): ?>
            <div style="text-align: center; margin-bottom: 60px;">
                <?php if ($title): ?>
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php echo esc_html($title); ?>
                </h2>
                <?php endif; ?>
                <?php if ($subtitle): ?>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php echo esc_html($subtitle); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php
            switch ($layout) {
                case 'carousel':
                    kilismile_render_partner_carousel($partners);
                    break;
                case 'logos':
                    kilismile_render_partner_logos($partners);
                    break;
                case 'featured':
                    kilismile_render_featured_partners($partners);
                    break;
                default:
                    kilismile_render_partner_grid($partners);
                    break;
            }
            ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Render Partner Grid Layout - Logo Focused
function kilismile_render_partner_grid($partners) {
    $colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;">
        <?php foreach ($partners as $index => $partner): 
            $color = $colors[$index % count($colors)];
            $sponsor_colors = [
                'Gold Sponsor' => '#FFD700',
                'Platinum Sponsor' => '#E5E4E2',
                'Silver Sponsor' => '#C0C0C0',
                'Bronze Sponsor' => '#CD7F32'
            ];
            $level_color = isset($sponsor_colors[$partner['level']]) ? $sponsor_colors[$partner['level']] : $color;
        ?>
        <div class="partner-card" style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); transition: all 0.3s ease; text-align: center; position: relative; overflow: hidden; border: 1px solid #f0f3f6;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'">
            
            <!-- Large Logo Section -->
            <?php if ($partner['logo']): ?>
                <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px; padding: 40px 20px; margin-bottom: 25px; height: 140px; display: flex; align-items: center; justify-content: center; border: 2px solid #f0f3f6; position: relative;">
                    <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 90%; max-height: 120px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1)); transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: <?php echo esc_attr($level_color); ?>; border-radius: 15px 15px 0 0;"></div>
                </div>
            <?php else: ?>
                <div style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); border-radius: 15px; padding: 40px 20px; margin-bottom: 25px; height: 140px; display: flex; align-items: center; justify-content: center; color: white; position: relative;">
                    <div style="text-align: center;">
                        <i class="<?php echo esc_attr($partner['icon']); ?>" style="font-size: 3.5rem; margin-bottom: 10px; opacity: 0.9;" aria-hidden="true"></i>
                        <div style="font-size: 1.1rem; font-weight: 600; opacity: 0.8;"><?php echo esc_html(strtoupper(substr($partner['name'], 0, 3))); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Partner Info -->
            <h4 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.4rem; font-weight: 700; line-height: 1.3;">
                <?php echo esc_html($partner['name']); ?>
            </h4>
            
            <!-- Partner Level Badge -->
            <div style="margin-bottom: 15px;">
                <span style="background: <?php echo esc_attr($level_color); ?>; color: <?php echo in_array($partner['level'], ['Silver Sponsor', 'Platinum Sponsor']) ? '#2c3e50' : 'white'; ?>; padding: 8px 18px; border-radius: 25px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <?php echo esc_html($partner['level']); ?>
                </span>
            </div>
            
            <!-- Partner Type -->
            <p style="color: #7f8c8d; font-size: 0.95rem; margin: 0; font-weight: 500;">
                <?php echo esc_html($partner['type']); ?>
            </p>
            
            <!-- Hover Effect Overlay -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, <?php echo esc_attr($level_color); ?>22, <?php echo esc_attr($level_color); ?>11); opacity: 0; transition: opacity 0.3s ease; border-radius: 20px; pointer-events: none;" class="partner-overlay"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    .partner-card:hover .partner-overlay {
        opacity: 1 !important;
    }
    </style>
    <?php
}

// Render Partner Carousel Layout - Logo Enhanced
function kilismile_render_partner_carousel($partners) {
    $colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
    ?>
    <div class="partner-carousel-container" style="position: relative; overflow: hidden; padding: 20px 0;">
        <div class="partner-carousel" style="display: flex; gap: 30px; overflow-x: auto; scroll-behavior: smooth; padding: 25px 0; scrollbar-width: thin;">
            <?php foreach ($partners as $index => $partner): 
                $color = $colors[$index % count($colors)];
            ?>
            <div class="partner-slide" style="flex: 0 0 280px; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 30px 25px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); text-align: center; transition: all 0.3s ease; position: relative; overflow: hidden; border: 1px solid #f0f3f6; min-height: 200px; display: flex; flex-direction: column; justify-content: center;" onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'">
                
                <!-- Decorative Top Bar -->
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>88); border-radius: 20px 20px 0 0;"></div>
                
                <!-- Background Pattern -->
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"<?php echo urlencode($color); ?>\" opacity=\"0.06\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>'); opacity: 0.5;"></div>
                
                <!-- Logo Section -->
                <?php if ($partner['logo']): ?>
                    <div style="position: relative; z-index: 2; background: white; border-radius: 15px; padding: 25px 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 2px solid #f8f9fa;">
                        <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 120px; max-height: 70px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.1)); transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'; this.style.filter='drop-shadow(0 3px 8px rgba(0,0,0,0.15))'" onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 2px 6px rgba(0,0,0,0.1))'">
                    </div>
                <?php else: ?>
                    <div style="position: relative; z-index: 2; margin-bottom: 20px;">
                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; box-shadow: 0 6px 18px <?php echo esc_attr($color); ?>33;">
                            <i class="<?php echo esc_attr($partner['icon']); ?>" style="font-size: 1.8rem;" aria-hidden="true"></i>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Partner Info -->
                <div style="position: relative; z-index: 2;">
                    <h5 style="color: #2c3e50; margin-bottom: 12px; font-size: 1.2rem; font-weight: 700; line-height: 1.3;">
                        <?php echo esc_html($partner['name']); ?>
                    </h5>
                    
                    <span style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 3px 10px <?php echo esc_attr($color); ?>33;">
                        <?php echo esc_html($partner['level']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
    .partner-carousel::-webkit-scrollbar {
        height: 10px;
    }
    .partner-carousel::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }
    .partner-carousel::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #3498db, #27ae60);
        border-radius: 6px;
    }
    .partner-carousel::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #2980b9, #219a52);
    }
    
    @media (max-width: 768px) {
        .partner-slide {
            flex: 0 0 250px !important;
            min-height: 180px !important;
            padding: 25px 20px !important;
        }
        
        .partner-slide img {
            max-width: 100px !important;
            max-height: 60px !important;
        }
    }
    </style>
    <?php
}

// Render Partner Logos Layout
// Render Partner Logos Layout - Enhanced Visual Focus
function kilismile_render_partner_logos($partners) {
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; align-items: center; padding: 30px 0;">
        <?php foreach ($partners as $partner): ?>
        <div class="partner-logo" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 35px 25px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); text-align: center; transition: all 0.4s ease; position: relative; overflow: hidden; border: 1px solid #f0f3f6; min-height: 160px; display: flex; flex-direction: column; justify-content: center; align-items: center;" onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'">
            
            <!-- Background Pattern -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1.5\" fill=\"rgba(52,152,219,0.05)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>'); opacity: 0.5;"></div>
            
            <?php if ($partner['logo']): ?>
                <div style="position: relative; z-index: 2; margin-bottom: 20px;">
                    <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 140px; max-height: 80px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1)); transition: all 0.3s ease;" onmouseover="this.style.filter='drop-shadow(0 4px 12px rgba(0,0,0,0.2)) brightness(1.1)'" onmouseout="this.style.filter='drop-shadow(0 2px 8px rgba(0,0,0,0.1)) brightness(1)'">
                </div>
                <div style="position: relative; z-index: 2;">
                    <h5 style="color: #2c3e50; font-size: 1.1rem; font-weight: 700; margin: 0; line-height: 1.3;"><?php echo esc_html($partner['name']); ?></h5>
                </div>
            <?php else: ?>
                <div style="position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                        <i class="<?php echo esc_attr($partner['icon']); ?>" style="font-size: 2.2rem; color: white;" aria-hidden="true"></i>
                    </div>
                    <h5 style="font-size: 1.1rem; color: #2c3e50; font-weight: 700; margin: 0; text-align: center; line-height: 1.3;"><?php echo esc_html($partner['name']); ?></h5>
                </div>
            <?php endif; ?>
            
            <!-- Hover Overlay -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.05)); opacity: 0; transition: opacity 0.3s ease; border-radius: 20px;" class="logo-overlay"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    .partner-logo:hover .logo-overlay {
        opacity: 1 !important;
    }
    
    @media (max-width: 768px) {
        .partner-logo {
            min-height: 140px !important;
            padding: 25px 20px !important;
        }
        
        .partner-logo img {
            max-width: 120px !important;
            max-height: 70px !important;
        }
    }
    </style>
    <?php
}

// Render Featured Partners Layout - Logo Prominent
function kilismile_render_featured_partners($partners) {
    $featured_partners = array_slice($partners, 0, 6); // Show top 6 as featured
    $colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 35px;">
        <?php foreach ($featured_partners as $index => $partner): 
            $color = $colors[$index % count($colors)];
        ?>
        <div class="featured-partner" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 35px; border-radius: 25px; box-shadow: 0 12px 35px rgba(0,0,0,0.1); text-align: center; position: relative; overflow: hidden; transition: all 0.4s ease; border: 1px solid #f0f3f6;" onmouseover="this.style.transform='translateY(-12px) scale(1.02)'; this.style.boxShadow='0 20px 50px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 12px 35px rgba(0,0,0,0.1)'">
            
            <!-- Decorative Background -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>88); border-radius: 25px 25px 0 0;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"25\" height=\"25\" patternUnits=\"userSpaceOnUse\"><circle cx=\"12.5\" cy=\"12.5\" r=\"1.5\" fill=\"<?php echo urlencode($color); ?>\" opacity=\"0.05\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>'); opacity: 0.4;"></div>
            
            <!-- Large Logo Section -->
            <?php if ($partner['logo']): ?>
                <div style="position: relative; z-index: 2; background: white; border-radius: 20px; padding: 40px 30px; margin-bottom: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 2px solid #f8f9fa;">
                    <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 160px; max-height: 100px; object-fit: contain; filter: drop-shadow(0 3px 10px rgba(0,0,0,0.1)); transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0 5px 15px rgba(0,0,0,0.2))'" onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 3px 10px rgba(0,0,0,0.1))'">
                </div>
            <?php else: ?>
                <div style="position: relative; z-index: 2; background: white; border-radius: 20px; padding: 40px 30px; margin-bottom: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 2px solid #f8f9fa;">
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>cc); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; box-shadow: 0 8px 20px <?php echo esc_attr($color); ?>33;">
                        <i class="<?php echo esc_attr($partner['icon']); ?>" style="font-size: 2.5rem;" aria-hidden="true"></i>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Partner Information -->
            <div style="position: relative; z-index: 2;">
                <h4 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem; font-weight: 700; line-height: 1.3;">
                    <?php echo esc_html($partner['name']); ?>
                </h4>
                
                <div style="margin-bottom: 20px;">
                    <span style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); color: white; padding: 10px 20px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px <?php echo esc_attr($color); ?>33;">
                        <?php echo esc_html($partner['level']); ?>
                    </span>
                </div>
                
                <p style="color: #7f8c8d; font-size: 1rem; margin: 0; font-weight: 500; line-height: 1.5;">
                    <?php echo esc_html($partner['type']); ?>
                </p>
            </div>
            
            <!-- Enhanced Hover Effect -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, <?php echo esc_attr($color); ?>15, <?php echo esc_attr($color); ?>08); opacity: 0; transition: opacity 0.3s ease; border-radius: 25px;" class="featured-overlay"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    .featured-partner:hover .featured-overlay {
        opacity: 1 !important;
    }
    
    @media (max-width: 768px) {
        .featured-partner {
            min-width: 300px;
            margin: 0 auto;
        }
        
        .featured-partner img {
            max-width: 140px !important;
            max-height: 80px !important;
        }
    }
    </style>
    <?php
}

// AJAX handler for partner logo upload
add_action('wp_ajax_kilismile_upload_partner_logo', 'kilismile_handle_partner_logo_upload');
function kilismile_handle_partner_logo_upload() {
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized', 'Error', array('response' => 403));
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_partner_upload_nonce')) {
        wp_die('Invalid nonce', 'Error', array('response' => 403));
    }
    
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    
    $upload_overrides = array('test_form' => false);
    $uploaded_file = wp_handle_upload($_FILES['partner_logo'], $upload_overrides);
    
    if ($uploaded_file && !isset($uploaded_file['error'])) {
        // File uploaded successfully
        $attachment = array(
            'post_mime_type' => $uploaded_file['type'],
            'post_title' => sanitize_file_name(pathinfo($uploaded_file['file'], PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            
            wp_send_json_success(array(
                'url' => $uploaded_file['url'],
                'attachment_id' => $attachment_id
            ));
        }
    }
    
    wp_send_json_error('Upload failed');
}

// Enhanced Partner Data Processing with Logo Support
function kilismile_get_enhanced_partner_data($category = 'all') {
    $partners = array();
    
    // Get partner data from options
    $corporate_partners = get_option('kilismile_corporate_partners_enhanced', array());
    $community_partners = get_option('kilismile_community_partners_enhanced', array());
    $strategic_partners = get_option('kilismile_strategic_partners_enhanced', array());
    
    // Process corporate partners
    if ($category === 'all' || $category === 'corporate') {
        foreach ($corporate_partners as $partner) {
            $partners[] = array_merge($partner, array('category' => 'corporate'));
        }
    }
    
    // Process community partners
    if ($category === 'all' || $category === 'community') {
        foreach ($community_partners as $partner) {
            $partners[] = array_merge($partner, array('category' => 'community'));
        }
    }
    
    // Process strategic partners
    if ($category === 'all' || $category === 'strategic') {
        foreach ($strategic_partners as $partner) {
            $partners[] = array_merge($partner, array('category' => 'strategic'));
        }
    }
    
    return $partners;
}

// Save Enhanced Partner Data
function kilismile_save_enhanced_partner_data($category, $partners) {
    $option_name = 'kilismile_' . $category . '_partners_enhanced';
    update_option($option_name, $partners);
}

// Partner Logo Management Functions
function kilismile_get_partner_logo_url($partner_id, $size = 'medium') {
    $logo_id = get_option('kilismile_partner_logo_' . $partner_id);
    if ($logo_id) {
        $logo_url = wp_get_attachment_image_url($logo_id, $size);
        return $logo_url ? $logo_url : '';
    }
    return '';
}

function kilismile_save_partner_logo($partner_id, $attachment_id) {
    update_option('kilismile_partner_logo_' . $partner_id, $attachment_id);
}

// Include Enhanced Partner Management System
require_once get_template_directory() . '/admin-partner-management.php';

// Strategic Partner Logo Display Functions
function kilismile_display_homepage_partner_logos() {
    $featured_partners = array();
    $corporate = kilismile_get_enhanced_partner_data('corporate');
    $community = kilismile_get_enhanced_partner_data('community');
    $strategic = kilismile_get_enhanced_partner_data('strategic');
    
    // Get featured partners from all categories
    foreach (array_merge($corporate, $community, $strategic) as $partner) {
        if (!empty($partner['featured']) && !empty($partner['logo_url'])) {
            $featured_partners[] = $partner;
        }
    }
    
    if (empty($featured_partners)) {
        return '';
    }
    
    // Limit to 8 for homepage
    $featured_partners = array_slice($featured_partners, 0, 8);
    
    ob_start();
    ?>
    <section class="homepage-partners" style="padding: 60px 0; background: #f8f9fa;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h3 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 15px; font-weight: 700;">
                    <?php _e('Trusted by Leading Organizations', 'kilismile'); ?>
                </h3>
                <p style="color: #7f8c8d; font-size: 1rem; max-width: 600px; margin: 0 auto;">
                    <?php _e('We partner with world-class organizations to amplify our impact and reach more communities.', 'kilismile'); ?>
                </p>
            </div>
            
            <div class="homepage-partner-logos" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 25px; align-items: center;">
                <?php foreach ($featured_partners as $partner): ?>
                    <div class="homepage-partner-logo" style="background: white; padding: 25px 20px; border-radius: 15px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.05)'">
                        <?php if (!empty($partner['website'])): ?>
                            <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" rel="noopener" style="display: block; text-decoration: none;">
                        <?php endif; ?>
                        
                        <img src="<?php echo esc_url($partner['logo_url']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 140px; max-height: 70px; object-fit: contain; filter: grayscale(30%); transition: all 0.3s ease;" onmouseover="this.style.filter='grayscale(0%) scale(1.05)'" onmouseout="this.style.filter='grayscale(30%) scale(1)'">
                        
                        <?php if (!empty($partner['website'])): ?>
                            </a>
                        <?php endif; ?>
                        
                        <h5 style="color: #2c3e50; font-size: 0.9rem; margin: 15px 0 0; font-weight: 600; line-height: 1.3;">
                            <?php echo esc_html($partner['name']); ?>
                        </h5>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo esc_url(home_url('/partners')); ?>" style="color: #3498db; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; padding: 12px 25px; border: 2px solid #3498db; border-radius: 25px; transition: all 0.3s ease;" onmouseover="this.style.background='#3498db'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#3498db'">
                    <?php _e('View All Partners', 'kilismile'); ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    
    <style>
    @media (max-width: 768px) {
        .homepage-partner-logos {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }
        
        .homepage-partner-logo {
            padding: 20px 15px !important;
        }
        
        .homepage-partner-logo img {
            max-width: 120px !important;
            max-height: 60px !important;
        }
    }
    
    @media (max-width: 480px) {
        .homepage-partner-logos {
            grid-template-columns: 1fr !important;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

// Footer Partner Logos
function kilismile_display_footer_partner_logos() {
    $featured_partners = array();
    $all_partners = array_merge(
        kilismile_get_enhanced_partner_data('corporate'),
        kilismile_get_enhanced_partner_data('community'),
        kilismile_get_enhanced_partner_data('strategic')
    );
    
    foreach ($all_partners as $partner) {
        if (!empty($partner['logo_url'])) {
            $featured_partners[] = $partner;
        }
    }
    
    if (empty($featured_partners)) {
        return '';
    }
    
    // Limit to 6 for footer
    $featured_partners = array_slice($featured_partners, 0, 6);
    
    ob_start();
    ?>
    <div class="footer-partners" style="padding: 30px 0; background: rgba(255,255,255,0.05); border-top: 1px solid rgba(255,255,255,0.1);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 25px;">
                <h4 style="color: white; font-size: 1.2rem; margin: 0; font-weight: 600; opacity: 0.9;">
                    <?php _e('Our Partners', 'kilismile'); ?>
                </h4>
            </div>
            
            <div style="display: flex; justify-content: center; align-items: center; gap: 30px; flex-wrap: wrap;">
                <?php foreach ($featured_partners as $partner): ?>
                    <div style="opacity: 0.7; transition: all 0.3s ease;" onmouseover="this.style.opacity='1'; this.style.transform='scale(1.1)'" onmouseout="this.style.opacity='0.7'; this.style.transform='scale(1)'">
                        <?php if (!empty($partner['website'])): ?>
                            <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" rel="noopener">
                        <?php endif; ?>
                        
                        <img src="<?php echo esc_url($partner['logo_url']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 100px; max-height: 50px; object-fit: contain; filter: brightness(0) invert(1);">
                        
                        <?php if (!empty($partner['website'])): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Widget for Partner Showcase
class Kilismile_Partner_Showcase_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'kilismile_partner_showcase',
            'Partner Showcase',
            array('description' => 'Display partner logos in sidebar or widget areas')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'logos';
        $category = !empty($instance['category']) ? $instance['category'] : 'all';
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 4;
        
        echo kilismile_render_enhanced_partner_showcase($layout, $category, $limit);
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Our Partners';
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'logos';
        $category = !empty($instance['category']) ? $instance['category'] : 'all';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 4;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('layout'); ?>">Layout:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>">
                <option value="logos" <?php selected($layout, 'logos'); ?>>Logo Display</option>
                <option value="grid" <?php selected($layout, 'grid'); ?>>Grid Layout</option>
                <option value="featured" <?php selected($layout, 'featured'); ?>>Featured Partners</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>">Category:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
                <option value="all" <?php selected($category, 'all'); ?>>All Partners</option>
                <option value="corporate" <?php selected($category, 'corporate'); ?>>Corporate</option>
                <option value="community" <?php selected($category, 'community'); ?>>Community</option>
                <option value="strategic" <?php selected($category, 'strategic'); ?>>Strategic</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>">Number to show:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1" max="20">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['layout'] = (!empty($new_instance['layout'])) ? strip_tags($new_instance['layout']) : 'logos';
        $instance['category'] = (!empty($new_instance['category'])) ? strip_tags($new_instance['category']) : 'all';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 4;
        
        return $instance;
    }
}

// Register the widget
add_action('widgets_init', function() {
    register_widget('Kilismile_Partner_Showcase_Widget');
});
// Enhanced Partner Grid with Logo Upload Support
function kilismile_render_enhanced_partner_grid($partners) {
    if (empty($partners)) {
        return '<p style="text-align: center; color: #7f8c8d; font-style: italic;">' . __('No partners found.', 'kilismile') . '</p>';
    }
    
    $colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
    
    ob_start();
    ?>
    <div class="enhanced-partner-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin: 30px 0;">
        <?php foreach ($partners as $index => $partner): 
            $color = $colors[$index % count($colors)];
            $logo_url = !empty($partner['logo_url']) ? $partner['logo_url'] : '';
            $has_logo = !empty($logo_url);
        ?>
        <div class="enhanced-partner-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 25px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s ease; text-align: center; position: relative; overflow: hidden; border: 1px solid #f0f3f6; min-height: 280px; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.transform='translateY(-10px) scale(1.02)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'">
            
            <!-- Decorative Elements -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>88); border-radius: 25px 25px 0 0;"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"25\" height=\"25\" patternUnits=\"userSpaceOnUse\"><circle cx=\"12.5\" cy=\"12.5\" r=\"1.5\" fill=\"<?php echo urlencode($color); ?>\" opacity=\"0.04\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>') opacity: 0.6;))"></div>
            
            <!-- Logo Section - Prominent Display -->
            <div style="position: relative; z-index: 2; margin-bottom: 25px;">
                <?php if ($has_logo): ?>
                    <div style="background: white; border-radius: 20px; padding: 35px 25px; margin-bottom: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 3px solid #f8f9fa; min-height: 140px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 180px; max-height: 120px; object-fit: contain; filter: drop-shadow(0 3px 10px rgba(0,0,0,0.1)); transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.08)'; this.style.filter='drop-shadow(0 5px 15px rgba(0,0,0,0.2))'" onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 3px 10px rgba(0,0,0,0.1))'">
                    </div>
                <?php else: ?>
                    <div style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); border-radius: 20px; padding: 35px 25px; margin-bottom: 20px; min-height: 140px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 8px 25px <?php echo esc_attr($color); ?>33;">
                        <div style="text-align: center;">
                            <i class="<?php echo esc_attr($partner['icon'] ?? 'fas fa-handshake'); ?>" style="font-size: 3.5rem; margin-bottom: 15px; opacity: 0.9;" aria-hidden="true"></i>
                            <div style="font-size: 1.2rem; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;"><?php echo esc_html(substr($partner['name'], 0, 8)); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Partner Information -->
            <div style="position: relative; z-index: 2; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.5rem; font-weight: 700; line-height: 1.3;">
                        <?php echo esc_html($partner['name']); ?>
                    </h4>
                    
                    <?php if (!empty($partner['description'])): ?>
                        <p style="color: #7f8c8d; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo esc_html($partner['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <!-- Partner Level/Type Badge -->
                    <div style="margin-bottom: 15px;">
                        <span style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?>, <?php echo esc_attr($color); ?>dd); color: white; padding: 10px 20px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px <?php echo esc_attr($color); ?>33;">
                            <?php echo esc_html($partner['level'] ?? $partner['type'] ?? 'Partner'); ?>
                        </span>
                    </div>
                    
                    <!-- Website Link -->
                    <?php if (!empty($partner['website'])): ?>
                        <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" style="color: <?php echo esc_attr($color); ?>; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; padding: 8px 16px; border: 2px solid <?php echo esc_attr($color); ?>; border-radius: 25px; background: transparent;" onmouseover="this.style.background='<?php echo esc_attr($color); ?>'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='<?php echo esc_attr($color); ?>'">
                            <?php _e('Visit Partner', 'kilismile'); ?> <i class="fas fa-external-link-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    @media (max-width: 768px) {
        .enhanced-partner-grid {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        .enhanced-partner-card {
            min-height: 250px !important;
            padding: 20px !important;
        }
        
        .enhanced-partner-card img {
            max-width: 150px !important;
            max-height: 100px !important;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

// Enhanced Logo Display Layout
function kilismile_render_enhanced_partner_logos($partners) {
    if (empty($partners)) {
        return '<p style="text-align: center; color: #7f8c8d; font-style: italic;">' . __('No partner logos available.', 'kilismile') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="enhanced-partner-logos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; padding: 40px 0;">
        <?php foreach ($partners as $partner): 
            $logo_url = !empty($partner['logo_url']) ? $partner['logo_url'] : '';
            $has_logo = !empty($logo_url);
        ?>
        <div class="enhanced-logo-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 30px 20px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.06); text-align: center; transition: all 0.4s ease; position: relative; overflow: hidden; border: 2px solid #f8f9fa; min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;" onmouseover="this.style.transform='translateY(-8px) scale(1.03)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.12)'; this.style.borderColor='#e9ecef'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.06)'; this.style.borderColor='#f8f9fa'">
            
            <!-- Background Pattern -->
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><pattern id='dots' width='20' height='20' patternUnits='userSpaceOnUse'><circle cx='10' cy='10' r='1' fill='rgba(52,152,219,0.03)'/></pattern></defs><rect width='100' height='100' fill='url(%23dots)'/></svg>'); opacity: 0.7;"></div>
            
            <?php if ($has_logo): ?>
                <div style="position: relative; z-index: 2; margin-bottom: 15px;">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="max-width: 160px; max-height: 90px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.08)); transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0 4px 12px rgba(0,0,0,0.15))'" onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 2px 8px rgba(0,0,0,0.08))'">
                </div>
            <?php else: ?>
                <div style="position: relative; z-index: 2; margin-bottom: 15px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                        <i class="<?php echo esc_attr($partner['icon'] ?? 'fas fa-handshake'); ?>" style="font-size: 2rem; color: white;" aria-hidden="true"></i>
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="position: relative; z-index: 2;">
                <h5 style="color: #2c3e50; font-size: 1.1rem; font-weight: 700; margin: 0; line-height: 1.3; text-align: center;">
                    <?php echo esc_html($partner['name']); ?>
                </h5>
                <?php if (!empty($partner['level']) || !empty($partner['type'])): ?>
                    <p style="color: #7f8c8d; font-size: 0.85rem; margin: 8px 0 0; font-weight: 500;">
                        <?php echo esc_html($partner['level'] ?? $partner['type']); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <style>
    @media (max-width: 768px) {
        .enhanced-partner-logos {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }
        
        .enhanced-logo-card {
            min-height: 160px !important;
            padding: 25px 15px !important;
        }
        
        .enhanced-logo-card img {
            max-width: 140px !important;
            max-height: 80px !important;
        }
    }
    
    @media (max-width: 480px) {
        .enhanced-partner-logos {
            grid-template-columns: 1fr !important;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

// Shortcode for Enhanced Partner Showcase
add_shortcode('partner_showcase', 'kilismile_partner_showcase_shortcode');
add_shortcode('kilismile_partners', 'kilismile_enhanced_partner_shortcode');

function kilismile_enhanced_partner_shortcode($atts) {
    $atts = shortcode_atts(array(
        'layout' => 'grid',
        'category' => 'all',
        'limit' => -1,
    ), $atts);
    
    return kilismile_render_enhanced_partner_showcase($atts['layout'], $atts['category'], intval($atts['limit']));
}

foreach ($inc_files as $file) {
    $file_path = get_template_directory() . '/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log("Kilismile Theme: File not found - $file_path");
    }
}

if (is_admin()) {
    require_once get_template_directory() . '/admin-theme-dashboard.php';
    require_once get_template_directory() . '/admin-partner-management.php';
}

// Analytics AJAX handler
add_action('wp_ajax_kilismile_get_analytics_data', 'kilismile_handle_analytics_data');
function kilismile_handle_analytics_data() {
    check_ajax_referer('kilismile_analytics_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    // Get category distribution
    $categories = $wpdb->get_results("
        SELECT category, COUNT(*) as count 
        FROM $table_name 
        WHERE status = 'active' 
        GROUP BY category
    ", ARRAY_A);
    
    $by_category = array();
    foreach ($categories as $cat) {
        $by_category[$cat['category']] = intval($cat['count']);
    }
    
    // Get level distribution
    $levels = $wpdb->get_results("
        SELECT partnership_level, COUNT(*) as count 
        FROM $table_name 
        WHERE status = 'active' 
        GROUP BY partnership_level
    ", ARRAY_A);
    
    $by_level = array();
    foreach ($levels as $level) {
        $by_level[$level['partnership_level']] = intval($level['count']);
    }
    
    wp_send_json_success(array(
        'by_category' => $by_category,
        'by_level' => $by_level
    ));
}

// Load partners AJAX handler
add_action('wp_ajax_kilismile_load_partners', 'kilismile_handle_load_partners');
function kilismile_handle_load_partners() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $search = sanitize_text_field($_POST['search'] ?? '');
    $category = sanitize_text_field($_POST['category'] ?? 'all');
    $level = sanitize_text_field($_POST['level'] ?? 'all');
    $page = intval($_POST['page'] ?? 1);
    $per_page = intval($_POST['per_page'] ?? 20);
    
    $args = array(
        'search' => $search,
        'category' => $category === 'all' ? '' : $category,
        'partnership_level' => $level === 'all' ? '' : $level,
        'limit' => $per_page,
        'offset' => ($page - 1) * $per_page
    );
    
    $partners = kilismile_get_partners($args);
    $stats = kilismile_get_partner_stats();
    
    $html = '';
    if (empty($partners)) {
        $html = '<div class="no-partners-found">
            <div class="no-partners-icon">
                <i class="dashicons dashicons-groups"></i>
            </div>
            <h3>No Partners Found</h3>
            <p>Try adjusting your search or filter criteria.</p>
        </div>';
    } else {
        foreach ($partners as $partner) {
            $html .= kilismile_render_partner_admin_card($partner);
        }
    }
    
    wp_send_json_success(array(
        'html' => $html,
        'stats' => $stats,
        'total' => count($partners)
    ));
}

// Update partner order AJAX handler
add_action('wp_ajax_kilismile_update_partner_order', 'kilismile_handle_update_partner_order');
function kilismile_handle_update_partner_order() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $order = $_POST['order'] ?? array();
    
    if (empty($order) || !is_array($order)) {
        wp_send_json_error('Invalid order data');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'partners';
    
    $success = true;
    foreach ($order as $index => $partner_id) {
        $priority = (count($order) - $index) * 10;
        
        $result = $wpdb->update(
            $table_name,
            array('priority' => $priority),
            array('id' => intval($partner_id)),
            array('%d'),
            array('%d')
        );
        
        if ($result === false) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        wp_send_json_success('Order updated successfully');
    } else {
        wp_send_json_error('Failed to update order');
    }
}

// Get partner stats AJAX handler
add_action('wp_ajax_kilismile_get_partner_stats', 'kilismile_handle_get_partner_stats');
function kilismile_handle_get_partner_stats() {
    check_ajax_referer('kilismile_partner_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $stats = kilismile_get_partner_stats();
    wp_send_json_success($stats);
}

// Add sample partners for testing if none exist
add_action('admin_init', 'kilismile_add_sample_partners_if_empty');

// Add a new partner function
function kilismile_add_partner($partner_data) {
    global $wpdb;
    // Correct table name (previously used $wpdb->prefix . 'partners')
    $table_name = $wpdb->prefix . 'kilismile_partners';
    
    // Prepare partner data with defaults
    $data = array(
        'name' => sanitize_text_field($partner_data['name']),
        'description' => isset($partner_data['description']) ? wp_kses_post($partner_data['description']) : '',
        'short_description' => isset($partner_data['short_description']) ? sanitize_textarea_field($partner_data['short_description']) : '',
        'website' => isset($partner_data['website']) ? esc_url_raw($partner_data['website']) : '',
        'category' => isset($partner_data['category']) ? sanitize_text_field($partner_data['category']) : 'corporate',
        'partnership_type' => isset($partner_data['partnership_type']) ? sanitize_text_field($partner_data['partnership_type']) : 'supporter',
        'partnership_level' => isset($partner_data['partnership_level']) ? sanitize_text_field($partner_data['partnership_level']) : 'basic',
        'contact_person' => isset($partner_data['contact_person']) ? sanitize_text_field($partner_data['contact_person']) : '',
        // Map legacy keys to actual columns
        'email' => isset($partner_data['contact_email']) ? sanitize_email($partner_data['contact_email']) : (isset($partner_data['email']) ? sanitize_email($partner_data['email']) : ''),
        'phone' => isset($partner_data['contact_phone']) ? sanitize_text_field($partner_data['contact_phone']) : (isset($partner_data['phone']) ? sanitize_text_field($partner_data['phone']) : ''),
        'partnership_value' => isset($partner_data['partnership_value']) ? floatval($partner_data['partnership_value']) : null,
        'featured' => isset($partner_data['featured']) ? intval($partner_data['featured']) : 0,
        'status' => isset($partner_data['status']) ? sanitize_text_field($partner_data['status']) : 'active',
        'priority_level' => isset($partner_data['priority']) ? intval($partner_data['priority']) : 5,
        'tags' => isset($partner_data['tags']) ? sanitize_text_field($partner_data['tags']) : '',
        'social_media_links' => isset($partner_data['social_links']) ? $partner_data['social_links'] : (isset($partner_data['social_media_links']) ? $partner_data['social_media_links'] : '{}'),
        'created_at' => current_time('mysql')
        // updated_at column auto-updates via ON UPDATE clause
    );

    // Remove null partnership_value if not provided (lets DB default remain NULL vs 0 if desired)
    if ($data['partnership_value'] === null) {
        unset($data['partnership_value']);
    }
    
    $result = $wpdb->insert($table_name, $data);
    
    if ($result !== false) {
        return $wpdb->insert_id;
    }
    
    return false;
}

function kilismile_add_sample_partners_if_empty() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_partners';

    // Ensure the table exists before querying
    $table_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
        DB_NAME,
        $table_name
    ));
    if (!$table_exists) {
        // Table not yet created (maybe theme not reactivated) – attempt to create now
        if (function_exists('kilismile_create_partners_table')) {
            kilismile_create_partners_table();
        }
    }

    // If still no table, abort silently
    $maybe_count = $wpdb->get_var(@"SELECT COUNT(*) FROM $table_name");
    if ($maybe_count === null) {
        return;
    }

    // Check if partners table is empty
    if (intval($maybe_count) === 0) {
        $sample_partners = array(
            array(
                'name' => 'Tech Innovators Inc.',
                'website' => 'https://techinnovators.com',
                'description' => 'Leading technology company specializing in innovative solutions for digital transformation.',
                'short_description' => 'Technology innovation partner',
                'category' => 'corporate',
                'partnership_type' => 'strategic',
                'partnership_level' => 'platinum',
                'contact_person' => 'John Smith',
                'contact_email' => 'john@techinnovators.com',
                'contact_phone' => '+1-555-0123',
                'partnership_value' => 50000.00,
                'featured' => 1,
                'status' => 'active',
                'priority' => 90,
                'tags' => 'technology, innovation, digital',
                'social_links' => json_encode(array(
                    'linkedin' => 'https://linkedin.com/company/techinnovators',
                    'twitter' => 'https://twitter.com/techinnovators'
                ))
            ),
            array(
                'name' => 'Community Health Foundation',
                'website' => 'https://communityhealthfdn.org',
                'description' => 'Non-profit organization dedicated to improving community health outcomes.',
                'short_description' => 'Community health advocacy partner',
                'category' => 'community',
                'partnership_type' => 'community',
                'partnership_level' => 'gold',
                'contact_person' => 'Dr. Sarah Johnson',
                'contact_email' => 'sarah@communityhealthfdn.org',
                'partnership_value' => 25000.00,
                'featured' => 1,
                'status' => 'active',
                'priority' => 80,
                'tags' => 'health, community, non-profit',
                'social_links' => json_encode(array(
                    'facebook' => 'https://facebook.com/communityhealthfdn'
                ))
            ),
            array(
                'name' => 'Green Energy Solutions',
                'website' => 'https://greenenergysol.com',
                'description' => 'Renewable energy company providing sustainable solutions for businesses.',
                'short_description' => 'Sustainable energy solutions provider',
                'category' => 'corporate',
                'partnership_type' => 'financial',
                'partnership_level' => 'silver',
                'contact_person' => 'Mike Wilson',
                'contact_email' => 'mike@greenenergysol.com',
                'partnership_value' => 15000.00,
                'featured' => 0,
                'status' => 'active',
                'priority' => 70,
                'tags' => 'energy, sustainability, green',
                'social_links' => json_encode(array(
                    'linkedin' => 'https://linkedin.com/company/greenenergysol'
                ))
            )
        );
        
        foreach ($sample_partners as $partner) {
            kilismile_add_partner($partner); // mapping handled in kilismile_add_partner
        }
    }
}

// Include Email System
require_once get_template_directory() . '/includes/email-system.php';

// Include Theme Settings - Enhanced settings take priority
if (!class_exists('KiliSmile_Settings_Framework')) {
    // Load legacy settings only if enhanced settings are not available
    require_once get_template_directory() . '/admin/theme-settings.php';
}

// Include Migration Helper
require_once get_template_directory() . '/admin/migration-helper.php';

/**
 * Enqueue admin scripts and styles
 */
function kilismile_admin_enqueue_scripts($hook) {
    // Only load on our theme settings pages
    if (strpos($hook, 'kilismile-settings') !== false || 
        strpos($hook, 'kilismile') !== false) {
        
        // Enqueue jQuery (WordPress includes this by default in admin)
        wp_enqueue_script('jquery');
        
        // Enqueue our hidden field implementation script for toggle handling
        wp_enqueue_script(
            'kilismile-hidden-field-implementation',
            get_template_directory_uri() . '/admin/js/hidden-field-implementation.js',
            array('jquery'),
            '1.0.3',
            true
        );
        
        // Enqueue WordPress admin styles
        wp_enqueue_style('wp-admin');
        
        // Add custom admin styles
        wp_add_inline_style('wp-admin', '
            .kilismile-admin-page .nav-tab-wrapper {
                margin-bottom: 20px;
            }
            .kilismile-admin-page .tab-content {
                display: none !important;
            }
            .kilismile-admin-page .tab-content.active {
                display: block !important;
            }
        ');
    }
}
add_action('admin_enqueue_scripts', 'kilismile_admin_enqueue_scripts');

/**
 * Initialize KiliSmile Payments Plugin
 */
function kilismile_init_payments_plugin() {
    $plugin_file = get_template_directory() . '/kilismile-payments.php';
    if (file_exists($plugin_file)) {
        require_once $plugin_file;
    }
}
// Load plugin early but after WordPress is loaded
add_action('init', 'kilismile_init_payments_plugin', 5);

/**
 * Ensure payment system assets are loaded when needed
 */
function kilismile_ensure_payment_assets() {
    // Force load assets on donation pages
    if (is_page(array('donate', 'donation', 'corporate', 'partnerships')) || 
        (get_post() && has_shortcode(get_post()->post_content, 'kilismile_donation_form'))) {
        
        if (class_exists('KiliSmile_Payments_Plugin')) {
            $plugin = KiliSmile_Payments_Plugin::get_instance();
            if (method_exists($plugin, 'enqueue_frontend_assets')) {
                $plugin->enqueue_frontend_assets();
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'kilismile_ensure_payment_assets', 20);

/**
 * Load simple donation form for testing
 */
require_once get_template_directory() . '/simple-donation-form.php';

/**
 * WordPress AJAX handler for donation processing
 */
add_action('wp_ajax_kilismile_process_donation', 'kilismile_handle_donation_ajax');
add_action('wp_ajax_nopriv_kilismile_process_donation', 'kilismile_handle_donation_ajax');
add_action('wp_ajax_kilismile_submit_manual_receipt', 'kilismile_submit_manual_receipt');
add_action('wp_ajax_nopriv_kilismile_submit_manual_receipt', 'kilismile_submit_manual_receipt');

function kilismile_handle_donation_ajax() {
    try {
        // Log the incoming request for debugging
        error_log('KiliSmile Donation AJAX Request: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kilismile_donation_checkout')) {
            error_log('KiliSmile Donation: Nonce verification failed');
            wp_send_json_error(array('message' => 'Security verification failed. Please refresh and try again.'));
            return;
        }
    
    // Get donation data - handle both nested and flat structure
    $donation_data = $_POST['donation_data'] ?? $_POST;
    
    // Log donation data for debugging
    error_log('KiliSmile Donation Data: ' . print_r($donation_data, true));
    
    // Validate required fields
    $required_fields = array('donation_amount', 'currency', 'donor_first_name', 'donor_last_name', 'donor_email', 'donor_phone', 'payment_method');
    foreach ($required_fields as $field) {
        $value = $donation_data[$field] ?? '';
        if (empty($value)) {
            error_log("KiliSmile Donation: Missing required field: {$field}");
            wp_send_json_error(array('message' => "Missing required field: " . str_replace('_', ' ', $field)));
            return;
        }
    }
    
    // Sanitize data
    $amount = floatval($donation_data['donation_amount']);
    $currency = sanitize_text_field($donation_data['currency']);
    $donor_first_name = sanitize_text_field($donation_data['donor_first_name']);
    $donor_last_name = sanitize_text_field($donation_data['donor_last_name']);
    $donor_email = sanitize_email($donation_data['donor_email']);
    $donor_phone = sanitize_text_field($donation_data['donor_phone']);
    $payment_method = sanitize_text_field($donation_data['payment_method']);
    $anonymous = !empty($donation_data['anonymous_donation']);

    $azampay_enabled = (bool) get_option('kilismile_azampay_enabled', true);
    $paypal_enabled = (bool) get_option('kilismile_paypal_enabled', false);
    $manual_enabled = (int) get_option('kilismile_local_bank_enabled', 1) === 1;

    if ($payment_method === 'azampay' && !$azampay_enabled) {
        wp_send_json_error(array('message' => 'AzamPay is currently disabled. Please choose another method.'));
        return;
    }

    if ($payment_method === 'paypal' && !$paypal_enabled) {
        wp_send_json_error(array('message' => 'PayPal is currently disabled. Please choose another method.'));
        return;
    }

    if (($payment_method === 'manual_transfer' || $payment_method === 'bank_transfer') && !$manual_enabled) {
        wp_send_json_error(array('message' => 'Manual transfer is currently disabled. Please choose another method.'));
        return;
    }
    
    // Validate amount
    if ($amount <= 0) {
        wp_send_json_error(array('message' => 'Invalid donation amount.'));
        return;
    }
    
    // Validate currency
    if (!in_array($currency, array('TZS', 'USD'))) {
        wp_send_json_error(array('message' => 'Invalid currency selected.'));
        return;
    }
    
    // Validate email
    if (!is_email($donor_email)) {
        wp_send_json_error(array('message' => 'Invalid email address.'));
        return;
    }
    
    // For now, simulate successful processing
    $transaction_id = 'KILISMILE_' . time() . '_' . rand(1000, 9999);
    
    // Log successful processing
    error_log("KiliSmile Donation: Processing successful - Transaction ID: {$transaction_id}");
    
    // Process payment through AzamPay gateway
    if ($payment_method === 'azampay') {
        error_log('KiliSmile Donation: Processing AzamPay STK Push payment');
        
        // Get additional AzamPay data
        $mobile_network = sanitize_text_field($donation_data['mobile_network'] ?? 'Tigo');
        $azampay_type = sanitize_text_field($donation_data['azampay_type'] ?? 'stkpush');
        $payment_phone = sanitize_text_field($donation_data['payment_phone'] ?? $donor_phone);
        
        // Format phone number for AzamPay (must be in 255XXXXXXXXX format)
        $formatted_phone = kilismile_format_phone_for_azampay($payment_phone);
        if (!$formatted_phone) {
            wp_send_json_error(array('message' => 'Invalid phone number format. Please use format: +255XXXXXXXXX or 255XXXXXXXXX'));
            return;
        }
        
        // Map network names to AzamPay providers
        $provider_map = array(
            'Tigo' => 'Tigo',
            'Vodacom' => 'Vodacom', 
            'Airtel' => 'Airtel',
            'Halopesa' => 'Halopesa'
        );
        $azampay_provider = $provider_map[$mobile_network] ?? 'Tigo';
        
        // Process AzamPay payment
        $azampay_result = kilismile_process_azampay_payment(array(
            'amount' => $amount,
            'currency' => $currency,
            'phone' => $formatted_phone,
            'provider' => $azampay_provider,
            'external_id' => $transaction_id,
            'donor_name' => $donor_first_name . ' ' . $donor_last_name,
            'donor_email' => $donor_email
        ));
        
        if ($azampay_result['success']) {
            wp_send_json_success(array(
                'payment_type' => 'stk_push',
                'transaction_id' => $azampay_result['transaction_id'],
                'azampay_transaction_id' => $azampay_result['azampay_transaction_id'] ?? '',
                'payment_provider' => 'AzamPay Mobile Money (' . $mobile_network . ')',
                'amount' => $amount,
                'currency' => $currency,
                'phone' => $formatted_phone,
                'network' => $mobile_network,
                'azampay_type' => $azampay_type,
                'message' => 'STK Push sent successfully. Please check your phone for the payment request.',
                'instructions' => array(
                    'Check your phone for an STK Push notification',
                    'Enter your mobile money PIN when prompted',
                    'Confirm the payment amount: ' . $currency . ' ' . number_format($amount),
                    'Complete the transaction on your phone'
                ),
                'test_mode' => $azampay_result['test_mode'] ?? false,
                'donor_name' => $donor_first_name . ' ' . $donor_last_name,
                'donor_email' => $donor_email
            ));
        } else {
            wp_send_json_error(array(
                'message' => $azampay_result['message'] ?? 'Payment processing failed. Please try again.',
                'error_code' => $azampay_result['error_code'] ?? 'PAYMENT_FAILED'
            ));
        }
    } elseif ($payment_method === 'paypal') {
        // Use real PayPal REST API integration
        if (!class_exists('KiliSmile_PayPal')) {
            require_once get_template_directory() . '/plugin-includes/paypal-integration.php.disabled';
        }
        $paypal = new KiliSmile_PayPal();
        try {
            $payment_url = $paypal->create_payment($amount, $currency, $transaction_id, $donor_first_name . ' ' . $donor_last_name, $donor_email);
            wp_send_json_success(array(
                'redirect_url' => $payment_url,
                'transaction_id' => $transaction_id,
                'payment_provider' => 'PayPal',
                'amount' => $amount,
                'currency' => $currency
            ));
        } catch (Exception $e) {
            error_log('KiliSmile PayPal Error: ' . $e->getMessage());
            wp_send_json_error(array('message' => 'PayPal payment initialization failed: ' . $e->getMessage()));
        }
    } elseif ($payment_method === 'manual_transfer' || $payment_method === 'bank_transfer') {
        // Handle manual/bank transfer - save donation with pending status
        error_log('KiliSmile Donation: Processing Manual Bank Transfer');
        
        // Save donation to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'donations';
        
        // Create donation record
        $donation_record = array(
            'donation_id' => $transaction_id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending_verification',
            'payment_method' => 'manual_transfer',
            'first_name' => $donor_first_name,
            'last_name' => $donor_last_name,
            'email' => $donor_email,
            'phone' => $donor_phone,
            'anonymous' => $anonymous ? 1 : 0,
            'purpose' => sanitize_text_field($donation_data['donation_purpose'] ?? 'general'),
            'message' => sanitize_textarea_field($donation_data['donor_message'] ?? ''),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
        
        // Insert donation record
        $inserted = $wpdb->insert($table_name, $donation_record);
        
        if ($inserted) {
            // Get bank account details from payment gateway settings
            $bank_details_raw = get_option('kilismile_local_bank_details', '');
            
            // Parse bank details from text format
            $bank_name = '';
            $account_name = '';
            $account_number = '';
            $swift_code = '';
            $branch = '';
            
            if (!empty($bank_details_raw)) {
                // Parse multiline bank details
                $lines = explode("\n", $bank_details_raw);
                foreach ($lines as $line) {
                    if (stripos($line, 'Bank Name:') !== false) {
                        $bank_name = trim(str_ireplace('Bank Name:', '', $line));
                    } elseif (stripos($line, 'Account Name:') !== false) {
                        $account_name = trim(str_ireplace('Account Name:', '', $line));
                    } elseif (stripos($line, 'Account Number:') !== false) {
                        $account_number = trim(str_ireplace('Account Number:', '', $line));
                    } elseif (stripos($line, 'Swift Code:') !== false || stripos($line, 'SWIFT Code:') !== false) {
                        $swift_code = trim(str_ireplace(array('Swift Code:', 'SWIFT Code:'), '', $line));
                    } elseif (stripos($line, 'Branch:') !== false) {
                        $branch = trim(str_ireplace('Branch:', '', $line));
                    }
                }
            }
            
            // Fallback to default values if not set
            $bank_name = !empty($bank_name) ? $bank_name : 'CRDB Bank';
            $account_name = !empty($account_name) ? $account_name : 'Kilimanjaro Smile Foundation';
            $account_number = !empty($account_number) ? $account_number : '0150414479200';
            $swift_code = !empty($swift_code) ? $swift_code : 'CORUTZTZ';
            $branch = !empty($branch) ? $branch : 'Moshi, Kilimanjaro';
            
            // Send confirmation email with bank details
            $to = $donor_email;
            $subject = 'Bank Transfer Instructions - KiliSmile Donation #' . $transaction_id;
            $message = "
Dear {$donor_first_name} {$donor_last_name},

Thank you for choosing to support KiliSmile Foundation!

Your donation of {$currency} " . number_format($amount, 2) . " has been registered with Transaction ID: {$transaction_id}

To complete your donation, please transfer the amount to our bank account:

Bank Name: {$bank_name}
Account Name: {$account_name}
Account Number: {$account_number}
Swift Code: {$swift_code}" . ($branch ? "\nBranch: {$branch}" : "") . "
Amount: {$currency} " . number_format($amount, 2) . "
Reference: {$transaction_id}

IMPORTANT: Please include the transaction ID '{$transaction_id}' in your transfer reference/description so we can match your payment.

After making the transfer, you can:
1. Take a screenshot of the payment confirmation
2. Email it to donations@kilismile.org with your transaction ID
3. Or upload it through your donation dashboard

We will verify your payment within 1-2 business days and send you a confirmation receipt.

Thank you for your generosity!

Best regards,
KiliSmile Foundation Team
            ";
            
            $headers = array('Content-Type: text/plain; charset=UTF-8');
            wp_mail($to, $subject, $message, $headers);
            
            // Return success with bank details
            wp_send_json_success(array(
                'payment_type' => 'manual_transfer',
                'transaction_id' => $transaction_id,
                'payment_provider' => 'Bank Transfer (Manual)',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending_verification',
                'message' => 'Donation registered successfully. Please check your email for bank transfer instructions.',
                'bank_details' => array(
                    'bank_name' => $bank_name,
                    'account_name' => $account_name,
                    'account_number' => $account_number,
                    'swift_code' => $swift_code,
                    'branch' => $branch,
                    'reference' => $transaction_id
                ),
                'instructions' => array(
                    'Transfer ' . $currency . ' ' . number_format($amount, 2) . ' to the bank account provided',
                    'Use Transaction ID: ' . $transaction_id . ' as your payment reference',
                    'Email proof of payment to donations@kilismile.org',
                    'We will verify and confirm your donation within 1-2 business days'
                ),
                'donor_name' => $donor_first_name . ' ' . $donor_last_name,
                'donor_email' => $donor_email
            ));
        } else {
            error_log('KiliSmile Donation: Database insert failed - ' . $wpdb->last_error);
            wp_send_json_error(array('message' => 'Failed to save donation. Please try again.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Unsupported payment method.'));
    }
    
    } catch (Exception $e) {
        error_log('KiliSmile Donation Fatal Error: ' . $e->getMessage());
        error_log('KiliSmile Donation Stack Trace: ' . $e->getTraceAsString());
        wp_send_json_error(array('message' => 'An error occurred while processing your donation. Please try again.'));
    } catch (Error $e) {
        error_log('KiliSmile Donation PHP Error: ' . $e->getMessage());
        error_log('KiliSmile Donation Error Trace: ' . $e->getTraceAsString());
        wp_send_json_error(array('message' => 'A system error occurred. Please try again.'));
    }
}

/**
 * Submit manual transfer receipt for verification
 */
function kilismile_submit_manual_receipt() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kilismile_manual_receipt')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
        return;
    }

    $donation_id = sanitize_text_field($_POST['donation_id'] ?? '');
    $receipt_reference = sanitize_text_field($_POST['receipt_reference'] ?? '');

    if (empty($donation_id) || empty($receipt_reference)) {
        wp_send_json_error(array('message' => 'Donation ID and receipt reference are required.'));
        return;
    }

    if (!class_exists('KiliSmile_Donation_Database')) {
        wp_send_json_error(array('message' => 'Donation system not available.'));
        return;
    }

    $db = new KiliSmile_Donation_Database();
    $donation = $db->get_donation($donation_id);

    if (empty($donation)) {
        wp_send_json_error(array('message' => 'Donation not found.'));
        return;
    }

    if (!in_array($donation['payment_method'], array('manual_transfer', 'bank_transfer'), true)) {
        wp_send_json_error(array('message' => 'Receipt upload is only available for bank transfers.'));
        return;
    }

    // Prevent duplicate receipt references
    global $wpdb;
    $meta_table = $wpdb->prefix . 'donation_meta';
    $existing_reference = $wpdb->get_var($wpdb->prepare(
        "SELECT donation_id FROM {$meta_table} WHERE meta_key = %s AND meta_value = %s AND donation_id != %s LIMIT 1",
        'manual_receipt_reference',
        $receipt_reference,
        $donation_id
    ));

    if (!empty($existing_reference)) {
        wp_send_json_error(array('message' => 'This receipt reference is already used. Please double-check your reference.'));
        return;
    }

    $receipt_url = '';
    if (!empty($_FILES['receipt_file']['name'])) {
        if ($_FILES['receipt_file']['size'] > 5 * 1024 * 1024) {
            wp_send_json_error(array('message' => 'Receipt file is too large. Maximum size is 5MB.'));
            return;
        }

        $allowed_mimes = array(
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'pdf'  => 'application/pdf'
        );

        $filetype = wp_check_filetype_and_ext(
            $_FILES['receipt_file']['tmp_name'],
            $_FILES['receipt_file']['name'],
            $allowed_mimes
        );

        if (empty($filetype['ext'])) {
            wp_send_json_error(array('message' => 'Invalid file type. Allowed: JPG, PNG, PDF.'));
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['receipt_file'], array('test_form' => false));

        if (!empty($upload['error'])) {
            wp_send_json_error(array('message' => 'Upload failed: ' . $upload['error']));
            return;
        }

        $receipt_url = $upload['url'] ?? '';
    }

    $db->update_donation_meta($donation_id, 'manual_receipt_reference', $receipt_reference);
    $db->update_donation_meta($donation_id, 'manual_receipt_file_url', $receipt_url);
    $db->update_donation_meta($donation_id, 'manual_receipt_submitted_at', current_time('mysql'));

    if (($donation['status'] ?? '') === 'pending_verification') {
        $db->update_donation_status($donation_id, 'pending');
    }

    wp_send_json_success(array(
        'message' => 'Receipt submitted successfully. We will verify and confirm your donation shortly.',
        'receipt_url' => $receipt_url
    ));
}

/**
 * AzamPay Integration Functions
 * Based on AzamPay API Documentation v1
 */

/**
 * Format phone number for AzamPay API
 */
function kilismile_format_phone_for_azampay($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert to 255XXXXXXXXX format
    if (preg_match('/^0([67]\d{8})$/', $phone, $matches)) {
        // Convert 0XXXXXXXXX to 255XXXXXXXXX
        return '255' . $matches[1];
    } elseif (preg_match('/^255([67]\d{8})$/', $phone, $matches)) {
        // Already in correct format
        return $phone;
    } elseif (preg_match('/^([67]\d{8})$/', $phone, $matches)) {
        // Add 255 prefix
        return '255' . $matches[1];
    }
    
    return false;
}

/**
 * Get AzamPay configuration
 */
function kilismile_get_azampay_config() {
    // Check if we're in test mode (default: yes for safety)
    $test_mode = get_option('kilismile_azampay_test_mode', 'yes') === 'yes';
    
    if ($test_mode) {
        return array(
            'test_mode' => true,
            'auth_url' => 'https://authenticator-sandbox.azampay.co.tz',
            'checkout_url' => 'https://sandbox.azampay.co.tz',
            'app_name' => get_option('kilismile_azampay_sandbox_app_name', 'KiliSmile-Sandbox'),
            'client_id' => get_option('kilismile_azampay_sandbox_client_id', ''),
            'client_secret' => get_option('kilismile_azampay_sandbox_client_secret', ''),
            'api_key' => get_option('kilismile_azampay_sandbox_api_key', ''),
        );
    } else {
        return array(
            'test_mode' => false,
            'auth_url' => 'https://authenticator.azampay.co.tz',
            'checkout_url' => 'https://checkout.azampay.co.tz',
            'app_name' => get_option('kilismile_azampay_live_app_name', ''),
            'client_id' => get_option('kilismile_azampay_live_client_id', ''),
            'client_secret' => get_option('kilismile_azampay_live_client_secret', ''),
            'api_key' => get_option('kilismile_azampay_live_api_key', ''),
        );
    }
}

/**
 * Generate AzamPay access token
 */
function kilismile_get_azampay_token() {
    $config = kilismile_get_azampay_config();
    
    // Check for cached token
    $cache_key = 'kilismile_azampay_token_' . ($config['test_mode'] ? 'sandbox' : 'live');
    $cached_token = get_transient($cache_key);
    
    if ($cached_token) {
        return $cached_token;
    }
    
    // Generate new token
    $token_url = $config['auth_url'] . '/AppRegistration/GenerateToken';
    
    $body = array(
        'appName' => $config['app_name'],
        'clientId' => $config['client_id'],
        'clientSecret' => $config['client_secret']
    );
    
    $response = wp_remote_post($token_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($body),
        'timeout' => 30,
    ));
    
    if (is_wp_error($response)) {
        error_log('KiliSmile AzamPay Token Error: ' . $response->get_error_message());
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('KiliSmile AzamPay Token Response Code: ' . $response_code);
    error_log('KiliSmile AzamPay Token Response Body: ' . $response_body);
    
    if ($response_code === 200) {
        $data = json_decode($response_body, true);
        
        if (isset($data['data']['accessToken'])) {
            $token = $data['data']['accessToken'];
            
            // Cache token for 55 minutes (tokens expire in 1 hour)
            set_transient($cache_key, $token, 55 * MINUTE_IN_SECONDS);
            
            return $token;
        }
    }
    
    return false;
}

/**
 * Process AzamPay payment
 */
function kilismile_process_azampay_payment($payment_data) {
    $config = kilismile_get_azampay_config();
    
    // Check if we have valid configuration
    if (empty($config['client_id']) || empty($config['client_secret'])) {
        if ($config['test_mode']) {
            // In test mode, simulate the payment
            error_log('KiliSmile AzamPay: Running in test mode (no credentials configured)');
            return array(
                'success' => true,
                'test_mode' => true,
                'transaction_id' => $payment_data['external_id'],
                'azampay_transaction_id' => 'AZAM_' . time(),
                'message' => 'Test mode: STK Push simulation sent'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'AzamPay credentials not configured',
                'error_code' => 'NO_CREDENTIALS'
            );
        }
    }
    
    // Get access token
    $token = kilismile_get_azampay_token();
    if (!$token) {
        return array(
            'success' => false,
            'message' => 'Failed to obtain AzamPay access token',
            'error_code' => 'TOKEN_FAILED'
        );
    }
    
    // Prepare checkout request
    $checkout_url = $config['checkout_url'] . '/azampay/mno/checkout';
    
    $checkout_body = array(
        'accountNumber' => $payment_data['phone'],
        'amount' => (string) $payment_data['amount'],
        'currency' => $payment_data['currency'],
        'externalId' => $payment_data['external_id'],
        'provider' => $payment_data['provider'],
        'callbackUrl' => home_url('/azampay/callback/'),
        'additionalProperties' => array(
            'donor_name' => $payment_data['donor_name'],
            'donor_email' => $payment_data['donor_email']
        )
    );
    
    $headers = array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token,
    );
    
    // Add API key if available
    if (!empty($config['api_key'])) {
        $headers['X-API-Key'] = $config['api_key'];
    }
    
    error_log('KiliSmile AzamPay Checkout Request: ' . json_encode($checkout_body));
    
    $response = wp_remote_post($checkout_url, array(
        'headers' => $headers,
        'body' => json_encode($checkout_body),
        'timeout' => 45,
    ));
    
    if (is_wp_error($response)) {
        error_log('KiliSmile AzamPay Checkout Error: ' . $response->get_error_message());
        return array(
            'success' => false,
            'message' => 'Network error: ' . $response->get_error_message(),
            'error_code' => 'NETWORK_ERROR'
        );
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('KiliSmile AzamPay Checkout Response Code: ' . $response_code);
    error_log('KiliSmile AzamPay Checkout Response Body: ' . $response_body);
    
    $data = json_decode($response_body, true);
    
    if ($response_code === 200 && isset($data['success']) && $data['success']) {
        // Store transaction for callback handling
        kilismile_store_azampay_transaction($payment_data['external_id'], array(
            'azampay_transaction_id' => $data['transactionId'] ?? '',
            'phone' => $payment_data['phone'],
            'amount' => $payment_data['amount'],
            'currency' => $payment_data['currency'],
            'provider' => $payment_data['provider'],
            'donor_name' => $payment_data['donor_name'],
            'donor_email' => $payment_data['donor_email'],
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));
        
        return array(
            'success' => true,
            'test_mode' => $config['test_mode'],
            'transaction_id' => $payment_data['external_id'],
            'azampay_transaction_id' => $data['transactionId'] ?? '',
            'message' => 'STK Push sent successfully'
        );
    } else {
        $error_message = 'Payment failed';
        if (isset($data['message'])) {
            $error_message = $data['message'];
        } elseif (isset($data['error'])) {
            $error_message = $data['error'];
        }
        
        return array(
            'success' => false,
            'message' => $error_message,
            'error_code' => 'AZAMPAY_ERROR',
            'response_code' => $response_code
        );
    }
}

/**
 * Store AzamPay transaction for callback handling
 */
function kilismile_store_azampay_transaction($external_id, $transaction_data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_azampay_transactions';
    
    // Create table if it doesn't exist
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        external_id varchar(255) NOT NULL,
        azampay_transaction_id varchar(255),
        phone varchar(20),
        amount decimal(10,2),
        currency varchar(3),
        provider varchar(50),
        donor_name varchar(255),
        donor_email varchar(255),
        status varchar(20) DEFAULT 'pending',
        created_at datetime,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY external_id (external_id)
    )");
    
    return $wpdb->insert($table_name, $transaction_data);
}

/**
 * Handle AzamPay callback
 */
add_action('wp_ajax_kilismile_azampay_callback', 'kilismile_handle_azampay_callback');
add_action('wp_ajax_nopriv_kilismile_azampay_callback', 'kilismile_handle_azampay_callback');

function kilismile_handle_azampay_callback() {
    error_log('KiliSmile AzamPay Callback Received: ' . print_r($_POST, true));
    
    // Verify callback data
    $required_fields = array('utilityref', 'amount', 'transactionstatus');
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            error_log('KiliSmile AzamPay Callback: Missing field ' . $field);
            wp_die('Missing required field: ' . $field, 'Bad Request', array('response' => 400));
        }
    }
    
    $external_id = sanitize_text_field($_POST['utilityref']);
    $amount = sanitize_text_field($_POST['amount']);
    $status = sanitize_text_field($_POST['transactionstatus']);
    $azampay_ref = sanitize_text_field($_POST['reference'] ?? '');
    $operator = sanitize_text_field($_POST['operator'] ?? '');
    $msisdn = sanitize_text_field($_POST['msisdn'] ?? '');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_azampay_transactions';
    
    // Update transaction status
    $updated = $wpdb->update(
        $table_name,
        array(
            'status' => $status,
            'azampay_transaction_id' => $azampay_ref
        ),
        array('external_id' => $external_id)
    );
    
    if ($updated) {
        error_log("KiliSmile AzamPay Callback: Updated transaction {$external_id} with status {$status}");
        
        // If payment successful, trigger any additional processing
        if ($status === 'success') {
            do_action('kilismile_donation_payment_completed', $external_id, array(
                'amount' => $amount,
                'provider' => 'azampay',
                'azampay_ref' => $azampay_ref,
                'operator' => $operator,
                'phone' => $msisdn
            ));
        }
        
        wp_die('OK', 'Success', array('response' => 200));
    } else {
        error_log("KiliSmile AzamPay Callback: Failed to update transaction {$external_id}");
        wp_die('Failed to update transaction', 'Internal Server Error', array('response' => 500));
    }
}

/**
 * Add custom rewrite rules for AzamPay webhooks
 */
add_action('init', 'kilismile_add_azampay_rewrite_rules');

function kilismile_add_azampay_rewrite_rules() {
    add_rewrite_rule(
        '^azampay/callback/?$',
        'index.php?azampay_callback=1',
        'top'
    );
}

add_filter('query_vars', 'kilismile_add_azampay_query_vars');

function kilismile_add_azampay_query_vars($vars) {
    $vars[] = 'azampay_callback';
    return $vars;
}

add_action('template_redirect', 'kilismile_handle_azampay_webhook');

function kilismile_handle_azampay_webhook() {
    if (get_query_var('azampay_callback')) {
        kilismile_handle_azampay_callback();
        exit;
    }
}

/**
 * Flush rewrite rules on theme activation
 */
add_action('after_switch_theme', 'kilismile_flush_rewrite_rules');
add_action('after_switch_theme', 'kilismile_create_default_pages');

function kilismile_flush_rewrite_rules() {
    kilismile_add_azampay_rewrite_rules();
    flush_rewrite_rules();
}

/**
 * Create default pages on theme activation
 */
function kilismile_create_default_pages() {
    // Create Become a Partner page
    $partner_page = get_page_by_path('become-partner');
    if (!$partner_page) {
        $page_data = array(
            'post_title'     => 'Become a Partner',
            'post_content'   => '', // Content will come from the template
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_name'      => 'become-partner',
            'page_template'  => 'page-become-partner.php'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', 'page-become-partner.php');
            error_log('KiliSmile: Created "Become a Partner" page with ID: ' . $page_id);
        }
    }
}

/**
 * Initialize default pages on theme init (runs on every page load until pages are created)
 */
add_action('init', 'kilismile_ensure_default_pages');

function kilismile_ensure_default_pages() {
    // Only run this once
    if (!get_option('kilismile_default_pages_created')) {
        kilismile_create_default_pages();
        update_option('kilismile_default_pages_created', true);
    }
}

/**
 * Handle Partnership Application Form Submission
 */
add_action('wp_ajax_kilismile_handle_partnership_application', 'kilismile_handle_partnership_application');
add_action('wp_ajax_nopriv_kilismile_handle_partnership_application', 'kilismile_handle_partnership_application');

function kilismile_handle_partnership_application() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_partnership_nonce')) {
        wp_die('Security check failed');
    }
    
    // Sanitize form data
    $organization_name = sanitize_text_field($_POST['organization_name']);
    $contact_person = sanitize_text_field($_POST['contact_person']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $organization_type = sanitize_text_field($_POST['organization_type']);
    $partnership_type = sanitize_text_field($_POST['partnership_type']);
    $annual_budget = sanitize_text_field($_POST['annual_budget']);
    $organization_goals = sanitize_textarea_field($_POST['organization_goals']);
    $partnership_interest = sanitize_textarea_field($_POST['partnership_interest']);
    
    // Validate required fields
    if (empty($organization_name) || empty($contact_person) || empty($email) || 
        empty($organization_type) || empty($partnership_type) || empty($partnership_interest)) {
        wp_send_json_error('Please fill in all required fields.');
        return;
    }
    
    // Validate email
    if (!is_email($email)) {
        wp_send_json_error('Please enter a valid email address.');
        return;
    }
    
    // Store partnership application in database
    global $wpdb;
    
    // Create partnerships table if it doesn't exist
    $table_name = $wpdb->prefix . 'kilismile_partnership_applications';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        organization_name varchar(255) NOT NULL,
        contact_person varchar(255) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(50),
        organization_type varchar(100) NOT NULL,
        partnership_type varchar(100) NOT NULL,
        annual_budget varchar(100),
        organization_goals text,
        partnership_interest text NOT NULL,
        status varchar(50) DEFAULT 'pending',
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Insert application data
    $result = $wpdb->insert(
        $table_name,
        array(
            'organization_name' => $organization_name,
            'contact_person' => $contact_person,
            'email' => $email,
            'phone' => $phone,
            'organization_type' => $organization_type,
            'partnership_type' => $partnership_type,
            'annual_budget' => $annual_budget,
            'organization_goals' => $organization_goals,
            'partnership_interest' => $partnership_interest,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result === false) {
        error_log('Partnership application database error: ' . $wpdb->last_error);
        wp_send_json_error('There was an error submitting your application. Please try again.');
        return;
    }
    
    // Send notification email to admin
    $admin_email = get_option('admin_email');
    $subject = 'New Partnership Application - ' . $organization_name;
    
    $message = "A new partnership application has been submitted:\n\n";
    $message .= "Organization: " . $organization_name . "\n";
    $message .= "Contact Person: " . $contact_person . "\n";
    $message .= "Email: " . $email . "\n";
    $message .= "Phone: " . $phone . "\n";
    $message .= "Organization Type: " . $organization_type . "\n";
    $message .= "Partnership Type: " . $partnership_type . "\n";
    $message .= "Budget Range: " . $annual_budget . "\n\n";
    $message .= "Organization Goals:\n" . $organization_goals . "\n\n";
    $message .= "Partnership Interest:\n" . $partnership_interest . "\n\n";
    $message .= "Submitted: " . current_time('mysql') . "\n";
    $message .= "View in admin: " . admin_url('admin.php?page=kilismile_partnership_applications');
    
    wp_mail($admin_email, $subject, $message);
    
    // Send confirmation email to applicant
    $confirmation_subject = 'Partnership Application Received - KiliSmile Organization';
    $confirmation_message = "Dear " . $contact_person . ",\n\n";
    $confirmation_message .= "Thank you for your interest in partnering with KiliSmile Organization!\n\n";
    $confirmation_message .= "We have received your partnership application for " . $organization_name . " and will review it carefully. Our partnership team will contact you within 24-48 hours to discuss next steps.\n\n";
    $confirmation_message .= "In the meantime, feel free to explore our current partnerships and impact stories on our website.\n\n";
    $confirmation_message .= "Best regards,\n";
    $confirmation_message .= "KiliSmile Partnership Team\n";
    $confirmation_message .= "Email: partnerships@kilismile.org\n";
    $confirmation_message .= "Website: " . home_url();
    
    wp_mail($email, $confirmation_subject, $confirmation_message);
    
    wp_send_json_success('Application submitted successfully!');
}

/**
 * Add admin menu pages
 */
add_action('admin_menu', 'kilismile_add_admin_menu_pages');

function kilismile_add_admin_menu_pages() {
    // Partnership Applications
    add_menu_page(
        'Partnership Applications',
        'Partnerships',
        'manage_options',
        'kilismile_partnership_applications',
        'kilismile_partnership_applications_page',
        'dashicons-groups',
        30
    );
    
    // Theme Tools
    add_submenu_page(
        'tools.php',
        'KiliSmile Tools',
        'KiliSmile Tools',
        'manage_options',
        'kilismile_tools',
        'kilismile_tools_page'
    );
}

/**
 * Partnership Applications Admin Page
 */
function kilismile_partnership_applications_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_partnership_applications';
    
    // Handle status updates
    if (isset($_POST['update_status']) && isset($_POST['application_id']) && isset($_POST['new_status'])) {
        $application_id = intval($_POST['application_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        $wpdb->update(
            $table_name,
            array('status' => $new_status),
            array('id' => $application_id),
            array('%s'),
            array('%d')
        );
        
        echo '<div class="notice notice-success"><p>Status updated successfully!</p></div>';
    }
    
    // Get all applications
    $applications = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC");
    
    ?>
    <div class="wrap">
        <h1><?php _e('Partnership Applications', 'kilismile'); ?></h1>
        
        <div class="partnership-stats" style="display: flex; gap: 20px; margin: 20px 0;">
            <?php
            $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $pending = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
            $approved = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'approved'");
            $rejected = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'");
            ?>
            <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 120px;">
                <div style="font-size: 2rem; font-weight: bold; color: #2271b1;"><?php echo $total; ?></div>
                <div style="color: #666;">Total Applications</div>
            </div>
            <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 120px;">
                <div style="font-size: 2rem; font-weight: bold; color: #dba617;"><?php echo $pending; ?></div>
                <div style="color: #666;">Pending</div>
            </div>
            <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 120px;">
                <div style="font-size: 2rem; font-weight: bold; color: #00a32a;"><?php echo $approved; ?></div>
                <div style="color: #666;">Approved</div>
            </div>
            <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 120px;">
                <div style="font-size: 2rem; font-weight: bold; color: #d63638;"><?php echo $rejected; ?></div>
                <div style="color: #666;">Rejected</div>
            </div>
        </div>
        
        <?php if (empty($applications)): ?>
            <div class="notice notice-info">
                <p><?php _e('No partnership applications yet.', 'kilismile'); ?></p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Organization', 'kilismile'); ?></th>
                        <th><?php _e('Contact Person', 'kilismile'); ?></th>
                        <th><?php _e('Email', 'kilismile'); ?></th>
                        <th><?php _e('Partnership Type', 'kilismile'); ?></th>
                        <th><?php _e('Budget Range', 'kilismile'); ?></th>
                        <th><?php _e('Status', 'kilismile'); ?></th>
                        <th><?php _e('Submitted', 'kilismile'); ?></th>
                        <th><?php _e('Actions', 'kilismile'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo esc_html($app->organization_name); ?></strong></td>
                            <td><?php echo esc_html($app->contact_person); ?></td>
                            <td><a href="mailto:<?php echo esc_attr($app->email); ?>"><?php echo esc_html($app->email); ?></a></td>
                            <td>
                                <span class="partnership-type-badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">
                                    <?php echo esc_html(ucwords(str_replace('_', ' ', $app->partnership_type))); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($app->annual_budget ? ucwords(str_replace('_', ' ', $app->annual_budget)) : 'Not specified'); ?></td>
                            <td>
                                <?php
                                $status_colors = array(
                                    'pending' => '#dba617',
                                    'approved' => '#00a32a',
                                    'rejected' => '#d63638',
                                    'reviewing' => '#2271b1'
                                );
                                $status_color = isset($status_colors[$app->status]) ? $status_colors[$app->status] : '#666';
                                ?>
                                <span class="status-badge" style="background: <?php echo $status_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">
                                    <?php echo esc_html(ucfirst($app->status)); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($app->submitted_at)); ?></td>
                            <td>
                                <button type="button" class="button button-small" onclick="viewApplication(<?php echo $app->id; ?>)">
                                    <?php _e('View', 'kilismile'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Application Detail Modal -->
    <div id="application-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 800px; width: 90%; max-height: 90%; overflow-y: auto;">
            <h2 id="modal-title"></h2>
            <div id="modal-content"></div>
            <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: space-between; align-items: center;">
                <div>
                    <label for="status-select" style="margin-right: 10px;"><?php _e('Status:', 'kilismile'); ?></label>
                    <select id="status-select">
                        <option value="pending"><?php _e('Pending', 'kilismile'); ?></option>
                        <option value="reviewing"><?php _e('Under Review', 'kilismile'); ?></option>
                        <option value="approved"><?php _e('Approved', 'kilismile'); ?></option>
                        <option value="rejected"><?php _e('Rejected', 'kilismile'); ?></option>
                    </select>
                    <button id="update-status-btn" class="button button-primary" style="margin-left: 10px;">
                        <?php _e('Update Status', 'kilismile'); ?>
                    </button>
                </div>
                <button onclick="closeModal()" class="button"><?php _e('Close', 'kilismile'); ?></button>
            </div>
        </div>
    </div>
    
    <script>
    const applications = <?php echo json_encode($applications); ?>;
    
    function viewApplication(id) {
        const app = applications.find(a => a.id == id);
        if (!app) return;
        
        document.getElementById('modal-title').textContent = app.organization_name + ' - Partnership Application';
        
        const content = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <strong>Contact Person:</strong><br>
                    ${app.contact_person}
                </div>
                <div>
                    <strong>Email:</strong><br>
                    <a href="mailto:${app.email}">${app.email}</a>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <strong>Phone:</strong><br>
                    ${app.phone || 'Not provided'}
                </div>
                <div>
                    <strong>Organization Type:</strong><br>
                    ${app.organization_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <strong>Partnership Type:</strong><br>
                    ${app.partnership_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </div>
                <div>
                    <strong>Budget Range:</strong><br>
                    ${app.annual_budget ? app.annual_budget.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified'}
                </div>
            </div>
            ${app.organization_goals ? `
                <div style="margin-bottom: 20px;">
                    <strong>Organization Goals & CSR Objectives:</strong><br>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-top: 5px;">
                        ${app.organization_goals.replace(/\n/g, '<br>')}
                    </div>
                </div>
            ` : ''}
            <div style="margin-bottom: 20px;">
                <strong>Partnership Interest:</strong><br>
                <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-top: 5px;">
                    ${app.partnership_interest.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div style="color: #666; font-size: 14px;">
                <strong>Submitted:</strong> ${new Date(app.submitted_at).toLocaleString()}
            </div>
        `;
        
        document.getElementById('modal-content').innerHTML = content;
        document.getElementById('status-select').value = app.status;
        
        // Set up status update handler
        document.getElementById('update-status-btn').onclick = function() {
            updateApplicationStatus(app.id, document.getElementById('status-select').value);
        };
        
        document.getElementById('application-modal').style.display = 'block';
    }
    
    function closeModal() {
        document.getElementById('application-modal').style.display = 'none';
    }
    
    function updateApplicationStatus(id, status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="application_id" value="${id}">
            <input type="hidden" name="new_status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
    
    // Close modal on outside click
    document.getElementById('application-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
    
    <style>
    .stat-card {
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .partnership-type-badge,
    .status-badge {
        display: inline-block;
    }
    </style>
    <?php
}

function kilismile_azampay_settings_page() {
    if (current_user_can('manage_options')) {
        wp_safe_redirect(admin_url('admin.php?page=kilismile-payment-gateways'));
        exit;
    }

    if (isset($_POST['submit'])) {
        // Save settings
        update_option('kilismile_azampay_test_mode', sanitize_text_field($_POST['test_mode']));
        
        // Sandbox settings
        update_option('kilismile_azampay_sandbox_app_name', sanitize_text_field($_POST['sandbox_app_name']));
        update_option('kilismile_azampay_sandbox_client_id', sanitize_text_field($_POST['sandbox_client_id']));
        update_option('kilismile_azampay_sandbox_client_secret', sanitize_text_field($_POST['sandbox_client_secret']));
        update_option('kilismile_azampay_sandbox_api_key', sanitize_text_field($_POST['sandbox_api_key']));
        
        // Live settings
        update_option('kilismile_azampay_live_app_name', sanitize_text_field($_POST['live_app_name']));
        update_option('kilismile_azampay_live_client_id', sanitize_text_field($_POST['live_client_id']));
        update_option('kilismile_azampay_live_client_secret', sanitize_text_field($_POST['live_client_secret']));
        update_option('kilismile_azampay_live_api_key', sanitize_text_field($_POST['live_api_key']));
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    // Get current settings
    $test_mode = get_option('kilismile_azampay_test_mode', 'yes');
    $sandbox_app_name = get_option('kilismile_azampay_sandbox_app_name', 'KiliSmile-Sandbox');
    $sandbox_client_id = get_option('kilismile_azampay_sandbox_client_id', '');
    $sandbox_client_secret = get_option('kilismile_azampay_sandbox_client_secret', '');
    $sandbox_api_key = get_option('kilismile_azampay_sandbox_api_key', '');
    
    $live_app_name = get_option('kilismile_azampay_live_app_name', '');
    $live_client_id = get_option('kilismile_azampay_live_client_id', '');
    $live_client_secret = get_option('kilismile_azampay_live_client_secret', '');
    $live_api_key = get_option('kilismile_azampay_live_api_key', '');
    
    ?>
    <div class="wrap">
        <h1>KiliSmile AzamPay Settings</h1>
        
        <div class="notice notice-info">
            <p><strong>Important:</strong> Configure your AzamPay credentials below. For sandbox testing, register at <a href="https://developers.azampay.co.tz" target="_blank">AzamPay Developer Portal</a>.</p>
        </div>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Test Mode</th>
                    <td>
                        <select name="test_mode">
                            <option value="yes" <?php selected($test_mode, 'yes'); ?>>Yes (Sandbox)</option>
                            <option value="no" <?php selected($test_mode, 'no'); ?>>No (Live)</option>
                        </select>
                        <p class="description">Enable test mode to use AzamPay sandbox environment.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Sandbox Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">App Name</th>
                    <td>
                        <input type="text" name="sandbox_app_name" value="<?php echo esc_attr($sandbox_app_name); ?>" class="regular-text" />
                        <p class="description">Your sandbox application name from AzamPay developer portal.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Client ID</th>
                    <td>
                        <input type="text" name="sandbox_client_id" value="<?php echo esc_attr($sandbox_client_id); ?>" class="regular-text" />
                        <p class="description">Your sandbox client ID from AzamPay developer portal.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Client Secret</th>
                    <td>
                        <input type="password" name="sandbox_client_secret" value="<?php echo esc_attr($sandbox_client_secret); ?>" class="regular-text" />
                        <p class="description">Your sandbox client secret from AzamPay developer portal.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="text" name="sandbox_api_key" value="<?php echo esc_attr($sandbox_api_key); ?>" class="regular-text" />
                        <p class="description">Your sandbox API key (X-API-Key header).</p>
                    </td>
                </tr>
            </table>
            
            <h2>Live Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">App Name</th>
                    <td>
                        <input type="text" name="live_app_name" value="<?php echo esc_attr($live_app_name); ?>" class="regular-text" />
                        <p class="description">Your live application name after KYC approval.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Client ID</th>
                    <td>
                        <input type="text" name="live_client_id" value="<?php echo esc_attr($live_client_id); ?>" class="regular-text" />
                        <p class="description">Your live client ID after KYC approval.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Client Secret</th>
                    <td>
                        <input type="password" name="live_client_secret" value="<?php echo esc_attr($live_client_secret); ?>" class="regular-text" />
                        <p class="description">Your live client secret after KYC approval.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="text" name="live_api_key" value="<?php echo esc_attr($live_api_key); ?>" class="regular-text" />
                        <p class="description">Your live API key (X-API-Key header).</p>
                    </td>
                </tr>
            </table>
            
            <h2>Callback Information</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Callback URL</th>
                    <td>
                        <code><?php echo home_url('/azampay/callback/'); ?></code>
                        <p class="description">Configure this URL in your AzamPay dashboard for payment callbacks.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * KiliSmile Tools Admin Page
 */
function kilismile_tools_page() {
    if (isset($_POST['create_pages'])) {
        kilismile_create_default_pages();
        flush_rewrite_rules();
        echo '<div class="notice notice-success"><p>Pages created successfully! Please check your site.</p></div>';
    }
    
    if (isset($_POST['flush_permalinks'])) {
        flush_rewrite_rules();
        echo '<div class="notice notice-success"><p>Permalinks flushed successfully!</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>KiliSmile Tools</h1>
        
        <div class="card" style="max-width: 600px;">
            <h2>Page Management</h2>
            <p>Use these tools to create missing pages or fix permalink issues.</p>
            
            <form method="post" style="margin-bottom: 20px;">
                <input type="hidden" name="create_pages" value="1">
                <button type="submit" class="button button-primary">
                    Create Missing Pages
                </button>
                <p class="description">Creates the "Become a Partner" page if it doesn't exist.</p>
            </form>
            
            <form method="post">
                <input type="hidden" name="flush_permalinks" value="1">
                <button type="submit" class="button button-secondary">
                    Flush Permalinks
                </button>
                <p class="description">Refreshes URL rewrite rules. Use this if pages return 404 errors.</p>
            </form>
        </div>
        
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <h2>Page Status</h2>
            <?php
            $partner_page = get_page_by_path('become-partner');
            if ($partner_page) {
                echo '<p style="color: green;">✓ Become a Partner page exists (ID: ' . $partner_page->ID . ')</p>';
                echo '<p><a href="' . get_permalink($partner_page->ID) . '" target="_blank">View Page</a> | ';
                echo '<a href="' . admin_url('post.php?post=' . $partner_page->ID . '&action=edit') . '">Edit Page</a></p>';
            } else {
                echo '<p style="color: red;">✗ Become a Partner page does not exist</p>';
            }
            ?>
        </div>
    </div>
    <?php
}
