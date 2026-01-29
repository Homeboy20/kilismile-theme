jQuery(document).ready(function($) {
    // WordPress Media Uploader for Images
    var mediaUploader;
    
    $('#upload-gallery-btn').click(function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Gallery Images',
            button: {
                text: 'Choose Images'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            var imageUrls = $('#gallery_images').val().split(',').filter(url => url.trim());
            
            attachments.forEach(function(attachment) {
                imageUrls.push(attachment.url);
                addImagePreview(attachment.url);
            });
            
            $('#gallery_images').val(imageUrls.join(','));
        });
        
        mediaUploader.open();
    });
    
    // WordPress Media Uploader for Files
    var fileUploader;
    
    $('#upload-files-btn').click(function(e) {
        e.preventDefault();
        
        if (fileUploader) {
            fileUploader.open();
            return;
        }
        
        fileUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Files for Download',
            button: {
                text: 'Choose Files'
            },
            multiple: true,
            library: {
                type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain']
            }
        });
        
        fileUploader.on('select', function() {
            var attachments = fileUploader.state().get('selection').toJSON();
            var currentDownloads = $('#downloads_field').val().split('\n').filter(line => line.trim());
            
            attachments.forEach(function(attachment) {
                var title = attachment.title || attachment.filename;
                var newDownload = attachment.url + '|' + title;
                currentDownloads.push(newDownload);
                addFilePreview(attachment.url, title, attachment.filename);
            });
            
            $('#downloads_field').val(currentDownloads.join('\n'));
        });
        
        fileUploader.open();
    });
    
    // Add image preview
    function addImagePreview(url) {
        var preview = $('<div class="image-preview" style="position:relative;width:80px;height:80px;">' +
            '<img src="' + url + '" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">' +
            '<button type="button" class="remove-image" style="position:absolute;top:-8px;right:-8px;background:#e74c3c;color:white;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;">×</button>' +
            '</div>');
        
        $('#image-previews').append(preview);
    }
    
    // Add file preview
    function addFilePreview(url, title, filename) {
        var extension = url.split('.').pop().toLowerCase();
        var icon = 'dashicons-media-document';
        if (extension === 'pdf') icon = 'dashicons-pdf';
        
        var preview = $('<div class="file-preview" style="display:flex;align-items:center;background:#f8f9fa;padding:12px;border-radius:8px;margin-bottom:8px;">' +
            '<span class="dashicons ' + icon + '" style="font-size:1.5em;color:#8e44ad;margin-right:12px;"></span>' +
            '<div style="flex:1;">' +
                '<strong>' + title + '</strong><br>' +
                '<small style="color:#666;">' + filename + '</small>' +
            '</div>' +
            '<button type="button" class="remove-file" data-url="' + url + '" style="background:#e74c3c;color:white;border:none;border-radius:4px;padding:4px 8px;cursor:pointer;">Remove</button>' +
            '</div>');
        
        $('#file-previews').append(preview);
    }
    
    // Remove image
    $(document).on('click', '.remove-image', function() {
        var $preview = $(this).parent();
        var imageUrl = $preview.find('img').attr('src');
        var imageUrls = $('#gallery_images').val().split(',').filter(url => url.trim() !== imageUrl);
        
        $('#gallery_images').val(imageUrls.join(','));
        $preview.remove();
    });
    
    // Remove file
    $(document).on('click', '.remove-file', function() {
        var $preview = $(this).parent();
        var fileUrl = $(this).data('url');
        var currentDownloads = $('#downloads_field').val().split('\n').filter(line => !line.includes(fileUrl));
        
        $('#downloads_field').val(currentDownloads.join('\n'));
        $preview.remove();
    });
    
    // Drag and drop functionality for images
    $('#image-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).css('background', '#e8f5e8');
    });
    
    $('#image-upload-area').on('dragleave', function(e) {
        e.preventDefault();
        $(this).css('background', '#f9f9f9');
    });
    
    $('#image-upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).css('background', '#f9f9f9');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#upload-gallery-btn').click(); // Open media library
        }
    });
    
    // Drag and drop functionality for files
    $('#file-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).css('background', '#f0e8ff');
    });
    
    $('#file-upload-area').on('dragleave', function(e) {
        e.preventDefault();
        $(this).css('background', '#f9f9f9');
    });
    
    $('#file-upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).css('background', '#f9f9f9');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#upload-files-btn').click(); // Open media library
        }
    });
});


