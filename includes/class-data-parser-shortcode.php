<?php

namespace DataParser;

class Shortcode {

    private $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        add_shortcode('dp_display_data', array($this, 'display_parsed_data'));
    }

    public function display_parsed_data($atts) {
        $atts = shortcode_atts(array(
            'url' => '',
            'format' => 'json',
            'title' => 'Parsed Data',
            'header' => 'true',
        ), $atts, 'dp_display_data');

        if (empty($atts['url']) || !filter_var($atts['url'], FILTER_VALIDATE_URL)) {
            return 'Invalid URL provided.';
        }

        $data = $this->plugin->fetch_data($atts['url']);

        if (!is_string($data)) {
            return $data;
        }

        $parsed_data = $this->plugin->parse_data($data, $atts['format']);

        if (!is_array($parsed_data)) {
            return $parsed_data;
        }

        $show_header = filter_var($atts['header'], FILTER_VALIDATE_BOOLEAN);
        return $this->plugin->generate_output($parsed_data, $atts['title'], $show_header);
    }
}