<?php

// Weather widget shortcode and related functions.

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get markup for an icon matching the given AccuWeather code.
 *
 * @param int|string $code AccuWeather icon code.
 * @return string Icon HTML.
 */
function cww_get_icon_markup( $code ) {
    $map = array(
        1  => 'fa-sun',
        2  => 'fa-cloud-sun',
        3  => 'fa-cloud-sun',
        4  => 'fa-cloud-sun',
        5  => 'fa-cloud-sun',
        6  => 'fa-cloud-sun',
        7  => 'fa-cloud',
        8  => 'fa-cloud',
        11 => 'fa-cloud',
        12 => 'fa-cloud-rain',
        13 => 'fa-cloud-rain',
        14 => 'fa-cloud-showers-heavy',
        15 => 'fa-cloud-showers-heavy',
        16 => 'fa-cloud-bolt',
        17 => 'fa-cloud-bolt',
        18 => 'fa-cloud-rain',
        19 => 'fa-snowflake',
        20 => 'fa-snowflake',
        21 => 'fa-snowflake',
        22 => 'fa-snowflake',
        23 => 'fa-snowflake',
        24 => 'fa-snowflake',
        25 => 'fa-snowflake',
        26 => 'fa-snowflake',
        29 => 'fa-wind',
    );

    $class = isset( $map[ absint( $code ) ] ) ? $map[ absint( $code ) ] : 'fa-cloud';

    return '<i class="weather-icon fa-solid ' . esc_attr( $class ) . '"></i>';
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

    $unit      = get_option( 'sdc_weather_temp_unit', 'celsius' );
    $threshold = get_option( 'sdc_weather_temp_threshold', 0 );
    $is_warning = $temperature > $threshold;
    $icon       = $is_warning ? '<i class="weather-icon fa-solid fa-triangle-exclamation"></i>' : cww_get_icon_markup( $icon_code );
    $classes    = 'weather-widget' . ( $is_warning ? ' warning' : '' );
    $suffix     = ( 'fahrenheit' === $unit ) ? '&deg;F' : '&deg;C';

    $html  = '<div class="' . esc_attr( $classes ) . '">';
    $html .= $icon;
    $html .= '<span class="weather-text">' . esc_html( $condition ) . ' ' . esc_html( $temperature ) . $suffix . '</span>';
    $html .= '</div>';

    return $html;
}

add_shortcode( 'stoke_weather_widget', 'cww_render_weather_widget' );

