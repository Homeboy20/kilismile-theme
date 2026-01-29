<?php
/**
 * Single Program Template with Bilingual Support
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

get_header();

// Use a single language view (no translation toggle)
$current_lang = 'english';
$program_content = kilismile_display_program_content(get_the_ID(), $current_lang);

// Get program meta
$implementing_org = get_post_meta(get_the_ID(), '_program_implementing_org', true);
$location = get_post_meta(get_the_ID(), '_program_location', true);
$start_date = get_post_meta(get_the_ID(), '_program_start_date', true);
$end_date = get_post_meta(get_the_ID(), '_program_end_date', true);
$duration = get_post_meta(get_the_ID(), '_program_duration', true);
$status = get_post_meta(get_the_ID(), '_program_status', true);
$beneficiaries = get_post_meta(get_the_ID(), '_program_beneficiaries', true);
$budget = get_post_meta(get_the_ID(), '_program_budget_total', true);
$main_objective = get_post_meta(get_the_ID(), '_program_main_objective', true);
$sustainability = get_post_meta(get_the_ID(), '_program_sustainability', true);
$activity_plan = get_post_meta(get_the_ID(), '_program_activity_plan', true);

// Parse objectives, activities, outcomes
$objectives = !empty($program_content['objectives']) ? explode("\n", $program_content['objectives']) : array();
$activities = !empty($program_content['activities']) ? explode("\n", $program_content['activities']) : array();
$outcomes = !empty($program_content['outcomes']) ? explode("\n", $program_content['outcomes']) : array();
?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="program-hero" style="background: var(--dark-green); color: white; padding: 60px 0; text-align: center;">
        <div class="container">
            <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 20px; font-weight: 700;">
                <?php echo esc_html($program_content['title'] ?: get_the_title()); ?>
            </h1>
            <?php if ($status): ?>
                <span class="program-status-badge" style="display: inline-block; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 25px; font-size: 0.9rem; margin-bottom: 20px;">
                    <?php 
                    $status_labels = array(
                        'planned' => $current_lang === 'swahili' ? 'Iliyopangwa' : 'Planned',
                        'active' => $current_lang === 'swahili' ? 'Inaendelea' : 'Active',
                        'completed' => $current_lang === 'swahili' ? 'Imekamilika' : 'Completed'
                    );
                    echo esc_html($status_labels[$status] ?? ucfirst($status));
                    ?>
                </span>
            <?php endif; ?>
        </div>
    </section>

    <!-- Program Details -->
    <section class="program-details" style="padding: 60px 0; background: white;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
                <!-- Main Content -->
                <div class="program-main-content">
                    <!-- Activities Plan -->
                    <?php if (!empty($activity_plan)) :
                        $activity_lines = array_filter(array_map('trim', explode("\n", (string) $activity_plan)));
                        ?>
                        <div class="program-activities" style="margin-bottom: 40px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.8rem;">
                                <?php echo esc_html__('Activities Plan', 'kilismile'); ?>
                            </h2>
                            <div style="display: grid; gap: 10px;">
                                <?php foreach ($activity_lines as $line) :
                                    $parts = array_map('trim', explode('|', $line));
                                    $date = $parts[0] ?? '';
                                    $location = $parts[1] ?? '';
                                    $activity = $parts[2] ?? '';
                                    ?>
                                    <div style="background: var(--light-gray); padding: 14px; border-radius: 6px; border-left: 4px solid var(--primary-green);">
                                        <div style="font-weight: 700; color: var(--primary-green); font-size: 0.95rem;">
                                            <?php echo esc_html($date); ?>
                                        </div>
                                        <div style="color: var(--dark-green); font-size: 1rem; margin: 4px 0;">
                                            <?php echo esc_html($activity); ?>
                                        </div>
                                        <div style="color: var(--text-secondary); font-size: 0.9rem;">
                                            <?php echo esc_html($location); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- Introduction -->
                    <?php if ($program_content['description']): ?>
                        <div class="program-intro" style="margin-bottom: 40px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.8rem;">
                                <?php echo $current_lang === 'swahili' ? 'Utangulizi' : 'Introduction'; ?>
                            </h2>
                            <div style="line-height: 1.8; color: var(--text-secondary);">
                                <?php echo wp_kses_post($program_content['description']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Main Objective -->
                    <?php if ($main_objective): ?>
                        <div class="program-objective" style="margin-bottom: 40px; padding: 30px; background: var(--light-gray); border-left: 4px solid var(--primary-green); border-radius: 5px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.5rem;">
                                <?php echo $current_lang === 'swahili' ? 'Lengo Kuu' : 'Main Objective'; ?>
                            </h2>
                            <p style="line-height: 1.8; color: var(--text-secondary); margin: 0;">
                                <?php echo nl2br(esc_html($main_objective)); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Objectives -->
                    <?php if (!empty($objectives) && !empty(array_filter($objectives))): ?>
                        <div class="program-objectives" style="margin-bottom: 40px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.8rem;">
                                <?php echo $current_lang === 'swahili' ? 'Malengo Mahususi' : 'Specific Objectives'; ?>
                            </h2>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($objectives as $index => $objective): 
                                    if (trim($objective)): ?>
                                        <li style="padding: 15px; margin-bottom: 10px; background: var(--light-gray); border-left: 4px solid var(--accent-green); border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; background: var(--primary-green); color: white; border-radius: 50%; font-weight: bold; flex-shrink: 0;">
                                                <?php echo $index + 1; ?>
                                            </span>
                                            <span style="line-height: 1.6; color: var(--text-secondary);">
                                                <?php echo esc_html(trim($objective)); ?>
                                            </span>
                                        </li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Activities -->
                    <?php if (!empty($activities) && !empty(array_filter($activities))): ?>
                        <div class="program-activities" style="margin-bottom: 40px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.8rem;">
                                <?php echo $current_lang === 'swahili' ? 'Shughuli Kuu za Mradi' : 'Main Activities'; ?>
                            </h2>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($activities as $activity): 
                                    if (trim($activity)): ?>
                                        <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; align-items: start; gap: 12px;">
                                            <i class="fas fa-check-circle" style="color: var(--primary-green); margin-top: 4px; flex-shrink: 0;"></i>
                                            <span style="line-height: 1.6; color: var(--text-secondary);">
                                                <?php echo esc_html(trim($activity)); ?>
                                            </span>
                                        </li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Expected Outcomes -->
                    <?php if (!empty($outcomes) && !empty(array_filter($outcomes))): ?>
                        <div class="program-outcomes" style="margin-bottom: 40px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.8rem;">
                                <?php echo $current_lang === 'swahili' ? 'Matokeo Yanayotarajiwa' : 'Expected Outcomes'; ?>
                            </h2>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($outcomes as $outcome): 
                                    if (trim($outcome)): ?>
                                        <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; align-items: start; gap: 12px;">
                                            <i class="fas fa-arrow-right" style="color: var(--accent-green); margin-top: 4px; flex-shrink: 0;"></i>
                                            <span style="line-height: 1.6; color: var(--text-secondary);">
                                                <?php echo esc_html(trim($outcome)); ?>
                                            </span>
                                        </li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Sustainability -->
                    <?php if ($sustainability): ?>
                        <div class="program-sustainability" style="margin-bottom: 40px; padding: 30px; background: var(--light-gray); border-radius: 5px;">
                            <h2 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.5rem;">
                                <?php echo $current_lang === 'swahili' ? 'Uendelevu wa Mradi' : 'Project Sustainability'; ?>
                            </h2>
                            <p style="line-height: 1.8; color: var(--text-secondary); margin: 0;">
                                <?php echo nl2br(esc_html($sustainability)); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="program-sidebar">
                    <div style="position: sticky; top: 20px;">
                        <!-- Program Info Card -->
                        <div class="program-info-card" style="background: white; border: 1px solid var(--border-color); border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.3rem; border-bottom: 2px solid var(--primary-green); padding-bottom: 10px;">
                                <?php echo $current_lang === 'swahili' ? 'Taarifa za Mradi' : 'Program Information'; ?>
                            </h3>
                            
                            <?php if ($implementing_org): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <?php echo $current_lang === 'swahili' ? 'Shirika Linalotekeleza:' : 'Implementing Organization:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary);"><?php echo esc_html($implementing_org); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($location): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                                        <?php echo $current_lang === 'swahili' ? 'Eneo:' : 'Location:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary);"><?php echo esc_html($location); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($duration): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
                                        <?php echo $current_lang === 'swahili' ? 'Muda:' : 'Duration:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary);"><?php echo esc_html($duration); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($start_date && $end_date): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <i class="fas fa-clock" style="margin-right: 5px;"></i>
                                        <?php echo $current_lang === 'swahili' ? 'Tarehe:' : 'Dates:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary);">
                                        <?php echo date_i18n(get_option('date_format'), strtotime($start_date)); ?> - 
                                        <?php echo date_i18n(get_option('date_format'), strtotime($end_date)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($beneficiaries): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <i class="fas fa-users" style="margin-right: 5px;"></i>
                                        <?php echo $current_lang === 'swahili' ? 'Wanufaika:' : 'Beneficiaries:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary); font-size: 1.2rem; font-weight: bold; color: var(--primary-green);">
                                        <?php echo number_format($beneficiaries); ?>+
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($budget): ?>
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: var(--dark-green); display: block; margin-bottom: 5px;">
                                        <i class="fas fa-money-bill-wave" style="margin-right: 5px;"></i>
                                        <?php echo $current_lang === 'swahili' ? 'Bajeti:' : 'Budget:'; ?>
                                    </strong>
                                    <span style="color: var(--text-secondary);"><?php echo esc_html($budget); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Call to Action -->
                        <div class="program-cta" style="background: var(--primary-green); color: white; padding: 25px; border-radius: 10px; text-align: center;">
                            <h3 style="margin-bottom: 15px; font-size: 1.2rem;">
                                <?php echo $current_lang === 'swahili' ? 'Jihusishe na Mradi' : 'Get Involved'; ?>
                            </h3>
                            <p style="margin-bottom: 20px; opacity: 0.9;">
                                <?php echo $current_lang === 'swahili' ? 'Tunaweza pamoja kuleta mabadiliko chanya' : 'Together we can make a positive impact'; ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/donation')); ?>" 
                               class="button" 
                               style="display: inline-block; padding: 12px 30px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 25px; font-weight: 600; transition: transform 0.3s;">
                                <?php echo $current_lang === 'swahili' ? 'Changia' : 'Donate'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    @media (max-width: 768px) {
        .program-details > .container > div {
            grid-template-columns: 1fr;
        }
        
        .program-sidebar {
            margin-top: 40px;
        }
    }
</style>

<?php get_footer(); ?>
