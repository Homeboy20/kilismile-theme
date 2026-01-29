<?php
/**
 * Template Name: News & Events Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="news-hero" style="background: var(--light-gray); color: var(--text-primary); padding: 100px 0 60px; text-align: center; border-bottom: 3px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--dark-green);">
                <?php _e('News & Events', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto; line-height: 1.6; color: var(--text-secondary);">
                <?php _e('Stay updated with our latest news, upcoming events, and community activities.', 'kilismile'); ?>
            </p>
        </div>
    </section>

    <!-- Content Tabs -->
    <section class="content-tabs" style="padding: 0; background: var(--primary-green);">
        <div class="container">
            <div class="tab-navigation" style="display: flex; justify-content: center;">
                <button onclick="switchTab('news')" class="tab-btn active" data-tab="news" style="padding: 20px 40px; background: white; color: var(--primary-green); border: none; cursor: pointer; font-weight: 600; transition: all 0.3s ease; border-radius: 10px 10px 0 0;">
                    <i class="fas fa-newspaper" style="margin-right: 8px;" aria-hidden="true"></i>
                    <?php _e('Latest News', 'kilismile'); ?>
                </button>
                <button onclick="switchTab('events')" class="tab-btn" data-tab="events" style="padding: 20px 40px; background: rgba(255,255,255,0.2); color: white; border: none; cursor: pointer; font-weight: 600; transition: all 0.3s ease; border-radius: 10px 10px 0 0; margin-left: 5px;">
                    <i class="fas fa-calendar-alt" style="margin-right: 8px;" aria-hidden="true"></i>
                    <?php _e('Upcoming Events', 'kilismile'); ?>
                </button>
                <button onclick="switchTab('stories')" class="tab-btn" data-tab="stories" style="padding: 20px 40px; background: rgba(255,255,255,0.2); color: white; border: none; cursor: pointer; font-weight: 600; transition: all 0.3s ease; border-radius: 10px 10px 0 0; margin-left: 5px;">
                    <i class="fas fa-heart" style="margin-right: 8px;" aria-hidden="true"></i>
                    <?php _e('Success Stories', 'kilismile'); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- News Tab Content -->
    <section id="news-content" class="tab-content active" style="padding: 80px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 60px;">
                <!-- Main News Content -->
                <div class="news-main">
                    <h2 style="color: var(--dark-green); font-size: 2.2rem; margin-bottom: 40px;">
                        <?php _e('Latest News & Updates', 'kilismile'); ?>
                    </h2>

                    <?php
                    $news_query = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 6,
                        'post_status' => 'publish'
                    ));

                    if ($news_query->have_posts()) : ?>
                        <div class="news-grid" style="display: grid; gap: 30px;">
                            <!-- Featured: World Oral Health Week 2026 -->
                            <article class="news-item featured-news" style="display: flex; gap: 25px; background: linear-gradient(135deg, var(--primary-green), var(--accent-green)); padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); transition: all 0.3s ease; color: white; margin-bottom: 20px;">
                                <div class="news-content" style="flex: 1;">
                                    <div class="news-meta" style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 10px; font-weight: 600;">
                                        <i class="fas fa-star" aria-hidden="true"></i> <?php _e('FEATURED EVENT', 'kilismile'); ?>
                                        <span style="margin: 0 10px;">•</span>
                                        <time datetime="2026-03-20">
                                            <i class="fas fa-calendar" aria-hidden="true"></i>
                                            <?php _e('March 14-20, 2026', 'kilismile'); ?>
                                        </time>
                                    </div>
                                    
                                    <h3 style="margin: 0 0 15px 0; font-size: 1.8rem; line-height: 1.3; color: white;">
                                        <?php _e('World Oral Health Week 2026 - Moshi, Tanzania', 'kilismile'); ?>
                                    </h3>
                                    
                                    <p style="line-height: 1.8; margin-bottom: 20px; font-size: 1.05rem; color: rgba(255,255,255,0.95);">
                                        <?php _e('Join us in celebrating World Oral Health Week alongside the official launch of KiliSmile Organization office and our modern dental clinic in Moshi. This milestone event will feature community health screenings, oral health education, and professional treatment services. Our dental clinic, established in April 2025, will serve as a permanent treatment center for the Moshi community and neighboring areas.', 'kilismile'); ?>
                                    </p>
                                    
                                    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
                                        <div style="background: rgba(255,255,255,0.2); padding: 10px 15px; border-radius: 8px;">
                                            <i class="fas fa-clinic-medical" aria-hidden="true"></i>
                                            <?php _e('Clinic Launch', 'kilismile'); ?>
                                        </div>
                                        <div style="background: rgba(255,255,255,0.2); padding: 10px 15px; border-radius: 8px;">
                                            <i class="fas fa-tooth" aria-hidden="true"></i>
                                            <?php _e('Free Screenings', 'kilismile'); ?>
                                        </div>
                                        <div style="background: rgba(255,255,255,0.2); padding: 10px 15px; border-radius: 8px;">
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                            <?php _e('Community Event', 'kilismile'); ?>
                                        </div>
                                    </div>
                                    
                                    <a href="<?php echo esc_url(home_url('/programs')); ?>" 
                                       style="background: white; color: var(--primary-green); padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                                        <?php _e('Learn More About This Event', 'kilismile'); ?>
                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                            
                            <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                                <article class="news-item" style="display: flex; gap: 25px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="news-thumbnail" style="flex: 0 0 200px; height: 150px; border-radius: 10px; overflow: hidden;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="news-content" style="flex: 1;">
                                        <div class="news-meta" style="color: var(--medium-gray); font-size: 0.9rem; margin-bottom: 10px;">
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                                <?php echo get_the_date(); ?>
                                            </time>
                                            <?php if (has_category()) : ?>
                                                <span style="margin: 0 10px;">•</span>
                                                <?php the_category(', '); ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h3 style="margin: 0 0 15px 0; font-size: 1.3rem; line-height: 1.4;">
                                            <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                                <?php the_title(); ?>
                                            </a>
                                        </h3>
                                        
                                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 15px;">
                                            <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                                        </p>
                                        
                                        <a href="<?php the_permalink(); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <?php _e('Read More', 'kilismile'); ?>
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        
                        <div style="text-align: center; margin-top: 40px;">
                            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" 
                               class="btn btn-primary" 
                               style="display: inline-block; padding: 15px 30px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                                <?php _e('View All News', 'kilismile'); ?>
                            </a>
                        </div>
                        
                        <?php wp_reset_postdata();
                    else : ?>
                        <div style="text-align: center; padding: 60px 20px; background: var(--light-gray); border-radius: 15px;">
                            <i class="fas fa-newspaper" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 20px;" aria-hidden="true"></i>
                            <h3 style="color: var(--dark-green); margin-bottom: 15px;"><?php _e('No news available', 'kilismile'); ?></h3>
                            <p style="color: var(--text-secondary);"><?php _e('Check back soon for the latest updates from Kilismile Organization.', 'kilismile'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- News Sidebar -->
                <aside class="news-sidebar">
                    <!-- Featured News -->
                    <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 20px; border-bottom: 2px solid var(--primary-green); padding-bottom: 10px;">
                            <?php _e('Featured News', 'kilismile'); ?>
                        </h3>
                        
                        <?php
                        $featured_query = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => 3,
                            'meta_key' => '_featured_post',
                            'meta_value' => 'yes'
                        ));
                        
                        if ($featured_query->have_posts()) :
                            while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                                <div class="featured-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div style="margin-bottom: 10px;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('thumbnail', array('style' => 'width: 100%; height: 120px; object-fit: cover; border-radius: 8px;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <h4 style="margin: 0 0 8px 0; font-size: 1rem; line-height: 1.3;">
                                        <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div style="color: var(--medium-gray); font-size: 0.8rem;">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                </div>
                            <?php endwhile;
                            wp_reset_postdata();
                        else : ?>
                            <p style="color: var(--text-secondary);"><?php _e('No featured news available.', 'kilismile'); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="sidebar-widget" style="background: var(--primary-green); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                        <h3 style="color: white; margin-bottom: 15px;">
                            <?php _e('Stay Updated', 'kilismile'); ?>
                        </h3>
                        <p style="margin-bottom: 20px; opacity: 0.9; font-size: 0.9rem;">
                            <?php _e('Subscribe to our newsletter for the latest news and updates.', 'kilismile'); ?>
                        </p>
                        <form style="display: flex; flex-direction: column; gap: 10px;">
                            <input type="email" 
                                   placeholder="<?php _e('Your email address', 'kilismile'); ?>" 
                                   style="padding: 12px; border: none; border-radius: 5px; font-size: 0.9rem;">
                            <button type="submit" 
                                    style="padding: 12px; background: white; color: var(--primary-green); border: none; border-radius: 5px; font-weight: 600; cursor: pointer;">
                                <?php _e('Subscribe', 'kilismile'); ?>
                            </button>
                        </form>
                    </div>

                    <!-- Social Media -->
                    <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                            <?php _e('Follow Us', 'kilismile'); ?>
                        </h3>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="#" style="width: 45px; height: 45px; background: #1877f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;">
                                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                            </a>
                            <a href="#" style="width: 45px; height: 45px; background: #1da1f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;">
                                <i class="fab fa-twitter" aria-hidden="true"></i>
                            </a>
                            <a href="#" style="width: 45px; height: 45px; background: #e4405f; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;">
                                <i class="fab fa-instagram" aria-hidden="true"></i>
                            </a>
                            <a href="#" style="width: 45px; height: 45px; background: #0077b5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;">
                                <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Events Tab Content -->
    <section id="events-content" class="tab-content" style="padding: 80px 0; display: none;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Upcoming Events', 'kilismile'); ?>
            </h2>

            <?php
            $events_query = new WP_Query(array(
                'post_type' => 'events',
                'posts_per_page' => 6,
                'meta_key' => '_event_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => '_event_date',
                        'value' => date('Y-m-d'),
                        'compare' => '>=',
                        'type' => 'DATE'
                    )
                )
            ));

            if ($events_query->have_posts()) : ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
                    <?php while ($events_query->have_posts()) : $events_query->the_post();
                        $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                        $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                        $event_location = get_post_meta(get_the_ID(), '_event_location', true);
                        $event_type = get_post_meta(get_the_ID(), '_event_type', true);
                        ?>
                        <div class="event-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="event-image" style="height: 200px; overflow: hidden; position: relative;">
                                    <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                    
                                    <!-- Event Type Badge -->
                                    <?php if ($event_type) : ?>
                                        <div style="position: absolute; top: 15px; left: 15px; background: var(--primary-green); color: white; padding: 6px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                                            <?php echo esc_html(ucfirst($event_type)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="event-content" style="padding: 30px;">
                                <?php if ($event_date) : ?>
                                    <div class="event-date" style="color: var(--primary-green); font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">
                                        <i class="fas fa-calendar" aria-hidden="true"></i>
                                        <?php echo kilismile_format_event_date($event_date, $event_time); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; line-height: 1.3;">
                                    <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <?php if ($event_location) : ?>
                                    <div class="event-location" style="color: var(--text-secondary); margin-bottom: 15px; font-size: 0.9rem;">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        <?php echo esc_html($event_location); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <a href="<?php the_permalink(); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                                        <?php _e('Learn More', 'kilismile'); ?> →
                                    </a>
                                    
                                    <button onclick="addToCalendar('<?php echo esc_js(get_the_title()); ?>', '<?php echo esc_js($event_date); ?>', '<?php echo esc_js($event_location); ?>')" 
                                            style="background: var(--light-gray); border: none; padding: 8px 12px; border-radius: 20px; color: var(--text-secondary); cursor: pointer; font-size: 0.8rem;">
                                        <i class="fas fa-calendar-plus" aria-hidden="true"></i> <?php _e('Add to Calendar', 'kilismile'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div style="text-align: center; margin-top: 60px;">
                    <a href="<?php echo esc_url(get_post_type_archive_link('events')); ?>" 
                       class="btn btn-primary" 
                       style="display: inline-block; padding: 15px 30px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('View All Events', 'kilismile'); ?>
                    </a>
                </div>
                
                <?php wp_reset_postdata();
            else : ?>
                <div style="text-align: center; padding: 60px 20px; background: var(--light-gray); border-radius: 15px;">
                    <i class="fas fa-calendar-alt" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 20px;" aria-hidden="true"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px;"><?php _e('No upcoming events', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary);"><?php _e('Check back soon for exciting upcoming events and activities.', 'kilismile'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Success Stories Tab Content -->
    <section id="stories-content" class="tab-content" style="padding: 80px 0; background: var(--light-gray); display: none;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Success Stories', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Real stories of transformation and hope from the communities we serve.', 'kilismile'); ?>
            </p>

            <?php
            $stories_query = new WP_Query(array(
                'post_type' => 'testimonials',
                'posts_per_page' => 6,
                'post_status' => 'publish'
            ));

            if ($stories_query->have_posts()) : ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <?php while ($stories_query->have_posts()) : $stories_query->the_post();
                        $testimonial_name = get_post_meta(get_the_ID(), '_testimonial_name', true);
                        $testimonial_location = get_post_meta(get_the_ID(), '_testimonial_location', true);
                        $testimonial_role = get_post_meta(get_the_ID(), '_testimonial_role', true);
                        ?>
                        <div class="story-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative; transition: all 0.3s ease;">
                            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-quote-left" aria-hidden="true"></i>
                            </div>
                            
                            <div style="padding-top: 30px;">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; border: 3px solid var(--light-gray);">
                                        <?php the_post_thumbnail('thumbnail', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <blockquote style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 25px; border: none; padding: 0;">
                                    "<?php echo wp_trim_words(get_the_content(), 30, '...'); ?>"
                                </blockquote>
                                
                                <?php if ($testimonial_name) : ?>
                                    <h4 style="color: var(--dark-green); margin-bottom: 5px; font-size: 1.1rem;">
                                        <?php echo esc_html($testimonial_name); ?>
                                    </h4>
                                <?php endif; ?>
                                
                                <?php if ($testimonial_role || $testimonial_location) : ?>
                                    <p style="color: var(--medium-gray); font-size: 0.9rem; margin: 0;">
                                        <?php 
                                        if ($testimonial_role) echo esc_html($testimonial_role);
                                        if ($testimonial_role && $testimonial_location) echo ', ';
                                        if ($testimonial_location) echo esc_html($testimonial_location);
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php wp_reset_postdata();
            else : ?>
                <!-- Placeholder stories -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <?php for ($i = 1; $i <= 3; $i++) : ?>
                        <div class="story-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative;">
                            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-quote-left" aria-hidden="true"></i>
                            </div>
                            <div style="padding-top: 30px;">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--light-gray); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: var(--medium-gray);">
                                    <i class="fas fa-user" style="font-size: 2rem;" aria-hidden="true"></i>
                                </div>
                                <blockquote style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 25px;">
                                    "<?php printf(__('This is a sample success story %d. Real testimonials will be added here.', 'kilismile'), $i); ?>"
                                </blockquote>
                                <h4 style="color: var(--dark-green); margin-bottom: 5px;"><?php printf(__('Community Member %d', 'kilismile'), $i); ?></h4>
                                <p style="color: var(--medium-gray); font-size: 0.9rem; margin: 0;"><?php _e('Program Participant', 'kilismile'); ?></p>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
        content.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = 'rgba(255,255,255,0.2)';
        btn.style.color = 'white';
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').style.display = 'block';
    document.getElementById(tabName + '-content').classList.add('active');
    
    // Activate selected tab button
    document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');
    document.querySelector('[data-tab="' + tabName + '"]').style.background = 'white';
    document.querySelector('[data-tab="' + tabName + '"]').style.color = 'var(--primary-green)';
}

// Add to calendar functionality
function addToCalendar(title, date, location) {
    const eventDate = new Date(date);
    const startDate = eventDate.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
    
    const googleCalendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate}/${startDate}&location=${encodeURIComponent(location || '')}&details=${encodeURIComponent('Event organized by Kilismile Organization')}`;
    
    window.open(googleCalendarUrl, '_blank');
}

// Newsletter form submission
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.sidebar-widget form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (email) {
                // Show success message
                const button = this.querySelector('button');
                const originalText = button.textContent;
                button.textContent = '<?php _e('Subscribed!', 'kilismile'); ?>';
                button.style.background = 'var(--accent-green)';
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = 'white';
                    this.reset();
                }, 2000);
            }
        });
    }
});
</script>

<style>
    .news-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .news-item:hover .news-thumbnail img {
        transform: scale(1.05);
    }
    
    .event-card:hover,
    .story-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .tab-btn:hover {
        transform: translateY(-2px);
    }
    
    .sidebar-widget:hover {
        transform: translateY(-3px);
    }
    
    @media (max-width: 768px) {
        .tab-navigation {
            flex-direction: column;
        }
        
        .tab-btn {
            border-radius: 0 !important;
            margin: 0 !important;
        }
        
        .news-main > div > div {
            grid-template-columns: 1fr;
        }
        
        .news-item {
            flex-direction: column;
        }
        
        .news-thumbnail {
            flex: none !important;
            height: 200px !important;
        }
    }
</style>

<?php get_footer(); ?>


