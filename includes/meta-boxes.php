<?php
if (!defined('ABSPATH')) exit;

function add_city_meta_boxes() {
    add_meta_box(
        'city_location_info',
        'City Location Information',
        'city_location_callback',
        'cities',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

function city_location_callback($post) {
    wp_nonce_field(basename(__FILE__), 'city_location_nonce');
    $latitude = get_post_meta($post->ID, '_city_latitude', true);
    $longitude = get_post_meta($post->ID, '_city_longitude', true);
    ?>
    <p>
        <label for="city_latitude">Latitude:</label>
        <input type="text" name="city_latitude" id="city_latitude" value="<?php echo esc_attr($latitude); ?>">
    </p>
    <p>
        <label for="city_longitude">Longitude:</label>
        <input type="text" name="city_longitude" id="city_longitude" value="<?php echo esc_attr($longitude); ?>">
    </p>
    <?php
}

function save_city_meta($post_id) {
    if (!isset($_POST['city_location_nonce']) || !wp_verify_nonce($_POST['city_location_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('cities' != $_POST['post_type']) {
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $latitude = isset($_POST['city_latitude']) ? sanitize_text_field($_POST['city_latitude']) : '';
    $longitude = isset($_POST['city_longitude']) ? sanitize_text_field($_POST['city_longitude']) : '';

    update_post_meta($post_id, '_city_latitude', $latitude);
    update_post_meta($post_id, '_city_longitude', $longitude);
}
add_action('save_post', 'save_city_meta');