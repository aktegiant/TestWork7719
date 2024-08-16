<?php
/*
Template Name: Weather Table
*/

get_header();


function enqueue_weather_table_script(){
    wp_enqueue_script('weather-table-script', get_template_directory_uri() . '/js/weather-table.js', array( 'jquery' ), '1.0.0', true);
    wp_localize_script('weather-table-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_weather_table_script');

// Enqueue scripts and styles for this template
function enqueue_weather_table_style() {
    wp_enqueue_style('weather-table-style', get_stylesheet_directory_uri() . '/css/weather-table.css');
}
add_action('wp_enqueue_styles', 'enqueue_weather_table_style');

?>

<div class="weather-table-container">
    <h1><?php the_title(); ?></h1>

    <input type="text" id="city-search" placeholder="Search cities...">

    <?php
    // Custom action hook before the table
    do_action('before_weather_table');

    // Display the weather table
    display_weather_table();

    // Custom action hook after the table
    do_action('after_weather_table');
    ?>
</div>

<?php
get_footer();

function display_weather_table() {
    global $wpdb;

    $search = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';

    $query = $wpdb->prepare("
    SELECT t.name AS country, p.post_title AS city, pm1.meta_value AS latitude, pm2.meta_value AS longitude
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
    LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
    LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_city_latitude'
    LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_city_longitude'
    WHERE p.post_type = 'cities' AND p.post_status = 'publish' AND tt.taxonomy = 'countries'
    AND p.post_title LIKE %s
    ORDER BY t.name ASC, p.post_title ASC
", '%' . $wpdb->esc_like($search) . '%');

    $results = $wpdb->get_results($query);

    if ($results) {
        echo '<table id="weather-table">';
        echo '<thead><tr><th>Country</th><th>City</th><th>Temperature</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            $temperature = get_city_temperature($row->latitude, $row->longitude);
            echo "<tr>";
            echo "<td>" . esc_html($row->country) . "</td>";
            echo "<td>" . esc_html($row->city) . "</td>";
            echo "<td>" . esc_html($temperature) . "°C</td>";
            echo "</tr>";
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No cities found.</p>';
    }
}

function get_city_temperature($latitude, $longitude) {
    $api_key = '4bac9eb0f521ebc2d2bd2073cd0206aa';
    $api_url = "http://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$api_key}";


    $response = wp_remote_get($api_url);

    if (!is_wp_error($response) && $response['response']['code'] == 200) {
        $weather_data = json_decode(wp_remote_retrieve_body($response), true);
        $temperature = $weather_data['main']['temp'];
        return round($temperature, 1);
    }

    return 'N/A';
}