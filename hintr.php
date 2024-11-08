<?php
/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

/**
 * Activation callback for the Hintr plugin.
 *
 * This function is triggered when the plugin is activated. It checks if the
 * 'hintr_settings' option already exists in the WordPress database. If the
 * option does not exist, it initializes the default settings with an array
 * specifying 'search_in' for 'post' and 'page'.
 *
 * @return void
 */
if (!function_exists('hintr_activate')) {
  register_activation_hook(__FILE__, 'hintr_activate');
  function hintr_activate() {
    $settings = get_option('hintr_settings');
    if (!$settings)  {
      update_option('hintr_settings', [
        'search_in' => [
          'post' => [],
          'page' => []
        ],
      ]);
    }
  }
}










if (is_admin()) {
  require_once plugin_dir_path(__FILE__) . 'inc/admin.php';
} else {
  require_once plugin_dir_path(__FILE__) . 'inc/public.php';
}
