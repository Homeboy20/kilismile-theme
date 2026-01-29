/**
 * KiliSmile Payments - Admin JavaScript
 */

(function($) {
    'use strict';

    // Main admin handler
    const KiliSmileAdmin = {
        
        // Configuration
        config: {
            dashboardCards: '.stat-card',
            gatewayToggle: '.gateway-toggle',
            settingsForm: '.gateway-settings-form',
            transactionTable: '.transactions-table',
            logTable: '.logs-table',
            testButton: '.test-gateway-button',
            exportButton: '.export-transactions',
            refreshButton: '.refresh-stats'
        },

        // Initialize
        init: function() {
            this.initDashboard();
            this.bindEvents();
            this.initDataTables();
            this.initCharts();
            this.setupAutoRefresh();
        },

        // Bind all events
        bindEvents: function() {
            $(document).on('change', this.config.gatewayToggle, this.handleGatewayToggle.bind(this));
            $(document).on('submit', this.config.settingsForm, this.handleSettingsSubmit.bind(this));
            $(document).on('click', this.config.testButton, this.handleGatewayTest.bind(this));
            $(document).on('click', this.config.exportButton, this.handleExportTransactions.bind(this));
            $(document).on('click', this.config.refreshButton, this.handleRefreshStats.bind(this));
            $(document).on('click', '.retry-transaction', this.handleRetryTransaction.bind(this));
            $(document).on('click', '.view-transaction-details', this.handleViewTransactionDetails.bind(this));
            $(document).on('click', '.clear-logs', this.handleClearLogs.bind(this));
            $(document).on('change', '.gateway-filter', this.handleGatewayFilter.bind(this));
            $(document).on('change', '.date-filter', this.handleDateFilter.bind(this));
        },

        // Initialize dashboard
        initDashboard: function() {
            this.animateStatCards();
            this.loadRealtimeStats();
            this.updateGatewayStatuses();
        },

        // Animate stat cards on load
        animateStatCards: function() {
            $(this.config.dashboardCards).each(function(index) {
                $(this).delay(index * 100).queue(function(next) {
                    $(this).addClass('animated');
                    next();
                });
            });
        },

        // Handle gateway toggle
        handleGatewayToggle: function(e) {
            const toggle = $(e.target);
            const gatewayId = toggle.data('gateway');
            const isEnabled = toggle.is(':checked');
            
            this.showLoadingIndicator();
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_toggle_gateway',
                    nonce: kilismile_admin.nonce,
                    gateway: gatewayId,
                    enabled: isEnabled
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotification('Gateway settings updated successfully', 'success');
                        this.updateGatewayCard(gatewayId, isEnabled);
                    } else {
                        this.showNotification(response.data.message || 'Failed to update gateway settings', 'error');
                        toggle.prop('checked', !isEnabled); // Revert toggle
                    }
                },
                error: () => {
                    this.showNotification('Network error. Please try again.', 'error');
                    toggle.prop('checked', !isEnabled); // Revert toggle
                },
                complete: () => {
                    this.hideLoadingIndicator();
                }
            });
        },

        // Handle settings form submit
        handleSettingsSubmit: function(e) {
            e.preventDefault();
            
            const form = $(e.target);
            const submitButton = form.find('button[type="submit"]');
            const originalText = submitButton.text();
            
            // Show loading state
            submitButton.prop('disabled', true).text('Saving...');
            
            const formData = form.serialize() + '&action=kilismile_save_gateway_settings&nonce=' + kilismile_admin.nonce;
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: formData,
                success: (response) => {
                    if (response.success) {
                        this.showNotification('Settings saved successfully', 'success');
                        
                        // Update gateway card if status changed
                        const gatewayId = form.data('gateway');
                        if (gatewayId) {
                            this.updateGatewayCard(gatewayId, response.data.enabled);
                        }
                    } else {
                        this.showNotification(response.data.message || 'Failed to save settings', 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error. Please try again.', 'error');
                },
                complete: () => {
                    submitButton.prop('disabled', false).text(originalText);
                }
            });
        },

        // Handle gateway test
        handleGatewayTest: function(e) {
            e.preventDefault();
            
            const button = $(e.target);
            const gatewayId = button.data('gateway');
            const originalText = button.text();
            
            button.prop('disabled', true).text('Testing...');
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_test_gateway',
                    nonce: kilismile_admin.nonce,
                    gateway: gatewayId
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotification('Gateway test successful', 'success');
                        this.displayTestResults(response.data);
                    } else {
                        this.showNotification('Gateway test failed: ' + (response.data.message || 'Unknown error'), 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error during test', 'error');
                },
                complete: () => {
                    button.prop('disabled', false).text(originalText);
                }
            });
        },

        // Handle export transactions
        handleExportTransactions: function(e) {
            e.preventDefault();
            
            const button = $(e.target);
            const filters = this.getTableFilters();
            
            // Create export form
            const form = $('<form>', {
                method: 'POST',
                action: kilismile_admin.ajax_url
            });
            
            // Add form fields
            form.append($('<input>', {type: 'hidden', name: 'action', value: 'kilismile_export_transactions'}));
            form.append($('<input>', {type: 'hidden', name: 'nonce', value: kilismile_admin.nonce}));
            
            // Add filters
            Object.keys(filters).forEach(key => {
                form.append($('<input>', {type: 'hidden', name: key, value: filters[key]}));
            });
            
            // Submit form (will trigger download)
            $('body').append(form);
            form.submit();
            form.remove();
            
            this.showNotification('Export started. Download will begin shortly.', 'info');
        },

        // Handle refresh stats
        handleRefreshStats: function(e) {
            e.preventDefault();
            
            const button = $(e.target);
            const originalText = button.text();
            
            button.prop('disabled', true).text('Refreshing...');
            
            this.loadRealtimeStats().then(() => {
                button.prop('disabled', false).text(originalText);
                this.showNotification('Statistics refreshed', 'success');
            });
        },

        // Handle retry transaction
        handleRetryTransaction: function(e) {
            e.preventDefault();
            
            const button = $(e.target);
            const transactionId = button.data('transaction-id');
            
            if (!confirm('Are you sure you want to retry this transaction?')) {
                return;
            }
            
            button.prop('disabled', true).text('Retrying...');
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_retry_transaction',
                    nonce: kilismile_admin.nonce,
                    transaction_id: transactionId
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotification('Transaction retry initiated', 'success');
                        this.refreshTransactionTable();
                    } else {
                        this.showNotification('Failed to retry transaction: ' + response.data.message, 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error during retry', 'error');
                },
                complete: () => {
                    button.prop('disabled', false).text('Retry');
                }
            });
        },

        // Handle view transaction details
        handleViewTransactionDetails: function(e) {
            e.preventDefault();
            
            const button = $(e.target);
            const transactionId = button.data('transaction-id');
            
            this.loadTransactionDetails(transactionId);
        },

        // Handle clear logs
        handleClearLogs: function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
                return;
            }
            
            const button = $(e.target);
            button.prop('disabled', true).text('Clearing...');
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_clear_logs',
                    nonce: kilismile_admin.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotification('Logs cleared successfully', 'success');
                        this.refreshLogTable();
                    } else {
                        this.showNotification('Failed to clear logs', 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error', 'error');
                },
                complete: () => {
                    button.prop('disabled', false).text('Clear Logs');
                }
            });
        },

        // Handle gateway filter
        handleGatewayFilter: function(e) {
            const gateway = $(e.target).val();
            this.filterTable('gateway', gateway);
        },

        // Handle date filter
        handleDateFilter: function(e) {
            const dateRange = $(e.target).val();
            this.filterTable('date_range', dateRange);
        },

        // Load realtime stats
        loadRealtimeStats: function() {
            return $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_get_stats',
                    nonce: kilismile_admin.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.updateStatCards(response.data);
                        this.updateCharts(response.data);
                    }
                }
            });
        },

        // Update stat cards
        updateStatCards: function(stats) {
            Object.keys(stats).forEach(key => {
                const card = $(`.stat-card[data-stat="${key}"]`);
                const value = card.find('.stat-value');
                const currentValue = parseInt(value.text().replace(/[^0-9]/g, '')) || 0;
                const newValue = stats[key];
                
                if (currentValue !== newValue) {
                    this.animateValue(value, currentValue, newValue);
                }
            });
        },

        // Animate value change
        animateValue: function(element, start, end) {
            const duration = 1000;
            const startTime = performance.now();
            
            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const currentValue = Math.floor(start + (end - start) * progress);
                element.text(this.formatNumber(currentValue));
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };
            
            requestAnimationFrame(animate);
        },

        // Update gateway card
        updateGatewayCard: function(gatewayId, enabled) {
            const card = $(`.gateway-card[data-gateway="${gatewayId}"]`);
            const statusBadge = card.find('.gateway-status');
            
            if (enabled) {
                card.removeClass('disabled').addClass('enabled');
                statusBadge.removeClass('status-disabled').addClass('status-enabled').text('Enabled');
            } else {
                card.removeClass('enabled').addClass('disabled');
                statusBadge.removeClass('status-enabled').addClass('status-disabled').text('Disabled');
            }
        },

        // Update gateway statuses
        updateGatewayStatuses: function() {
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_get_gateway_statuses',
                    nonce: kilismile_admin.nonce
                },
                success: (response) => {
                    if (response.success) {
                        Object.keys(response.data).forEach(gatewayId => {
                            const status = response.data[gatewayId];
                            this.updateGatewayStatusIndicator(gatewayId, status);
                        });
                    }
                }
            });
        },

        // Update gateway status indicator
        updateGatewayStatusIndicator: function(gatewayId, status) {
            const card = $(`.gateway-card[data-gateway="${gatewayId}"]`);
            const indicator = card.find('.status-indicator');
            
            indicator.removeClass('status-online status-offline status-error')
                    .addClass(`status-${status.connection}`);
            
            // Update tooltip
            indicator.attr('title', `Last checked: ${status.last_check}\nStatus: ${status.connection}`);
        },

        // Initialize DataTables
        initDataTables: function() {
            if ($(this.config.transactionTable).length) {
                this.transactionDataTable = $(this.config.transactionTable).DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'desc']],
                    columnDefs: [
                        {targets: 'no-sort', orderable: false}
                    ],
                    language: {
                        search: 'Search transactions:'
                    }
                });
            }
            
            if ($(this.config.logTable).length) {
                this.logDataTable = $(this.config.logTable).DataTable({
                    responsive: true,
                    pageLength: 50,
                    order: [[0, 'desc']],
                    columnDefs: [
                        {targets: 'no-sort', orderable: false}
                    ],
                    language: {
                        search: 'Search logs:'
                    }
                });
            }
        },

        // Initialize charts
        initCharts: function() {
            this.initTransactionChart();
            this.initGatewayChart();
        },

        // Initialize transaction chart
        initTransactionChart: function() {
            const chartContainer = $('#transaction-chart');
            if (!chartContainer.length) return;
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_get_chart_data',
                    nonce: kilismile_admin.nonce,
                    chart_type: 'transactions'
                },
                success: (response) => {
                    if (response.success) {
                        this.renderTransactionChart(chartContainer[0], response.data);
                    }
                }
            });
        },

        // Initialize gateway chart
        initGatewayChart: function() {
            const chartContainer = $('#gateway-chart');
            if (!chartContainer.length) return;
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_get_chart_data',
                    nonce: kilismile_admin.nonce,
                    chart_type: 'gateways'
                },
                success: (response) => {
                    if (response.success) {
                        this.renderGatewayChart(chartContainer[0], response.data);
                    }
                }
            });
        },

        // Render transaction chart
        renderTransactionChart: function(container, data) {
            const ctx = container.getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Transactions',
                        data: data.values,
                        borderColor: '#007cba',
                        backgroundColor: 'rgba(0, 124, 186, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },

        // Render gateway chart
        renderGatewayChart: function(container, data) {
            const ctx = container.getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: ['#007cba', '#00a32a', '#d63638', '#ffb900']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        // Update charts
        updateCharts: function(stats) {
            // Refresh chart data if needed
            if (stats.chart_data) {
                this.initCharts();
            }
        },

        // Load transaction details
        loadTransactionDetails: function(transactionId) {
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_get_transaction_details',
                    nonce: kilismile_admin.nonce,
                    transaction_id: transactionId
                },
                success: (response) => {
                    if (response.success) {
                        this.showTransactionModal(response.data);
                    } else {
                        this.showNotification('Failed to load transaction details', 'error');
                    }
                }
            });
        },

        // Show transaction modal
        showTransactionModal: function(transaction) {
            const modal = this.createModal('Transaction Details', this.buildTransactionDetailsHtml(transaction));
            modal.show();
        },

        // Build transaction details HTML
        buildTransactionDetailsHtml: function(transaction) {
            return `
                <div class="transaction-details">
                    <div class="detail-row">
                        <label>Transaction ID:</label>
                        <span>${transaction.id}</span>
                    </div>
                    <div class="detail-row">
                        <label>Status:</label>
                        <span class="status-badge status-${transaction.status}">${transaction.status}</span>
                    </div>
                    <div class="detail-row">
                        <label>Gateway:</label>
                        <span>${transaction.gateway}</span>
                    </div>
                    <div class="detail-row">
                        <label>Amount:</label>
                        <span>${transaction.amount} ${transaction.currency}</span>
                    </div>
                    <div class="detail-row">
                        <label>Donor:</label>
                        <span>${transaction.donor_name} (${transaction.donor_email})</span>
                    </div>
                    <div class="detail-row">
                        <label>Created:</label>
                        <span>${transaction.created_at}</span>
                    </div>
                    ${transaction.gateway_transaction_id ? `
                    <div class="detail-row">
                        <label>Gateway Transaction ID:</label>
                        <span>${transaction.gateway_transaction_id}</span>
                    </div>
                    ` : ''}
                    ${transaction.failure_reason ? `
                    <div class="detail-row">
                        <label>Failure Reason:</label>
                        <span class="error-text">${transaction.failure_reason}</span>
                    </div>
                    ` : ''}
                </div>
            `;
        },

        // Display test results
        displayTestResults: function(results) {
            const modal = this.createModal('Gateway Test Results', this.buildTestResultsHtml(results));
            modal.show();
        },

        // Build test results HTML
        buildTestResultsHtml: function(results) {
            let html = '<div class="test-results">';
            
            Object.keys(results.tests).forEach(testName => {
                const test = results.tests[testName];
                const statusClass = test.passed ? 'success' : 'error';
                
                html += `
                    <div class="test-result ${statusClass}">
                        <h4>${testName}</h4>
                        <p class="test-status">${test.passed ? 'PASSED' : 'FAILED'}</p>
                        <p class="test-message">${test.message}</p>
                        ${test.details ? `<div class="test-details">${JSON.stringify(test.details, null, 2)}</div>` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            return html;
        },

        // Create modal
        createModal: function(title, content) {
            // Remove existing modal
            $('#kilismile-modal').remove();
            
            const modal = $(`
                <div id="kilismile-modal" class="kilismile-modal">
                    <div class="modal-backdrop"></div>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>${title}</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            // Bind close events
            modal.find('.modal-close, .modal-backdrop').on('click', function() {
                modal.fadeOut(() => modal.remove());
            });
            
            return {
                show: function() {
                    modal.fadeIn();
                },
                hide: function() {
                    modal.fadeOut(() => modal.remove());
                }
            };
        },

        // Get table filters
        getTableFilters: function() {
            return {
                gateway: $('.gateway-filter').val(),
                date_range: $('.date-filter').val(),
                status: $('.status-filter').val()
            };
        },

        // Filter table
        filterTable: function(filterType, value) {
            if (this.transactionDataTable) {
                // Apply custom filtering logic
                this.transactionDataTable.draw();
            }
        },

        // Refresh transaction table
        refreshTransactionTable: function() {
            if (this.transactionDataTable) {
                this.transactionDataTable.ajax.reload();
            }
        },

        // Refresh log table
        refreshLogTable: function() {
            if (this.logDataTable) {
                this.logDataTable.ajax.reload();
            }
        },

        // Setup auto refresh
        setupAutoRefresh: function() {
            // Refresh stats every 5 minutes
            setInterval(() => {
                this.loadRealtimeStats();
            }, 300000);
            
            // Update gateway statuses every 2 minutes
            setInterval(() => {
                this.updateGatewayStatuses();
            }, 120000);
        },

        // Show notification
        showNotification: function(message, type = 'info') {
            const notification = $(`
                <div class="kilismile-notification ${type}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);
            
            $('.kilismile-notifications').append(notification);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
            
            // Manual close
            notification.find('.notification-close').on('click', function() {
                notification.fadeOut(() => notification.remove());
            });
        },

        // Show/hide loading indicator
        showLoadingIndicator: function() {
            if (!$('.kilismile-loading').length) {
                $('body').append('<div class="kilismile-loading"><div class="loading-spinner"></div></div>');
            }
        },

        hideLoadingIndicator: function() {
            $('.kilismile-loading').remove();
        },

        // Utility functions
        formatNumber: function(num) {
            return num.toLocaleString();
        },

        formatCurrency: function(amount, currency) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency || 'USD'
            }).format(amount);
        }
    };

    // Settings manager for gateway configuration
    const SettingsManager = {
        init: function() {
            this.bindEvents();
            this.initValidation();
        },

        bindEvents: function() {
            $(document).on('click', '.toggle-section', this.toggleSection);
            $(document).on('click', '.reset-gateway', this.resetGatewaySettings);
            $(document).on('change', '.setting-field', this.handleSettingChange);
        },

        toggleSection: function(e) {
            e.preventDefault();
            const section = $(this).closest('.settings-section');
            const content = section.find('.section-content');
            
            if (content.is(':visible')) {
                content.slideUp();
                $(this).removeClass('expanded');
            } else {
                content.slideDown();
                $(this).addClass('expanded');
            }
        },

        resetGatewaySettings: function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to reset this gateway to default settings?')) {
                return;
            }
            
            const gatewayId = $(this).data('gateway');
            
            $.ajax({
                url: kilismile_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_reset_gateway_settings',
                    nonce: kilismile_admin.nonce,
                    gateway: gatewayId
                },
                success: (response) => {
                    if (response.success) {
                        location.reload(); // Reload to show default values
                    } else {
                        KiliSmileAdmin.showNotification('Failed to reset settings', 'error');
                    }
                }
            });
        },

        handleSettingChange: function(e) {
            const field = $(this);
            const section = field.closest('.settings-section');
            
            // Mark section as modified
            section.addClass('modified');
            
            // Enable save button
            section.find('.save-settings').prop('disabled', false);
        },

        initValidation: function() {
            // Add real-time validation for settings fields
            $('.setting-field[data-validation]').each(function() {
                const field = $(this);
                const validation = field.data('validation');
                
                field.on('blur', function() {
                    SettingsManager.validateField(field, validation);
                });
            });
        },

        validateField: function(field, validationType) {
            let isValid = true;
            let message = '';
            
            const value = field.val();
            
            switch (validationType) {
                case 'required':
                    isValid = value.trim() !== '';
                    message = 'This field is required';
                    break;
                case 'url':
                    isValid = /^https?:\/\/.+/.test(value);
                    message = 'Please enter a valid URL';
                    break;
                case 'email':
                    isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    message = 'Please enter a valid email address';
                    break;
            }
            
            const fieldGroup = field.closest('.form-field');
            fieldGroup.removeClass('error success');
            fieldGroup.find('.validation-message').remove();
            
            if (!isValid && value !== '') {
                fieldGroup.addClass('error');
                fieldGroup.append(`<div class="validation-message error">${message}</div>`);
            } else if (value !== '') {
                fieldGroup.addClass('success');
            }
            
            return isValid;
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        KiliSmileAdmin.init();
        SettingsManager.init();
        
        // Create notification container
        if (!$('.kilismile-notifications').length) {
            $('body').append('<div class="kilismile-notifications"></div>');
        }
    });

    // Expose to global scope
    window.KiliSmileAdmin = KiliSmileAdmin;
    window.KiliSmileSettingsManager = SettingsManager;

})(jQuery);

