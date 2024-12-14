=== Hintr ===
Contributors: martincipriano
Donate link: https://www.paypal.me/martincipriano
Tags: search, suggestion
Requires at least: 5.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance your WordPress site's search functionality by offering search suggestions sourced from selected post types and metadata.

== Description ==

**Hintr** is a WordPress plugin designed to improve search by providing lightning-fast suggestions.
Easily configure which post types to search in. You can also customize it to search for keywords within specific post metadata like price, SKU, serial number, etc.

**Features:**
- Effortlessly select which post types should provide search suggestions.
- Refine search suggestions by targeting specific metadata fields.
- Customize search behavior for different input fields via data attributes.
- Lightweight and highly configurable, ensuring seamless integration.

This plugin is perfect for blogs, e-commerce sites, or any WordPress site that can benefit from enhanced search capabilities.

== Installation ==

1. Download the plugin file and extract it.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings > Hintr** to configure the plugin options.

== Frequently Asked Questions ==

= What does Hintr do? =
Hintr enhances your search input by adding a dynamic list of suggestions. You can configure it to retrieve suggestions from specific post types and target keywords within selected metadata fields.

= Can I use this plugin with custom post types? =
Yes! Hintr works with any public custom post type registered on your site.

= Do I need to code anything? =
- No coding required when using the global settings for effortless setup.
- Fine-tune search suggestions for multiple search inputs with basic knowledge of:
  - Writing a JSON string.
  - Using HTML data attributes.

= Will this plugin slow down my site? =
Hintr is optimized for speed, but performance can be affected by the number of posts being searched.

== Changelog ==

= 1.0.0 =
* Added: Admin settings page for selecting post types and metadata.
* Added: REST API endpoint based on selected settings.
* Added: JavaScript to store posts in the browser's local storage.
* Added: JavaScript to show search suggestions in a dropdown.

= 1.1.0 =
* Added: Count limit field in settings page.
* Added: JavaScript to limit the number of suggestions displayed in the dropdown.

= 1.1.1 =
* Apply fixes for text domain mismatch required by WordPress

= 1.1.2 =
* Cache the database query results for getting metadata used by a post type in the settings page

== Upgrade Notice ==

= 1.1.0 =
* Added a count limit for dropdown suggestions to improve performance.

== License ==

This plugin is licensed under the GNU General Public License v2.0 or later.  
You may obtain a copy of the license at: https://www.gnu.org/licenses/gpl-2.0.html

== Screenshots ==
1. ![Screenshot 1](assets/screenshot-1.png)
  *Description: Displays post suggestions pulled from the selected post types.*

2. ![Screenshot 2](assets/screenshot-2.png)
  *Description: Displays post suggestions if the keyword partially matches its metadata*

3. ![Screenshot 3](assets/screenshot-3.png)
  *Description: Searching for "capacitor" will display posts with the keyword in their title. Searching "electronics" will show posts where the keyword is present in the metadata.*