<?php
/**
 * Donations Page (Slug: donations)
 *
 * WordPress will automatically use this file for the /donations page.
 * We keep this as a thin wrapper so the actual layout can live in a shared
 * template-part.
 *
 * Template Name: Donations Page
 *
 * @package KiliSmile
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$clean_path = get_template_directory() . '/template-parts/donation-page-clean.php';
if ( file_exists( $clean_path ) ) {
    include $clean_path;
} else {
    $component_path = get_template_directory() . '/template-parts/donation-form-component.php';
    if ( file_exists( $component_path ) ) {
        include $component_path;
    } else {
        echo '<main id="main" class="site-main"><div class="container"><p>'
            . esc_html__( 'Donation template not found. Please contact the site administrator.', 'kilismile' )
            . '</p></div></main>';
    }
}

get_footer();
