<?php
/**
 * Template Functions
 *
 * @package KiliSmile
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Organization Information
 */
function kilismile_get_organization_info($field = '') {
    $info = array(
        'name' => 'Kilismile ORGANIZATION',
        'tagline' => get_theme_mod('kilismile_tagline', 'No health without oral health'),
        'registration' => get_theme_mod('kilismile_registration', '07NGO/R/6067'),
        'founded' => 'April 25, 2024',
        'registration_date' => 'April 25, 2024',
        'phone' => get_theme_mod('kilismile_phone', '+255763495575/+255735495575'),
        'email' => get_theme_mod('kilismile_email', 'kilismile21@gmail.com'),
        'address' => get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania'),
        'instagram' => get_theme_mod('kilismile_instagram', 'https://instagram.com/kili_smile'),
        'facebook' => get_theme_mod('kilismile_facebook', ''),
        'twitter' => get_theme_mod('kilismile_twitter', ''),
    );
    
    if ($field && isset($info[$field])) {
        return $info[$field];
    }
    
    return $info;
}

/**
 * Get Mission Areas
 */
function kilismile_get_mission_areas() {
    return array(
        array(
            'title' => __('Health Education', 'kilismile'),
            'description' => __('Promoting oral and general health education services to children and elderly living in remote areas.', 'kilismile'),
            'icon' => 'fas fa-tooth',
        ),
        array(
            'title' => __('Teacher Training', 'kilismile'),
            'description' => __('Training primary school teachers on basic oral and general health knowledge and practices.', 'kilismile'),
            'icon' => 'fas fa-chalkboard-teacher',
        ),
        array(
            'title' => __('Health Screening', 'kilismile'),
            'description' => __('Conducting screening of non-communicable diseases for children and elderly populations.', 'kilismile'),
            'icon' => 'fas fa-stethoscope',
        ),
    );
}

/**
 * Get Recent Programs
 */
function kilismile_get_recent_programs($limit = 3) {
    $programs = new WP_Query(array(
        'post_type' => 'programs',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_program_status',
                'value' => 'active',
                'compare' => '='
            )
        )
    ));
    
    return $programs;
}

/**
 * Get Team Members
 */
function kilismile_get_team_members($limit = -1) {
    $team = new WP_Query(array(
        'post_type' => 'team',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ));
    
    return $team;
}

/**
 * Get Upcoming Events
 */
function kilismile_get_upcoming_events($limit = 3) {
    $events = new WP_Query(array(
        'post_type' => 'events',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_event_date',
                'value' => date('Y-m-d'),
                'compare' => '>='
            )
        ),
        'meta_key' => '_event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC'
    ));
    
    return $events;
}

/**
 * Get Testimonials
 */
function kilismile_get_testimonials($limit = 3) {
    $testimonials = new WP_Query(array(
        'post_type' => 'testimonials',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'rand'
    ));
    
    return $testimonials;
}

/**
 * Format Event Date
 */
function kilismile_format_event_date($date, $time = '') {
    if (!$date) return '';
    
    $formatted_date = date_i18n(get_option('date_format'), strtotime($date));
    
    if ($time) {
        $formatted_time = date_i18n(get_option('time_format'), strtotime($time));
        return sprintf('%s at %s', $formatted_date, $formatted_time);
    }
    
    return $formatted_date;
}

/**
 * Get Program Status Badge
 */
function kilismile_get_program_status_badge($status) {
    $badges = array(
        'active' => array(
            'class' => 'status-active',
            'text' => __('Active', 'kilismile'),
            'color' => '#4CAF50'
        ),
        'completed' => array(
            'class' => 'status-completed',
            'text' => __('Completed', 'kilismile'),
            'color' => '#2196F3'
        ),
        'planned' => array(
            'class' => 'status-planned',
            'text' => __('Planned', 'kilismile'),
            'color' => '#FF9800'
        )
    );
    
    if (isset($badges[$status])) {
        return sprintf(
            '<span class="program-status %s" style="background-color: %s; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; text-transform: uppercase;">%s</span>',
            $badges[$status]['class'],
            $badges[$status]['color'],
            $badges[$status]['text']
        );
    }
    
    return '';
}

