<?php
if (!defined('ABSPATH')) exit;

// Enqueue parent theme styles
add_action('wp_enqueue_scripts', 'child_theme_enqueue_styles');
function child_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    wp_enqueue_style('weather-widget-style', get_stylesheet_directory_uri() . '/css/weather-widget.css');
}


// Include custom files
require_once get_stylesheet_directory() . '/includes/custom-post-type.php';
require_once get_stylesheet_directory() . '/includes/custom-taxonomy.php';
require_once get_stylesheet_directory() . '/includes/meta-boxes.php';
require_once get_stylesheet_directory() . '/includes/weather-widget.php';