<?php

class Hintr_Admin {
  private $plugin_path;
  private $plugin_url;
  private $plugin_settings;

  public function __construct() {
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_settings = get_option('hintr_settings');

    register_activation_hook($this->plugin_path . 'hintr.php', [$this, 'activate']);
  }

  public function activate() : void
  {
    if (!is_writable(ABSPATH . 'wp-content/uploads')) {
      wp_die('Please ensure that the "uploads" directory exists in the "wp-content" folder and has the necessary write permissions.');
    }
  }
}