/**
 * Get Target Audience Badge
 */
function kilismile_get_target_audience_badge($audience) {
    $audiences = array(
        'children' => array(
            'text' => __('Children', 'kilismile'),
            'icon' => 'fas fa-child',
            'color' => '#E91E63'
        ),
        'elderly' => array(
            'text' => __('Elderly', 'kilismile'),
            'icon' => 'fas fa-user-friends',
            'color' => '#9C27B0'
        ),
        'teachers' => array(
            'text' => __('Teachers', 'kilismile'),
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => '#3F51B5'
        ),
        'community' => array(
            'text' => __('Community', 'kilismile'),
            'icon' => 'fas fa-users',
            'color' => '#607D8B'
        )
    );
    
    if (isset($audiences[$audience])) {
        return sprintf(
            '<span class="audience-badge" style="background-color: %s; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; text-transform: uppercase;"><i class="%s" aria-hidden="true"></i> %s</span>',
            $audiences[$audience]['color'],
            $audiences[$audience]['icon'],
            $audiences[$audience]['text']
        );
    }
    
    return '';
}

/**
 * Get Impact Statistics
 */
function kilismile_get_impact_stats() {
    // These could be stored in options or calculated from database
    return array(
        array(
            'number' => get_theme_mod('kilismile_stat_children', '500'),
            'label' => __('Children Reached', 'kilismile'),
            'icon' => 'fas fa-child'
        ),
        array(
            'number' => get_theme_mod('kilismile_stat_elderly', '200'),
            'label' => __('Elderly Served', 'kilismile'),
            'icon' => 'fas fa-user-friends'
        ),
        array(
            'number' => get_theme_mod('kilismile_stat_teachers', '50'),
            'label' => __('Teachers Trained', 'kilismile'),
            'icon' => 'fas fa-chalkboard-teacher'
        ),
        array(
            'number' => get_theme_mod('kilismile_stat_areas', '10'),
            'label' => __('Remote Areas', 'kilismile'),
            'icon' => 'fas fa-map-marker-alt'
        )
    );
}

/**
 * Generate Breadcrumbs
 */
function kilismile_breadcrumbs() {
    if (is_home() || is_front_page()) return;
    
    $breadcrumbs = array();
    $breadcrumbs[] = sprintf('<a href="%s">%s</a>', esc_url(home_url('/')), __('Home', 'kilismile'));
    
    if (is_category() || is_single()) {
        if (is_single()) {
            $category = get_the_category();
            if (!empty($category)) {
                $breadcrumbs[] = sprintf('<a href="%s">%s</a>', esc_url(get_category_link($category[0]->term_id)), $category[0]->name);
            }
            $breadcrumbs[] = get_the_title();
        } else {
            $breadcrumbs[] = single_cat_title('', false);
        }
    } elseif (is_page()) {
        if ($post = get_post()) {
            if ($post->post_parent) {
                $parent = get_post($post->post_parent);
                $breadcrumbs[] = sprintf('<a href="%s">%s</a>', esc_url(get_permalink($parent)), $parent->post_title);
            }
            $breadcrumbs[] = get_the_title();
        }
    } elseif (is_tag()) {
        $breadcrumbs[] = single_tag_title('', false);
    } elseif (is_author()) {
        $breadcrumbs[] = get_the_author();
    } elseif (is_404()) {
        $breadcrumbs[] = __('404 Not Found', 'kilismile');
    }
    
    if (!empty($breadcrumbs)) {
        echo '<nav class="breadcrumbs" aria-label="' . __('Breadcrumb', 'kilismile') . '">';
        echo '<ol class="breadcrumb-list">';
        foreach ($breadcrumbs as $key => $crumb) {
            if ($key === count($breadcrumbs) - 1) {
                echo '<li class="breadcrumb-item active" aria-current="page">' . $crumb . '</li>';
            } else {
                echo '<li class="breadcrumb-item">' . $crumb . '</li>';
            }
        }
        echo '</ol>';
        echo '</nav>';
    }
}

