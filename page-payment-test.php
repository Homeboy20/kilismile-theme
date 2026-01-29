<?php
/**
 * Template Name: Payment Test Page
 * Test page for payment form functionality
 */

get_header(); ?>

<main class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <article class="page-content">
                    <header class="page-header">
                        <h1 class="page-title">Payment System Test</h1>
                        <p class="page-description">Test the integrated payment system with PayPal and AzamPay</p>
                    </header>

                    <div class="payment-test-content">
                        <div class="test-info">
                            <h3>Testing Instructions</h3>
                            <ul>
                                <li><strong>USD Payments:</strong> Automatically uses PayPal</li>
                                <li><strong>TZS Payments:</strong> Uses AzamPay with mobile money</li>
                                <li><strong>Mobile Networks:</strong> Select Vodacom M-Pesa, Airtel Money, Tigo Pesa, or HaloPesa</li>
                                <li><strong>Test Mode:</strong> Both gateways are in sandbox/test mode</li>
                            </ul>
                        </div>

                        <div class="payment-form-section">
                            <h3>Make a Test Donation</h3>
                            <?php echo do_shortcode('[kilismile_payment_form title="Test Payment" description="Try our integrated payment system with automatic gateway selection"]'); ?>
                        </div>

                        <div class="test-notes">
                            <h3>Test Notes</h3>
                            <div class="notes-grid">
                                <div class="note-item">
                                    <h4>PayPal Testing</h4>
                                    <p>Use PayPal sandbox credentials for USD payments. The system will redirect to PayPal's test environment.</p>
                                </div>
                                <div class="note-item">
                                    <h4>AzamPay Testing</h4>
                                    <p>Use test phone numbers for TZS payments. STK Push requests will be sent to the sandbox environment.</p>
                                </div>
                                <div class="note-item">
                                    <h4>Network Selection</h4>
                                    <p>Choose your mobile network for TZS payments. Each network has different STK Push formats.</p>
                                </div>
                                <div class="note-item">
                                    <h4>Transaction Tracking</h4>
                                    <p>All transactions are stored in the database with status updates for monitoring.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>

<style>
.payment-test-content {
    margin-top: 2rem;
}

.test-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.test-info h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.test-info ul {
    list-style: none;
    padding: 0;
}

.test-info li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.test-info li:last-child {
    border-bottom: none;
}

.test-info strong {
    color: #27ae60;
}

.payment-form-section {
    margin: 2rem 0;
    padding: 2rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: #fff;
}

.test-notes {
    margin-top: 2rem;
}

.notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.note-item {
    background: #fff;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.note-item h4 {
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.note-item p {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0;
}

@media (max-width: 768px) {
    .notes-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-form-section {
        padding: 1rem;
    }
}
</style>

<?php get_footer(); ?>

