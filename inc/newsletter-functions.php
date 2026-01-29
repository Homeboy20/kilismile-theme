<?php
/**
 * Newsletter Functions for Kilismile Organization
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create Newsletter Subscribers Database Table
 */
function kilismile_create_newsletter_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        first_name varchar(50) NOT NULL,
        last_name varchar(50) NOT NULL,
        location varchar(100) DEFAULT '',
        interests text DEFAULT '',
        frequency varchar(20) DEFAULT 'monthly',
        status varchar(20) DEFAULT 'active',
        subscribe_date datetime DEFAULT CURRENT_TIMESTAMP,
        confirmation_token varchar(64) DEFAULT '',
        confirmed tinyint(1) DEFAULT 0,
        last_email_sent datetime DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Create table on theme activation
add_action('after_switch_theme', 'kilismile_create_newsletter_table');

/**
 * Handle Newsletter Subscription Form
 */
function kilismile_handle_newsletter_subscription() {
    if (isset($_POST['subscribe_newsletter']) && wp_verify_nonce($_POST['newsletter_nonce'], 'kilismile_newsletter_nonce')) {
        
        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $location = sanitize_text_field($_POST['location']);
        $interests = isset($_POST['interests']) ? array_map('sanitize_text_field', $_POST['interests']) : array();
        $frequency = sanitize_text_field($_POST['frequency']);
        
        // Validate required fields
        if (empty($email) || empty($first_name) || empty($last_name)) {
            wp_die(__('Please fill in all required fields.', 'kilismile'));
        }
        
        if (!is_email($email)) {
            wp_die(__('Please enter a valid email address.', 'kilismile'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
        
        // Check if email already exists
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s",
            $email
        ));
        
        if ($existing) {
            // Update existing subscriber
            $result = $wpdb->update(
                $table_name,
                array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'location' => $location,
                    'interests' => implode(',', $interests),
                    'frequency' => $frequency,
                    'status' => 'active'
                ),
                array('email' => $email),
                array('%s', '%s', '%s', '%s', '%s', '%s'),
                array('%s')
            );
            
            $message = __('Your subscription has been updated successfully!', 'kilismile');
        } else {
            // Create new subscriber
            $confirmation_token = wp_generate_password(32, false);
            
            $result = $wpdb->insert(
                $table_name,
                array(
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'location' => $location,
                    'interests' => implode(',', $interests),
                    'frequency' => $frequency,
                    'confirmation_token' => $confirmation_token,
                    'subscribe_date' => current_time('mysql')
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
            );
            
            if ($result) {
                // Send confirmation email
                kilismile_send_confirmation_email($email, $first_name, $confirmation_token);
                $message = __('Thank you for subscribing! Please check your email to confirm your subscription.', 'kilismile');
            } else {
                $message = __('There was an error processing your subscription. Please try again.', 'kilismile');
            }
        }
        
        // Redirect with success message
        wp_redirect(add_query_arg('newsletter_message', urlencode($message), wp_get_referer()));
        exit;
    }
}
add_action('wp', 'kilismile_handle_newsletter_subscription');

/**
 * Send Confirmation Email
 */
function kilismile_send_confirmation_email($email, $first_name, $token) {
    $confirmation_url = add_query_arg(
        array(
            'action' => 'confirm_newsletter',
            'token' => $token,
            'email' => urlencode($email)
        ),
        home_url()
    );
    
    $subject = __('Confirm Your Newsletter Subscription - Kilismile Organization', 'kilismile');
    
    // Use the enhanced email system if available
    if (function_exists('kilismile_send_welcome')) {
        $data = array(
            'first_name' => $first_name,
            'confirmation_url' => $confirmation_url,
            'confirmation_required' => true
        );
        return kilismile_send_welcome($email, $first_name, $data);
    }
    
    // Fallback to basic email
    $message = sprintf(
        __('Hello %s,

Thank you for subscribing to the Kilismile Organization newsletter!

To complete your subscription, please click the link below:
%s

If you did not request this subscription, you can safely ignore this email.

Best regards,
The Kilismile Team

--
Kilismile Organization
"No health without oral health"
Email: %s
Website: %s', 'kilismile'),
        $first_name,
        $confirmation_url,
        get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
        home_url()
    );
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Kilismile Organization <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>'
    );
    
    return wp_mail($email, $subject, $message, $headers);
}

/**
 * Handle Newsletter Confirmation
 */
function kilismile_handle_newsletter_confirmation() {
    if (isset($_GET['action']) && $_GET['action'] === 'confirm_newsletter' && 
        isset($_GET['token']) && isset($_GET['email'])) {
        
        $token = sanitize_text_field($_GET['token']);
        $email = sanitize_email($_GET['email']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
        
        $subscriber = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s AND confirmation_token = %s",
            $email, $token
        ));
        
        if ($subscriber) {
            // Confirm the subscription
            $wpdb->update(
                $table_name,
                array('confirmed' => 1, 'status' => 'active'),
                array('email' => $email),
                array('%d', '%s'),
                array('%s')
            );
            
            // Send welcome email
            kilismile_send_welcome_email($email, $subscriber->first_name);
            
            // Redirect to newsletter page with success message
            wp_redirect(add_query_arg('confirmed', '1', home_url('/newsletter')));
            exit;
        } else {
            wp_redirect(add_query_arg('error', 'invalid_token', home_url('/newsletter')));
            exit;
        }
    }
}
add_action('wp', 'kilismile_handle_newsletter_confirmation');

