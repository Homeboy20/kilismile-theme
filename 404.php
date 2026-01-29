<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <div class="error-404-page" style="text-align: center; padding: 120px 20px; max-width: 800px; margin: 0 auto;">
            <!-- 404 Illustration -->
            <div class="error-illustration" style="margin-bottom: 40px;">
                <div style="font-size: 8rem; color: var(--primary-green); font-weight: 700; line-height: 1;">404</div>
                <div style="font-size: 1.5rem; color: var(--medium-gray); margin-top: 10px;">
                    <?php _e('Page Not Found', 'kilismile'); ?>
                </div>
            </div>
            
            <h1 style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 30px;">
                <?php _e('Oops! This page seems to have gone for a health check.', 'kilismile'); ?>
            </h1>
            
            <p style="font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 40px;">
                <?php _e('The page you\'re looking for might have been moved, deleted, or doesn\'t exist. But don\'t worry, we\'re here to help you find what you need!', 'kilismile'); ?>
            </p>
            
            <!-- Search Form -->
            <div class="search-form-container" style="background: var(--light-gray); padding: 40px; border-radius: 15px; margin-bottom: 40px;">
                <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                    <?php _e('Search Our Site', 'kilismile'); ?>
                </h3>
                
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; max-width: 400px; margin: 0 auto; gap: 10px;">
                    <input type="search" 
                           name="s" 
                           placeholder="<?php _e('Search for programs, news, or information...', 'kilismile'); ?>" 
                           style="flex: 1; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;"
                           aria-label="<?php _e('Search', 'kilismile'); ?>">
                    <button type="submit" 
                            style="padding: 15px 20px; background: var(--primary-green); color: white; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;"
                            aria-label="<?php _e('Submit search', 'kilismile'); ?>">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            
            <!-- Quick Links -->
            <div class="quick-links" style="margin-bottom: 40px;">
                <h3 style="color: var(--dark-green); margin-bottom: 25px;">
                    <?php _e('Quick Links', 'kilismile'); ?>
                </h3>
                
                <div class="links-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 600px; margin: 0 auto;">
                    <a href="<?php echo esc_url(home_url('/')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: white; border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: var(--primary-green); transition: all 0.3s ease;">
                        <i class="fas fa-home" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('Home', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/about')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: white; border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: var(--primary-green); transition: all 0.3s ease;">
                        <i class="fas fa-info-circle" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('About Us', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/programs')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: white; border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: var(--primary-green); transition: all 0.3s ease;">
                        <i class="fas fa-heart" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('Our Programs', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: white; border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: var(--primary-green); transition: all 0.3s ease;">
                        <i class="fas fa-envelope" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('Contact', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_donation_url', '#donate')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: var(--primary-green); border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: white; transition: all 0.3s ease;">
                        <i class="fas fa-donate" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('Donate', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/news')); ?>" 
                       class="quick-link" 
                       style="display: flex; align-items: center; padding: 15px 20px; background: white; border: 2px solid var(--primary-green); border-radius: 10px; text-decoration: none; color: var(--primary-green); transition: all 0.3s ease;">
                        <i class="fas fa-newspaper" style="margin-right: 10px;" aria-hidden="true"></i>
                        <?php _e('News', 'kilismile'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Recent Posts -->
            <div class="recent-posts-section" style="background: var(--light-gray); padding: 40px; border-radius: 15px; margin-bottom: 40px;">
                <h3 style="color: var(--dark-green); margin-bottom: 25px;">
                    <?php _e('Recent News & Updates', 'kilismile'); ?>
                </h3>
                
                <?php
                $recent_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish'
                ));
                
                if ($recent_posts->have_posts()) : ?>
                    <div class="posts-list" style="max-width: 500px; margin: 0 auto;">
                        <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                            <article style="display: flex; align-items: center; padding: 15px; background: white; border-radius: 10px; margin-bottom: 15px; text-decoration: none; transition: all 0.3s ease;">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; margin-right: 15px; flex-shrink: 0;">
                                        <?php the_post_thumbnail('thumbnail', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 5px; font-size: 1rem;">
                                        <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div style="font-size: 0.9rem; color: var(--medium-gray);">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php 
                else : ?>
                    <p style="color: var(--text-secondary);">
                        <?php _e('No recent posts available.', 'kilismile'); ?>
                    </p>
                <?php 
                endif;
                wp_reset_postdata();
                ?>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info-404" style="background: var(--primary-green); color: white; padding: 40px; border-radius: 15px;">
                <h3 style="margin-bottom: 20px; color: white;">
                    <?php _e('Still need help?', 'kilismile'); ?>
                </h3>
                
                <p style="margin-bottom: 25px; opacity: 0.9;">
                    <?php _e('Our team is here to assist you. Get in touch and we\'ll help you find what you\'re looking for.', 'kilismile'); ?>
                </p>
                
                <div class="contact-methods" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <?php 
                    $email = get_theme_mod('kilismile_email', 'kilismile21@gmail.com');
                    $phone = get_theme_mod('kilismile_phone', '+255763495575/+255735495575');
                    ?>
                    
                    <a href="mailto:<?php echo esc_attr($email); ?>" 
                       style="display: flex; align-items: center; padding: 12px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; transition: all 0.3s ease;">
                        <i class="fas fa-envelope" style="margin-right: 8px;" aria-hidden="true"></i>
                        <?php _e('Email Us', 'kilismile'); ?>
                    </a>
                    
                    <a href="tel:<?php echo esc_attr(str_replace(['/', ' '], '', $phone)); ?>" 
                       style="display: flex; align-items: center; padding: 12px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; transition: all 0.3s ease;">
                        <i class="fas fa-phone" style="margin-right: 8px;" aria-hidden="true"></i>
                        <?php _e('Call Us', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .quick-link:hover {
        background: var(--primary-green) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }
    
    .quick-link:hover i {
        color: white !important;
    }
    
    .search-form-container button:hover {
        background: var(--dark-green);
    }
    
    .contact-methods a:hover {
        background: rgba(255,255,255,0.3) !important;
    }
    
    .posts-list article:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<?php get_footer(); ?>


