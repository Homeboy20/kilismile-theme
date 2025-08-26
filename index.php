<?php
/**
 * The main template file - Homepage Redesign
 *
 * @package KiliSmile
 * @version 2.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section with Clean Background -->
    <section class="hero-section-redesign" style="position: relative; min-height: 75vh; display: flex; align-items: center; justify-content: center; background: var(--dark-green); color: white; text-align: center; overflow: hidden; padding-top: 80px; padding-bottom: 30px;">
        <!-- Simple Subtle Pattern -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: 
            linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
            background-size: 10px 10px; 
            z-index: 1;"></div>
        
        <div class="container" style="position: relative; z-index: 2; padding-top: 10px;">
            <div class="hero-content-redesign" style="max-width: 850px; margin: 0 auto; padding: 10px 15px;">
                <!-- Organization Details -->
                <?php if (get_theme_mod('kilismile_show_hero_badge', true)) : ?>
                <div class="registration-badge" style="display: inline-block; background: rgba(0, 0, 0, 0.2); padding: 8px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <span style="font-size: 0.85rem; font-weight: 500; color: white;">Est. 25/04/2024 | NGO Reg. No: 07NGO/R/6067</span>
                </div>
                <?php endif; ?>

                <h1 class="hero-title-redesign" style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 20px; font-weight: 700; line-height: 1.2; color: white;">
                    Transforming Lives Through 
                    <span style="color: var(--light-green); display: inline; margin-top: 5px;">Health Education</span>
                </h1>
                
                <p class="hero-subtitle-redesign" style="font-size: clamp(1rem, 2vw, 1.2rem); margin-bottom: 25px; line-height: 1.5; max-width: 700px; margin-left: auto; margin-right: auto; color: rgba(255, 255, 255, 0.9);">
                    Empowering communities in Tanzania with essential oral health knowledge, teacher training, and comprehensive health screening programs.
                </p>

                <!-- CTA Buttons with Simple Design -->
                <div class="cta-buttons-redesign" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 35px;">
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_primary_btn_url', '#donate')); ?>" 
                       class="btn-primary-redesign" 
                       style="background: var(--primary-green); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="font-size: 1rem;"></i>
                        <?php echo esc_html(get_theme_mod('kilismile_primary_btn_text', 'Donate Now')); ?>
                    </a>
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_secondary_btn_url', '/about')); ?>" 
                       class="btn-secondary-redesign" 
                       style="background: rgba(255, 255, 255, 0.1); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 500; font-size: 1rem; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2); display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="font-size: 1rem;"></i>
                        <?php echo esc_html(get_theme_mod('kilismile_secondary_btn_text', 'Our Story')); ?>
                    </a>
                </div>

                <!-- Simplified Quick Stats -->
                <?php if (get_theme_mod('kilismile_show_hero_stats', true)) : ?>
            <div class="hero-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; max-width: 800px; margin: 0 auto;">
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_children', '500')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Children Reached</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_elderly', '200')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Elderly Served</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_teachers', '50')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Teachers Trained</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_areas', '10')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Remote Areas</div>
                </div>
            </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Simple Scroll Indicator -->
        <?php if (get_theme_mod('kilismile_show_scroll_indicator', true)) : ?>
        <div class="scroll-indicator" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 2;">
            <i class="fas fa-chevron-down" style="font-size: 1.2rem; color: white; opacity: 0.6;"></i>
        </div>
        <?php endif; ?>
    </section>

    <!-- Mission Section with Card Layout -->
    <section class="mission-section-redesign" style="padding: 60px 0; background: var(--light-gray); position: relative;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header" style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Our Mission Framework
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    A comprehensive approach to sustainable health improvements in Tanzania's communities.
                </p>
            </div>

            <!-- Mission Cards -->
            <div class="mission-grid-redesign" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="mission-card-redesign" style="background: white; padding: 30px 25px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; overflow: hidden; border-left: 4px solid var(--primary-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-tooth" style="font-size: 1.2rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; color: var(--dark-green); margin: 0; font-weight: 600;">Health Education</h3>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.95rem;">Providing oral and general health education to children and elderly in remote areas, ensuring access to essential health knowledge.</p>
                    <div class="progress-bar" style="background: #E8F5E8; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: var(--primary-green); height: 100%; width: 85%;"></div>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--primary-green); font-weight: 500; margin-top: 5px; display: block;">85% Implementation</span>
                </div>

                <div class="mission-card-redesign" style="background: white; padding: 30px 25px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; overflow: hidden; border-left: 4px solid var(--accent-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--accent-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-chalkboard-teacher" style="font-size: 1.2rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; color: var(--dark-green); margin: 0; font-weight: 600;">Teacher Training</h3>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.95rem;">Training primary school teachers on basic oral and general health, creating sustainable health advocates within communities.</p>
                    <div class="progress-bar" style="background: #E8F5E8; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: var(--accent-green); height: 100%; width: 70%;"></div>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--accent-green); font-weight: 500; margin-top: 5px; display: block;">70% Implementation</span>
                </div>

                <div class="mission-card-redesign" style="background: white; padding: 30px 25px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; overflow: hidden; border-left: 4px solid var(--light-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--light-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-stethoscope" style="font-size: 1.2rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; color: var(--dark-green); margin: 0; font-weight: 600;">Health Screening</h3>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.95rem;">Conducting comprehensive screening of non-communicable diseases for children and elderly, enabling early detection and intervention.</p>
                    <div class="progress-bar" style="background: #E8F5E8; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: var(--light-green); height: 100%; width: 90%;"></div>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--light-green); font-weight: 500; margin-top: 5px; display: block;">90% Implementation</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Counter Section -->
    <section class="impact-counter-section" style="padding: 60px 0; background: var(--light-gray); color: var(--dark-green); position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin-bottom: 15px; font-weight: 600; color: var(--dark-green);">
                    Real Impact, Real Change
                </h2>
                <p style="font-size: 1rem; max-width: 700px; margin: 0 auto; line-height: 1.5; color: var(--text-secondary);">
                    Every number represents a life touched and a community strengthened through our health initiatives.
                </p>
            </div>

            <div class="impact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 900px; margin: 0 auto;">
                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="500">0</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Children Educated</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Comprehensive health awareness</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="200">0</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Elderly Supported</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Health screening services</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="50">0</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Teachers Trained</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Health education advocates</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="10">0</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Remote Communities</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Areas receiving care</div>
                </div>
            </div>

            <!-- Milestones Timeline -->
            <div class="milestones-section" style="margin-top: 50px; text-align: center; background: white; padding: 30px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                <h2 style="font-size: 1.5rem; margin-bottom: 25px; color: var(--dark-green); font-weight: 600; position: relative; padding-bottom: 10px; display: inline-block;">
                    Our Journey Since April 2024
                    <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
                </h2>
                
                <!-- Timeline -->
                <div class="timeline" style="position: relative; max-width: 900px; margin: 30px auto 0;">
                    <!-- Timeline line -->
                    <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: var(--light-green); transform: translateX(-50%);"></div>
                    
                    <!-- Timeline items -->
                    <div class="timeline-item" style="position: relative; margin-bottom: 30px; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-right: auto; border-left: 3px solid var(--primary-green);">
                            <div class="timeline-year" style="color: var(--primary-green); font-weight: bold; font-size: 1rem; margin-bottom: 5px;">April 2024</div>
                            <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.1rem;">Launch</h3>
                            <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.9rem;">Kili Smile Organization was officially registered with NGO number 07NGO/R/6067.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>

                    <div class="timeline-item" style="position: relative; margin-bottom: 30px; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-left: auto; border-left: 3px solid var(--primary-green);">
                            <div class="timeline-year" style="color: var(--primary-green); font-weight: bold; font-size: 1rem; margin-bottom: 5px;">May 2024</div>
                            <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.1rem;">First Outreach</h3>
                            <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.9rem;">Conducted our first community health education program in Moshi Rural District.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>

                    <div class="timeline-item" style="position: relative; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: var(--primary-green); color: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-right: auto;">
                            <div class="timeline-year" style="color: white; font-weight: bold; font-size: 1rem; margin-bottom: 5px;">August 2025</div>
                            <h3 style="color: white; margin-bottom: 8px; font-size: 1.1rem;">Major Milestone</h3>
                            <p style="color: white; opacity: 0.9; line-height: 1.5; font-size: 0.9rem;">Reaching over 500 lives through our comprehensive health education programs.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>
                </div>
                
                <!-- Future Goals Banner -->
                <div style="margin-top: 40px; background: var(--light-gray); padding: 25px; border-radius: 4px; max-width: 600px; margin-left: auto; margin-right: auto; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border-left: 3px solid var(--primary-green);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-bullseye" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <h4 style="font-size: 1.2rem; font-weight: 600; margin: 0; color: var(--dark-green);">Future Goals: 2026</h4>
                    </div>
                    <p style="margin: 10px 0 0; line-height: 1.5; color: var(--text-secondary); font-size: 0.95rem; text-align: left; padding-left: 55px;">Expanding to 5 more remote communities and reaching an additional 1,000 children through our enhanced health education curriculum.</p>
                </div>
            </div>
        </div>
        
        <!-- Simplified animation keyframes and responsive styles -->
        <style>
            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            
            /* Responsive styles for milestones timeline */
            @media (max-width: 768px) {
                .timeline-item .timeline-content {
                    width: calc(100% - 30px) !important;
                    margin: 0 15px !important;
                }
                
                .timeline {
                    padding: 0 10px;
                }
            }
        </style>
    </section>

    <!-- Programs Showcase -->
    <section class="programs-showcase" style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Our Health Programs
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    From education to screening, our integrated approach addresses the full spectrum of community health needs.
                </p>
            </div>

            <div class="programs-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php
                $programs = new WP_Query(array(
                    'post_type' => 'programs',
                    'posts_per_page' => 3,
                    'post_status' => 'publish'
                ));
                
                if ($programs->have_posts()) :
                    $program_icons = ['fas fa-tooth', 'fas fa-chalkboard-teacher', 'fas fa-stethoscope'];
                    $program_colors = ['var(--primary-green)', 'var(--accent-green)', 'var(--light-green)'];
                    $icon_index = 0;
                    
                    while ($programs->have_posts()) : $programs->the_post();
                        $target_audience = get_post_meta(get_the_ID(), '_program_target_audience', true);
                        $status = get_post_meta(get_the_ID(), '_program_status', true);
                        $beneficiaries = get_post_meta(get_the_ID(), '_program_beneficiaries', true);
                        $current_icon = $program_icons[$icon_index % 3];
                        $current_color = $program_colors[$icon_index % 3];
                ?>
                <div class="program-card-redesign" style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; border: 1px solid rgba(76, 175, 80, 0.1);">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="program-image-container" style="height: 180px; position: relative; overflow: hidden;">
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                            <?php if ($status) : ?>
                                <div style="position: absolute; top: 10px; left: 10px; background: <?php echo $current_color; ?>; color: white; padding: 4px 10px; border-radius: 2px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">
                                    <?php echo esc_html($status); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div style="height: 180px; background: rgba(76, 175, 80, 0.05); display: flex; align-items: center; justify-content: center;">
                            <i class="<?php echo $current_icon; ?>" style="font-size: 3rem; color: <?php echo $current_color; ?>; opacity: 0.5;"></i>
                        </div>
                    <?php endif; ?>

                    <div class="program-content" style="padding: 20px;">
                        <h3 style="font-size: 1.2rem; margin: 0 0 10px; font-weight: 600; color: var(--text-primary);">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <div class="program-excerpt" style="color: var(--text-secondary); margin-bottom: 15px; line-height: 1.5; font-size: 0.9rem;">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </div>
                        
                        <div class="program-meta" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; font-size: 0.85rem;">
                            <?php if ($target_audience) : ?>
                            <div class="meta-item" style="display: flex; align-items: flex-start; gap: 8px;">
                                <i class="fas fa-users" style="color: <?php echo $current_color; ?>; width: 16px; margin-top: 2px;"></i>
                                <div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1px;">Target Group</div>
                                    <div style="font-weight: 600; color: var(--text-primary);"><?php echo esc_html($target_audience); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($beneficiaries) : ?>
                            <div class="meta-item" style="display: flex; align-items: flex-start; gap: 8px;">
                                <i class="fas fa-heart" style="color: <?php echo $current_color; ?>; width: 16px; margin-top: 2px;"></i>
                                <div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1px;">Beneficiaries</div>
                                    <div style="font-weight: 600; color: var(--text-primary);"><?php echo esc_html($beneficiaries); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="program-button" style="display: inline-flex; align-items: center; padding: 8px 16px; background: <?php echo $current_color; ?>; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.9rem; transition: all 0.3s ease; gap: 6px;">
                            Learn More
                            <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
                <?php
                        $icon_index++;
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <div class="no-programs" style="grid-column: 1 / -1; text-align: center; padding: 30px 20px; background: rgba(76, 175, 80, 0.05); border-radius: 4px;">
                    <i class="fas fa-seedling" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 15px; opacity: 0.7;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.2rem;">Programs Coming Soon</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">We're developing comprehensive programs to serve our communities better.</p>
                </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo esc_url(home_url('/programs')); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary-green); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;">
                    <i class="fas fa-th-large" style="font-size: 0.9rem;"></i>
                    Explore All Programs
                </a>
            </div>
        </div>
    </section>

    <!-- News & Updates Section -->
    <section class="news-section-redesign" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Latest News & Updates
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    Stay connected with our latest community outreach, success stories, and upcoming initiatives.
                </p>
            </div>

            <div class="news-grid-redesign" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
                <style>
                    @media (min-width: 992px) {
                        .news-grid-redesign {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }
                    @media (max-width: 991px) and (min-width: 768px) {
                        .news-grid-redesign {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                </style>
                <?php
                $news_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish'
                ));
                
                if ($news_query->have_posts()) :
                    while ($news_query->have_posts()) : $news_query->the_post();
                        $reading_time = ceil(str_word_count(get_the_content()) / 200);
                ?>
                <article class="news-card-redesign" style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; border: 1px solid rgba(76, 175, 80, 0.1); height: 100%; display: flex; flex-direction: column;">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="news-image-container" style="height: 180px; position: relative; overflow: hidden;">
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                            <div style="position: absolute; top: 10px; left: 10px; background: var(--primary-green); color: white; padding: 4px 10px; border-radius: 2px; font-size: 0.75rem; font-weight: 500;">
                                <?php echo get_the_date('M j, Y'); ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div style="height: 180px; background: rgba(76, 175, 80, 0.05); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-newspaper" style="font-size: 3rem; color: var(--primary-green); opacity: 0.5;"></i>
                        </div>
                    <?php endif; ?>

                    <div class="news-content" style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.8rem; color: var(--text-secondary);">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-user" style="font-size: 0.8rem;"></i>
                                <?php echo get_the_author(); ?>
                            </div>
                            <?php 
                            $categories = get_the_category();
                            if (!empty($categories)) : 
                            ?>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-folder" style="font-size: 0.8rem;"></i>
                                <?php echo esc_html($categories[0]->name); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h3 style="font-size: 1.1rem; margin: 0 0 10px; font-weight: 600; line-height: 1.4; flex-grow: 0;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--text-primary);">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <div style="color: var(--text-secondary); margin-bottom: 15px; line-height: 1.5; font-size: 0.9rem; flex-grow: 1;">
                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" style="display: inline-flex; align-items: center; font-size: 0.85rem; color: var(--primary-green); font-weight: 600; text-decoration: none; gap: 5px; margin-top: auto;">
                            Read More
                            <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <div class="no-news" style="grid-column: 1 / -1; text-align: center; padding: 30px 20px; background: white; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <i class="fas fa-newspaper" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 15px; opacity: 0.7;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.2rem;">News Coming Soon</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">We're working on bringing you the latest updates from our field work.</p>
                </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo esc_url(home_url('/news')); ?>" class="view-all-news-btn" style="display: inline-flex; align-items: center; gap: 8px; background: var(--accent-green); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(102, 187, 106, 0.2);">
                    <i class="fas fa-newspaper" style="font-size: 0.9rem;"></i>
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- Health Quotes Section -->
    <?php if (get_theme_mod('kilismile_show_health_quotes', true)) : ?>
    <section class="health-quotes-section" style="padding: 60px 0; background: white; position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    <?php echo esc_html(get_theme_mod('kilismile_health_quotes_title', 'Health Wisdom')); ?>
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    <?php echo esc_html(get_theme_mod('kilismile_health_quotes_subtitle', 'Wisdom that guides our mission and inspires healthier communities.')); ?>
                </p>
            </div>

            <div class="health-quotes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <!-- Quote 1 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--primary-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--primary-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(76, 175, 80, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote1_text', 'A smile is a curve that sets everything straight. Oral health is not just about healthy teeth; it\'s about maintaining dignity and quality of life.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(76, 175, 80, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tooth" style="color: var(--primary-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote1_author', 'World Health Organization')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote1_source', 'Global Health')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote 2 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--accent-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--accent-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(102, 187, 106, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote2_text', 'Education is the most powerful weapon which you can use to change the world. Health education empowers communities to take control of their wellbeing.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(102, 187, 106, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-heartbeat" style="color: var(--accent-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote2_author', 'Nelson Mandela')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote2_source', 'Humanitarian Leader')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote 3 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--light-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--light-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(129, 199, 132, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote3_text', 'The greatest wealth is health. When you invest in community health education, you\'re investing in the future of humanity.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(129, 199, 132, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-hand-holding-heart" style="color: var(--light-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote3_author', 'Virgil')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote3_source', 'Ancient Wisdom')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Featured Quote -->
            <div style="margin-top: 40px; padding: 20px; background: rgba(76, 175, 80, 0.05); border-radius: 4px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fas fa-lightbulb" style="font-size: 1rem; color: var(--primary-green);"></i>
                    <div style="font-weight: 600; color: var(--dark-green); font-size: 1rem;">Featured Quote</div>
                </div>
                <blockquote style="color: var(--text-secondary); line-height: 1.6; font-size: 1rem; margin-bottom: 10px;">
                    "<?php echo esc_html(get_theme_mod('kilismile_featured_quote_text', 'Health is not valued until sickness comes. Prevention through education is the foundation of public health.')); ?>"
                </blockquote>
                <div style="font-weight: 600; color: var(--dark-green); font-size: 0.9rem; text-align: right;">
                    — <?php echo esc_html(get_theme_mod('kilismile_featured_quote_author', 'Dr. Thomas Fuller')); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call to Action Section -->
    <section class="cta-section-redesign" style="padding: 60px 0; background: var(--dark-green); color: white; text-align: center; position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; font-weight: 600; line-height: 1.3;">
                    Be Part of the Health Revolution
                </h2>

                <p style="font-size: 1rem; margin-bottom: 30px; line-height: 1.5; max-width: 700px; margin-left: auto; margin-right: auto; color: rgba(255, 255, 255, 0.9);">
                    Your support enables us to reach more remote communities with life-saving health education. 
                    Together, we can create a healthier Tanzania, one community at a time.
                </p>

                <!-- Impact Preview -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🏥</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Health Screenings</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">👨‍🏫</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Teacher Training</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🦷</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Oral Health</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🏘️</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Community Outreach</div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 25px;">
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_donation_url', '#donate')); ?>" 
                       style="background: white; color: var(--primary-green); padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="font-size: 0.9rem; color: #e74c3c;"></i>
                        Make a Donation
                    </a>
                    <a href="<?php echo esc_url(home_url('/volunteer')); ?>" 
                       style="background: rgba(255, 255, 255, 0.15); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 500; font-size: 1rem; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-hands-helping" style="font-size: 0.9rem;"></i>
                        Volunteer With Us
                    </a>
                </div>

                <!-- Quick Contact -->
                <div style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.8);">
                    Questions? <a href="<?php echo esc_url(home_url('/contact')); ?>" style="color: var(--light-green); text-decoration: underline; font-weight: 500;">Get in touch</a> 
                    or call us at <a href="tel:+255123456789" style="color: white; text-decoration: none; font-weight: 500;">+255 123 456 789</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Enhanced animations and hover effects */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-8px); }
    60% { transform: translateX(-50%) translateY(-4px); }
}

