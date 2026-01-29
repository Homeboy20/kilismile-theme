<?php
/**
 * Template Name: Programs Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="programs-hero" style="background: var(--dark-green); padding: 120px 0 80px; color: white; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                <?php _e('Our Programs', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; opacity: 0.95; line-height: 1.6;">
                <?php _e('Comprehensive health education and community development initiatives designed to create lasting positive impact across Tanzania.', 'kilismile'); ?>
            </p>
            <div class="hero-stats" style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 40px;">
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;">25+</div>
                    <div style="opacity: 0.9;"><?php _e('Active Programs', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;">15</div>
                    <div style="opacity: 0.9;"><?php _e('Districts Served', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;">50,000+</div>
                    <div style="opacity: 0.9;"><?php _e('People Reached', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Plan -->
    <section class="program-activities" style="padding: 60px 0; background: white; border-bottom: 1px solid var(--border-color);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2rem; margin-bottom: 15px;">
                <?php _e('Upcoming Activities Plan', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); max-width: 700px; margin: 0 auto 30px; line-height: 1.6;">
                <?php _e('Planned activities with dates and locations for each program.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <?php
                $programs_plan_query = new WP_Query(array(
                    'post_type' => 'programs',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => '_program_activity_plan',
                            'value' => '',
                            'compare' => '!='
                        )
                    )
                ));

                if ($programs_plan_query->have_posts()) :
                    while ($programs_plan_query->have_posts()) : $programs_plan_query->the_post();
                        $activity_plan = get_post_meta(get_the_ID(), '_program_activity_plan', true);
                        if (empty($activity_plan)) {
                            $title = strtolower(get_the_title());
                            if (strpos($title, 'non communicable disease screening program') !== false) {
                                $activity_plan = "2026-03-14 | Moshi | Non communicable disease screening\n2026-03-15 | Moshi | Non communicable disease screening\n2026-03-16 | Moshi | Non communicable disease screening";
                            } elseif (strpos($title, 'school oral health program') !== false) {
                                $activity_plan = "2026-03-17 | Moshi | School oral health program\n2026-03-18 | Moshi | School oral health program\n2026-03-19 | Moshi | School oral health program";
                            }
                        }
                        $lines = array_filter(array_map('trim', explode("\n", (string) $activity_plan)));
                        if (empty($lines)) {
                            continue;
                        }
                        ?>
                        <div style="background: var(--light-gray); padding: 20px; border-radius: 12px; border: 1px solid rgba(76, 175, 80, 0.12);">
                            <h3 style="margin: 0 0 12px; color: var(--dark-green); font-size: 1.1rem;">
                                <?php the_title(); ?>
                            </h3>
                            <ul style="list-style: none; margin: 0; padding: 0; display: grid; gap: 10px;">
                                <?php foreach ($lines as $line) :
                                    $parts = array_map('trim', explode('|', $line));
                                    $date = $parts[0] ?? '';
                                    $location = $parts[1] ?? '';
                                    $activity = $parts[2] ?? '';
                                    ?>
                                    <li style="background: white; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                                        <div style="font-weight: 600; color: var(--primary-green); font-size: 0.9rem;">
                                            <?php echo esc_html($date); ?>
                                        </div>
                                        <div style="color: var(--dark-green); font-size: 0.95rem; margin: 2px 0;">
                                            <?php echo esc_html($activity); ?>
                                        </div>
                                        <div style="color: var(--text-secondary); font-size: 0.85rem;">
                                            <?php echo esc_html($location); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else : ?>
                    <div style="text-align: center; padding: 20px; color: var(--text-secondary);">
                        <?php _e('No activity plans have been added yet.', 'kilismile'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Program Categories -->
    <section class="program-categories" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Program Categories', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Our programs are organized into key focus areas to maximize impact and effectiveness.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <!-- Maternal & Child Health -->
                <div class="category-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; position: relative; overflow: hidden;">
                    <div class="category-icon" style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-baby" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.6rem;">
                        <?php _e('Maternal & Child Health', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px;">
                        <?php _e('Comprehensive programs supporting mothers and children through pregnancy, birth, and early childhood development with education and resources.', 'kilismile'); ?>
                    </p>
                    <div class="program-stats" style="display: flex; justify-content: space-around; margin-bottom: 25px; padding: 15px; background: var(--light-gray); border-radius: 10px;">
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--primary-green);">8</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Programs', 'kilismile'); ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--primary-green);">12,000+</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Beneficiaries', 'kilismile'); ?></div>
                        </div>
                    </div>
                    <a href="#maternal-child" class="view-programs-btn" style="display: inline-block; padding: 12px 25px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View Programs', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Community Health Education -->
                <div class="category-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div class="category-icon" style="width: 80px; height: 80px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.6rem;">
                        <?php _e('Community Health Education', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px;">
                        <?php _e('Educational workshops and campaigns promoting health awareness, disease prevention, and healthy lifestyle choices in communities.', 'kilismile'); ?>
                    </p>
                    <div class="program-stats" style="display: flex; justify-content: space-around; margin-bottom: 25px; padding: 15px; background: var(--light-gray); border-radius: 10px;">
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--accent-green);">12</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Programs', 'kilismile'); ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--accent-green);">25,000+</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Participants', 'kilismile'); ?></div>
                        </div>
                    </div>
                    <a href="#community-health" class="view-programs-btn" style="display: inline-block; padding: 12px 25px; background: var(--accent-green); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View Programs', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Youth Development -->
                <div class="category-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div class="category-icon" style="width: 80px; height: 80px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.6rem;">
                        <?php _e('Youth Development', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px;">
                        <?php _e('Empowering young people through health education, life skills training, and leadership development programs.', 'kilismile'); ?>
                    </p>
                    <div class="program-stats" style="display: flex; justify-content: space-around; margin-bottom: 25px; padding: 15px; background: var(--light-gray); border-radius: 10px;">
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--dark-green);">6</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Programs', 'kilismile'); ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: var(--dark-green);">8,500+</div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Youth Reached', 'kilismile'); ?></div>
                        </div>
                    </div>
                    <a href="#youth-development" class="view-programs-btn" style="display: inline-block; padding: 12px 25px; background: var(--dark-green); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View Programs', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Programs -->
    <section class="featured-programs" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Featured Programs', 'kilismile'); ?>
            </h2>

            <?php
            $featured_programs = new WP_Query(array(
                'post_type' => 'programs',
                'posts_per_page' => 6,
                'meta_query' => array(
                    array(
                        'key' => '_program_featured',
                        'value' => 'yes',
                        'compare' => '='
                    )
                )
            ));

            if ($featured_programs->have_posts()) : ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
                    <?php while ($featured_programs->have_posts()) : $featured_programs->the_post();
                        $target_audience = get_post_meta(get_the_ID(), '_program_target_audience', true);
                        $status = get_post_meta(get_the_ID(), '_program_status', true);
                        $participants = get_post_meta(get_the_ID(), '_program_participants', true);
                        $location = get_post_meta(get_the_ID(), '_program_location', true);
                        ?>
                        <article class="program-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="program-image" style="height: 250px; overflow: hidden; position: relative;">
                                    <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;')); ?>
                                    
                                    <!-- Status Badge -->
                                    <?php if ($status) : ?>
                                        <div class="status-badge" style="position: absolute; top: 15px; right: 15px;">
                                            <?php echo kilismile_get_program_status_badge($status); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="program-content" style="padding: 30px;">
                                <div class="program-meta" style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                                    <?php if ($target_audience) : ?>
                                        <?php echo kilismile_get_target_audience_badge($target_audience); ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($location) : ?>
                                        <span style="background: var(--light-gray); color: var(--text-secondary); padding: 4px 12px; border-radius: 15px; font-size: 0.8rem;">
                                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?php echo esc_html($location); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem; line-height: 1.3;">
                                    <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </p>
                                
                                <?php if ($participants) : ?>
                                    <div class="participants-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; color: var(--primary-green); font-size: 0.9rem;">
                                        <i class="fas fa-users" aria-hidden="true"></i>
                                        <span><?php printf(__('%s participants', 'kilismile'), $participants); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <a href="<?php the_permalink(); ?>" class="learn-more-btn" style="color: var(--primary-green); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                                        <?php _e('Learn More', 'kilismile'); ?>
                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                    
                                    <div class="program-actions" style="display: flex; gap: 10px;">
                                        <button onclick="shareProgram('<?php echo esc_js(get_the_title()); ?>', '<?php echo esc_js(get_permalink()); ?>')" 
                                                style="background: none; border: none; color: var(--medium-gray); cursor: pointer; padding: 8px; border-radius: 50%; transition: all 0.3s ease;"
                                                title="<?php _e('Share Program', 'kilismile'); ?>">
                                            <i class="fas fa-share-alt" aria-hidden="true"></i>
                                        </button>
                                        <button onclick="saveProgram(<?php echo get_the_ID(); ?>)" 
                                                style="background: none; border: none; color: var(--medium-gray); cursor: pointer; padding: 8px; border-radius: 50%; transition: all 0.3s ease;"
                                                title="<?php _e('Save Program', 'kilismile'); ?>">
                                            <i class="fas fa-bookmark" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <div style="text-align: center; margin-top: 60px;">
                    <a href="<?php echo esc_url(get_post_type_archive_link('programs')); ?>" 
                       class="btn btn-primary" 
                       style="display: inline-block; padding: 15px 30px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View All Programs', 'kilismile'); ?>
                    </a>
                </div>
                
                <?php wp_reset_postdata();
            else : ?>
                <!-- Placeholder programs if none exist -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
                    <?php for ($i = 1; $i <= 3; $i++) : ?>
                        <article class="program-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div class="program-image" style="height: 250px; background: var(--light-gray); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-heartbeat" style="font-size: 4rem; color: var(--medium-gray);" aria-hidden="true"></i>
                            </div>
                            <div class="program-content" style="padding: 30px;">
                                <h3 style="color: var(--dark-green); margin-bottom: 15px;"><?php printf(__('Sample Program %d', 'kilismile'), $i); ?></h3>
                                <p style="color: var(--text-secondary); line-height: 1.6;"><?php _e('This is a sample program description. Replace with actual program content.', 'kilismile'); ?></p>
                            </div>
                        </article>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Program Impact -->
    <section class="program-impact" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                <div class="impact-content">
                    <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 30px;">
                        <?php _e('Measuring Our Impact', 'kilismile'); ?>
                    </h2>
                    <p style="color: var(--text-secondary); line-height: 1.8; font-size: 1.1rem; margin-bottom: 30px;">
                        <?php _e('We believe in transparency and accountability. Our programs are designed with clear objectives and measurable outcomes to ensure maximum effectiveness and community benefit.', 'kilismile'); ?>
                    </p>
                    
                    <div class="impact-metrics" style="margin-bottom: 30px;">
                        <div class="metric-item" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 15px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: bold; color: var(--dark-green); font-size: 1.1rem;"><?php _e('85% Improvement', 'kilismile'); ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('in health knowledge retention', 'kilismile'); ?></div>
                            </div>
                        </div>
                        
                        <div class="metric-item" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 15px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-users" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: bold; color: var(--dark-green); font-size: 1.1rem;"><?php _e('95% Satisfaction', 'kilismile'); ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('from program participants', 'kilismile'); ?></div>
                            </div>
                        </div>
                        
                        <div class="metric-item" style="display: flex; align-items: center; gap: 15px; padding: 15px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="width: 50px; height: 50px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-heart" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: bold; color: var(--dark-green); font-size: 1.1rem;"><?php _e('70% Reduction', 'kilismile'); ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('in preventable health issues', 'kilismile'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?php echo esc_url(home_url('/impact-report')); ?>" 
                       style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View Full Impact Report', 'kilismile'); ?>
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                    </a>
                </div>
                
                <div class="impact-visual" style="text-align: center;">
                    <div class="impact-chart" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <!-- Placeholder for impact visualization -->
                        <div style="width: 100%; height: 300px; background: var(--light-gray); border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-pie" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 20px;" aria-hidden="true"></i>
                            <div style="color: var(--dark-green); font-weight: bold; margin-bottom: 10px;"><?php _e('Program Impact Visualization', 'kilismile'); ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Interactive charts and graphs showing program effectiveness', 'kilismile'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Get Involved Section -->
    <section class="get-involved" style="padding: 80px 0; background: var(--primary-green); color: white; text-align: center; border-top: 4px solid var(--dark-green);">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: white; font-weight: 700;">
                <?php _e('Get Involved in Our Programs', 'kilismile'); ?>
            </h2>
            <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.95; line-height: 1.6;">
                <?php _e('Join us in making a difference. Whether you want to volunteer, donate, or partner with us, there are many ways to support our programs.', 'kilismile'); ?>
            </p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/volunteer')); ?>" 
                   class="btn btn-secondary" 
                   style="display: inline-block; padding: 15px 30px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Volunteer', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/donation')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 15px 30px; background: transparent; color: white; text-decoration: none; border: 2px solid white; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Support Our Programs', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/partnerships')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 15px 30px; background: transparent; color: white; text-decoration: none; border: 2px solid white; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Partner With Us', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<script>
// Program interaction functions
function shareProgram(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(console.error);
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showNotification('<?php _e('Program link copied to clipboard!', 'kilismile'); ?>');
        });
    }
}

function saveProgram(programId) {
    // This would typically save to user favorites or local storage
    let savedPrograms = JSON.parse(localStorage.getItem('kilismile_saved_programs') || '[]');
    
    if (!savedPrograms.includes(programId)) {
        savedPrograms.push(programId);
        localStorage.setItem('kilismile_saved_programs', JSON.stringify(savedPrograms));
        showNotification('<?php _e('Program saved to your favorites!', 'kilismile'); ?>');
        
        // Update button state
        event.target.style.color = 'var(--primary-green)';
    } else {
        showNotification('<?php _e('Program already in your favorites!', 'kilismile'); ?>');
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--primary-green);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Smooth scrolling for category links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<style>
    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .program-card:hover .program-image img {
        transform: scale(1.05);
    }
    
    .view-programs-btn:hover,
    .learn-more-btn:hover {
        transform: translateY(-2px);
    }
    
    .program-actions button:hover {
        background: var(--light-gray);
        color: var(--primary-green);
        transform: scale(1.1);
    }
    
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    .btn-outline:hover {
        background: white;
        color: var(--primary-green);
    }
    
    .metric-item:hover {
        transform: translateX(10px);
    }
    
    @media (max-width: 768px) {
        .program-impact > .container > div {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .hero-stats {
            gap: 30px;
        }
        
        .program-stats {
            flex-direction: column;
            gap: 10px;
        }
        
        .program-actions {
            flex-direction: column;
        }
    }
</style>

<?php get_footer(); ?>


