/**
 * Enhanced KiliSmile Theme Settings JavaScript
 * 
 * Interactive functionality for the theme settings panel
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

(function($) {
    'use strict';
    
    // Settings object
    const KiliSmileSettings = {
        
        // Initialize
        init: function() {
            this.bindEvents();
            this.initializeComponents();
            this.setupAutoSave();
            this.setupConditionalFields();
            this.loadSavedState();
        },
        
        // Bind events
        bindEvents: function() {
            // Tab navigation
            $('.nav-tab').on('click', this.switchTab);
            
            // Save settings
            $('#save-settings').on('click', this.saveSettings);
            
            // Reset settings
            $('#reset-settings').on('click', this.resetSettings);
            
            // Export settings
            $('#export-settings').on('click', this.exportSettings);
            
            // Import settings
            $('#import-settings').on('click', this.triggerImport);
            $('#import-file').on('change', this.importSettings);
            
            // Field interactions
            $(document).on('change', '.setting-field input, .setting-field select, .setting-field textarea', this.handleFieldChange);
            $(document).on('input', '.slider-input', this.updateSliderValue);
            $(document).on('click', '.radio-image-option', this.selectRadioImage);
            $(document).on('input', '.color-picker', this.updateColorPalette);
            $(document).on('click', '.repeater-add', this.addRepeaterItem);
            $(document).on('click', '.repeater-action.delete', this.removeRepeaterItem);
            $(document).on('click', '.checkbox-option', this.toggleCheckboxOption);
            
            // Typography preview
            $(document).on('change', '.typography-font select', this.updateTypographyPreview);
            
            // Keyboard shortcuts
            $(document).on('keydown', this.handleKeyboardShortcuts);
            
            // Window events
            $(window).on('beforeunload', this.handleBeforeUnload);
            $(window).on('hashchange', this.handleHashChange);
        },
        
        // Initialize components
        initializeComponents: function() {
            this.initColorPickers();
            this.initSliders();
            this.initCodeEditors();
            this.initTypographySelectors();
            this.initImageUploaders();
            this.updateAllPreviews();
        },
        
        // Switch tabs
        switchTab: function(e) {
            e.preventDefault();
            
            const $tab = $(this);
            const sectionId = $tab.data('section');
            
            // Update active states
            $('.nav-tab').removeClass('active');
            $tab.addClass('active');
            
            $('.settings-section').removeClass('active');
            $('#section-' + sectionId).addClass('active');
            
            // Update URL hash
            history.pushState(null, null, '#' + sectionId);
            
            // Save current section
            localStorage.setItem('kilismile_current_section', sectionId);
            
            // Trigger section change event
            $(document).trigger('kilismile:section_changed', sectionId);
        },
        
        // Save settings
        saveSettings: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const originalText = $button.html();
            
            // Show loading state
            $button.html('<span class="dashicons dashicons-update dashicons-spin"></span> ' + kilismileSettings.strings.saving);
            $button.prop('disabled', true);
            
            // Prepare form data
            const formData = new FormData($('#kilismile-settings-form')[0]);
            formData.append('action', 'kilismile_save_settings');
            formData.append('nonce', kilismileSettings.nonce);
            
            // Send AJAX request
            $.ajax({
                url: kilismileSettings.ajax_url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        KiliSmileSettings.showNotification(response.data.message, 'success');
                        $('#last-saved').text(new Date().toLocaleString());
                        KiliSmileSettings.setDirtyState(false);
                        
                        // Update live preview if available
                        KiliSmileSettings.updateLivePreview();
                    } else {
                        KiliSmileSettings.showNotification(response.data || kilismileSettings.strings.error, 'error');
                    }
                },
                error: function() {
                    KiliSmileSettings.showNotification(kilismileSettings.strings.error, 'error');
                },
                complete: function() {
                    $button.html(originalText);
                    $button.prop('disabled', false);
                }
            });
        },
        
        // Reset settings
        resetSettings: function(e) {
            e.preventDefault();
            
            if (!confirm(kilismileSettings.strings.confirm_reset)) {
                return;
            }
            
            const $button = $(this);
            const originalText = $button.html();
            
            $button.html('<span class="dashicons dashicons-update dashicons-spin"></span> Resetting...');
            $button.prop('disabled', true);
            
            $.ajax({
                url: kilismileSettings.ajax_url,
                method: 'POST',
                data: {
                    action: 'kilismile_reset_settings',
                    nonce: kilismileSettings.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KiliSmileSettings.showNotification(response.data.message, 'success');
                        location.reload();
                    } else {
                        KiliSmileSettings.showNotification(response.data || 'Error resetting settings', 'error');
                    }
                },
                error: function() {
                    KiliSmileSettings.showNotification('Error resetting settings', 'error');
                },
                complete: function() {
                    $button.html(originalText);
                    $button.prop('disabled', false);
                }
            });
        },
        
        // Export settings
        exportSettings: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const originalText = $button.html();
            
            $button.html('<span class="dashicons dashicons-update dashicons-spin"></span> Exporting...');
            $button.prop('disabled', true);
            
            $.ajax({
                url: kilismileSettings.ajax_url,
                method: 'POST',
                data: {
                    action: 'kilismile_export_settings',
                    nonce: kilismileSettings.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const blob = new Blob([response.data.data], { type: 'application/json' });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        
                        KiliSmileSettings.showNotification('Settings exported successfully!', 'success');
                    } else {
                        KiliSmileSettings.showNotification(response.data || 'Error exporting settings', 'error');
                    }
                },
                error: function() {
                    KiliSmileSettings.showNotification('Error exporting settings', 'error');
                },
                complete: function() {
                    $button.html(originalText);
                    $button.prop('disabled', false);
                }
            });
        },
        
        // Trigger import
        triggerImport: function(e) {
            e.preventDefault();
            $('#import-file').click();
        },
        
        // Import settings
        importSettings: function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            if (!confirm(kilismileSettings.strings.confirm_import)) {
                $(this).val('');
                return;
            }
            
            const formData = new FormData();
            formData.append('import_file', file);
            formData.append('action', 'kilismile_import_settings');
            formData.append('nonce', kilismileSettings.nonce);
            
            $.ajax({
                url: kilismileSettings.ajax_url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        KiliSmileSettings.showNotification(response.data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        KiliSmileSettings.showNotification(response.data || 'Error importing settings', 'error');
                    }
                },
                error: function() {
                    KiliSmileSettings.showNotification('Error importing settings', 'error');
                }
            });
            
            $(this).val('');
        },
        
        // Handle field changes
        handleFieldChange: function() {
            const $field = $(this);
            const $fieldContainer = $field.closest('.setting-field');
            
            // Mark as dirty
            KiliSmileSettings.setDirtyState(true);
            
            // Handle conditional fields
            KiliSmileSettings.updateConditionalFields($field);
            
            // Update previews
            KiliSmileSettings.updateFieldPreview($fieldContainer);
            
            // Trigger auto-save
            KiliSmileSettings.triggerAutoSave();
        },
        
        // Update slider value
        updateSliderValue: function() {
            const $slider = $(this);
            const value = $slider.val();
            const suffix = $slider.data('suffix') || '';
            
            $slider.closest('.field-content').find('.slider-value').text(value + suffix);
        },
        
        // Select radio image
        selectRadioImage: function() {
            const $option = $(this);
            const $container = $option.closest('.field-content');
            
            // Update visual state
            $container.find('.radio-image-option').removeClass('selected');
            $option.addClass('selected');
            
            // Update input value
            $option.find('input').prop('checked', true).trigger('change');
        },
        
        // Update color palette
        updateColorPalette: function() {
            const $colorPicker = $(this);
            const value = $colorPicker.val();
            const $textInput = $colorPicker.siblings('input[type="text"]');
            
            $textInput.val(value.toUpperCase());
        },
        
        // Add repeater item
        addRepeaterItem: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $container = $button.closest('.setting-field-repeater');
            const $template = $container.find('.repeater-item-template').first();
            const $itemsContainer = $container.find('.repeater-items');
            
            if ($template.length) {
                const $newItem = $template.clone().removeClass('repeater-item-template').addClass('repeater-item');
                const newIndex = $itemsContainer.find('.repeater-item').length;
                
                // Update field names and IDs
                $newItem.find('input, select, textarea').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    const id = $input.attr('id');
                    
                    if (name) {
                        $input.attr('name', name.replace('[0]', '[' + newIndex + ']'));
                    }
                    if (id) {
                        $input.attr('id', id.replace('_0', '_' + newIndex));
                    }
                });
                
                $itemsContainer.append($newItem);
                $newItem.hide().slideDown(300);
                
                // Initialize components in new item
                KiliSmileSettings.initializeComponentsInContainer($newItem);
            }
        },
        
        // Remove repeater item
        removeRepeaterItem: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $item = $button.closest('.repeater-item');
            
            $item.slideUp(300, function() {
                $(this).remove();
                KiliSmileSettings.triggerAutoSave();
            });
        },
        
        // Toggle checkbox option
        toggleCheckboxOption: function(e) {
            if (e.target.type === 'checkbox') return;
            
            const $option = $(this);
            const $checkbox = $option.find('input[type="checkbox"]');
            
            $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            $option.toggleClass('checked', $checkbox.prop('checked'));
        },
        
        // Update typography preview
        updateTypographyPreview: function() {
            const $select = $(this);
            const fontFamily = $select.val();
            const $preview = $select.closest('.typography-font').find('.font-preview');
            
            $preview.css('font-family', fontFamily);
            
            // Load font if needed
            if (fontFamily && !KiliSmileSettings.loadedFonts.includes(fontFamily)) {
                const link = document.createElement('link');
                link.href = `https://fonts.googleapis.com/css2?family=${fontFamily.replace(' ', '+')}:wght@400;600&display=swap`;
                link.rel = 'stylesheet';
                document.head.appendChild(link);
                
                KiliSmileSettings.loadedFonts.push(fontFamily);
            }
        },
        
        // Handle keyboard shortcuts
        handleKeyboardShortcuts: function(e) {
            // Ctrl+S to save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#save-settings').click();
            }
            
            // Ctrl+E to export
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                $('#export-settings').click();
            }
            
            // Tab navigation with arrow keys
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                const $currentTab = $('.nav-tab.active');
                const $tabs = $('.nav-tab');
                const currentIndex = $tabs.index($currentTab);
                let newIndex;
                
                if (e.key === 'ArrowLeft') {
                    newIndex = currentIndex > 0 ? currentIndex - 1 : $tabs.length - 1;
                } else {
                    newIndex = currentIndex < $tabs.length - 1 ? currentIndex + 1 : 0;
                }
                
                $tabs.eq(newIndex).click();
            }
        },
        
        // Handle before unload
        handleBeforeUnload: function(e) {
            if (KiliSmileSettings.isDirty) {
                const message = 'You have unsaved changes. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            }
        },
        
        // Handle hash change
        handleHashChange: function() {
            const hash = window.location.hash.substr(1);
            if (hash) {
                const $tab = $('.nav-tab[data-section="' + hash + '"]');
                if ($tab.length) {
                    $tab.click();
                }
            }
        },
        
        // Setup auto-save
        setupAutoSave: function() {
            this.autoSaveDelay = 3000; // 3 seconds
            this.autoSaveTimeout = null;
        },
        
        // Trigger auto-save
        triggerAutoSave: function() {
            clearTimeout(this.autoSaveTimeout);
            this.autoSaveTimeout = setTimeout(() => {
                this.performAutoSave();
            }, this.autoSaveDelay);
        },
        
        // Perform auto-save
        performAutoSave: function() {
            if (!this.isDirty) return;
            
            const formData = new FormData($('#kilismile-settings-form')[0]);
            formData.append('action', 'kilismile_save_settings');
            formData.append('nonce', kilismileSettings.nonce);
            formData.append('auto_save', '1');
            
            $.ajax({
                url: kilismileSettings.ajax_url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#last-saved').text(new Date().toLocaleString());
                        KiliSmileSettings.setDirtyState(false);
                        KiliSmileSettings.showNotification('Auto-saved', 'success', 2000);
                    }
                }
            });
        },
        
        // Setup conditional fields
        setupConditionalFields: function() {
            $(document).on('change', '[data-conditional-field]', function() {
                KiliSmileSettings.updateConditionalFields($(this));
            });
            
            // Initial check
            $('[data-conditional-field]').each(function() {
                KiliSmileSettings.updateConditionalFields($(this));
            });
        },
        
        // Update conditional fields
        updateConditionalFields: function($field) {
            const fieldId = $field.attr('id') || $field.attr('name');
            const fieldValue = $field.val();
            const fieldChecked = $field.is(':checked');
            
            $('[data-conditional-field="' + fieldId + '"]').each(function() {
                const $conditionalField = $(this);
                const requiredValue = $conditionalField.data('conditional-value');
                const requiredChecked = $conditionalField.data('conditional-checked');
                
                let shouldShow = false;
                
                if (requiredValue !== undefined) {
                    shouldShow = fieldValue == requiredValue;
                } else if (requiredChecked !== undefined) {
                    shouldShow = fieldChecked == requiredChecked;
                }
                
                if (shouldShow) {
                    $conditionalField.removeClass('conditional-hidden').addClass('conditional-show');
                } else {
                    $conditionalField.removeClass('conditional-show').addClass('conditional-hidden');
                }
            });
        },
        
        // Initialize color pickers
        initColorPickers: function() {
            $('.color-picker').each(function() {
                const $picker = $(this);
                if ($picker.hasClass('wp-color-picker')) return;
                
                $picker.wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        $(this).val(color).trigger('change');
                    }
                });
            });
        },
        
        // Initialize sliders
        initSliders: function() {
            $('.slider-input').each(function() {
                const $slider = $(this);
                const value = $slider.val();
                const suffix = $slider.data('suffix') || '';
                
                $slider.closest('.field-content').find('.slider-value').text(value + suffix);
            });
        },
        
        // Initialize code editors
        initCodeEditors: function() {
            $('.code-editor-textarea').each(function() {
                const $textarea = $(this);
                const language = $textarea.data('language') || 'css';
                const theme = $textarea.data('theme') || 'default';
                
                if (typeof wp !== 'undefined' && wp.codeEditor) {
                    const settings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                    settings.codemirror = _.extend({}, settings.codemirror, {
                        mode: language,
                        theme: theme,
                        lineNumbers: true,
                        lineWrapping: true,
                        tabSize: 2,
                        indentUnit: 2
                    });
                    
                    wp.codeEditor.initialize($textarea, settings);
                }
            });
        },
        
        // Initialize typography selectors
        initTypographySelectors: function() {
            $('.typography-font select').each(function() {
                KiliSmileSettings.updateTypographyPreview.call(this);
            });
        },
        
        // Initialize image uploaders
        initImageUploaders: function() {
            $('.image-upload-button').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const $input = $button.siblings('.image-upload-input');
                
                const frame = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use Image' },
                    multiple: false
                });
                
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.url).trigger('change');
                    $button.siblings('.image-preview').attr('src', attachment.url).show();
                });
                
                frame.open();
            });
        },
        
        // Initialize components in container
        initializeComponentsInContainer: function($container) {
            $container.find('.color-picker').each(function() {
                $(this).wpColorPicker();
            });
            
            $container.find('.code-editor-textarea').each(function() {
                const $textarea = $(this);
                const language = $textarea.data('language') || 'css';
                
                if (typeof wp !== 'undefined' && wp.codeEditor) {
                    wp.codeEditor.initialize($textarea);
                }
            });
        },
        
        // Update all previews
        updateAllPreviews: function() {
            $('.setting-field').each(function() {
                KiliSmileSettings.updateFieldPreview($(this));
            });
        },
        
        // Update field preview
        updateFieldPreview: function($field) {
            const fieldType = $field.attr('class').match(/setting-field-(\w+)/);
            if (!fieldType) return;
            
            switch (fieldType[1]) {
                case 'color':
                case 'color_palette':
                    this.updateColorPreview($field);
                    break;
                case 'typography':
                    this.updateTypographyPreview($field);
                    break;
                case 'slider':
                    this.updateSliderPreview($field);
                    break;
            }
        },
        
        // Update color preview
        updateColorPreview: function($field) {
            const $input = $field.find('.color-picker');
            const color = $input.val();
            
            // Update any preview elements
            $field.find('.color-preview').css('background-color', color);
        },
        
        // Update live preview
        updateLivePreview: function() {
            // Send message to preview window if open
            if (this.previewWindow && !this.previewWindow.closed) {
                this.previewWindow.postMessage({
                    type: 'kilismile_settings_updated',
                    settings: this.getFormData()
                }, '*');
            }
        },
        
        // Get form data
        getFormData: function() {
            const data = {};
            $('#kilismile-settings-form').serializeArray().forEach(function(item) {
                data[item.name] = item.value;
            });
            return data;
        },
        
        // Set dirty state
        setDirtyState: function(isDirty) {
            this.isDirty = isDirty;
            
            if (isDirty) {
                $('#save-settings').addClass('button-primary-dirty');
            } else {
                $('#save-settings').removeClass('button-primary-dirty');
            }
        },
        
        // Show notification
        showNotification: function(message, type, duration) {
            type = type || 'success';
            duration = duration || 4000;
            
            const $notification = $(`
                <div class="kilismile-notification ${type}">
                    <div class="notification-content">
                        <span class="notification-icon dashicons dashicons-${type === 'success' ? 'yes' : type === 'error' ? 'no' : 'warning'}"></span>
                        <span class="notification-message">${message}</span>
                        <button class="notification-close dashicons dashicons-dismiss"></button>
                    </div>
                </div>
            `);
            
            $('body').append($notification);
            
            // Show with animation
            setTimeout(() => $notification.addClass('show'), 100);
            
            // Auto-hide
            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => $notification.remove(), 300);
            }, duration);
            
            // Manual close
            $notification.find('.notification-close').on('click', function() {
                $notification.removeClass('show');
                setTimeout(() => $notification.remove(), 300);
            });
        },
        
        // Load saved state
        loadSavedState: function() {
            // Load current section
            const savedSection = localStorage.getItem('kilismile_current_section');
            if (savedSection) {
                const $tab = $('.nav-tab[data-section="' + savedSection + '"]');
                if ($tab.length) {
                    $tab.click();
                }
            }
            
            // Load from URL hash
            const hash = window.location.hash.substr(1);
            if (hash) {
                const $tab = $('.nav-tab[data-section="' + hash + '"]');
                if ($tab.length) {
                    $tab.click();
                }
            }
        },
        
        // State properties
        isDirty: false,
        autoSaveTimeout: null,
        autoSaveDelay: 3000,
        loadedFonts: [],
        previewWindow: null
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KiliSmileSettings.init();
    });
    
    // Make available globally
    window.KiliSmileSettings = KiliSmileSettings;
    
})(jQuery);


