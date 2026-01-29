<?php
/**
 * KiliSmile Email System
 * 
 * Comprehensive email handling for contact forms, newsletters, and notifications
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Prevent class redeclaration
if (class_exists('KiliSmile_Email_System')) {
    return;
}

class KiliSmile_Email_System {
    
    private $from_email;
    private $from_name;
    private $admin_email;
    
    public function __construct() {
        $this->from_email = get_option('admin_email');
        $this->from_name = get_bloginfo('name');
        $this->admin_email = get_option('admin_email');
        
        $this->init_hooks();
        $this->create_email_tables();
    }
    
    private function init_hooks() {
        // AJAX handlers for forms
        add_action('wp_ajax_kilismile_contact_form', array($this, 'handle_contact_form'));
        add_action('wp_ajax_nopriv_kilismile_contact_form', array($this, 'handle_contact_form'));
        
        add_action('wp_ajax_kilismile_newsletter_signup', array($this, 'handle_newsletter_signup'));
        add_action('wp_ajax_nopriv_kilismile_newsletter_signup', array($this, 'handle_newsletter_signup'));
        
        add_action('wp_ajax_kilismile_volunteer_form', array($this, 'handle_volunteer_form'));
        add_action('wp_ajax_nopriv_kilismile_volunteer_form', array($this, 'handle_volunteer_form'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menus'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_email_scripts'));
        
        // Email settings
        add_action('phpmailer_init', array($this, 'configure_phpmailer'));
    }
    
    /**
     * Create database tables for email system
     */
    private function create_email_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Contact messages table
        $contact_table = $wpdb->prefix . 'kilismile_contacts';
        $contact_sql = "CREATE TABLE $contact_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            subject varchar(200) NOT NULL,
            message text NOT NULL,
            phone varchar(20),
            organization varchar(100),
            message_type varchar(50) DEFAULT 'general',
            status varchar(20) DEFAULT 'unread',
            ip_address varchar(45),
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            responded_at datetime NULL,
            responded_by bigint(20) NULL,
            PRIMARY KEY (id),
            KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Newsletter subscribers table
        $newsletter_table = $wpdb->prefix . 'kilismile_newsletter';
        // NOTE: Removed duplicate UNIQUE constraint on email (had both inline UNIQUE and separate UNIQUE KEY)
        $newsletter_sql = "CREATE TABLE $newsletter_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            name varchar(100),
            status varchar(20) DEFAULT 'active',
            interests text,
            source varchar(50) DEFAULT 'website',
            confirmed_at datetime NULL,
            unsubscribed_at datetime NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status)
        ) $charset_collate;";
        
        // Email logs table
        $logs_table = $wpdb->prefix . 'kilismile_email_logs';
        $logs_sql = "CREATE TABLE $logs_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email_type varchar(50) NOT NULL,
            recipient_email varchar(100) NOT NULL,
            subject varchar(200) NOT NULL,
            status varchar(20) DEFAULT 'sent',
            error_message text NULL,
            sent_at datetime DEFAULT CURRENT_TIMESTAMP,
            related_id mediumint(9) NULL,
            PRIMARY KEY (id),
            KEY email_type (email_type),
            KEY recipient_email (recipient_email),
            KEY sent_at (sent_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($contact_sql);
        dbDelta($newsletter_sql);
        dbDelta($logs_sql);
    }
    
    /**
     * Handle contact form submissions
     */
    public function handle_contact_form() {
        check_ajax_referer('kilismile_email_nonce', 'nonce');
        
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $organization = sanitize_text_field($_POST['organization'] ?? '');
        $message_type = sanitize_text_field($_POST['message_type'] ?? 'general');
        
        // Validation
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            wp_send_json_error('Please fill in all required fields.');
        }
        
        if (!is_email($email)) {
            wp_send_json_error('Please enter a valid email address.');
        }
        
        // Save to database
        global $wpdb;
        $contact_table = $wpdb->prefix . 'kilismile_contacts';
        
        $result = $wpdb->insert($contact_table, array(
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'phone' => $phone,
            'organization' => $organization,
            'message_type' => $message_type,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ));
        
        if ($result === false) {
            wp_send_json_error('Failed to save your message. Please try again.');
        }
        
        $contact_id = $wpdb->insert_id;
        
        // Send notification email to admin
        $this->send_contact_notification($contact_id, array(
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'phone' => $phone,
            'organization' => $organization,
            'message_type' => $message_type
        ));
        
        // Send confirmation email to user
        $this->send_contact_confirmation($email, $name, $subject);
        
        wp_send_json_success('Thank you for your message! We will get back to you soon.');
    }
    
    /**
     * Handle newsletter signup
     */
    public function handle_newsletter_signup() {
        check_ajax_referer('kilismile_email_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $interests = sanitize_text_field($_POST['interests'] ?? '');
        
        if (!is_email($email)) {
            wp_send_json_error('Please enter a valid email address.');
        }
        
        global $wpdb;
        $newsletter_table = $wpdb->prefix . 'kilismile_newsletter';
        
        // Check if already subscribed
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $newsletter_table WHERE email = %s", $email
        ));
        
        if ($existing) {
            if ($existing->status === 'active') {
                wp_send_json_error('This email is already subscribed to our newsletter.');
            } else {
                // Reactivate subscription
                $wpdb->update($newsletter_table, 
                    array('status' => 'active', 'unsubscribed_at' => null),
                    array('id' => $existing->id)
                );
                wp_send_json_success('Welcome back! Your newsletter subscription has been reactivated.');
            }
        } else {
            // New subscription
            $result = $wpdb->insert($newsletter_table, array(
                'email' => $email,
                'name' => $name,
                'interests' => $interests,
                'source' => 'website',
                'confirmed_at' => current_time('mysql')
            ));
            
            if ($result === false) {
                wp_send_json_error('Failed to subscribe. Please try again.');
            }
        }
        
        // Send welcome email
        $this->send_newsletter_welcome($email, $name);
        
        wp_send_json_success('Thank you for subscribing to our newsletter!');
    }
    
    /**
     * Handle volunteer form submissions
     */
    public function handle_volunteer_form() {
        check_ajax_referer('kilismile_email_nonce', 'nonce');
        
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $skills = sanitize_textarea_field($_POST['skills']);
        $availability = sanitize_text_field($_POST['availability']);
        $experience = sanitize_textarea_field($_POST['experience']);
        
        if (empty($name) || empty($email) || empty($phone)) {
            wp_send_json_error('Please fill in all required fields.');
        }
        
        // Save as contact with volunteer type
        global $wpdb;
        $contact_table = $wpdb->prefix . 'kilismile_contacts';
        
        $message = "Volunteer Application\n\n";
        $message .= "Skills: $skills\n";
        $message .= "Availability: $availability\n";
        $message .= "Experience: $experience\n";
        
        $result = $wpdb->insert($contact_table, array(
            'name' => $name,
            'email' => $email,
            'subject' => 'Volunteer Application',
            'message' => $message,
            'phone' => $phone,
            'message_type' => 'volunteer',
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ));
        
        if ($result === false) {
            wp_send_json_error('Failed to submit application. Please try again.');
        }
        
        // Send notification to admin
        $this->send_volunteer_notification($name, $email, $phone, $skills, $availability, $experience);
        
        // Send confirmation to volunteer
        $this->send_volunteer_confirmation($email, $name);
        
        wp_send_json_success('Thank you for your volunteer application! We will review it and get back to you soon.');
    }
    
    /**
     * Send contact form notification to admin
     */
    private function send_contact_notification($contact_id, $data) {
        $subject = 'New Contact Form Submission - ' . $data['subject'];
        
        $message = "You have received a new contact form submission:\n\n";
        $message .= "Name: {$data['name']}\n";
        $message .= "Email: {$data['email']}\n";
        $message .= "Phone: {$data['phone']}\n";
        $message .= "Organization: {$data['organization']}\n";
        $message .= "Type: {$data['message_type']}\n";
        $message .= "Subject: {$data['subject']}\n\n";
        $message .= "Message:\n{$data['message']}\n\n";
        $message .= "---\n";
        $message .= "Reply to this message directly at: {$data['email']}\n";
        $message .= "View in admin: " . admin_url('admin.php?page=kilismile-contacts&contact_id=' . $contact_id);
        
        $this->send_email($this->admin_email, $subject, $message, 'contact_notification', $contact_id);
    }
    
    /**
     * Send contact confirmation to user
     */
    private function send_contact_confirmation($email, $name, $subject) {
        $email_subject = 'Thank you for contacting KiliSmile - ' . $subject;
        
        $message = "Dear $name,\n\n";
        $message .= "Thank you for reaching out to KiliSmile Organization. We have received your message regarding: \"$subject\"\n\n";
        $message .= "Our team will review your message and respond within 24-48 hours. If your matter is urgent, please call us directly.\n\n";
        $message .= "Best regards,\n";
        $message .= "KiliSmile Team\n\n";
        $message .= "---\n";
        $message .= "KiliSmile Organization\n";
        $message .= "Improving oral health and tackling NCDs in underserved communities\n";
        $message .= "Website: " . home_url();
        
        $this->send_email($email, $email_subject, $message, 'contact_confirmation');
    }
    
    /**
     * Send newsletter welcome email
     */
    private function send_newsletter_welcome($email, $name) {
        $subject = 'Welcome to KiliSmile Newsletter!';
        
        $message = "Dear " . ($name ?: "Friend") . ",\n\n";
        $message .= "Welcome to the KiliSmile family! Thank you for subscribing to our newsletter.\n\n";
        $message .= "You'll now receive regular updates about:\n";
        $message .= "• Our health education programs and initiatives\n";
        $message .= "• Community impact stories and success stories\n";
        $message .= "• Upcoming events and volunteer opportunities\n";
        $message .= "• Health tips and educational resources\n\n";
        $message .= "We're committed to improving oral health and tackling non-communicable diseases in underserved communities, and we're excited to have you join us on this journey.\n\n";
        $message .= "Best regards,\n";
        $message .= "The KiliSmile Team\n\n";
        $message .= "---\n";
        $message .= "You can unsubscribe at any time by visiting: " . home_url('/unsubscribe');
        
        $this->send_email($email, $subject, $message, 'newsletter_welcome');
    }
    
    /**
     * Send volunteer application notification
     */
    private function send_volunteer_notification($name, $email, $phone, $skills, $availability, $experience) {
        $subject = 'New Volunteer Application - ' . $name;
        
        $message = "You have received a new volunteer application:\n\n";
        $message .= "Name: $name\n";
        $message .= "Email: $email\n";
        $message .= "Phone: $phone\n\n";
        $message .= "Skills & Interests:\n$skills\n\n";
        $message .= "Availability:\n$availability\n\n";
        $message .= "Previous Experience:\n$experience\n\n";
        $message .= "---\n";
        $message .= "Please review this application and reach out to the volunteer directly.";
        
        $this->send_email($this->admin_email, $subject, $message, 'volunteer_notification');
    }
    
    /**
     * Send volunteer confirmation
     */
    private function send_volunteer_confirmation($email, $name) {
        $subject = 'Thank you for your volunteer application - KiliSmile';
        
        $message = "Dear $name,\n\n";
        $message .= "Thank you for your interest in volunteering with KiliSmile Organization!\n\n";
        $message .= "We have received your application and our volunteer coordinator will review it carefully. We will contact you within the next week to discuss potential opportunities that match your skills and availability.\n\n";
        $message .= "In the meantime, feel free to explore our website to learn more about our current programs and initiatives.\n\n";
        $message .= "We appreciate your commitment to improving health outcomes in our communities.\n\n";
        $message .= "Best regards,\n";
        $message .= "KiliSmile Volunteer Team\n\n";
        $message .= "---\n";
        $message .= "KiliSmile Organization\n";
        $message .= "Building healthier generations, one smile at a time";
        
        $this->send_email($email, $subject, $message, 'volunteer_confirmation');
    }
    
    /**
     * Core email sending function
     */
    private function send_email($to, $subject, $message, $type = 'general', $related_id = null) {
        $headers = array(
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'Content-Type: text/plain; charset=UTF-8'
        );
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        // Log email
        $this->log_email($type, $to, $subject, $sent ? 'sent' : 'failed', $related_id);
        
        return $sent;
    }
    
    /**
     * Log email activity
     */
    private function log_email($type, $recipient, $subject, $status, $related_id = null) {
        global $wpdb;
        
        $logs_table = $wpdb->prefix . 'kilismile_email_logs';
        
        $wpdb->insert($logs_table, array(
            'email_type' => $type,
            'recipient_email' => $recipient,
            'subject' => $subject,
            'status' => $status,
            'related_id' => $related_id
        ));
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_fields = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_fields as $field) {
            if (!empty($_SERVER[$field])) {
                $ip = $_SERVER[$field];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Configure PHPMailer settings
     */
    public function configure_phpmailer($phpmailer) {
        // You can configure SMTP settings here if needed
        // Example for Gmail SMTP:
        /*
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.gmail.com';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 587;
        $phpmailer->Username = 'your-email@gmail.com';
        $phpmailer->Password = 'your-app-password';
        $phpmailer->SMTPSecure = 'tls';
        */
    }
    
    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        add_menu_page(
            'Email Management',
            'Email System',
            'manage_options',
            'kilismile-emails',
            array($this, 'admin_email_dashboard'),
            'dashicons-email-alt',
            30
        );
        
        add_submenu_page(
            'kilismile-emails',
            'Contact Messages',
            'Contact Messages',
            'manage_options',
            'kilismile-contacts',
            array($this, 'admin_contacts_page')
        );
        
        add_submenu_page(
            'kilismile-emails',
            'Newsletter Subscribers',
            'Newsletter',
            'manage_options',
            'kilismile-newsletter',
            array($this, 'admin_newsletter_page')
        );
        
        add_submenu_page(
            'kilismile-emails',
            'Email Logs',
            'Email Logs',
            'manage_options',
            'kilismile-email-logs',
            array($this, 'admin_email_logs_page')
        );
    }
    
    /**
     * Email dashboard admin page
     */
    public function admin_email_dashboard() {
        global $wpdb;
        
        $contacts_table = $wpdb->prefix . 'kilismile_contacts';
        $newsletter_table = $wpdb->prefix . 'kilismile_newsletter';
        $logs_table = $wpdb->prefix . 'kilismile_email_logs';
        
        $stats = array(
            'total_contacts' => $wpdb->get_var("SELECT COUNT(*) FROM $contacts_table"),
            'unread_contacts' => $wpdb->get_var("SELECT COUNT(*) FROM $contacts_table WHERE status = 'unread'"),
            'newsletter_subscribers' => $wpdb->get_var("SELECT COUNT(*) FROM $newsletter_table WHERE status = 'active'"),
            'emails_sent_today' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $logs_table WHERE DATE(sent_at) = %s AND status = 'sent'",
                current_time('Y-m-d')
            ))
        );
        
        echo '<div class="wrap">
            <h1>Email System Dashboard</h1>
            <div class="email-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="stat-box" style="background: white; padding: 20px; border-left: 4px solid #4CAF50; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0; color: #333;">Total Contacts</h3>
                    <p style="font-size: 2rem; margin: 10px 0; color: #4CAF50;">' . $stats['total_contacts'] . '</p>
                </div>
                <div class="stat-box" style="background: white; padding: 20px; border-left: 4px solid #ff9800; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0; color: #333;">Unread Messages</h3>
                    <p style="font-size: 2rem; margin: 10px 0; color: #ff9800;">' . $stats['unread_contacts'] . '</p>
                </div>
                <div class="stat-box" style="background: white; padding: 20px; border-left: 4px solid #2196F3; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0; color: #333;">Newsletter Subscribers</h3>
                    <p style="font-size: 2rem; margin: 10px 0; color: #2196F3;">' . $stats['newsletter_subscribers'] . '</p>
                </div>
                <div class="stat-box" style="background: white; padding: 20px; border-left: 4px solid #9C27B0; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0; color: #333;">Emails Sent Today</h3>
                    <p style="font-size: 2rem; margin: 10px 0; color: #9C27B0;">' . $stats['emails_sent_today'] . '</p>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Contacts admin page
     */
    public function admin_contacts_page() {
        global $wpdb;
        
        $contacts_table = $wpdb->prefix . 'kilismile_contacts';
        
        // Handle actions
        if (isset($_GET['action']) && isset($_GET['contact_id'])) {
            $contact_id = intval($_GET['contact_id']);
            
            if ($_GET['action'] === 'mark_read') {
                $wpdb->update($contacts_table, 
                    array('status' => 'read'), 
                    array('id' => $contact_id)
                );
            } elseif ($_GET['action'] === 'delete') {
                $wpdb->delete($contacts_table, array('id' => $contact_id));
            }
        }
        
        // Get contacts
        $contacts = $wpdb->get_results("SELECT * FROM $contacts_table ORDER BY created_at DESC LIMIT 50");
        
        echo '<div class="wrap">
            <h1>Contact Messages</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($contacts as $contact) {
            $status_badge = $contact->status === 'unread' ? '<span style="background: #ff9800; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Unread</span>' : '<span style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Read</span>';
            
            echo '<tr>
                <td>' . esc_html($contact->name) . '</td>
                <td><a href="mailto:' . esc_attr($contact->email) . '">' . esc_html($contact->email) . '</a></td>
                <td>' . esc_html($contact->subject) . '</td>
                <td>' . esc_html($contact->message_type) . '</td>
                <td>' . date('M j, Y g:i A', strtotime($contact->created_at)) . '</td>
                <td>' . $status_badge . '</td>
                <td>
                    <a href="?page=kilismile-contacts&action=mark_read&contact_id=' . $contact->id . '" class="button button-small">Mark Read</a>
                    <a href="?page=kilismile-contacts&action=delete&contact_id=' . $contact->id . '" class="button button-small" onclick="return confirm(\'Delete this message?\')">Delete</a>
                </td>
            </tr>';
        }
        
        echo '</tbody></table></div>';
    }
    
    /**
     * Newsletter admin page
     */
    public function admin_newsletter_page() {
        global $wpdb;
        
        $newsletter_table = $wpdb->prefix . 'kilismile_newsletter';
        
        // Get subscribers
        $subscribers = $wpdb->get_results("SELECT * FROM $newsletter_table ORDER BY created_at DESC LIMIT 100");
        
        echo '<div class="wrap">
            <h1>Newsletter Subscribers</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Interests</th>
                        <th>Source</th>
                        <th>Subscribed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($subscribers as $subscriber) {
            $status_badge = $subscriber->status === 'active' ? '<span style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Active</span>' : '<span style="background: #ccc; color: #666; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Inactive</span>';
            
            echo '<tr>
                <td>' . esc_html($subscriber->email) . '</td>
                <td>' . esc_html($subscriber->name ?: '—') . '</td>
                <td>' . esc_html($subscriber->interests ?: '—') . '</td>
                <td>' . esc_html($subscriber->source) . '</td>
                <td>' . date('M j, Y', strtotime($subscriber->created_at)) . '</td>
                <td>' . $status_badge . '</td>
            </tr>';
        }
        
        echo '</tbody></table></div>';
    }
    
    /**
     * Email logs admin page
     */
    public function admin_email_logs_page() {
        global $wpdb;
        
        $logs_table = $wpdb->prefix . 'kilismile_email_logs';
        
        $logs = $wpdb->get_results("SELECT * FROM $logs_table ORDER BY sent_at DESC LIMIT 100");
        
        echo '<div class="wrap">
            <h1>Email Logs</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Sent</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($logs as $log) {
            $status_badge = $log->status === 'sent' ? '<span style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Sent</span>' : '<span style="background: #f44336; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.8em;">Failed</span>';
            
            echo '<tr>
                <td>' . esc_html($log->email_type) . '</td>
                <td>' . esc_html($log->recipient_email) . '</td>
                <td>' . esc_html($log->subject) . '</td>
                <td>' . $status_badge . '</td>
                <td>' . date('M j, Y g:i A', strtotime($log->sent_at)) . '</td>
            </tr>';
        }
        
        echo '</tbody></table></div>';
    }
    
    /**
     * Enqueue email system scripts
     */
    public function enqueue_email_scripts() {
        wp_enqueue_script('kilismile-email-forms', get_template_directory_uri() . '/assets/js/email-forms.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('kilismile-email-forms', 'kilismileEmail', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kilismile_email_nonce'),
            'strings' => array(
                'sending' => __('Sending...', 'kilismile'),
                'success' => __('Message sent successfully!', 'kilismile'),
                'error' => __('Error sending message. Please try again.', 'kilismile')
            )
        ));
    }
}

