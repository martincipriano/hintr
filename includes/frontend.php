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
    add_action('rest_api_init', [$this, 'register_routes']);
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
      'hint' => '<li><a class="hint" href="url">title</a></li>',
      'ajax_url' => admin_url('admin-ajax.php')
    ], $this->plugin_settings));
  }

  public function register_routes() : void
  {
    register_rest_route('hintr/v1', '/posts', [
      'methods'             => 'GET',
      'callback'            => [$this, 'get_posts'],
      'permission_callback' => '__return_true'
    ]);
  }

  public function get_posts(WP_REST_Request $request) {
    $settings = $this->plugin_settings;
    $post_type = array_keys($settings['search_in']);
    $per_page  = max(1, min(100, intval($request->get_param('per_page') ?? 100)));
    $page      = max(1, intval($request->get_param('page') ?? 1));

    $query = new \WP_Query([
      'post_status'    => 'publish',
      'post_type'      => $post_type,
      'posts_per_page' => (int) $per_page,
      'paged'          => (int) $page,
      'fields'         => 'ids',
    ]);

    $posts = [];

    while ($query->have_posts()) {
      $query->the_post();

      $metadata = [];
      if (isset($settings['search_in'][get_post_type()])) {
        foreach ( $settings['search_in'][get_post_type()] as $meta_key) {
          $metadata[$meta_key] = implode(', ', array_map('esc_html', get_post_meta(get_the_ID(), $meta_key, false)));
        }
      }

      $posts[] = [
        'id'        => get_the_ID(),
        'metadata'  => $metadata,
        'title'     => esc_html(get_the_title()),
        'type'      => esc_html(get_post_type()),
        'url'       => esc_url(get_permalink())
      ];
    }

    return new WP_REST_Response([
      'posts'       => $posts,
      'total_posts' => $query->found_posts,
      'total_pages' => $query->max_num_pages,
      'current_page' => (int) $page,
    ], 200);
  }
}

new Hintr;