/**
 * Send Welcome Email
 */
function kilismile_send_welcome_email($email, $first_name) {
    $subject = __('Welcome to Kilismile Newsletter!', 'kilismile');
    
    // Use the enhanced email system if available
    if (function_exists('kilismile_send_welcome')) {
        return kilismile_send_welcome($email, $first_name);
    }
    
    // Fallback to basic email
    $message = sprintf(
        __('Hello %s,

Welcome to the Kilismile Organization newsletter family!

Your subscription has been confirmed successfully. You will now receive our updates about:

• Health programs and their impact
• Success stories from Tanzania
• Upcoming events and volunteer opportunities
• Health education tips and resources
• Ways to support our mission

Thank you for joining us in our mission to improve oral health in remote communities of Tanzania.

Visit our website: %s
Learn about our programs: %s/programs
Get involved: %s/volunteer

Best regards,
The Kilismile Team

--
Kilismile Organization
"No health without oral health"
Registration: %s
Email: %s', 'kilismile'),
        $first_name,
        home_url(),
        home_url(),
        home_url(),
        get_theme_mod('kilismile_registration', '07NGO/R/6067'),
        get_theme_mod('kilismile_email', 'kilismile21@gmail.com')
    );
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Kilismile Organization <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>'
    );
    
    return wp_mail($email, $subject, $message, $headers);
}

/**
 * Handle Newsletter Unsubscription
 */
function kilismile_handle_newsletter_unsubscription() {
    if (isset($_GET['action']) && $_GET['action'] === 'unsubscribe_newsletter' && 
        isset($_GET['email'])) {
        
        $email = sanitize_email($_GET['email']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
        
        $wpdb->update(
            $table_name,
            array('status' => 'unsubscribed'),
            array('email' => $email),
            array('%s'),
            array('%s')
        );
        
        wp_redirect(add_query_arg('unsubscribed', '1', home_url('/newsletter')));
        exit;
    }
}
add_action('wp', 'kilismile_handle_newsletter_unsubscription');

/**
 * Get Newsletter Subscriber Count
 */
function kilismile_get_subscriber_count() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
    
    $count = $wpdb->get_var(
        "SELECT COUNT(*) FROM $table_name WHERE status = 'active' AND confirmed = 1"
    );
    
    return intval($count);
}

/**
 * Register Newsletter Shortcodes
 */
