<?php

// Create an options page for the plugin.

if (!function_exists('hntr_create_options_page')) {
  add_action('admin_menu', 'hntr_create_options_page');
  function hntr_create_options_page() {
    add_options_page('Hntr Options', 'Hntr', 'manage_options', 'hntr', 'hntr_options_page');
  }
}
