<?php

namespace DataParser;

use League\Csv\Reader;

class Plugin {

    public function __construct() {
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        new Shortcode($this);
    }

    public function register_settings_page() {
        add_options_page(
            'Data Parser Settings',
            'Data Parser',
            'manage_options',
            'data-parser-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Data Parser Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('dp_settings_group');
                do_settings_sections('data-parser-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('dp_settings_group', 'dp_cache_duration');

        add_settings_section(
            'dp_main_section',
            'General Settings',
            array($this, 'main_section_callback'),
            'data-parser-settings'
        );

        add_settings_field(
            'dp_cache_duration',
            'Cache Duration (seconds)',
            array($this, 'cache_duration_callback'),
            'data-parser-settings',
            'dp_main_section'
        );
    }

    public function main_section_callback() {
        echo '<p>Configure general settings for the Data Parser plugin.</p>';
    }

    public function cache_duration_callback() {
        $value = get_option('dp_cache_duration', 3600); // Default to 1 hour
        echo '<input type="number" name="dp_cache_duration" value="' . esc_attr($value) . '" />';
    }

    public function update_cache_duration() {
        $cache_duration = get_option('dp_cache_duration', 3600);
        return intval($cache_duration);
    }

    public function fetch_data($url) {
        $cache_key = 'dp_data_' . md5($url);
        $cached_data = get_transient($cache_key);

        if ($cached_data !== false) {
            return $cached_data;
        }

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return 'Error fetching data: ' . $response->get_error_message();
        }

        $body = wp_remote_retrieve_body($response);

        set_transient($cache_key, $body, $this->update_cache_duration());

        return $body;
    }

    public function parse_json_data($data) {
        $parsed_data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return 'Error decoding JSON data: ' . json_last_error_msg();
        }

        return $parsed_data;
    }

    public function parse_xml_data($data) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);

        if (!$xml) {
            $errors = libxml_get_errors();
            $error_message = '';
            foreach ($errors as $error) {
                $error_message .= $error->message . ' ';
            }
            libxml_clear_errors();
            return 'Error decoding XML data: ' . $error_message;
        }

        $parsed_data = json_decode(json_encode($xml), true);

        return $parsed_data;
    }

    public function parse_csv_data($data) {
        try {
            $csv = Reader::createFromString($data);
            $parsed_data = iterator_to_array($csv->getRecords());
        } catch (\Exception $e) {
            return 'Error decoding CSV data: ' . $e->getMessage();
        }

        return $parsed_data;
    }

    public function parse_data($data, $format) {
        switch (strtolower($format)) {
            case 'json':
                return $this->parse_json_data($data);
            case 'xml':
                return $this->parse_xml_data($data);
            case 'csv':
                return $this->parse_csv_data($data);
            default:
                return 'Unsupported data format: ' . esc_html($format);
        }
    }

    public function generate_output($parsed_data, $title, $show_header) {
        $output = '<div class="dp-data">';
        $output .= '<h1>' . esc_html($title) . '</h1>';
        $output .= '<p>';

        if ($show_header && isset($parsed_data[0]) && is_array($parsed_data[0])) {
            $output .= '<strong>Headers:</strong><br>';
            foreach (array_keys($parsed_data[0]) as $header) {
                $output .= esc_html(ucwords(str_replace('_', ' ', $header))) . '<br>';
            }
            $output .= '<br>';
        }

        foreach ($parsed_data as $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $output .= '<strong>' . esc_html(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . esc_html($value) . '<br>';
                }
            } else {
                $output .= esc_html($item) . '<br>';
            }
        }

        $output .= '</p>';
        $output .= '</div>';

        return $output;
    }
}