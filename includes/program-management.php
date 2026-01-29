<?php
/**
 * Enhanced Program Management System with Bilingual Support
 * Allows admins to add programs without coding
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Program Meta Boxes
 */
function kilismile_enhanced_program_meta_boxes() {
    add_meta_box(
        'program_bilingual_content',
        __('Bilingual Content (Swahili/English)', 'kilismile'),
        'kilismile_program_bilingual_meta_box',
        'programs',
        'normal',
        'high'
    );
    
    add_meta_box(
        'program_detailed_info',
        __('Program Details', 'kilismile'),
        'kilismile_program_detailed_meta_box',
        'programs',
        'normal',
        'high'
    );
    
    add_meta_box(
        'program_objectives',
        __('Program Objectives & Activities', 'kilismile'),
        'kilismile_program_objectives_meta_box',
        'programs',
        'normal',
        'high'
    );
    
    add_meta_box(
        'program_budget',
        __('Program Budget & Resources', 'kilismile'),
        'kilismile_program_budget_meta_box',
        'programs',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'kilismile_enhanced_program_meta_boxes');

/**
 * Bilingual Content Meta Box
 */
function kilismile_program_bilingual_meta_box($post) {
    wp_nonce_field('kilismile_program_bilingual_nonce', 'kilismile_program_bilingual_nonce');
    
    // Swahili fields
    $title_sw = get_post_meta($post->ID, '_program_title_swahili', true);
    $intro_sw = get_post_meta($post->ID, '_program_intro_swahili', true);
    $description_sw = get_post_meta($post->ID, '_program_description_swahili', true);
    $objectives_sw = get_post_meta($post->ID, '_program_objectives_swahili', true);
    $activities_sw = get_post_meta($post->ID, '_program_activities_swahili', true);
    $outcomes_sw = get_post_meta($post->ID, '_program_outcomes_swahili', true);
    
    // English fields
    $title_en = get_post_meta($post->ID, '_program_title_english', true);
    $intro_en = get_post_meta($post->ID, '_program_intro_english', true);
    $description_en = get_post_meta($post->ID, '_program_description_english', true);
    $objectives_en = get_post_meta($post->ID, '_program_objectives_english', true);
    $activities_en = get_post_meta($post->ID, '_program_activities_english', true);
    $outcomes_en = get_post_meta($post->ID, '_program_outcomes_english', true);
    
    ?>
    <div class="kilismile-bilingual-tabs" style="margin-top: 10px;">
        <div class="tab-buttons" style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd;">
            <button type="button" class="tab-btn active" data-tab="swahili" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 5px 5px 0 0;">
                <?php _e('Swahili (Kiswahili)', 'kilismile'); ?>
            </button>
            <button type="button" class="tab-btn" data-tab="english" style="padding: 10px 20px; background: #e0e0e0; color: #333; border: none; cursor: pointer; border-radius: 5px 5px 0 0;">
                <?php _e('English', 'kilismile'); ?>
            </button>
        </div>
        
        <!-- Swahili Tab -->
        <div class="tab-content active" id="swahili-tab">
            <table class="form-table">
                <tr>
                    <th><label for="program_title_swahili"><?php _e('Program Title (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <input type="text" id="program_title_swahili" name="program_title_swahili" 
                               value="<?php echo esc_attr($title_sw); ?>" class="large-text" />
                        <p class="description"><?php _e('Kichwa cha mradi kwa Kiswahili', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_intro_swahili"><?php _e('Introduction (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_intro_swahili" name="program_intro_swahili" rows="4" class="large-text"><?php echo esc_textarea($intro_sw); ?></textarea>
                        <p class="description"><?php _e('Utangulizi na sababu za mradi', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_description_swahili"><?php _e('Description (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <?php 
                        wp_editor($description_sw, 'program_description_swahili', array(
                            'textarea_name' => 'program_description_swahili',
                            'textarea_rows' => 10,
                            'media_buttons' => true,
                        )); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_objectives_swahili"><?php _e('Objectives (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_objectives_swahili" name="program_objectives_swahili" rows="6" class="large-text"><?php echo esc_textarea($objectives_sw); ?></textarea>
                        <p class="description"><?php _e('Malengo ya mradi (weka kila lengo kwenye mstari mmoja)', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_activities_swahili"><?php _e('Activities (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_activities_swahili" name="program_activities_swahili" rows="6" class="large-text"><?php echo esc_textarea($activities_sw); ?></textarea>
                        <p class="description"><?php _e('Shughuli za mradi (weka kila shughuli kwenye mstari mmoja)', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_outcomes_swahili"><?php _e('Expected Outcomes (Swahili)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_outcomes_swahili" name="program_outcomes_swahili" rows="6" class="large-text"><?php echo esc_textarea($outcomes_sw); ?></textarea>
                        <p class="description"><?php _e('Matokeo yanayotarajiwa (weka kila matokeo kwenye mstari mmoja)', 'kilismile'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- English Tab -->
        <div class="tab-content" id="english-tab" style="display: none;">
            <table class="form-table">
                <tr>
                    <th><label for="program_title_english"><?php _e('Program Title (English)', 'kilismile'); ?></label></th>
                    <td>
                        <input type="text" id="program_title_english" name="program_title_english" 
                               value="<?php echo esc_attr($title_en); ?>" class="large-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="program_intro_english"><?php _e('Introduction (English)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_intro_english" name="program_intro_english" rows="4" class="large-text"><?php echo esc_textarea($intro_en); ?></textarea>
                        <p class="description"><?php _e('Introduction and project rationale', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_description_english"><?php _e('Description (English)', 'kilismile'); ?></label></th>
                    <td>
                        <?php 
                        wp_editor($description_en, 'program_description_english', array(
                            'textarea_name' => 'program_description_english',
                            'textarea_rows' => 10,
                            'media_buttons' => true,
                        )); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_objectives_english"><?php _e('Objectives (English)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_objectives_english" name="program_objectives_english" rows="6" class="large-text"><?php echo esc_textarea($objectives_en); ?></textarea>
                        <p class="description"><?php _e('Program objectives (one per line)', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_activities_english"><?php _e('Activities (English)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_activities_english" name="program_activities_english" rows="6" class="large-text"><?php echo esc_textarea($activities_en); ?></textarea>
                        <p class="description"><?php _e('Program activities (one per line)', 'kilismile'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_outcomes_english"><?php _e('Expected Outcomes (English)', 'kilismile'); ?></label></th>
                    <td>
                        <textarea id="program_outcomes_english" name="program_outcomes_english" rows="6" class="large-text"><?php echo esc_textarea($outcomes_en); ?></textarea>
                        <p class="description"><?php _e('Expected outcomes (one per line)', 'kilismile'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.tab-btn').on('click', function() {
            var tab = $(this).data('tab');
            
            // Update buttons
            $('.tab-btn').removeClass('active').css({
                'background': '#e0e0e0',
                'color': '#333'
            });
            $(this).addClass('active').css({
                'background': '#4CAF50',
                'color': 'white'
            });
            
            // Update content
            $('.tab-content').hide();
            $('#' + tab + '-tab').show();
        });
    });
    </script>
    <?php
}

/**
 * Detailed Program Info Meta Box
 */
function kilismile_program_detailed_meta_box($post) {
    wp_nonce_field('kilismile_program_detailed_nonce', 'kilismile_program_detailed_nonce');
    
    $implementing_org = get_post_meta($post->ID, '_program_implementing_org', true);
    $location = get_post_meta($post->ID, '_program_location', true);
    $activity_plan = get_post_meta($post->ID, '_program_activity_plan', true);
    $start_date = get_post_meta($post->ID, '_program_start_date', true);
    $end_date = get_post_meta($post->ID, '_program_end_date', true);
    $duration = get_post_meta($post->ID, '_program_duration', true);
    $target_audience = get_post_meta($post->ID, '_program_target_audience', true);
    $status = get_post_meta($post->ID, '_program_status', true);
    $beneficiaries = get_post_meta($post->ID, '_program_beneficiaries', true);
    $featured = get_post_meta($post->ID, '_program_featured', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="program_implementing_org"><?php _e('Implementing Organization', 'kilismile'); ?></label></th>
            <td>
                <input type="text" id="program_implementing_org" name="program_implementing_org" 
                       value="<?php echo esc_attr($implementing_org); ?>" class="regular-text" />
                <p class="description"><?php _e('Shirika linalotekeleza mradi', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_location"><?php _e('Location', 'kilismile'); ?></label></th>
            <td>
                <input type="text" id="program_location" name="program_location" 
                       value="<?php echo esc_attr($location); ?>" class="regular-text" />
                <p class="description"><?php _e('Eneo la utekelezaji', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_activity_plan"><?php _e('Activity Plan (Date | Location | Activity)', 'kilismile'); ?></label></th>
            <td>
                <textarea id="program_activity_plan" name="program_activity_plan" rows="4" class="large-text" placeholder="2026-02-10 | Moshi | Community screening
2026-03-05 | Arusha | School outreach"><?php echo esc_textarea($activity_plan); ?></textarea>
                <p class="description"><?php _e('One activity per line using: Date | Location | Activity', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_start_date"><?php _e('Start Date', 'kilismile'); ?></label></th>
            <td>
                <input type="date" id="program_start_date" name="program_start_date" 
                       value="<?php echo esc_attr($start_date); ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="program_end_date"><?php _e('End Date', 'kilismile'); ?></label></th>
            <td>
                <input type="date" id="program_end_date" name="program_end_date" 
                       value="<?php echo esc_attr($end_date); ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="program_duration"><?php _e('Duration', 'kilismile'); ?></label></th>
            <td>
                <input type="text" id="program_duration" name="program_duration" 
                       value="<?php echo esc_attr($duration); ?>" class="regular-text" />
                <p class="description"><?php _e('e.g., "One Week", "3 Months", "1 Year"', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_target_audience"><?php _e('Target Audience', 'kilismile'); ?></label></th>
            <td>
                <select id="program_target_audience" name="program_target_audience">
                    <option value="children" <?php selected($target_audience, 'children'); ?>><?php _e('Children', 'kilismile'); ?></option>
                    <option value="elderly" <?php selected($target_audience, 'elderly'); ?>><?php _e('Elderly', 'kilismile'); ?></option>
                    <option value="teachers" <?php selected($target_audience, 'teachers'); ?>><?php _e('Teachers', 'kilismile'); ?></option>
                    <option value="community" <?php selected($target_audience, 'community'); ?>><?php _e('Community', 'kilismile'); ?></option>
                    <option value="mixed" <?php selected($target_audience, 'mixed'); ?>><?php _e('Mixed (Children & Elderly)', 'kilismile'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="program_status"><?php _e('Status', 'kilismile'); ?></label></th>
            <td>
                <select id="program_status" name="program_status">
                    <option value="planned" <?php selected($status, 'planned'); ?>><?php _e('Planned', 'kilismile'); ?></option>
                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'kilismile'); ?></option>
                    <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'kilismile'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="program_beneficiaries"><?php _e('Number of Beneficiaries', 'kilismile'); ?></label></th>
            <td>
                <input type="number" id="program_beneficiaries" name="program_beneficiaries" 
                       value="<?php echo esc_attr($beneficiaries); ?>" min="0" />
            </td>
        </tr>
        <tr>
            <th><label for="program_featured"><?php _e('Featured Program', 'kilismile'); ?></label></th>
            <td>
                <input type="checkbox" id="program_featured" name="program_featured" value="yes" 
                       <?php checked($featured, 'yes'); ?> />
                <label for="program_featured"><?php _e('Show this program as featured on the programs page', 'kilismile'); ?></label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Objectives & Activities Meta Box
 */
function kilismile_program_objectives_meta_box($post) {
    wp_nonce_field('kilismile_program_objectives_nonce', 'kilismile_program_objectives_nonce');
    
    $main_objective = get_post_meta($post->ID, '_program_main_objective', true);
    $sustainability = get_post_meta($post->ID, '_program_sustainability', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="program_main_objective"><?php _e('Main Objective', 'kilismile'); ?></label></th>
            <td>
                <textarea id="program_main_objective" name="program_main_objective" rows="3" class="large-text"><?php echo esc_textarea($main_objective); ?></textarea>
                <p class="description"><?php _e('Lengo kuu la mradi / Main goal of the program', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_sustainability"><?php _e('Sustainability', 'kilismile'); ?></label></th>
            <td>
                <textarea id="program_sustainability" name="program_sustainability" rows="4" class="large-text"><?php echo esc_textarea($sustainability); ?></textarea>
                <p class="description"><?php _e('Uendelevu wa mradi / How the program will continue after completion', 'kilismile'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Budget Meta Box
 */
function kilismile_program_budget_meta_box($post) {
    wp_nonce_field('kilismile_program_budget_nonce', 'kilismile_program_budget_nonce');
    
    $budget_total = get_post_meta($post->ID, '_program_budget_total', true);
    $budget_file = get_post_meta($post->ID, '_program_budget_file', true);
    $funding_source = get_post_meta($post->ID, '_program_funding_source', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="program_budget_total"><?php _e('Total Budget', 'kilismile'); ?></label></th>
            <td>
                <input type="text" id="program_budget_total" name="program_budget_total" 
                       value="<?php echo esc_attr($budget_total); ?>" class="regular-text" />
                <p class="description"><?php _e('e.g., "TZS 5,000,000" or "USD 2,000"', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_budget_file"><?php _e('Budget File URL', 'kilismile'); ?></label></th>
            <td>
                <input type="url" id="program_budget_file" name="program_budget_file" 
                       value="<?php echo esc_url($budget_file); ?>" class="regular-text" />
                <p class="description"><?php _e('Link to budget Excel/PDF file', 'kilismile'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="program_funding_source"><?php _e('Funding Source', 'kilismile'); ?></label></th>
            <td>
                <textarea id="program_funding_source" name="program_funding_source" rows="3" class="large-text"><?php echo esc_textarea($funding_source); ?></textarea>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Enhanced Program Meta Boxes
 */
function kilismile_save_enhanced_program_meta($post_id) {
    // Check nonces and permissions
    if (!isset($_POST['kilismile_program_bilingual_nonce']) || 
        !wp_verify_nonce($_POST['kilismile_program_bilingual_nonce'], 'kilismile_program_bilingual_nonce')) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save bilingual content
    $bilingual_fields = array(
        'program_title_swahili', 'program_intro_swahili', 'program_description_swahili',
        'program_objectives_swahili', 'program_activities_swahili', 'program_outcomes_swahili',
        'program_title_english', 'program_intro_english', 'program_description_english',
        'program_objectives_english', 'program_activities_english', 'program_outcomes_english'
    );
    
    foreach ($bilingual_fields as $field) {
        if (isset($_POST[$field])) {
            if (strpos($field, 'description') !== false) {
                update_post_meta($post_id, '_' . $field, wp_kses_post($_POST[$field]));
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_textarea_field($_POST[$field]));
            }
        }
    }
    
    // Save detailed info
    $detailed_fields = array(
        'program_implementing_org', 'program_location', 'program_activity_plan', 'program_start_date', 'program_end_date',
        'program_duration', 'program_target_audience', 'program_status', 'program_beneficiaries'
    );
    
    foreach ($detailed_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Save featured status
    $featured = isset($_POST['program_featured']) ? 'yes' : 'no';
    update_post_meta($post_id, '_program_featured', $featured);
    
    // Save objectives
    if (isset($_POST['program_main_objective'])) {
        update_post_meta($post_id, '_program_main_objective', sanitize_textarea_field($_POST['program_main_objective']));
    }
    if (isset($_POST['program_sustainability'])) {
        update_post_meta($post_id, '_program_sustainability', sanitize_textarea_field($_POST['program_sustainability']));
    }
    
    // Save budget
    if (isset($_POST['program_budget_total'])) {
        update_post_meta($post_id, '_program_budget_total', sanitize_text_field($_POST['program_budget_total']));
    }
    if (isset($_POST['program_budget_file'])) {
        update_post_meta($post_id, '_program_budget_file', esc_url_raw($_POST['program_budget_file']));
    }
    if (isset($_POST['program_funding_source'])) {
        update_post_meta($post_id, '_program_funding_source', sanitize_textarea_field($_POST['program_funding_source']));
    }
}
add_action('save_post_programs', 'kilismile_save_enhanced_program_meta');

/**
 * Get Program Content by Language
 */
function kilismile_get_program_content($post_id, $field, $language = 'swahili') {
    $meta_key = '_program_' . $field . '_' . $language;
    $content = get_post_meta($post_id, $meta_key, true);
    
    // Fallback to post title/editor if translation not available
    if (empty($content)) {
        if ($field === 'title') {
            return get_the_title($post_id);
        } elseif ($field === 'description') {
            return get_the_content(null, false, $post_id);
        }
    }
    
    return $content;
}

/**
 * Display Program Content with Language Toggle
 */
function kilismile_display_program_content($post_id, $current_lang = 'swahili') {
    $other_lang = ($current_lang === 'swahili') ? 'english' : 'swahili';
    
    $title = kilismile_get_program_content($post_id, 'title', $current_lang);
    $description = kilismile_get_program_content($post_id, 'description', $current_lang);
    $objectives = kilismile_get_program_content($post_id, 'objectives', $current_lang);
    $activities = kilismile_get_program_content($post_id, 'activities', $current_lang);
    $outcomes = kilismile_get_program_content($post_id, 'outcomes', $current_lang);
    
    return array(
        'title' => $title,
        'description' => $description,
        'objectives' => $objectives,
        'activities' => $activities,
        'outcomes' => $outcomes,
        'other_lang' => $other_lang
    );
}
