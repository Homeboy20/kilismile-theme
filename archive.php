<?php
/**
 * Template for displaying archive pages
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <!-- Archive Header -->
        <header class="archive-header" style="text-align: center; padding: 80px 0 40px; background: var(--primary-green); color: white; border-radius: 20px; margin-bottom: 60px;">
            <div style="padding: 0 40px;">
                <h1 style="font-size: 3rem; margin-bottom: 20px; color: white;">
                    <?php
                    if (is_category()) {
                        printf(__('Category: %s', 'kilismile'), single_cat_title('', false));
                    } elseif (is_tag()) {
                        printf(__('Tag: %s', 'kilismile'), single_tag_title('', false));
                    } elseif (is_author()) {
                        printf(__('Author: %s', 'kilismile'), get_the_author());
                    } elseif (is_day()) {
                        printf(__('Day: %s', 'kilismile'), get_the_date());
                    } elseif (is_month()) {
                        printf(__('Month: %s', 'kilismile'), get_the_date('F Y'));
                    } elseif (is_year()) {
                        printf(__('Year: %s', 'kilismile'), get_the_date('Y'));
                    } elseif (is_post_type_archive()) {
                        echo post_type_archive_title('', false);
                    } else {
                        _e('Archives', 'kilismile');
                    }
                    ?>
                </h1>
                
                <?php
                $description = get_the_archive_description();
                if ($description) : ?>
                    <div class="archive-description" style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>
                
                <div class="archive-stats" style="margin-top: 30px; font-size: 1.1rem; opacity: 0.8;">
                    <?php
                    global $wp_query;
                    printf(
                        _n('%d item found', '%d items found', $wp_query->found_posts, 'kilismile'),
                        $wp_query->found_posts
                    );
                    ?>
                </div>
            </div>
        </header>

        <div class="archive-content" style="display: flex; gap: 40px;">
            <!-- Main Content -->
            <div class="archive-main" style="flex: 2;">
                <?php if (have_posts()) : ?>
                    <!-- Filters and Sorting (for certain post types) -->
                    <?php if (is_post_type_archive()) : ?>
                        <div class="archive-filters" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                                <div class="filter-options" style="display: flex; gap: 15px; flex-wrap: wrap;">
                                    <?php if (get_post_type() === 'programs') : ?>
                                        <select onchange="filterArchive(this)" data-filter="target_audience" style="padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                            <option value=""><?php _e('All Audiences', 'kilismile'); ?></option>
                                            <option value="children"><?php _e('Children', 'kilismile'); ?></option>
                                            <option value="youth"><?php _e('Youth', 'kilismile'); ?></option>
                                            <option value="adults"><?php _e('Adults', 'kilismile'); ?></option>
                                            <option value="elderly"><?php _e('Elderly', 'kilismile'); ?></option>
                                        </select>
                                        
                                        <select onchange="filterArchive(this)" data-filter="status" style="padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                            <option value=""><?php _e('All Status', 'kilismile'); ?></option>
                                            <option value="active"><?php _e('Active', 'kilismile'); ?></option>
                                            <option value="upcoming"><?php _e('Upcoming', 'kilismile'); ?></option>
                                            <option value="completed"><?php _e('Completed', 'kilismile'); ?></option>
                                        </select>
                                    <?php elseif (get_post_type() === 'events') : ?>
                                        <select onchange="filterArchive(this)" data-filter="event_type" style="padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                            <option value=""><?php _e('All Event Types', 'kilismile'); ?></option>
                                            <option value="workshop"><?php _e('Workshop', 'kilismile'); ?></option>
                                            <option value="seminar"><?php _e('Seminar', 'kilismile'); ?></option>
                                            <option value="campaign"><?php _e('Campaign', 'kilismile'); ?></option>
                                            <option value="training"><?php _e('Training', 'kilismile'); ?></option>
                                        </select>
                                        
                                        <select onchange="filterArchive(this)" data-filter="time_period" style="padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                            <option value=""><?php _e('All Events', 'kilismile'); ?></option>
                                            <option value="upcoming"><?php _e('Upcoming', 'kilismile'); ?></option>
                                            <option value="past"><?php _e('Past Events', 'kilismile'); ?></option>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="sort-options" style="display: flex; align-items: center; gap: 10px;">
                                    <label for="sort-by" style="font-weight: 600; color: var(--dark-green);">
                                        <?php _e('Sort by:', 'kilismile'); ?>
                                    </label>
                                    <select id="sort-by" onchange="sortArchive(this)" style="padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 5px;">
                                        <option value="date_desc"><?php _e('Newest First', 'kilismile'); ?></option>
                                        <option value="date_asc"><?php _e('Oldest First', 'kilismile'); ?></option>
                                        <option value="title_asc"><?php _e('Title A-Z', 'kilismile'); ?></option>
                                        <option value="title_desc"><?php _e('Title Z-A', 'kilismile'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- View Toggle -->
                    <div class="view-toggle" style="text-align: right; margin-bottom: 30px;">
                        <div class="toggle-buttons" style="display: inline-flex; background: white; padding: 5px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <button onclick="setView('grid')" class="view-btn active" data-view="grid" style="padding: 10px 15px; border: none; background: var(--primary-green); color: white; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-th" aria-hidden="true"></i>
                                <span class="sr-only"><?php _e('Grid View', 'kilismile'); ?></span>
                            </button>
                            <button onclick="setView('list')" class="view-btn" data-view="list" style="padding: 10px 15px; border: none; background: transparent; color: var(--text-secondary); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-list" aria-hidden="true"></i>
                                <span class="sr-only"><?php _e('List View', 'kilismile'); ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Posts Grid/List -->
                    <div class="posts-container" id="posts-container">
                        <div class="posts-grid" id="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                            <?php while (have_posts()) : the_post(); ?>
                                <article <?php post_class('archive-post-card'); ?> style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail" style="height: 200px; overflow: hidden; position: relative;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;')); ?>
                                            </a>
                                            
                                            <!-- Post type badge -->
                                            <div class="post-type-badge" style="position: absolute; top: 15px; left: 15px; background: var(--primary-green); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                                                <?php 
                                                $post_type_obj = get_post_type_object(get_post_type());
                                                echo esc_html($post_type_obj->labels->singular_name);
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content" style="padding: 25px;">
                                        <!-- Meta info -->
                                        <div class="post-meta" style="margin-bottom: 15px; color: var(--medium-gray); font-size: 0.9rem;">
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                                <?php echo get_the_date(); ?>
                                            </time>
                                            
                                            <?php if (get_post_type() === 'post' && has_category()) : ?>
                                                <span style="margin: 0 10px;">•</span>
                                                <?php the_category(', '); ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h2 style="margin: 0 0 15px 0; font-size: 1.3rem; line-height: 1.4;">
                                            <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        
                                        <div class="post-excerpt" style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>
                                        
                                        <!-- Custom post type specific info -->
                                        <?php if (get_post_type() === 'programs') : ?>
                                            <div class="program-meta" style="margin-bottom: 20px;">
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
                                            <div class="event-meta" style="margin-bottom: 20px; color: var(--primary-green); font-size: 0.9rem;">
                                                <?php 
                                                $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                                                $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                                                $event_location = get_post_meta(get_the_ID(), '_event_location', true);
                                                
                                                if ($event_date) {
                                                    echo '<div style="margin-bottom: 5px;"><i class="fas fa-clock" aria-hidden="true"></i> ';
                                                    echo kilismile_format_event_date($event_date, $event_time);
                                                    echo '</div>';
                                                }
                                                
                                                if ($event_location) {
                                                    echo '<div><i class="fas fa-map-marker-alt" aria-hidden="true"></i> ';
                                                    echo esc_html($event_location);
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        <?php elseif (get_post_type() === 'team') : ?>
                                            <div class="team-meta" style="margin-bottom: 20px; color: var(--primary-green);">
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
                        
                        <!-- List view (hidden by default) -->
                        <div class="posts-list" id="posts-list" style="display: none;">
                            <?php 
                            // Reset the loop for list view
                            rewind_posts();
                            while (have_posts()) : the_post(); ?>
                                <article <?php post_class('archive-post-list-item'); ?> style="display: flex; gap: 20px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 20px; transition: all 0.3s ease;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail" style="flex: 0 0 150px; height: 120px; border-radius: 10px; overflow: hidden;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content" style="flex: 1;">
                                        <div class="post-meta" style="margin-bottom: 10px; color: var(--medium-gray); font-size: 0.9rem;">
                                            <span class="post-type" style="background: var(--primary-green); color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.8rem; margin-right: 10px;">
                                                <?php 
                                                $post_type_obj = get_post_type_object(get_post_type());
                                                echo esc_html($post_type_obj->labels->singular_name);
                                                ?>
                                            </span>
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        </div>
                                        
                                        <h2 style="margin: 0 0 10px 0; font-size: 1.4rem;">
                                            <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        
                                        <div class="post-excerpt" style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 15px;">
                                            <?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>
                                        </div>
                                        
                                        <a href="<?php the_permalink(); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                                            <?php _e('Continue Reading', 'kilismile'); ?> →
                                        </a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav class="archive-pagination" style="margin-top: 60px; text-align: center;">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'kilismile'),
                            'next_text' => __('Next', 'kilismile') . ' <i class="fas fa-chevron-right"></i>',
                            'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'kilismile') . ' </span>',
                        ));
                        ?>
                    </nav>

                <?php else : ?>
                    <!-- No posts found -->
                    <div class="no-posts" style="text-align: center; padding: 60px 40px; background: var(--light-gray); border-radius: 15px;">
                        <i class="fas fa-search" style="font-size: 4rem; color: var(--medium-gray); margin-bottom: 30px;" aria-hidden="true"></i>
                        <h2 style="color: var(--dark-green); margin-bottom: 20px;">
                            <?php _e('No content found', 'kilismile'); ?>
                        </h2>
                        <p style="color: var(--text-secondary); margin-bottom: 30px;">
                            <?php _e('There are no items in this archive yet. Check back later or explore other sections.', 'kilismile'); ?>
                        </p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                            <?php _e('Back to Home', 'kilismile'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="archive-sidebar" style="flex: 1; max-width: 350px;">
                <!-- Categories/Tags Widget -->
                <?php if (is_category() || is_tag() || is_post_type_archive('post')) : ?>
                    <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                            <?php _e('Categories', 'kilismile'); ?>
                        </h3>
                        <ul style="list-style: none; padding: 0;">
                            <?php
                            $categories = get_categories(array('hide_empty' => true));
                            foreach ($categories as $category) : ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                                       style="display: flex; justify-content: space-between; padding: 8px 0; color: var(--text-secondary); text-decoration: none; border-bottom: 1px solid #f0f0f0;">
                                        <span><?php echo esc_html($category->name); ?></span>
                                        <span style="background: var(--light-gray); color: var(--text-secondary); padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                                            <?php echo $category->count; ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Recent Posts -->
                <div class="sidebar-widget" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                        <?php _e('Recent Posts', 'kilismile'); ?>
                    </h3>
                    
                    <?php
                    $recent_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    if ($recent_posts->have_posts()) : ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none; font-size: 0.9rem; line-height: 1.4;">
                                        <?php the_title(); ?>
                                    </a>
                                    <div style="color: var(--medium-gray); font-size: 0.8rem; margin-top: 5px;">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
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

                <!-- Call to Action -->
                <div class="sidebar-widget" style="background: var(--primary-green); color: white; padding: 30px; border-radius: 15px;">
                    <h3 style="color: white; margin-bottom: 15px;">
                        <?php _e('Support Our Mission', 'kilismile'); ?>
                    </h3>
                    <p style="margin-bottom: 20px; opacity: 0.9; font-size: 0.9rem;">
                        <?php _e('Help us continue our vital work in health education and community development.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/donate')); ?>" 
                       style="display: inline-block; padding: 12px 20px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('Donate Now', 'kilismile'); ?>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>

<script>
    // View toggle functionality
    function setView(view) {
        const gridView = document.getElementById('posts-grid');
        const listView = document.getElementById('posts-list');
        const buttons = document.querySelectorAll('.view-btn');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        
        if (view === 'grid') {
            gridView.style.display = 'grid';
            listView.style.display = 'none';
        } else {
            gridView.style.display = 'none';
            listView.style.display = 'block';
        }
        
        // Store preference
        localStorage.setItem('kilismile_archive_view', view);
    }
    
    // Restore view preference
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('kilismile_archive_view') || 'grid';
        setView(savedView);
    });
    
    // Filter functionality (basic implementation)
    function filterArchive(select) {
        // This would typically be implemented with AJAX
        // For now, we'll just reload the page with query parameters
        const filter = select.dataset.filter;
        const value = select.value;
        
        if (value) {
            const url = new URL(window.location);
            url.searchParams.set(filter, value);
            window.location = url.toString();
        } else {
            const url = new URL(window.location);
            url.searchParams.delete(filter);
            window.location = url.toString();
        }
    }
    
    // Sort functionality
    function sortArchive(select) {
        const sort = select.value;
        const url = new URL(window.location);
        url.searchParams.set('orderby', sort);
        window.location = url.toString();
    }
</script>

<style>
    .archive-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .archive-post-card:hover .post-thumbnail img {
        transform: scale(1.05);
    }
    
    .archive-post-list-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .read-more-btn:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
    }
    
    .view-btn.active {
        background: var(--primary-green) !important;
        color: white !important;
    }
    
    .view-btn:not(.active):hover {
        background: var(--light-gray);
        color: var(--dark-green);
    }
    
    .archive-pagination .page-numbers {
        display: inline-block;
        padding: 12px 16px;
        margin: 0 5px;
        background: white;
        color: var(--primary-green);
        text-decoration: none;
        border: 2px solid var(--primary-green);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .archive-pagination .page-numbers:hover,
    .archive-pagination .page-numbers.current {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .archive-content {
            flex-direction: column;
        }
        
        .posts-grid {
            grid-template-columns: 1fr;
        }
        
        .archive-post-list-item {
            flex-direction: column;
        }
        
        .archive-post-list-item .post-thumbnail {
            flex: none;
            height: 200px;
        }
        
        .filter-options,
        .sort-options {
            width: 100%;
        }
        
        .archive-filters > div {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<?php get_footer(); ?>
