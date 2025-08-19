<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
    define( 'HOUR_IN_SECONDS', 3600 );
}

/**
 * Retrieve weather data for a location, using transient caching.
 *
 * @param string $location Location to retrieve weather for.
 * @return array|false Parsed weather data or false on failure.
 */
function sdc_weather_get_weather( $location ) {
    $cache_key = 'sdc_weather_' . md5( strtolower( $location ) );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) {
        return $cached;
    }

    $api_key = get_option( 'sdc_weather_api_key', '' );
    $url     = 'https://api.example.com/weather?location=' . rawurlencode( $location ) . '&key=' . $api_key;

    $response = wp_remote_get( $url );
    if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
        return false;
    }

    $data = json_decode( $response['body'], true );
    set_transient( $cache_key, $data, HOUR_IN_SECONDS );
    return $data;
}
