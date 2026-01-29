<?php
/**
 * KiliSmile Payments - Currency Conversion System
 * 
 * @package KiliSmilePayments
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KiliSmile_Currency_Converter {
    
    private $api_key;
    private $cache_duration = 3600; // 1 hour
    private $supported_currencies = array('USD', 'TZS', 'EUR', 'GBP', 'KES', 'UGX', 'RWF');
    private $api_endpoints = array(
        'primary' => 'https://api.exchangerate-api.com/v4/latest/',
        'backup' => 'https://api.fixer.io/latest',
        'fallback' => 'https://open.er-api.com/v6/latest/'
    );
    
    public function __construct() {
        $this->api_key = get_option('kilismile_payments_exchange_api_key', '');
        
        // Add action hooks
        add_action('init', array($this, 'init_currency_converter'));
        add_action('wp_ajax_kilismile_convert_currency', array($this, 'ajax_convert_currency'));
        add_action('wp_ajax_nopriv_kilismile_convert_currency', array($this, 'ajax_convert_currency'));
        add_action('wp_ajax_kilismile_refresh_rates', array($this, 'ajax_refresh_rates'));
        
        // Schedule daily rate updates
        add_action('kilismile_daily_rate_update', array($this, 'update_exchange_rates'));
        
        if (!wp_next_scheduled('kilismile_daily_rate_update')) {
            wp_schedule_event(time(), 'daily', 'kilismile_daily_rate_update');
        }
    }
    
    /**
     * Initialize currency converter
     */
    public function init_currency_converter() {
        // Ensure exchange rates are available
        $this->maybe_update_rates();
    }
    
    /**
     * Get supported currencies
     */
    public function get_supported_currencies() {
        return apply_filters('kilismile_supported_currencies', $this->supported_currencies);
    }
    
    /**
     * Get currency symbol
     */
    public function get_currency_symbol($currency) {
        $symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'TZS' => 'TSh',
            'KES' => 'KSh',
            'UGX' => 'USh',
            'RWF' => 'RWF'
        );
        
        return isset($symbols[$currency]) ? $symbols[$currency] : $currency . ' ';
    }
    
    /**
     * Get currency names
     */
    public function get_currency_names() {
        return array(
            'USD' => __('US Dollar', 'kilismile-payments'),
            'EUR' => __('Euro', 'kilismile-payments'),
            'GBP' => __('British Pound', 'kilismile-payments'),
            'TZS' => __('Tanzanian Shilling', 'kilismile-payments'),
            'KES' => __('Kenyan Shilling', 'kilismile-payments'),
            'UGX' => __('Ugandan Shilling', 'kilismile-payments'),
            'RWF' => __('Rwandan Franc', 'kilismile-payments')
        );
    }
    
    /**
     * Convert amount between currencies
     */
    public function convert($amount, $from_currency, $to_currency) {
        // If same currency, return original amount
        if ($from_currency === $to_currency) {
            return floatval($amount);
        }
        
        // Get exchange rate
        $rate = $this->get_exchange_rate($from_currency, $to_currency);
        
        if ($rate === false) {
            // Fallback to manual rates if API fails
            $rate = $this->get_fallback_rate($from_currency, $to_currency);
        }
        
        if ($rate === false) {
            return false;
        }
        
        $converted_amount = floatval($amount) * $rate;
        
        // Log conversion for debugging
        $this->log_conversion($amount, $from_currency, $to_currency, $converted_amount, $rate);
        
        return round($converted_amount, 2);
    }
    
    /**
     * Get exchange rate between two currencies
     */
    public function get_exchange_rate($from_currency, $to_currency) {
        if ($from_currency === $to_currency) {
            return 1.0;
        }
        
        // Try to get from cache first
        $cache_key = "kilismile_rate_{$from_currency}_{$to_currency}";
        $cached_rate = get_transient($cache_key);
        
        if ($cached_rate !== false) {
            return floatval($cached_rate);
        }
        
        // Get rates from API
        $rates = $this->fetch_exchange_rates($from_currency);
        
        if ($rates && isset($rates[$to_currency])) {
            $rate = floatval($rates[$to_currency]);
            
            // Cache the rate
            set_transient($cache_key, $rate, $this->cache_duration);
            
            return $rate;
        }
        
        return false;
    }
    
    /**
     * Fetch exchange rates from API
     */
    private function fetch_exchange_rates($base_currency = 'USD') {
        // Try primary API first
        $rates = $this->fetch_from_api($this->api_endpoints['primary'] . $base_currency);
        
        if ($rates) {
            return $rates;
        }
        
        // Try backup API
        if (!empty($this->api_key)) {
            $backup_url = $this->api_endpoints['backup'] . '?access_key=' . $this->api_key . '&base=' . $base_currency;
            $rates = $this->fetch_from_api($backup_url, 'fixer');
            
            if ($rates) {
                return $rates;
            }
        }
        
        // Try fallback API
        $rates = $this->fetch_from_api($this->api_endpoints['fallback'] . $base_currency);
        
        return $rates;
    }
    
    /**
     * Fetch data from API endpoint
     */
    private function fetch_from_api($url, $api_type = 'default') {
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'KiliSmile Payments Currency Converter'
            )
        ));
        
        if (is_wp_error($response)) {
            error_log('KiliSmile Currency API Error: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('KiliSmile Currency API JSON Error: ' . json_last_error_msg());
            return false;
        }
        
        // Handle different API response formats
        switch ($api_type) {
            case 'fixer':
                return isset($data['rates']) ? $data['rates'] : false;
            
            default:
                return isset($data['rates']) ? $data['rates'] : false;
        }
    }
    
    /**
     * Get fallback exchange rates (manual/hardcoded)
     */
    private function get_fallback_rate($from_currency, $to_currency) {
        // Hardcoded rates as emergency fallback (approximate rates)
        $fallback_rates = array(
            'USD' => array(
                'TZS' => 2400,
                'EUR' => 0.85,
                'GBP' => 0.73,
                'KES' => 150,
                'UGX' => 3700,
                'RWF' => 1300
            ),
            'TZS' => array(
                'USD' => 0.00042,
                'EUR' => 0.00035,
                'GBP' => 0.0003,
                'KES' => 0.063,
                'UGX' => 1.54,
                'RWF' => 0.54
            )
        );
        
        if (isset($fallback_rates[$from_currency][$to_currency])) {
            return $fallback_rates[$from_currency][$to_currency];
        }
        
        // Try reverse calculation
        if (isset($fallback_rates[$to_currency][$from_currency])) {
            return 1 / $fallback_rates[$to_currency][$from_currency];
        }
        
        return false;
    }
    
    /**
     * Update exchange rates (called by cron)
     */
    public function update_exchange_rates() {
        $supported_currencies = $this->get_supported_currencies();
        
        foreach ($supported_currencies as $base_currency) {
            $rates = $this->fetch_exchange_rates($base_currency);
            
            if ($rates) {
                // Store rates for this base currency
                update_option("kilismile_rates_{$base_currency}", $rates);
                update_option("kilismile_rates_{$base_currency}_updated", current_time('timestamp'));
            }
            
            // Add small delay to avoid rate limiting
            sleep(1);
        }
    }
    
    /**
     * Maybe update rates if they're stale
     */
    private function maybe_update_rates() {
        $last_update = get_option('kilismile_rates_USD_updated', 0);
        $now = current_time('timestamp');
        
        // Update if rates are older than cache duration
        if (($now - $last_update) > $this->cache_duration) {
            wp_schedule_single_event(time() + 60, 'kilismile_daily_rate_update');
        }
    }
    
    /**
     * Format amount with currency
     */
    public function format_amount($amount, $currency, $include_symbol = true) {
        $amount = floatval($amount);
        $symbol = $this->get_currency_symbol($currency);
        
        // Get formatting options
        $decimal_places = get_option('kilismile_payments_decimal_places', 2);
        $decimal_separator = get_option('kilismile_payments_decimal_separator', '.');
        $thousand_separator = get_option('kilismile_payments_thousand_separator', ',');
        $currency_position = get_option('kilismile_payments_currency_position', 'left');
        
        $formatted_amount = number_format($amount, $decimal_places, $decimal_separator, $thousand_separator);
        
        if (!$include_symbol) {
            return $formatted_amount;
        }
        
        switch ($currency_position) {
            case 'left':
                return $symbol . $formatted_amount;
            case 'right':
                return $formatted_amount . $symbol;
            case 'left_space':
                return $symbol . ' ' . $formatted_amount;
            case 'right_space':
                return $formatted_amount . ' ' . $symbol;
            default:
                return $symbol . $formatted_amount;
        }
    }
    
    /**
     * Get conversion rate display
     */
    public function get_rate_display($from_currency, $to_currency) {
        $rate = $this->get_exchange_rate($from_currency, $to_currency);
        
        if ($rate === false) {
            return __('Rate unavailable', 'kilismile-payments');
        }
        
        $from_symbol = $this->get_currency_symbol($from_currency);
        $to_symbol = $this->get_currency_symbol($to_currency);
        
        return sprintf(
            __('%s1 %s = %s%s %s', 'kilismile-payments'),
            $from_symbol,
            $from_currency,
            $to_symbol,
            number_format($rate, 4),
            $to_currency
        );
    }
    
    /**
     * AJAX handler for currency conversion
     */
    public function ajax_convert_currency() {
        check_ajax_referer('kilismile_currency_nonce', 'nonce');
        
        $amount = floatval($_POST['amount']);
        $from_currency = sanitize_text_field($_POST['from_currency']);
        $to_currency = sanitize_text_field($_POST['to_currency']);
        
        if ($amount <= 0) {
            wp_send_json_error(__('Invalid amount', 'kilismile-payments'));
        }
        
        $converted_amount = $this->convert($amount, $from_currency, $to_currency);
        
        if ($converted_amount === false) {
            wp_send_json_error(__('Conversion failed', 'kilismile-payments'));
        }
        
        wp_send_json_success(array(
            'converted_amount' => $converted_amount,
            'formatted_amount' => $this->format_amount($converted_amount, $to_currency),
            'rate_display' => $this->get_rate_display($from_currency, $to_currency)
        ));
    }
    
    /**
     * AJAX handler for refreshing rates
     */
    public function ajax_refresh_rates() {
        check_ajax_referer('kilismile_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'kilismile-payments'));
        }
        
        // Clear cached rates
        $this->clear_rate_cache();
        
        // Force update rates
        $this->update_exchange_rates();
        
        wp_send_json_success(__('Exchange rates updated successfully', 'kilismile-payments'));
    }
    
    /**
     * Clear rate cache
     */
    public function clear_rate_cache() {
        $supported_currencies = $this->get_supported_currencies();
        
        foreach ($supported_currencies as $from_currency) {
            foreach ($supported_currencies as $to_currency) {
                if ($from_currency !== $to_currency) {
                    delete_transient("kilismile_rate_{$from_currency}_{$to_currency}");
                }
            }
        }
    }
    
    /**
     * Log currency conversion
     */
    private function log_conversion($amount, $from_currency, $to_currency, $converted_amount, $rate) {
        if (get_option('kilismile_payments_debug_mode', 0)) {
            error_log(sprintf(
                'KiliSmile Currency Conversion: %s %s -> %s %s (Rate: %s)',
                number_format($amount, 2),
                $from_currency,
                number_format($converted_amount, 2),
                $to_currency,
                number_format($rate, 6)
            ));
        }
    }
    
    /**
     * Get currency selector HTML
     */
    public function get_currency_selector($selected_currency = 'USD', $name = 'currency', $id = '') {
        $currencies = $this->get_supported_currencies();
        $currency_names = $this->get_currency_names();
        
        $id_attr = $id ? 'id="' . esc_attr($id) . '"' : '';
        
        $html = '<select name="' . esc_attr($name) . '" ' . $id_attr . ' class="kilismile-currency-selector">';
        
        foreach ($currencies as $currency) {
            $selected = selected($selected_currency, $currency, false);
            $symbol = $this->get_currency_symbol($currency);
            $name_display = isset($currency_names[$currency]) ? $currency_names[$currency] : $currency;
            
            $html .= sprintf(
                '<option value="%s" %s>%s - %s (%s)</option>',
                esc_attr($currency),
                $selected,
                esc_html($currency),
                esc_html($name_display),
                esc_html($symbol)
            );
        }
        
        $html .= '</select>';
        
        return $html;
    }
    
    /**
     * Add currency conversion JavaScript
     */
    public function add_conversion_script() {
        $supported_currencies = $this->get_supported_currencies();
        ?>
        <script>
        (function($) {
            'use strict';
            
            const CurrencyConverter = {
                init: function() {
                    this.bindEvents();
                    this.loadCachedRates();
                },
                
                bindEvents: function() {
                    $(document).on('change', '.kilismile-currency-selector', this.handleCurrencyChange);
                    $(document).on('input', '.kilismile-amount-input', this.handleAmountChange);
                    $(document).on('click', '.kilismile-refresh-rates', this.refreshRates);
                },
                
                handleCurrencyChange: function() {
                    const $form = $(this).closest('form');
                    const $amountInput = $form.find('.kilismile-amount-input');
                    
                    if ($amountInput.val()) {
                        CurrencyConverter.convertAmount($form);
                    }
                    
                    CurrencyConverter.updateCurrencyDisplay($form);
                },
                
                handleAmountChange: function() {
                    const $form = $(this).closest('form');
                    CurrencyConverter.convertAmount($form);
                },
                
                convertAmount: function($form) {
                    const amount = parseFloat($form.find('.kilismile-amount-input').val());
                    const fromCurrency = $form.find('.kilismile-currency-selector').val();
                    const toCurrency = $form.find('.kilismile-display-currency').val();
                    
                    if (!amount || amount <= 0 || fromCurrency === toCurrency) {
                        return;
                    }
                    
                    $.ajax({
                        url: kilismileAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'kilismile_convert_currency',
                            amount: amount,
                            from_currency: fromCurrency,
                            to_currency: toCurrency,
                            nonce: kilismileAjax.currencyNonce
                        },
                        success: function(response) {
                            if (response.success) {
                                $form.find('.kilismile-converted-amount').html(response.data.formatted_amount);
                                $form.find('.kilismile-rate-display').html(response.data.rate_display);
                            }
                        }
                    });
                },
                
                updateCurrencyDisplay: function($form) {
                    const currency = $form.find('.kilismile-currency-selector').val();
                    const symbols = <?php echo json_encode(array_map(array($this, 'get_currency_symbol'), $this->get_supported_currencies())); ?>;
                    
                    $form.find('.kilismile-currency-symbol').text(symbols[currency] || currency + ' ');
                },
                
                refreshRates: function(e) {
                    e.preventDefault();
                    
                    const $button = $(this);
                    const originalText = $button.text();
                    
                    $button.prop('disabled', true).text('<?php esc_js(_e('Refreshing...', 'kilismile-payments')); ?>');
                    
                    $.ajax({
                        url: kilismileAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'kilismile_refresh_rates',
                            nonce: kilismileAjax.adminNonce
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.data);
                                location.reload();
                            } else {
                                alert('<?php esc_js(_e('Error refreshing rates', 'kilismile-payments')); ?>');
                            }
                        },
                        complete: function() {
                            $button.prop('disabled', false).text(originalText);
                        }
                    });
                },
                
                loadCachedRates: function() {
                    // Load any cached conversion rates to avoid API calls
                    const cachedRates = localStorage.getItem('kilismile_cached_rates');
                    if (cachedRates) {
                        this.cachedRates = JSON.parse(cachedRates);
                    }
                }
            };
            
            $(document).ready(function() {
                CurrencyConverter.init();
            });
            
        })(jQuery);
        </script>
        <?php
    }
    
    /**
     * Get rate information for admin display
     */
    public function get_rate_info() {
        $info = array();
        $supported_currencies = $this->get_supported_currencies();
        
        foreach ($supported_currencies as $currency) {
            $last_update = get_option("kilismile_rates_{$currency}_updated", 0);
            $rates = get_option("kilismile_rates_{$currency}", array());
            
            $info[$currency] = array(
                'last_update' => $last_update,
                'rates_count' => count($rates),
                'status' => $last_update > (current_time('timestamp') - $this->cache_duration) ? 'fresh' : 'stale'
            );
        }
        
        return $info;
    }
}

// Initialize currency converter
new KiliSmile_Currency_Converter();

