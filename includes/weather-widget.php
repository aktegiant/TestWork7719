<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Constructs a new instance of the Weather_Widget class.
 */
class Weather_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'weather_widget',
            'City Weather Widget',
            array('description' => 'Displays weather information for a selected city.')
        );
    }


    /**
     * Displays the weather widget on the front-end of the website.
     *
     * @param array $args     The widget arguments.
     * @param array $instance The widget instance settings.
     */
    public function widget($args, $instance)
    {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if (!empty($city_id)) {
            $city = get_post($city_id);
            $latitude = get_post_meta($city_id, '_city_latitude', true);
            $longitude = get_post_meta($city_id, '_city_longitude', true);

            // Fetch weather data from OpenWeatherMap API
            $api_key = '8e97a120fb97ceebea57e422f34721ea';
            $api_url = "http://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$api_key}";

            $response = wp_remote_get($api_url);

            if (!is_wp_error($response) && $response['response']['code'] == 200) {
                $weather_data = json_decode(wp_remote_retrieve_body($response), true);
                $temperature = $weather_data['main']['temp'];

                echo '<div class="weather-widget">';
                echo '<h2>' . $city->post_title . '</h2>';
                echo '<p class="temperature">Temperature: ' . $temperature . '°C</p>';
                echo '</div>';
            } else {
                echo "<p>Unable to fetch weather data.</p>";
            }
        } else {
            echo "<p>No city selected.</p>";
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('city_id')); ?>">Select City:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('city_id')); ?>"
                name="<?php echo esc_attr($this->get_field_name('city_id')); ?>">
                <option value="">Select a city</option>
                <?php
                $cities = get_posts(array('post_type' => 'cities', 'posts_per_page' => -1));
                foreach ($cities as $city) {
                    echo '<option value="' . esc_attr($city->ID) . '" ' . selected($city_id, $city->ID, false) . '>' . esc_html($city->post_title) . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? absint($new_instance['city_id']) : '';
        return $instance;
    }
}

function register_weather_widget()
{
    register_widget('Weather_Widget');
}
add_action('widgets_init', 'register_weather_widget');