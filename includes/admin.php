<?php

class Hintr_Admin {
  private $plugin_path;
  private $plugin_url;
  private $plugin_settings;
  private $initial_plugin_settings;

  public function __construct() {
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_settings = get_option('hintr_settings');
    $this->initial_plugin_settings = [
      'search_in' => [
        'post' => [],
        'page' => []
      ]
    ];

    register_activation_hook($this->plugin_path . 'hintr.php', [$this, 'activate']);
  }

  public function activate() : void
  {
    // Check if the uploads directory exists and is writable
    // If it doesn't, display an error message and stop the activation process
    if (!is_writable(ABSPATH . 'wp-content/uploads')) {
      wp_die('Please ensure that the "uploads" directory exists in the "wp-content" folder and has the necessary write permissions.');
    }

    // If the uploads directory exists and is writable,
    // create the hintr directory in which we will store the json files needed for the sarch
    if (!file_exists(ABSPATH . 'wp-content/uploads/hintr')) {
      mkdir(ABSPATH . 'wp-content/uploads/hintr');
    }

    // Create the plugin settings in the wp_options table
    update_option('hintr_settings', $initial_settings);
  }
}