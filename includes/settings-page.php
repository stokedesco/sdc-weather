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
        'sdc_weather_color',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default'           => '#000000',
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
        'sdc_weather_color',
        __( 'Base Widget Color', 'sdc-weather' ),
        'sdc_weather_color_field',
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
    echo '<input type="text" name="sdc_weather_location" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

/**
 * Output the base widget color input field.
 */
function sdc_weather_color_field() {
    $value = get_option( 'sdc_weather_color', '#000000' );
    echo '<input type="text" name="sdc_weather_color" value="' . esc_attr( $value ) . '" class="regular-text" />';
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
