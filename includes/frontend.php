<?php

class Hintr {
  protected $plugin_url;
  protected $plugin_path;
  protected $plugin_uploads_url;
  protected $plugin_settings;

  public function __construct()
  {
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_uploads_url = wp_upload_dir()['baseurl'] . '/hintr/';
    $this->plugin_settings = get_option('hintr_settings');

    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_filter('script_loader_tag', [$this, 'script_attribute'], 10, 3);
  }

  public function enqueue_scripts() : void
  {
    wp_enqueue_style('hintr', $this->plugin_url . 'assets/css/hintr.css', [], filemtime($this->plugin_path . 'assets/css/hintr.css'), 'all');
    wp_enqueue_script('hintr', $this->plugin_url . 'assets/js/hintr.js', [], filemtime($this->plugin_path . 'assets/js/hintr.js'), true);

    wp_localize_script('hintr', 'hintrSettings', array_merge([
      'hint' => '<li><a class="hint" href="url">title</a></li>',
      'ajax_url' => admin_url('admin-ajax.php'),
      'uploads_url' => $this->plugin_uploads_url
    ], $this->plugin_settings));
  }

  public function script_attribute($tag, $handle, $src) : void
  {
    $async_handles = [];
    if (in_array($handle, $async_handles)) {
      return str_replace('<script ', '<script async ', $tag);
    }
    return $tag;
  } 
}

new Hintr;