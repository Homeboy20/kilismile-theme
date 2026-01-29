/**
 * KiliSmile Gallery Management Admin Scripts
 */

jQuery(document).ready(function($) {
    
    // Bulk upload functionality
    $('#bulk-upload-btn').on('click', function(e) {
        e.preventDefault();
        
        var frame = wp.media({
            title: 'Select Multiple Gallery Images',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true
        });
        
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var category = $('#bulk-category').val();
            var featured = $('#bulk-featured').is(':checked');
            
            processBulkUpload(attachments, category, featured);
        });
        
        frame.open();
    });
    
    // Process bulk upload
    function processBulkUpload(attachments, category, featured) {
        var $progress = $('#bulk-upload-progress');
        var $progressFill = $('.progress-fill');
        var $progressText = $('#bulk-progress-text');
        
        $progress.show();
        $progressFill.css('width', '0%');
        $progressText.text('Starting bulk upload...');
        
        // Find available slots
        var availableSlots = [];
        for (var i = 1; i <= 20; i++) {
            if (!$('#kilismile_gallery_image_' + i).val()) {
                availableSlots.push(i);
            }
        }
        
        if (availableSlots.length === 0) {
            alert('No available gallery slots. Please remove some images first.');
            $progress.hide();
            return;
        }
        
        var itemsToProcess = Math.min(attachments.length, availableSlots.length);
        var processed = 0;
        
        $progressText.text('Processing ' + itemsToProcess + ' images...');
        
        attachments.slice(0, itemsToProcess).forEach(function(attachment, index) {
            var slotIndex = availableSlots[index];
            
            setTimeout(function() {
                // Fill the slot with image data
                $('#kilismile_gallery_image_' + slotIndex).val(attachment.id);
                $('input[name="kilismile_gallery_title_' + slotIndex + '"]').val(attachment.title || attachment.filename || 'Gallery Image ' + slotIndex);
                $('textarea[name="kilismile_gallery_description_' + slotIndex + '"]').val(attachment.description || attachment.caption || 'Uploaded via bulk upload');
                $('select[name="kilismile_gallery_category_' + slotIndex + '"]').val(category);
                
                if (featured) {
                    $('input[name="kilismile_gallery_featured_' + slotIndex + '"]').prop('checked', true);
                }
                
                // Show preview
                var imageUrl = attachment.sizes && attachment.sizes.medium ? 
                              attachment.sizes.medium.url : attachment.url;
                
                $('#preview_kilismile_gallery_image_' + slotIndex).html(
                    '<img src="' + imageUrl + '" style="max-width: 200px; height: auto; margin-top: 10px; border-radius: 4px;" />'
                );
                
                // Show remove button
                $('.remove-image-button[data-target="kilismile_gallery_image_' + slotIndex + '"]').show();
                
                // Update progress
                processed++;
                var progressPercent = (processed / itemsToProcess) * 100;
                $progressFill.css('width', progressPercent + '%');
                $progressText.text('Processed ' + processed + ' of ' + itemsToProcess + ' images');
                
                // Hide progress when done
                if (processed === itemsToProcess) {
                    setTimeout(function() {
                        $progress.hide();
                        updateImageCounter();
                        
                        // Show success message
                        $('<div class="notice notice-success" style="margin: 20px 0; padding: 10px;"><p>' + 
                          processed + ' images added successfully! Remember to save your changes.</p></div>')
                        .insertAfter('.bulk-upload-section').delay(5000).fadeOut();
                    }, 1000);
                }
            }, index * 300); // Stagger the updates
        });
        
        if (attachments.length > availableSlots.length) {
            setTimeout(function() {
                alert('Only ' + availableSlots.length + ' images could be added. ' + 
                     (attachments.length - availableSlots.length) + ' images were skipped due to lack of available slots.');
            }, 2000);
        }
    }
    
    // Quick add functionality
    $('#quick-add-image-btn').on('click', function(e) {
        e.preventDefault();
        
        var frame = wp.media({
            title: 'Select Gallery Image',
            button: {
                text: 'Use This Image'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            $('#quick-add-image-id').val(attachment.id);
            $('#quick-add-title').val(attachment.title || attachment.filename || '');
            
            var imageUrl = attachment.sizes && attachment.sizes.medium ? 
                          attachment.sizes.medium.url : attachment.url;
            
            $('#quick-add-preview').html(
                '<img src="' + imageUrl + '" style="max-width: 100px; height: auto; border-radius: 4px;" />'
            );
            
            $('#quick-add-btn').prop('disabled', false);
        });
        
        frame.open();
    });
    
    // Quick add submit
    $('#quick-add-btn').on('click', function(e) {
        e.preventDefault();
        
        var imageId = $('#quick-add-image-id').val();
        var title = $('#quick-add-title').val();
        var description = $('#quick-add-description').val();
        var category = $('#quick-add-category').val();
        var featured = $('#quick-add-featured').is(':checked');
        
        if (!imageId) {
            alert('Please select an image first.');
            return;
        }
        
        // Find next available slot
        var availableSlot = null;
        for (var i = 1; i <= 20; i++) {
            if (!$('#kilismile_gallery_image_' + i).val()) {
                availableSlot = i;
                break;
            }
        }
        
        if (!availableSlot) {
            alert('No available gallery slots. Please remove some images first.');
            return;
        }
        
        // Fill the slot
        $('#kilismile_gallery_image_' + availableSlot).val(imageId);
        $('input[name="kilismile_gallery_title_' + availableSlot + '"]').val(title || 'Gallery Image ' + availableSlot);
        $('textarea[name="kilismile_gallery_description_' + availableSlot + '"]').val(description || 'Added via quick add');
        $('select[name="kilismile_gallery_category_' + availableSlot + '"]').val(category);
        
        if (featured) {
            $('input[name="kilismile_gallery_featured_' + availableSlot + '"]').prop('checked', true);
        }
        
        // Show preview in the slot
        var previewHtml = $('#quick-add-preview').html();
        $('#preview_kilismile_gallery_image_' + availableSlot).html(previewHtml);
        
        // Show remove button for the slot
        $('.remove-image-button[data-target="kilismile_gallery_image_' + availableSlot + '"]').show();
        
        // Clear quick add form
        $('#quick-add-image-id').val('');
        $('#quick-add-title').val('');
        $('#quick-add-description').val('');
        $('#quick-add-featured').prop('checked', false);
        $('#quick-add-preview').html('');
        $('#quick-add-btn').prop('disabled', true);
        
        updateImageCounter();
        
        // Show success message
        $('<div class="notice notice-success" style="margin: 10px 0; padding: 10px;"><p>' + 
          'Image added to slot ' + availableSlot + '! Remember to save your changes.</p></div>')
        .insertAfter('.quick-add-section').delay(3000).fadeOut();
        
        // Scroll to the added item
        $('html, body').animate({
            scrollTop: $('#preview_kilismile_gallery_image_' + availableSlot).offset().top - 100
        }, 1000);
    });
    
    // Media uploader for gallery images
    $('.upload-image-button').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var targetInput = button.data('target');
        var previewContainer = $('#preview_' + targetInput);
        var removeButton = button.siblings('.remove-image-button');
        
        // Create media frame
        var frame = wp.media({
            title: 'Select Gallery Image',
            button: {
                text: 'Use This Image'
            },
            multiple: false
        });
        
        // When image is selected
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            // Set the image ID
            $('#' + targetInput).val(attachment.id);
            
            // Show preview
            var imageUrl = attachment.sizes && attachment.sizes.medium ? 
                          attachment.sizes.medium.url : attachment.url;
            
            previewContainer.html('<img src="' + imageUrl + '" style="max-width: 200px; height: auto; margin-top: 10px; border-radius: 4px;" />');
            
            // Show remove button
            removeButton.show();
        });
        
        // Open media frame
        frame.open();
    });
    
    // Remove image functionality
    $('.remove-image-button').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var targetInput = button.data('target');
        var previewContainer = $('#preview_' + targetInput);
        
        // Clear the image ID
        $('#' + targetInput).val('');
        
        // Clear preview
        previewContainer.html('');
        
        // Hide remove button
        button.hide();
    });
    
    // Tab switching functionality
    window.switchTab = function(evt, tabName) {
        var i, tabcontent, tablinks;
        
        // Hide all tab content
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        
        // Remove active class from all tabs
        tablinks = document.getElementsByClassName("nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("nav-tab-active");
        }
        
        // Show selected tab content and mark tab as active
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("nav-tab-active");
        
        evt.preventDefault();
    };
    
    // Form validation
    $('form').on('submit', function(e) {
        var hasContent = false;
        
        // Check if at least one gallery item has an image
        for (var i = 1; i <= 20; i++) {
            if ($('#kilismile_gallery_image_' + i).val()) {
                hasContent = true;
                break;
            }
        }
        
        if (!hasContent) {
            if (!confirm('No gallery images have been selected. Are you sure you want to save?')) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Auto-save functionality (optional)
    var autoSaveTimer;
    $('input, textarea, select', '.kilismile-gallery-items').on('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Could implement auto-save here if needed
            console.log('Gallery data changed - consider implementing auto-save');
        }, 2000);
    });
    
    // Collapse/expand gallery items
    $('.gallery-item-panel h3').on('click', function() {
        var panel = $(this).parent();
        var content = panel.find('.form-table');
        
        content.slideToggle();
        panel.toggleClass('collapsed');
    });
    
    // Add collapse/expand all buttons
    if ($('.gallery-item-panel').length > 0) {
        $('.kilismile-gallery-items').prepend(
            '<div class="gallery-controls" style="margin-bottom: 20px;">' +
            '<button type="button" class="button" id="expand-all">Expand All</button> ' +
            '<button type="button" class="button" id="collapse-all">Collapse All</button>' +
            '</div>'
        );
        
        $('#expand-all').on('click', function() {
            $('.gallery-item-panel .form-table').slideDown();
            $('.gallery-item-panel').removeClass('collapsed');
        });
        
        $('#collapse-all').on('click', function() {
            $('.gallery-item-panel .form-table').slideUp();
            $('.gallery-item-panel').addClass('collapsed');
        });
    }
    
    // Image counter
    function updateImageCounter() {
        var count = 0;
        for (var i = 1; i <= 20; i++) {
            if ($('#kilismile_gallery_image_' + i).val()) {
                count++;
            }
        }
        
        $('#image-counter').remove();
        $('.wrap h1').after('<div id="image-counter" style="background: #e7f3ff; padding: 10px; border-radius: 4px; margin: 10px 0; color: #0073aa;"><strong>' + count + ' of 20</strong> gallery slots used</div>');
    }
    
    // Update counter on page load and when images change
    updateImageCounter();
    $('input[id^="kilismile_gallery_image_"]').on('change', updateImageCounter);
    
    // Add success message fade out
    $('.notice-success').delay(3000).fadeOut();
});