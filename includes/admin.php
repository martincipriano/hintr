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
      'auto' => false,
      'count' => 10,
      'search_in' => [
        'page' => []
      ]
    ];

    register_activation_hook($this->plugin_path . 'hintr.php', [$this, 'activate']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function enqueue_scripts() : void
  {
    if (get_current_screen()->id !== 'settings_page_hintr') {
      return;
    }

    $css_path = $this->plugin_path . 'assets/css/hintr-admin.css';
    $css_version = file_exists($css_path) ? filemtime($css_path) : '1.0.0';

    $js_path = $this->plugin_path . 'assets/js/hintr-admin.js';
    $js_version = file_exists($js_path) ? filemtime($js_path) : '1.0.0';

    wp_enqueue_style('slim-select', $this->plugin_url . 'assets/css/slimselect.css', [], '2.9.2');
    wp_enqueue_style('hintr-admin', $this->plugin_url . 'assets/css/hintr-admin.css', [], $css_version);

    wp_enqueue_script('slim-select', $this->plugin_url . 'assets/js/slimselect.js', [], '2.9.2', true);
    wp_enqueue_script('hintr-admin', $this->plugin_url . 'assets/js/hintr-admin.js', ['slim-select'], $js_version, true);
  }

  public function activate() : void
  {
    $upload_dir = wp_upload_dir();
  
    if (!is_dir($upload_dir['basedir'])) {
      wp_die('The uploads directory does not exist. Please create it before activating the plugin.');
    }

    if (!is_writable($upload_dir['basedir'])) {
      wp_die('The uploads directory is not writable. Please make sure it has the correct permissions before activating the plugin.');
    }

    if (!$this->plugin_settings) {
      update_option('hintr_settings', $this->initial_plugin_settings);
    }

    $this->create_json_file();
  }

  protected function get_meta_keys($post_type = '') : array
  {
    global $wpdb;

    $post_type = sanitize_key($post_type);

    if (!post_type_exists($post_type)) {
      return [];
    }

    $cache_key = "{$post_type}_meta_keys";
    $cached_meta_keys = wp_cache_get($cache_key, 'hintr');

    if ($cached_meta_keys !== false) {
      return $cached_meta_keys;
    }

    $query = $wpdb->prepare(
      "SELECT DISTINCT pm.meta_key
      FROM {$wpdb->postmeta} pm
      INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
      WHERE p.post_type = %s",
      $post_type
    );

    $meta_keys = $wpdb->get_col($query);
    wp_cache_set($cache_key, $meta_keys, 'hintr', HOUR_IN_SECONDS);

    return $meta_keys;
  }

  protected function create_json_file() : void
  {
    $posts = [];
    $upload_dir = wp_upload_dir();
    $hintr_json = $upload_dir['basedir'] . '/hintr.json';
    $settings = $this->plugin_settings;
    $post_type = array_keys($settings['search_in']);

    if ($post_type) {
      $query = new \WP_Query([
        'post_status' => 'publish',
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'fields' => 'ids',
      ]);

      while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);
        $post_title = get_the_title($post_id);
        $permalink = get_permalink($post_id);

        $metadata = [];
        if (isset($settings['search_in'][$post_type])) {
          foreach ( $settings['search_in'][$post_type] as $meta_key) {
            $meta_value = get_post_meta($post_id, $meta_key, false);
            $meta_value = $this->flatten_array($meta_value);
            $metadata[$meta_key] = implode(', ', $meta_value);
          }
        }

        $object = [
          'title' => esc_html($post_title),
          'url' => esc_url($permalink),
          'type' => esc_html($post_type),
        ];
        if ($metadata) {
          $object['metadata'] = $metadata;
        }

        $posts[] = $object;
      }
    }

    file_put_contents($hintr_json, json_encode($posts));
  }

  private function flatten_array($array): array
  {
    $result = [];
    foreach ($array as $value) {
      if (is_array($value)) {
        $result = array_merge($result, $this->flatten_array($value));
      } else {
        $result[] = $value;
      }
    }
    return $result;
  }
}

new Hintr_Admin;
