<?php

if (!defined('ABSPATH')) {
  exit;
}

class Hintr_Admin {
  protected $plugin_url;
  protected $plugin_path;
  protected $plugin_settings;
  protected $initial_plugin_settings;

  public function __construct()
  {
    $this->plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->plugin_path = plugin_dir_path(dirname(__FILE__));
    $this->plugin_settings = get_option('hintr_settings');
    $this->initial_plugin_settings = [
      'search_in' => [
        'post' => [],
        'page' => []
      ]
    ];

    register_activation_hook($this->plugin_path . 'hintr.php', [$this, 'activate']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function enqueue_scripts() : void
  {
    wp_enqueue_style('slim-select', $this->plugin_url . 'assets/css/slimselect.min.css', [], '2.9.2');
    wp_enqueue_style('hintr-admin', $this->plugin_url . 'assets/css/hintr-admin.css', [], (filemtime($this->plugin_path . 'assets/css/hintr-admin.css') ? filemtime($this->plugin_path . 'assets/css/hintr-admin.css') : '1.0.0'));

    wp_enqueue_script('slim-select', $this->plugin_url . 'assets/js/slimselect.min.js', [], '2.9.2', true);
    wp_enqueue_script('hintr-admin', $this->plugin_url . 'assets/js/hintr-admin.js', ['slim-select'], (filemtime($this->plugin_path . 'assets/js/hintr-admin.js') ? filemtime($this->plugin_path . 'assets/js/hintr-admin.js') : '1.0.0'), true);
  }

  protected function activate() : void
  {
    update_option('hintr_settings', $this->initial_plugin_settings);
  }

  protected function get_meta_keys($post_type = '') : array
  {
    global $wpdb;

    if (!post_type_exists($post_type)) {
      return [];
    }

    $query = $wpdb->prepare("SELECT DISTINCT pm.meta_key FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.post_type = %s", $post_type);
    return $wpdb->get_col($query);
  }
}

new Hintr_Admin;
