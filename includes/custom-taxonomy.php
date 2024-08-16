<?php
if (!defined('ABSPATH')) exit;

function create_countries_taxonomy() {
    $labels = array(
        'name' => 'Countries',
        'singular_name' => 'Country',
        // Add other labels as needed
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_admin_column' => true,
    );

    register_taxonomy('countries', 'cities', $args);
}
add_action('init', 'create_countries_taxonomy');