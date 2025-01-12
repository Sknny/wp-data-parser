# Data Parser Plugin for WordPress

## Description
The Data Parser plugin allows you to fetch and display data from URLs in various formats (JSON, XML, CSV) on your WordPress site

## Installation
1. Upload the `data-parser` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Usage
Use the `[dp_display_data]` shortcode to display parsed data

### Attributes
* `url:` The URL of the data source
* `format:` The format of the data (json, xml, yaml, csv). Default is ___json___
* `title:` Title to display above the parsed data. Default is ___Parsed Data___
* `header:` Whether to display headers for CSV. Default is ___true___

### Examples
JSON:
```
[dp_display_data url="https://api.example.com/data.json" format="json" title="JSON Data"]
```
XML:
```
[dp_display_data url="https://api.example.com/data.xml" format="xml" title="XML Data"]
```
CSV:
```
[dp_display_data url="https://api.example.com/data.csv" format="csv" title="CSV Data" header="false"]
```
You can check an example in the data.json file and use this URL for testing: `https://<YOUR_WP_DOMAIN>/wp-content/plugins/data-parser/data.json`

### Settings
Navigate to `Settings > Data Parser` to configure the cache duration