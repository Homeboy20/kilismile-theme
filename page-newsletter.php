<?php
/**
 * Template Name: Newsletter Page
 * 
 * Newsletter subscription and archive page for Kili Smile Organization
 *
 * @package KiliSmile
 */

get_header(); ?>

<main id="main" class="site-main newsletter-page">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Hero Section -->
        <section class="newsletter-hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <div class="hero-badge">
                            <i class="fas fa-envelope"></i>
                            <span>Join Our Community</span>
                        </div>
                        <h1 class="page-title"><?php the_title(); ?></h1>
                        <p class="hero-subtitle">Stay Connected with Our Mission to Transform Lives</p>
                        <p class="hero-description">
                            Join over <strong>1,250 supporters</strong> from <strong>45 countries</strong> who receive exclusive updates 
                            on our health programs, inspiring success stories from Tanzania, upcoming volunteer opportunities, 
                            and actionable ways you can make a lasting difference in oral health education.
                        </p>
                        <div class="hero-cta">
                            <a href="#newsletter-subscription-form" class="btn btn-white smooth-scroll">
                                <i class="fas fa-envelope"></i>
                                Subscribe Now - It's Free
                            </a>
                            <div class="hero-social-proof">
                                <div class="subscriber-avatars">
                                    <div class="avatar"></div>
                                    <div class="avatar"></div>
                                    <div class="avatar"></div>
                                    <div class="avatar plus">+</div>
                                </div>
                                <span>Join 1,250+ subscribers</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero-image">
                        <div class="newsletter-preview">
                            <div class="device-mockup">
                                <div class="newsletter-content-preview">
                                    <div class="header-bar"></div>
                                    <div class="content-lines">
                                        <div class="line"></div>
                                        <div class="line short"></div>
                                        <div class="line"></div>
                                        <div class="line medium"></div>
                                    </div>
                                    <div class="image-placeholder"></div>
                                    <div class="content-lines">
                                        <div class="line"></div>
                                        <div class="line short"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
        </section>

        <!-- Newsletter Subscription Section -->
        <section class="newsletter-subscription">
            <div class="container">
                <div class="subscription-content">
                    <div class="subscription-form-wrapper">
                        <div class="form-header">
                            <h2>Get Exclusive Updates</h2>
                            <p>Join our community and receive monthly insights, impact stories, and early access to volunteer opportunities.</p>
                            
                            <div class="newsletter-preview-tabs">
                                <button class="preview-tab active" data-tab="monthly">Monthly Newsletter</button>
                                <button class="preview-tab" data-tab="impact">Impact Reports</button>
                                <button class="preview-tab" data-tab="events">Event Alerts</button>
                            </div>
                            
                            <div class="newsletter-preview-content">
                                <div class="preview-item active" data-content="monthly">
                                    <div class="preview-icon">📧</div>
                                    <div class="preview-text">
                                        <strong>Monthly Newsletter:</strong> Program updates, success stories, and health education tips
                                    </div>
                                </div>
                                <div class="preview-item" data-content="impact">
                                    <div class="preview-icon">📊</div>
                                    <div class="preview-text">
                                        <strong>Impact Reports:</strong> Detailed reports on lives changed and communities served
                                    </div>
                                </div>
                                <div class="preview-item" data-content="events">
                                    <div class="preview-icon">📅</div>
                                    <div class="preview-text">
                                        <strong>Event Alerts:</strong> Be first to know about volunteer opportunities and fundraising events
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form id="newsletter-subscription-form" class="newsletter-form modern-form" method="post" action="">
                            <?php wp_nonce_field('kilismile_newsletter_nonce', 'newsletter_nonce'); ?>
                            
                            <div class="form-step active" data-step="1">
                                <h3>Let's get to know you</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="newsletter_first_name">
                                            <i class="fas fa-user"></i>
                                            First Name *
                                        </label>
                                        <input type="text" id="newsletter_first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="newsletter_last_name">
                                            <i class="fas fa-user"></i>
                                            Last Name *
                                        </label>
                                        <input type="text" id="newsletter_last_name" name="last_name" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="newsletter_email">
                                        <i class="fas fa-envelope"></i>
                                        Email Address *
                                    </label>
                                    <input type="email" id="newsletter_email" name="email" required>
                                    <div class="input-hint">We'll never share your email address</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="newsletter_location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Location (Optional)
                                    </label>
                                    <input type="text" id="newsletter_location" name="location" placeholder="City, Country">
                                    <div class="input-hint">Help us understand our global community</div>
                                </div>
                                
                                <button type="button" class="btn btn-primary next-step">
                                    Next: Choose Your Interests
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            
                            <div class="form-step" data-step="2">
                                <h3>What interests you most?</h3>
                                <p>Select the topics you'd like to hear about (choose all that apply)</p>
                                
                                <div class="interests-grid">
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="programs" checked>
                                        <div class="card-content">
                                            <div class="card-icon">🏥</div>
                                            <div class="card-title">Health Programs</div>
                                            <div class="card-description">Updates on our oral health initiatives and community screenings</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                    
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="stories">
                                        <div class="card-content">
                                            <div class="card-icon">❤️</div>
                                            <div class="card-title">Success Stories</div>
                                            <div class="card-description">Inspiring stories from communities we've helped</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                    
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="events">
                                        <div class="card-content">
                                            <div class="card-icon">📅</div>
                                            <div class="card-title">Events & Volunteering</div>
                                            <div class="card-description">Volunteer opportunities and upcoming events</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                    
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="health_tips">
                                        <div class="card-content">
                                            <div class="card-icon">🦷</div>
                                            <div class="card-title">Health Education</div>
                                            <div class="card-description">Oral health tips and educational resources</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                    
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="fundraising">
                                        <div class="card-content">
                                            <div class="card-icon">💝</div>
                                            <div class="card-title">Fundraising</div>
                                            <div class="card-description">Special campaigns and donation opportunities</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                    
                                    <label class="interest-card">
                                        <input type="checkbox" name="interests[]" value="research">
                                        <div class="card-content">
                                            <div class="card-icon">📊</div>
                                            <div class="card-title">Research & Reports</div>
                                            <div class="card-description">Impact reports and research findings</div>
                                        </div>
                                        <div class="card-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-outline prev-step">
                                        <i class="fas fa-arrow-left"></i>
                                        Back
                                    </button>
                                    <button type="button" class="btn btn-primary next-step">
                                        Next: Email Preferences
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-step" data-step="3">
                                <h3>Email preferences</h3>
                                <p>How often would you like to hear from us?</p>
                                
                                <div class="frequency-options">
                                    <label class="frequency-card">
                                        <input type="radio" name="frequency" value="monthly" checked>
                                        <div class="card-content">
                                            <div class="card-icon">📅</div>
                                            <div class="card-info">
                                                <div class="card-title">Monthly Newsletter</div>
                                                <div class="card-description">Perfect for staying updated without inbox overload</div>
                                                <div class="card-badge">Most Popular</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="frequency-card">
                                        <input type="radio" name="frequency" value="weekly">
                                        <div class="card-content">
                                            <div class="card-icon">📬</div>
                                            <div class="card-info">
                                                <div class="card-title">Weekly Updates</div>
                                                <div class="card-description">Get the latest news and opportunities as they happen</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="frequency-card">
                                        <input type="radio" name="frequency" value="quarterly">
                                        <div class="card-content">
                                            <div class="card-icon">📊</div>
                                            <div class="card-info">
                                                <div class="card-title">Quarterly Reports</div>
                                                <div class="card-description">Comprehensive impact reports four times a year</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="consent-section">
                                    <label class="consent-checkbox">
                                        <input type="checkbox" name="consent" value="yes" required>
                                        <div class="consent-content">
                                            <div class="consent-title">
                                                <i class="fas fa-shield-alt"></i>
                                                Privacy & Consent
                                            </div>
                                            <div class="consent-text">
                                                I agree to receive newsletters and communications from Kili Smile Organization. 
                                                Your data is secure and you can unsubscribe at any time. 
                                                <a href="<?php echo home_url('/privacy-policy'); ?>" target="_blank">Read our Privacy Policy</a>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-outline prev-step">
                                        <i class="fas fa-arrow-left"></i>
                                        Back
                                    </button>
                                    <button type="submit" name="subscribe_newsletter" class="btn btn-primary subscribe-btn">
                                        <i class="fas fa-envelope"></i>
                                        Subscribe Now
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div id="newsletter-message" class="form-message" style="display: none;"></div>
                        
                        <div class="trust-indicators">
                            <div class="trust-item">
                                <i class="fas fa-lock"></i>
                                <span>Your data is secure</span>
                            </div>
                            <div class="trust-item">
                                <i class="fas fa-times-circle"></i>
                                <span>Unsubscribe anytime</span>
                            </div>
                            <div class="trust-item">
                                <i class="fas fa-heart"></i>
                                <span>No spam, just impact</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="subscription-benefits">
                        <h3>What You'll Receive</h3>
                        <div class="benefits-list">
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Impact Stories</h4>
                                    <p>Read inspiring stories from communities we've helped and see the real impact of your support.</p>
                                </div>
                            </div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Event Updates</h4>
                                    <p>Be the first to know about upcoming events, volunteer opportunities, and ways to get involved.</p>
                                </div>
                            </div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Health Education</h4>
                                    <p>Learn about oral health best practices and tips you can share with your community.</p>
                                </div>
                            </div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Progress Reports</h4>
                                    <p>See detailed reports on our programs' progress and how donations are being used.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Archive Section -->
        <section class="newsletter-archive">
            <div class="container">
                <h2>Newsletter Archive</h2>
                <p>Catch up on our recent newsletters and see what we've been up to.</p>
                
                <div class="newsletter-grid">
                    <?php
                    // Get newsletter posts (we'll create a custom post type for newsletters)
                    $newsletter_args = array(
                        'post_type' => 'newsletter',
                        'posts_per_page' => 6,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    
                    $newsletters = new WP_Query($newsletter_args);
                    
                    if ($newsletters->have_posts()) :
                        while ($newsletters->have_posts()) : $newsletters->the_post();
                            $newsletter_date = get_post_meta(get_the_ID(), '_newsletter_date', true);
                            $newsletter_issue = get_post_meta(get_the_ID(), '_newsletter_issue', true);
                            ?>
                            <div class="newsletter-item">
                                <div class="newsletter-thumbnail">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <div class="newsletter-placeholder">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="newsletter-content">
                                    <div class="newsletter-meta">
                                        <?php if ($newsletter_issue) : ?>
                                            <span class="issue-number">Issue #<?php echo esc_html($newsletter_issue); ?></span>
                                        <?php endif; ?>
                                        <span class="newsletter-date">
                                            <?php echo $newsletter_date ? esc_html($newsletter_date) : get_the_date(); ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="newsletter-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="newsletter-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <div class="newsletter-actions">
                                        <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                            <i class="fas fa-eye"></i>
                                            Read Online
                                        </a>
                                        <?php 
                                        $pdf_file = get_post_meta(get_the_ID(), '_newsletter_pdf', true);
                                        if ($pdf_file) : ?>
                                            <a href="<?php echo esc_url($pdf_file); ?>" class="btn btn-outline" target="_blank">
                                                <i class="fas fa-download"></i>
                                                Download PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="no-newsletters">
                            <i class="fas fa-newspaper"></i>
                            <h3>No Newsletters Yet</h3>
                            <p>We're working on our first newsletter. Subscribe above to be notified when it's ready!</p>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
                
                <?php if ($newsletters->found_posts > 6) : ?>
                    <div class="newsletter-pagination">
                        <a href="#" class="btn btn-primary load-more-newsletters" data-page="2">
                            <i class="fas fa-plus"></i>
                            Load More Newsletters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Newsletter Stats Section -->
        <section class="newsletter-stats">
            <div class="container">
                <h2>Our Newsletter Community</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="1250">0</div>
                            <div class="stat-label">Subscribers</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="45">0</div>
                            <div class="stat-label">Countries Reached</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="89">0</div>
                            <div class="stat-label">Open Rate %</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="24">0</div>
                            <div class="stat-label">Issues Published</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="newsletter-cta">
            <div class="container">
                <div class="cta-content">
                    <h2>Join Our Mission</h2>
                    <p>Your subscription helps us stay connected and builds a community of supporters working together to improve oral health in Tanzania.</p>
                    <div class="cta-actions">
                        <a href="#newsletter-subscription-form" class="btn btn-primary smooth-scroll">
                            <i class="fas fa-envelope"></i>
                            Subscribe Now
                        </a>
                        <a href="<?php echo home_url('/donate'); ?>" class="btn btn-outline">
                            <i class="fas fa-heart"></i>
                            Make a Donation
                        </a>
                    </div>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
