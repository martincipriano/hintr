# Hintr - Lightning-Fast, Advanced Search Suggestions

**Hintr** is a WordPress plugin designed to improve search by providing lightning-fast suggestions. Easily configure which post types to pull search suggestions from. You can also show suggestions if the search keyword is found in a post's metadata, such as price, SKU, serial number, etc.

### Features:
- Effortlessly select which post types search suggestions should be pulled from.
- Show suggestions when the search keyword is found in a post's metadata.
- Customize search behavior for different input fields via data attributes.
- Lightweight and highly configurable, ensuring seamless integration.

This plugin is perfect for blogs, e-commerce sites, or any WordPress site that can benefit from enhanced search capabilities.

---

## Installation

1. Download the plugin file and extract it.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Settings > Hintr** to configure the plugin options.

---

## Usage
- Automatic Application: Search suggestions are automatically applied to text inputs with the data attribute name="s".
- Custom Implementation: To customize behavior, add the data-hintr attribute to a text input.
  - Override the default settings by assigning a JSON-formatted settings value to the data-hintr attribute.

---

## Frequently Asked Questions

### What does Hintr do?
Hintr enhances your search input with a dynamic list of suggestions. It allows you to retrieve suggestions from specific post types and display posts when the search keyword matches their metadata.

### Can I use this plugin with custom post types?
Yes! Hintr works with any public custom post type registered on your site.

### Do I need to code anything?
- No coding required when using the global settings for effortless setup.
- Fine-tune search suggestions for multiple search inputs with basic knowledge of:
  - Writing a JSON string.
  - Using HTML data attributes.

### How can I apply different search suggestion settings to multiple search inputs?

To apply custom settings for each search input, add the `data-hintr` attribute to the respective `<input type="text"> or <input type="search">` fields. Here's what you can configure:

- `count` (int/string): The number of suggestions to display.
- `search_in` (array): Define the post types and corresponding metadata to search against.
  - `key` (string): The post type from which suggestions will be pulled (e.g., 'post', 'page').
  - `value` (array): An array of meta keys that will be compared to the search keyword.

Example usage:

```html
<input type="text" data-hintr='{
  "count": "10",
  "search_in": {
    "post": ["meta_key_1", "meta_key_2", "meta_key_3"],
    "page": ["meta_key_1", "meta_key_2", "meta_key_3"]
  }
}'>
```

### Will this plugin slow down my site?
Hintr is optimized for speed, but performance can be affected by the number of posts being searched.

---

## Changelog

### 1.0.0
- **Added:** Admin settings page for selecting post types and metadata.
- **Added:** REST API endpoint based on selected settings.
- **Added:** JavaScript to store posts in the browser's local storage.
- **Added:** JavaScript to show search suggestions in a dropdown.

### 1.1.0
- **Added:** Count limit field in settings page.
- **Added:** JavaScript to limit the number of suggestions displayed in the dropdown.

### 1.1.1  
- **Fixed:** Apply fixes for text domain mismatch required by WordPress.

### 1.1.2
- **Added:** Cache the database query results for getting metadata used by a post type in the settings page.

### 1.1.3
- **Added:** Donation link and screenshots to the readme file.

### 1.1.4
- **Added:** Add a suggestion count to improve performance and prevent showing too many suggestions.
- **Updated:** assets/js/hintr.js to replace the use of hashed post. Instead check when the local storage was last updated before updating the local storage.
- **Updated:** Updated slimselect.js to use the uinminified version

---

## Upgrade Notice

### 1.1.4
**Update: Store when the settings and posts were last updated. Compare it to when the local storage was last updated before updating the local storage instead of hashing the entire json posts.**

---

## License

This plugin is licensed under the **GNU General Public License v2.0 or later**.
You may obtain a copy of the license at: [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html).

---

## Screenshots

1. ![Screenshot 1](https://github.com/martincipriano/hintr/blob/master/assets/screenshot-1.jpg?raw=true "Post type settings")
   **Description:** Displays post suggestions pulled from the selected post types.

2. ![Screenshot 2](https://github.com/martincipriano/hintr/blob/master/assets/screenshot-2.jpg?raw=true "Post metadata settings")
   **Description:** Displays post suggestions if the keyword partially matches its metadata.

3. ![Screenshot 3](https://github.com/martincipriano/hintr/blob/master/assets/screenshot-3.jpg?raw=true "Search suggestions")
   **Description:** Searching for "capacitor" will display posts with the keyword in their title.
   Searching "electronics" will show posts where the keyword is present in the metadata.
