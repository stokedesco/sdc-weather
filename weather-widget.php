<?php
/**
 * Plugin Name: Custom Weather Widget
 * Description: Displays current weather information via shortcode.
 * Version: 1.0.0
 * Author: OpenAI Assistant
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
    $icon        = isset( $data['icon'] ) ? $data['icon'] : '';

    $threshold = get_option( 'weather_widget_temperature_threshold', 90 );
    $is_warning = $temperature > $threshold;

    if ( $is_warning ) {
        $icon = plugin_dir_url( __FILE__ ) . 'warning.svg';
    }

    $classes = 'weather-widget' . ( $is_warning ? ' warning' : '' );

    $html  = '<div class="' . esc_attr( $classes ) . '">';
    if ( ! empty( $icon ) ) {
        $html .= '<img class="weather-icon" width="27" height="27" src="' . esc_url( $icon ) . '" alt="' . esc_attr( $condition ) . '" />';
    }
    $html .= '<span class="weather-text">' . esc_html( $condition ) . ' ' . esc_html( $temperature ) . '&deg;</span>';
    $html .= '</div>';

    return $html;
}

add_shortcode( 'custom_weather_widget', 'cww_render_weather_widget' );