/**
 * Format Phone Number for Links
 */
function kilismile_format_phone_link($phone) {
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Get Social Media Links
 */
function kilismile_get_social_links() {
    $links = array();
    
    $instagram = get_theme_mod('kilismile_instagram');
    if ($instagram) {
        $links['instagram'] = array(
            'url' => $instagram,
            'icon' => 'fab fa-instagram',
            'label' => __('Follow us on Instagram', 'kilismile')
        );
    }
    
    $facebook = get_theme_mod('kilismile_facebook');
    if ($facebook) {
        $links['facebook'] = array(
            'url' => $facebook,
            'icon' => 'fab fa-facebook-f',
            'label' => __('Follow us on Facebook', 'kilismile')
        );
    }
    
    $twitter = get_theme_mod('kilismile_twitter');
    if ($twitter) {
        $links['twitter'] = array(
            'url' => $twitter,
            'icon' => 'fab fa-twitter',
            'label' => __('Follow us on Twitter', 'kilismile')
        );
    }
    
    return $links;
}

/**
 * Generate Schema.org Structured Data
 */
function kilismile_get_schema_data() {
    $org_info = kilismile_get_organization_info();
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $org_info['name'],
        'description' => $org_info['tagline'],
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/assets/images/logo.png',
        'address' => array(
            '@type' => 'PostalAddress',
            'addressLocality' => 'Moshi',
            'addressRegion' => 'Kilimanjaro',
            'addressCountry' => 'Tanzania',
            'streetAddress' => $org_info['address']
        ),
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'telephone' => $org_info['phone'],
            'email' => $org_info['email'],
            'contactType' => 'Customer Service'
        ),
        'sameAs' => array_filter(array(
            $org_info['instagram'],
            $org_info['facebook'],
            $org_info['twitter']
        ))
    );
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Add Schema.org JSON-LD to Head
 */
function kilismile_add_schema_data() {
    if (is_front_page()) {
        echo '<script type="application/ld+json">' . kilismile_get_schema_data() . '</script>';
    }
}
add_action('wp_head', 'kilismile_add_schema_data');

/**
 * Custom Post Class
 */
function kilismile_post_classes($classes, $class, $post_id) {
    if (is_admin()) return $classes;
    
    $post_type = get_post_type($post_id);
    
    // Add post type class
    $classes[] = 'post-type-' . $post_type;
    
    // Add status class for programs
    if ($post_type === 'programs') {
        $status = get_post_meta($post_id, '_program_status', true);
        if ($status) {
            $classes[] = 'program-' . $status;
        }
    }
    
    // Add audience class for programs
    if ($post_type === 'programs') {
        $audience = get_post_meta($post_id, '_program_target_audience', true);
        if ($audience) {
            $classes[] = 'audience-' . $audience;
        }
    }
    
    return $classes;
}
add_filter('post_class', 'kilismile_post_classes', 10, 3);

/**
 * Custom Body Class
 */
if (!function_exists('kilismile_body_classes')) {
    function kilismile_body_classes($classes) {
        // Add page slug class
        if (is_page()) {
            global $post;
            $classes[] = 'page-' . $post->post_name;
        }
        
        // Add template class
        if (is_page_template()) {
            $template = get_page_template_slug();
            $template_name = str_replace(array('page-', '.php'), '', basename($template));
            $classes[] = 'template-' . $template_name;
        }
        
        return $classes;
    }
}
add_filter('body_class', 'kilismile_body_classes');

?>


