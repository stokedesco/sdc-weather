<?php

// Weather widget shortcode and related functions.

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the AccuWeather icon URL for a given icon code.
 *
 * @param int|string $code AccuWeather icon code.
 * @return string Icon URL.
 */
function cww_get_accuweather_icon_url( $code ) {
    if ( '' === $code ) {
        return '';
    }

    $code = str_pad( absint( $code ), 2, '0', STR_PAD_LEFT );

    return sprintf( 'https://developer.accuweather.com/sites/default/files/%s-s.png', $code );
}

/**
 * Render the weather widget shortcode.
 *
 * @return string HTML output for the widget.
 */
function cww_render_weather_widget() {
    if ( ! function_exists( 'fetch_current_weather' ) ) {
        return '';
    }

    $data = fetch_current_weather();
    if ( empty( $data ) || ! is_array( $data ) ) {
        return '<div class="weather-widget unavailable">' . esc_html__( 'Weather data unavailable', 'sdc-weather' ) . '</div>';
    }

    $temperature = isset( $data['temperature'] ) ? floatval( $data['temperature'] ) : 0;
    $condition   = isset( $data['condition'] ) ? $data['condition'] : '';
    $icon_code   = isset( $data['icon'] ) ? $data['icon'] : '';

    $threshold  = get_option( 'sdc_weather_temp_threshold', 0 );
    $is_warning = $temperature > $threshold;

    if ( $is_warning ) {
        $icon = plugin_dir_url( __FILE__ ) . 'warning.svg';
    } else {
        $icon = cww_get_accuweather_icon_url( $icon_code );
    }

    $classes = 'weather-widget' . ( $is_warning ? ' warning' : '' );

    $html  = '<div class="' . esc_attr( $classes ) . '">';
    if ( ! empty( $icon ) ) {
        $html .= '<img class="weather-icon" width="27" height="27" src="' . esc_url( $icon ) . '" alt="' . esc_attr( $condition ) . '" />';
    }
    $unit   = get_option( 'sdc_weather_temp_unit', 'celsius' );
    $symbol = ( 'fahrenheit' === $unit ) ? 'F' : 'C';
    $html .= '<span class="weather-text">' . esc_html( $condition ) . ' ' . esc_html( $temperature ) . '&deg;' . $symbol . '</span>';
    $html .= '</div>';

    return $html;
}

add_shortcode( 'custom_weather_widget', 'cww_render_weather_widget' );

