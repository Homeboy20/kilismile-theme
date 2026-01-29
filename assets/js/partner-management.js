/**
 * Partner Management JavaScript
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    'use strict';
    
    let partnerIndex = $('.partner-item').length;
    
    // Initialize sortable partners
    if (typeof $.fn.sortable !== 'undefined') {
        $('#partners-container').sortable({
            handle: '.partner-item',
            placeholder: 'partner-placeholder',
            helper: 'clone',
            opacity: 0.8,
            update: function(event, ui) {
                updateDisplayOrder();
            }
        });
    }
    
    // Add new partner
    $('#add-partner').on('click', function() {
        const template = $('#partner-item-template').html();
        const html = template.replace(/\{\{INDEX\}\}/g, partnerIndex);
        $('#partners-container').append(html);
        partnerIndex++;
        
        // Scroll to new partner
        $('html, body').animate({
            scrollTop: $('.partner-item:last').offset().top - 100
        }, 500);
    });
    
    // Remove partner with confirmation
    $(document).on('click', '.remove-partner', function() {
        const partnerName = $(this).closest('.partner-item').find('input[name*="[name]"]').val();
        const confirmText = partnerName ? 
            `Are you sure you want to remove "${partnerName}"?` : 
            'Are you sure you want to remove this partner?';
            
        if (confirm(confirmText)) {
            $(this).closest('.partner-item').fadeOut(300, function() {
                $(this).remove();
                updateDisplayOrder();
            });
        }
    });
    
    // Logo upload functionality
    $(document).on('click', '.upload-logo-btn', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const preview = button.siblings('.logo-preview');
        const input = button.siblings('input[type="hidden"]');
        const removeBtn = button.siblings('.remove-logo-btn');
        
        // Create media uploader
        const mediaUploader = wp.media({
            title: 'Select Partner Logo',
            button: {
                text: 'Use This Logo'
            },
            library: {
                type: 'image'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Validate image dimensions
            if (attachment.width < 100 || attachment.height < 50) {
                alert('Logo should be at least 100x50 pixels for optimal display.');
                return;
            }
            
            // Update preview and input
            preview.attr('src', attachment.url).show();
            input.val(attachment.url);
            button.text('Change Logo');
            
            // Show remove button
            if (removeBtn.length === 0) {
                button.after('<br><button type="button" class="button remove-logo-btn" style="color: #dc3545; margin-top: 5px;">Remove Logo</button>');
            } else {
                removeBtn.show();
            }
            
            // Show success message
            showNotification('Logo uploaded successfully!', 'success');
        });
        
        mediaUploader.open();
    });
    
    // Remove logo
    $(document).on('click', '.remove-logo-btn', function() {
        const button = $(this);
        const preview = button.siblings('.logo-preview');
        const input = button.siblings('input[type="hidden"]');
        const uploadBtn = button.siblings('.upload-logo-btn');
        
        if (confirm('Are you sure you want to remove this logo?')) {
            preview.hide();
            input.val('');
            uploadBtn.text('Upload Logo');
            button.hide();
            
            showNotification('Logo removed successfully!', 'info');
        }
    });
    
    // Auto-save draft functionality
    let autoSaveTimeout;
    $(document).on('input change', 'input, textarea, select', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            saveDraft();
        }, 2000);
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let hasErrors = false;
        const errors = [];
        
        // Check for required fields
        $('.partner-item').each(function(index) {
            const partnerName = $(this).find('input[name*="[name]"]').val().trim();
            const logoUrl = $(this).find('input[name*="[logo_url]"]').val().trim();
            
            if (!partnerName) {
                errors.push(`Partner ${index + 1}: Name is required`);
                hasErrors = true;
            }
            
            if (!logoUrl) {
                const warning = confirm(`Partner "${partnerName}" doesn't have a logo. Continue anyway?`);
                if (!warning) {
                    hasErrors = true;
                }
            }
        });
        
        if (hasErrors && errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        // Show saving indicator
        const submitBtn = $(this).find('input[type="submit"]');
        const originalText = submitBtn.val();
        submitBtn.val('Saving Partners...').prop('disabled', true);
        
        // Re-enable after form submission
        setTimeout(function() {
            submitBtn.val(originalText).prop('disabled', false);
        }, 3000);
    });
    
    // Update display order based on current position
    function updateDisplayOrder() {
        $('.partner-item').each(function(index) {
            $(this).find('input[name*="[display_order]"]').val(index + 1);
        });
    }
    
    // Save draft to localStorage
    function saveDraft() {
        const formData = $('form').serializeArray();
        const category = $('input[name="partner_category"]').val();
        localStorage.setItem(`kilismile_partners_draft_${category}`, JSON.stringify(formData));
        
        showNotification('Draft saved automatically', 'info', 2000);
    }
    
    // Load draft from localStorage
    function loadDraft() {
        const category = $('input[name="partner_category"]').val();
        const draft = localStorage.getItem(`kilismile_partners_draft_${category}`);
        
        if (draft && confirm('Load previously saved draft?')) {
            const formData = JSON.parse(draft);
            formData.forEach(function(field) {
                $(`[name="${field.name}"]`).val(field.value);
            });
            
            showNotification('Draft loaded successfully!', 'success');
        }
    }
    
    // Clear draft after successful save
    $(document).on('submit', 'form', function() {
        const category = $('input[name="partner_category"]').val();
        localStorage.removeItem(`kilismile_partners_draft_${category}`);
    });
    
    // Bulk operations
    $('#bulk-actions').on('change', function() {
        const action = $(this).val();
        const checkedPartners = $('.partner-checkbox:checked');
        
        if (action && checkedPartners.length > 0) {
            switch (action) {
                case 'delete':
                    if (confirm(`Delete ${checkedPartners.length} selected partners?`)) {
                        checkedPartners.closest('.partner-item').fadeOut(300, function() {
                            $(this).remove();
                            updateDisplayOrder();
                        });
                    }
                    break;
                case 'feature':
                    checkedPartners.closest('.partner-item').find('input[name*="[featured]"]').prop('checked', true);
                    showNotification(`${checkedPartners.length} partners marked as featured`, 'success');
                    break;
                case 'unfeature':
                    checkedPartners.closest('.partner-item').find('input[name*="[featured]"]').prop('checked', false);
                    showNotification(`${checkedPartners.length} partners unmarked as featured`, 'info');
                    break;
            }
            $(this).val('');
        }
    });
    
    // Show notification
    function showNotification(message, type = 'info', duration = 3000) {
        const colors = {
            success: '#27ae60',
            error: '#e74c3c',
            warning: '#f39c12',
            info: '#3498db'
        };
        
        const notification = $(`
            <div style="
                position: fixed;
                top: 30px;
                right: 30px;
                background: ${colors[type]};
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 10000;
                font-weight: 600;
                max-width: 300px;
            ">
                ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // Load draft on page load
    if ($('.partner-item').length === 0) {
        loadDraft();
    }
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+S to save
        if (e.ctrlKey && e.which === 83) {
            e.preventDefault();
            $('form').submit();
        }
        
        // Ctrl+N to add new partner
        if (e.ctrlKey && e.which === 78) {
            e.preventDefault();
            $('#add-partner').click();
        }
    });
    
    // Image drag and drop support
    $('.logo-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    $('.logo-upload-area').on('dragleave', function(e) {
        $(this).removeClass('dragover');
    });
    
    $('.logo-upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                // Handle file upload
                uploadImageFile(file, $(this));
            } else {
                alert('Please drop an image file.');
            }
        }
    });
    
    // Upload image file
    function uploadImageFile(file, container) {
        const formData = new FormData();
        formData.append('partner_logo', file);
        formData.append('action', 'kilismile_upload_partner_logo');
        formData.append('nonce', kilismile_ajax.nonce);
        
        $.ajax({
            url: kilismile_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    const preview = container.find('.logo-preview');
                    const input = container.find('input[type="hidden"]');
                    const button = container.find('.upload-logo-btn');
                    
                    preview.attr('src', response.data.url).show();
                    input.val(response.data.url);
                    button.text('Change Logo');
                    
                    showNotification('Logo uploaded successfully!', 'success');
                } else {
                    showNotification('Upload failed: ' + response.data, 'error');
                }
            },
            error: function() {
                showNotification('Upload failed. Please try again.', 'error');
            }
        });
    }
    
    console.log('Partner Management System initialized successfully');
});


