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
        'sdc_weather_font',
        array(
            'type'              => 'integer',
            'sanitize_callback' => 'sdc_weather_sanitize_font',
            'default'           => 0,
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

    add_settings_field(
        'sdc_weather_font',
        __( 'Custom Font', 'sdc-weather' ),
        'sdc_weather_font_field',
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
 * Output the custom font upload field.
 */
function sdc_weather_font_field() {
    wp_enqueue_media();

    $font_id  = get_option( 'sdc_weather_font', 0 );
    $font_url = $font_id ? wp_get_attachment_url( $font_id ) : '';
    ?>
    <div class="sdc-weather-font-wrapper">
        <input type="hidden" id="sdc_weather_font" name="sdc_weather_font" value="<?php echo esc_attr( $font_id ); ?>" />
        <button type="button" class="button sdc-weather-font-upload"><?php esc_html_e( 'Select Font', 'sdc-weather' ); ?></button>
        <span class="sdc-weather-font-file"><?php echo esc_html( $font_url ? basename( $font_url ) : '' ); ?></span>
    </div>
    <script>
    jQuery(function($){
        var frame;
        $('.sdc-weather-font-upload').on('click', function(e){
            e.preventDefault();
            if ( frame ) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: '<?php echo esc_js( __( 'Select Font', 'sdc-weather' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this font', 'sdc-weather' ) ); ?>' },
                library: { type: ['font', 'application'] },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#sdc_weather_font').val( attachment.id );
                $('.sdc-weather-font-file').text( attachment.filename );
            });
            frame.open();
        });
    });
    </script>
    <?php
}

/**
 * Sanitize the uploaded font attachment.
 *
 * @param int $attachment_id Attachment ID.
 * @return int Sanitized attachment ID or 0 if invalid.
 */
function sdc_weather_sanitize_font( $attachment_id ) {
    $attachment_id = absint( $attachment_id );
    if ( ! $attachment_id ) {
        return 0;
    }

    $file = get_attached_file( $attachment_id );
    if ( ! $file ) {
        return 0;
    }

    $type    = wp_check_filetype( $file );
    $allowed = array( 'ttf', 'woff', 'woff2' );

    if ( empty( $type['ext'] ) || ! in_array( strtolower( $type['ext'] ), $allowed, true ) ) {
        return 0;
    }

    return $attachment_id;
}

/**
 * Output frontend CSS for custom font.
 */
function sdc_weather_frontend_font_css() {
    $font_id = get_option( 'sdc_weather_font', 0 );
    $css     = '.weather-widget { font-family: sans-serif; }';

    if ( $font_id ) {
        $file = get_attached_file( $font_id );
        $type = $file ? wp_check_filetype( $file ) : array();
        $map  = array(
            'ttf'   => 'truetype',
            'woff'  => 'woff',
            'woff2' => 'woff2',
        );

        if ( $file && ! empty( $type['ext'] ) && isset( $map[ $type['ext'] ] ) ) {
            $url    = wp_get_attachment_url( $font_id );
            $format = $map[ $type['ext'] ];
            $css    = "@font-face { font-family: 'SDCWeatherFont'; src: url('{$url}') format('{$format}'); font-weight: normal; font-style: normal; }\n";
            $css   .= ".weather-widget { font-family: 'SDCWeatherFont', sans-serif; }";
        }
    }

    wp_register_style( 'sdc-weather-font', false );
    wp_enqueue_style( 'sdc-weather-font' );
    wp_add_inline_style( 'sdc-weather-font', $css );
}
add_action( 'wp_enqueue_scripts', 'sdc_weather_frontend_font_css' );

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
