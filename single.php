<?php
/**
 * Single Post Template
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <?php if (get_theme_mod('kilismile_enable_breadcrumbs', true)) : ?>
            <?php kilismile_breadcrumbs(); ?>
        <?php endif; ?>
        
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                <header class="entry-header" style="text-align: center; margin-bottom: 40px; padding: 60px 0;">
                    <h1 class="entry-title" style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 20px;">
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="entry-meta" style="color: var(--medium-gray); margin-bottom: 20px;">
                        <span class="posted-on">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </span>
                        
                        <?php if (has_category()) : ?>
                            <span class="cat-links" style="margin-left: 20px;">
                                <i class="fas fa-folder" aria-hidden="true"></i>
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                        
                        <span class="author-link" style="margin-left: 20px;">
                            <i class="fas fa-user" aria-hidden="true"></i>
                            <?php the_author(); ?>
                        </span>
                    </div>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="featured-image" style="margin: 40px 0;">
                            <?php the_post_thumbnail('large', array('style' => 'width: 100%; max-width: 800px; height: auto; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);')); ?>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="entry-content" style="max-width: 800px; margin: 0 auto; font-size: 1.1rem; line-height: 1.8; color: #444;">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">',
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="entry-footer" style="max-width: 800px; margin: 60px auto 0; padding: 30px 0; border-top: 1px solid #e0e0e0;">
                    <?php if (has_tag()) : ?>
                        <div class="tag-links" style="margin-bottom: 20px;">
                            <strong><?php _e('Tags:', 'kilismile'); ?></strong>
                            <?php the_tags('', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Sharing -->
                    <div class="social-sharing" style="text-align: center;">
                        <h4 style="margin-bottom: 20px; color: var(--dark-green);"><?php _e('Share this post', 'kilismile'); ?></h4>
                        <div class="share-buttons" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                            <?php 
                            $post_url = get_permalink();
                            $post_title = get_the_title();
                            ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($post_url); ?>" 
                               target="_blank" 
                               class="share-btn facebook" 
                               style="display: flex; align-items: center; padding: 10px 15px; background: #3b5998; color: white; text-decoration: none; border-radius: 25px; transition: all 0.3s ease;">
                                <i class="fab fa-facebook-f" style="margin-right: 8px;"></i>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($post_url); ?>&text=<?php echo urlencode($post_title); ?>" 
                               target="_blank" 
                               class="share-btn twitter" 
                               style="display: flex; align-items: center; padding: 10px 15px; background: #1da1f2; color: white; text-decoration: none; border-radius: 25px; transition: all 0.3s ease;">
                                <i class="fab fa-twitter" style="margin-right: 8px;"></i>
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($post_url); ?>" 
                               target="_blank" 
                               class="share-btn linkedin" 
                               style="display: flex; align-items: center; padding: 10px 15px; background: #0077b5; color: white; text-decoration: none; border-radius: 25px; transition: all 0.3s ease;">
                                <i class="fab fa-linkedin-in" style="margin-right: 8px;"></i>
                                LinkedIn
                            </a>
                            <a href="whatsapp://send?text=<?php echo urlencode($post_title . ' ' . $post_url); ?>" 
                               class="share-btn whatsapp" 
                               style="display: flex; align-items: center; padding: 10px 15px; background: #25d366; color: white; text-decoration: none; border-radius: 25px; transition: all 0.3s ease;">
                                <i class="fab fa-whatsapp" style="margin-right: 8px;"></i>
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </footer>
            </article>

            <!-- Related Posts -->
            <section class="related-posts" style="margin: 80px 0; padding: 60px 0; background: var(--light-gray); border-radius: 15px;">
                <div class="container">
                    <h3 style="text-align: center; margin-bottom: 40px; color: var(--dark-green);">
                        <?php _e('Related Articles', 'kilismile'); ?>
                    </h3>
                    
                    <?php
                    $related_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'rand',
                        'post_status' => 'publish'
                    ));
                    
                    if ($related_posts->have_posts()) : ?>
                        <div class="related-posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                            <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                                <article class="related-post-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 200px; object-fit: cover;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content" style="padding: 20px;">
                                        <h4 style="margin-bottom: 10px;">
                                            <a href="<?php the_permalink(); ?>" style="color: var(--dark-green); text-decoration: none;">
                                                <?php the_title(); ?>
                                            </a>
                                        </h4>
                                        <div class="post-meta" style="color: var(--medium-gray); font-size: 0.9rem; margin-bottom: 10px;">
                                            <?php echo get_the_date(); ?>
                                        </div>
                                        <p style="color: var(--text-secondary); line-height: 1.6;">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                        </p>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php 
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>

            <!-- Comments -->
            <?php if (comments_open() || get_comments_number()) : ?>
                <section class="comments-section" style="max-width: 800px; margin: 60px auto;">
                    <?php comments_template(); ?>
                </section>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<style>
    .share-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .related-post-card:hover {
        transform: translateY(-5px);
    }
    
    .entry-content h2,
    .entry-content h3,
    .entry-content h4 {
        color: var(--dark-green);
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .entry-content p {
        margin-bottom: 1.5rem;
    }
    
    .entry-content blockquote {
        background: var(--light-gray);
        border-left: 5px solid var(--primary-green);
        margin: 2rem 0;
        padding: 1.5rem;
        border-radius: 0 10px 10px 0;
        font-style: italic;
    }
    
    .entry-content ul,
    .entry-content ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
    }
    
    .entry-content li {
        margin-bottom: 0.5rem;
    }
</style>

<?php get_footer(); ?>
