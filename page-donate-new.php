<?php
/**
 * Template Name: Donation Page - Enhanced
 * 
 * @package KiliSmile
 */

get_header();

$layout_path = get_template_directory() . '/template-parts/donation-page-clean.php';
if (file_exists($layout_path)) {
    include $layout_path;
}

get_footer();

