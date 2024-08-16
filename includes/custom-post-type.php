<?php
if (!defined('ABSPATH')) exit;

function create_cities_post_type() {
    $labels = array(
        'name' => 'Cities',
        'singular_name' => 'City',
        // Add other labels as needed
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-location-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('cities', $args);
}
add_action('init', 'create_cities_post_type');
