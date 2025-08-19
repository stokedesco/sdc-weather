<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register plugin settings, section, and fields.
 */
function sdc_weather_register_settings() {
    register_setting(
        'sdc_weather',
        'sdc_weather_api_key',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_location',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_temp_threshold',
        array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 0,
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_temp_unit',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'celsius',
        )
    );

    add_settings_section(
        'sdc_weather_section',
        __( 'Stoke Weather Widget Settings', 'sdc-weather' ),
        '__return_false',
        'sdc_weather'
    );

    add_settings_field(
        'sdc_weather_api_key',
        __( 'API Key', 'sdc-weather' ),
        'sdc_weather_api_key_field',
        'sdc_weather',
        'sdc_weather_section'
    );

    add_settings_field(
        'sdc_weather_location',
        __( 'Location', 'sdc-weather' ),
        'sdc_weather_location_field',
        'sdc_weather',
        'sdc_weather_section'
    );

    add_settings_field(
        'sdc_weather_temp_threshold',
        __( 'Temperature Threshold', 'sdc-weather' ),
        'sdc_weather_temp_threshold_field',
        'sdc_weather',
        'sdc_weather_section'
    );

    add_settings_field(
        'sdc_weather_temp_unit',
        __( 'Temperature Unit', 'sdc-weather' ),
        'sdc_weather_temp_unit_field',
        'sdc_weather',
        'sdc_weather_section'
    );
}
add_action( 'admin_init', 'sdc_weather_register_settings' );

/**
 * Output the location input field.
 */
function sdc_weather_location_field() {
    $value = get_option( 'sdc_weather_location', '' );
    echo '<input type="text" id="sdc-weather-location" name="sdc_weather_location" value="' . esc_attr( $value ) . '" class="regular-text" /> ';
    echo '<button type="button" class="button" id="sdc-weather-find-location">' . esc_html__( 'Find location', 'sdc-weather' ) . '</button>';
    echo '<div id="sdc-weather-location-results"></div>';
    echo '<p class="description">' . esc_html__( 'Enter your AccuWeather Location ID, find your AccuWeather ID by', 'sdc-weather' ) . ' <a href="https://www.accuweather.com/en/search-locations" target="_blank">' . esc_html__( 'clicking here', 'sdc-weather' ) . '</a>.</p>';
}

/**
 * Output the temperature threshold input field.
 */
function sdc_weather_temp_threshold_field() {
    $value = get_option( 'sdc_weather_temp_threshold', 0 );
    echo '<input type="number" name="sdc_weather_temp_threshold" value="' . esc_attr( $value ) . '" class="small-text" />';
    echo '<p class="description">' . esc_html__( 'Enter the Temperature in which the warning sign displays instead.', 'sdc-weather' ) . '</p>';
}

/**
 * Output the API key input field.
 */
function sdc_weather_api_key_field() {
    $value = get_option( 'sdc_weather_api_key', '' );
    echo '<input type="text" name="sdc_weather_api_key" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p class="description">' . esc_html__( 'Enter your AccWeather API Key. If you do not have one, please register at', 'sdc-weather' ) . ' <a href="https://developer.accuweather.com/" target="_blank">https://developer.accuweather.com/</a></p>';
}

/**
 * Output the temperature unit select field.
 */
function sdc_weather_temp_unit_field() {
    $value = get_option( 'sdc_weather_temp_unit', 'celsius' );
    echo '<select name="sdc_weather_temp_unit">';
    echo '<option value="celsius"' . ( 'celsius' === $value ? ' selected' : '' ) . '>' . esc_html__( 'Celsius', 'sdc-weather' ) . '</option>';
    echo '<option value="fahrenheit"' . ( 'fahrenheit' === $value ? ' selected' : '' ) . '>' . esc_html__( 'Fahrenheit', 'sdc-weather' ) . '</option>';
    echo '</select>';
}

/**
 * Add the Stoke Weather Widget settings page under Settings.
 */
function sdc_weather_add_admin_menu() {
    add_options_page(
        __( 'Stoke Weather Widget', 'sdc-weather' ),
        __( 'Stoke Weather Widget', 'sdc-weather' ),
        'manage_options',
        'sdc_weather',
        'sdc_weather_settings_page'
    );
}
add_action( 'admin_menu', 'sdc_weather_add_admin_menu' );

/**
 * Render the settings page.
 */
function sdc_weather_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Stoke Weather Widget', 'sdc-weather' ); ?></h1>
        <?php
        if ( isset( $_GET['settings-updated'] ) ) {
            $location   = get_option( 'sdc_weather_location', '' );
            $api_key    = get_option( 'sdc_weather_api_key', '' );
            $connection = sdc_weather_test_connection( $location, $api_key );

            echo '<div id="sdc-weather-connection-status">';
            if ( is_wp_error( $connection ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Unable to connect to weather service. Please check your API key and location.', 'sdc-weather' ) . '</p></div>';
            } else {
                echo '<div class="notice notice-success"><p>' . esc_html__( 'Connected to weather service.', 'sdc-weather' ) . '</p></div>';
                $threshold = get_option( 'sdc_weather_temp_threshold', 0 );
                $unit      = get_option( 'sdc_weather_temp_unit', 'celsius' );
                $icon      = ( $connection['temperature'] > $threshold ) ? '<i class="weather-icon fa-solid fa-triangle-exclamation"></i>' : cww_get_icon_markup( $connection['icon'] );
                $suffix    = ( 'fahrenheit' === $unit ) ? '&deg;F' : '&deg;C';
                echo '<div class="weather-widget">' . $icon . '<span class="weather-text">' . esc_html( $connection['condition'] ) . ' ' . esc_html( $connection['temperature'] ) . $suffix . '</span></div>';
            }
            echo '</div>';
        }
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'sdc_weather' );
            do_settings_sections( 'sdc_weather' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Enqueue admin assets for the settings page.
 *
 * @param string $hook Current admin page.
 */
function sdc_weather_admin_assets( $hook ) {
    if ( 'settings_page_sdc_weather' !== $hook ) {
        return;
    }
    wp_enqueue_script( 'sdc-weather-location-search', plugins_url( '../assets/js/location-search.js', __FILE__ ), array(), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'sdc_weather_admin_assets' );
