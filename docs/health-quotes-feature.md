# Health Quotes Feature Documentation

## Overview

This document explains the implementation of the Health Quotes feature that replaced the previous Testimonials section on the Kili Smile website. The Health Quotes section displays inspirational health-related quotes with a focus on oral health education and community wellness.

## Key Features

1. **Customizable Content**: All quote text, author names, and sources can be customized through the WordPress Customizer.
2. **Responsive Layout**: The quotes display in a responsive grid layout that adapts to different screen sizes.
3. **Visual Design**: Each quote has a distinctive visual style with health-related icons and a color-coded border.
4. **Show/Hide Option**: The entire section can be toggled on/off through the Customizer.
5. **Live Preview**: Changes made in the Customizer are immediately visible in the preview.

## Implementation Details

### Files Modified

1. `index.php`: Added the Health Quotes section markup with dynamic content from Customizer settings
2. `inc/customizer.php`: Added Customizer controls for the Health Quotes section
3. `assets/js/customizer-preview.js`: Added JavaScript for live preview functionality

### Customizer Settings

The Health Quotes section can be customized through WordPress Customizer under the "Health Quotes Section" panel:

- **Section Title**: The main heading for the section
- **Section Subtitle**: The descriptive text below the heading
- **Show/Hide Section**: Toggle to show or hide the entire section
- **Quote Content (1-3)**: For each of the three main quotes:
  - Quote Text: The quote content
  - Author: Who said the quote
  - Source: Additional context about the author
- **Featured Quote**: A larger, highlighted quote at the bottom of the section

### Usage Instructions

1. To edit the Health Quotes section:
   - Go to WordPress Admin → Appearance → Customize
   - Select "Health Quotes Section" from the menu
   - Make desired changes to content
   - Changes will show in real-time in the preview pane
   - Click "Publish" to save changes

2. To hide the section:
   - Uncheck the "Show Health Quotes Section" option in the Customizer

3. To customize the health quotes:
   - Edit each quote's text, author, and source
   - Changes apply immediately in the preview

## Design Elements

- **Icons**: Font Awesome icons represent different health concepts:
  - Tooth icon: Represents oral health
  - Heartbeat icon: Represents general health
  - Hand holding heart: Represents care and compassion
  - Lightbulb: Represents education and insight

- **Color Scheme**: 
  - Each quote card has a different shade of green for its accent color
  - Colors automatically inherit from the site's primary and accent color settings

## Maintenance Notes

If additional quotes are needed, the template can be expanded by:

1. Adding new Customizer settings in `inc/customizer.php`
2. Adding markup for the new quotes in `index.php`
3. Adding live preview bindings in `assets/js/customizer-preview.js`

## Technical Implementation

The Health Quotes section uses static quotes rather than a custom post type (unlike the previous Testimonials section). This approach provides easier management through the Customizer interface without requiring content editors to create separate posts.
