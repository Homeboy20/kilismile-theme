<?php
/**
 * Plugin Name: KiliSmile Settings Test
 * Description: Quick test to verify enhanced settings system functionality
 * Version: 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add a test menu to verify enhanced settings
 */
function kilismile_test_menu() {
    add_submenu_page(
        'tools.php',
        'KiliSmile Settings Test',
        'KiliSmile Test',
        'manage_options',
        'kilismile-test',
        'kilismile_test_page'
    );
}
add_action('admin_menu', 'kilismile_test_menu');

/**
 * Test page content
 */
function kilismile_test_page() {
    ?>
    <div class="wrap">
        <h1>KiliSmile Enhanced Settings Test</h1>
        
        <?php
        echo '<div class="notice notice-info"><p><strong>Testing Enhanced Settings System...</strong></p></div>';
        
        // Test 1: Check if enhanced settings class exists
        echo '<h2>1. Class Availability Test</h2>';
        if (class_exists('KiliSmile_Settings_Framework')) {
            echo '<p style="color: green;">✅ <strong>KiliSmile_Settings_Framework class is available</strong></p>';
            
            // Try to instantiate
            try {
                $framework = new KiliSmile_Settings_Framework();
                echo '<p style="color: green;">✅ <strong>Framework can be instantiated</strong></p>';
                
                // Test sections
                echo '<h3>Available Sections:</h3>';
                $sections = $framework->get_sections();
                if (!empty($sections)) {
                    echo '<ul>';
                    foreach ($sections as $key => $section) {
                        echo '<li>✅ <strong>' . esc_html($section['title']) . '</strong> (' . esc_html($key) . ')</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="color: orange;">⚠️ No sections configured</p>';
                }
                
            } catch (Exception $e) {
                echo '<p style="color: red;">❌ <strong>Error instantiating framework:</strong> ' . esc_html($e->getMessage()) . '</p>';
            }
        } else {
            echo '<p style="color: red;">❌ <strong>KiliSmile_Settings_Framework class not found</strong></p>';
        }
        
        // Test 2: Check helper functions
        echo '<h2>2. Helper Functions Test</h2>';
        $functions = [
            'kilismile_get_setting',
            'kilismile_get_enhanced_organization_info',
            'kilismile_enhanced_body_classes',
            'kilismile_get_header_settings',
            'kilismile_get_performance_settings'
        ];
        
        foreach ($functions as $func) {
            if (function_exists($func)) {
                echo '<p style="color: green;">✅ <strong>' . esc_html($func) . '()</strong> is available</p>';
            } else {
                echo '<p style="color: red;">❌ <strong>' . esc_html($func) . '()</strong> not found</p>';
            }
        }
        
        // Test 3: Check if admin menu exists
        echo '<h2>3. Admin Menu Test</h2>';
        global $menu, $submenu;
        $kilismile_menu_found = false;
        
        foreach ($menu as $menu_item) {
            if (isset($menu_item[2]) && $menu_item[2] === 'kilismile-settings') {
                echo '<p style="color: green;">✅ <strong>KiliSmile main menu found</strong></p>';
                $kilismile_menu_found = true;
                break;
            }
        }
        
        if (!$kilismile_menu_found) {
            echo '<p style="color: red;">❌ <strong>KiliSmile main menu not found</strong></p>';
        } else {
            echo '<p><a href="' . admin_url('admin.php?page=kilismile-settings') . '" class="button button-primary">🚀 Go to KiliSmile Settings</a></p>';
        }
        
        // Test 4: Settings data test
        echo '<h2>4. Settings Data Test</h2>';
        if (function_exists('kilismile_get_setting')) {
            // Test getting a setting
            $test_setting = kilismile_get_setting('general', 'site_mode', 'default');
            echo '<p>✅ <strong>Test setting retrieval:</strong> Site mode = ' . esc_html($test_setting) . '</p>';
            
            // Test all sections
            $sections = ['general', 'appearance', 'header', 'content', 'donations', 'social', 'performance', 'advanced'];
            foreach ($sections as $section) {
                $section_data = get_option('kilismile_settings_' . $section, array());
                $count = count($section_data);
                echo '<p>✅ <strong>' . ucfirst($section) . ' section:</strong> ' . $count . ' settings</p>';
            }
        }
        
        ?>
        
        <hr>
        <h2>Quick Actions</h2>
        <p>
            <a href="<?php echo admin_url('admin.php?page=kilismile-settings'); ?>" class="button button-primary">🎯 Open Enhanced Settings</a>
            <a href="<?php echo admin_url('tools.php?page=kilismile-test'); ?>" class="button">🔄 Refresh Test</a>
        </p>
        
    </div>
    <?php
}
?>


