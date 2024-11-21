<?php

class Hintr_Admin {
  private $wordpress_uploads_path;
  private $plugin_uploads_path;
  private $plugin_path;
  private $plugin_url;
  private $plugin_settings;
  private $initial_plugin_settings;

  public function __construct()
  {
    $this->wordpress_uploads_path = ABSPATH . 'wp-content/uploads/';
    $this->plugin_uploads_path = ABSPATH . 'wp-content/uploads/hintr/';
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
    if (!is_writable($this->wordpress_uploads_path)) {
      wp_die('Please ensure that the "uploads" directory exists in the "wp-content" folder and has the necessary write permissions.');
    }

    // If the uploads directory exists and is writable,
    // create the hintr directory in which we will store the json files needed for the sarch
    if (!file_exists($this->plugin_uploads_path)) {
      mkdir($this->plugin_uploads_path);
    }

    // Create the plugin settings in the wp_options table
    update_option('hintr_settings', $this->initial_plugin_settings);
  }

  public function admin_notice() : void
  {
    // Check if the uploads directory exists and is writable
    // If it doesn't, display an error message
    if (!is_writable($this->wordpress_uploads_path)) {
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
      $settings = $this->plugin_settings;
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

      file_put_contents($this->plugin_uploads_path . $post_type . '.json', json_encode($post_type_data));
    }
  }

  public function delete_json($post_type = null) : void
  {
    if (isset($post_type) && $post_type) {
      if (file_exists($this->plugin_uploads_path . $post_type . '.json')) {
        unlink($this->plugin_uploads_path . $post_type . '.json');
      }
    } else {
      $files = glob($this->plugin_uploads_path . '*.json');
      foreach ($files as $file) {
        unlink($file);
      }
    }
  }

  public function update_json_post($post) : void
  {
    // Check if the file exists before doing a file get contents
    if (file_exists($this->plugin_uploads_path . $post->post_type . '.json')) {
      $json_file = file_get_contents($this->plugin_uploads_path . $post->post_type . '.json');
      $posts = json_decode($json_file, true);

      $posts[$post->ID] = [
        'metadata' => [],
        'post_type' => $post->post_type,
        'title' => $post->post_title,
        'url' => get_permalink($post)
      ];

      if ($this->plugin_settings['search_in'][$post->post_type]) {
        foreach ($this->plugin_settings['search_in'][$post->post_type] as $meta_key) {
          $metadata = get_post_meta($post->ID, $meta_key, false);
          $posts[$post->ID]['metadata'][$meta_key] = implode(',', $metadata);
        }
      }

      file_put_contents($this->plugin_uploads_path . $post->post_type . '.json', json_encode($posts));
    }
  }
}

new Hintr_Admin;