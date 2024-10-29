<?php

/**
 * Plugin Name: Hntr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

if (!function_exists('hntr_enqueue_scripts')) {
  add_action('wp_enqueue_scripts', 'hntr_enqueue_scripts');
  function hntr_enqueue_scripts() {
    wp_enqueue_style('hntr', plugin_dir_url(__FILE__) . 'hntr.css', [], filemtime(plugin_dir_path(__FILE__) . 'hntr.css'));
    wp_enqueue_script('hntr', plugin_dir_url(__FILE__) . 'hntr.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'hntr.js'), true);
  }
}