@keyframes wave {
    0% { transform: translateX(0px); }
    100% { transform: translateX(-200px); }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hero section enhancements */
.hero-section-redesign {
    /* Ensure content doesn't get hidden behind header */
    box-sizing: border-box;
}

.hero-content-redesign {
    animation: fadeInUp 1s ease-out;
    /* Ensure content has proper spacing */
    margin-top: auto;
    margin-bottom: auto;
}

.impact-badge {
    animation: fadeInUp 1s ease-out 0.2s both;
}

.hero-title-redesign {
    animation: fadeInUp 1s ease-out 0.4s both;
}

.hero-subtitle-redesign {
    animation: fadeInUp 1s ease-out 0.6s both;
}

.hero-description-redesign {
    animation: fadeInUp 1s ease-out 0.8s both;
}

.cta-buttons-redesign {
    animation: fadeInUp 1s ease-out 0.8s both;
}

.hero-stats {
    animation: fadeInUp 1s ease-out 1s both;
}

.stat-item:hover {
    transform: translateY(-5px) scale(1.02);
    background: rgba(255, 255, 255, 0.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Enhanced hover effects for hero buttons */
.btn-primary-redesign:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 35px rgba(76, 175, 80, 0.5) !important;
    background: linear-gradient(135deg, #66BB6A, #4CAF50) !important;
}

.btn-secondary-redesign:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 35px rgba(255,255,255,0.2) !important;
}

/* Original hover effects for other elements */

.mission-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.12) !important;
}

