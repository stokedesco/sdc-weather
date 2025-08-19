<?php
/**
 * Plugin Name: SDC Weather
 * Description: Weather information plugin.
 * Version: 1.0.0
 * Author: SDC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load core functionality early.
require_once plugin_dir_path( __FILE__ ) . 'includes/api-client.php';
require_once plugin_dir_path( __FILE__ ) . 'weather-widget.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';

add_action( 'wp_enqueue_scripts', 'sdc_weather_enqueue_typography' );

/**
 * Enqueue typography styles for the weather widget.
 */
function sdc_weather_enqueue_typography() {
    $font_source       = get_option( 'sdc_weather_font_source', '' );
    $font_family       = get_option( 'sdc_weather_font_family', 'Proxima Nova' );
    $font_size         = get_option( 'sdc_weather_font_size', '16pt' );
    $font_weight       = get_option( 'sdc_weather_font_weight', '400' );
    $adobe_kit         = get_option( 'sdc_weather_adobe_kit', '' );
    $uploaded_font_id  = get_option( 'sdc_weather_uploaded_font', 0 );
    $uploaded_font_url = $uploaded_font_id ? wp_get_attachment_url( $uploaded_font_id ) : '';

    wp_register_style( 'sdc-weather-widget', plugin_dir_url( __FILE__ ) . 'assets/css/widget.css', array(), '1.0.0' );
    wp_enqueue_style( 'sdc-weather-widget' );

    $css = '';

    if ( 'google' === $font_source && ! empty( $font_family ) ) {
        $google_url = 'https://fonts.googleapis.com/css?family=' . urlencode( $font_family );
        if ( ! empty( $font_weight ) ) {
            $google_url .= ':' . esc_attr( $font_weight );
        }
        wp_enqueue_style( 'sdc-weather-google-font', $google_url, array(), null );
    } elseif ( 'adobe' === $font_source && ! empty( $adobe_kit ) ) {
        wp_enqueue_style( 'sdc-weather-adobe-font', 'https://use.typekit.net/' . trim( $adobe_kit ) . '.css', array(), null );
    } elseif ( 'upload' === $font_source && ! empty( $uploaded_font_url ) ) {
        $mime   = get_post_mime_type( $uploaded_font_id );
        $format = 'woff2';
        if ( in_array( $mime, array( 'font/ttf', 'application/x-font-ttf' ), true ) ) {
            $format = 'truetype';
        } elseif ( 'font/otf' === $mime ) {
            $format = 'opentype';
        } elseif ( in_array( $mime, array( 'font/woff', 'application/font-woff' ), true ) ) {
            $format = 'woff';
        }

        $css        .= "@font-face { font-family: 'sdc-weather-upload'; src: url('" . esc_url( $uploaded_font_url ) . "') format('" . esc_attr( $format ) . "'); font-weight: normal; font-style: normal; }";
        $font_family = 'sdc-weather-upload';
    }

    $css .= '.weather-widget { font-family: ' . esc_attr( $font_family ) . '; font-size: ' . esc_attr( $font_size ) . '; font-weight: ' . esc_attr( $font_weight ) . '; }';

    wp_add_inline_style( 'sdc-weather-widget', $css );
}
