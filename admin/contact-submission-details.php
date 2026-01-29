<?php
/**
 * Contact Submission Details View
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="submission-details-container">
    <div class="submission-header">
        <div class="submitter-info">
            <div class="avatar">
                <?php echo get_avatar($submission->email, 64); ?>
            </div>
            <div class="info">
                <h3><?php echo esc_html($submission->name); ?></h3>
                <p class="email">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:<?php echo esc_attr($submission->email); ?>">
                        <?php echo esc_html($submission->email); ?>
                    </a>
                </p>
                <?php if ($submission->phone) : ?>
                    <p class="phone">
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?php echo esc_attr($submission->phone); ?>">
                            <?php echo esc_html($submission->phone); ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if ($submission->organization) : ?>
                    <p class="organization">
                        <i class="fas fa-building"></i>
                        <?php echo esc_html($submission->organization); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="submission-meta">
            <div class="meta-item">
                <label><?php _e('Submitted:', 'kilismile'); ?></label>
                <span><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($submission->submitted_at))); ?></span>
            </div>
            
            <?php if ($submission->interest) : ?>
                <div class="meta-item">
                    <label><?php _e('Interest Area:', 'kilismile'); ?></label>
                    <span class="interest-badge interest-<?php echo esc_attr($submission->interest); ?>">
                        <?php echo esc_html(ucfirst(str_replace('_', ' ', $submission->interest))); ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="meta-item">
                <label><?php _e('IP Address:', 'kilismile'); ?></label>
                <span><?php echo esc_html($submission->ip_address); ?></span>
            </div>
            
            <div class="meta-item">
                <label><?php _e('User Agent:', 'kilismile'); ?></label>
                <span class="user-agent"><?php echo esc_html(wp_trim_words($submission->user_agent, 10)); ?></span>
            </div>
        </div>
    </div>
    
    <div class="submission-content">
        <div class="subject-section">
            <h4><?php _e('Subject:', 'kilismile'); ?></h4>
            <p class="subject"><?php echo esc_html($submission->subject); ?></p>
        </div>
        
        <div class="message-section">
            <h4><?php _e('Message:', 'kilismile'); ?></h4>
            <div class="message-content">
                <?php echo nl2br(esc_html($submission->message)); ?>
            </div>
        </div>
    </div>
    
    <div class="submission-actions">
        <div class="status-update">
            <form method="post" action="" class="inline-form">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="submission_id" value="<?php echo esc_attr($submission->id); ?>">
                <?php wp_nonce_field('kilismile_contact_admin', 'contact_admin_nonce'); ?>
                
                <div class="form-group">
                    <label for="new_status"><?php _e('Status:', 'kilismile'); ?></label>
                    <select name="new_status" id="new_status">
                        <option value="new" <?php selected($submission->status, 'new'); ?>><?php _e('New', 'kilismile'); ?></option>
                        <option value="read" <?php selected($submission->status, 'read'); ?>><?php _e('Read', 'kilismile'); ?></option>
                        <option value="replied" <?php selected($submission->status, 'replied'); ?>><?php _e('Replied', 'kilismile'); ?></option>
                        <option value="closed" <?php selected($submission->status, 'closed'); ?>><?php _e('Closed', 'kilismile'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="replied" value="1" <?php checked($submission->replied, 1); ?>>
                        <?php _e('Mark as replied', 'kilismile'); ?>
                    </label>
                </div>
                
                <button type="submit" class="button button-primary">
                    <?php _e('Update Status', 'kilismile'); ?>
                </button>
            </form>
        </div>
        
        <div class="action-buttons">
            <a href="mailto:<?php echo esc_attr($submission->email); ?>?subject=Re: <?php echo esc_attr($submission->subject); ?>&body=Hello <?php echo esc_attr($submission->name); ?>,%0D%0A%0D%0AThank you for contacting Kilismile Organization.%0D%0A%0D%0A" 
               class="button button-primary">
                <i class="fas fa-reply"></i>
                <?php _e('Reply via Email', 'kilismile'); ?>
            </a>
            
            <button type="button" class="button copy-email" data-email="<?php echo esc_attr($submission->email); ?>">
                <i class="fas fa-copy"></i>
                <?php _e('Copy Email', 'kilismile'); ?>
            </button>
            
            <button type="button" class="button export-submission" data-id="<?php echo esc_attr($submission->id); ?>">
                <i class="fas fa-download"></i>
                <?php _e('Export', 'kilismile'); ?>
            </button>
            
            <button type="button" class="button button-link-delete delete-submission" data-id="<?php echo esc_attr($submission->id); ?>">
                <i class="fas fa-trash"></i>
                <?php _e('Delete', 'kilismile'); ?>
            </button>
        </div>
        
        <?php if (function_exists('kilismile_get_email_system')) : ?>
            <div class="quick-email-section">
                <h4><?php _e('Quick Email Response', 'kilismile'); ?></h4>
                <form class="quick-email-form" data-submission-id="<?php echo esc_attr($submission->id); ?>">
                    <div class="form-group">
                        <label for="quick_subject"><?php _e('Subject:', 'kilismile'); ?></label>
                        <input type="text" id="quick_subject" name="quick_subject" 
                               value="Re: <?php echo esc_attr($submission->subject); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_message"><?php _e('Message:', 'kilismile'); ?></label>
                        <textarea id="quick_message" name="quick_message" rows="6" required
                                  placeholder="Hello <?php echo esc_attr($submission->name); ?>,&#10;&#10;Thank you for contacting Kilismile Organization..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="mark_replied" value="1" checked>
                            <?php _e('Mark submission as replied after sending', 'kilismile'); ?>
                        </label>
                    </div>
                    
                    <button type="submit" class="button button-primary">
                        <i class="fas fa-paper-plane"></i>
                        <?php _e('Send Reply', 'kilismile'); ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="submission-timeline">
        <h4><?php _e('Activity Timeline', 'kilismile'); ?></h4>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h5><?php _e('Message Submitted', 'kilismile'); ?></h5>
                    <p><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($submission->submitted_at))); ?></p>
                </div>
            </div>
            
            <?php if ($submission->status !== 'new') : ?>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h5><?php _e('Status Updated', 'kilismile'); ?></h5>
                        <p><?php printf(__('Changed to: %s', 'kilismile'), '<strong>' . esc_html(ucfirst($submission->status)) . '</strong>'); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($submission->replied) : ?>
                <div class="timeline-item">
                    <div class="timeline-marker replied"></div>
                    <div class="timeline-content">
                        <h5><?php _e('Reply Sent', 'kilismile'); ?></h5>
                        <p><?php _e('Admin replied to this message', 'kilismile'); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.submission-details-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.submission-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.submitter-info {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
}

.submitter-info .avatar {
    flex-shrink: 0;
}

.submitter-info .avatar img {
    border-radius: 50%;
    border: 3px solid #4CAF50;
}

.submitter-info .info h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.3rem;
}

.submitter-info .info p {
    margin: 5px 0;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
}

.submitter-info .info i {
    color: #4CAF50;
    width: 16px;
}

.submitter-info .info a {
    color: #4CAF50;
    text-decoration: none;
}

.submitter-info .info a:hover {
    text-decoration: underline;
}

.submission-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    background: #f9f9f9;
    padding: 15px;
    border-radius: 8px;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.meta-item label {
    font-weight: 600;
    color: #555;
    font-size: 0.9rem;
}

.meta-item span {
    color: #333;
}

.meta-item .user-agent {
    font-size: 0.8rem;
    color: #777;
    word-break: break-all;
}

.submission-content {
    margin-bottom: 30px;
}

.subject-section, .message-section {
    margin-bottom: 25px;
}

.subject-section h4, .message-section h4 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.1rem;
}

.subject-section .subject {
    font-size: 1.1rem;
    font-weight: 600;
    color: #4CAF50;
    margin: 0;
    padding: 10px 15px;
    background: #f0f8f0;
    border-left: 4px solid #4CAF50;
    border-radius: 0 8px 8px 0;
}

.message-content {
    background: white;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    line-height: 1.6;
    color: #333;
    font-size: 1rem;
}

.submission-actions {
    border-top: 1px solid #eee;
    padding-top: 25px;
    margin-top: 25px;
}

.status-update {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.inline-form {
    display: flex;
    align-items: end;
    gap: 15px;
    flex-wrap: wrap;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.form-group label {
    font-weight: 600;
    color: #555;
    font-size: 0.9rem;
}

.form-group select,
.form-group input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 25px;
}

.quick-email-section {
    background: #f0f8f0;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #4CAF50;
}

.quick-email-section h4 {
    color: #2E7D32;
    margin: 0 0 15px 0;
}

.quick-email-form .form-group {
    margin-bottom: 15px;
}

.quick-email-form input,
.quick-email-form textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
}

.quick-email-form textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.submission-timeline {
    border-top: 1px solid #eee;
    padding-top: 25px;
    margin-top: 25px;
}

.submission-timeline h4 {
    color: #333;
    margin: 0 0 20px 0;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 0;
    width: 16px;
    height: 16px;
    background: #4CAF50;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #4CAF50;
}

.timeline-marker.replied {
    background: #2196F3;
    box-shadow: 0 0 0 2px #2196F3;
}

.timeline-content h5 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 1rem;
}

.timeline-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.button i {
    margin-right: 6px;
}

@media (max-width: 768px) {
    .submitter-info {
        flex-direction: column;
        text-align: center;
    }
    
    .submission-meta {
        grid-template-columns: 1fr;
    }
    
    .inline-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    
    // Copy email to clipboard
    $('.copy-email').on('click', function() {
        const email = $(this).data('email');
        navigator.clipboard.writeText(email).then(function() {
            alert('Email copied to clipboard!');
        });
    });
    
    // Quick email form
    $('.quick-email-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'send_quick_email_reply',
                submission_id: form.data('submission-id'),
                subject: form.find('#quick_subject').val(),
                message: form.find('#quick_message').val(),
                mark_replied: form.find('input[name="mark_replied"]').is(':checked') ? 1 : 0,
                nonce: '<?php echo wp_create_nonce('kilismile_contact_admin'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Email sent successfully!');
                    location.reload();
                } else {
                    alert('Error sending email: ' + response.data);
                }
            },
            error: function() {
                alert('Error sending email. Please try again.');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Delete submission
    $('.delete-submission').on('click', function() {
        if (!confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
            return;
        }
        
        const submissionId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_contact_submission',
                submission_id: submissionId,
                nonce: '<?php echo wp_create_nonce('kilismile_contact_admin'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Submission deleted successfully.');
                    $('#submission-modal').hide();
                    location.reload();
                } else {
                    alert('Error deleting submission.');
                }
            }
        });
    });
    
});
</script>

<?php

// AJAX handler for quick email reply
add_action('wp_ajax_send_quick_email_reply', 'kilismile_ajax_send_quick_email_reply');

function kilismile_ajax_send_quick_email_reply() {
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_contact_admin') || !current_user_can('manage_options')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    $submission_id = intval($_POST['submission_id']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);
    $mark_replied = intval($_POST['mark_replied']);
    
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    $submission = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d", 
        $submission_id
    ));
    
    if (!$submission) {
        wp_send_json_error('Submission not found');
    }
    
    // Send email using enhanced system if available
    if (function_exists('kilismile_get_email_system')) {
        $email_system = kilismile_get_email_system();
        $email_sent = $email_system->send_email(
            $submission->email,
            $subject,
            $message,
            array('name' => $submission->name)
        );
    } else {
        // Fallback to wp_mail
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: Kilismile Organization <' . get_theme_mod('kilismile_email', 'kilismile21@gmail.com') . '>'
        );
        $email_sent = wp_mail($submission->email, $subject, $message, $headers);
    }
    
    if ($email_sent) {
        if ($mark_replied) {
            $wpdb->update(
                $table_name,
                array('replied' => 1, 'status' => 'replied'),
                array('id' => $submission_id),
                array('%d', '%s'),
                array('%d')
            );
        }
        wp_send_json_success('Email sent successfully');
    } else {
        wp_send_json_error('Failed to send email');
    }
}

// AJAX handler for deleting submission
add_action('wp_ajax_delete_contact_submission', 'kilismile_ajax_delete_contact_submission');

function kilismile_ajax_delete_contact_submission() {
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_contact_admin') || !current_user_can('manage_options')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    $submission_id = intval($_POST['submission_id']);
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    $result = $wpdb->delete(
        $table_name,
        array('id' => $submission_id),
        array('%d')
    );
    
    if ($result) {
        wp_send_json_success('Submission deleted successfully');
    } else {
        wp_send_json_error('Failed to delete submission');
    }
}

?>


