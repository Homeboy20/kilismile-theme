<?php
/**
 * Enhanced KiliSmile Settings Field Renderers
 * 
 * Custom field types and rendering methods for the theme settings
 * 
 * @package KiliSmile
 * @version 3.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * KiliSmile Settings Field Renderers Class
 */
class KiliSmile_Settings_Field_Renderers {
    
    /**
     * Render Radio Image Field
     */
    public static function render_radio_image($field_id, $field_name, $field_value, $field) {
        echo '<div class="radio-image-options">';
        
        foreach ($field['options'] as $value => $option) {
            $checked = ($field_value == $value) ? 'selected' : '';
            $image_url = isset($option['image']) ? $option['image'] : '';
            $label = isset($option['label']) ? $option['label'] : $value;
            
            echo '<div class="radio-image-option ' . $checked . '" data-value="' . esc_attr($value) . '">';
            echo '<input type="radio" id="' . esc_attr($field_id . '_' . $value) . '" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" ' . checked($field_value, $value, false) . '>';
            
            if ($image_url) {
                echo '<div class="radio-image-preview" style="background-image: url(' . esc_url($image_url) . ')"></div>';
            }
            
            echo '<div class="radio-image-label">' . esc_html($label) . '</div>';
            echo '<div class="check-icon"><span class="dashicons dashicons-yes"></span></div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render Color Palette Field
     */
    public static function render_color_palette($field_id, $field_name, $field_value, $field) {
        echo '<div class="color-palette-grid">';
        
        foreach ($field['colors'] as $color_key => $color_config) {
            $color_value = isset($field_value[$color_key]) ? $field_value[$color_key] : $color_config['default'];
            
            echo '<div class="color-palette-item">';
            echo '<label class="color-palette-label">' . esc_html($color_config['label']) . '</label>';
            echo '<div class="color-palette-input">';
            echo '<input type="color" class="color-picker" value="' . esc_attr($color_value) . '">';
            echo '<input type="text" name="' . esc_attr($field_name . '[' . $color_key . ']') . '" value="' . esc_attr($color_value) . '" placeholder="#000000">';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render Typography Field
     */
    public static function render_typography($field_id, $field_name, $field_value, $field) {
        $google_fonts = self::get_google_fonts();
        
        echo '<div class="typography-controls">';
        
        foreach ($field['fonts'] as $font_key => $font_config) {
            $font_value = isset($field_value[$font_key]) ? $field_value[$font_key] : $font_config['default'];
            
            echo '<div class="typography-font">';
            echo '<label>' . esc_html($font_config['label']) . '</label>';
            echo '<select name="' . esc_attr($field_name . '[' . $font_key . ']') . '" class="typography-font-select">';
            
            // System fonts
            echo '<optgroup label="System Fonts">';
            $system_fonts = array(
                'Arial, sans-serif' => 'Arial',
                'Helvetica, sans-serif' => 'Helvetica',
                'Georgia, serif' => 'Georgia',
                'Times New Roman, serif' => 'Times New Roman',
                'Courier New, monospace' => 'Courier New'
            );
            
            foreach ($system_fonts as $font_family => $font_name) {
                echo '<option value="' . esc_attr($font_family) . '"' . selected($font_value, $font_family, false) . '>' . esc_html($font_name) . '</option>';
            }
            echo '</optgroup>';
            
            // Google fonts
            echo '<optgroup label="Google Fonts">';
            foreach ($google_fonts as $font_family => $font_name) {
                echo '<option value="' . esc_attr($font_family) . '"' . selected($font_value, $font_family, false) . '>' . esc_html($font_name) . '</option>';
            }
            echo '</optgroup>';
            
            echo '</select>';
            echo '<div class="font-preview" style="font-family: ' . esc_attr($font_value) . '">The quick brown fox jumps over the lazy dog</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render Slider Field
     */
    public static function render_slider($field_id, $field_name, $field_value, $field) {
        $min = isset($field['min']) ? $field['min'] : 0;
        $max = isset($field['max']) ? $field['max'] : 100;
        $step = isset($field['step']) ? $field['step'] : 1;
        $suffix = isset($field['suffix']) ? $field['suffix'] : '';
        
        echo '<div class="slider-control">';
        echo '<input type="range" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" class="slider-input" ';
        echo 'min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" step="' . esc_attr($step) . '" ';
        echo 'value="' . esc_attr($field_value) . '" data-suffix="' . esc_attr($suffix) . '">';
        echo '<div class="slider-value">' . esc_html($field_value . $suffix) . '</div>';
        echo '</div>';
    }
    
    /**
     * Render Code Editor Field
     */
    public static function render_code_editor($field_id, $field_name, $field_value, $field) {
        $language = isset($field['language']) ? $field['language'] : 'css';
        $theme = isset($field['theme']) ? $field['theme'] : 'default';
        
        echo '<div class="code-editor-container">';
        echo '<div class="code-editor-toolbar">';
        echo '<span class="editor-language">' . esc_html(strtoupper($language)) . '</span>';
        echo '<div class="editor-actions">';
        echo '<button type="button" class="button button-small editor-fullscreen" title="Fullscreen"><span class="dashicons dashicons-fullscreen-alt"></span></button>';
        echo '<button type="button" class="button button-small editor-format" title="Format Code"><span class="dashicons dashicons-editor-code"></span></button>';
        echo '</div>';
        echo '</div>';
        echo '<textarea id="' . esc_attr($field_id) . '" name="' . esc_attr($field_name) . '" class="code-editor-textarea" ';
        echo 'data-language="' . esc_attr($language) . '" data-theme="' . esc_attr($theme) . '">';
        echo esc_textarea($field_value);
        echo '</textarea>';
        echo '</div>';
    }
    
    /**
     * Render Group Field
     */
    public static function render_group($field_id, $field_name, $field_value, $field) {
        echo '<div class="group-fields">';
        
        foreach ($field['fields'] as $subfield_key => $subfield_config) {
            $subfield_value = isset($field_value[$subfield_key]) ? $field_value[$subfield_key] : ($subfield_config['default'] ?? '');
            $subfield_name = $field_name . '[' . $subfield_key . ']';
            $subfield_id = $field_id . '_' . $subfield_key;
            
            echo '<div class="group-field">';
            echo '<label for="' . esc_attr($subfield_id) . '">' . esc_html($subfield_config['title']) . '</label>';
            
            switch ($subfield_config['type']) {
                case 'text':
                    echo '<input type="text" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="' . esc_attr($subfield_value) . '"';
                    if (isset($subfield_config['placeholder'])) {
                        echo ' placeholder="' . esc_attr($subfield_config['placeholder']) . '"';
                    }
                    echo '>';
                    break;
                    
                case 'email':
                    echo '<input type="email" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="' . esc_attr($subfield_value) . '"';
                    if (isset($subfield_config['placeholder'])) {
                        echo ' placeholder="' . esc_attr($subfield_config['placeholder']) . '"';
                    }
                    echo '>';
                    break;
                    
                case 'textarea':
                    $rows = isset($subfield_config['rows']) ? $subfield_config['rows'] : 3;
                    echo '<textarea id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" rows="' . esc_attr($rows) . '">' . esc_textarea($subfield_value) . '</textarea>';
                    break;
                    
                case 'toggle':
                    echo '<label class="toggle-switch">';
                    echo '<input type="checkbox" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="1"' . checked($subfield_value, 1, false) . '>';
                    echo '<span class="toggle-slider"></span>';
                    echo '</label>';
                    break;
                    
                case 'select':
                    echo '<select id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '">';
                    foreach ($subfield_config['options'] as $option_value => $option_label) {
                        echo '<option value="' . esc_attr($option_value) . '"' . selected($subfield_value, $option_value, false) . '>' . esc_html($option_label) . '</option>';
                    }
                    echo '</select>';
                    break;
                    
                case 'slider':
                    self::render_slider($subfield_id, $subfield_name, $subfield_value, $subfield_config);
                    break;
            }
            
            if (isset($subfield_config['description'])) {
                echo '<p class="field-description">' . esc_html($subfield_config['description']) . '</p>';
            }
            
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render Repeater Field
     */
    public static function render_repeater($field_id, $field_name, $field_value, $field) {
        $field_value = is_array($field_value) ? $field_value : array();
        
        echo '<div class="repeater-container">';
        echo '<div class="repeater-items">';
        
        if (!empty($field_value)) {
            foreach ($field_value as $index => $item_value) {
                self::render_repeater_item($field_id, $field_name, $index, $item_value, $field);
            }
        }
        
        echo '</div>';
        
        // Add template for new items
        echo '<script type="text/template" class="repeater-item-template">';
        self::render_repeater_item($field_id, $field_name, 0, array(), $field, true);
        echo '</script>';
        
        echo '<div class="repeater-add">';
        echo '<span class="dashicons dashicons-plus-alt"></span>';
        echo __('Add New Item', 'kilismile');
        echo '</div>';
        
        echo '</div>';
    }
    
    /**
     * Render Repeater Item
     */
    private static function render_repeater_item($field_id, $field_name, $index, $item_value, $field, $is_template = false) {
        $item_class = $is_template ? 'repeater-item-template' : 'repeater-item';
        
        echo '<div class="' . $item_class . '">';
        echo '<div class="repeater-item-header">';
        echo '<h4 class="repeater-item-title">' . sprintf(__('Item %d', 'kilismile'), $index + 1) . '</h4>';
        echo '<div class="repeater-item-actions">';
        echo '<button type="button" class="repeater-action move-up" title="Move Up"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
        echo '<button type="button" class="repeater-action move-down" title="Move Down"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
        echo '<button type="button" class="repeater-action delete" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="repeater-fields">';
        
        foreach ($field['fields'] as $subfield_key => $subfield_config) {
            $subfield_value = isset($item_value[$subfield_key]) ? $item_value[$subfield_key] : ($subfield_config['default'] ?? '');
            $subfield_name = $field_name . '[' . $index . '][' . $subfield_key . ']';
            $subfield_id = $field_id . '_' . $index . '_' . $subfield_key;
            
            echo '<div class="repeater-field">';
            echo '<label for="' . esc_attr($subfield_id) . '">' . esc_html($subfield_config['title']) . '</label>';
            
            switch ($subfield_config['type']) {
                case 'text':
                    echo '<input type="text" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="' . esc_attr($subfield_value) . '"';
                    if (isset($subfield_config['placeholder'])) {
                        echo ' placeholder="' . esc_attr($subfield_config['placeholder']) . '"';
                    }
                    echo '>';
                    break;
                    
                case 'number':
                    echo '<input type="number" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="' . esc_attr($subfield_value) . '"';
                    if (isset($subfield_config['min'])) echo ' min="' . esc_attr($subfield_config['min']) . '"';
                    if (isset($subfield_config['max'])) echo ' max="' . esc_attr($subfield_config['max']) . '"';
                    if (isset($subfield_config['step'])) echo ' step="' . esc_attr($subfield_config['step']) . '"';
                    echo '>';
                    break;
                    
                case 'select':
                    echo '<select id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '">';
                    foreach ($subfield_config['options'] as $option_value => $option_label) {
                        echo '<option value="' . esc_attr($option_value) . '"' . selected($subfield_value, $option_value, false) . '>' . esc_html($option_label) . '</option>';
                    }
                    echo '</select>';
                    break;
                    
                case 'date':
                    echo '<input type="date" id="' . esc_attr($subfield_id) . '" name="' . esc_attr($subfield_name) . '" value="' . esc_attr($subfield_value) . '">';
                    break;
            }
            
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Render Social Links Field
     */
    public static function render_social_links($field_id, $field_name, $field_value, $field) {
        echo '<div class="social-networks">';
        
        foreach ($field['networks'] as $network_key => $network_label) {
            $network_value = isset($field_value[$network_key]) ? $field_value[$network_key] : '';
            
            echo '<div class="social-network">';
            echo '<div class="social-icon ' . esc_attr($network_key) . '">';
            echo self::get_social_icon($network_key);
            echo '</div>';
            echo '<input type="url" name="' . esc_attr($field_name . '[' . $network_key . ']') . '" ';
            echo 'placeholder="' . sprintf(__('Enter your %s URL', 'kilismile'), $network_label) . '" ';
            echo 'value="' . esc_attr($network_value) . '">';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render Checkbox Group Field
     */
    public static function render_checkbox_group($field_id, $field_name, $field_value, $field) {
        $field_value = is_array($field_value) ? $field_value : array();
        
        echo '<div class="checkbox-options">';
        
        foreach ($field['options'] as $option_value => $option_label) {
            $checked = in_array($option_value, $field_value) ? 'checked' : '';
            
            echo '<div class="checkbox-option ' . $checked . '">';
            echo '<input type="checkbox" id="' . esc_attr($field_id . '_' . $option_value) . '" ';
            echo 'name="' . esc_attr($field_name . '[]') . '" value="' . esc_attr($option_value) . '" ';
            echo checked(in_array($option_value, $field_value), true, false) . '>';
            echo '<label for="' . esc_attr($field_id . '_' . $option_value) . '">' . esc_html($option_label) . '</label>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Get Google Fonts List
     */
    private static function get_google_fonts() {
        return array(
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Source Sans Pro' => 'Source Sans Pro',
            'Roboto Condensed' => 'Roboto Condensed',
            'Oswald' => 'Oswald',
            'Roboto Slab' => 'Roboto Slab',
            'Raleway' => 'Raleway',
            'Poppins' => 'Poppins',
            'Merriweather' => 'Merriweather',
            'PT Sans' => 'PT Sans',
            'Ubuntu' => 'Ubuntu',
            'Playfair Display' => 'Playfair Display',
            'Nunito' => 'Nunito',
            'Lora' => 'Lora',
            'Work Sans' => 'Work Sans',
            'Fira Sans' => 'Fira Sans',
            'Roboto Mono' => 'Roboto Mono',
            'Inter' => 'Inter'
        );
    }
    
    /**
     * Get Social Icon
     */
    private static function get_social_icon($network) {
        $icons = array(
            'facebook' => '<i class="fab fa-facebook-f"></i>',
            'twitter' => '<i class="fab fa-twitter"></i>',
            'instagram' => '<i class="fab fa-instagram"></i>',
            'linkedin' => '<i class="fab fa-linkedin-in"></i>',
            'youtube' => '<i class="fab fa-youtube"></i>',
            'whatsapp' => '<i class="fab fa-whatsapp"></i>'
        );
        
        return isset($icons[$network]) ? $icons[$network] : '<i class="fas fa-link"></i>';
    }
}

// Register custom field type actions
add_action('kilismile_render_custom_field_type', function($type, $field_id, $field_name, $field_value, $field) {
    switch ($type) {
        case 'radio_image':
            KiliSmile_Settings_Field_Renderers::render_radio_image($field_id, $field_name, $field_value, $field);
            break;
        case 'color_palette':
            KiliSmile_Settings_Field_Renderers::render_color_palette($field_id, $field_name, $field_value, $field);
            break;
        case 'typography':
            KiliSmile_Settings_Field_Renderers::render_typography($field_id, $field_name, $field_value, $field);
            break;
        case 'slider':
            KiliSmile_Settings_Field_Renderers::render_slider($field_id, $field_name, $field_value, $field);
            break;
        case 'code_editor':
            KiliSmile_Settings_Field_Renderers::render_code_editor($field_id, $field_name, $field_value, $field);
            break;
        case 'group':
            KiliSmile_Settings_Field_Renderers::render_group($field_id, $field_name, $field_value, $field);
            break;
        case 'repeater':
            KiliSmile_Settings_Field_Renderers::render_repeater($field_id, $field_name, $field_value, $field);
            break;
        case 'social_links':
            KiliSmile_Settings_Field_Renderers::render_social_links($field_id, $field_name, $field_value, $field);
            break;
        case 'checkbox_group':
            KiliSmile_Settings_Field_Renderers::render_checkbox_group($field_id, $field_name, $field_value, $field);
            break;
    }
}, 10, 5);


