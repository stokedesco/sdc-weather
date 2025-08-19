<?php
// Define minimal WordPress-like environment for tests.

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__);
}

if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

// Globals for storing options and transients.
$GLOBALS['wp_options'] = array();
$GLOBALS['transients'] = array();
$GLOBALS['registered_settings'] = array();
$GLOBALS['wp_remote_get_calls'] = 0;
$GLOBALS['mock_body'] = '';

// Option handling.
function get_option($name, $default = false) {
    global $wp_options;
    return array_key_exists($name, $wp_options) ? $wp_options[$name] : $default;
}
function update_option($name, $value) {
    global $wp_options;
    $wp_options[$name] = $value;
}

// Settings registration.
function register_setting($group, $name, $args = array()) {
    global $registered_settings;
    $registered_settings[$name] = $args;
}
function add_settings_section() {}
function add_settings_field() {}

// Sanitizers.
function sanitize_text_field($value) { return $value; }
function sanitize_hex_color($value) { return $value; }
function absint($value) { return (int) $value; }
function esc_url_raw($value) { return $value; }
function __($text) { return $text; }

// Escapers.
function esc_attr($text) { return $text; }
function esc_html($text) { return $text; }
function esc_url($text) { return $text; }

// Shortcodes.
function add_shortcode($tag, $func) {}

// Transients.
function set_transient($key, $value, $expiration) {
    global $transients;
    $transients[$key] = $value;
    return true;
}
function get_transient($key) {
    global $transients;
    return array_key_exists($key, $transients) ? $transients[$key] : false;
}

// HTTP API mock.
function wp_remote_get($url) {
    global $mock_body, $wp_remote_get_calls;
    $wp_remote_get_calls++;
    return array('body' => $mock_body);
}
function wp_remote_retrieve_body($response) {
    return $response['body'];
}
function is_wp_error($response) {
    return false;
}

// Misc.
function plugin_dir_url($file) {
    return '/';
}

// Load plugin files.
require_once __DIR__ . '/../includes/api-client.php';
require_once __DIR__ . '/../weather-widget.php';
require_once __DIR__ . '/../includes/settings-page.php';
