<?php
use PHPUnit\Framework\TestCase;

class WeatherPluginTest extends TestCase {
    protected function setUp(): void {
        global $wp_options, $transients, $wp_remote_get_calls, $mock_body, $registered_settings;
        $wp_options = array();
        $transients = array();
        $registered_settings = array();
        $wp_remote_get_calls = 0;
        $mock_body = '';
    }

    public function test_default_settings_registered() {
        sdc_weather_register_settings();
        global $registered_settings;
        $this->assertSame('#000000', $registered_settings['sdc_weather_color']['default']);
        $this->assertSame(0, $registered_settings['sdc_weather_temp_threshold']['default']);
        $this->assertSame('Proxima Nova', $registered_settings['sdc_weather_font_family']['default']);
    }

    public function test_api_response_is_cached() {
        global $wp_options, $mock_body, $wp_remote_get_calls;
        $wp_options['sdc_weather_api_key'] = 'abc';
        $wp_options['sdc_weather_location'] = '123';
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Sunny',
            'WeatherIcon' => 1,
            'Temperature' => array('Imperial' => array('Value' => 75)),
        )));

        $first = fetch_current_weather();
        $this->assertSame(array('condition' => 'Sunny', 'icon' => 1, 'temperature' => 75), $first);
        $this->assertSame(1, $wp_remote_get_calls);

        $second = fetch_current_weather();
        $this->assertSame($first, $second);
        $this->assertSame(1, $wp_remote_get_calls, 'API should not be called when using cache');
    }

    public function test_shortcode_output_with_mocked_http() {
        global $wp_options, $mock_body;
        $wp_options['sdc_weather_api_key'] = 'key';
        $wp_options['sdc_weather_location'] = 'loc';
        $wp_options['sdc_weather_color'] = '#00ff00';
        $wp_options['sdc_weather_temp_threshold'] = 70;
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Cloudy',
            'WeatherIcon' => 2,
            'Temperature' => array('Imperial' => array('Value' => 65)),
        )));

        $html = cww_render_weather_widget();
        $this->assertStringContainsString('class="weather-widget"', $html);
        $this->assertStringContainsString('Cloudy 65&deg;', $html);
        $this->assertStringContainsString('color:#00ff00', $html);
        $this->assertStringContainsString('02-s.png', $html);
    }
}
