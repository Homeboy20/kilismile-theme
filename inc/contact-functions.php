<?php
/**
 * Contact Form Handler with Enhanced Email System
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Contact Form Submission
 */
function kilismile_handle_contact_form() {
    if (isset($_POST['submit_contact']) && wp_verify_nonce($_POST['contact_nonce'], 'kilismile_contact_nonce')) {
        
        $form_data = array(
            'name' => sanitize_text_field($_POST['contact_name']),
            'email' => sanitize_email($_POST['contact_email']),
            'phone' => sanitize_text_field($_POST['contact_phone']),
            'subject' => sanitize_text_field($_POST['contact_subject']),
            'message' => sanitize_textarea_field($_POST['contact_message']),
            'organization' => sanitize_text_field($_POST['contact_organization']),
            'interest' => sanitize_text_field($_POST['contact_interest'])
        );
        
        // Validate required fields
        if (empty($form_data['name']) || empty($form_data['email']) || empty($form_data['subject']) || empty($form_data['message'])) {
            wp_redirect(add_query_arg('contact_error', 'missing_fields', wp_get_referer()));
            exit;
        }
        
        if (!is_email($form_data['email'])) {
            wp_redirect(add_query_arg('contact_error', 'invalid_email', wp_get_referer()));
            exit;
        }
        
        // Store contact form submission in database
        $submission_id = kilismile_store_contact_submission($form_data);
        
        // Send notification email using the enhanced email system
        if (function_exists('kilismile_send_contact_form')) {
            $email_sent = kilismile_send_contact_form($form_data);
        } else {
            // Fallback to basic email
            $email_sent = kilismile_send_basic_contact_notification($form_data);
        }
        
        // Send auto-reply to the sender
        kilismile_send_contact_auto_reply($form_data);
        
        if ($email_sent) {
            error_log(sprintf(
                'KiliSmile Contact Form: email sent to %s (submission_id=%s, from=%s)',
                'contact@kilismile.org',
                $submission_id ? $submission_id : 'n/a',
                $form_data['email']
            ));
            wp_redirect(add_query_arg('contact_success', '1', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('contact_error', 'email_failed', wp_get_referer()));
        }
        exit;
    }
}
add_action('wp', 'kilismile_handle_contact_form');

/**
 * Store Contact Form Submission in Database
 */
function kilismile_store_contact_submission($form_data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    // Create table if it doesn't exist
    kilismile_create_contact_table();
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $form_data['name'],
            'email' => $form_data['email'],
            'phone' => $form_data['phone'],
            'subject' => $form_data['subject'],
            'message' => $form_data['message'],
            'organization' => $form_data['organization'],
            'interest' => $form_data['interest'],
            'ip_address' => kilismile_get_user_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'submitted_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    return $result ? $wpdb->insert_id : false;
}

/**
 * Create Contact Submissions Table
 */
function kilismile_create_contact_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50),
        subject varchar(500) NOT NULL,
        message text NOT NULL,
        organization varchar(255),
        interest varchar(100),
        ip_address varchar(45),
        user_agent varchar(500),
        status varchar(50) DEFAULT 'new',
        replied tinyint(1) DEFAULT 0,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        INDEX email_idx (email),
        INDEX status_idx (status),
        INDEX submitted_at_idx (submitted_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Send Basic Contact Notification (Fallback)
 */
function kilismile_send_basic_contact_notification($form_data) {
    $admin_email = 'contact@kilismile.org';
    $site_name = get_bloginfo('name');
    
    $subject = sprintf(__('[%s] New Contact Form Submission: %s', 'kilismile'), $site_name, $form_data['subject']);
    
    $message = sprintf(
        __('You have received a new contact form submission from your website.

Name: %s
Email: %s
Phone: %s
Organization: %s
Interest: %s
Subject: %s

Message:
%s

--
Submitted from: %s
Date: %s
IP Address: %s', 'kilismile'),
        $form_data['name'],
        $form_data['email'],
        $form_data['phone'],
        $form_data['organization'],
        $form_data['interest'],
        $form_data['subject'],
        $form_data['message'],
        home_url(),
        current_time('F j, Y g:i A'),
        kilismile_get_user_ip()
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $site_name . ' <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>',
        'Reply-To: ' . $form_data['email']
    );
    
    return wp_mail($admin_email, $subject, $message, $headers);
}

/**
 * Send Auto-Reply to Contact Form Submitter
 */
function kilismile_send_contact_auto_reply($form_data) {
    $subject = sprintf(__('Thank you for contacting %s', 'kilismile'), get_bloginfo('name'));
    
    $message = sprintf(
        __('Hello %s,

Thank you for contacting Kilismile Organization. We have received your message and will respond as soon as possible.

Your message:
Subject: %s
%s

We typically respond within 24-48 hours during business days.

Best regards,
The Kilismile Team

--
Kilismile Organization
"No health without oral health"
Website: %s
Email: %s
Phone: %s', 'kilismile'),
        $form_data['name'],
        $form_data['subject'],
        $form_data['message'],
        home_url(),
        get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
        get_theme_mod('kilismile_phone', '+255763495575/+255735495575')
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: Kilismile Organization <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>'
    );
    
    return wp_mail($form_data['email'], $subject, $message, $headers);
}

/**
 * Get User IP Address
 */
function kilismile_get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

/**
 * Contact Form Shortcode
 */
function kilismile_contact_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Contact Us', 'kilismile'),
        'show_info' => 'yes',
        'columns' => '2',
        'style' => 'default'
    ), $atts);
    
    ob_start();
    ?>
    <div class="kilismile-contact-form-container">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="contact-form-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>
        
        <?php if (isset($_GET['contact_success'])) : ?>
            <div class="contact-message success">
                <i class="fas fa-check-circle"></i>
                <p><?php _e('Thank you for your message! We will get back to you soon.', 'kilismile'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['contact_error'])) : ?>
            <div class="contact-message error">
                <i class="fas fa-exclamation-circle"></i>
                <p>
                    <?php
                    switch ($_GET['contact_error']) {
                        case 'missing_fields':
                            _e('Please fill in all required fields.', 'kilismile');
                            break;
                        case 'invalid_email':
                            _e('Please enter a valid email address.', 'kilismile');
                            break;
                        case 'email_failed':
                            _e('There was an error sending your message. Please try again.', 'kilismile');
                            break;
                        default:
                            _e('An error occurred. Please try again.', 'kilismile');
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="contact-form-wrapper columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php if ($atts['show_info'] === 'yes') : ?>
                <div class="contact-info">
                    <h3><?php _e('Get in Touch', 'kilismile'); ?></h3>
                    <p><?php _e('We would love to hear from you. Send us a message and we will respond as soon as possible.', 'kilismile'); ?></p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong><?php _e('Email:', 'kilismile'); ?></strong>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('kilismile_email', 'kilismile21@gmail.com')); ?>">
                                    <?php echo esc_html(get_theme_mod('kilismile_email', 'kilismile21@gmail.com')); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong><?php _e('Phone:', 'kilismile'); ?></strong>
                                <a href="tel:<?php echo esc_attr(str_replace(array('/', ' '), '', get_theme_mod('kilismile_phone', '0763495575'))); ?>">
                                    <?php echo esc_html(get_theme_mod('kilismile_phone', '+255763495575/+255735495575')); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong><?php _e('Address:', 'kilismile'); ?></strong>
                                <address><?php echo nl2br(esc_html(get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania'))); ?></address>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="contact-form">
                <form method="post" action="" id="kilismile-contact-form" novalidate>
                    <?php wp_nonce_field('kilismile_contact_nonce', 'contact_nonce'); ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_name"><?php _e('Full Name', 'kilismile'); ?> *</label>
                            <input type="text" id="contact_name" name="contact_name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_email"><?php _e('Email Address', 'kilismile'); ?> *</label>
                            <input type="email" id="contact_email" name="contact_email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_phone"><?php _e('Phone Number', 'kilismile'); ?></label>
                            <input type="tel" id="contact_phone" name="contact_phone">
                        </div>
                        <div class="form-group">
                            <label for="contact_organization"><?php _e('Organization (Optional)', 'kilismile'); ?></label>
                            <input type="text" id="contact_organization" name="contact_organization">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_interest"><?php _e('Interest Area', 'kilismile'); ?></label>
                            <select id="contact_interest" name="contact_interest">
                                <option value=""><?php _e('Select an option...', 'kilismile'); ?></option>
                                <option value="volunteer"><?php _e('Volunteering Opportunities', 'kilismile'); ?></option>
                                <option value="partnership"><?php _e('Partnership & Collaboration', 'kilismile'); ?></option>
                                <option value="donation"><?php _e('Donations & Fundraising', 'kilismile'); ?></option>
                                <option value="programs"><?php _e('Our Programs & Services', 'kilismile'); ?></option>
                                <option value="media"><?php _e('Media & Press Inquiries', 'kilismile'); ?></option>
                                <option value="general"><?php _e('General Information', 'kilismile'); ?></option>
                                <option value="other"><?php _e('Other', 'kilismile'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contact_subject"><?php _e('Subject', 'kilismile'); ?> *</label>
                            <input type="text" id="contact_subject" name="contact_subject" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_message"><?php _e('Message', 'kilismile'); ?> *</label>
                        <textarea id="contact_message" name="contact_message" rows="6" required 
                                  placeholder="<?php _e('Please share your message, questions, or how we can help you...', 'kilismile'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group consent-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="contact_consent" value="yes" required>
                            <span class="checkmark"></span>
                            <?php _e('I agree to the privacy policy and consent to being contacted by Kilismile Organization regarding my inquiry.', 'kilismile'); ?>
                        </label>
                    </div>
                    
                    <button type="submit" name="submit_contact" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        <?php _e('Send Message', 'kilismile'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .kilismile-contact-form-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .contact-form-title {
        text-align: center;
        color: var(--dark-green);
        margin-bottom: 30px;
    }
    
    .contact-message {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .contact-message.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .contact-message.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .contact-message i {
        font-size: 1.2rem;
    }
    
    .contact-message p {
        margin: 0;
    }
    
    .contact-form-wrapper {
        display: grid;
        gap: 40px;
        margin-top: 30px;
    }
    
    .contact-form-wrapper.columns-2 {
        grid-template-columns: 1fr 1fr;
    }
    
    .contact-info h3 {
        color: var(--dark-green);
        margin-bottom: 15px;
    }
    
    .contact-details {
        margin-top: 30px;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .contact-item i {
        color: var(--primary-green);
        font-size: 1.2rem;
        margin-top: 2px;
        min-width: 20px;
    }
    
    .contact-item strong {
        display: block;
        color: var(--dark-green);
        margin-bottom: 5px;
    }
    
    .contact-item a {
        color: var(--primary-green);
        text-decoration: none;
    }
    
    .contact-item a:hover {
        text-decoration: underline;
    }
    
    .contact-item address {
        font-style: normal;
        line-height: 1.5;
    }
    
    .contact-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .contact-form .form-group {
        margin-bottom: 20px;
    }
    
    .contact-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--dark-green);
    }
    
    .contact-form input,
    .contact-form select,
    .contact-form textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    
    .contact-form input:focus,
    .contact-form select:focus,
    .contact-form textarea:focus {
        outline: none;
        border-color: var(--primary-green);
    }
    
    .contact-form textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .consent-group {
        margin: 25px 0;
    }
    
    .checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    .checkbox-label input[type="checkbox"] {
        width: auto;
        margin: 0;
    }
    
    .contact-form .btn {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .contact-form .btn:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }
    
    @media (max-width: 768px) {
        .contact-form-wrapper.columns-2 {
            grid-template-columns: 1fr;
        }
        
        .contact-form .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('kilismile-contact-form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            // Basic client-side validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3232';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e0e0e0';
                }
            });
            
            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailField.style.borderColor = '#dc3232';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('<?php _e('Please fill in all required fields correctly.', 'kilismile'); ?>');
                return;
            }
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e('Sending...', 'kilismile'); ?>';
            submitBtn.disabled = true;
        });
        
        // Remove error styling on input
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                if (this.style.borderColor === 'rgb(220, 50, 50)') {
                    this.style.borderColor = '#e0e0e0';
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('kilismile_contact_form', 'kilismile_contact_form_shortcode');

/**
 * Add Contact Form Admin Menu
 */
function kilismile_contact_admin_menu() {
    add_submenu_page(
        'edit.php',
        __('Contact Submissions', 'kilismile'),
        __('Contact Forms', 'kilismile'),
        'manage_options',
        'kilismile-contact-submissions',
        'kilismile_contact_submissions_page'
    );
}
add_action('admin_menu', 'kilismile_contact_admin_menu');

/**
 * Contact Submissions Admin Page
 */
function kilismile_contact_submissions_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    // Handle status updates
    if (isset($_POST['action']) && $_POST['action'] === 'update_status' && isset($_POST['submission_id'])) {
        $submission_id = intval($_POST['submission_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        $replied = isset($_POST['replied']) ? 1 : 0;
        
        $wpdb->update(
            $table_name,
            array('status' => $new_status, 'replied' => $replied),
            array('id' => $submission_id),
            array('%s', '%d'),
            array('%d')
        );
        
        echo '<div class="notice notice-success"><p>' . __('Status updated successfully.', 'kilismile') . '</p></div>';
    }
    
    // Get submissions
    $per_page = 20;
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($page - 1) * $per_page;
    
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $submissions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));
    
    include get_template_directory() . '/admin/contact-submissions-page.php';
}

// Create table on activation
add_action('after_switch_theme', 'kilismile_create_contact_table');

?>


