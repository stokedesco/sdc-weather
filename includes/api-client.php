<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fetch current weather data from AccuWeather.
 *
 * Uses a transient cache to minimize external requests.
 *
 * @param string $location Optional location key. Falls back to saved option.
 * @return array Associative array containing condition, icon, and temperature.
 */
function fetch_current_weather( $location = '' ) {
    $api_key = get_option( 'sdc_weather_api_key', '' );
    if ( empty( $api_key ) ) {
        return array();
    }

    if ( empty( $location ) ) {
        $location = get_option( 'sdc_weather_location', '' );
    }

    if ( empty( $location ) ) {
        return array();
    }

    $transient_key = 'sdc_weather_' . md5( $location );
    $cached = get_transient( $transient_key );
    if ( false !== $cached ) {
        return $cached;
    }

    $url = sprintf(
        'https://dataservice.accuweather.com/currentconditions/v1/%s?apikey=%s',
        rawurlencode( $location ),
        rawurlencode( $api_key )
    );

    $response = wp_remote_get( $url );
    if ( is_wp_error( $response ) ) {
        return array();
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    if ( empty( $data ) || ! is_array( $data ) ) {
        return array();
    }

    $current = reset( $data );
    if ( empty( $current ) || ! is_array( $current ) ) {
        return array();
    }

    $result = array(
        'condition'   => isset( $current['WeatherText'] ) ? $current['WeatherText'] : '',
        'icon'        => isset( $current['WeatherIcon'] ) ? $current['WeatherIcon'] : '',
        'temperature' => isset( $current['Temperature']['Imperial']['Value'] ) ? $current['Temperature']['Imperial']['Value'] : '',
    );

    set_transient( $transient_key, $result, HOUR_IN_SECONDS );

    return $result;
}
