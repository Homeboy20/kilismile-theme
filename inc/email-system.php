<?php
/**
 * Advanced Server-Based Email System for Kili Smile Organization
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Email System Configuration Class
 */
class KiliSmile_Email_System {
    
    private $smtp_config;
    private $templates_path;
    private $log_emails;
    
    public function __construct() {
        $this->templates_path = get_template_directory() . '/email-templates/';
        $this->log_emails = get_option('kilismile_log_emails', true);
        $this->init_hooks();
        $this->setup_smtp_config();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('phpmailer_init', array($this, 'configure_smtp'));
        add_action('wp_mail_failed', array($this, 'log_email_failure'));
        add_filter('wp_mail_from', array($this, 'set_from_email'));
        add_filter('wp_mail_from_name', array($this, 'set_from_name'));
        add_filter('wp_mail_content_type', array($this, 'set_email_content_type'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_email_admin_menu'));
        add_action('admin_init', array($this, 'register_email_settings'));
        add_action('wp_ajax_test_email_configuration', array($this, 'test_email_configuration'));
        add_action('wp_ajax_send_bulk_newsletter', array($this, 'send_bulk_newsletter'));
        
        // Customizer hooks
        add_action('customize_register', array($this, 'add_email_customizer_settings'));
        
        // Create email templates directory
        $this->ensure_templates_directory();
        
        // Create email log table
        $this->create_email_log_table();
    }
    
    /**
     * Setup SMTP Configuration
     */
    private function setup_smtp_config() {
        $this->smtp_config = array(
            'enabled' => get_option('kilismile_smtp_enabled', false),
            'host' => get_option('kilismile_smtp_host', ''),
            'port' => get_option('kilismile_smtp_port', 587),
            'username' => get_option('kilismile_smtp_username', ''),
            'password' => get_option('kilismile_smtp_password', ''),
            'encryption' => get_option('kilismile_smtp_encryption', 'tls'),
            'auth' => get_option('kilismile_smtp_auth', true),
            'from_email' => get_option('kilismile_from_email', get_theme_mod('kilismile_email', 'kilismile21@gmail.com')),
            'from_name' => get_option('kilismile_from_name', 'Kili Smile Organization'),
        );
    }
    
    /**
     * Configure SMTP for wp_mail
     */
    public function configure_smtp($phpmailer) {
        if (!$this->smtp_config['enabled'] || empty($this->smtp_config['host'])) {
            return;
        }
        
        $phpmailer->isSMTP();
        $phpmailer->Host = $this->smtp_config['host'];
        $phpmailer->SMTPAuth = $this->smtp_config['auth'];
        $phpmailer->Username = $this->smtp_config['username'];
        $phpmailer->Password = $this->smtp_config['password'];
        $phpmailer->SMTPSecure = $this->smtp_config['encryption'];
        $phpmailer->Port = $this->smtp_config['port'];
        
        // Enable SMTP debugging for admin users
        if (current_user_can('manage_options') && isset($_GET['debug_email'])) {
            $phpmailer->SMTPDebug = 2;
            $phpmailer->Debugoutput = 'html';
        }
        
        // Additional SMTP settings
        $phpmailer->Timeout = 30;
        $phpmailer->SMTPKeepAlive = true;
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Encoding = 'base64';
        
        $this->log_email_attempt('SMTP configuration applied');
    }
    
    /**
     * Set from email address
     */
    public function set_from_email($email) {
        return $this->smtp_config['from_email'] ?: $email;
    }
    
    /**
     * Set from name
     */
    public function set_from_name($name) {
        return $this->smtp_config['from_name'] ?: $name;
    }
    
    /**
     * Set email content type to HTML
     */
    public function set_email_content_type($content_type) {
        return 'text/html';
    }
    
    /**
     * Send Enhanced Newsletter Email
     */
    public function send_newsletter_email($to, $subject, $data = array()) {
        $template = $this->load_email_template('newsletter', $data);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->smtp_config['from_name'] . ' <' . $this->smtp_config['from_email'] . '>',
            'Reply-To: ' . $this->smtp_config['from_email'],
            'X-Mailer: Kili Smile Email System v1.0'
        );
        
        $result = wp_mail($to, $subject, $template, $headers);
        
        $this->log_email_send($to, $subject, $result ? 'success' : 'failed', 'newsletter');
        
        return $result;
    }
    
    /**
     * Send Welcome Email
     */
    public function send_welcome_email($to, $first_name = '', $data = array()) {
        $default_data = array(
            'first_name' => $first_name,
            'organization_name' => 'Kili Smile Organization',
            'website_url' => home_url(),
            'unsubscribe_url' => home_url('/unsubscribe?email=' . urlencode($to)),
            'email' => $to
        );
        
        $template_data = wp_parse_args($data, $default_data);
        $template = $this->load_email_template('welcome', $template_data);
        
        $subject = sprintf(__('Welcome to %s Newsletter!', 'kilismile'), get_bloginfo('name'));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->smtp_config['from_name'] . ' <' . $this->smtp_config['from_email'] . '>',
            'Reply-To: ' . $this->smtp_config['from_email']
        );
        
        $result = wp_mail($to, $subject, $template, $headers);
        
        $this->log_email_send($to, $subject, $result ? 'success' : 'failed', 'welcome');
        
        return $result;
    }
    
