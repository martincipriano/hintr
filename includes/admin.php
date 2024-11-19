<?php

class Hintr_Admin {
  private $uploads_path;
  private $plugin_path;
  private $plugin_url;
  private $plugin_settings;
  private $initial_plugin_settings;

  public function __construct()
  {
    $this->uploads_path = ABSPATH . 'wp-content/uploads';
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
    add_action('admin_notices', [$this, 'admin_notice']);
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
    update_option('hintr_settings', $this->initial_plugin_settings);
  }

  public function admin_notice() : void
  {
    // Check if the uploads directory exists and is writable
    // If it doesn't, display an error message
    if (!is_writable($this->uploads_path)) {
      echo '<div class="error"><p>Please ensure that the "uploads" directory exists in the "wp-content" folder and has the necessary write permissions.</p></div>';
    }
  }

  public function get_meta_keys($post_type) : array
  {
    global $wpdb;
    $query = $wpdb->prepare("SELECT DISTINCT pm.meta_key FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.post_type = %s", $post_type);
    return $wpdb->get_col($query);
  }

  public function create_json($settings = []) : void
  {
    if (!$settings) {
      $settings = get_option('hintr_settings');
    }

    $posts_per_batch = 100;

    foreach ($settings['search_in'] as $post_type => $meta_keys) {
      $post_type_data = [];
      $page = 1;

      do {
        $posts = get_posts([
          'post_status' => 'publish',
          'post_type' => $post_type,
          'posts_per_page' => $posts_per_batch,
          'paged' => $page
        ]);

        foreach ($posts as $post) {

          $post_type_data[$post->ID] = [
            'metadata' => [],
            'post_type' => $post->post_type,
            'title' => $post->post_title,
            'url' => get_permalink($post)
          ];

          foreach ($meta_keys as $meta_key) {
            $metadata = get_post_meta($post->ID, $meta_key, false);
            $post_type_data[$post->ID]['metadata'][$meta_key] = implode(',', $metadata);
          }
        }

        $page++;

      } while (count($posts) === $posts_per_batch);

      file_put_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post_type . '.json', json_encode($post_type_data));
    }
  }
}

new Hintr_Admin;