<?php
use PHPUnit\Framework\TestCase;

class ShortcodeTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['wp_options']    = array();
        $GLOBALS['wp_transients'] = array();
        $GLOBALS['wp_filter']     = array();
        $GLOBALS['shortcode_tags'] = array();
        // Re-register shortcode after resetting tags.
        add_shortcode( 'sdc_weather', 'sdc_weather_render_shortcode' );
    }

    public function test_shortcode_renders_temperature() {
        add_filter( 'pre_http_request', function( $pre, $args ) {
            return array( 'body' => json_encode( array( 'temp' => 55 ) ) );
        }, 10, 2 );

        $output = do_shortcode( '[sdc_weather location="Paris"]' );
        $this->assertSame( '<div class="sdc-weather">Temperature: 55</div>', $output );
    }
}
