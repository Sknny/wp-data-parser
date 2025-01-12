<?php
/*
Plugin Name: Data Parser
Description: Plugin to parse JSON, XML and CSV data from a URL and display it
Version: 0.1
Author: Igor Slavin
*/

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('DP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once DP_PLUGIN_DIR . 'vendor/league-csv/autoload.php';

require_once DP_PLUGIN_DIR . 'includes/class-data-parser.php';
require_once DP_PLUGIN_DIR . 'includes/class-data-parser-shortcode.php';

function dp_init_plugin() {
    new DataParser\Plugin();
}

add_action('plugins_loaded', 'dp_init_plugin');