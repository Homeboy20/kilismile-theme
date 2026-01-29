<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
    <meta name="keywords" content="oral health, Tanzania, NGO, health education, Kilimanjaro, Moshi, children health, elderly care">
    <meta name="author" content="Kilismile Organization">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta property="og:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url('/')); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/apple-touch-icon.png">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <?php wp_head(); ?>
    
    <!-- Dynamic Header Styles -->
    <style>
        :root {
            --header-bg-color: <?php echo esc_attr(get_theme_mod('kilismile_header_bg_color', '#ffffff')); ?>;
            --header-text-color: <?php echo esc_attr(get_theme_mod('kilismile_header_text_color', '#333333')); ?>;
            --header-transparency: <?php echo esc_attr(get_theme_mod('kilismile_header_transparency', 0.95)); ?>;
            --header-height: <?php echo esc_attr(get_theme_mod('kilismile_header_height', 80)); ?>px;
            --menu-text-color: <?php echo esc_attr(get_theme_mod('kilismile_menu_text_color', '#333333')); ?>;
            --menu-hover-color: <?php echo esc_attr(get_theme_mod('kilismile_menu_hover_color', '#4CAF50')); ?>;
            --dropdown-bg-color: <?php echo esc_attr(get_theme_mod('kilismile_dropdown_bg_color', '#ffffff')); ?>;
            --cta-bg-color: <?php echo esc_attr(get_theme_mod('kilismile_cta_bg_color', '#4CAF50')); ?>;
            --cta-text-color: <?php echo esc_attr(get_theme_mod('kilismile_cta_text_color', '#ffffff')); ?>;
            --logo-size: <?php echo esc_attr(get_theme_mod('kilismile_logo_size', 50)); ?>px;
        }
        
        /* Header Background Styles */
        .site-header {
            min-height: var(--header-height);
            transition: all 0.3s ease;
            color: var(--header-text-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            
            <?php 
            $header_bg_type = get_theme_mod('kilismile_header_bg_type', 'solid');
            $header_layout = get_theme_mod('kilismile_header_layout', 'default');
            
            if ($header_bg_type === 'transparent' || $header_layout === 'transparent'): 
                $bg_color = get_theme_mod('kilismile_header_bg_color', '#ffffff');
                $transparency = get_theme_mod('kilismile_header_transparency', 0.95);
                
                // Convert hex to RGB
                $hex = str_replace('#', '', $bg_color);
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            ?>
                background: rgba(<?php echo $r . ', ' . $g . ', ' . $b . ', ' . $transparency; ?>);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            <?php else: ?>
                background: var(--header-bg-color);
                border-bottom: 3px solid var(--primary-green, #4CAF50);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            <?php endif; ?>
        }
        
        /* Transparent Header Specific Styles */
        <?php if ($header_bg_type === 'transparent' || $header_layout === 'transparent'): ?>
        .site-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
        }
        
        .site-header .site-title,
        .site-header .menu-link,
        .site-header .header-contact a {
            color: var(--header-text-color);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .site-header .menu-link:hover {
            background: rgba(var(--menu-hover-color), 0.2);
            color: var(--header-text-color);
        }
        
        /* Ensure content doesn't hide behind transparent header */
        #content {
            margin-top: var(--header-height);
        }
        
        /* Sticky behavior for transparent header */
        .site-header.is-sticky {
            position: fixed !important;
            background: rgba(<?php echo $r . ', ' . $g . ', ' . $b . ', 0.95'; ?>) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        <?php endif; ?>
        
        /* Logo Sizing */
        .site-logo img {
            width: var(--logo-size);
            height: var(--logo-size);
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            object-fit: contain;
        }
        
        .site-logo {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block;
        }
        
        /* Menu Colors */
        .main-navigation .menu-link {
            color: var(--menu-text-color);
        }
        
        .main-navigation .menu-link:hover {
            background: var(--menu-hover-color);
            color: white;
        }
        
        /* Dropdown Styling */
        .dropdown-menu {
            background: var(--dropdown-bg-color);
        }
        
        /* CTA Button Styling */
        .donate-btn {
            background: var(--cta-bg-color);
            color: var(--cta-text-color);
        }
        
        /* Header Styling */
        .site-header {
            <?php if (get_theme_mod('kilismile_transparent_header', false)): ?>
                background: rgba(<?php 
                    $bg_color = get_theme_mod('kilismile_header_bg_color', '#ffffff');
                    $rgb = sscanf($bg_color, "#%02x%02x%02x");
                    echo implode(', ', $rgb);
                ?>, var(--header-transparency)) !important;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            <?php endif; ?>
            
            <?php if (get_theme_mod('kilismile_header_shadow', true)): ?>
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            <?php endif; ?>
            
            <?php if (get_theme_mod('kilismile_header_border', false)): ?>
                border-bottom: 1px solid <?php echo esc_attr(get_theme_mod('kilismile_header_border_color', '#e0e0e0')); ?>;
            <?php endif; ?>
        }
    </style>
        
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip to Content Link for Accessibility -->
<a class="skip-link screen-reader-text" href="#main"><?php _e('Skip to content', 'kilismile'); ?></a>

<div id="page" class="site">
    <header id="masthead" class="site-header<?php 
        $header_bg_type = get_theme_mod('kilismile_header_bg_type', 'solid');
        $header_layout = get_theme_mod('kilismile_header_layout', 'default');
        
        // Allow testing transparent header with URL parameter
        if (isset($_GET['transparent']) && $_GET['transparent'] === '1') {
            echo ' transparent-header';
        } elseif ($header_bg_type === 'transparent' || $header_layout === 'transparent') {
            echo ' transparent-header';
        }
    ?>">
        <div class="container">
            <div class="header-container">
                <!-- Site Logo and Branding -->
                <div class="site-branding">
                    <?php if (has_custom_logo()) : ?>
                        <?php
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                        if ($logo) {
                            $logo_url = set_url_scheme($logo[0], is_ssl() ? 'https' : 'http');
                            echo '<a href="' . esc_url(home_url('/')) . '" class="site-logo" rel="home">';
                            echo '<img id="siteLogo" src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="custom-logo">';
                            echo '</a>';
                        }
                        ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                            <img id="siteLogo" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.svg" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 class="custom-logo">
                        </a>
                    <?php endif; ?>
                    
                    <div class="site-info">
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration: none; color: inherit;">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php 
                        $tagline = get_theme_mod('kilismile_tagline', get_bloginfo('description'));
                        if ($tagline && get_theme_mod('kilismile_header_layout') !== 'minimal') : 
                        ?>
                            <p class="site-tagline"><?php echo esc_html($tagline); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Header Contact Info -->
                <?php if (get_theme_mod('kilismile_show_header_contact', false)) : ?>
                    <div class="header-contact">
                        <?php 
                        $phone = get_theme_mod('kilismile_header_phone', '');
                        $email = get_theme_mod('kilismile_header_email', '');
                        if ($phone) : 
                        ?>
                            <div class="phone">
                                <a href="tel:<?php echo esc_attr(str_replace(['/', ' '], '', $phone)); ?>">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($email) : ?>
                            <div class="email">
                                <a href="mailto:<?php echo esc_attr($email); ?>">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php _e('Toggle navigation', 'kilismile'); ?>">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>

                <!-- Primary Navigation -->
                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e('Primary Menu', 'kilismile'); ?>">
                    <ul id="primary-menu" class="main-menu">
                        <!-- About Dropdown -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/about')); ?>" class="menu-link">
                                <?php _e('About', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('Our Story', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/about#mission')); ?>"><?php _e('Mission & Vision', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/about#team')); ?>"><?php _e('Our Team', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/about#impact')); ?>"><?php _e('Our Impact', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Programs Dropdown -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/programs')); ?>" class="menu-link">
                                <?php _e('Programs', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/programs')); ?>"><?php _e('All Programs', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/programs#healthcare')); ?>"><?php _e('Healthcare Services', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/programs#education')); ?>"><?php _e('Health Education', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/programs#community')); ?>"><?php _e('Community Outreach', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/programs#prevention')); ?>"><?php _e('Prevention Programs', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Partners Menu -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/partners')); ?>" class="menu-link">
                                <?php _e('Partners', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/partners')); ?>"><?php _e('All Partners', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/corporate')); ?>"><?php _e('Corporate Partnerships', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/partnerships')); ?>"><?php _e('Community Partners', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/corporate-sponsors')); ?>"><?php _e('Strategic Partners', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Get Involved Dropdown -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/volunteer')); ?>" class="menu-link">
                                <?php _e('Get Involved', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/volunteer')); ?>"><?php _e('Volunteer', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(function_exists('kilismile_get_donation_page_url_legacy') ? kilismile_get_donation_page_url_legacy() : home_url('/donation/')); ?>"><?php _e('Donate', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/fundraising')); ?>"><?php _e('Fundraising', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/become-partner')); ?>"><?php _e('Become a Partner', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Resources Dropdown -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/resources')); ?>" class="menu-link">
                                <?php _e('Resources', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/gallery')); ?>"><?php _e('Photo Gallery', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/resources#health-tips')); ?>"><?php _e('Health Tips', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/resources#downloads')); ?>"><?php _e('Downloads', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/resources#faqs')); ?>"><?php _e('FAQs', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/resources#testimonials')); ?>"><?php _e('Testimonials', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- News & Events Dropdown -->
                        <li class="menu-item has-dropdown">
                            <a href="<?php echo esc_url(home_url('/news')); ?>" class="menu-link">
                                <?php _e('News', 'kilismile'); ?>
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo esc_url(home_url('/news')); ?>"><?php _e('Latest News', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/news#events')); ?>"><?php _e('Upcoming Events', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/news#stories')); ?>"><?php _e('Success Stories', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/media')); ?>"><?php _e('Media Center', 'kilismile'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/newsletter')); ?>"><?php _e('Newsletter', 'kilismile'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Contact -->
                        <li class="menu-item">
                            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="menu-link">
                                <?php _e('Contact', 'kilismile'); ?>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- CTA Button -->
                    <?php if (get_theme_mod('kilismile_show_cta_button', true)) : ?>
                        <?php
                        $kilismile_cta_default_url = function_exists('kilismile_get_donation_page_url_legacy')
                            ? kilismile_get_donation_page_url_legacy()
                            : home_url('/donation/');

                        $kilismile_cta_url = get_theme_mod('kilismile_cta_url', $kilismile_cta_default_url);

                        if (is_string($kilismile_cta_url) && preg_match('~/(donate|donations)/?$~i', $kilismile_cta_url)) {
                            $kilismile_cta_url = $kilismile_cta_default_url;
                        }
                        ?>
                        <a href="<?php echo esc_url($kilismile_cta_url); ?>" 
                           class="donate-btn" 
                           aria-label="<?php _e('Make a donation', 'kilismile'); ?>">
                            <i class="fas fa-heart" aria-hidden="true"></i>
                            <?php echo esc_html(get_theme_mod('kilismile_cta_text', 'Donate Now')); ?>
                        </a>
                    <?php endif; ?>

                    <!-- Header Social Media -->
                    <?php if (get_theme_mod('kilismile_show_header_social', false)) : ?>
                        <div class="header-social">
                            <?php
                            $social_networks = array(
                                'facebook'  => 'fab fa-facebook-f',
                                'twitter'   => 'fab fa-twitter',
                                'instagram' => 'fab fa-instagram',
                                'linkedin'  => 'fab fa-linkedin-in',
                                'youtube'   => 'fab fa-youtube',
                                'whatsapp'  => 'fab fa-whatsapp',
                            );

                            foreach ($social_networks as $network => $icon) {
                                $url = get_theme_mod("kilismile_header_{$network}_url", '');
                                if ($url) {
                                    echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . ucfirst($network) . '">';
                                    echo '<i class="' . esc_attr($icon) . '" aria-hidden="true"></i>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Language Switcher (if multilingual) -->
                    <div class="language-switcher" style="display: none;">
                        <button class="lang-toggle" aria-label="<?php _e('Choose language', 'kilismile'); ?>">
                            <i class="fas fa-globe" aria-hidden="true"></i>
                            <span>EN</span>
                        </button>
                        <ul class="lang-menu">
                            <li><a href="#" data-lang="en">English</a></li>
                            <li><a href="#" data-lang="sw">Kiswahili</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Emergency Contact Bar (Optional) -->
    <?php if (get_theme_mod('kilismile_show_emergency_bar', false)) : ?>
        <div class="emergency-bar" style="background: #d32f2f; color: white; padding: 10px 0; text-align: center; font-size: 0.9rem;">
            <div class="container">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <?php _e('Emergency Health Services:', 'kilismile'); ?>
                <a href="tel:112" style="color: white; font-weight: bold; margin-left: 10px;">112</a>
            </div>
        </div>
    <?php endif; ?>

    <div id="content" class="site-content">

<?php
/**
 * Fallback menu function
 */
function kilismile_fallback_menu() {
    echo '<ul id="primary-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . __('About Us', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/programs')) . '">' . __('Our Work', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/get-involved')) . '">' . __('Get Involved', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/resources')) . '">' . __('Resources', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/news')) . '">' . __('News & Updates', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . __('Contact', 'kilismile') . '</a></li>';
    echo '</ul>';
}
?>