.program-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.1) !important;
}

.program-card-redesign:hover img {
    transform: scale(1.03);
}

.news-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.1) !important;
}

.news-card-redesign:hover img {
    transform: scale(1.03);
}

.testimonial-card-redesign:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section-redesign {
        padding-top: 80px !important;
        padding-bottom: 30px !important;
        min-height: calc(90vh - 15px) !important;
    }
    
    .header-container {
        padding: 8px 15px !important;
        min-height: 60px !important;
    }
    
    .site-title {
        font-size: 1.1rem !important;
    }
    
    .site-tagline {
        font-size: 0.75rem !important;
    }
    
    .main-navigation {
        display: none !important;
    }
    
    .mobile-menu-toggle {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .cta-buttons-redesign {
        flex-direction: column;
        align-items: center;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
    
    .impact-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
    
    .mission-grid-redesign {
        grid-template-columns: 1fr !important;
    }
    
    .programs-grid {
        grid-template-columns: 1fr !important;
    }
    
    .news-grid-redesign {
        grid-template-columns: 1fr !important;
    }
    
    .testimonials-grid-redesign {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .hero-section-redesign {
        padding-top: 70px !important;
        padding-bottom: 25px !important;
        min-height: calc(85vh - 10px) !important;
    }
    
    .hero-stats {
        grid-template-columns: 1fr !important;
    }
    
    .impact-grid {
        grid-template-columns: 1fr !important;
    }
    
    .site-logo img {
        width: 35px !important;
        height: 35px !important;
    }
    
    .site-title {
        font-size: 1rem !important;
    }
    
    .site-tagline {
        display: none;
    }
    
    .hero-section-redesign {
        min-height: 80vh !important;
    }
    
    .impact-badge, .registration-badge {
        padding: 8px 15px !important;
        font-size: 0.8rem !important;
        margin-bottom: 10px !important;
    }
}

/* Header fixes for better layout */
@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none !important;
    }
}

/* Ensure donation button stays visible */
.donate-btn {
    white-space: nowrap;
}

@media (max-width: 1024px) {
    .main-menu {
        gap: 2px !important;
    }
    
    .menu-link {
        padding: 8px 12px !important;
        font-size: 0.85rem !important;
    }
    
    .donate-btn {
        padding: 8px 16px !important;
        font-size: 0.85rem !important;
    }
}
</style>

<!-- Simplified Counter Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple counter animation
    const counters = document.querySelectorAll('[data-count]');
    
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        let count = 0;
        const duration = 1500; // ms
        const increment = target / (duration / 16); // for 60fps
        
        const updateCount = () => {
            if (count < target) {
                count += increment;
                counter.innerText = Math.ceil(count);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };
        
        // Only start animation when element is in viewport
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                updateCount();
                observer.disconnect();
            }
        });
        
        observer.observe(counter);
    });
});
});
</script>

<?php get_footer(); ?>