function kilismile_register_newsletter_shortcodes() {
    add_shortcode('kilismile_newsletter_form', 'kilismile_newsletter_form_shortcode');
    add_shortcode('kilismile_recent_newsletters', 'kilismile_recent_newsletters_shortcode');
    add_shortcode('kilismile_subscriber_count', 'kilismile_subscriber_count_shortcode');
}
add_action('init', 'kilismile_register_newsletter_shortcodes');

/**
 * Newsletter Form Shortcode
 */
function kilismile_newsletter_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Subscribe to Our Newsletter', 'kilismile'),
        'description' => __('Join our community and stay updated with our latest news, events, and success stories.', 'kilismile'),
        'button_text' => __('Subscribe Now', 'kilismile'),
        'show_preferences' => 'yes',
    ), $atts);
    
    ob_start();
    ?>
    <div class="kilismile-newsletter-form-container">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="newsletter-form-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>
        
        <?php if (!empty($atts['description'])) : ?>
            <p class="newsletter-form-description"><?php echo esc_html($atts['description']); ?></p>
        <?php endif; ?>
        
        <form id="newsletter-subscription-form" class="newsletter-form" method="post" action="">
            <?php wp_nonce_field('kilismile_newsletter_nonce', 'newsletter_nonce'); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="newsletter_first_name"><?php _e('First Name', 'kilismile'); ?> *</label>
                    <input type="text" id="newsletter_first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="newsletter_last_name"><?php _e('Last Name', 'kilismile'); ?> *</label>
                    <input type="text" id="newsletter_last_name" name="last_name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="newsletter_email"><?php _e('Email Address', 'kilismile'); ?> *</label>
                <input type="email" id="newsletter_email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="newsletter_location"><?php _e('Location (Optional)', 'kilismile'); ?></label>
                <input type="text" id="newsletter_location" name="location" placeholder="<?php _e('City, Country', 'kilismile'); ?>">
            </div>
            
            <?php if ($atts['show_preferences'] === 'yes') : ?>
                <div class="form-group checkbox-group">
                    <h3><?php _e('Subscription Preferences', 'kilismile'); ?></h3>
                    <label class="checkbox-label">
                        <input type="checkbox" name="interests[]" value="programs" checked>
                        <span class="checkmark"></span>
                        <?php _e('Program Updates & Success Stories', 'kilismile'); ?>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="interests[]" value="events">
                        <span class="checkmark"></span>
                        <?php _e('Events & Volunteer Opportunities', 'kilismile'); ?>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="interests[]" value="health_tips">
                        <span class="checkmark"></span>
                        <?php _e('Health Education Tips', 'kilismile'); ?>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="interests[]" value="fundraising">
                        <span class="checkmark"></span>
                        <?php _e('Fundraising Campaigns', 'kilismile'); ?>
                    </label>
                </div>
                
                <div class="form-group frequency-group">
                    <h3><?php _e('Email Frequency', 'kilismile'); ?></h3>
                    <label class="radio-label">
                        <input type="radio" name="frequency" value="monthly" checked>
                        <span class="radio-mark"></span>
                        <?php _e('Monthly Newsletter', 'kilismile'); ?>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="frequency" value="weekly">
                        <span class="radio-mark"></span>
                        <?php _e('Weekly Updates', 'kilismile'); ?>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="frequency" value="quarterly">
                        <span class="radio-mark"></span>
                        <?php _e('Quarterly Reports', 'kilismile'); ?>
                    </label>
                </div>
            <?php endif; ?>
            
            <div class="form-group consent-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="consent" value="yes" required>
                    <span class="checkmark"></span>
                    <?php _e('I agree to receive newsletters and communications from Kilismile Organization. You can unsubscribe at any time.', 'kilismile'); ?>
                </label>
            </div>
            
            <button type="submit" name="subscribe_newsletter" class="btn btn-primary">
                <i class="fas fa-envelope"></i>
                <?php echo esc_html($atts['button_text']); ?>
            </button>
        </form>
        
        <div id="newsletter-message" class="form-message" style="display: none;"></div>
    </div>
    
    <script>
    (function($) {
        $(document).ready(function() {
            $('#newsletter-subscription-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {
                        action: 'newsletter_subscription',
                        ...formData
                    },
                    beforeSend: function() {
                        $('#newsletter-message').html('<div class="loading">Processing your subscription...</div>').show();
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#newsletter-message').html('<div class="success">' + response.data + '</div>').show();
                            $('#newsletter-subscription-form')[0].reset();
                        } else {
                            $('#newsletter-message').html('<div class="error">' + response.data + '</div>').show();
                        }
                    },
                    error: function() {
                        $('#newsletter-message').html('<div class="error">An error occurred. Please try again later.</div>').show();
                    }
                });
            });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Recent Newsletters Shortcode
 */
