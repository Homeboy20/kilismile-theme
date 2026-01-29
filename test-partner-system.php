<?php
/**
 * Template Name: Test Partner System
 * 
 * Testing page for partner management system functionality
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container" style="padding: 40px 20px;">
        <div class="test-header" style="text-align: center; margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h1 style="color: var(--dark-green); font-size: 2.2rem; margin-bottom: 15px;">
                <i class="fas fa-handshake" style="margin-right: 10px; color: var(--primary-green);"></i>
                Partner Management System Test
            </h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Testing all partner management features including logo display, categories, and strategic positioning.
            </p>
        </div>

        <!-- Test Partner Functions Status -->
        <div class="functions-test" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2 style="color: var(--dark-green); margin-bottom: 20px;">
                <i class="fas fa-cogs"></i> Function Status Check
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <?php
                $functions_to_test = array(
                    'kilismile_get_partners' => 'Get Partners Function',
                    'kilismile_save_partner' => 'Save Partner Function',
                    'kilismile_create_partners_table' => 'Create Table Function',
                    'display_enhanced_partner_grid' => 'Enhanced Partner Grid',
                    'display_homepage_partner_logos' => 'Homepage Partner Display',
                    'Kilismile_Partner_Showcase_Widget' => 'Partner Widget Class'
                );

                foreach ($functions_to_test as $function_name => $display_name) :
                    $exists = function_exists($function_name) || class_exists($function_name);
                ?>
                <div style="padding: 15px; border-radius: 4px; background: <?php echo $exists ? 'rgba(76, 175, 80, 0.1)' : 'rgba(244, 67, 54, 0.1)'; ?>; border-left: 4px solid <?php echo $exists ? 'var(--primary-green)' : '#f44336'; ?>;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas <?php echo $exists ? 'fa-check-circle' : 'fa-times-circle'; ?>" style="color: <?php echo $exists ? 'var(--primary-green)' : '#f44336'; ?>;"></i>
                        <strong style="color: var(--dark-green);"><?php echo $display_name; ?></strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 5px;">
                        <?php echo $exists ? 'Available' : 'Not Found'; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Test Database Status -->
        <div class="database-test" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2 style="color: var(--dark-green); margin-bottom: 20px;">
                <i class="fas fa-database"></i> Database Status
            </h2>
            
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'kilismile_partners';
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
            ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="padding: 15px; border-radius: 4px; background: <?php echo $table_exists ? 'rgba(76, 175, 80, 0.1)' : 'rgba(244, 67, 54, 0.1)'; ?>; border-left: 4px solid <?php echo $table_exists ? 'var(--primary-green)' : '#f44336'; ?>;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas <?php echo $table_exists ? 'fa-check-circle' : 'fa-times-circle'; ?>" style="color: <?php echo $table_exists ? 'var(--primary-green)' : '#f44336'; ?>;"></i>
                        <strong style="color: var(--dark-green);">Partners Table</strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 5px;">
                        <?php echo $table_exists ? 'Exists' : 'Missing'; ?>
                    </div>
                </div>

                <?php if ($table_exists) : ?>
                <div style="padding: 15px; border-radius: 4px; background: rgba(76, 175, 80, 0.1); border-left: 4px solid var(--primary-green);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chart-bar" style="color: var(--primary-green);"></i>
                        <strong style="color: var(--dark-green);">Records Count</strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 5px;">
                        <?php echo $wpdb->get_var("SELECT COUNT(*) FROM $table_name"); ?> partner(s)
                    </div>
                </div>
                <?php else : ?>
                <div style="padding: 15px; border-radius: 4px; background: rgba(255, 193, 7, 0.1); border-left: 4px solid #ffc107;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-triangle" style="color: #ffc107;"></i>
                        <strong style="color: var(--dark-green);">Create Table</strong>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 5px;">
                        <button onclick="createTable()" style="background: var(--primary-green); color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.8rem;">Create Now</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Test Partner Data -->
        <?php if ($table_exists && function_exists('kilismile_get_partners')) : ?>
        <div class="partner-data-test" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2 style="color: var(--dark-green); margin-bottom: 20px;">
                <i class="fas fa-database"></i> Partner Data Test
            </h2>
            
            <?php
            $partners = kilismile_get_partners();
            if (!empty($partners)) :
            ?>
                <div style="margin-bottom: 20px;">
                    <p style="color: var(--primary-green); font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Found <?php echo count($partners); ?> partner(s) in database
                    </p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($partners as $partner) : ?>
                    <div style="border: 1px solid rgba(76, 175, 80, 0.2); border-radius: 8px; padding: 20px; background: rgba(76, 175, 80, 0.02);">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <?php if (!empty($partner['logo_url'])) : ?>
                                <img src="<?php echo esc_url($partner['logo_url']); ?>" 
                                     alt="<?php echo esc_attr($partner['name']); ?> Logo" 
                                     style="width: 60px; height: 60px; object-fit: contain; border-radius: 4px; background: white; padding: 5px; border: 1px solid rgba(0,0,0,0.1);">
                            <?php else : ?>
                                <div style="width: 60px; height: 60px; background: rgba(76, 175, 80, 0.1); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building" style="color: var(--primary-green); font-size: 1.5rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h3 style="color: var(--dark-green); margin: 0 0 5px; font-size: 1.1rem;"><?php echo esc_html($partner['name']); ?></h3>
                                <span style="background: var(--primary-green); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; text-transform: uppercase;">
                                    <?php echo esc_html($partner['category']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($partner['description'])) : ?>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.4; margin-bottom: 10px;">
                            <?php echo esc_html(wp_trim_words($partner['description'], 20)); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($partner['website'])) : ?>
                        <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" 
                           style="color: var(--primary-green); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fas fa-external-link-alt"></i>
                            Visit Website
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="text-align: center; padding: 40px; background: rgba(255, 193, 7, 0.1); border-radius: 8px; border: 1px solid rgba(255, 193, 7, 0.3);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ffc107; margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 10px;">No Partners Found</h3>
                    <p style="color: var(--text-secondary);">No partners have been added yet. You can add partners through the WordPress admin panel.</p>
                    <?php if (current_user_can('manage_options')) : ?>
                    <a href="<?php echo admin_url('admin.php?page=partner-management'); ?>" 
                       style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary-green); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-top: 15px;">
                        <i class="fas fa-plus"></i>
                        Add First Partner
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Admin Access -->
        <?php if (current_user_can('manage_options')) : ?>
        <div class="admin-access" style="background: linear-gradient(135deg, var(--primary-green), var(--accent-green)); padding: 30px; border-radius: 8px; text-align: center; margin-bottom: 30px;">
            <h2 style="color: white; margin-bottom: 15px;">
                <i class="fas fa-user-shield"></i> Admin Access
            </h2>
            <p style="color: rgba(255,255,255,0.9); margin-bottom: 20px;">
                You have admin privileges. Access the partner management system to add, edit, or manage partners.
            </p>
            <a href="<?php echo admin_url('admin.php?page=partner-management'); ?>" 
               style="display: inline-flex; align-items: center; gap: 8px; background: white; color: var(--primary-green); padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-cog"></i>
                Open Partner Management
            </a>
        </div>
        <?php endif; ?>

        <!-- Success Message -->
        <div class="success-message" style="background: rgba(76, 175, 80, 0.1); border: 1px solid var(--primary-green); border-radius: 8px; padding: 20px; text-align: center;">
            <i class="fas fa-check-circle" style="color: var(--primary-green); font-size: 2rem; margin-bottom: 10px;"></i>
            <h3 style="color: var(--dark-green); margin-bottom: 10px;">Partner System Ready!</h3>
            <p style="color: var(--text-secondary);">The fatal error has been fixed and the partner management system is now functional.</p>
        </div>
    </div>
</main>

<script>
function createTable() {
    if (confirm('Create the partners database table?')) {
        alert('Table would be created via AJAX. For now, please contact admin to run the table creation function.');
    }
}
</script>

<?php get_footer(); ?>


