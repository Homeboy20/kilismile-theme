<?php
/**
 * Enhanced KiliSmile Theme Settings Framework
 * 
 * A comprehensive settings system with modular architecture,
 * advanced UI components, and extensive customization options
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * KiliSmile Settings Framework Class
 */
class KiliSmile_Settings_Framework {
    
    private $version = '3.0.0';
    private $settings_page_hook;
    private $settings_sections = array();
    private $settings_fields = array();
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_ajax_kilismile_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_kilismile_reset_settings', array($this, 'ajax_reset_settings'));
        add_action('wp_ajax_kilismile_export_settings', array($this, 'ajax_export_settings'));
        add_action('wp_ajax_kilismile_import_settings', array($this, 'ajax_import_settings'));
        
        // Delay settings structure initialization until init hook to avoid early translation loading
        add_action('init', array($this, 'init_settings_structure'));
    }
    
    /**
     * Initialize Settings Structure
     */
    public function init_settings_structure() {
        $this->settings_sections = array(
            'general' => array(
                'title' => __('General Settings', 'kilismile'),
                'icon' => 'dashicons-admin-generic',
                'description' => __('Basic theme configuration and global settings', 'kilismile'),
                'priority' => 10
            ),
            'appearance' => array(
                'title' => __('Appearance & Layout', 'kilismile'),
                'icon' => 'dashicons-admin-appearance',
                'description' => __('Visual design, colors, typography, and layout options', 'kilismile'),
                'priority' => 20
            ),
            'header' => array(
                'title' => __('Header & Navigation', 'kilismile'),
                'icon' => 'dashicons-menu',
                'description' => __('Header layout, logo, navigation menu settings', 'kilismile'),
                'priority' => 30
            ),
            'content' => array(
                'title' => __('Content & Pages', 'kilismile'),
                'icon' => 'dashicons-admin-page',
                'description' => __('Page layouts, content display, and archive settings', 'kilismile'),
                'priority' => 40
            ),
            'donations' => array(
                'title' => __('Donation System', 'kilismile'),
                'icon' => 'dashicons-heart',
                'description' => __('Donation forms, goals, campaigns, and payment methods', 'kilismile'),
                'priority' => 50
            ),
            'social' => array(
                'title' => __('Social & Contact', 'kilismile'),
                'icon' => 'dashicons-share',
                'description' => __('Social media integration, contact forms, and communication', 'kilismile'),
                'priority' => 60
            ),
            'performance' => array(
                'title' => __('Performance & SEO', 'kilismile'),
                'icon' => 'dashicons-performance',
                'description' => __('Speed optimization, SEO settings, and analytics', 'kilismile'),
                'priority' => 70
            ),
            'payments' => array(
                'title' => __('Payments', 'kilismile'),
                'icon' => 'dashicons-cart',
                'description' => __('Configure payment gateways and AzamPay integration.', 'kilismile'),
                'priority' => 75
            ),
            'advanced' => array(
                'title' => __('Advanced Options', 'kilismile'),
                'icon' => 'dashicons-admin-tools',
                'description' => __('Developer options, custom code, and advanced features', 'kilismile'),
                'priority' => 80
            )
        );
        
        $this->init_settings_fields();
    }
    
    /**
     * Initialize Settings Fields
     */
    private function init_settings_fields() {
        // General Settings
        $this->add_settings_field('general', 'site_mode', array(
            'type' => 'select',
            'title' => __('Site Mode', 'kilismile'),
            'description' => __('Choose the primary mode for your website', 'kilismile'),
            'options' => array(
                'charity' => __('Charity Organization', 'kilismile'),
                'ngo' => __('Non-Governmental Organization', 'kilismile'),
                'foundation' => __('Foundation', 'kilismile'),
                'nonprofit' => __('Nonprofit Organization', 'kilismile'),
                'community' => __('Community Group', 'kilismile')
            ),
            'default' => 'charity'
        ));
        
        $this->add_settings_field('general', 'organization_name', array(
            'type' => 'text',
            'title' => __('Organization Name', 'kilismile'),
            'description' => __('Official name of your organization', 'kilismile'),
            'default' => get_bloginfo('name')
        ));
        
        $this->add_settings_field('general', 'organization_tagline', array(
            'type' => 'textarea',
            'title' => __('Organization Tagline', 'kilismile'),
            'description' => __('Short description or mission statement', 'kilismile'),
            'default' => get_bloginfo('description')
        ));
        
        $this->add_settings_field('general', 'contact_info', array(
            'type' => 'group',
            'title' => __('Contact Information', 'kilismile'),
            'fields' => array(
                'phone' => array(
                    'type' => 'text',
                    'title' => __('Phone Number', 'kilismile'),
                    'placeholder' => '+1 (555) 123-4567'
                ),
                'email' => array(
                    'type' => 'email',
                    'title' => __('Email Address', 'kilismile'),
                    'placeholder' => 'contact@kilismile.org'
                ),
                'address' => array(
                    'type' => 'textarea',
                    'title' => __('Physical Address', 'kilismile'),
                    'rows' => 3
                )
            )
        ));
        
        // Appearance Settings
        $this->add_settings_field('appearance', 'color_scheme', array(
            'type' => 'radio_image',
            'title' => __('Color Scheme', 'kilismile'),
            'description' => __('Choose a pre-designed color scheme', 'kilismile'),
            'options' => array(
                'default' => array(
                    'label' => __('Default Blue', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/color-scheme-default.png'
                ),
                'green' => array(
                    'label' => __('Nature Green', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/color-scheme-green.png'
                ),
                'red' => array(
                    'label' => __('Charity Red', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/color-scheme-red.png'
                ),
                'orange' => array(
                    'label' => __('Hope Orange', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/color-scheme-orange.png'
                ),
                'custom' => array(
                    'label' => __('Custom Colors', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/color-scheme-custom.png'
                )
            ),
            'default' => 'default'
        ));
        
        $this->add_settings_field('appearance', 'custom_colors', array(
            'type' => 'color_palette',
            'title' => __('Custom Color Palette', 'kilismile'),
            'description' => __('Define your custom brand colors', 'kilismile'),
            'colors' => array(
                'primary' => array(
                    'label' => __('Primary Color', 'kilismile'),
                    'default' => '#2271b1'
                ),
                'secondary' => array(
                    'label' => __('Secondary Color', 'kilismile'),
                    'default' => '#00a32a'
                ),
                'accent' => array(
                    'label' => __('Accent Color', 'kilismile'),
                    'default' => '#ff6b35'
                ),
                'text' => array(
                    'label' => __('Text Color', 'kilismile'),
                    'default' => '#333333'
                ),
                'background' => array(
                    'label' => __('Background Color', 'kilismile'),
                    'default' => '#ffffff'
                )
            ),
            'conditional' => array(
                'field' => 'color_scheme',
                'value' => 'custom'
            )
        ));
        
        $this->add_settings_field('appearance', 'typography', array(
            'type' => 'typography',
            'title' => __('Typography Settings', 'kilismile'),
            'description' => __('Choose fonts and text styling', 'kilismile'),
            'fonts' => array(
                'headings' => array(
                    'label' => __('Headings Font', 'kilismile'),
                    'default' => 'Roboto'
                ),
                'body' => array(
                    'label' => __('Body Font', 'kilismile'),
                    'default' => 'Open Sans'
                )
            )
        ));
        
        // Header Settings
        $this->add_settings_field('header', 'header_layout', array(
            'type' => 'radio_image',
            'title' => __('Header Layout', 'kilismile'),
            'options' => array(
                'standard' => array(
                    'label' => __('Standard Layout', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/header-standard.png'
                ),
                'centered' => array(
                    'label' => __('Centered Layout', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/header-centered.png'
                ),
                'minimal' => array(
                    'label' => __('Minimal Layout', 'kilismile'),
                    'image' => get_template_directory_uri() . '/admin/images/header-minimal.png'
                )
            ),
            'default' => 'standard'
        ));
        
        $this->add_settings_field('header', 'logo_settings', array(
            'type' => 'group',
            'title' => __('Logo Settings', 'kilismile'),
            'fields' => array(
                'size' => array(
                    'type' => 'slider',
                    'title' => __('Logo Size', 'kilismile'),
                    'min' => 20,
                    'max' => 200,
                    'step' => 5,
                    'default' => 60,
                    'suffix' => 'px'
                ),
                'border_radius' => array(
                    'type' => 'slider',
                    'title' => __('Border Radius', 'kilismile'),
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                    'default' => 0,
                    'suffix' => '%'
                ),
                'retina_support' => array(
                    'type' => 'toggle',
                    'title' => __('Retina Support', 'kilismile'),
                    'description' => __('Upload 2x size logo for high-resolution displays', 'kilismile'),
                    'default' => true
                )
            )
        ));
        
        // Donation Settings
        $this->add_settings_field('donations', 'donation_system', array(
            'type' => 'toggle',
            'title' => __('Enable Donation System', 'kilismile'),
            'description' => __('Turn on/off the entire donation functionality', 'kilismile'),
            'default' => true
        ));
        
        $this->add_settings_field('donations', 'donation_goals', array(
            'type' => 'repeater',
            'title' => __('Donation Goals', 'kilismile'),
            'description' => __('Set up multiple fundraising goals', 'kilismile'),
            'fields' => array(
                'title' => array(
                    'type' => 'text',
                    'title' => __('Goal Title', 'kilismile'),
                    'placeholder' => 'e.g., Monthly Operations'
                ),
                'amount' => array(
                    'type' => 'number',
                    'title' => __('Target Amount', 'kilismile'),
                    'min' => 0,
                    'step' => 0.01
                ),
                'currency' => array(
                    'type' => 'select',
                    'title' => __('Currency', 'kilismile'),
                    'options' => array(
                        'USD' => 'USD ($)',
                        'TZS' => 'TZS (TSh)',
                        'EUR' => 'EUR (€)',
                        'GBP' => 'GBP (£)'
                    )
                ),
                'deadline' => array(
                    'type' => 'date',
                    'title' => __('Deadline', 'kilismile')
                )
            ),
            'conditional' => array(
                'field' => 'donation_system',
                'value' => true
            )
        ));
        
        // Social Settings
        $this->add_settings_field('social', 'social_media', array(
            'type' => 'social_links',
            'title' => __('Social Media Links', 'kilismile'),
            'description' => __('Add your social media profiles', 'kilismile'),
            'networks' => array(
                'facebook' => __('Facebook', 'kilismile'),
                'twitter' => __('Twitter', 'kilismile'),
                'instagram' => __('Instagram', 'kilismile'),
                'linkedin' => __('LinkedIn', 'kilismile'),
                'youtube' => __('YouTube', 'kilismile'),
                'whatsapp' => __('WhatsApp', 'kilismile')
            )
        ));
        
        // Performance Settings
        $this->add_settings_field('performance', 'optimization', array(
            'type' => 'checkbox_group',
            'title' => __('Performance Optimizations', 'kilismile'),
            'options' => array(
                'lazy_loading' => __('Enable Lazy Loading for Images', 'kilismile'),
                'minify_css' => __('Minify CSS Files', 'kilismile'),
                'minify_js' => __('Minify JavaScript Files', 'kilismile'),
                'cache_assets' => __('Enable Asset Caching', 'kilismile'),
                'compress_images' => __('Compress Images on Upload', 'kilismile')
            ),
            'default' => array('lazy_loading', 'cache_assets')
        ));
        
        // Advanced Settings
        $this->add_settings_field('advanced', 'custom_css', array(
            'type' => 'code_editor',
            'title' => __('Custom CSS', 'kilismile'),
            'description' => __('Add custom CSS styles', 'kilismile'),
            'language' => 'css',
            'theme' => 'monokai'
        ));
        
        $this->add_settings_field('advanced', 'custom_js', array(
            'type' => 'code_editor',
            'title' => __('Custom JavaScript', 'kilismile'),
            'description' => __('Add custom JavaScript code (loaded in footer)', 'kilismile'),
            'language' => 'javascript',
            'theme' => 'monokai'
        ));
    }
    
    /**
     * Add Settings Field
     */
    private function add_settings_field($section, $id, $args) {
        if (!isset($this->settings_fields[$section])) {
            $this->settings_fields[$section] = array();
        }
        
        $this->settings_fields[$section][$id] = $args;
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        $this->settings_page_hook = add_menu_page(
            __('KiliSmile Settings', 'kilismile'),
            __('KiliSmile', 'kilismile'),
            'manage_options',
            'kilismile-settings',
            array($this, 'settings_page'),
            'dashicons-heart',
            30
        );
        
        // Add submenu pages for different sections
        foreach ($this->settings_sections as $section_id => $section) {
            add_submenu_page(
                'kilismile-settings',
                $section['title'],
                $section['title'],
                'manage_options',
                'kilismile-settings#' . $section_id,
                array($this, 'settings_page')
            );
        }
    }
    
    /**
     * Register Settings
     */
    public function register_settings() {
        register_setting('kilismile_settings', 'kilismile_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));
    }
    
    /**
     * Enqueue Admin Scripts
     */
    public function admin_scripts($hook) {
        if ($hook !== $this->settings_page_hook) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'kilismile-admin-settings',
            get_template_directory_uri() . '/admin/css/settings.css',
            array(),
            $this->version
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'kilismile-admin-settings',
            get_template_directory_uri() . '/admin/js/settings.js',
            array('jquery', 'wp-color-picker', 'code-editor'),
            $this->version,
            true
        );
        
        // Localize script
        wp_localize_script('kilismile-admin-settings', 'kilismileSettings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_settings_nonce'),
            'strings' => array(
                'saving' => __('Saving...', 'kilismile'),
                'saved' => __('Settings Saved!', 'kilismile'),
                'error' => __('Error saving settings', 'kilismile'),
                'confirm_reset' => __('Are you sure you want to reset all settings?', 'kilismile'),
                'confirm_import' => __('This will overwrite your current settings. Continue?', 'kilismile')
            )
        ));
        
        // Add color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Add code editor
        wp_enqueue_code_editor(array('type' => 'text/css'));
        wp_enqueue_code_editor(array('type' => 'text/javascript'));
    }
    
    /**
     * Settings Page HTML
     */
    public function settings_page() {
        $current_settings = get_option('kilismile_settings', array());
        ?>
        <div class="wrap kilismile-settings-wrap">
            <header class="kilismile-settings-header">
                <div class="header-content">
                    <h1>
                        <span class="dashicons dashicons-heart"></span>
                        <?php _e('KiliSmile Theme Settings', 'kilismile'); ?>
                        <span class="version">v<?php echo $this->version; ?></span>
                    </h1>
                    <p class="description">
                        <?php _e('Customize your KiliSmile theme with powerful options and real-time preview', 'kilismile'); ?>
                    </p>
                </div>
                <div class="header-actions">
                    <button type="button" class="button button-secondary" id="export-settings">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export', 'kilismile'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="import-settings">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Import', 'kilismile'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="reset-settings">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Reset', 'kilismile'); ?>
                    </button>
                    <button type="button" class="button button-primary" id="save-settings">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Save All', 'kilismile'); ?>
                    </button>
                </div>
            </header>
            
            <div class="kilismile-settings-body">
                <nav class="settings-navigation">
                    <ul class="nav-tabs">
                        <?php foreach ($this->settings_sections as $section_id => $section): ?>
                        <li class="nav-tab<?php echo $section_id === 'general' ? ' active' : ''; ?>" 
                            data-section="<?php echo esc_attr($section_id); ?>">
                            <span class="dashicons <?php echo esc_attr($section['icon']); ?>"></span>
                            <span class="tab-label"><?php echo esc_html($section['title']); ?></span>
                            <span class="tab-indicator"></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                
                <main class="settings-content">
                    <form id="kilismile-settings-form" method="post">
                        <?php wp_nonce_field('kilismile_settings_action', 'kilismile_settings_nonce'); ?>
                        
                        <?php foreach ($this->settings_sections as $section_id => $section): ?>
                        <section class="settings-section<?php echo $section_id === 'general' ? ' active' : ''; ?>" 
                                id="section-<?php echo esc_attr($section_id); ?>">
                            <div class="section-header">
                                <h2>
                                    <span class="dashicons <?php echo esc_attr($section['icon']); ?>"></span>
                                    <?php echo esc_html($section['title']); ?>
                                </h2>
                                <p class="section-description">
                                    <?php echo esc_html($section['description']); ?>
                                </p>
                            </div>
                            
                            <div class="section-content">
                                <?php if (isset($this->settings_fields[$section_id])): ?>
                                    <?php foreach ($this->settings_fields[$section_id] as $field_id => $field): ?>
                                        <?php $this->render_field($section_id, $field_id, $field, $current_settings); ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </section>
                        <?php endforeach; ?>
                    </form>
                </main>
                
                <aside class="settings-sidebar">
                    <div class="sidebar-widget">
                        <h3><?php _e('Quick Actions', 'kilismile'); ?></h3>
                        <div class="quick-actions">
                            <a href="<?php echo home_url(); ?>" class="button button-secondary" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                                <?php _e('Preview Site', 'kilismile'); ?>
                            </a>
                            <a href="<?php echo admin_url('customize.php'); ?>" class="button button-secondary">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <?php _e('Customizer', 'kilismile'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3><?php _e('Settings Status', 'kilismile'); ?></h3>
                        <div class="settings-status">
                            <div class="status-item">
                                <span class="status-label"><?php _e('Last Saved:', 'kilismile'); ?></span>
                                <span class="status-value" id="last-saved">
                                    <?php echo get_option('kilismile_settings_last_saved', __('Never', 'kilismile')); ?>
                                </span>
                            </div>
                            <div class="status-item">
                                <span class="status-label"><?php _e('Auto-save:', 'kilismile'); ?></span>
                                <span class="status-value status-enabled"><?php _e('Enabled', 'kilismile'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3><?php _e('Theme Information', 'kilismile'); ?></h3>
                        <div class="theme-info">
                            <p><strong><?php _e('Version:', 'kilismile'); ?></strong> <?php echo wp_get_theme()->get('Version'); ?></p>
                            <p><strong><?php _e('Author:', 'kilismile'); ?></strong> <?php echo wp_get_theme()->get('Author'); ?></p>
                            <p><strong><?php _e('Description:', 'kilismile'); ?></strong> <?php echo wp_get_theme()->get('Description'); ?></p>
                        </div>
                    </div>
                </aside>
            </div>
            
            <!-- Hidden file input for import -->
            <input type="file" id="import-file" accept=".json" style="display: none;">
        </div>
        
        <style>
            .kilismile-settings-wrap {
                margin: 0 0 0 -20px;
                background: #f1f1f1;
                min-height: 100vh;
            }
            
            .kilismile-settings-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            .header-content h1 {
                margin: 0;
                font-size: 2.5em;
                font-weight: 300;
                display: flex;
                align-items: center;
                gap: 15px;
                color: white;
            }
            
            .header-content .version {
                font-size: 0.4em;
                background: rgba(255,255,255,0.2);
                padding: 5px 10px;
                border-radius: 15px;
                font-weight: 600;
            }
            
            .header-content .description {
                margin: 10px 0 0 0;
                opacity: 0.9;
                font-size: 1.1em;
            }
            
            .header-actions {
                display: flex;
                gap: 10px;
            }
            
            .header-actions .button {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                border: 2px solid rgba(255,255,255,0.3);
                background: rgba(255,255,255,0.1);
                color: white;
                border-radius: 25px;
                transition: all 0.3s ease;
            }
            
            .header-actions .button:hover {
                background: rgba(255,255,255,0.2);
                border-color: rgba(255,255,255,0.5);
                transform: translateY(-2px);
            }
            
            .header-actions .button-primary {
                background: #00a32a;
                border-color: #00a32a;
            }
            
            .kilismile-settings-body {
                display: grid;
                grid-template-columns: 250px 1fr 300px;
                gap: 0;
                min-height: calc(100vh - 140px);
            }
            
            .settings-navigation {
                background: white;
                border-right: 1px solid #e0e0e0;
                padding: 0;
            }
            
            .nav-tabs {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            
            .nav-tab {
                border-bottom: 1px solid #f0f0f0;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .nav-tab a {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 20px 25px;
                color: #666;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .nav-tab:hover {
                background: #f8f9fa;
            }
            
            .nav-tab.active {
                background: #2271b1;
                color: white;
            }
            
            .nav-tab.active a {
                color: white;
            }
            
            .tab-indicator {
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 0;
                background: #00a32a;
                transition: height 0.3s ease;
            }
            
            .nav-tab.active .tab-indicator {
                height: 60%;
            }
            
            .settings-content {
                background: white;
                padding: 40px;
                overflow-y: auto;
            }
            
            .settings-section {
                display: none;
            }
            
            .settings-section.active {
                display: block;
                animation: fadeIn 0.3s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .section-header {
                margin-bottom: 40px;
                padding-bottom: 20px;
                border-bottom: 2px solid #f0f0f0;
            }
            
            .section-header h2 {
                margin: 0 0 10px 0;
                font-size: 2em;
                font-weight: 300;
                color: #333;
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .section-description {
                margin: 0;
                color: #666;
                font-size: 1.1em;
            }
            
            .settings-sidebar {
                background: #f8f9fa;
                border-left: 1px solid #e0e0e0;
                padding: 30px 25px;
            }
            
            .sidebar-widget {
                background: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }
            
            .sidebar-widget h3 {
                margin: 0 0 15px 0;
                font-size: 1.1em;
                color: #333;
            }
            
            .quick-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .quick-actions .button {
                display: flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
                text-align: center;
            }
            
            .settings-status .status-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                font-size: 0.9em;
            }
            
            .status-enabled {
                color: #00a32a;
                font-weight: 600;
            }
            
            .theme-info {
                font-size: 0.9em;
                line-height: 1.6;
            }
            
            /* Responsive Design */
            @media (max-width: 1200px) {
                .kilismile-settings-body {
                    grid-template-columns: 200px 1fr 250px;
                }
            }
            
            @media (max-width: 768px) {
                .kilismile-settings-body {
                    grid-template-columns: 1fr;
                    grid-template-rows: auto 1fr;
                }
                
                .settings-navigation {
                    border-right: none;
                    border-bottom: 1px solid #e0e0e0;
                }
                
                .nav-tabs {
                    display: flex;
                    overflow-x: auto;
                }
                
                .nav-tab {
                    border-bottom: none;
                    border-right: 1px solid #f0f0f0;
                    flex-shrink: 0;
                }
                
                .settings-sidebar {
                    order: -1;
                    padding: 20px;
                }
                
                .kilismile-settings-header {
                    flex-direction: column;
                    gap: 20px;
                    text-align: center;
                }
                
                .header-actions {
                    flex-wrap: wrap;
                    justify-content: center;
                }
            }
        </style>
        
        <script>
            jQuery(document).ready(function($) {
                // Tab switching
                $('.nav-tab').on('click', function() {
                    const sectionId = $(this).data('section');
                    
                    // Update active tab
                    $('.nav-tab').removeClass('active');
                    $(this).addClass('active');
                    
                    // Update active section
                    $('.settings-section').removeClass('active');
                    $('#section-' + sectionId).addClass('active');
                    
                    // Update URL hash
                    history.pushState(null, null, '#' + sectionId);
                });
                
                // Load section from URL hash
                const hash = window.location.hash.substr(1);
                if (hash) {
                    $('.nav-tab[data-section="' + hash + '"]').trigger('click');
                }
                
                // Auto-save functionality
                let autoSaveTimeout;
                $('#kilismile-settings-form').on('change', 'input, select, textarea', function() {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(autoSave, 2000);
                });
                
                function autoSave() {
                    const formData = new FormData($('#kilismile-settings-form')[0]);
                    formData.append('action', 'kilismile_save_settings');
                    formData.append('auto_save', '1');
                    
                    $.ajax({
                        url: kilismileSettings.ajax_url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#last-saved').text(new Date().toLocaleString());
                                showNotification('Auto-saved', 'success');
                            }
                        }
                    });
                }
                
                function showNotification(message, type) {
                    // Implementation for notifications
                }
            });
        </script>
        <?php
    }
    
    /**
     * Render Settings Field
     */
    private function render_field($section_id, $field_id, $field, $current_settings) {
        $field_name = "kilismile_settings[{$section_id}][{$field_id}]";
        $field_value = isset($current_settings[$section_id][$field_id]) ? $current_settings[$section_id][$field_id] : ($field['default'] ?? '');
        
        echo '<div class="setting-field setting-field-' . esc_attr($field['type']) . '">';
        echo '<div class="field-header">';
        echo '<label for="' . esc_attr($field_id) . '">' . esc_html($field['title']) . '</label>';
        if (isset($field['description'])) {
            echo '<p class="field-description">' . esc_html($field['description']) . '</p>';
        }
        echo '</div>';
        echo '<div class="field-content">';
        
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" class="regular-text">';
                break;
                
            case 'textarea':
                $rows = isset($field['rows']) ? $field['rows'] : 4;
                echo '<textarea id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" rows="' . esc_attr($rows) . '" class="large-text">' . esc_textarea($field_value) . '</textarea>';
                break;
                
            case 'select':
                echo '<select id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '">';
                foreach ($field['options'] as $value => $label) {
                    echo '<option value="' . esc_attr($value) . '"' . selected($field_value, $value, false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;
                
            case 'toggle':
                echo '<label class="toggle-switch">';
                echo '<input type="checkbox" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="1"' . checked($field_value, 1, false) . '>';
                echo '<span class="toggle-slider"></span>';
                echo '</label>';
                break;
                
            case 'color':
                echo '<input type="color" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" class="color-picker">';
                break;
                
            case 'number':
                $min = isset($field['min']) ? 'min="' . esc_attr($field['min']) . '"' : '';
                $max = isset($field['max']) ? 'max="' . esc_attr($field['max']) . '"' : '';
                $step = isset($field['step']) ? 'step="' . esc_attr($field['step']) . '"' : '';
                echo '<input type="number" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" ' . $min . ' ' . $max . ' ' . $step . ' class="small-text">';
                break;
                
            default:
                do_action('kilismile_render_custom_field_type', $field['type'], $field_id, $field_name, $field_value, $field);
                break;
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * AJAX Save Settings
     */
    public function ajax_save_settings() {
        check_ajax_referer('kilismile_settings_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $settings = $_POST['kilismile_settings'] ?? array();
        $sanitized_settings = $this->sanitize_settings($settings);
        
        update_option('kilismile_settings', $sanitized_settings);
        update_option('kilismile_settings_last_saved', current_time('mysql'));
        
        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', 'kilismile'),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Sanitize Settings
     */
    public function sanitize_settings($settings) {
        $sanitized = array();
        
        foreach ($this->settings_sections as $section_id => $section) {
            if (!isset($settings[$section_id]) || !isset($this->settings_fields[$section_id])) {
                continue;
            }
            
            $sanitized[$section_id] = array();
            
            foreach ($this->settings_fields[$section_id] as $field_id => $field) {
                if (!isset($settings[$section_id][$field_id])) {
                    continue;
                }
                
                $value = $settings[$section_id][$field_id];
                
                switch ($field['type']) {
                    case 'text':
                        $sanitized[$section_id][$field_id] = sanitize_text_field($value);
                        break;
                    case 'textarea':
                        $sanitized[$section_id][$field_id] = sanitize_textarea_field($value);
                        break;
                    case 'email':
                        $sanitized[$section_id][$field_id] = sanitize_email($value);
                        break;
                    case 'url':
                        $sanitized[$section_id][$field_id] = esc_url_raw($value);
                        break;
                    case 'number':
                        $sanitized[$section_id][$field_id] = floatval($value);
                        break;
                    case 'toggle':
                        $sanitized[$section_id][$field_id] = $value ? 1 : 0;
                        break;
                    default:
                        $sanitized[$section_id][$field_id] = apply_filters('kilismile_sanitize_field_' . $field['type'], $value, $field);
                        break;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * AJAX Reset Settings
     */
    public function ajax_reset_settings() {
        check_ajax_referer('kilismile_settings_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        delete_option('kilismile_settings');
        delete_option('kilismile_settings_last_saved');
        
        wp_send_json_success(array(
            'message' => __('Settings reset successfully!', 'kilismile')
        ));
    }
    
    /**
     * AJAX Export Settings
     */
    public function ajax_export_settings() {
        check_ajax_referer('kilismile_settings_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $settings = get_option('kilismile_settings', array());
        $export_data = array(
            'version' => $this->version,
            'timestamp' => current_time('mysql'),
            'settings' => $settings
        );
        
        wp_send_json_success(array(
            'data' => json_encode($export_data, JSON_PRETTY_PRINT),
            'filename' => 'kilismile-settings-' . date('Y-m-d-H-i-s') . '.json'
        ));
    }
    
    /**
     * AJAX Import Settings
     */
    public function ajax_import_settings() {
        check_ajax_referer('kilismile_settings_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(__('No file uploaded', 'kilismile'));
        }
        
        $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $import_data = json_decode($file_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Invalid JSON file', 'kilismile'));
        }
        
        if (!isset($import_data['settings'])) {
            wp_send_json_error(__('Invalid settings file', 'kilismile'));
        }
        
        $sanitized_settings = $this->sanitize_settings($import_data['settings']);
        update_option('kilismile_settings', $sanitized_settings);
        update_option('kilismile_settings_last_saved', current_time('mysql'));
        
        wp_send_json_success(array(
            'message' => __('Settings imported successfully!', 'kilismile')
        ));
    }
}

// Initialize the settings framework
new KiliSmile_Settings_Framework();


