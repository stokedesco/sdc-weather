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

    register_setting(
        'sdc_weather',
        'sdc_weather_font_source',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_font_family',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Proxima Nova',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_adobe_kit',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_uploaded_font',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default'           => '',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_font_size',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '16pt',
        )
    );

    register_setting(
        'sdc_weather',
        'sdc_weather_font_weight',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '400',
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

    add_settings_section(
        'sdc_weather_typography',
        __( 'Typography', 'sdc-weather' ),
        '__return_false',
        'sdc_weather'
    );

    add_settings_field(
        'sdc_weather_font_source',
        __( 'Font Source', 'sdc-weather' ),
        'sdc_weather_font_source_field',
        'sdc_weather',
        'sdc_weather_typography'
    );

    add_settings_field(
        'sdc_weather_font_family',
        __( 'Font Family', 'sdc-weather' ),
        'sdc_weather_font_family_field',
        'sdc_weather',
        'sdc_weather_typography'
    );

    add_settings_field(
        'sdc_weather_adobe_kit',
        __( 'Adobe Kit ID', 'sdc-weather' ),
        'sdc_weather_adobe_kit_field',
        'sdc_weather',
        'sdc_weather_typography'
    );

    add_settings_field(
        'sdc_weather_uploaded_font',
        __( 'Uploaded Font URL', 'sdc-weather' ),
        'sdc_weather_uploaded_font_field',
        'sdc_weather',
        'sdc_weather_typography'
    );

    add_settings_field(
        'sdc_weather_font_size',
        __( 'Font Size', 'sdc-weather' ),
        'sdc_weather_font_size_field',
        'sdc_weather',
        'sdc_weather_typography'
    );

    add_settings_field(
        'sdc_weather_font_weight',
        __( 'Font Weight', 'sdc-weather' ),
        'sdc_weather_font_weight_field',
        'sdc_weather',
        'sdc_weather_typography'
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
 * Output the font source field.
 */
function sdc_weather_font_source_field() {
    $value   = get_option( 'sdc_weather_font_source', '' );
    $options = array(
        'google' => __( 'Google', 'sdc-weather' ),
        'adobe'  => __( 'Adobe', 'sdc-weather' ),
        'upload' => __( 'Upload', 'sdc-weather' ),
    );
    echo '<select name="sdc_weather_font_source">';
    foreach ( $options as $key => $label ) {
        printf(
            '<option value="%1$s" %2$s>%3$s</option>',
            esc_attr( $key ),
            selected( $value, $key, false ),
            esc_html( $label )
        );
    }
    echo '</select>';
}

/**
 * Output the font family field.
 */
function sdc_weather_font_family_field() {
    $value = get_option( 'sdc_weather_font_family', 'Proxima Nova' );
    echo '<input type="text" name="sdc_weather_font_family" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

/**
 * Output the Adobe kit ID field.
 */
function sdc_weather_adobe_kit_field() {
    $value = get_option( 'sdc_weather_adobe_kit', '' );
    echo '<input type="text" name="sdc_weather_adobe_kit" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

/**
 * Output the uploaded font URL field.
 */
function sdc_weather_uploaded_font_field() {
    $value = get_option( 'sdc_weather_uploaded_font', '' );
    echo '<input type="text" name="sdc_weather_uploaded_font" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

/**
 * Output the font size field.
 */
function sdc_weather_font_size_field() {
    $value = get_option( 'sdc_weather_font_size', '16pt' );
    echo '<input type="text" name="sdc_weather_font_size" value="' . esc_attr( $value ) . '" class="small-text" />';
}

/**
 * Output the font weight field.
 */
function sdc_weather_font_weight_field() {
    $value = get_option( 'sdc_weather_font_weight', '400' );
    echo '<input type="number" name="sdc_weather_font_weight" value="' . esc_attr( $value ) . '" class="small-text" />';
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
