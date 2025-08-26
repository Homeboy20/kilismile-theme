<?php
/**
 * Kili Smile Organization Theme Functions
 *
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
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
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'kilismile'),
        'footer'  => __('Footer Menu', 'kilismile'),
        'social'  => __('Social Links', 'kilismile'),
    ));
    
    // Add translation support
    load_theme_textdomain('kilismile', get_template_directory() . '/languages');
    
    // Set content width
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'kilismile_setup');

/**
 * Enqueue Scripts and Styles
 */
function kilismile_scripts() {
    // Enqueue styles
    wp_enqueue_style('kilismile-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('kilismile-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(), '5.15.3');
    
    // Newsletter CSS - load only on newsletter page
    if (is_page('newsletter') || is_post_type_archive('newsletter') || is_singular('newsletter')) {
        wp_enqueue_style('kilismile-newsletter', get_template_directory_uri() . '/assets/css/newsletter.css', array(), '1.0.0');
        wp_enqueue_script('kilismile-newsletter-js', get_template_directory_uri() . '/assets/js/newsletter.js', array('jquery'), '1.0.0', true);
    }
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('kilismile-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    // Add Ajax URL for newsletter form
    wp_localize_script('kilismile-main', 'kilismile_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
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
    ?>
    <script type="text/javascript">
    (function($) {
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
        'default'           => '0763495575/0735495575',
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
            'content' => 'Learn about Kili Smile Organization, our mission, vision, and the team dedicated to improving oral health in Tanzania.',
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
            'content' => 'Get in touch with Kili Smile Organization. We would love to hear from you and answer any questions you may have.',
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
            'content' => 'Start your own fundraising campaign to support Kili Smile Organization and help us reach more communities.',
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

                error_log("Kili Smile: Created page '{$page_data['title']}' with slug '{$slug}'");
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
<p class="newsletter-intro">Stay connected with Kili Smile Organization and our mission to improve oral health in Tanzania. Subscribe to our newsletter to receive regular updates about our programs, success stories, upcoming events, and ways you can make a difference.</p>
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
            
            error_log("Kili Smile: Created enhanced newsletter page with ID: $page_id");
        }
    } else {
        // Ensure template is set correctly
        $template = get_post_meta($newsletter_page->ID, '_wp_page_template', true);
        
        if ($template !== 'page-newsletter.php') {
            update_post_meta($newsletter_page->ID, '_wp_page_template', 'page-newsletter.php');
            error_log("Kili Smile: Updated newsletter page template to 'page-newsletter.php'");
        }
        
        // Only update content if it's the default basic content
        $current_content = $newsletter_page->post_content;
        if (trim($current_content) === 'Subscribe to our newsletter to stay updated with our latest programs, success stories, and ways to get involved.') {
            wp_update_post(array(
                'ID' => $newsletter_page->ID,
                'post_content' => $enhanced_content
            ));
            error_log("Kili Smile: Enhanced newsletter page content");
        }
    }
}

// Run the fix on init to ensure the newsletter page exists
add_action('init', 'kilismile_fix_newsletter_page');

/**
 * Include additional functionality files
 */
$inc_files = array(
    'inc/customizer.php',
    'inc/customizer-contact.php', 
    'inc/customizer-header.php',
    'inc/template-functions.php',
    'inc/email-system.php',
    'inc/donation-functions.php',
    'inc/newsletter-functions.php',
    'inc/contact-functions.php',
    'inc/contact-email-templates.php'
);

foreach ($inc_files as $file) {
    $file_path = get_template_directory() . '/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log("Kili Smile Theme: File not found - $file_path");
    }
}

?>
