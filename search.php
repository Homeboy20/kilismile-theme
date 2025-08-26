<?php
/**
 * The template for displaying search results
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <!-- Search Header -->
        <header class="search-header" style="text-align: center; padding: 80px 0 40px;">
            <h1 style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 20px;">
                <?php
                printf(
                    __('Search Results for: %s', 'kilismile'),
                    '<span style="color: var(--primary-green);">"' . get_search_query() . '"</span>'
                );
                ?>
            </h1>
            
            <?php if (have_posts()) : ?>
                <p style="color: var(--medium-gray); font-size: 1.1rem;">
                    <?php
                    global $wp_query;
                    printf(
                        _n('Found %d result', 'Found %d results', $wp_query->found_posts, 'kilismile'),
                        $wp_query->found_posts
                    );
                    ?>
                </p>
            <?php endif; ?>
        </header>

        <div class="search-content" style="display: flex; gap: 40px; margin-bottom: 80px;">
            <!-- Main Search Results -->
            <div class="search-results" style="flex: 2;">
                <?php if (have_posts()) : ?>
                    <div class="results-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <article <?php post_class('search-result-item'); ?> style="background: white; padding: 30px; margin-bottom: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                                <header class="result-header" style="margin-bottom: 20px;">
                                    <div class="result-meta" style="color: var(--medium-gray); font-size: 0.9rem; margin-bottom: 10px;">
                                        <span class="post-type">
                                            <?php 
                                            $post_type = get_post_type();
                                            $post_type_obj = get_post_type_object($post_type);
                                            echo esc_html($post_type_obj->labels->singular_name);
                                            ?>
                                        </span>
                                        
                                        <?php if (get_post_type() === 'post') : ?>
                                            <span style="margin: 0 10px;">•</span>
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        <?php endif; ?>
                                        
                                        <?php if (has_category() && get_post_type() === 'post') : ?>
                                            <span style="margin: 0 10px;">•</span>
                                            <?php the_category(', '); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h2 style="margin: 0; font-size: 1.5rem;">
                                        <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                </header>

                                <div class="result-content">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="result-thumbnail" style="float: left; margin: 0 20px 15px 0; width: 150px; height: 100px; border-radius: 8px; overflow: hidden;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="result-excerpt" style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                                        <?php 
                                        $excerpt = get_the_excerpt();
                                        $search_term = get_search_query();
                                        if ($search_term) {
                                            $excerpt = str_ireplace($search_term, '<mark style="background: yellow; padding: 2px 4px;">' . $search_term . '</mark>', $excerpt);
                                        }
                                        echo wp_kses_post($excerpt);
                                        ?>
                                    </div>
                                    
                                    <div style="clear: both;"></div>
                                    
                                    <!-- Custom post type specific info -->
                                    <?php if (get_post_type() === 'programs') : ?>
                                        <div class="program-meta" style="margin-bottom: 15px;">
                                            <?php 
                                            $target_audience = get_post_meta(get_the_ID(), '_program_target_audience', true);
                                            $status = get_post_meta(get_the_ID(), '_program_status', true);
                                            
                                            if ($target_audience) {
                                                echo kilismile_get_target_audience_badge($target_audience);
                                                echo ' ';
                                            }
                                            
                                            if ($status) {
                                                echo kilismile_get_program_status_badge($status);
                                            }
                                            ?>
                                        </div>
                                    <?php elseif (get_post_type() === 'events') : ?>
                                        <div class="event-meta" style="margin-bottom: 15px; color: var(--primary-green);">
                                            <?php 
                                            $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                                            $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                                            $event_location = get_post_meta(get_the_ID(), '_event_location', true);
                                            
                                            if ($event_date) {
                                                echo '<i class="fas fa-calendar" aria-hidden="true"></i> ';
                                                echo kilismile_format_event_date($event_date, $event_time);
                                            }
                                            
                                            if ($event_location) {
                                                echo '<br><i class="fas fa-map-marker-alt" aria-hidden="true"></i> ';
                                                echo esc_html($event_location);
                                            }
                                            ?>
                                        </div>
                                    <?php elseif (get_post_type() === 'team') : ?>
                                        <div class="team-meta" style="margin-bottom: 15px; color: var(--primary-green);">
                                            <?php 
                                            $position = get_post_meta(get_the_ID(), '_team_position', true);
                                            if ($position) {
                                                echo '<i class="fas fa-user-tie" aria-hidden="true"></i> ';
                                                echo esc_html($position);
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-more-btn" style="display: inline-flex; align-items: center; padding: 10px 20px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 25px; font-size: 0.9rem; transition: all 0.3s ease;">
                                        <?php _e('Read More', 'kilismile'); ?>
                                        <i class="fas fa-arrow-right" style="margin-left: 8px;" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <nav class="search-pagination" style="margin-top: 40px;">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'kilismile'),
                            'next_text' => __('Next', 'kilismile') . ' <i class="fas fa-chevron-right"></i>',
                            'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'kilismile') . ' </span>',
                        ));
                        ?>
                    </nav>

                <?php else : ?>
                    <!-- No Results -->
                    <div class="no-results" style="text-align: center; padding: 60px 40px; background: var(--light-gray); border-radius: 15px;">
                        <i class="fas fa-search" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 30px;" aria-hidden="true"></i>
                        
                        <h2 style="color: var(--dark-green); margin-bottom: 20px;">
                            <?php _e('No results found', 'kilismile'); ?>
                        </h2>
                        
                        <p style="color: var(--text-secondary); margin-bottom: 30px; font-size: 1.1rem;">
                            <?php printf(__('Sorry, we couldn\'t find any results for "%s". Try refining your search or browse our content below.', 'kilismile'), get_search_query()); ?>
                        </p>
                        
                        <!-- Search suggestions -->
                        <div class="search-suggestions" style="margin-bottom: 40px;">
                            <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                                <?php _e('Search Suggestions:', 'kilismile'); ?>
                            </h3>
                            <ul style="list-style: none; padding: 0; color: var(--text-secondary);">
                                <li style="margin-bottom: 8px;">• <?php _e('Check your spelling', 'kilismile'); ?></li>
                                <li style="margin-bottom: 8px;">• <?php _e('Try different keywords', 'kilismile'); ?></li>
                                <li style="margin-bottom: 8px;">• <?php _e('Use more general terms', 'kilismile'); ?></li>
                                <li style="margin-bottom: 8px;">• <?php _e('Try using fewer keywords', 'kilismile'); ?></li>
                            </ul>
                        </div>
                        
                        <!-- Alternative search form -->
                        <div class="alternative-search" style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                            <h4 style="margin-bottom: 15px; color: var(--dark-green);">
                                <?php _e('Try another search:', 'kilismile'); ?>
                            </h4>
                            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 10px;">
                                <input type="search" 
                                       name="s" 
                                       placeholder="<?php _e('Enter your search terms...', 'kilismile'); ?>" 
                                       style="flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"
                                       aria-label="<?php _e('Search', 'kilismile'); ?>">
                                <button type="submit" 
                                        style="padding: 12px 20px; background: var(--primary-green); color: white; border: none; border-radius: 8px; cursor: pointer;">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Browse categories -->
                        <div class="browse-content">
                            <h4 style="margin-bottom: 20px; color: var(--dark-green);">
                                <?php _e('Or browse our content:', 'kilismile'); ?>
                            </h4>
                            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                                <a href="<?php echo esc_url(home_url('/programs')); ?>" class="browse-link">
                                    <?php _e('Programs', 'kilismile'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/news')); ?>" class="browse-link">
                                    <?php _e('News', 'kilismile'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/events')); ?>" class="browse-link">
                                    <?php _e('Events', 'kilismile'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/team')); ?>" class="browse-link">
                                    <?php _e('Our Team', 'kilismile'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="search-sidebar" style="flex: 1; max-width: 350px;">
                <!-- Refined Search -->
                <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                        <?php _e('Refine Your Search', 'kilismile'); ?>
                    </h3>
                    
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="search-input" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                <?php _e('Search Terms', 'kilismile'); ?>
                            </label>
                            <input type="search" 
                                   id="search-input" 
                                   name="s" 
                                   value="<?php echo esc_attr(get_search_query()); ?>" 
                                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                <?php _e('Content Type', 'kilismile'); ?>
                            </label>
                            <select name="post_type" style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                <option value=""><?php _e('All Content', 'kilismile'); ?></option>
                                <option value="post" <?php selected(get_query_var('post_type'), 'post'); ?>><?php _e('News & Articles', 'kilismile'); ?></option>
                                <option value="programs" <?php selected(get_query_var('post_type'), 'programs'); ?>><?php _e('Programs', 'kilismile'); ?></option>
                                <option value="events" <?php selected(get_query_var('post_type'), 'events'); ?>><?php _e('Events', 'kilismile'); ?></option>
                                <option value="team" <?php selected(get_query_var('post_type'), 'team'); ?>><?php _e('Team Members', 'kilismile'); ?></option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border: none; border-radius: 5px; cursor: pointer;">
                            <?php _e('Search', 'kilismile'); ?>
                        </button>
                    </form>
                </div>

                <!-- Popular Content -->
                <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                        <?php _e('Popular Content', 'kilismile'); ?>
                    </h3>
                    
                    <?php
                    $popular_posts = new WP_Query(array(
                        'post_type' => array('post', 'programs'),
                        'posts_per_page' => 5,
                        'meta_key' => 'post_views_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    ));
                    
                    if ($popular_posts->have_posts()) : ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
                                <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none; font-size: 0.9rem; line-height: 1.4;">
                                        <?php the_title(); ?>
                                    </a>
                                    <div style="color: var(--medium-gray); font-size: 0.8rem; margin-top: 5px;">
                                        <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php 
                    else : ?>
                        <p style="color: var(--text-secondary);">
                            <?php _e('No popular content available.', 'kilismile'); ?>
                        </p>
                    <?php 
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>

                <!-- Contact Widget -->
                <div class="sidebar-widget" style="background: var(--primary-green); color: white; padding: 30px; border-radius: 15px;">
                    <h3 style="color: white; margin-bottom: 15px;">
                        <?php _e('Need Help?', 'kilismile'); ?>
                    </h3>
                    <p style="margin-bottom: 20px; opacity: 0.9; font-size: 0.9rem;">
                        <?php _e('Can\'t find what you\'re looking for? Contact us and we\'ll help you find the information you need.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" 
                       style="display: inline-block; padding: 10px 20px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 5px; font-weight: 600;">
                        <?php _e('Contact Us', 'kilismile'); ?>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>

<style>
    .search-result-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .read-more-btn:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
    }
    
    .browse-link {
        display: inline-block;
        padding: 8px 15px;
        background: var(--primary-green);
        color: white;
        text-decoration: none;
        border-radius: 20px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .browse-link:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
    }
    
    .search-pagination {
        text-align: center;
    }
    
    .search-pagination .page-numbers {
        display: inline-block;
        padding: 10px 15px;
        margin: 0 5px;
        background: white;
        color: var(--primary-green);
        text-decoration: none;
        border: 2px solid var(--primary-green);
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .search-pagination .page-numbers:hover,
    .search-pagination .page-numbers.current {
        background: var(--primary-green);
        color: white;
    }
</style>

<?php get_footer(); ?>
