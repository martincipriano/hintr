<?php

/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

/**
 * Initialize the plugin.
 * 
 * This function is called when WordPress is initialized.
 * It checks if the uploads directory exists and is writable.
 * Create the uploads directory if it does not exist.
 * If the uploads directory is not writable, change the permissions to 0755.
 * Create the hintr directory in the uploads directory if it does not exist.
 * 
 * @return void
 * 
 * @since 0.0.1
 */
if (!function_exists('hintr_init')) {
  add_action('init', 'hintr_init');
  function hintr_init() {
    if (!file_exists(ABSPATH . 'wp-content/uploads')) {
      wp_mkdir_p(ABSPATH . 'wp-content/uploads');
    }
    if (!is_writable(ABSPATH . 'wp-content/uploads')) {
      chmod(ABSPATH . 'wp-content/uploads', 0755);
    }
    if (!file_exists(ABSPATH . 'wp-content/uploads/hintr')) {
      wp_mkdir_p(ABSPATH . 'wp-content/uploads/hintr');
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