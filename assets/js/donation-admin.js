/**
 * Donation Admin JavaScript
 * 
 * Provides interactive functionality for the donation admin dashboard
 * including charts, AJAX operations, and dynamic updates.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

(function($) {
    'use strict';
    
    // Admin dashboard controller
    class KiliSmileDonationAdmin {
        constructor() {
            this.charts = {};
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.initializeCharts();
            this.setupStatusUpdates();
        }
        
        bindEvents() {
            // View donation details
            $(document).on('click', '.view-donation', (e) => {
                e.preventDefault();
                const donationId = $(e.currentTarget).data('id');
                this.viewDonationDetails(donationId);
            });
            
            // Status update dropdown
            $(document).on('change', '.status-update', (e) => {
                const $select = $(e.currentTarget);
                const donationId = $select.data('id');
                const newStatus = $select.val();
                
                if (newStatus) {
                    this.updateDonationStatus(donationId, newStatus);
                }
            });
            
            // Export functionality
            $(document).on('submit', 'form[name="export_donations"]', (e) => {
                e.preventDefault();
                this.exportDonations($(e.currentTarget));
            });
            
            // Real-time refresh
            if ($('.donations-table-container').length) {
                setInterval(() => {
                    this.refreshDonationsTable();
                }, 30000); // Refresh every 30 seconds
            }
        }
        
        async viewDonationDetails(donationId) {
            try {
                const response = await this.makeAjaxRequest('get_donation_details', {
                    donation_id: donationId
                });
                
                if (response.success) {
                    this.showDonationModal(response.data);
                } else {
                    this.showNotice('Error loading donation details.', 'error');
                }
            } catch (error) {
                console.error('Error viewing donation:', error);
                this.showNotice('Failed to load donation details.', 'error');
            }
        }
        
        showDonationModal(donation) {
            const modalHtml = `
                <div id="donation-modal" class="donation-modal-overlay">
                    <div class="donation-modal">
                        <div class="donation-modal-header">
                            <h3>Donation Details #${donation.id}</h3>
                            <button class="close-modal">&times;</button>
                        </div>
                        <div class="donation-modal-content">
                            <div class="donation-details-grid">
                                <div class="detail-section">
                                    <h4>Donor Information</h4>
                                    <p><strong>Name:</strong> ${donation.anonymous ? 'Anonymous' : donation.first_name + ' ' + donation.last_name}</p>
                                    <p><strong>Email:</strong> ${donation.anonymous ? 'Hidden' : donation.email}</p>
                                    <p><strong>Phone:</strong> ${donation.phone || 'Not provided'}</p>
                                </div>
                                
                                <div class="detail-section">
                                    <h4>Donation Details</h4>
                                    <p><strong>Amount:</strong> ${donation.currency === 'USD' ? '$' : 'TSh '}${parseFloat(donation.amount).toLocaleString()}</p>
                                    <p><strong>Currency:</strong> ${donation.currency}</p>
                                    <p><strong>Recurring:</strong> ${donation.recurring ? 'Yes' : 'No'}</p>
                                    <p><strong>Purpose:</strong> ${donation.purpose || 'General'}</p>
                                </div>
                                
                                <div class="detail-section">
                                    <h4>Payment Information</h4>
                                    <p><strong>Method:</strong> ${donation.payment_method}</p>
                                    <p><strong>Transaction ID:</strong> ${donation.transaction_id || 'N/A'}</p>
                                    <p><strong>Status:</strong> <span class="status-badge status-${donation.status}">${donation.status.charAt(0).toUpperCase() + donation.status.slice(1)}</span></p>
                                </div>
                                
                                <div class="detail-section">
                                    <h4>Timestamps</h4>
                                    <p><strong>Created:</strong> ${new Date(donation.created_at).toLocaleString()}</p>
                                    <p><strong>Updated:</strong> ${new Date(donation.updated_at).toLocaleString()}</p>
                                </div>
                            </div>
                            
                            ${donation.message ? `
                                <div class="detail-section">
                                    <h4>Message</h4>
                                    <p>${donation.message}</p>
                                </div>
                            ` : ''}
                            
                            <div class="donation-actions">
                                <button class="button button-primary send-receipt" data-id="${donation.id}">Send Receipt</button>
                                <button class="button button-secondary refund-donation" data-id="${donation.id}">Process Refund</button>
                                <select class="status-change" data-id="${donation.id}">
                                    <option value="">Change Status</option>
                                    <option value="pending" ${donation.status === 'pending' ? 'disabled' : ''}>Pending</option>
                                    <option value="completed" ${donation.status === 'completed' ? 'disabled' : ''}>Completed</option>
                                    <option value="failed" ${donation.status === 'failed' ? 'disabled' : ''}>Failed</option>
                                    <option value="refunded" ${donation.status === 'refunded' ? 'disabled' : ''}>Refunded</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            
            // Bind modal events
            $('.close-modal, .donation-modal-overlay').on('click', (e) => {
                if (e.target === e.currentTarget) {
                    this.closeDonationModal();
                }
            });
            
            $('.send-receipt').on('click', (e) => {
                this.sendReceipt($(e.currentTarget).data('id'));
            });
            
            $('.refund-donation').on('click', (e) => {
                this.processRefund($(e.currentTarget).data('id'));
            });
            
            $('.status-change').on('change', (e) => {
                const $select = $(e.currentTarget);
                const donationId = $select.data('id');
                const newStatus = $select.val();
                
                if (newStatus) {
                    this.updateDonationStatus(donationId, newStatus);
                }
            });
        }
        
        closeDonationModal() {
            $('#donation-modal').fadeOut(300, function() {
                $(this).remove();
            });
        }
        
        async updateDonationStatus(donationId, newStatus) {
            try {
                const response = await this.makeAjaxRequest('update_donation_status', {
                    donation_id: donationId,
                    new_status: newStatus
                });
                
                if (response.success) {
                    this.showNotice('Donation status updated successfully.', 'success');
                    this.refreshDonationsTable();
                    this.closeDonationModal();
                } else {
                    this.showNotice('Failed to update donation status.', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                this.showNotice('Error updating donation status.', 'error');
            }
        }
        
        async sendReceipt(donationId) {
            try {
                const response = await this.makeAjaxRequest('send_donation_receipt', {
                    donation_id: donationId
                });
                
                if (response.success) {
                    this.showNotice('Receipt sent successfully.', 'success');
                } else {
                    this.showNotice('Failed to send receipt.', 'error');
                }
            } catch (error) {
                console.error('Error sending receipt:', error);
                this.showNotice('Error sending receipt.', 'error');
            }
        }
        
        async processRefund(donationId) {
            if (!confirm('Are you sure you want to process a refund for this donation?')) {
                return;
            }
            
            try {
                const response = await this.makeAjaxRequest('process_donation_refund', {
                    donation_id: donationId
                });
                
                if (response.success) {
                    this.showNotice('Refund processed successfully.', 'success');
                    this.refreshDonationsTable();
                    this.closeDonationModal();
                } else {
                    this.showNotice('Failed to process refund: ' + response.message, 'error');
                }
            } catch (error) {
                console.error('Error processing refund:', error);
                this.showNotice('Error processing refund.', 'error');
            }
        }
        
        async refreshDonationsTable() {
            const $table = $('.donations-table-container');
            if (!$table.length) return;
            
            try {
                const response = await this.makeAjaxRequest('refresh_donations_table', {
                    page: new URLSearchParams(window.location.search).get('paged') || 1
                });
                
                if (response.success) {
                    $table.html(response.data.table_html);
                    this.setupStatusUpdates();
                }
            } catch (error) {
                console.error('Error refreshing table:', error);
            }
        }
        
        setupStatusUpdates() {
            $('.status-update').each(function() {
                $(this).off('change').on('change', function() {
                    const donationId = $(this).data('id');
                    const newStatus = $(this).val();
                    
                    if (newStatus) {
                        window.donationAdmin.updateDonationStatus(donationId, newStatus);
                    }
                });
            });
        }
        
        initializeCharts() {
            // Initialize charts if on analytics page
            if (typeof analyticsData !== 'undefined' && window.Chart) {
                this.createMonthlyChart();
                this.createPaymentMethodChart();
                this.createCurrencyChart();
                this.createStatusChart();
            }
        }
        
        createMonthlyChart() {
            const ctx = document.getElementById('monthlyChart');
            if (!ctx || !analyticsData.monthly_data) return;
            
            this.charts.monthly = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: analyticsData.monthly_data.map(item => item.month),
                    datasets: [{
                        label: 'Monthly Donations',
                        data: analyticsData.monthly_data.map(item => item.amount),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        createPaymentMethodChart() {
            const ctx = document.getElementById('paymentMethodChart');
            if (!ctx || !analyticsData.payment_methods) return;
            
            this.charts.paymentMethod = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: analyticsData.payment_methods.map(item => item.method),
                    datasets: [{
                        data: analyticsData.payment_methods.map(item => item.count),
                        backgroundColor: [
                            '#28a745',
                            '#17a2b8',
                            '#ffc107',
                            '#dc3545',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        createCurrencyChart() {
            const ctx = document.getElementById('currencyChart');
            if (!ctx || !analyticsData.currencies) return;
            
            this.charts.currency = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: analyticsData.currencies.map(item => item.currency),
                    datasets: [{
                        data: analyticsData.currencies.map(item => item.amount),
                        backgroundColor: ['#28a745', '#17a2b8']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        createStatusChart() {
            const ctx = document.getElementById('statusChart');
            if (!ctx || !analyticsData.statuses) return;
            
            this.charts.status = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: analyticsData.statuses.map(item => item.status),
                    datasets: [{
                        label: 'Donations by Status',
                        data: analyticsData.statuses.map(item => item.count),
                        backgroundColor: [
                            '#28a745', // completed
                            '#ffc107', // pending
                            '#dc3545', // failed
                            '#6c757d'  // refunded
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        async exportDonations($form) {
            const formData = new FormData($form[0]);
            
            try {
                const response = await fetch(kilismileAdmin.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'donations-export.' + formData.get('export_format');
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showNotice('Export completed successfully.', 'success');
                } else {
                    this.showNotice('Export failed. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Export error:', error);
                this.showNotice('Export failed. Please try again.', 'error');
            }
        }
        
        showNotice(message, type = 'info') {
            const noticeClass = type === 'success' ? 'notice-success' : 
                               type === 'error' ? 'notice-error' : 'notice-info';
            
            const notice = $(`
                <div class="notice ${noticeClass} is-dismissible">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);
            
            $('.wrap').prepend(notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                notice.fadeOut();
            }, 5000);
            
            // Manual dismiss
            notice.find('.notice-dismiss').on('click', () => {
                notice.fadeOut();
            });
        }
        
        async makeAjaxRequest(action, data = {}) {
            const requestData = {
                action: 'kilismile_admin_' + action,
                nonce: kilismileAdmin.nonce,
                ...data
            };
            
            const response = await fetch(kilismileAdmin.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(requestData)
            });
            
            return await response.json();
        }
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        window.donationAdmin = new KiliSmileDonationAdmin();
        
        // Add custom styles
        const style = document.createElement('style');
        style.textContent = `
            .donation-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 100000;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .donation-modal {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .donation-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .donation-modal-header h3 {
                margin: 0;
            }
            
            .close-modal {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .donation-modal-content {
                padding: 20px;
            }
            
            .donation-details-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }
            
            .detail-section {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
            }
            
            .detail-section h4 {
                margin: 0 0 10px 0;
                color: #495057;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .detail-section p {
                margin: 5px 0;
                font-size: 14px;
            }
            
            .donation-actions {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            
            .status-update, .status-change {
                min-width: 150px;
            }
        `;
        document.head.appendChild(style);
    });
    
})(jQuery);


