<?php
define( 'ABSPATH', true );

if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
    define( 'HOUR_IN_SECONDS', 3600 );
}

// Option storage.
$GLOBALS['wp_options'] = array();
function get_option( $name, $default = false ) {
    return array_key_exists( $name, $GLOBALS['wp_options'] ) ? $GLOBALS['wp_options'][ $name ] : $default;
}
function update_option( $name, $value ) {
    $GLOBALS['wp_options'][ $name ] = $value;
}

// Transient storage.
$GLOBALS['wp_transients'] = array();
function get_transient( $key ) {
    if ( isset( $GLOBALS['wp_transients'][ $key ] ) ) {
        $item = $GLOBALS['wp_transients'][ $key ];
        if ( $item['expiration'] >= time() ) {
            return $item['value'];
        }
    }
    return false;
}
function set_transient( $key, $value, $expiration ) {
    $GLOBALS['wp_transients'][ $key ] = array(
        'value'      => $value,
        'expiration' => time() + $expiration,
    );
}
function delete_transient( $key ) {
    unset( $GLOBALS['wp_transients'][ $key ] );
}

// Filter system.
$GLOBALS['wp_filter'] = array();
function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
    $GLOBALS['wp_filter'][ $tag ][] = $function_to_add;
}
function apply_filters( $tag, $value ) {
    $args = func_get_args();
    array_shift( $args );
    array_shift( $args );
    if ( isset( $GLOBALS['wp_filter'][ $tag ] ) ) {
        foreach ( $GLOBALS['wp_filter'][ $tag ] as $callback ) {
            $value = $callback( $value, ...$args );
        }
    }
    return $value;
}

function wp_remote_get( $url ) {
    $response = apply_filters( 'pre_http_request', null, array( 'url' => $url ) );
    if ( null !== $response ) {
        return $response;
    }
    return array( 'body' => '' );
}
function is_wp_error( $thing ) {
    return false;
}

// Shortcode system.
$GLOBALS['shortcode_tags'] = array();
function add_shortcode( $tag, $func ) {
    $GLOBALS['shortcode_tags'][ $tag ] = $func;
}
function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
    return array_merge( $pairs, $atts );
}
function do_shortcode( $content ) {
    return preg_replace_callback( '/\[(\w+)([^\]]*)\]/', function ( $matches ) {
        $tag = $matches[1];
        if ( ! isset( $GLOBALS['shortcode_tags'][ $tag ] ) ) {
            return $matches[0];
        }
        $atts_string = trim( $matches[2] );
        $atts       = array();
        if ( preg_match_all( '/(\w+)="([^"]*)"/', $atts_string, $attr_matches, PREG_SET_ORDER ) ) {
            foreach ( $attr_matches as $attr ) {
                $atts[ $attr[1] ] = $attr[2];
            }
        }
        return call_user_func( $GLOBALS['shortcode_tags'][ $tag ], $atts );
    }, $content );
}

function esc_html( $text ) {
    return htmlspecialchars( $text, ENT_QUOTES );
}
function esc_attr( $text ) {
    return $text;
}
function sanitize_text_field( $text ) {
    return $text;
}
function __( $text, $domain = '' ) {
    return $text;
}
function esc_html_e( $text, $domain = '' ) {
    echo esc_html( $text );
}
function plugin_dir_path( $file ) {
    return __DIR__ . '/../';
}
function add_options_page() {}
function register_setting() {}
function add_settings_section() {}
function add_settings_field() {}
function add_action() {}
function settings_fields() {}
function do_settings_sections() {}
function submit_button() {}

// Load plugin files needed for tests.
require_once __DIR__ . '/../includes/api.php';
require_once __DIR__ . '/../includes/shortcode.php';
