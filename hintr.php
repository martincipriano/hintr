<?php

/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

if (!function_exists('hintr_enqueue_scripts')) {
  add_action('wp_enqueue_scripts', 'hintr_enqueue_scripts');
  function hintr_enqueue_scripts() {
    wp_enqueue_style('hintr', plugin_dir_url(__FILE__) . 'hintr.css', [], filemtime(plugin_dir_path(__FILE__) . 'hintr.css'));
    wp_enqueue_script('hintr', plugin_dir_url(__FILE__) . 'hintr.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'hintr.js'), true);
  }
}