function kilismile_recent_newsletters_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 3,
        'show_thumbnail' => 'yes',
        'show_excerpt' => 'yes',
        'columns' => 3,
        'view_all_link' => 'yes',
        'view_all_text' => __('View All Newsletters', 'kilismile')
    ), $atts);
    
    $limit = intval($atts['limit']);
    $columns = intval($atts['columns']);
    
    if ($columns < 1 || $columns > 4) {
        $columns = 3;
    }
    
    $newsletter_args = array(
        'post_type' => 'newsletter',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $newsletters = new WP_Query($newsletter_args);
    
    ob_start();
    
    if ($newsletters->have_posts()) :
        ?>
        <div class="kilismile-recent-newsletters columns-<?php echo esc_attr($columns); ?>">
            <div class="newsletter-grid">
                <?php while ($newsletters->have_posts()) : $newsletters->the_post();
                    $newsletter_date = get_post_meta(get_the_ID(), '_newsletter_date', true);
                    $newsletter_issue = get_post_meta(get_the_ID(), '_newsletter_issue', true);
                    $pdf_file = get_post_meta(get_the_ID(), '_newsletter_pdf', true);
                    ?>
                    <div class="newsletter-item">
                        <?php if ($atts['show_thumbnail'] === 'yes') : ?>
                            <div class="newsletter-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="newsletter-placeholder">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
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
                            
                            <?php if ($atts['show_excerpt'] === 'yes') : ?>
                                <div class="newsletter-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="newsletter-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i>
                                    <?php _e('Read Online', 'kilismile'); ?>
                                </a>
                                <?php if ($pdf_file) : ?>
                                    <a href="<?php echo esc_url($pdf_file); ?>" class="btn btn-outline" target="_blank">
                                        <i class="fas fa-download"></i>
                                        <?php _e('Download PDF', 'kilismile'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <?php if ($atts['view_all_link'] === 'yes' && $newsletters->found_posts > $limit) : ?>
                <div class="newsletter-view-all">
                    <a href="<?php echo esc_url(get_post_type_archive_link('newsletter')); ?>" class="btn btn-primary">
                        <i class="fas fa-list"></i>
                        <?php echo esc_html($atts['view_all_text']); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    else :
        ?>
        <div class="no-newsletters">
            <i class="fas fa-newspaper"></i>
            <h3><?php _e('No Newsletters Yet', 'kilismile'); ?></h3>
            <p><?php _e('We\'re working on our first newsletter. Subscribe above to be notified when it\'s ready!', 'kilismile'); ?></p>
        </div>
        <?php
    endif;
    
    return ob_get_clean();
}

/**
 * Subscriber Count Shortcode
 */
function kilismile_subscriber_count_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text_before' => __('Join our', 'kilismile'),
        'text_after' => __('subscribers', 'kilismile'),
        'show_icon' => 'yes',
        'icon' => 'fas fa-users',
        'animation' => 'yes'
    ), $atts);
    
    $count = kilismile_get_subscriber_count();
    
    ob_start();
    ?>
    <div class="kilismile-subscriber-count">
        <?php if ($atts['show_icon'] === 'yes') : ?>
            <i class="<?php echo esc_attr($atts['icon']); ?>"></i>
        <?php endif; ?>
        
        <span class="subscriber-count-text">
            <?php if (!empty($atts['text_before'])) : ?>
                <span class="text-before"><?php echo esc_html($atts['text_before']); ?></span>
            <?php endif; ?>
            
            <span class="count-number <?php echo $atts['animation'] === 'yes' ? 'animated' : ''; ?>" 
                  data-count="<?php echo esc_attr($count); ?>">
                <?php echo $atts['animation'] === 'yes' ? '0' : esc_html($count); ?>
            </span>
            
            <?php if (!empty($atts['text_after'])) : ?>
                <span class="text-after"><?php echo esc_html($atts['text_after']); ?></span>
            <?php endif; ?>
        </span>
    </div>
    
    <?php if ($atts['animation'] === 'yes') : ?>
    <script>
    (function($) {
        $(document).ready(function() {
            $('.count-number.animated').each(function() {
                var $this = $(this);
                var countTo = parseInt($this.attr('data-count'));
                
                $({ countNum: 0 }).animate({ countNum: countTo }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
        });
    })(jQuery);
    </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}
/**
 * AJAX Handler for Newsletter Subscription
 */
function kilismile_ajax_newsletter_subscription() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['newsletter_nonce'], 'kilismile_newsletter_nonce')) {
        wp_send_json_error(__('Security check failed', 'kilismile'));
    }
    
    $email = sanitize_email($_POST['email']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $interests = isset($_POST['interests']) ? array_map('sanitize_text_field', $_POST['interests']) : array();
    $frequency = isset($_POST['frequency']) ? sanitize_text_field($_POST['frequency']) : 'monthly';
    
    // Validate required fields
    if (empty($email) || empty($first_name) || empty($last_name)) {
        wp_send_json_error(__('Please fill in all required fields.', 'kilismile'));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(__('Please enter a valid email address.', 'kilismile'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
    
    // Check if email already exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE email = %s",
        $email
    ));
    
    if ($existing) {
        // Update existing subscriber
        $result = $wpdb->update(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'location' => $location,
                'interests' => implode(',', $interests),
                'frequency' => $frequency,
                'status' => 'active'
            ),
            array('email' => $email),
            array('%s', '%s', '%s', '%s', '%s', '%s'),
            array('%s')
        );
        
        wp_send_json_success(__('Your subscription has been updated successfully!', 'kilismile'));
    } else {
        // Create new subscriber
        $confirmation_token = wp_generate_password(32, false);
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'location' => $location,
                'interests' => implode(',', $interests),
                'frequency' => $frequency,
                'confirmation_token' => $confirmation_token,
                'subscribe_date' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result) {
            // Send confirmation email
            kilismile_send_confirmation_email($email, $first_name, $confirmation_token);
            wp_send_json_success(__('Thank you for subscribing! Please check your email to confirm your subscription.', 'kilismile'));
        } else {
            wp_send_json_error(__('There was an error processing your subscription. Please try again.', 'kilismile'));
        }
    }
}
add_action('wp_ajax_newsletter_subscription', 'kilismile_ajax_newsletter_subscription');
add_action('wp_ajax_nopriv_newsletter_subscription', 'kilismile_ajax_newsletter_subscription');

