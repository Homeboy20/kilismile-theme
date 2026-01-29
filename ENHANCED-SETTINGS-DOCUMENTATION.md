# Enhanced KiliSmile Theme Settings Documentation

## Overview

The Enhanced KiliSmile Theme Settings system provides a comprehensive, modern interface for customizing your charity/nonprofit website. This system replaces the basic WordPress Customizer approach with a powerful, modular settings framework.

## Features

### 🎨 **Advanced Design Options**
- **Color Schemes**: Pre-designed color palettes + custom color picker
- **Typography**: Google Fonts integration with live preview
- **Layout Options**: Multiple header layouts with visual selection
- **Logo Settings**: Size, border radius, and retina support

### 🏗️ **Modular Architecture**
- **8 Settings Sections**: Organized by functionality
- **Custom Field Types**: 15+ field types including sliders, code editors, repeaters
- **Conditional Fields**: Show/hide fields based on other settings
- **Real-time Preview**: See changes instantly

### 💾 **Data Management**
- **Auto-save**: Settings saved automatically every 3 seconds
- **Import/Export**: Backup and restore all settings
- **Reset Options**: Reset individual sections or all settings
- **Version Control**: Track setting changes with timestamps

### ⚡ **Performance Optimized**
- **Lazy Loading**: Images and scripts loaded on demand
- **CSS Variables**: Dynamic theming with CSS custom properties
- **Minification**: Optional CSS/JS compression
- **Caching**: Browser and server-side caching support

## Settings Sections

### 1. General Settings
**Icon**: `dashicons-admin-generic`
**Purpose**: Basic theme configuration and global settings

**Fields**:
- **Site Mode**: Charity, NGO, Foundation, Nonprofit, Community
- **Organization Name**: Official organization name
- **Organization Tagline**: Mission statement or description
- **Contact Information**: Phone, email, physical address

### 2. Appearance & Layout
**Icon**: `dashicons-admin-appearance`
**Purpose**: Visual design, colors, typography, and layout

**Fields**:
- **Color Scheme**: 5 predefined schemes + custom option
- **Custom Color Palette**: 5-color system (primary, secondary, accent, text, background)
- **Typography Settings**: Google Fonts for headings and body text

### 3. Header & Navigation
**Icon**: `dashicons-menu`
**Purpose**: Header layout, logo, navigation menu settings

**Fields**:
- **Header Layout**: Standard, Centered, Minimal layouts
- **Logo Settings**: Size (20-200px), border radius (0-50%), retina support
- **Navigation Options**: Menu position and styling

### 4. Content & Pages
**Icon**: `dashicons-admin-page`
**Purpose**: Page layouts, content display, archive settings

**Fields**:
- **Page Layouts**: Default layouts for different page types
- **Archive Settings**: Blog and category page configurations
- **Content Display**: Excerpt length, read more settings

### 5. Donation System
**Icon**: `dashicons-heart`
**Purpose**: Donation forms, goals, campaigns, payment methods

**Fields**:
- **Enable Donation System**: Master toggle
- **Donation Goals**: Repeater field for multiple fundraising goals
- **Payment Methods**: Gateway configuration
- **Campaign Management**: Create and manage fundraising campaigns

### 6. Social & Contact
**Icon**: `dashicons-share`
**Purpose**: Social media integration, contact forms, communication

**Fields**:
- **Social Media Links**: Facebook, Twitter, Instagram, LinkedIn, YouTube, WhatsApp
- **Contact Form Settings**: Email handling and notifications
- **Communication Preferences**: Newsletter and email settings

### 7. Performance & SEO
**Icon**: `dashicons-performance`
**Purpose**: Speed optimization, SEO settings, analytics

**Fields**:
- **Performance Optimizations**: 
  - Lazy loading for images
  - CSS/JS minification
  - Asset caching
  - Image compression
- **SEO Settings**: Meta tags, Open Graph, structured data
- **Analytics Integration**: Google Analytics, Facebook Pixel

### 8. Advanced Options
**Icon**: `dashicons-admin-tools`
**Purpose**: Developer options, custom code, advanced features

**Fields**:
- **Custom CSS**: Code editor with syntax highlighting
- **Custom JavaScript**: Code editor for footer scripts
- **Developer Mode**: Advanced debugging and logging
- **API Settings**: External service integrations

## Custom Field Types

### Basic Fields
- **Text**: Single-line text input
- **Textarea**: Multi-line text input
- **Email**: Email validation
- **URL**: URL validation
- **Number**: Numeric input with min/max
- **Date**: Date picker
- **Select**: Dropdown selection
- **Toggle**: On/off switch

### Advanced Fields
- **Radio Image**: Visual selection with images
- **Color Palette**: Multiple color picker group
- **Slider**: Range slider with live value display
- **Typography**: Font family selector with preview
- **Code Editor**: Syntax-highlighted code input
- **Group**: Nested field collection
- **Repeater**: Dynamic field repetition
- **Social Links**: Social media URL manager
- **Checkbox Group**: Multiple option selection

## Usage Examples

### Getting Settings in PHP

```php
// Get a specific setting
$site_mode = kilismile_get_setting('general', 'site_mode', 'charity');

// Get all settings for a section
$appearance_settings = kilismile_get_section_settings('appearance');

// Get organization info
$org_info = kilismile_get_organization_info();

// Get color scheme
$colors = kilismile_get_color_scheme();

// Check if donations are enabled
$donations_enabled = kilismile_is_donation_enabled();
```

### Using Settings in Templates

