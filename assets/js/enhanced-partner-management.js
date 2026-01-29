/**
 * Enhanced Partner Management JavaScript
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

(function($) {
    'use strict';
    
    const PartnerManagement = {
        
        init: function() {
            this.bindEvents();
            this.initializeComponents();
            this.loadPartners();
        },
        
        bindEvents: function() {
            // Search functionality
            $('#partner-search').on('input', this.debounce(this.handleSearch, 300));
            $('#search-btn').on('click', this.handleSearch);
            
            // Filter functionality
            $('#category-filter, #level-filter').on('change', this.handleFilter);
            
            // View toggle
            $('#grid-view, #list-view').on('click', this.handleViewToggle);
            
            // Bulk actions
            $('#apply-bulk-action').on('click', this.handleBulkAction);
            $('#select-all').on('click', this.selectAllPartners);
            $('#select-none').on('click', this.selectNonePartners);
            
            // Individual partner actions
            $(document).on('click', '.partner-action', this.handlePartnerAction);
            $(document).on('click', '.action-toggle', this.toggleActionDropdown);
            $(document).on('change', '.partner-checkbox', this.updateBulkActions);
            
            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').hide();
                }
            });
            
            // Real-time updates
            setInterval(this.refreshStats.bind(this), 30000); // Every 30 seconds
        },
        
        initializeComponents: function() {
            // Initialize sortable if needed
            if ($('#partners-container').hasClass('partners-grid')) {
                this.initSortable();
            }
            
            // Initialize tooltips
            this.initTooltips();
            
            // Initialize AOS animations
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 600,
                    easing: 'ease-in-out',
                    once: true
                });
            }
        },
        
        initSortable: function() {
            $('#partners-container').sortable({
                items: '.partner-admin-card',
                handle: '.partner-card-header',
                placeholder: 'partner-placeholder',
                update: this.handleOrderUpdate.bind(this)
            });
        },
        
        initTooltips: function() {
            // Add tooltips to action buttons
            $('.partner-action').each(function() {
                const action = $(this).data('action');
                const tooltips = {
                    'activate': 'Activate this partner',
                    'deactivate': 'Deactivate this partner',
                    'feature': 'Add to featured partners',
                    'unfeature': 'Remove from featured partners',
                    'delete': 'Delete this partner permanently'
                };
                
                if (tooltips[action]) {
                    $(this).attr('title', tooltips[action]);
                }
            });
        },
        
        loadPartners: function(params = {}) {
            this.showLoading();
            
            const defaultParams = {
                action: 'kilismile_load_partners',
                nonce: partnerAdmin.nonce,
                search: $('#partner-search').val(),
                category: $('#category-filter').val(),
                level: $('#level-filter').val(),
                page: 1,
                per_page: 20
            };
            
            const requestParams = $.extend(defaultParams, params);
            
            $.post(partnerAdmin.ajaxUrl, requestParams)
                .done(this.handleLoadSuccess.bind(this))
                .fail(this.handleLoadError.bind(this))
                .always(this.hideLoading.bind(this));
        },
        
        handleLoadSuccess: function(response) {
            if (response.success) {
                $('#partners-container').html(response.data.html);
                this.updateStats(response.data.stats);
                this.reinitializeComponents();
            } else {
                this.showNotice('error', response.data || 'Failed to load partners');
            }
        },
        
        handleLoadError: function(xhr, status, error) {
            this.showNotice('error', 'Network error occurred. Please try again.');
            console.error('Partner load error:', error);
        },
        
        handleSearch: function() {
            PartnerManagement.loadPartners();
        },
        
        handleFilter: function() {
            PartnerManagement.loadPartners();
        },
        
        handleViewToggle: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const viewType = $btn.attr('id').replace('-view', '');
            
            // Update button states
            $('.view-btn').removeClass('active');
            $btn.addClass('active');
            
            // Update container class
            const $container = $('#partners-container');
            $container.removeClass('partners-grid partners-list');
            $container.addClass('partners-' + viewType);
            
            // Reinitialize sortable if grid view
            if (viewType === 'grid') {
                PartnerManagement.initSortable();
            }
        },
        
        handleBulkAction: function(e) {
            e.preventDefault();
            
            const action = $('#bulk-action-selector').val();
            const selectedIds = $('.partner-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (action === '-1') {
                PartnerManagement.showNotice('warning', partnerAdmin.strings.selectPartners);
                return;
            }
            
            if (selectedIds.length === 0) {
                PartnerManagement.showNotice('warning', partnerAdmin.strings.selectPartners);
                return;
            }
            
            if (action === 'delete') {
                if (!confirm(partnerAdmin.strings.confirmBulkDelete)) {
                    return;
                }
            }
            
            PartnerManagement.executeBulkAction(action, selectedIds);
        },
        
        executeBulkAction: function(action, ids) {
            this.showLoading();
            
            $.post(partnerAdmin.ajaxUrl, {
                action: 'kilismile_bulk_partner_action',
                nonce: partnerAdmin.nonce,
                partner_action: action,
                partner_ids: ids
            })
            .done(function(response) {
                if (response.success) {
                    PartnerManagement.showNotice('success', response.data.message || partnerAdmin.strings.updateSuccess);
                    PartnerManagement.loadPartners();
                    PartnerManagement.clearSelection();
                } else {
                    PartnerManagement.showNotice('error', response.data || 'Bulk action failed');
                }
            })
            .fail(function() {
                PartnerManagement.showNotice('error', 'Network error occurred');
            })
            .always(this.hideLoading.bind(this));
        },
        
        handlePartnerAction: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const action = $btn.data('action');
            const partnerId = $btn.data('id');
            
            if (action === 'delete') {
                if (!confirm(partnerAdmin.strings.confirmDelete)) {
                    return;
                }
            }
            
            PartnerManagement.executePartnerAction(action, partnerId);
        },
        
        executePartnerAction: function(action, partnerId) {
            this.showLoading();
            
            $.post(partnerAdmin.ajaxUrl, {
                action: 'kilismile_partner_action',
                nonce: partnerAdmin.nonce,
                partner_action: action,
                partner_id: partnerId
            })
            .done(function(response) {
                if (response.success) {
                    PartnerManagement.showNotice('success', response.data.message || 'Action completed successfully');
                    PartnerManagement.loadPartners();
                } else {
                    PartnerManagement.showNotice('error', response.data || 'Action failed');
                }
            })
            .fail(function() {
                PartnerManagement.showNotice('error', 'Network error occurred');
            })
            .always(this.hideLoading.bind(this));
        },
        
        handleOrderUpdate: function(event, ui) {
            const newOrder = $('#partners-container').sortable('toArray', {
                attribute: 'data-partner-id'
            });
            
            $.post(partnerAdmin.ajaxUrl, {
                action: 'kilismile_update_partner_order',
                nonce: partnerAdmin.nonce,
                order: newOrder
            })
            .done(function(response) {
                if (response.success) {
                    PartnerManagement.showNotice('success', 'Order updated successfully');
                } else {
                    PartnerManagement.showNotice('error', 'Failed to update order');
                }
            });
        },
        
        toggleActionDropdown: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $dropdown = $(this).siblings('.dropdown-menu');
            
            // Close other dropdowns
            $('.dropdown-menu').not($dropdown).hide();
            
            // Toggle current dropdown
            $dropdown.toggle();
        },
        
        selectAllPartners: function(e) {
            e.preventDefault();
            $('.partner-checkbox').prop('checked', true);
            PartnerManagement.updateBulkActions();
        },
        
        selectNonePartners: function(e) {
            e.preventDefault();
            $('.partner-checkbox').prop('checked', false);
            PartnerManagement.updateBulkActions();
        },
        
        updateBulkActions: function() {
            const selectedCount = $('.partner-checkbox:checked').length;
            const $bulkSection = $('.bulk-actions-section');
            
            if (selectedCount > 0) {
                $bulkSection.addClass('has-selection');
            } else {
                $bulkSection.removeClass('has-selection');
            }
        },
        
        clearSelection: function() {
            $('.partner-checkbox').prop('checked', false);
            $('#bulk-action-selector').val('-1');
            this.updateBulkActions();
        },
        
        refreshStats: function() {
            $.post(partnerAdmin.ajaxUrl, {
                action: 'kilismile_get_partner_stats',
                nonce: partnerAdmin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    PartnerManagement.updateStats(response.data);
                }
            });
        },
        
        updateStats: function(stats) {
            if (!stats) return;
            
            $('.stat-card.total .stat-content h3').text(stats.total || 0);
            $('.stat-card.featured .stat-content h3').text(stats.featured || 0);
            $('.stat-card.expiring .stat-content h3').text(stats.expiring_soon || 0);
            
            // Update total clicks
            if (stats.total_clicks) {
                $('.stat-card.analytics .stat-content h3').text(stats.total_clicks);
            }
        },
        
        reinitializeComponents: function() {
            // Reinitialize sortable if in grid view
            if ($('#partners-container').hasClass('partners-grid')) {
                this.initSortable();
            }
            
            // Reinitialize tooltips
            this.initTooltips();
            
            // Update bulk actions state
            this.updateBulkActions();
        },
        
        showLoading: function() {
            $('#loading-overlay').show();
        },
        
        hideLoading: function() {
            $('#loading-overlay').hide();
        },
        
        showNotice: function(type, message) {
            const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Remove existing notices
            $('.notice').remove();
            
            // Add new notice
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut();
            }, 5000);
            
            // Handle dismiss button
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut();
            });
        },
        
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Partner Form specific functionality
    const PartnerForm = {
        
        init: function() {
            if ($('#partner-form').length) {
                this.bindFormEvents();
                this.initializeValidation();
            }
        },
        
        bindFormEvents: function() {
            // Logo upload handlers
            this.setupLogoUpload('upload-logo', 'logo-preview', 'logo_url', 'remove-logo');
            this.setupLogoUpload('upload-logo-alt', 'logo-alt-preview', 'logo_alt_url', 'remove-logo-alt');
            
            // Form validation
            $('#partner-form').on('submit', this.validateForm);
            
            // Auto-save draft (every 30 seconds)
            setInterval(this.saveDraft.bind(this), 30000);
            
            // Partnership level change handler
            $('#partnership_level').on('change', this.handleLevelChange);
            
            // URL validation
            $('#website').on('blur', this.validateUrl);
            $('input[type="url"]').on('blur', this.validateUrl);
        },
        
        setupLogoUpload: function(buttonId, previewId, inputId, removeId) {
            $('#' + buttonId).on('click', function(e) {
                e.preventDefault();
                
                const mediaUploader = wp.media({
                    title: 'Select Logo',
                    button: { text: 'Use This Logo' },
                    multiple: false,
                    library: { type: 'image' }
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    
                    $('#' + inputId).val(attachment.url);
                    $('#' + previewId).html('<img src="' + attachment.url + '" alt="Logo preview">');
                    $('#' + removeId).show();
                    
                    PartnerForm.showNotice('success', partnerAdmin.strings.uploadSuccess);
                });
                
                mediaUploader.open();
            });
            
            $('#' + removeId).on('click', function(e) {
                e.preventDefault();
                $('#' + inputId).val('');
                $('#' + previewId).html('<div class="no-logo"><i class="dashicons dashicons-format-image"></i><p>No logo selected</p></div>');
                $(this).hide();
            });
        },
        
        validateForm: function(e) {
            let isValid = true;
            const errors = [];
            
            // Required fields validation
            const requiredFields = ['name', 'website', 'category'];
            
            requiredFields.forEach(function(field) {
                const $field = $('#' + field);
                if (!$field.val().trim()) {
                    isValid = false;
                    errors.push('Please fill in the ' + $field.prev('label').text().replace('*', '').trim());
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
            
            // URL validation
            const urlFields = ['website'];
            $('input[name^="social_links"]').each(function() {
                if ($(this).val()) {
                    urlFields.push($(this).attr('name'));
                }
            });
            
            urlFields.forEach(function(field) {
                const $field = field.includes('[') ? $('input[name="' + field + '"]') : $('#' + field);
                const url = $field.val();
                
                if (url && !PartnerForm.isValidUrl(url)) {
                    isValid = false;
                    errors.push('Please enter a valid URL for ' + $field.prev('label').text());
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                PartnerForm.showNotice('error', 'Please fix the following errors:\n' + errors.join('\n'));
                
                // Scroll to first error
                $('html, body').animate({
                    scrollTop: $('.error').first().offset().top - 100
                }, 500);
            }
            
            return isValid;
        },
        
        initializeValidation: function() {
            // Real-time validation
            $('#name').on('blur', function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            $('#website').on('blur', function() {
                const url = $(this).val();
                if (!url || !PartnerForm.isValidUrl(url)) {
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
        },
        
        handleLevelChange: function() {
            const level = $(this).val();
            const colors = {
                'platinum': '#e5e5e5',
                'gold': '#ffd700',
                'silver': '#c0c0c0',
                'bronze': '#cd7f32',
                'basic': '#4CAF50'
            };
            
            // Visual feedback for level selection
            $(this).css('border-color', colors[level] || '#ddd');
        },
        
        validateUrl: function() {
            const url = $(this).val();
            if (url && !PartnerForm.isValidUrl(url)) {
                $(this).addClass('error');
                $(this).after('<span class="validation-error">Please enter a valid URL</span>');
            } else {
                $(this).removeClass('error');
                $(this).siblings('.validation-error').remove();
            }
        },
        
        isValidUrl: function(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        },
        
        saveDraft: function() {
            // Auto-save form data to localStorage
            const formData = {};
            $('#partner-form input, #partner-form select, #partner-form textarea').each(function() {
                if ($(this).attr('name')) {
                    formData[$(this).attr('name')] = $(this).val();
                }
            });
            
            localStorage.setItem('partner_draft', JSON.stringify(formData));
        },
        
        loadDraft: function() {
            // Load draft from localStorage
            const draft = localStorage.getItem('partner_draft');
            if (draft) {
                const formData = JSON.parse(draft);
                Object.keys(formData).forEach(function(name) {
                    $('[name="' + name + '"]').val(formData[name]);
                });
            }
        },
        
        showNotice: function(type, message) {
            const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Remove existing notices
            $('.notice').remove();
            
            // Add new notice
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut();
            }, 5000);
        }
    };
    
    // Analytics specific functionality
    const PartnerAnalytics = {
        
        init: function() {
            if ($('.analytics-wrap').length) {
                this.loadAnalyticsData();
                this.initializeCharts();
            }
        },
        
        loadAnalyticsData: function() {
            $.post(partnerAdmin.ajaxUrl, {
                action: 'kilismile_get_analytics_data',
                nonce: partnerAdmin.nonce
            })
            .done(function(response) {
                if (response.success) {
                    PartnerAnalytics.createCharts(response.data);
                }
            });
        },
        
        initializeCharts: function() {
            // Chart.js default configuration
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;
            Chart.defaults.plugins.legend.position = 'bottom';
        },
        
        createCharts: function(data) {
            // Category Distribution Chart
            if (document.getElementById('categoryChart')) {
                new Chart(document.getElementById('categoryChart'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data.by_category).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                        datasets: [{
                            data: Object.values(data.by_category),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                                '#9966FF', '#FF9F40', '#FF6384', '#C7B42C'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }
            
            // Partnership Level Chart
            if (document.getElementById('levelChart')) {
                new Chart(document.getElementById('levelChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data.by_level).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                        datasets: [{
                            label: 'Number of Partners',
                            data: Object.values(data.by_level),
                            backgroundColor: [
                                '#e5e5e5', '#ffd700', '#c0c0c0', '#cd7f32', '#4CAF50'
                            ],
                            borderColor: [
                                '#d5d5d5', '#e6c200', '#b0b0b0', '#bd6f22', '#45a049'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : null;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Click Trends Chart (if data available)
            if (data.click_trends && document.getElementById('clickTrendsChart')) {
                new Chart(document.getElementById('clickTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: data.click_trends.labels,
                        datasets: [{
                            label: 'Clicks',
                            data: data.click_trends.data,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    };
    
    // Initialize everything when document is ready
    $(document).ready(function() {
        PartnerManagement.init();
        PartnerForm.init();
        PartnerAnalytics.init();
    });
    
})(jQuery);


