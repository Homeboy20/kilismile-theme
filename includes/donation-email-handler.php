<?php
/**
 * Donation Email Handler
 * 
 * Handles all email communications related to donations
 * including confirmations, receipts, and notifications.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donation Email Handler Class
 */
class KiliSmile_Donation_Email_Handler {
    
    private $from_email;
    private $from_name;
    private $template_path;
    
    public function __construct() {
        $this->from_email = get_option('kilismile_donation_from_email', get_option('admin_email'));
        $this->from_name = get_option('kilismile_donation_from_name', get_bloginfo('name'));
        $this->template_path = get_template_directory() . '/email-templates/';
    }
    
    /**
     * Send donation confirmation email
     */
    public function send_donation_confirmation($donation_data) {
        $subject = sprintf(__('Thank you for your donation - %s', 'kilismile'), $this->from_name);
        
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['donation_id'],
            'payment_method' => $donation_data['payment_method'],
            'recurring' => $donation_data['recurring'],
            'purpose' => $donation_data['purpose'],
            'date' => current_time('F j, Y'),
            'organization_name' => $this->from_name,
            'tax_deductible' => true
        );
        
        $message = $this->load_template('donation_confirmation', $template_data);
        
        return $this->send_email($donation_data['email'], $subject, $message);
    }
    
    /**
     * Send donation receipt email (after payment completion)
     */
    public function send_donation_receipt($donation_data, $transaction_id = null) {
        $subject = sprintf(__('Donation Receipt - %s', 'kilismile'), $donation_data['donation_id']);
        
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['donation_id'],
            'transaction_id' => $transaction_id,
            'payment_method' => $donation_data['payment_method'],
            'recurring' => $donation_data['recurring'],
            'purpose' => $donation_data['purpose'],
            'date' => current_time('F j, Y'),
            'organization_name' => $this->from_name,
            'tax_id' => get_option('kilismile_tax_id', ''),
            'receipt_number' => 'R-' . $donation_data['donation_id']
        );
        
        $message = $this->load_template('donation_receipt', $template_data);
        
        return $this->send_email($donation_data['email'], $subject, $message);
    }
    
    /**
     * Send admin notification email
     */
    public function send_admin_notification($donation_data) {
        $admin_emails = $this->get_admin_notification_emails();
        
        if (empty($admin_emails)) {
            return false;
        }
        
        $subject = sprintf(__('New Donation Received - %s %s', 'kilismile'), 
            $donation_data['currency'], 
            number_format($donation_data['amount'], 2)
        );
        
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'donor_email' => $donation_data['email'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['donation_id'],
            'payment_method' => $donation_data['payment_method'],
            'recurring' => $donation_data['recurring'],
            'purpose' => $donation_data['purpose'],
            'message' => $donation_data['message'],
            'date' => current_time('F j, Y g:i A'),
            'admin_url' => admin_url('admin.php?page=kilismile-donations&donation_id=' . $donation_data['donation_id'])
        );
        
        $message = $this->load_template('admin_notification', $template_data);
        
        $results = array();
        foreach ($admin_emails as $email) {
            $results[] = $this->send_email($email, $subject, $message);
        }
        
        return !in_array(false, $results);
    }

    /**
     * Send manual payment instructions (bank transfer / mobile money transfer)
     */
    public function send_manual_payment_instructions($donation_data, $instructions = array()) {
        $subject = sprintf(__('Payment Instructions (Manual Transfer) - %s', 'kilismile'), $donation_data['donation_id']);

        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['donation_id'],
            'date' => current_time('F j, Y'),
            'organization_name' => $this->from_name,
            'instructions' => is_array($instructions) ? $instructions : array()
        );

        $message = $this->load_template('manual_payment_instructions', $template_data);

        return $this->send_email($donation_data['email'], $subject, $message);
    }
    
    /**
     * Send recurring donation reminder
     */
    public function send_recurring_reminder($donation_data, $next_payment_date) {
        $subject = sprintf(__('Upcoming Monthly Donation - %s', 'kilismile'), $this->from_name);
        
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'next_payment_date' => $next_payment_date,
            'donation_id' => $donation_data['donation_id'],
            'organization_name' => $this->from_name,
            'cancel_url' => home_url('/cancel-recurring/?donation_id=' . $donation_data['donation_id'])
        );
        
        $message = $this->load_template('recurring_reminder', $template_data);
        
        return $this->send_email($donation_data['email'], $subject, $message);
    }
    
    /**
     * Send payment failed notification
     */
    public function send_payment_failed_notification($donation_data, $error_message = '') {
        $subject = sprintf(__('Payment Issue - %s', 'kilismile'), $this->from_name);
        
        $template_data = array(
            'donor_name' => $donation_data['first_name'] . ' ' . $donation_data['last_name'],
            'amount' => $donation_data['amount'],
            'currency' => $donation_data['currency'],
            'donation_id' => $donation_data['donation_id'],
            'error_message' => $error_message,
            'retry_url' => home_url('/donate/?retry=' . $donation_data['donation_id']),
            'organization_name' => $this->from_name
        );
        
        $message = $this->load_template('payment_failed', $template_data);
        
        return $this->send_email($donation_data['email'], $subject, $message);
    }
    
    /**
     * Load email template
     */
    private function load_template($template_name, $data = array()) {
        $template_file = $this->template_path . $template_name . '.php';
        
        if (file_exists($template_file)) {
            ob_start();
            extract($data);
            include $template_file;
            return ob_get_clean();
        }
        
        // Fallback to built-in templates
        return $this->get_built_in_template($template_name, $data);
    }
    
    /**
     * Get built-in email templates
     */
    private function get_built_in_template($template_name, $data) {
        switch ($template_name) {
            case 'donation_confirmation':
                return $this->get_confirmation_template($data);
            case 'donation_receipt':
                return $this->get_receipt_template($data);
            case 'admin_notification':
                return $this->get_admin_notification_template($data);
            case 'recurring_reminder':
                return $this->get_recurring_reminder_template($data);
            case 'payment_failed':
                return $this->get_payment_failed_template($data);
            case 'manual_payment_instructions':
                return $this->get_manual_payment_instructions_template($data);
            default:
                return $this->get_default_template($data);
        }
    }

    /**
     * Manual payment instructions template
     */
    private function get_manual_payment_instructions_template($data) {
        $currency_symbol = $data['currency'] === 'USD' ? '$' : 'TSh ';
        $amount_formatted = $currency_symbol . number_format($data['amount'], 2);

        $instruction_lines = '';
        if (!empty($data['instructions']) && is_array($data['instructions'])) {
            $instruction_lines .= '<ol style="margin: 12px 0 0 18px;">';
            foreach ($data['instructions'] as $line) {
                $instruction_lines .= '<li style="margin: 6px 0;">' . esc_html($line) . '</li>';
            }
            $instruction_lines .= '</ol>';
        }

        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0b5ed7; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 16px 0; }
                .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Manual Payment Instructions</h1>
                </div>
                <div class='content'>
                    <p>Dear {$data['donor_name']},</p>
                    <p>Thank you for choosing to donate to {$data['organization_name']}. Please complete your transfer using the instructions below.</p>

                    <div class='panel'>
                        <strong>Donation Summary</strong><br>
                        Amount: {$amount_formatted}<br>
                        Donation ID / Reference: <span class='mono'>{$data['donation_id']}</span><br>
                        Date: {$data['date']}
                    </div>

                    <div class='panel'>
                        <strong>Steps</strong>
                        {$instruction_lines}
                    </div>

                    <p>If you have any questions, just reply to this email.</p>
                </div>
                <div class='footer'>
                    <p>{$data['organization_name']} • Thank you for your support</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Donation confirmation template
     */
    private function get_confirmation_template($data) {
        $currency_symbol = $data['currency'] === 'USD' ? '$' : 'TSh ';
        $amount_formatted = $currency_symbol . number_format($data['amount'], 2);
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
                .highlight { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Thank You for Your Donation!</h1>
                </div>
                <div class='content'>
                    <p>Dear {$data['donor_name']},</p>
                    
                    <p>Thank you so much for your generous donation to {$data['organization_name']}. Your support makes a real difference in the lives of those we serve.</p>
                    
                    <div class='highlight'>
                        <strong>Donation Details:</strong><br>
                        Amount: {$amount_formatted}<br>
                        Donation ID: {$data['donation_id']}<br>
                        Date: {$data['date']}<br>
                        Purpose: {$data['purpose']}<br>
                        " . ($data['recurring'] ? 'Type: Monthly Recurring Donation<br>' : '') . "
                    </div>
                    
                    <p>Your donation will help us continue our mission of bringing healthcare to remote communities in Tanzania. We'll send you a formal receipt once your payment is processed.</p>
                    
                    <p>If you have any questions about your donation, please don't hesitate to contact us.</p>
                    
                    <p>With gratitude,<br>
                    The {$data['organization_name']} Team</p>
                </div>
                <div class='footer'>
                    This donation may be tax-deductible. Please consult your tax advisor.
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Donation receipt template
     */
    private function get_receipt_template($data) {
        $currency_symbol = $data['currency'] === 'USD' ? '$' : 'TSh ';
        $amount_formatted = $currency_symbol . number_format($data['amount'], 2);
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .receipt-box { border: 2px solid #28a745; padding: 20px; margin: 20px 0; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Official Donation Receipt</h1>
                </div>
                <div class='content'>
                    <div class='receipt-box'>
                        <h2>Receipt #{$data['receipt_number']}</h2>
                        <table width='100%'>
                            <tr><td><strong>Donor:</strong></td><td>{$data['donor_name']}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>{$amount_formatted}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>{$data['date']}</td></tr>
                            <tr><td><strong>Donation ID:</strong></td><td>{$data['donation_id']}</td></tr>
                            " . ($data['transaction_id'] ? "<tr><td><strong>Transaction ID:</strong></td><td>{$data['transaction_id']}</td></tr>" : '') . "
                            <tr><td><strong>Purpose:</strong></td><td>{$data['purpose']}</td></tr>
                            <tr><td><strong>Method:</strong></td><td>{$data['payment_method']}</td></tr>
                        </table>
                    </div>
                    
                    <p>This serves as your official receipt for tax purposes. Please keep this for your records.</p>
                    
                    <p>Thank you for supporting {$data['organization_name']} and our mission.</p>
                </div>
                <div class='footer'>
                    {$data['organization_name']} - Tax ID: {$data['tax_id']}
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Admin notification template
     */
    private function get_admin_notification_template($data) {
        $currency_symbol = $data['currency'] === 'USD' ? '$' : 'TSh ';
        $amount_formatted = $currency_symbol . number_format($data['amount'], 2);
        
        return "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>New Donation Received</h2>
            
            <p><strong>Donation Details:</strong></p>
            <ul>
                <li>Donor: {$data['donor_name']} ({$data['donor_email']})</li>
                <li>Amount: {$amount_formatted}</li>
                <li>Donation ID: {$data['donation_id']}</li>
                <li>Payment Method: {$data['payment_method']}</li>
                <li>Purpose: {$data['purpose']}</li>
                <li>Date: {$data['date']}</li>
                " . ($data['recurring'] ? '<li>Type: Recurring Monthly</li>' : '') . "
                " . (!empty($data['message']) ? '<li>Message: ' . $data['message'] . '</li>' : '') . "
            </ul>
            
            <p><a href='{$data['admin_url']}'>View in Admin Dashboard</a></p>
        </body>
        </html>";
    }
    
    /**
     * Send email using WordPress wp_mail
     */
    private function send_email($to, $subject, $message) {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>'
        );
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Get admin notification emails
     */
    private function get_admin_notification_emails() {
        $emails = get_option('kilismile_admin_notification_emails', array());
        
        if (empty($emails)) {
            $emails = array(get_option('admin_email'));
        }
        
        return array_filter($emails);
    }
    
    /**
     * Additional template methods would go here...
     */
    private function get_recurring_reminder_template($data) { /* ... */ }
    private function get_payment_failed_template($data) { /* ... */ }
    private function get_default_template($data) { /* ... */ }
}


