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

add_action( 'wp_enqueue_scripts', 'sdc_weather_enqueue_assets' );

/**
 * Enqueue assets for the weather widget.
 */
function sdc_weather_enqueue_assets() {
    wp_enqueue_style( 'sdc-weather-widget', plugin_dir_url( __FILE__ ) . 'assets/css/widget.css', array(), '1.0.0' );
}
