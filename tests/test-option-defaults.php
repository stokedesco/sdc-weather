<?php
use PHPUnit\Framework\TestCase;

class OptionDefaultsTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['wp_options']    = array();
        $GLOBALS['wp_transients'] = array();
        $GLOBALS['wp_filter']     = array();
        $GLOBALS['shortcode_tags'] = array();
    }

    public function test_api_key_default_empty() {
        $this->assertSame( '', get_option( 'sdc_weather_api_key', '' ) );
    }
}
