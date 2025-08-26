<?php
/**
 * Template Name: About Us Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="about-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 140px 0 80px; text-align: center; border-bottom: 2px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: clamp(2rem, 4vw, 2.8rem); margin-bottom: 20px; color: var(--dark-green); font-weight: 600;">
                <?php _e('About Kili Smile Organization', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1rem; max-width: 800px; margin: 0 auto 30px; color: var(--text-secondary); line-height: 1.5;">
                <?php _e('Dedicated to improving health education and community well-being in Tanzania through innovative programs, compassionate care, and sustainable development initiatives.', 'kilismile'); ?>
            </p>
            <div class="hero-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; max-width: 800px; margin: 20px auto 0;">
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">15+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Years of Service', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">50,000+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Lives Impacted', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">25+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Active Programs', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">100+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Community Partners', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission, Vision, Values Section -->
    <section class="mission-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <!-- Mission -->
                <div class="mission-card" style="background: white; padding: 25px; border-radius: 4px; text-align: left; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid var(--primary-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-bullseye" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <h2 style="color: var(--dark-green); margin: 0; font-size: 1.3rem; font-weight: 600;">
                            <?php _e('Our Mission', 'kilismile'); ?>
                        </h2>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('To empower communities in Tanzania through comprehensive health education, preventive care programs, and sustainable development initiatives that promote lasting well-being and resilience.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Vision -->
                <div class="vision-card" style="background: white; padding: 25px; border-radius: 4px; text-align: left; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid var(--accent-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--accent-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-eye" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <h2 style="color: var(--dark-green); margin: 0; font-size: 1.3rem; font-weight: 600;">
                            <?php _e('Our Vision', 'kilismile'); ?>
                        </h2>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('A healthy Tanzania where every individual has access to quality health education, preventive care, and the knowledge to make informed decisions about their well-being.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Values -->
                <div class="values-card" style="background: white; padding: 25px; border-radius: 4px; text-align: left; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid var(--light-green);">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--light-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-heart" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <h2 style="color: var(--dark-green); margin: 0; font-size: 1.3rem; font-weight: 600;">
                            <?php _e('Our Values', 'kilismile'); ?>
                        </h2>
                    </div>
                    <ul style="color: var(--text-secondary); line-height: 1.5; list-style: none; padding: 0; margin: 0; font-size: 0.95rem;">
                        <li style="margin-bottom: 8px; display: flex; align-items: flex-start;"><i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem; margin-top: 5px;"></i><?php _e('Compassion & Empathy', 'kilismile'); ?></li>
                        <li style="margin-bottom: 8px; display: flex; align-items: flex-start;"><i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem; margin-top: 5px;"></i><?php _e('Community-Centered Approach', 'kilismile'); ?></li>
                        <li style="margin-bottom: 8px; display: flex; align-items: flex-start;"><i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem; margin-top: 5px;"></i><?php _e('Integrity & Transparency', 'kilismile'); ?></li>
                        <li style="margin-bottom: 8px; display: flex; align-items: flex-start;"><i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem; margin-top: 5px;"></i><?php _e('Innovation & Excellence', 'kilismile'); ?></li>
                        <li style="display: flex; align-items: flex-start;"><i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem; margin-top: 5px;"></i><?php _e('Sustainable Impact', 'kilismile'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="story-section" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
                <div class="story-content">
                    <h2 style="color: var(--dark-green); font-size: 1.75rem; margin-bottom: 15px; position: relative; padding-bottom: 10px;">
                        <?php _e('Our Story', 'kilismile'); ?>
                        <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 0;"></span>
                    </h2>
                    <div style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem;">
                        <p style="margin-bottom: 15px;">
                            <?php _e('Founded on April 25, 2024 and officially registered with NGO number 07NGO/R/6067, Kili Smile Organization began as an initiative to address the growing health education gap in rural Tanzania.', 'kilismile'); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <?php _e('What started as weekend health workshops in local communities has grown into a comprehensive organization that serves thousands of individuals across Tanzania through innovative programs, partnerships, and sustainable development initiatives.', 'kilismile'); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <?php _e('Our approach combines traditional community wisdom with modern healthcare knowledge, creating culturally sensitive programs that resonate with local populations while delivering measurable health outcomes.', 'kilismile'); ?>
                        </p>
                        <p>
                            <?php _e('Today, we continue to expand our reach while maintaining our core commitment to community-driven health education and empowerment.', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
                <div class="story-image">
                    <div style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 400'><rect fill='%23f8f8f8' width='600' height='400'/><circle fill='%234CAF50' cx='300' cy='200' r='100'/><text x='300' y='210' text-anchor='middle' fill='white' font-size='20' font-family='Arial'>Our Journey</text></svg>" 
                             alt="<?php _e('Kili Smile Organization Journey', 'kilismile'); ?>" 
                             style="width: 100%; display: block;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="milestones-section" style="text-align: center; background: white; padding: 30px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                <h2 style="font-size: 1.75rem; margin-bottom: 30px; color: var(--dark-green); font-weight: 600; position: relative; padding-bottom: 10px; display: inline-block;">
                    <?php _e('Our Journey Since April 2024', 'kilismile'); ?>
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
    </section>

    <!-- Leadership Team Section -->
    <section class="leadership-section" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 1.75rem; margin-bottom: 15px; position: relative; padding-bottom: 10px; display: inline-block;">
                <?php _e('Our Leadership Team', 'kilismile'); ?>
                <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 0.95rem; max-width: 600px; margin: 0 auto 30px; line-height: 1.5;">
                <?php _e('Meet the dedicated professionals who guide our mission and drive our impact in communities across Tanzania.', 'kilismile'); ?>
            </p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <!-- Leadership team members would be populated from custom post type 'team' -->
                <?php
                $leadership_query = new WP_Query(array(
                    'post_type' => 'team',
                    'posts_per_page' => 6,
                    'meta_query' => array(
                        array(
                            'key' => '_team_is_leadership',
                            'value' => 'yes',
                            'compare' => '='
                        )
                    )
                ));
                
                if ($leadership_query->have_posts()) :
                    while ($leadership_query->have_posts()) : $leadership_query->the_post();
                        $position = get_post_meta(get_the_ID(), '_team_position', true);
                        $bio = get_post_meta(get_the_ID(), '_team_bio', true);
                        ?>
                        <div class="team-member" style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="member-photo" style="height: 200px; overflow: hidden;">
                                    <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                </div>
                            <?php endif; ?>
                            <div class="member-info" style="padding: 15px;">
                                <h3 style="color: var(--dark-green); margin-bottom: 5px; font-size: 1.1rem; font-weight: 600;">
                                    <?php the_title(); ?>
                                </h3>
                                <?php if ($position) : ?>
                                    <div style="color: var(--primary-green); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">
                                        <?php echo esc_html($position); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($bio) : ?>
                                    <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.85rem; margin: 0;">
                                        <?php echo wp_trim_words(esc_html($bio), 20, '...'); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else : ?>
                    <!-- Placeholder team members if none exist -->
                    <div class="team-member" style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                        <div class="member-photo" style="height: 200px; background: var(--light-gray); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 3rem; color: var(--medium-gray);" aria-hidden="true"></i>
                        </div>
                        <div class="member-info" style="padding: 15px;">
                            <h3 style="color: var(--dark-green); margin-bottom: 5px; font-size: 1.1rem; font-weight: 600;"><?php _e('Dr. Sarah Mwamba', 'kilismile'); ?></h3>
                            <div style="color: var(--primary-green); font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;"><?php _e('Executive Director', 'kilismile'); ?></div>
                            <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.85rem; margin: 0;"><?php _e('Leading our organization with over 15 years of experience in public health and community development.', 'kilismile'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section" style="padding: 50px 0; background: var(--primary-green); color: white; text-align: center; border-top: 2px solid var(--dark-green);">
        <div class="container">
            <h2 style="font-size: 1.8rem; margin-bottom: 15px; color: white; font-weight: 600;">
                <?php _e('Join Our Mission', 'kilismile'); ?>
            </h2>
            <p style="font-size: 0.95rem; max-width: 600px; margin: 0 auto 25px; color: rgba(255,255,255,0.95); line-height: 1.5;">
                <?php _e('Be part of the positive change in Tanzania. Whether through donations, volunteering, or partnerships, your support makes a difference.', 'kilismile'); ?>
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/donate')); ?>" 
                   class="btn btn-secondary" 
                   style="display: inline-block; padding: 10px 20px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Donate Now', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/volunteer')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 10px 20px; background: transparent; color: white; text-decoration: none; border: 1px solid white; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Volunteer', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 10px 20px; background: transparent; color: white; text-decoration: none; border: 1px solid white; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Partner With Us', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
    @media (max-width: 768px) {
        .story-section > .container > div {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .timeline-item .timeline-content {
            width: calc(100% - 30px) !important;
            margin: 0 15px !important;
        }
        
        .timeline {
            padding: 0 10px;
        }
        
        .hero-stats {
            gap: 15px;
        }
        
        .hero-stats .stat-item {
            min-width: 120px;
        }
        
        .about-hero {
            padding-top: 120px !important; /* Ensure title is visible on mobile */
        }
    }
</style>

<?php get_footer(); ?>
