<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode handler for [sdc_weather].
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function sdc_weather_render_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'location' => '',
        ),
        $atts,
        'sdc_weather'
    );

    $data = sdc_weather_get_weather( $atts['location'] );
    if ( ! is_array( $data ) || ! isset( $data['temp'] ) ) {
        return '';
    }

    return '<div class="sdc-weather">Temperature: ' . esc_html( $data['temp'] ) . '</div>';
}
add_shortcode( 'sdc_weather', 'sdc_weather_render_shortcode' );
