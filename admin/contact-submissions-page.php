<?php
/**
 * Contact Submissions Admin Page
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Contact Form Submissions', 'kilismile'); ?></h1>
    
    <div class="kilismile-admin-stats">
        <div class="stat-card">
            <h3><?php echo number_format($total); ?></h3>
            <p><?php _e('Total Submissions', 'kilismile'); ?></p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format($wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'")); ?></h3>
            <p><?php _e('New Messages', 'kilismile'); ?></p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format($wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE replied = 1")); ?></h3>
            <p><?php _e('Replied', 'kilismile'); ?></p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format($wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")); ?></h3>
            <p><?php _e('This Week', 'kilismile'); ?></p>
        </div>
    </div>
    
    <?php if (empty($submissions)) : ?>
        <div class="no-submissions">
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x"></i>
                <h3><?php _e('No contact submissions yet', 'kilismile'); ?></h3>
                <p><?php _e('Contact form submissions will appear here when visitors send messages through your contact form.', 'kilismile'); ?></p>
                <p><strong><?php _e('Shortcode:', 'kilismile'); ?></strong> <code>[kilismile_contact_form]</code></p>
            </div>
        </div>
    <?php else : ?>
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <select name="bulk_action" id="bulk-action-selector-top">
                    <option value=""><?php _e('Bulk Actions', 'kilismile'); ?></option>
                    <option value="mark_read"><?php _e('Mark as Read', 'kilismile'); ?></option>
                    <option value="mark_replied"><?php _e('Mark as Replied', 'kilismile'); ?></option>
                    <option value="delete"><?php _e('Delete', 'kilismile'); ?></option>
                </select>
                <button type="button" class="button action"><?php _e('Apply', 'kilismile'); ?></button>
            </div>
            
            <div class="tablenav-pages">
                <?php
                $total_pages = ceil($total / $per_page);
                if ($total_pages > 1) {
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total' => $total_pages,
                        'current' => $page
                    ));
                }
                ?>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-1">
                    </td>
                    <th class="manage-column column-name"><?php _e('Name', 'kilismile'); ?></th>
                    <th class="manage-column column-email"><?php _e('Email', 'kilismile'); ?></th>
                    <th class="manage-column column-subject"><?php _e('Subject', 'kilismile'); ?></th>
                    <th class="manage-column column-interest"><?php _e('Interest', 'kilismile'); ?></th>
                    <th class="manage-column column-status"><?php _e('Status', 'kilismile'); ?></th>
                    <th class="manage-column column-date"><?php _e('Date', 'kilismile'); ?></th>
                    <th class="manage-column column-actions"><?php _e('Actions', 'kilismile'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission) : ?>
                    <tr class="submission-row status-<?php echo esc_attr($submission->status); ?>" 
                        data-id="<?php echo esc_attr($submission->id); ?>">
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="submission[]" value="<?php echo esc_attr($submission->id); ?>">
                        </th>
                        <td class="column-name">
                            <strong><?php echo esc_html($submission->name); ?></strong>
                            <?php if ($submission->organization) : ?>
                                <br><small class="organization"><?php echo esc_html($submission->organization); ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="column-email">
                            <a href="mailto:<?php echo esc_attr($submission->email); ?>">
                                <?php echo esc_html($submission->email); ?>
                            </a>
                            <?php if ($submission->phone) : ?>
                                <br><small><?php echo esc_html($submission->phone); ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="column-subject">
                            <div class="subject-preview">
                                <?php echo esc_html($submission->subject); ?>
                            </div>
                            <div class="message-preview">
                                <?php echo esc_html(wp_trim_words($submission->message, 15)); ?>
                            </div>
                        </td>
                        <td class="column-interest">
                            <?php if ($submission->interest) : ?>
                                <span class="interest-badge interest-<?php echo esc_attr($submission->interest); ?>">
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $submission->interest))); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="column-status">
                            <span class="status-badge status-<?php echo esc_attr($submission->status); ?>">
                                <?php echo esc_html(ucfirst($submission->status)); ?>
                            </span>
                            <?php if ($submission->replied) : ?>
                                <br><span class="replied-badge"><?php _e('Replied', 'kilismile'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="column-date">
                            <?php echo esc_html(date('M j, Y', strtotime($submission->submitted_at))); ?>
                            <br><small><?php echo esc_html(date('g:i A', strtotime($submission->submitted_at))); ?></small>
                        </td>
                        <td class="column-actions">
                            <button type="button" class="button button-small view-submission" 
                                    data-id="<?php echo esc_attr($submission->id); ?>">
                                <?php _e('View', 'kilismile'); ?>
                            </button>
                            <a href="mailto:<?php echo esc_attr($submission->email); ?>?subject=Re: <?php echo esc_attr($submission->subject); ?>" 
                               class="button button-small">
                                <?php _e('Reply', 'kilismile'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php endif; ?>
    
    <!-- Submission Detail Modal -->
    <div id="submission-modal" class="kilismile-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php _e('Contact Submission Details', 'kilismile'); ?></h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="submission-details">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kilismile-admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0 30px 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 4px solid #4CAF50;
}

.stat-card h3 {
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    color: #4CAF50;
    font-weight: bold;
}

.stat-card p {
    margin: 0;
    color: #666;
    font-weight: 500;
}

.no-submissions {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    color: #ccc;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #666;
    margin-bottom: 10px;
}

.empty-state p {
    color: #999;
    max-width: 500px;
    margin: 0 auto 10px auto;
}

.submission-row.status-new {
    background-color: #f9f9f9;
    font-weight: 600;
}

.column-name .organization {
    color: #666;
    font-style: italic;
}

.subject-preview {
    font-weight: 600;
    margin-bottom: 5px;
}

.message-preview {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
}

.interest-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.interest-volunteer { background: #e3f2fd; color: #1976d2; }
.interest-partnership { background: #fce4ec; color: #c2185b; }
.interest-donation { background: #e8f5e8; color: #388e3c; }
.interest-programs { background: #fff3e0; color: #f57c00; }
.interest-media { background: #f3e5f5; color: #7b1fa2; }
.interest-general { background: #f5f5f5; color: #616161; }
.interest-other { background: #e0f2f1; color: #00796b; }

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-new { background: #e3f2fd; color: #1976d2; }
.status-read { background: #e8f5e8; color: #388e3c; }
.status-replied { background: #f3e5f5; color: #7b1fa2; }
.status-closed { background: #f5f5f5; color: #616161; }

.replied-badge {
    display: inline-block;
    padding: 2px 6px;
    background: #4caf50;
    color: white;
    font-size: 0.7rem;
    border-radius: 8px;
    margin-top: 4px;
}

.kilismile-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9f9f9;
    border-radius: 8px 8px 0 0;
}

.modal-header h2 {
    margin: 0;
    color: #333;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 30px;
}

.button.button-small {
    margin-right: 5px;
}

@media (max-width: 768px) {
    .kilismile-admin-stats {
        grid-template-columns: 1fr 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 10px auto;
    }
    
    .modal-body {
        padding: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    
    // View submission modal
    $('.view-submission').on('click', function() {
        const submissionId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_contact_submission',
                submission_id: submissionId,
                nonce: '<?php echo wp_create_nonce('kilismile_contact_admin'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#submission-details').html(response.data);
                    $('#submission-modal').show();
                } else {
                    alert('Error loading submission details.');
                }
            }
        });
    });
    
    // Close modal
    $('.close, .kilismile-modal').on('click', function(e) {
        if (e.target === this) {
            $('#submission-modal').hide();
        }
    });
    
    // Escape key to close modal
    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            $('#submission-modal').hide();
        }
    });
    
    // Select all checkbox
    $('#cb-select-all-1').change(function() {
        $('input[name="submission[]"]').prop('checked', this.checked);
    });
    
    // Bulk actions
    $('.action').on('click', function() {
        const action = $('#bulk-action-selector-top').val();
        const selected = $('input[name="submission[]"]:checked').map(function() {
            return this.value;
        }).get();
        
        if (!action || selected.length === 0) {
            alert('Please select an action and at least one submission.');
            return;
        }
        
        if (action === 'delete' && !confirm('Are you sure you want to delete the selected submissions?')) {
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bulk_contact_action',
                bulk_action: action,
                submissions: selected,
                nonce: '<?php echo wp_create_nonce('kilismile_contact_admin'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error performing bulk action.');
                }
            }
        });
    });
    
});
</script>

<?php

// AJAX handler for getting submission details
add_action('wp_ajax_get_contact_submission', 'kilismile_ajax_get_contact_submission');

function kilismile_ajax_get_contact_submission() {
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_contact_admin') || !current_user_can('manage_options')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    $submission_id = intval($_POST['submission_id']);
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    
    $submission = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d", 
        $submission_id
    ));
    
    if (!$submission) {
        wp_send_json_error('Submission not found');
    }
    
    // Mark as read
    $wpdb->update(
        $table_name,
        array('status' => 'read'),
        array('id' => $submission_id),
        array('%s'),
        array('%d')
    );
    
    ob_start();
    include get_template_directory() . '/admin/contact-submission-details.php';
    $content = ob_get_clean();
    
    wp_send_json_success($content);
}

// AJAX handler for bulk actions
add_action('wp_ajax_bulk_contact_action', 'kilismile_ajax_bulk_contact_action');

function kilismile_ajax_bulk_contact_action() {
    if (!wp_verify_nonce($_POST['nonce'], 'kilismile_contact_admin') || !current_user_can('manage_options')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'kilismile_contact_submissions';
    $action = sanitize_text_field($_POST['bulk_action']);
    $submissions = array_map('intval', $_POST['submissions']);
    
    if (empty($submissions)) {
        wp_send_json_error('No submissions selected');
    }
    
    $placeholders = implode(',', array_fill(0, count($submissions), '%d'));
    
    switch ($action) {
        case 'mark_read':
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET status = 'read' WHERE id IN ($placeholders)",
                $submissions
            ));
            break;
            
        case 'mark_replied':
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET replied = 1, status = 'replied' WHERE id IN ($placeholders)",
                $submissions
            ));
            break;
            
        case 'delete':
            $wpdb->query($wpdb->prepare(
                "DELETE FROM $table_name WHERE id IN ($placeholders)",
                $submissions
            ));
            break;
    }
    
    wp_send_json_success('Action completed successfully');
}

?>