// Initialize the email system
new KiliSmile_Email_System();

/**
 * Helper functions for templates
 */

if (!function_exists('kilismile_contact_form')) {
function kilismile_contact_form($args = array()) {
    $defaults = array(
        'show_phone' => true,
        'show_organization' => true,
        'show_message_type' => true,
        'class' => 'kilismile-contact-form',
        'submit_text' => 'Send Message'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    ob_start();
    ?>
    <form class="<?php echo esc_attr($args['class']); ?>" data-form-type="contact">
        <div class="form-row">
            <div class="form-group">
                <label for="contact-name">Name *</label>
                <input type="text" id="contact-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="contact-email">Email *</label>
                <input type="email" id="contact-email" name="email" required>
            </div>
        </div>
        
        <?php if ($args['show_phone']): ?>
        <div class="form-row">
            <div class="form-group">
                <label for="contact-phone">Phone</label>
                <input type="tel" id="contact-phone" name="phone">
            </div>
            <?php if ($args['show_organization']): ?>
            <div class="form-group">
                <label for="contact-organization">Organization</label>
                <input type="text" id="contact-organization" name="organization">
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="contact-subject">Subject *</label>
            <input type="text" id="contact-subject" name="subject" required>
        </div>
        
        <?php if ($args['show_message_type']): ?>
        <div class="form-group">
            <label for="contact-type">Message Type</label>
            <select id="contact-type" name="message_type">
                <option value="general">General Inquiry</option>
                <option value="partnership">Partnership</option>
                <option value="volunteer">Volunteer</option>
                <option value="donation">Donation</option>
                <option value="media">Media Inquiry</option>
                <option value="support">Support</option>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="contact-message">Message *</label>
            <textarea id="contact-message" name="message" rows="5" required></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><?php echo esc_html($args['submit_text']); ?></button>
        </div>
        
        <div class="form-message"></div>
    </form>
    <?php
    return ob_get_clean();
}
}

if (!function_exists('kilismile_newsletter_form')) {
function kilismile_newsletter_form($args = array()) {
    $defaults = array(
        'show_name' => true,
        'show_interests' => false,
        'class' => 'kilismile-newsletter-form',
        'submit_text' => 'Subscribe'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    ob_start();
    ?>
    <form class="<?php echo esc_attr($args['class']); ?>" data-form-type="newsletter">
        <div class="form-row">
            <div class="form-group">
                <label for="newsletter-email">Email Address *</label>
                <input type="email" id="newsletter-email" name="email" required>
            </div>
            <?php if ($args['show_name']): ?>
            <div class="form-group">
                <label for="newsletter-name">Name</label>
                <input type="text" id="newsletter-name" name="name">
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($args['show_interests']): ?>
        <div class="form-group">
            <label for="newsletter-interests">Interests</label>
            <input type="text" id="newsletter-interests" name="interests" placeholder="e.g., health education, community programs">
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><?php echo esc_html($args['submit_text']); ?></button>
        </div>
        
        <div class="form-message"></div>
    </form>
    <?php
    return ob_get_clean();
}
}

if (!function_exists('kilismile_volunteer_form')) {
function kilismile_volunteer_form($args = array()) {
    $defaults = array(
        'class' => 'kilismile-volunteer-form',
        'submit_text' => 'Submit Application'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    ob_start();
    ?>
    <form class="<?php echo esc_attr($args['class']); ?>" data-form-type="volunteer">
        <div class="form-row">
            <div class="form-group">
                <label for="volunteer-name">Full Name *</label>
                <input type="text" id="volunteer-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="volunteer-email">Email *</label>
                <input type="email" id="volunteer-email" name="email" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="volunteer-phone">Phone Number *</label>
            <input type="tel" id="volunteer-phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="volunteer-skills">Skills & Interests</label>
            <textarea id="volunteer-skills" name="skills" rows="3" placeholder="Tell us about your skills, interests, and how you'd like to help..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="volunteer-availability">Availability</label>
            <select id="volunteer-availability" name="availability">
                <option value="weekdays">Weekdays</option>
                <option value="weekends">Weekends</option>
                <option value="both">Both weekdays and weekends</option>
                <option value="flexible">Flexible</option>
                <option value="specific">Specific times (please specify in experience)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="volunteer-experience">Previous Experience</label>
            <textarea id="volunteer-experience" name="experience" rows="3" placeholder="Tell us about any relevant experience or additional information..."></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><?php echo esc_html($args['submit_text']); ?></button>
        </div>
        
        <div class="form-message"></div>
    </form>
    <?php
    return ob_get_clean();
}
}

// Initialize the email system globally
if (!isset($GLOBALS['kilismile_email_system'])) {
    $GLOBALS['kilismile_email_system'] = new KiliSmile_Email_System();
}


