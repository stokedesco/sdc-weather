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

// Load settings.
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';
// Load API client and shortcode.
require_once plugin_dir_path( __FILE__ ) . 'includes/api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
