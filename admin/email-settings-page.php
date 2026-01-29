<?php
/**
 * Email Settings Admin Page
 * 
 * @package KiliSmile
 */

if (!defined('ABSPATH')) {
    exit;
}

$smtp_enabled = get_option('kilismile_smtp_enabled', false);
$smtp_host = get_option('kilismile_smtp_host', '');
$smtp_port = get_option('kilismile_smtp_port', 587);
$smtp_username = get_option('kilismile_smtp_username', '');
$smtp_password = get_option('kilismile_smtp_password', '');
$smtp_encryption = get_option('kilismile_smtp_encryption', 'tls');
$smtp_auth = get_option('kilismile_smtp_auth', true);
$from_email = get_option('kilismile_from_email', get_theme_mod('kilismile_email', 'kilismile21@gmail.com'));
$from_name = get_option('kilismile_from_name', 'Kilismile Organization');
$log_emails = get_option('kilismile_log_emails', true);
$tax_deduction_info = get_option('kilismile_tax_deduction_info', '');
?>

<div class="wrap">
    <h1><?php _e('Email System Settings', 'kilismile'); ?></h1>
    
    <div class="email-system-tabs">
        <h2 class="nav-tab-wrapper">
            <a href="#smtp-settings" class="nav-tab nav-tab-active"><?php _e('SMTP Settings', 'kilismile'); ?></a>
            <a href="#email-templates" class="nav-tab"><?php _e('Email Templates', 'kilismile'); ?></a>
            <a href="#test-email" class="nav-tab"><?php _e('Test Email', 'kilismile'); ?></a>
            <a href="#bulk-newsletter" class="nav-tab"><?php _e('Send Newsletter', 'kilismile'); ?></a>
        </h2>
        
        <!-- SMTP Settings Tab -->
        <div id="smtp-settings" class="tab-content active">
            <form method="post" action="">
                <?php wp_nonce_field('kilismile_email_settings', 'email_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable SMTP Email', 'kilismile'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="smtp_enabled" value="1" <?php checked($smtp_enabled); ?>>
                                <?php _e('Use SMTP for sending emails (recommended for reliability)', 'kilismile'); ?>
                            </label>
                            <p class="description"><?php _e('Enable this to use SMTP instead of PHP mail() function', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('SMTP Host', 'kilismile'); ?></th>
                        <td>
                            <input type="text" name="smtp_host" value="<?php echo esc_attr($smtp_host); ?>" class="regular-text" placeholder="smtp.gmail.com">
                            <p class="description">
                                <?php _e('Common SMTP hosts:', 'kilismile'); ?><br>
                                <strong>Gmail:</strong> smtp.gmail.com<br>
                                <strong>Outlook:</strong> smtp-mail.outlook.com<br>
                                <strong>Yahoo:</strong> smtp.mail.yahoo.com<br>
                                <strong>SendGrid:</strong> smtp.sendgrid.net
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('SMTP Port', 'kilismile'); ?></th>
                        <td>
                            <input type="number" name="smtp_port" value="<?php echo esc_attr($smtp_port); ?>" class="small-text" min="1" max="65535">
                            <p class="description">
                                <?php _e('Common ports:', 'kilismile'); ?><br>
                                <strong>587</strong> - TLS encryption (recommended)<br>
                                <strong>465</strong> - SSL encryption<br>
                                <strong>25</strong> - No encryption (not recommended)
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Encryption', 'kilismile'); ?></th>
                        <td>
                            <select name="smtp_encryption">
                                <option value="tls" <?php selected($smtp_encryption, 'tls'); ?>>TLS (recommended)</option>
                                <option value="ssl" <?php selected($smtp_encryption, 'ssl'); ?>>SSL</option>
                                <option value="" <?php selected($smtp_encryption, ''); ?>><?php _e('None', 'kilismile'); ?></option>
                            </select>
                            <p class="description"><?php _e('Choose the encryption method supported by your SMTP server', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('SMTP Authentication', 'kilismile'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="smtp_auth" value="1" <?php checked($smtp_auth); ?>>
                                <?php _e('Enable SMTP authentication', 'kilismile'); ?>
                            </label>
                            <p class="description"><?php _e('Most SMTP servers require authentication', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('SMTP Username', 'kilismile'); ?></th>
                        <td>
                            <input type="text" name="smtp_username" value="<?php echo esc_attr($smtp_username); ?>" class="regular-text" placeholder="your-email@domain.com">
                            <p class="description"><?php _e('Usually your email address', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('SMTP Password', 'kilismile'); ?></th>
                        <td>
                            <input type="password" name="smtp_password" value="" class="regular-text" placeholder="<?php echo $smtp_password ? '••••••••••••' : ''; ?>">
                            <p class="description">
                                <?php _e('Your email password or app-specific password', 'kilismile'); ?><br>
                                <strong><?php _e('Gmail users:', 'kilismile'); ?></strong> <?php _e('Use an App Password, not your regular password', 'kilismile'); ?><br>
                                <?php if (!empty($smtp_password)) : ?>
                                    <em><?php _e('Leave blank to keep current password', 'kilismile'); ?></em>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('From Email Address', 'kilismile'); ?></th>
                        <td>
                            <input type="email" name="from_email" value="<?php echo esc_attr($from_email); ?>" class="regular-text" required>
                            <p class="description"><?php _e('The email address that emails will be sent from', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('From Name', 'kilismile'); ?></th>
                        <td>
                            <input type="text" name="from_name" value="<?php echo esc_attr($from_name); ?>" class="regular-text" required>
                            <p class="description"><?php _e('The name that will appear as the sender', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Email Logging', 'kilismile'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="log_emails" value="1" <?php checked($log_emails); ?>>
                                <?php _e('Log all email activity for debugging', 'kilismile'); ?>
                            </label>
                            <p class="description"><?php _e('Keep a record of sent emails and failures for troubleshooting', 'kilismile'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Tax Deduction Information', 'kilismile'); ?></th>
                        <td>
                            <textarea name="tax_deduction_info" rows="3" class="large-text"><?php echo esc_textarea($tax_deduction_info); ?></textarea>
                            <p class="description"><?php _e('Information about tax deductions to include in donation confirmation emails', 'kilismile'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Save Email Settings', 'kilismile')); ?>
            </form>
        </div>
        
        <!-- Email Templates Tab -->
        <div id="email-templates" class="tab-content">
            <h3><?php _e('Email Templates', 'kilismile'); ?></h3>
            <p><?php _e('Email templates are automatically created and stored in:', 'kilismile'); ?></p>
            <code><?php echo esc_html(get_template_directory() . '/email-templates/'); ?></code>
            
            <div class="template-list">
                <h4><?php _e('Available Templates:', 'kilismile'); ?></h4>
                <ul>
                    <li><strong>welcome.php</strong> - <?php _e('Welcome email for new newsletter subscribers', 'kilismile'); ?></li>
                    <li><strong>newsletter.php</strong> - <?php _e('Newsletter email template', 'kilismile'); ?></li>
                    <li><strong>donation-confirmation.php</strong> - <?php _e('Donation confirmation email', 'kilismile'); ?></li>
                    <li><strong>contact-form.php</strong> - <?php _e('Contact form notification email', 'kilismile'); ?></li>
                    <li><strong>event-confirmation.php</strong> - <?php _e('Event registration confirmation email', 'kilismile'); ?></li>
                </ul>
            </div>
            
            <div class="template-variables">
                <h4><?php _e('Template Variables:', 'kilismile'); ?></h4>
                <p><?php _e('The following variables are available in email templates:', 'kilismile'); ?></p>
                <ul>
                    <li><code>$first_name</code> - <?php _e('Recipient\'s first name', 'kilismile'); ?></li>
                    <li><code>$organization_name</code> - <?php _e('Organization name', 'kilismile'); ?></li>
                    <li><code>$website_url</code> - <?php _e('Website URL', 'kilismile'); ?></li>
                    <li><code>$unsubscribe_url</code> - <?php _e('Unsubscribe link', 'kilismile'); ?></li>
                    <li><code>$newsletter_content</code> - <?php _e('Newsletter content (newsletter template)', 'kilismile'); ?></li>
                    <li><code>$donation_data</code> - <?php _e('Donation information (donation template)', 'kilismile'); ?></li>
                </ul>
            </div>
        </div>
        
        <!-- Test Email Tab -->
        <div id="test-email" class="tab-content">
            <h3><?php _e('Test Email Configuration', 'kilismile'); ?></h3>
            <p><?php _e('Send a test email to verify your SMTP configuration is working correctly.', 'kilismile'); ?></p>
            
            <form id="test-email-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Test Email Address', 'kilismile'); ?></th>
                        <td>
                            <input type="email" id="test_email" name="test_email" value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" class="regular-text" required>
                            <p class="description"><?php _e('Enter the email address where you want to receive the test email', 'kilismile'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary" id="send-test-email">
                        <span class="spinner"></span>
                        <?php _e('Send Test Email', 'kilismile'); ?>
                    </button>
                </p>
            </form>
            
            <div id="test-email-result" class="notice" style="display: none;"></div>
        </div>
        
        <!-- Bulk Newsletter Tab -->
        <div id="bulk-newsletter" class="tab-content">
            <h3><?php _e('Send Newsletter to All Subscribers', 'kilismile'); ?></h3>
            <p><?php _e('Send a newsletter email to all active subscribers.', 'kilismile'); ?></p>
            
            <?php
            $subscriber_count = 0;
            global $wpdb;
            $subscriber_table = $wpdb->prefix . 'kilismile_newsletter_subscribers';
            if ($wpdb->get_var("SHOW TABLES LIKE '$subscriber_table'") == $subscriber_table) {
                $subscriber_count = $wpdb->get_var("SELECT COUNT(*) FROM $subscriber_table WHERE status = 'active' AND confirmed = 1");
            }
            ?>
            
            <div class="subscriber-info">
                <p><strong><?php printf(__('Active Subscribers: %d', 'kilismile'), $subscriber_count); ?></strong></p>
            </div>
            
            <?php if ($subscriber_count > 0) : ?>
                <form id="bulk-newsletter-form">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Newsletter Post', 'kilismile'); ?></th>
                            <td>
                                <?php
                                $newsletters = get_posts(array(
                                    'post_type' => 'newsletter',
                                    'posts_per_page' => 20,
                                    'post_status' => 'publish'
                                ));
                                ?>
                                <select name="newsletter_id" id="newsletter_id" required>
                                    <option value=""><?php _e('Select a newsletter...', 'kilismile'); ?></option>
                                    <?php foreach ($newsletters as $newsletter) : ?>
                                        <option value="<?php echo $newsletter->ID; ?>"><?php echo esc_html($newsletter->post_title); ?> (<?php echo get_the_date('F j, Y', $newsletter); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Select the newsletter post to send to subscribers', 'kilismile'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Email Subject', 'kilismile'); ?></th>
                            <td>
                                <input type="text" name="email_subject" id="email_subject" class="large-text" required>
                                <p class="description"><?php _e('The subject line for the email', 'kilismile'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Preview', 'kilismile'); ?></th>
                            <td>
                                <div id="newsletter_preview" style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9; min-height: 100px;">
                                    <em><?php _e('Select a newsletter to preview content...', 'kilismile'); ?></em>
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="bulk-send-warning">
                        <h4><?php _e('Important Notes:', 'kilismile'); ?></h4>
                        <ul>
                            <li><?php _e('This will send emails to all active subscribers', 'kilismile'); ?></li>
                            <li><?php _e('The process may take several minutes to complete', 'kilismile'); ?></li>
                            <li><?php _e('Please ensure your SMTP settings are configured correctly', 'kilismile'); ?></li>
                            <li><?php _e('Large subscriber lists will be sent in batches to prevent server overload', 'kilismile'); ?></li>
                        </ul>
                    </div>
                    
                    <p class="submit">
                        <button type="submit" class="button button-primary" id="send-bulk-newsletter">
                            <span class="spinner"></span>
                            <?php printf(__('Send Newsletter to %d Subscribers', 'kilismile'), $subscriber_count); ?>
                        </button>
                    </p>
                </form>
                
                <div id="bulk-newsletter-result" class="notice" style="display: none;"></div>
            <?php else : ?>
                <div class="notice notice-warning">
                    <p><?php _e('No active subscribers found. Subscribers need to confirm their email addresses before receiving newsletters.', 'kilismile'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.email-system-tabs .nav-tab-wrapper {
    margin-bottom: 20px;
}

.email-system-tabs .tab-content {
    display: none;
}

.email-system-tabs .tab-content.active {
    display: block;
}

.template-list, .template-variables {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    margin: 15px 0;
}

.bulk-send-warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 15px;
    margin: 15px 0;
    border-radius: 4px;
}

.bulk-send-warning h4 {
    margin-top: 0;
    color: #856404;
}

.bulk-send-warning ul {
    margin-bottom: 0;
    color: #856404;
}

.subscriber-info {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 4px;
    margin: 15px 0;
}

.spinner {
    float: none;
    margin-left: 5px;
    display: none;
}

.button.loading .spinner {
    visibility: visible;
    display: inline-block;
}

#newsletter_preview {
    max-height: 300px;
    overflow-y: auto;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Update tab states
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Update content visibility
        $('.tab-content').removeClass('active');
        $(target).addClass('active');
    });
    
    // Test email functionality
    $('#test-email-form').on('submit', function(e) {
        e.preventDefault();
        
        var $button = $('#send-test-email');
        var $result = $('#test-email-result');
        var testEmail = $('#test_email').val();
        
        $button.addClass('loading').prop('disabled', true);
        $result.hide();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'test_email_configuration',
                test_email: testEmail,
                _wpnonce: '<?php echo wp_create_nonce('test_email'); ?>'
            },
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false);
                
                if (response.success) {
                    $result.removeClass('notice-error').addClass('notice-success')
                           .html('<p>' + response.data + '</p>').show();
                } else {
                    $result.removeClass('notice-success').addClass('notice-error')
                           .html('<p>' + response.data + '</p>').show();
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                $result.removeClass('notice-success').addClass('notice-error')
                       .html('<p><?php _e('An error occurred while sending the test email.', 'kilismile'); ?></p>').show();
            }
        });
    });
    
    // Newsletter preview functionality
    $('#newsletter_id').on('change', function() {
        var newsletterId = $(this).val();
        var $preview = $('#newsletter_preview');
        var $subject = $('#email_subject');
        
        if (newsletterId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_newsletter_preview',
                    newsletter_id: newsletterId,
                    _wpnonce: '<?php echo wp_create_nonce('newsletter_preview'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $preview.html(response.data.content);
                        $subject.val(response.data.subject);
                    }
                }
            });
        } else {
            $preview.html('<em><?php _e('Select a newsletter to preview content...', 'kilismile'); ?></em>');
            $subject.val('');
        }
    });
    
    // Bulk newsletter functionality
    $('#bulk-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        var $button = $('#send-bulk-newsletter');
        var $result = $('#bulk-newsletter-result');
        var formData = $(this).serialize();
        
        if (!confirm('<?php _e('Are you sure you want to send this newsletter to all subscribers? This action cannot be undone.', 'kilismile'); ?>')) {
            return;
        }
        
        $button.addClass('loading').prop('disabled', true);
        $result.hide();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=send_bulk_newsletter&_wpnonce=<?php echo wp_create_nonce('bulk_newsletter'); ?>',
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false);
                
                if (response.success) {
                    $result.removeClass('notice-error').addClass('notice-success')
                           .html('<p>' + response.data + '</p>').show();
                } else {
                    $result.removeClass('notice-success').addClass('notice-error')
                           .html('<p>' + response.data + '</p>').show();
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                $result.removeClass('notice-success').addClass('notice-error')
                       .html('<p><?php _e('An error occurred while sending the newsletter.', 'kilismile'); ?></p>').show();
            }
        });
    });
});
</script>


