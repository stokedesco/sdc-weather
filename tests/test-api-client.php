<?php
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['wp_options']    = array();
        $GLOBALS['wp_transients'] = array();
        $GLOBALS['wp_filter']     = array();
        $GLOBALS['shortcode_tags'] = array();
    }

    public function test_caches_responses() {
        $GLOBALS['wp_options']['sdc_weather_api_key'] = 'abc123';
        $calls = 0;
        add_filter( 'pre_http_request', function( $pre, $args ) use ( &$calls ) {
            $calls++;
            return array( 'body' => json_encode( array( 'temp' => 70 ) ) );
        }, 10, 2 );

        $first  = sdc_weather_get_weather( 'London' );
        $second = sdc_weather_get_weather( 'London' );

        $this->assertSame( 1, $calls, 'Expected only one HTTP request due to caching.' );
        $this->assertSame( $first, $second );
    }
}
