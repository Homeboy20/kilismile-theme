<?php
/**
 * Create First Program - World Oral Health Week 2026
 * Run this function once to create the first program
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create World Oral Health Week 2026 Program
 * Call this function once: kilismile_create_woh_2026_program();
 */
function kilismile_create_woh_2026_program() {
    // Check if program already exists
    $existing = get_posts(array(
        'post_type' => 'programs',
        'post_status' => 'any',
        'meta_query' => array(
            array(
                'key' => '_program_start_date',
                'value' => '2026-03-14',
                'compare' => '='
            )
        ),
        'posts_per_page' => 1
    ));
    
    if (!empty($existing)) {
        return false; // Program already exists
    }
    
    // Create the program post
    $program_data = array(
        'post_title'    => 'World Oral Health Week 2026 - Moshi, Tanzania',
        'post_content'  => 'Celebration of World Oral Health Week, Community Health Screening, and Official Launch of Kilismile Organization Office and Dental Clinic in Moshi, Tanzania.',
        'post_status'   => 'publish',
        'post_type'     => 'programs',
        'post_author'   => 1,
    );
    
    $program_id = wp_insert_post($program_data);
    
    if (is_wp_error($program_id)) {
        return false;
    }
    
    // Swahili Content
    update_post_meta($program_id, '_program_title_swahili', 'Maadhimisho ya Wiki ya Afya ya Kinywa na Meno Duniani MARCH 2026');
    update_post_meta($program_id, '_program_intro_swahili', 'Magonjwa ya Afya ya Kinywa na Meno, yakiwemo kuoza kwa meno, magonjwa ya fizi na maambukizi ya kinywa, ni miongoni mwa changamoto kubwa za afya ya umma zinazoathiri watoto wengi nchini Tanzania. Pamoja na athari zake kwa afya na ustawi wa mtoto, magonjwa haya husababisha maumivu makali, maambukizi ya mara kwa mara, utoro shuleni, na kushuka kwa kiwango cha ufaulu wa masomo. Hali hii inachangiwa kwa kiasi kikubwa na uelewa mdogo wa jamii kuhusu usafi wa kinywa, upatikanaji mdogo wa huduma za meno, na gharama za matibabu.

Aidha, magonjwa yasiyoambukiza (NCDs) kama vile shinikizo la damu na kisukari yanaendelea kuongezeka miongoni mwa watu wazima na wazee. Idadi kubwa ya wananchi hawafanyi uchunguzi wa afya mara kwa mara, hali inayosababisha kugundulika kwa magonjwa haya katika hatua za mwisho na kuongeza gharama za matibabu pamoja na hatari ya vifo.

Maadhimisho ya Wiki ya afya ya kinywa na meno Duniani yanatoa fursa muhimu ya kuelimisha jamii, kufanya uchunguzi wa mapema, na kutoa huduma za matibabu. Shirika la Kilismile litaadhimisha wiki hii sambamba na uzinduzi rasmi wa ofisi yake ya Moshi pamoja na kliniki yake ya kisasa ya kinywa na meno, hatua itakayowezesha utoaji wa huduma endelevu kwa jamii ya Moshi na maeneo jirani.');
    
    update_post_meta($program_id, '_program_description_swahili', '<p>Mradi huu unalenga kuboresha afya ya kinywa na meno na afya ya jumla kwa watoto na watu wazima (wazee) katika Manispaa ya Moshi kupitia uchunguzi wa mapema, matibabu, na elimu ya afya.</p>');
    
    update_post_meta($program_id, '_program_objectives_swahili', 'Kufanya uchunguzi wa Afya ya Kinywa na Meno kwa angalau watoto 500 wa shule za msingi katika Manispaa ya Moshi
Kufanya uchunguzi wa shinikizo la damu na kiwango cha sukari kwenye damu kwa angalau wanajamii 300
Kutoa matibabu ya meno kwa wagonjwa wote watakaobainika kuwa na matatizo ya Afya ya Kinywa na Meno kupitia kliniki ya Shirika la Kilismile
Kuongeza uelewa wa jamii kuhusu umuhimu wa usafi wa kinywa na kinga dhidi ya magonjwa yasiyoambukiza
Kuzindua rasmi ofisi ya Moshi na kliniki ya meno ya Shirika la Kilismile');
    
    update_post_meta($program_id, '_program_activities_swahili', 'Uchunguzi wa afya ya kinywa na meno kwa watoto wa shule za msingi
Uchunguzi wa magonjwa yasiyoambukiza (vipimo vya shinikizo la damu na sukari kwenye damu)
Utoaji wa matibabu ya meno katika kliniki ya Kilismile
Utoaji wa elimu ya afya ya kinywa na meno na kinga ya Magonjwa yasiyo ya kuambukiza kwa jamii
Hafla ya uzinduzi rasmi wa ofisi na kliniki ya meno ya Shirika la Kilismile');
    
    update_post_meta($program_id, '_program_outcomes_swahili', 'Kuongezeka kwa utambuzi wa mapema wa magonjwa ya afya ya kinywa na meno na magonjwa yasiyoambukiza
Kupungua kwa idadi ya wananchi wenye matatizo ya afya ya kinywa na meno yasiyotibiwa
Kuimarika kwa uelewa wa jamii kuhusu mbinu za kinga na mabadiliko chanya ya tabia za kiafya
Kuimarika kwa upatikanaji wa huduma za kudumu na bora za afya ya kinywa na meno katika Manispaa ya Moshi');
    
    // English Content
    update_post_meta($program_id, '_program_title_english', 'World Oral Health Week Celebration March 2026');
    update_post_meta($program_id, '_program_intro_english', 'Oral and dental health diseases, including tooth decay, gum diseases, and oral infections, are among the major public health challenges affecting many children in Tanzania. In addition to their impact on children\'s health and well-being, these diseases cause severe pain, frequent infections, school absenteeism, and decreased academic performance. This situation is largely contributed to by limited community awareness about oral hygiene, limited access to dental services, and treatment costs.

Furthermore, non-communicable diseases (NCDs) such as high blood pressure and diabetes continue to increase among adults and the elderly. A large number of citizens do not undergo regular health screenings, a situation that leads to the detection of these diseases at late stages and increases treatment costs along with the risk of death.

The celebration of World Oral Health Week provides an important opportunity to educate communities, conduct early screening, and provide treatment services. Kilismile Organization will celebrate this week alongside the official launch of its Moshi office together with its modern dental clinic, a step that will enable the provision of continuous services to the Moshi community and neighboring areas.');
    
    update_post_meta($program_id, '_program_description_english', '<p>This project aims to improve oral and dental health and general health for children and adults (elderly) in Moshi Municipality through early screening, treatment, and health education.</p>');
    
    update_post_meta($program_id, '_program_objectives_english', 'Conduct Oral and Dental Health screening for at least 500 primary school children in Moshi Municipality
Conduct blood pressure and blood sugar level screening for at least 300 community members
Provide dental treatment to all patients identified with Oral and Dental Health problems through Kilismile Organization clinic
Increase community awareness about the importance of oral hygiene and prevention of non-communicable diseases
Officially launch the Moshi office and dental clinic of Kilismile Organization');
    
    update_post_meta($program_id, '_program_activities_english', 'Oral and dental health screening for primary school children
Non-communicable disease screening (blood pressure and blood sugar tests)
Provision of dental treatment at Kilismile clinic
Provision of oral and dental health education and non-communicable disease prevention to the community
Official launch ceremony of Kilismile Organization office and dental clinic');
    
    update_post_meta($program_id, '_program_outcomes_english', 'Increased early detection of oral and dental health diseases and non-communicable diseases
Decreased number of citizens with untreated oral and dental health problems
Strengthened community awareness about prevention methods and positive health behavior changes
Strengthened availability of sustainable and quality oral and dental health services in Moshi Municipality');
    
    // Program Details
    update_post_meta($program_id, '_program_implementing_org', 'Kilismile Organization (Registered NGO in Tanzania)');
    update_post_meta($program_id, '_program_location', 'Moshi Municipality, Kicheko Building, Kilimanjaro Region, Tanzania');
    update_post_meta($program_id, '_program_start_date', '2026-03-14');
    update_post_meta($program_id, '_program_end_date', '2026-03-20');
    update_post_meta($program_id, '_program_duration', 'One Week: March 14-20, 2026 (World Oral Health Week)');
    update_post_meta($program_id, '_program_target_audience', 'mixed');
    update_post_meta($program_id, '_program_status', 'planned');
    update_post_meta($program_id, '_program_beneficiaries', '800');
    update_post_meta($program_id, '_program_featured', 'yes');
    
    // Main Objective
    update_post_meta($program_id, '_program_main_objective', 'Kuboresha afya ya kinywa na meno na afya ya jumla kwa watoto na watu wazima (wazee) katika Manispaa ya Moshi kupitia uchunguzi wa mapema, matibabu, na elimu ya afya.

To improve oral and dental health and general health for children and adults (elderly) in Moshi Municipality through early screening, treatment, and health education.');
    
    // Sustainability
    update_post_meta($program_id, '_program_sustainability', 'Baada ya kukamilika kwa maadhimisho ya Wiki ya afya ya kinywa na meno Duniani, kliniki ya meno ya Shirika la Kilismile itaendelea kutoa huduma za afya ya kinywa na meno kwa jamii kama kituo cha kudumu cha matibabu, ufuatiliaji wa wagonjwa, na utekelezaji wa programu za afya ya kinywa na meno mashuleni. Hii itahakikisha manufaa ya mradi yanaendelea kwa muda mrefu.

After the completion of the World Oral Health Week celebration, Kilismile Organization\'s dental clinic will continue to provide oral and dental health services to the community as a permanent treatment center, patient follow-up, and implementation of oral and dental health programs in schools. This will ensure the project benefits continue for a long time.');
    
    // Budget (reference to Excel file)
    update_post_meta($program_id, '_program_budget_total', 'See attached budget file');
    update_post_meta($program_id, '_program_budget_file', '');
    update_post_meta($program_id, '_program_funding_source', 'To be determined');
    
    // Set featured image if available
    // You can upload an image and set it as featured image
    
    return $program_id;
}

/**
 * Admin notice to create first program
 */
function kilismile_program_setup_notice() {
    if (get_option('kilismile_woh_2026_created')) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'programs') {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php _e('Welcome to Program Management!', 'kilismile'); ?></strong><br>
                <?php _e('Would you like to create the first program (World Oral Health Week 2026)?', 'kilismile'); ?>
                <a href="<?php echo admin_url('admin.php?page=kilismile-create-first-program'); ?>" class="button button-primary" style="margin-left: 10px;">
                    <?php _e('Create First Program', 'kilismile'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'kilismile_program_setup_notice');

/**
 * Add admin page for creating first program
 */
function kilismile_add_program_setup_page() {
    add_submenu_page(
        'edit.php?post_type=programs',
        __('Create First Program', 'kilismile'),
        __('Create First Program', 'kilismile'),
        'manage_options',
        'kilismile-create-first-program',
        'kilismile_create_first_program_page'
    );
}
add_action('admin_menu', 'kilismile_add_program_setup_page');

/**
 * Create First Program Admin Page
 */
function kilismile_create_first_program_page() {
    if (isset($_POST['create_woh_program']) && check_admin_referer('kilismile_create_woh_program')) {
        $program_id = kilismile_create_woh_2026_program();
        
        if ($program_id) {
            update_option('kilismile_woh_2026_created', true);
            ?>
            <div class="notice notice-success">
                <p><?php _e('Program created successfully!', 'kilismile'); ?></p>
                <p>
                    <a href="<?php echo get_edit_post_link($program_id); ?>" class="button button-primary">
                        <?php _e('Edit Program', 'kilismile'); ?>
                    </a>
                    <a href="<?php echo get_permalink($program_id); ?>" class="button" target="_blank">
                        <?php _e('View Program', 'kilismile'); ?>
                    </a>
                </p>
            </div>
            <?php
        } else {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Program could not be created. It may already exist.', 'kilismile'); ?></p>
            </div>
            <?php
        }
    }
    
    $already_exists = get_option('kilismile_woh_2026_created');
    ?>
    <div class="wrap">
        <h1><?php _e('Create First Program', 'kilismile'); ?></h1>
        
        <?php if ($already_exists): ?>
            <div class="notice notice-info">
                <p><?php _e('The World Oral Health Week 2026 program has already been created.', 'kilismile'); ?></p>
            </div>
        <?php else: ?>
            <div class="card" style="max-width: 800px;">
                <h2><?php _e('World Oral Health Week 2026', 'kilismile'); ?></h2>
                <p><?php _e('This will create your first program with complete bilingual content (Swahili and English).', 'kilismile'); ?></p>
                
                <h3><?php _e('Program Details:', 'kilismile'); ?></h3>
                <ul>
                    <li><strong><?php _e('Title:', 'kilismile'); ?></strong> World Oral Health Week Celebration March 2026</li>
                    <li><strong><?php _e('Duration:', 'kilismile'); ?></strong> March 14-20, 2026 (One Week)</li>
                    <li><strong><?php _e('Location:', 'kilismile'); ?></strong> Moshi Municipality, Kilimanjaro Region, Tanzania</li>
                    <li><strong><?php _e('Target Beneficiaries:', 'kilismile'); ?></strong> 800+ (500 children, 300 adults)</li>
                </ul>
                
                <form method="post">
                    <?php wp_nonce_field('kilismile_create_woh_program'); ?>
                    <p>
                        <button type="submit" name="create_woh_program" class="button button-primary button-large">
                            <?php _e('Create Program', 'kilismile'); ?>
                        </button>
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
