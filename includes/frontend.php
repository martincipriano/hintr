<?php

if (!defined('ABSPATH')) {
  exit;
}

class Hintr {
  protected $plugin_url;
  protected $plugin_path;
  protected $plugin_settings;

  public function __construct()
  {
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_settings = get_option('hintr_settings');

    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function enqueue_scripts() : void
  {
    $css_path = $this->plugin_path . 'assets/css/hintr.css';
    $css_version = file_exists($css_path) ? filemtime($css_path) : '1.0.0';

    $js_path = $this->plugin_path . 'assets/js/hintr.js';
    $js_version = file_exists($js_path) ? filemtime($js_path) : '1.0.0';

    wp_enqueue_style('hintr', $this->plugin_url . 'assets/css/hintr.css', [], $css_version, 'all');
    wp_enqueue_script('hintr', $this->plugin_url . 'assets/js/hintr.js', [], $js_version, true);

    wp_localize_script('hintr', 'hintrSettings', array_merge([
      'ajax_url' => admin_url('admin-ajax.php'),
      'hint' => '<li><a class="hint" href="url">title</a></li>',
      'last_updated' => get_option('hintr_last_updated'),
      'upload_dir' => wp_upload_dir()['baseurl']
    ], $this->plugin_settings));
  }
}

new Hintr;
