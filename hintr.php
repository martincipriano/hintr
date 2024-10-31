<?php

/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

/**
 * Activate the plugin.
 * 
 * This function is called when the plugin is activated.
 * It checks if the uploads directory exists and is writable.
 * If it does not exist or is not writable, it displays an error message.
 * 
 * @return void
 * 
 * @since 0.0.1
 */
if (!function_exists('hintr_activate')) {
  register_activation_hook(__FILE__, 'hintr_activate');
  function hintr_activate() {
    if (!wp_mkdir_p(ABSPATH . 'wp-content/uploads/hintr') || !is_writable(ABSPATH . 'wp-content/uploads/hintr')) {
      wp_die('The plugin needs an upload directory at wp-content/uploads/hintr that is writable.');
    }
  }
}

if (!function_exists('hintr_enqueue_scripts')) {
  add_action('wp_enqueue_scripts', 'hintr_enqueue_scripts');
  function hintr_enqueue_scripts() {
    wp_enqueue_style('hintr', plugin_dir_url(__FILE__) . 'hintr.css', [], filemtime(plugin_dir_path(__FILE__) . 'hintr.css'));
    wp_enqueue_script('hintr', plugin_dir_url(__FILE__) . 'hintr.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'hintr.js'), true);
  }
}