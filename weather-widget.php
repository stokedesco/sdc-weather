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
        return '';
    }

    $temperature = isset( $data['temperature'] ) ? floatval( $data['temperature'] ) : 0;
    $condition   = isset( $data['condition'] ) ? $data['condition'] : '';
    $icon_code   = isset( $data['icon'] ) ? $data['icon'] : '';

    $base_color = get_option( 'sdc_weather_color', '#000000' );
    $threshold  = get_option( 'sdc_weather_temp_threshold', 0 );
    $is_warning = $temperature > $threshold;

    if ( $is_warning ) {
        $icon  = plugin_dir_url( __FILE__ ) . 'warning.svg';
        $color = '#ff0000';
    } else {
        $icon  = cww_get_accuweather_icon_url( $icon_code );
        $color = $base_color;
    }

    $classes = 'weather-widget' . ( $is_warning ? ' warning' : '' );

    $html  = '<div class="' . esc_attr( $classes ) . '" style="color:' . esc_attr( $color ) . '">';
    if ( ! empty( $icon ) ) {
        $html .= '<img class="weather-icon" width="27" height="27" src="' . esc_url( $icon ) . '" alt="' . esc_attr( $condition ) . '" />';
    }
    $html .= '<span class="weather-text">' . esc_html( $condition ) . ' ' . esc_html( $temperature ) . '&deg;</span>';
    $html .= '</div>';

    return $html;
}

add_shortcode( 'custom_weather_widget', 'cww_render_weather_widget' );

