<?php
/**
 * Contact Form Email Templates
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Email System Instance
 */
function kilismile_get_email_system() {
    global $kilismile_email_system;
    if (!$kilismile_email_system) {
        $kilismile_email_system = new KiliSmile_Email_System();
    }
    return $kilismile_email_system;
}

/**
 * Send Contact Form Notification Email Using Enhanced System
 */
function kilismile_send_contact_form($form_data) {
    $email_system = kilismile_get_email_system();
    if (!$email_system) {
        return false;
    }
    
    // Send notification to admin
    $admin_email = 'contact@kilismile.org';
    $subject = sprintf(__('[%s] New Contact Form Submission: %s', 'kilismile'), 
                      get_bloginfo('name'), 
                      $form_data['subject']);
    
    $template_data = array(
        'name' => $form_data['name'],
        'email' => $form_data['email'],
        'phone' => $form_data['phone'],
        'organization' => $form_data['organization'],
        'interest' => $form_data['interest'],
        'subject' => $form_data['subject'],
        'message' => $form_data['message'],
        'submitted_time' => current_time('F j, Y g:i A'),
        'ip_address' => kilismile_get_user_ip(),
        'website_url' => home_url()
    );
    
    $admin_notification = $email_system->send_email(
        $admin_email,
        $subject,
        '',
        $template_data,
        'contact_notification'
    );
    
    // Send auto-reply to submitter
    $auto_reply_subject = sprintf(__('Thank you for contacting %s', 'kilismile'), get_bloginfo('name'));
    
    $auto_reply_data = array(
        'name' => $form_data['name'],
        'subject' => $form_data['subject'],
        'message' => $form_data['message'],
        'organization_name' => get_bloginfo('name'),
        'website_url' => home_url(),
        'organization_email' => get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
        'organization_phone' => get_theme_mod('kilismile_phone', '+255763495575/+255735495575')
    );
    
    $auto_reply_sent = $email_system->send_email(
        $form_data['email'],
        $auto_reply_subject,
        '',
        $auto_reply_data,
        'contact_auto_reply'
    );
    
    return $admin_notification;
}

// Create the contact notification template
add_action('init', function() {
    $email_system = kilismile_get_email_system();
    if ($email_system) {
        $template_dir = get_template_directory() . '/email-templates/';
        
        // Contact notification template
        if (!file_exists($template_dir . 'contact_notification.php')) {
            $notification_template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; }
        .field { margin-bottom: 15px; }
        .field label { font-weight: bold; color: #2E7D32; display: inline-block; width: 120px; }
        .field value { color: #333; }
        .message-content { background: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 15px 0; }
        .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 0.9rem; color: #666; }
        .interest-badge { background: #e8f5e8; color: #2E7D32; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
            <p>Someone has submitted a message through your website contact form</p>
        </div>
        
        <div class="content">
            <h2>Contact Details</h2>
            
            <div class="field">
                <label>Name:</label>
                <span class="value">{{name}}</span>
            </div>
            
            <div class="field">
                <label>Email:</label>
                <span class="value"><a href="mailto:{{email}}">{{email}}</a></span>
            </div>
            
            {{#phone}}
            <div class="field">
                <label>Phone:</label>
                <span class="value">{{phone}}</span>
            </div>
            {{/phone}}
            
            {{#organization}}
            <div class="field">
                <label>Organization:</label>
                <span class="value">{{organization}}</span>
            </div>
            {{/organization}}
            
            {{#interest}}
            <div class="field">
                <label>Interest Area:</label>
                <span class="interest-badge">{{interest}}</span>
            </div>
            {{/interest}}
            
            <div class="field">
                <label>Subject:</label>
                <span class="value">{{subject}}</span>
            </div>
            
            <div class="field">
                <label>Message:</label>
                <div class="message-content">
                    {{message}}
                </div>
            </div>
            
            <h3>Submission Information</h3>
            
            <div class="field">
                <label>Submitted:</label>
                <span class="value">{{submitted_time}}</span>
            </div>
            
            <div class="field">
                <label>IP Address:</label>
                <span class="value">{{ip_address}}</span>
            </div>
            
            <div class="field">
                <label>Website:</label>
                <span class="value"><a href="{{website_url}}">{{website_url}}</a></span>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Quick Actions:</strong></p>
            <p>
                <a href="mailto:{{email}}?subject=Re: {{subject}}&body=Hello {{name}},%0D%0A%0D%0AThank you for contacting us.%0D%0A%0D%0A" 
                   style="background: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 0 5px;">
                    Reply to {{name}}
                </a>
            </p>
            <p>This email was sent from the contact form on your website.</p>
        </div>
    </div>
</body>
</html>';
            
            file_put_contents($template_dir . 'contact_notification.php', $notification_template);
        }
        
        // Contact auto-reply template
        if (!file_exists($template_dir . 'contact_auto_reply.php')) {
            $auto_reply_template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contacting Us</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .logo { width: 60px; height: 60px; margin: 0 auto 15px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; border-top: none; }
        .message-summary { background: #f0f8f0; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; }
        .contact-info { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .contact-item { margin-bottom: 10px; }
        .contact-item i { color: #4CAF50; margin-right: 8px; width: 16px; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; font-size: 0.9rem; color: #666; border-radius: 0 0 8px 8px; }
        .btn { background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #2E7D32; }
        .social-links { margin: 15px 0; }
        .social-links a { display: inline-block; margin: 0 10px; color: #4CAF50; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <span style="color: #4CAF50; font-size: 24px; font-weight: bold;">KS</span>
            </div>
            <h1>Thank You for Contacting Us!</h1>
            <p>We have received your message and will respond soon</p>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{name}}</strong>,</p>
            
            <p>Thank you for reaching out to <strong>{{organization_name}}</strong>! We appreciate your interest in our work and mission to improve oral health in Tanzania.</p>
            
            <div class="message-summary">
                <h3>Your Message Summary:</h3>
                <p><strong>Subject:</strong> {{subject}}</p>
                <p><strong>Message:</strong></p>
                <p>{{message}}</p>
            </div>
            
            <p>We typically respond to all inquiries within <strong>24-48 hours</strong> during business days. Our team will review your message and get back to you as soon as possible.</p>
            
            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <i>📧</i> <strong>Email:</strong> <a href="mailto:{{organization_email}}">{{organization_email}}</a>
                </div>
                <div class="contact-item">
                    <i>📞</i> <strong>Phone:</strong> {{organization_phone}}
                </div>
                <div class="contact-item">
                    <i>🌐</i> <strong>Website:</strong> <a href="{{website_url}}">{{website_url}}</a>
                </div>
            </div>
            
            <h3>While You Wait</h3>
            <p>Learn more about our work and how you can get involved:</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{website_url}}/programs" class="btn">Our Programs</a>
                <a href="{{website_url}}/volunteer" class="btn">Volunteer</a>
                <a href="{{website_url}}/donate" class="btn">Donate</a>
            </div>
            
            <div class="social-links">
                <p><strong>Follow Us:</strong></p>
                <a href="https://instagram.com/kili_smile">Instagram</a> |
                <a href="{{website_url}}/newsletter">Newsletter</a> |
                <a href="{{website_url}}/news">Latest News</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Kilismile Organization</strong></p>
            <p>"No health without oral health"</p>
            <p>Improving oral health in Tanzania through education, prevention, and community outreach.</p>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">
            <p style="font-size: 0.8rem;">This is an automated response to confirm we received your message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>';
            
            file_put_contents($template_dir . 'contact_auto_reply.php', $auto_reply_template);
        }
    }
});

?>