```php
// Get header layout
$header_settings = kilismile_get_header_settings();
$layout = $header_settings['header_layout'];

// Apply color scheme
$colors = kilismile_get_color_scheme();
echo '<div style="background-color: ' . $colors['primary'] . '">';

// Show social links
$social_links = kilismile_get_social_links();
foreach ($social_links as $network => $url) {
    if (!empty($url)) {
        echo '<a href="' . esc_url($url) . '">' . $network . '</a>';
    }
}
```

### CSS Variables

The system automatically generates CSS custom properties:

```css
:root {
    --kilismile-color-primary: #2271b1;
    --kilismile-color-secondary: #00a32a;
    --kilismile-color-accent: #ff6b35;
    --kilismile-color-text: #333333;
    --kilismile-color-background: #ffffff;
    --kilismile-font-headings: 'Roboto', sans-serif;
    --kilismile-font-body: 'Open Sans', sans-serif;
}
```

Use in your stylesheets:
```css
.button {
    background-color: var(--kilismile-color-primary);
    font-family: var(--kilismile-font-body);
}

h1, h2, h3 {
    font-family: var(--kilismile-font-headings);
    color: var(--kilismile-color-text);
}
```

## JavaScript API

### Client-side Access

```javascript
// Access settings object
const settings = window.KiliSmileSettings;

// Listen for setting changes
$(document).on('kilismile:section_changed', function(e, sectionId) {
    console.log('Changed to section:', sectionId);
});

// Show notification
settings.showNotification('Settings saved!', 'success');

// Get form data
const formData = settings.getFormData();
```

### Auto-save System

Settings are automatically saved every 3 seconds when changes are detected:

```javascript
// Trigger auto-save manually
KiliSmileSettings.triggerAutoSave();

// Disable auto-save
KiliSmileSettings.autoSaveDelay = 0;
```

## Import/Export

### Export Settings
1. Click "Export" button in header
2. JSON file downloads automatically
3. File includes version info and timestamp

### Import Settings
1. Click "Import" button in header
2. Select previously exported JSON file
3. Confirm overwrite warning
4. Settings are validated and imported

### Export Format
```json
{
    "version": "3.0.0",
    "timestamp": "2024-01-15 10:30:00",
    "settings": {
        "general": {
            "site_mode": "charity",
            "organization_name": "KiliSmile Foundation"
        },
        "appearance": {
            "color_scheme": "custom",
            "custom_colors": {
                "primary": "#2271b1",
                "secondary": "#00a32a"
            }
        }
    }
}
```

## Customization

### Adding Custom Field Types

```php
// Register custom field renderer
add_action('kilismile_render_custom_field_type', function($type, $field_id, $field_name, $field_value, $field) {
    if ($type === 'my_custom_field') {
        echo '<div class="my-custom-field">';
        echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '">';
        echo '</div>';
    }
}, 10, 5);
```

### Adding Custom Sanitization

```php
// Custom field sanitization
add_filter('kilismile_sanitize_field_my_custom_field', function($value, $field) {
    return sanitize_text_field(strtoupper($value));
}, 10, 2);
```

### Extending Settings Sections

```php
// Add custom section
add_filter('kilismile_settings_sections', function($sections) {
    $sections['custom'] = array(
        'title' => 'Custom Settings',
        'icon' => 'dashicons-admin-generic',
        'description' => 'My custom settings section',
        'priority' => 90
    );
    return $sections;
});

// Add custom fields
add_filter('kilismile_settings_fields', function($fields) {
    $fields['custom']['my_setting'] = array(
        'type' => 'text',
        'title' => 'My Setting',
        'description' => 'This is my custom setting',
        'default' => 'default value'
    );
    return $fields;
});
```

## Performance Considerations

### Optimization Features
- **Lazy Loading**: Automatic image lazy loading
- **CSS Minification**: Compress stylesheets
- **JS Minification**: Compress JavaScript files
- **Asset Caching**: Browser cache optimization
- **Font Preloading**: Preload Google Fonts
- **Schema Markup**: Automatic structured data

### Best Practices
1. **Use CSS Variables**: Leverage the generated custom properties
2. **Minimize HTTP Requests**: Combine when possible
3. **Optimize Images**: Enable compression in performance settings
4. **Cache Settings**: Use WordPress object caching for frequently accessed settings
5. **Conditional Loading**: Only load what's needed per page

## Browser Support

### Minimum Requirements
- **Chrome**: 70+
- **Firefox**: 65+
- **Safari**: 12+
- **Edge**: 79+
- **Mobile**: iOS 12+, Android 7+

### Progressive Enhancement
- Graceful degradation for older browsers
- Fallbacks for unsupported CSS features
- JavaScript polyfills where needed

## Security

### Data Validation
- All inputs sanitized before storage
- Nonce verification for AJAX requests
- Capability checks for admin access
- SQL injection protection
- XSS prevention

### Best Practices
- Regular security updates
- Secure file uploads
- Validated user permissions
- Escaped output everywhere
- HTTPS enforcement

## Troubleshooting

### Common Issues

**Settings Not Saving**
- Check user permissions (`manage_options`)
- Verify nonce in AJAX requests
- Check PHP error logs
- Ensure database write permissions

**JavaScript Errors**
- Check browser console for errors
- Verify jQuery is loaded
- Check for plugin conflicts
- Ensure proper script dependencies

**Performance Issues**
- Enable caching plugins
- Optimize database queries
- Use performance profiling tools
- Monitor server resources

### Debug Mode

Enable WordPress debug mode to troubleshoot:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Support

For technical support and feature requests:
- **Documentation**: This file
- **Theme Support**: WordPress theme forums
- **Bug Reports**: GitHub issues
- **Feature Requests**: Development roadmap

---

**Version**: 3.0.0  
**Last Updated**: January 2024  
**Compatibility**: WordPress 6.0+  
**License**: GPL v2 or later