    /**
     * Send Donation Confirmation Email
     */
    public function send_donation_confirmation($to, $donation_data) {
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['id'],
            'payment_method' => $donation_data['payment_method'],
            'date' => date('F j, Y', strtotime($donation_data['created_at'])),
            'organization_name' => 'Kili Smile Organization',
            'website_url' => home_url(),
            'tax_info' => get_option('kilismile_tax_deduction_info', '')
        );
        
        $template = $this->load_email_template('donation-confirmation', $template_data);
        
        $subject = sprintf(__('Thank you for your donation - %s', 'kilismile'), get_bloginfo('name'));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->smtp_config['from_name'] . ' <' . $this->smtp_config['from_email'] . '>',
            'Reply-To: ' . $this->smtp_config['from_email']
        );
        
        $result = wp_mail($to, $subject, $template, $headers);
        
        $this->log_email_send($to, $subject, $result ? 'success' : 'failed', 'donation-confirmation');
        
        return $result;
    }
    
    /**
     * Send Event Registration Confirmation
     */
    public function send_event_confirmation($to, $event_data) {
        $template_data = array(
            'participant_name' => $event_data['name'],
            'event_title' => $event_data['event_title'],
            'event_date' => $event_data['event_date'],
            'event_time' => $event_data['event_time'],
            'event_location' => $event_data['event_location'],
            'registration_id' => $event_data['registration_id'],
            'organization_name' => 'Kili Smile Organization',
            'website_url' => home_url(),
            'contact_email' => $this->smtp_config['from_email']
        );
        
        $template = $this->load_email_template('event-confirmation', $template_data);
        
        $subject = sprintf(__('Event Registration Confirmed - %s', 'kilismile'), $event_data['event_title']);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->smtp_config['from_name'] . ' <' . $this->smtp_config['from_email'] . '>',
            'Reply-To: ' . $this->smtp_config['from_email']
        );
        
        $result = wp_mail($to, $subject, $template, $headers);
        
        $this->log_email_send($to, $subject, $result ? 'success' : 'failed', 'event-confirmation');
        
        return $result;
    }
    
    /**
     * Send Contact Form Email
     */
    public function send_contact_form_email($form_data) {
        $admin_email = get_option('admin_email');
        
        $template_data = array(
            'name' => $form_data['name'],
            'email' => $form_data['email'],
            'subject' => $form_data['subject'],
            'message' => $form_data['message'],
            'phone' => isset($form_data['phone']) ? $form_data['phone'] : '',
            'date' => current_time('F j, Y g:i A'),
            'ip_address' => $this->get_user_ip(),
            'website_url' => home_url()
        );
        
        $template = $this->load_email_template('contact-form', $template_data);
        
        $subject = sprintf(__('New Contact Form Submission - %s', 'kilismile'), $form_data['subject']);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->smtp_config['from_name'] . ' <' . $this->smtp_config['from_email'] . '>',
            'Reply-To: ' . $form_data['email']
        );
        
        $result = wp_mail($admin_email, $subject, $template, $headers);
        
        $this->log_email_send($admin_email, $subject, $result ? 'success' : 'failed', 'contact-form');
        
        return $result;
    }
    
    /**
     * Send Bulk Newsletter to All Subscribers
     */
    public function send_bulk_newsletter_campaign($newsletter_id, $subject, $content) {
        global $wpdb;
        
        $subscriber_table = $wpdb->prefix . 'kilismile_newsletter_subscribers';
        
        // Get all active subscribers
        $subscribers = $wpdb->get_results(
            "SELECT email, first_name, last_name FROM $subscriber_table 
             WHERE status = 'active' AND confirmed = 1"
        );
        
        if (empty($subscribers)) {
            return array('success' => false, 'message' => 'No active subscribers found');
        }
        
        $sent_count = 0;
        $failed_count = 0;
        $batch_size = 50; // Send in batches to avoid server overload
        $batches = array_chunk($subscribers, $batch_size);
        
        foreach ($batches as $batch_index => $batch) {
            foreach ($batch as $subscriber) {
                $template_data = array(
                    'first_name' => $subscriber->first_name,
                    'full_name' => trim($subscriber->first_name . ' ' . $subscriber->last_name),
                    'newsletter_content' => $content,
                    'unsubscribe_url' => home_url('/unsubscribe?email=' . urlencode($subscriber->email)),
                    'view_online_url' => home_url('/newsletter/' . $newsletter_id),
                    'organization_name' => 'Kili Smile Organization',
                    'website_url' => home_url()
                );
                
                $result = $this->send_newsletter_email($subscriber->email, $subject, $template_data);
                
                if ($result) {
                    $sent_count++;
                } else {
                    $failed_count++;
                }
                
                // Add small delay to prevent overwhelming the server
                usleep(100000); // 0.1 second delay
            }
            
            // Longer delay between batches
            if ($batch_index < count($batches) - 1) {
                sleep(2);
            }
        }
        
        return array(
            'success' => true,
            'sent' => $sent_count,
            'failed' => $failed_count,
            'total' => count($subscribers)
        );
    }
    
    /**
     * Load Email Template
     */
    private function load_email_template($template_name, $data = array()) {
        $template_file = $this->templates_path . $template_name . '.php';
        
        if (!file_exists($template_file)) {
            // Create default template if it doesn't exist
            $this->create_default_template($template_name);
        }
        
        if (file_exists($template_file)) {
            // Extract data for template
            extract($data);
            
            ob_start();
            include $template_file;
            return ob_get_clean();
        }
        
        // Fallback to basic template
        return $this->get_basic_email_template($template_name, $data);
    }
    
    /**
     * Get Basic Email Template (fallback)
     */
    private function get_basic_email_template($template_name, $data) {
        $header = $this->get_email_header();
        $footer = $this->get_email_footer();
        
        $content = '';
        
        switch ($template_name) {
            case 'welcome':
                $content = sprintf(
                    '<h2>Welcome %s!</h2>
                    <p>Thank you for subscribing to our newsletter. You will receive updates about our health programs and impact in Tanzania.</p>
                    <p>Best regards,<br>The Kili Smile Team</p>',
                    isset($data['first_name']) ? esc_html($data['first_name']) : ''
                );
                break;
                
            case 'newsletter':
                $content = sprintf(
                    '<h2>Newsletter Update</h2>
                    <p>Hello %s,</p>
                    <div>%s</div>',
                    isset($data['first_name']) ? esc_html($data['first_name']) : '',
                    isset($data['newsletter_content']) ? $data['newsletter_content'] : ''
                );
                break;
                
            case 'donation-confirmation':
                $content = sprintf(
                    '<h2>Thank you for your donation!</h2>
                    <p>Dear %s,</p>
                    <p>We have received your donation of %s %s. Your support helps us continue our mission.</p>
                    <p>Donation ID: %s</p>
                    <p>With gratitude,<br>The Kili Smile Team</p>',
                    isset($data['donor_name']) ? esc_html($data['donor_name']) : '',
                    isset($data['currency']) ? esc_html($data['currency']) : '',
                    isset($data['amount']) ? esc_html($data['amount']) : '',
                    isset($data['donation_id']) ? esc_html($data['donation_id']) : ''
                );
                break;
                
            default:
                $content = '<p>Thank you for your interest in Kili Smile Organization.</p>';
        }
        
        return $header . $content . $footer;
    }
    
    /**
     * Get Email Header
     */
    private function get_email_header() {
        $logo_url = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'medium');
        $site_url = home_url();
        $site_name = get_bloginfo('name');
        
        return sprintf('
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>%s</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 20px 0; border-bottom: 3px solid #4CAF50; }
                .logo { max-width: 150px; height: auto; }
                .content { padding: 30px 0; }
                .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; font-size: 12px; color: #666; }
                .btn { display: inline-block; background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 10px 0; }
                .btn:hover { background: #45a049; }
                h1, h2 { color: #2d5a41; }
                .unsubscribe { font-size: 11px; color: #999; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    %s
                    <h1>%s</h1>
                    <p><em>No health without oral health</em></p>
                </div>
                <div class="content">',
            esc_html($site_name),
            $logo_url ? '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="logo">' : '',
            esc_html($site_name)
        );
    }
    
    /**
     * Get Email Footer
     */
    private function get_email_footer() {
        $site_name = get_bloginfo('name');
        $site_url = home_url();
        $contact_email = get_theme_mod('kilismile_email', 'kilismile21@gmail.com');
        $phone = get_theme_mod('kilismile_phone', '0763495575/0735495575');
        $address = get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania');
        
        return sprintf('
                </div>
                <div class="footer">
                    <p><strong>%s</strong><br>
                    <em>No health without oral health</em></p>
                    <p>
                        Email: %s<br>
                        Phone: %s<br>
                        Address: %s
                    </p>
                    <p>
                        <a href="%s">Visit our website</a> | 
                        <a href="%s/programs">Our Programs</a> | 
                        <a href="%s/contact">Contact Us</a>
                    </p>
                    <p class="unsubscribe">
                        You received this email because you subscribed to our newsletter.<br>
                        <a href="{{unsubscribe_url}}">Unsubscribe</a> | <a href="{{view_online_url}}">View Online</a>
                    </p>
                </div>
            </div>
        </body>
        </html>',
            esc_html($site_name),
            esc_html($contact_email),
            esc_html($phone),
            esc_html($address),
            esc_url($site_url),
            esc_url($site_url),
            esc_url($site_url)
        );
    }
    
    /**
     * Create Default Email Templates
     */
    private function create_default_template($template_name) {
        if (!is_dir($this->templates_path)) {
            wp_mkdir_p($this->templates_path);
        }
        
        $template_content = $this->get_default_template_content($template_name);
        $template_file = $this->templates_path . $template_name . '.php';
        
        file_put_contents($template_file, $template_content);
    }
    
    /**
     * Get Default Template Content
     */
    private function get_default_template_content($template_name) {
        $header = '<?php
/**
 * Email Template: ' . ucwords(str_replace('-', ' ', $template_name)) . '
 * 
 * Available variables:
 * All data passed to the template is available as PHP variables
 * 
 * @package KiliSmile
 */

if (!defined("ABSPATH")) {
    exit;
}

echo $this->get_email_header();
?>';
        
        $footer = '<?php echo $this->get_email_footer(); ?>';
        
        switch ($template_name) {
            case 'welcome':
                return $header . '

<h2><?php _e("Welcome", "kilismile"); ?> <?php echo isset($first_name) ? esc_html($first_name) : ""; ?>!</h2>

<p><?php _e("Thank you for subscribing to the Kili Smile Organization newsletter!", "kilismile"); ?></p>

<p><?php _e("You will now receive updates about:", "kilismile"); ?></p>
<ul>
    <li><?php _e("Health programs and their impact", "kilismile"); ?></li>
    <li><?php _e("Success stories from Tanzania", "kilismile"); ?></li>
    <li><?php _e("Upcoming events and volunteer opportunities", "kilismile"); ?></li>
    <li><?php _e("Health education tips and resources", "kilismile"); ?></li>
</ul>

<p style="text-align: center;">
    <a href="<?php echo isset($website_url) ? esc_url($website_url) : home_url(); ?>" class="btn">
        <?php _e("Visit Our Website", "kilismile"); ?>
    </a>
</p>

<p><?php _e("Thank you for joining us in our mission to improve oral health in remote communities of Tanzania.", "kilismile"); ?></p>

<p><?php _e("Best regards,", "kilismile"); ?><br>
<?php _e("The Kili Smile Organization Team", "kilismile"); ?></p>

' . $footer;
                
            case 'newsletter':
                return $header . '

<h2><?php _e("Newsletter Update", "kilismile"); ?></h2>

<p><?php _e("Hello", "kilismile"); ?> <?php echo isset($first_name) ? esc_html($first_name) : ""; ?>,</p>

<div style="margin: 20px 0;">
    <?php echo isset($newsletter_content) ? $newsletter_content : ""; ?>
</div>

<?php if (isset($view_online_url)) : ?>
<p style="text-align: center;">
    <a href="<?php echo esc_url($view_online_url); ?>" class="btn">
        <?php _e("View Online Version", "kilismile"); ?>
    </a>
</p>
<?php endif; ?>

<p><?php _e("Thank you for your continued support!", "kilismile"); ?></p>

<p><?php _e("Best regards,", "kilismile"); ?><br>
<?php _e("The Kili Smile Team", "kilismile"); ?></p>

' . $footer;
                
            case 'donation-confirmation':
                return $header . '

<h2><?php _e("Thank you for your generous donation!", "kilismile"); ?></h2>

<p><?php _e("Dear", "kilismile"); ?> <?php echo isset($donor_name) ? esc_html($donor_name) : ""; ?>,</p>

<p><?php _e("We have received your donation and we are incredibly grateful for your support.", "kilismile"); ?></p>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3><?php _e("Donation Details:", "kilismile"); ?></h3>
    <p><strong><?php _e("Amount:", "kilismile"); ?></strong> <?php echo isset($currency) ? esc_html($currency) : ""; ?> <?php echo isset($amount) ? esc_html(number_format($amount, 2)) : ""; ?></p>
    <p><strong><?php _e("Donation ID:", "kilismile"); ?></strong> <?php echo isset($donation_id) ? esc_html($donation_id) : ""; ?></p>
    <p><strong><?php _e("Date:", "kilismile"); ?></strong> <?php echo isset($date) ? esc_html($date) : ""; ?></p>
    <p><strong><?php _e("Payment Method:", "kilismile"); ?></strong> <?php echo isset($payment_method) ? esc_html(ucwords(str_replace("_", " ", $payment_method))) : ""; ?></p>
</div>

<p><?php _e("Your donation will help us:", "kilismile"); ?></p>
<ul>
    <li><?php _e("Provide health education to remote communities", "kilismile"); ?></li>
    <li><?php _e("Conduct oral health screenings", "kilismile"); ?></li>
    <li><?php _e("Train local healthcare workers", "kilismile"); ?></li>
    <li><?php _e("Supply essential medical equipment", "kilismile"); ?></li>
</ul>

<?php if (isset($tax_info) && !empty($tax_info)) : ?>
<div style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <h4><?php _e("Tax Information:", "kilismile"); ?></h4>
    <p><?php echo esc_html($tax_info); ?></p>
</div>
<?php endif; ?>

<p style="text-align: center;">
    <a href="<?php echo isset($website_url) ? esc_url($website_url . \"/programs\") : home_url(\"/programs\"); ?>" class="btn">
        <?php _e("See Our Impact", "kilismile"); ?>
    </a>
</p>

<p><?php _e("With deep gratitude,", "kilismile"); ?><br>
<?php _e("The Kili Smile Organization Team", "kilismile"); ?></p>

' . $footer;
                
            case 'contact-form':
                return $header . '

<h2><?php _e("New Contact Form Submission", "kilismile"); ?></h2>

<p><?php _e("You have received a new contact form submission from your website.", "kilismile"); ?></p>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3><?php _e("Contact Details:", "kilismile"); ?></h3>
    <p><strong><?php _e("Name:", "kilismile"); ?></strong> <?php echo isset($name) ? esc_html($name) : ""; ?></p>
    <p><strong><?php _e("Email:", "kilismile"); ?></strong> <?php echo isset($email) ? esc_html($email) : ""; ?></p>
    <?php if (isset($phone) && !empty($phone)) : ?>
    <p><strong><?php _e("Phone:", "kilismile"); ?></strong> <?php echo esc_html($phone); ?></p>
    <?php endif; ?>
    <p><strong><?php _e("Subject:", "kilismile"); ?></strong> <?php echo isset($subject) ? esc_html($subject) : ""; ?></p>
    <p><strong><?php _e("Date:", "kilismile"); ?></strong> <?php echo isset($date) ? esc_html($date) : ""; ?></p>
    <?php if (isset($ip_address)) : ?>
    <p><strong><?php _e("IP Address:", "kilismile"); ?></strong> <?php echo esc_html($ip_address); ?></p>
    <?php endif; ?>
</div>

<div style="background: #fff; padding: 20px; border-left: 4px solid #4CAF50; margin: 20px 0;">
    <h3><?php _e("Message:", "kilismile"); ?></h3>
    <p><?php echo isset($message) ? nl2br(esc_html($message)) : ""; ?></p>
</div>

<p style="text-align: center;">
    <a href="mailto:<?php echo isset($email) ? esc_attr($email) : ""; ?>" class="btn">
        <?php _e("Reply to this Email", "kilismile"); ?>
    </a>
</p>

' . $footer;
                
            default:
                return $header . '

<h2><?php _e("Message from Kili Smile Organization", "kilismile"); ?></h2>

<p><?php _e("Thank you for your interest in Kili Smile Organization.", "kilismile"); ?></p>

<p><?php _e("Best regards,", "kilismile"); ?><br>
<?php _e("The Kili Smile Team", "kilismile"); ?></p>

' . $footer;
        }
    }
    
    /**
     * Ensure Templates Directory Exists
     */
    private function ensure_templates_directory() {
        if (!is_dir($this->templates_path)) {
            wp_mkdir_p($this->templates_path);
            
            // Create index.php for security
            $index_content = '<?php
// Silence is golden.
';
            file_put_contents($this->templates_path . 'index.php', $index_content);
        }
    }
    
    /**
     * Create Email Log Table
     */
    private function create_email_log_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_email_log';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            recipient varchar(255) NOT NULL,
            subject varchar(500) NOT NULL,
            email_type varchar(100) DEFAULT 'general',
            status varchar(50) NOT NULL DEFAULT 'pending',
            error_message text,
            sent_at datetime DEFAULT CURRENT_TIMESTAMP,
            user_agent varchar(500),
            ip_address varchar(45),
            PRIMARY KEY (id),
            INDEX recipient_idx (recipient),
            INDEX status_idx (status),
            INDEX email_type_idx (email_type),
            INDEX sent_at_idx (sent_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Log Email Send Attempt
     */
    private function log_email_send($recipient, $subject, $status, $type = 'general', $error = '') {
        if (!$this->log_emails) {
            return;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_email_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'recipient' => $recipient,
                'subject' => $subject,
                'email_type' => $type,
                'status' => $status,
                'error_message' => $error,
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'ip_address' => $this->get_user_ip()
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log Email Attempt
     */
    private function log_email_attempt($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Kili Smile Email System: ' . $message);
        }
    }
    
    /**
     * Log Email Failure
     */
    public function log_email_failure($wp_error) {
        $this->log_email_attempt('Email failed: ' . $wp_error->get_error_message());
        
        if ($this->log_emails) {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'kilismile_email_log';
            
            $wpdb->insert(
                $table_name,
                array(
                    'recipient' => 'unknown',
                    'subject' => 'Email Failure',
                    'email_type' => 'error',
                    'status' => 'failed',
                    'error_message' => $wp_error->get_error_message(),
                    'ip_address' => $this->get_user_ip()
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Get User IP Address
     */
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '';
        }
    }
    
    /**
     * Test Email Configuration
     */
    public function test_email_configuration() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Unauthorized', 'kilismile'));
        }
        
        $test_email = sanitize_email($_POST['test_email']);
        
        if (!is_email($test_email)) {
            wp_send_json_error(__('Invalid email address', 'kilismile'));
        }
        
        $subject = __('Test Email from Kili Smile Organization', 'kilismile');
        $message = $this->get_basic_email_template('test', array(
            'test_time' => current_time('F j, Y g:i A'),
            'server_info' => $_SERVER['SERVER_NAME'] ?? 'Unknown'
        ));
        
        $result = wp_mail($test_email, $subject, $message);
        
        if ($result) {
            wp_send_json_success(__('Test email sent successfully!', 'kilismile'));
        } else {
            wp_send_json_error(__('Failed to send test email. Please check your SMTP configuration.', 'kilismile'));
        }
    }
    
    /**
     * Send Bulk Newsletter AJAX Handler
     */
    public function send_bulk_newsletter() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Unauthorized', 'kilismile'));
        }
        
        $newsletter_id = intval($_POST['newsletter_id']);
        $subject = sanitize_text_field($_POST['subject']);
        $content = wp_kses_post($_POST['content']);
        
        $result = $this->send_bulk_newsletter_campaign($newsletter_id, $subject, $content);
        
        if ($result['success']) {
            wp_send_json_success(sprintf(
                __('Newsletter sent successfully! Sent: %d, Failed: %d, Total: %d', 'kilismile'),
                $result['sent'],
                $result['failed'],
                $result['total']
            ));
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Add Email Settings to Customizer
     */
    public function add_email_customizer_settings($wp_customize) {
        // Email System Section
        $wp_customize->add_section('kilismile_email_system', array(
            'title'    => __('Email System Settings', 'kilismile'),
            'priority' => 50,
        ));
        
        // SMTP Enable
        $wp_customize->add_setting('kilismile_smtp_enabled', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('kilismile_smtp_enabled', array(
            'label'   => __('Enable SMTP Email', 'kilismile'),
            'section' => 'kilismile_email_system',
            'type'    => 'checkbox',
        ));
        
        // From Email
        $wp_customize->add_setting('kilismile_from_email', array(
            'default'           => get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
            'sanitize_callback' => 'sanitize_email',
        ));
        
        $wp_customize->add_control('kilismile_from_email', array(
            'label'   => __('From Email Address', 'kilismile'),
            'section' => 'kilismile_email_system',
            'type'    => 'email',
        ));
        
        // From Name
        $wp_customize->add_setting('kilismile_from_name', array(
            'default'           => 'Kili Smile Organization',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('kilismile_from_name', array(
            'label'   => __('From Name', 'kilismile'),
            'section' => 'kilismile_email_system',
            'type'    => 'text',
        ));
    }
    
    /**
     * Register Email Settings
     */
    public function register_email_settings() {
        // SMTP Settings
        register_setting('kilismile_email_settings', 'kilismile_smtp_enabled', array('type' => 'boolean'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_host', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_port', array('type' => 'integer'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_username', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_password', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_encryption', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_smtp_auth', array('type' => 'boolean'));
        register_setting('kilismile_email_settings', 'kilismile_from_email', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_from_name', array('type' => 'string'));
        register_setting('kilismile_email_settings', 'kilismile_log_emails', array('type' => 'boolean'));
        register_setting('kilismile_email_settings', 'kilismile_tax_deduction_info', array('type' => 'string'));
    }
    
    /**
     * Add Email Admin Menu
     */
    public function add_email_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __('Email System', 'kilismile'),
            __('Email System', 'kilismile'),
            'manage_options',
            'kilismile-email-system',
            array($this, 'email_settings_page')
        );
        
        add_submenu_page(
            'edit.php?post_type=newsletter',
            __('Email Logs', 'kilismile'),
            __('Email Logs', 'kilismile'),
            'manage_options',
            'kilismile-email-logs',
            array($this, 'email_logs_page')
        );
    }
    
    /**
     * Email Settings Admin Page
     */
    public function email_settings_page() {
        if (isset($_POST['submit'])) {
            // Save settings
            update_option('kilismile_smtp_enabled', isset($_POST['smtp_enabled']));
            update_option('kilismile_smtp_host', sanitize_text_field($_POST['smtp_host']));
            update_option('kilismile_smtp_port', intval($_POST['smtp_port']));
            update_option('kilismile_smtp_username', sanitize_text_field($_POST['smtp_username']));
            if (!empty($_POST['smtp_password'])) {
                update_option('kilismile_smtp_password', sanitize_text_field($_POST['smtp_password']));
            }
            update_option('kilismile_smtp_encryption', sanitize_text_field($_POST['smtp_encryption']));
            update_option('kilismile_smtp_auth', isset($_POST['smtp_auth']));
            update_option('kilismile_from_email', sanitize_email($_POST['from_email']));
            update_option('kilismile_from_name', sanitize_text_field($_POST['from_name']));
            update_option('kilismile_log_emails', isset($_POST['log_emails']));
            update_option('kilismile_tax_deduction_info', sanitize_textarea_field($_POST['tax_deduction_info']));
            
            echo '<div class="notice notice-success"><p>' . __('Settings saved!', 'kilismile') . '</p></div>';
            
            // Refresh config
            $this->setup_smtp_config();
        }
        
        include get_template_directory() . '/admin/email-settings-page.php';
    }
    
    /**
     * Email Logs Admin Page
     */
    public function email_logs_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kilismile_email_log';
        $per_page = 50;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY sent_at DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));
        
        include get_template_directory() . '/admin/email-logs-page.php';
    }
}

// Initialize the email system
new KiliSmile_Email_System();

/**
 * Helper Functions for Easy Access
 */

/**
 * Send Newsletter Email
 */
function kilismile_send_newsletter($to, $subject, $data = array()) {
    global $kilismile_email_system;
    if (!$kilismile_email_system) {
        $kilismile_email_system = new KiliSmile_Email_System();
    }
    return $kilismile_email_system->send_newsletter_email($to, $subject, $data);
}

/**
 * Send Welcome Email
 */
function kilismile_send_welcome($to, $first_name = '', $data = array()) {
    global $kilismile_email_system;
    if (!$kilismile_email_system) {
        $kilismile_email_system = new KiliSmile_Email_System();
    }
    return $kilismile_email_system->send_welcome_email($to, $first_name, $data);
}

/**
 * Send Donation Confirmation
 */
function kilismile_send_donation_confirmation($to, $donation_data) {
    global $kilismile_email_system;
    if (!$kilismile_email_system) {
        $kilismile_email_system = new KiliSmile_Email_System();
    }
    return $kilismile_email_system->send_donation_confirmation($to, $donation_data);
}



/**
 * Send Bulk Newsletter Campaign
 */
function kilismile_send_bulk_newsletter($newsletter_id, $subject, $content) {
    global $kilismile_email_system;
    if (!$kilismile_email_system) {
        $kilismile_email_system = new KiliSmile_Email_System();
    }
    return $kilismile_email_system->send_bulk_newsletter_campaign($newsletter_id, $subject, $content);
}

?>
