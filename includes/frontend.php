<?php

class Hintr {
  private $plugin_path;
  private $plugin_url;
  private $plugin_uploads_url;
  private $plugin_settings;

  public function __construct()
  {
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_uploads_url = wp_upload_dir()['baseurl'] . '/hintr/';
    $this->plugin_settings = get_option('hintr_settings');

    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function enqueue_scripts() : void
  {
    wp_enqueue_style('hintr', $this->plugin_url . 'assets/css/hintr.css', [], filemtime($this->plugin_path . 'assets/css/hintr.css'), 'all');
    wp_enqueue_script('hintr', $this->plugin_url . 'assets/js/hintr.js', [], filemtime($this->plugin_path . 'assets/js/hintr.js'), true);

    wp_localize_script('hintr', 'hintr', [
      'hint' => '<li><a class="hint" href="url">title</a></li>',
      'search_in' => $this->plugin_settings,
      'uploads_url' => $this->plugin_uploads_url
    ]);
  }
}

new Hintr;