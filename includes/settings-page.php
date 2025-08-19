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
        'sdc_weather_api_key',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    add_settings_section(
        'sdc_weather_section',
        __( 'SDC Weather Settings', 'sdc-weather' ),
        '__return_false',
        'sdc_weather'
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
        'sdc_weather_api_key',
        __( 'API Key', 'sdc-weather' ),
        'sdc_weather_api_key_field',
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
}

/**
 * Output the temperature threshold input field.
 */
function sdc_weather_temp_threshold_field() {
    $value = get_option( 'sdc_weather_temp_threshold', 0 );
    echo '<input type="number" name="sdc_weather_temp_threshold" value="' . esc_attr( $value ) . '" class="small-text" />';
}

/**
 * Output the API key input field.
 */
function sdc_weather_api_key_field() {
    $value = get_option( 'sdc_weather_api_key', '' );
    echo '<input type="text" name="sdc_weather_api_key" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

/**
 * Add the SDC Weather settings page under Settings.
 */
function sdc_weather_add_admin_menu() {
    add_options_page(
        __( 'SDC Weather', 'sdc-weather' ),
        __( 'SDC Weather', 'sdc-weather' ),
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
        <h1><?php esc_html_e( 'SDC Weather', 'sdc-weather' ); ?></h1>
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
                $icon = cww_get_accuweather_icon_url( $connection['icon'] );
                echo '<div class="weather-widget">';
                if ( ! empty( $icon ) ) {
                    echo '<img class="weather-icon" width="27" height="27" src="' . esc_url( $icon ) . '" alt="' . esc_attr( $connection['condition'] ) . '" /> ';
                }
                echo '<span class="weather-text">' . esc_html( $connection['condition'] ) . ' ' . esc_html( $connection['temperature'] ) . '&deg;</span>';
                echo '</div>';
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