/**
 * AJAX Handler for Loading More Newsletters
 */
function kilismile_ajax_load_more_newsletters() {
    $page = intval($_POST['page']);
    $per_page = 6;
    
    $newsletter_args = array(
        'post_type' => 'newsletter',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'paged' => $page
    );
    
    $newsletters = new WP_Query($newsletter_args);
    
    if ($newsletters->have_posts()) {
        ob_start();
        
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
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $page < $newsletters->max_num_pages
        ));
    } else {
        wp_send_json_error(__('No more newsletters found.', 'kilismile'));
    }
}
add_action('wp_ajax_load_more_newsletters', 'kilismile_ajax_load_more_newsletters');
add_action('wp_ajax_nopriv_load_more_newsletters', 'kilismile_ajax_load_more_newsletters');

/**
 * Add Newsletter Admin Menu
 */
function kilismile_newsletter_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=newsletter',
        __('Subscribers', 'kilismile'),
        __('Subscribers', 'kilismile'),
        'manage_options',
        'newsletter-subscribers',
        'kilismile_newsletter_subscribers_page'
    );
}
add_action('admin_menu', 'kilismile_newsletter_admin_menu');

/**
 * Newsletter Subscribers Admin Page
 */
function kilismile_newsletter_subscribers_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_newsletter_subscribers';
    
    // Handle bulk actions
    if (isset($_POST['action']) && $_POST['action'] === 'delete_subscribers' && isset($_POST['subscriber_ids'])) {
        $ids = array_map('intval', $_POST['subscriber_ids']);
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $ids));
        echo '<div class="notice notice-success"><p>' . __('Selected subscribers have been deleted.', 'kilismile') . '</p></div>';
    }
    
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY subscribe_date DESC");
    ?>
    <div class="wrap">
        <h1><?php _e('Newsletter Subscribers', 'kilismile'); ?></h1>
        
        <div class="subscriber-stats">
            <div class="stats-boxes">
                <div class="stat-box">
                    <h3><?php echo kilismile_get_subscriber_count(); ?></h3>
                    <p><?php _e('Active Subscribers', 'kilismile'); ?></p>
                </div>
                <div class="stat-box">
                    <h3><?php echo count(array_filter($subscribers, function($s) { return $s->confirmed == 1; })); ?></h3>
                    <p><?php _e('Confirmed Subscribers', 'kilismile'); ?></p>
                </div>
                <div class="stat-box">
                    <h3><?php echo count(array_filter($subscribers, function($s) { return $s->status === 'unsubscribed'; })); ?></h3>
                    <p><?php _e('Unsubscribed', 'kilismile'); ?></p>
                </div>
            </div>
        </div>
        
        <form method="post" action="">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <select name="action">
                        <option value=""><?php _e('Bulk Actions', 'kilismile'); ?></option>
                        <option value="delete_subscribers"><?php _e('Delete', 'kilismile'); ?></option>
                    </select>
                    <input type="submit" class="button action" value="<?php _e('Apply', 'kilismile'); ?>">
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" />
                        </td>
                        <th><?php _e('Name', 'kilismile'); ?></th>
                        <th><?php _e('Email', 'kilismile'); ?></th>
                        <th><?php _e('Location', 'kilismile'); ?></th>
                        <th><?php _e('Interests', 'kilismile'); ?></th>
                        <th><?php _e('Frequency', 'kilismile'); ?></th>
                        <th><?php _e('Status', 'kilismile'); ?></th>
                        <th><?php _e('Subscribe Date', 'kilismile'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $subscriber) : ?>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" name="subscriber_ids[]" value="<?php echo $subscriber->id; ?>" />
                            </th>
                            <td><?php echo esc_html($subscriber->first_name . ' ' . $subscriber->last_name); ?></td>
                            <td><?php echo esc_html($subscriber->email); ?></td>
                            <td><?php echo esc_html($subscriber->location); ?></td>
                            <td><?php echo esc_html($subscriber->interests); ?></td>
                            <td><?php echo esc_html($subscriber->frequency); ?></td>
                            <td>
                                <span class="status-<?php echo esc_attr($subscriber->status); ?>">
                                    <?php echo esc_html($subscriber->status); ?>
                                    <?php if (!$subscriber->confirmed && $subscriber->status === 'active') : ?>
                                        (<?php _e('Unconfirmed', 'kilismile'); ?>)
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($subscriber->subscribe_date); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        
        <style>
        .subscriber-stats {
            margin: 20px 0;
        }
        .stats-boxes {
            display: flex;
            gap: 20px;
        }
        .stat-box {
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            min-width: 150px;
        }
        .stat-box h3 {
            font-size: 24px;
            margin: 0 0 5px 0;
            color: #23282d;
        }
        .stat-box p {
            margin: 0;
            color: #666;
        }
        .status-active {
            color: #46b450;
        }
        .status-unsubscribed {
            color: #dc3232;
        }
        </style>
    </div>
    <?php
}
?>


