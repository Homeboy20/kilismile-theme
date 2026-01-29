<?php
/**
 * KiliSmile Settings Migration Script
 * 
 * Migrates existing theme settings to the new enhanced settings system
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * KiliSmile Settings Migration Class
 */
class KiliSmile_Settings_Migration {
    
    private $migration_version = '3.0.0';
    
    public function __construct() {
        add_action('admin_init', array($this, 'check_migration'));
        add_action('wp_ajax_kilismile_run_migration', array($this, 'run_migration'));
    }
    
    /**
     * Check if migration is needed
     */
    public function check_migration() {
        $current_version = get_option('kilismile_settings_version', '1.0.0');
        
        if (version_compare($current_version, $this->migration_version, '<')) {
            add_action('admin_notices', array($this, 'migration_notice'));
        }
    }
    
    /**
     * Show migration notice
     */
    public function migration_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_kilismile-settings') {
            return; // Don't show on settings page
        }
        
        ?>
        <div class="notice notice-info is-dismissible" id="kilismile-migration-notice">
            <h3><?php _e('KiliSmile Theme Update Available', 'kilismile'); ?></h3>
            <p>
                <?php _e('A new enhanced settings system is available! Migrate your existing settings to take advantage of new features:', 'kilismile'); ?>
            </p>
            <ul style="list-style: disc; margin-left: 30px;">
                <li><?php _e('Advanced color schemes and typography options', 'kilismile'); ?></li>
                <li><?php _e('Improved donation system with analytics', 'kilismile'); ?></li>
                <li><?php _e('Real-time preview and auto-save functionality', 'kilismile'); ?></li>
                <li><?php _e('Import/export settings for backup', 'kilismile'); ?></li>
                <li><?php _e('Performance optimizations and SEO enhancements', 'kilismile'); ?></li>
            </ul>
            <p>
                <button type="button" class="button button-primary" id="start-migration">
                    <?php _e('Migrate Settings Now', 'kilismile'); ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=kilismile-settings'); ?>" class="button">
                    <?php _e('View New Settings', 'kilismile'); ?>
                </a>
                <button type="button" class="button-link" onclick="this.parentNode.parentNode.parentNode.style.display='none'">
                    <?php _e('Maybe Later', 'kilismile'); ?>
                </button>
            </p>
            <div id="migration-progress" style="display: none;">
                <p><?php _e('Migration in progress...', 'kilismile'); ?></p>
                <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden; height: 20px;">
                    <div id="migration-progress-bar" style="background: #00a32a; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p id="migration-status"><?php _e('Preparing migration...', 'kilismile'); ?></p>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#start-migration').on('click', function() {
                const button = $(this);
                const progress = $('#migration-progress');
                const progressBar = $('#migration-progress-bar');
                const status = $('#migration-status');
                
                button.prop('disabled', true);
                progress.show();
                
                // Start migration
                $.post(ajaxurl, {
                    action: 'kilismile_run_migration',
                    nonce: '<?php echo wp_create_nonce('kilismile_migration'); ?>'
                }, function(response) {
                    if (response.success) {
                        progressBar.css('width', '100%');
                        status.text('<?php _e('Migration completed successfully!', 'kilismile'); ?>');
                        
                        setTimeout(function() {
                            $('#kilismile-migration-notice').fadeOut();
                            location.reload();
                        }, 2000);
                    } else {
                        status.text('<?php _e('Migration failed: ', 'kilismile'); ?>' + (response.data || '<?php _e('Unknown error', 'kilismile'); ?>'));
                        button.prop('disabled', false);
                    }
                }).fail(function() {
                    status.text('<?php _e('Migration failed: Network error', 'kilismile'); ?>');
                    button.prop('disabled', false);
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Run migration via AJAX
     */
    public function run_migration() {
        check_ajax_referer('kilismile_migration', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'kilismile'));
        }
        
        try {
            $this->migrate_existing_settings();
            wp_send_json_success(__('Migration completed successfully', 'kilismile'));
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Migrate existing settings to new format
     */
    private function migrate_existing_settings() {
        $migrated_settings = array();
        
        // Migrate general settings
        $migrated_settings['general'] = $this->migrate_general_settings();
        
        // Migrate appearance settings
        $migrated_settings['appearance'] = $this->migrate_appearance_settings();
        
        // Migrate header settings
        $migrated_settings['header'] = $this->migrate_header_settings();
        
        // Migrate donation settings
        $migrated_settings['donations'] = $this->migrate_donation_settings();
        
        // Migrate social settings
        $migrated_settings['social'] = $this->migrate_social_settings();
        
        // Migrate performance settings
        $migrated_settings['performance'] = $this->migrate_performance_settings();
        
        // Migrate advanced settings
        $migrated_settings['advanced'] = $this->migrate_advanced_settings();
        
        // Save migrated settings
        update_option('kilismile_settings', $migrated_settings);
        update_option('kilismile_settings_version', $this->migration_version);
        update_option('kilismile_settings_migrated_at', current_time('mysql'));
        
        // Create backup of old settings
        $this->backup_old_settings();
    }
    
    /**
     * Migrate general settings
     */
    private function migrate_general_settings() {
        return array(
            'site_mode' => 'charity',
            'organization_name' => get_bloginfo('name'),
            'organization_tagline' => get_bloginfo('description'),
            'contact_info' => array(
                'phone' => get_option('kilismile_contact_phone', ''),
                'email' => get_option('kilismile_contact_email', get_option('admin_email')),
                'address' => get_option('kilismile_contact_address', '')
            )
        );
    }
    
    /**
     * Migrate appearance settings
     */
    private function migrate_appearance_settings() {
        return array(
            'color_scheme' => 'default',
            'custom_colors' => array(
                'primary' => get_theme_mod('primary_color', '#2271b1'),
                'secondary' => get_theme_mod('secondary_color', '#00a32a'),
                'accent' => get_theme_mod('accent_color', '#ff6b35'),
                'text' => get_theme_mod('text_color', '#333333'),
                'background' => get_theme_mod('background_color', '#ffffff')
            ),
            'typography' => array(
                'headings' => get_theme_mod('headings_font', 'Roboto, sans-serif'),
                'body' => get_theme_mod('body_font', 'Open Sans, sans-serif')
            )
        );
    }
    
    /**
     * Migrate header settings
     */
    private function migrate_header_settings() {
        return array(
            'header_layout' => get_theme_mod('header_layout', 'standard'),
            'logo_settings' => array(
                'size' => get_theme_mod('logo_size', 60),
                'border_radius' => get_theme_mod('logo_border_radius', 0),
                'retina_support' => get_theme_mod('logo_retina_support', true)
            )
        );
    }
    
    /**
     * Migrate donation settings
     */
    private function migrate_donation_settings() {
        $settings = array(
            'donation_system' => get_option('kilismile_enable_donations', true)
        );
        
        // Migrate donation goals
        $goals = array();
        $old_goal_amount = get_option('kilismile_donation_goal_amount');
        $old_goal_title = get_option('kilismile_donation_goal_title', 'General Fund');
        
        if ($old_goal_amount) {
            $goals[] = array(
                'title' => $old_goal_title,
                'amount' => $old_goal_amount,
                'currency' => get_option('kilismile_donation_currency', 'USD'),
                'deadline' => ''
            );
        }
        
        $settings['donation_goals'] = $goals;
        
        return $settings;
    }
    
    /**
     * Migrate social settings
     */
    private function migrate_social_settings() {
        return array(
            'social_media' => array(
                'facebook' => get_option('kilismile_facebook_url', ''),
                'twitter' => get_option('kilismile_twitter_url', ''),
                'instagram' => get_option('kilismile_instagram_url', ''),
                'linkedin' => get_option('kilismile_linkedin_url', ''),
                'youtube' => get_option('kilismile_youtube_url', ''),
                'whatsapp' => get_option('kilismile_whatsapp_url', '')
            )
        );
    }
    
    /**
     * Migrate performance settings
     */
    private function migrate_performance_settings() {
        return array(
            'optimization' => array(
                'lazy_loading' => get_option('kilismile_lazy_loading', true),
                'cache_assets' => get_option('kilismile_cache_assets', true)
            )
        );
    }
    
    /**
     * Migrate advanced settings
     */
    private function migrate_advanced_settings() {
        return array(
            'custom_css' => get_option('kilismile_custom_css', ''),
            'custom_js' => get_option('kilismile_custom_js', '')
        );
    }
    
    /**
     * Backup old settings
     */
    private function backup_old_settings() {
        global $wpdb;
        
        // Get all theme-related options
        $old_options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} 
             WHERE option_name LIKE 'kilismile_%' 
             OR option_name LIKE 'theme_mods_%'"
        );
        
        $backup_data = array();
        foreach ($old_options as $option) {
            $backup_data[$option->option_name] = maybe_unserialize($option->option_value);
        }
        
        // Save backup
        update_option('kilismile_settings_backup_' . time(), $backup_data);
        
        // Keep only last 5 backups
        $backups = $wpdb->get_col(
            "SELECT option_name FROM {$wpdb->options} 
             WHERE option_name LIKE 'kilismile_settings_backup_%' 
             ORDER BY option_id DESC"
        );
        
        if (count($backups) > 5) {
            foreach (array_slice($backups, 5) as $old_backup) {
                delete_option($old_backup);
            }
        }
    }
    
    /**
     * Restore from backup
     */
    public function restore_from_backup($backup_timestamp) {
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        $backup_data = get_option('kilismile_settings_backup_' . $backup_timestamp);
        if (!$backup_data) {
            return false;
        }
        
        foreach ($backup_data as $option_name => $option_value) {
            update_option($option_name, $option_value);
        }
        
        return true;
    }
    
    /**
     * Get available backups
     */
    public function get_available_backups() {
        global $wpdb;
        
        $backups = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} 
             WHERE option_name LIKE 'kilismile_settings_backup_%' 
             ORDER BY option_id DESC"
        );
        
        $backup_list = array();
        foreach ($backups as $backup) {
            $timestamp = str_replace('kilismile_settings_backup_', '', $backup->option_name);
            $backup_list[$timestamp] = array(
                'timestamp' => $timestamp,
                'date' => date('Y-m-d H:i:s', $timestamp),
                'size' => strlen(serialize($backup->option_value))
            );
        }
        
        return $backup_list;
    }
}

// Initialize migration
new KiliSmile_Settings_Migration();


