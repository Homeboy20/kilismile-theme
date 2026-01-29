<?php
/**
 * Single Newsletter Template
 * 
 * @package KiliSmile
 */

get_header(); ?>

<?php
    $newsletter_page = get_page_by_path('newsletter');
    $newsletter_archive_url = $newsletter_page ? get_permalink($newsletter_page) : home_url('/newsletter/');
?>

<main id="main" class="site-main single-newsletter-page">
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('newsletter-article'); ?>>
            
            <!-- Newsletter Header -->
            <header class="newsletter-header">
                <div class="container">
                    <div class="newsletter-meta">
                        <a href="<?php echo esc_url($newsletter_archive_url); ?>" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                            <?php _e('Back to Newsletter Archive', 'kilismile'); ?>
                        </a>
                        
                        <div class="newsletter-info">
                            <?php 
                            $newsletter_issue = get_post_meta(get_the_ID(), '_newsletter_issue', true);
                            $newsletter_date = get_post_meta(get_the_ID(), '_newsletter_date', true);
                            $newsletter_recipients = get_post_meta(get_the_ID(), '_newsletter_recipients', true);
                            ?>
                            
                            <?php if ($newsletter_issue) : ?>
                                <span class="issue-badge">Issue #<?php echo esc_html($newsletter_issue); ?></span>
                            <?php endif; ?>
                            
                            <time class="newsletter-date" datetime="<?php echo esc_attr($newsletter_date ?: get_the_date('c')); ?>">
                                <?php echo $newsletter_date ? esc_html(date('F j, Y', strtotime($newsletter_date))) : get_the_date(); ?>
                            </time>
                            
                            <?php if ($newsletter_recipients) : ?>
                                <span class="recipients-count">
                                    <i class="fas fa-users"></i>
                                    <?php printf(__('Sent to %s subscribers', 'kilismile'), number_format($newsletter_recipients)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h1 class="newsletter-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()) : ?>
                        <div class="newsletter-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Newsletter Actions -->
                    <div class="newsletter-actions">
                        <?php 
                        $pdf_file = get_post_meta(get_the_ID(), '_newsletter_pdf', true);
                        if ($pdf_file) : ?>
                            <a href="<?php echo esc_url($pdf_file); ?>" class="btn btn-primary" target="_blank">
                                <i class="fas fa-download"></i>
                                <?php _e('Download PDF', 'kilismile'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <a href="mailto:?subject=<?php echo rawurlencode(get_the_title()); ?>&body=<?php echo rawurlencode(get_permalink()); ?>" class="btn btn-outline">
                            <i class="fas fa-envelope"></i>
                            <?php _e('Share via Email', 'kilismile'); ?>
                        </a>
                        
                        <button onclick="window.print()" class="btn btn-outline">
                            <i class="fas fa-print"></i>
                            <?php _e('Print', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Newsletter Content -->
            <div class="newsletter-content">
                <div class="container">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="newsletter-featured-image">
                            <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="newsletter-body">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Newsletter Navigation -->
                    <nav class="newsletter-navigation">
                        <div class="nav-links">
                            <?php
                            $prev_newsletter = get_previous_post();
                            $next_newsletter = get_next_post();
                            ?>
                            
                            <?php if ($prev_newsletter) : ?>
                                <div class="nav-previous">
                                    <a href="<?php echo esc_url(get_permalink($prev_newsletter->ID)); ?>" class="nav-link">
                                        <span class="nav-direction">
                                            <i class="fas fa-chevron-left"></i>
                                            <?php _e('Previous Newsletter', 'kilismile'); ?>
                                        </span>
                                        <span class="nav-title"><?php echo get_the_title($prev_newsletter->ID); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_newsletter) : ?>
                                <div class="nav-next">
                                    <a href="<?php echo esc_url(get_permalink($next_newsletter->ID)); ?>" class="nav-link">
                                        <span class="nav-direction">
                                            <?php _e('Next Newsletter', 'kilismile'); ?>
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                        <span class="nav-title"><?php echo get_the_title($next_newsletter->ID); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </nav>
                </div>
            </div>
            
        </article>
        
        <!-- Newsletter Signup CTA -->
        <section class="newsletter-signup-cta">
            <div class="container">
                <div class="cta-content">
                    <div class="cta-text">
                        <h2><?php _e('Subscribe to Our Newsletter', 'kilismile'); ?></h2>
                        <p><?php _e('Stay updated with our latest programs, success stories, and ways to get involved in improving oral health in Tanzania.', 'kilismile'); ?></p>
                    </div>
                    <div class="cta-action">
                        <a href="<?php echo esc_url(trailingslashit($newsletter_archive_url) . '#newsletter-subscription-form'); ?>" class="btn btn-primary">
                            <i class="fas fa-envelope"></i>
                            <?php _e('Subscribe Now', 'kilismile'); ?>
                        </a>
                        <a href="<?php echo esc_url(function_exists('kilismile_get_donation_page_url_legacy') ? kilismile_get_donation_page_url_legacy() : home_url('/donation/')); ?>" class="btn btn-outline">
                            <i class="fas fa-heart"></i>
                            <?php _e('Make a Donation', 'kilismile'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Related Newsletters -->
        <section class="related-newsletters">
            <div class="container">
                <h2><?php _e('More Newsletters', 'kilismile'); ?></h2>
                
                <div class="related-newsletters-grid">
                    <?php
                    $related_args = array(
                        'post_type' => 'newsletter',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    
                    $related_newsletters = new WP_Query($related_args);
                    
                    if ($related_newsletters->have_posts()) :
                        while ($related_newsletters->have_posts()) : $related_newsletters->the_post();
                            $newsletter_date = get_post_meta(get_the_ID(), '_newsletter_date', true);
                            $newsletter_issue = get_post_meta(get_the_ID(), '_newsletter_issue', true);
                            ?>
                            <div class="related-newsletter-item">
                                <div class="related-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php echo esc_url(get_permalink()); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    <?php else : ?>
                                        <div class="related-placeholder">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="related-content">
                                    <div class="related-meta">
                                        <?php if ($newsletter_issue) : ?>
                                            <span class="issue-number">Issue #<?php echo esc_html($newsletter_issue); ?></span>
                                        <?php endif; ?>
                                        <time class="related-date">
                                            <?php echo $newsletter_date ? esc_html(date('M j, Y', strtotime($newsletter_date))) : get_the_date('M j, Y'); ?>
                                        </time>
                                    </div>
                                    
                                    <h3 class="related-title">
                                        <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="related-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </div>
                                    
                                    <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">
                                        <?php _e('Read More', 'kilismile'); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>
        
    <?php endwhile; ?>
</main>

<!-- Newsletter Print Styles -->
<style media="print">
    .newsletter-header .newsletter-actions,
    .newsletter-navigation,
    .newsletter-signup-cta,
    .related-newsletters,
    .site-header,
    .site-footer {
        display: none !important;
    }
    
    .newsletter-content {
        font-size: 14px;
        line-height: 1.6;
    }
    
    .newsletter-title {
        font-size: 24px;
        margin-bottom: 20px;
    }
    
    .newsletter-body img {
        max-width: 100% !important;
        height: auto !important;
    }
    
    .back-link::after {
        content: " (Newsletter available at: " attr(href) ")";
        font-size: 12px;
        color: #666;
    }
</style>

<?php get_footer(); ?>


