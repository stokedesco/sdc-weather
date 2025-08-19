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
        $this->assertSame('', $registered_settings['sdc_weather_location']['default']);
        $this->assertSame('celsius', $registered_settings['sdc_weather_temp_unit']['default']);
        $this->assertSame(0, $registered_settings['sdc_weather_temp_threshold']['default']);
        $this->assertSame('', $registered_settings['sdc_weather_api_key']['default']);
    }

    public function test_api_response_is_cached() {
        global $wp_options, $mock_body, $wp_remote_get_calls;
        $wp_options['sdc_weather_api_key'] = 'abc';
        $wp_options['sdc_weather_location'] = '123';
        $wp_options['sdc_weather_temp_unit'] = 'fahrenheit';
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Sunny',
            'WeatherIcon' => 1,
            'Temperature' => array(
                'Imperial' => array('Value' => 75),
                'Metric'   => array('Value' => 24),
            ),
        )));

        $first = fetch_current_weather();
        $this->assertSame(array('condition' => 'Sunny', 'icon' => 1, 'temperature' => 75), $first);
        $this->assertSame(1, $wp_remote_get_calls);

        $second = fetch_current_weather();
        $this->assertSame($first, $second);
        $this->assertSame(1, $wp_remote_get_calls, 'API should not be called when using cache');
    }

    public function test_fetch_current_weather_celsius() {
        global $wp_options, $mock_body;
        $wp_options['sdc_weather_api_key'] = 'abc';
        $wp_options['sdc_weather_location'] = '123';
        $wp_options['sdc_weather_temp_unit'] = 'celsius';
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Sunny',
            'WeatherIcon' => 1,
            'Temperature' => array(
                'Imperial' => array('Value' => 75),
                'Metric'   => array('Value' => 24),
            ),
        )));

        $data = fetch_current_weather();
        $this->assertSame(array('condition' => 'Sunny', 'icon' => 1, 'temperature' => 24), $data);
    }

    public function test_shortcode_output_with_mocked_http() {
        global $wp_options, $mock_body;
        $wp_options['sdc_weather_api_key'] = 'key';
        $wp_options['sdc_weather_location'] = 'loc';
        $wp_options['sdc_weather_temp_threshold'] = 70;
        $wp_options['sdc_weather_temp_unit'] = 'fahrenheit';
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Cloudy',
            'WeatherIcon' => 2,
            'Temperature' => array(
                'Imperial' => array('Value' => 65),
                'Metric'   => array('Value' => 18),
            ),
        )));

        $html = cww_render_weather_widget();
        $this->assertStringContainsString('class="weather-widget"', $html);
        $this->assertStringContainsString('Cloudy 65&deg;F', $html);
        $this->assertStringNotContainsString('style=', $html);
        $this->assertStringContainsString('02-s.png', $html);
    }

    public function test_shortcode_output_with_mocked_http_celsius() {
        global $wp_options, $mock_body;
        $wp_options['sdc_weather_api_key'] = 'key';
        $wp_options['sdc_weather_location'] = 'loc';
        $wp_options['sdc_weather_temp_threshold'] = 70;
        $wp_options['sdc_weather_temp_unit'] = 'celsius';
        $mock_body = json_encode(array(array(
            'WeatherText' => 'Cloudy',
            'WeatherIcon' => 2,
            'Temperature' => array(
                'Imperial' => array('Value' => 65),
                'Metric'   => array('Value' => 18),
            ),
        )));

        $html = cww_render_weather_widget();
        $this->assertStringContainsString('class="weather-widget"', $html);
        $this->assertStringContainsString('Cloudy 18&deg;C', $html);
        $this->assertStringNotContainsString('style=', $html);
        $this->assertStringContainsString('02-s.png', $html);
    }

    public function test_shortcode_fallback_output() {
        global $wp_options;
        $wp_options['sdc_weather_api_key'] = '';
        $wp_options['sdc_weather_location'] = '';
        $html = cww_render_weather_widget();
        $this->assertStringContainsString('weather-widget unavailable', $html);
        $this->assertStringContainsString('Weather data unavailable', $html);
    }
}
