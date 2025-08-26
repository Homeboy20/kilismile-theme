<?php
/**
 * Test file to check for function conflicts
 */

// Define constants that WordPress would define
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../');
}

// Mock WordPress functions
if (!function_exists('get_option')) {
    function get_option($option) { return 'test'; }
}
if (!function_exists('get_bloginfo')) {
    function get_bloginfo($info) { return 'Test Site'; }
}
if (!function_exists('home_url')) {
    function home_url($path = '') { return 'http://localhost:8000' . $path; }
}
if (!function_exists('current_time')) {
    function current_time($format) { return date($format); }
}
if (!function_exists('__')) {
    function __($text, $domain = '') { return $text; }
}
if (!function_exists('get_theme_mod')) {
    function get_theme_mod($mod, $default = '') { return $default; }
}
if (!function_exists('get_template_directory')) {
    function get_template_directory() { return dirname(__FILE__); }
}

echo "Loading email-system.php...\n";
include 'inc/email-system.php';

echo "Loading contact-email-templates.php...\n";
include 'inc/contact-email-templates.php';

echo "Loading contact-functions.php...\n";
include 'inc/contact-functions.php';

echo "\nAll files loaded successfully! No function conflicts detected.\n";
