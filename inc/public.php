<?php

/**
 * Initialize the plugin.
 *
 * This function is called when WordPress is initialized.
 * If an upload and hintr directory does not exist, create them.
 * If a JSON file for a post type does not exist, create it.
 *
 * @return void
 *
 * @since 0.0.1
 */
if (!function_exists('hintr_init')) {
  add_action('init', 'hintr_init');
  function hintr_init() {

    if (!file_exists(ABSPATH . 'wp-content/uploads')) {
      wp_mkdir_p(ABSPATH . 'wp-content/uploads');
    }

    if (!is_writable(ABSPATH . 'wp-content/uploads')) {
      chmod(ABSPATH . 'wp-content/uploads', 0755);
    }

    if (!file_exists(ABSPATH . 'wp-content/uploads/hintr')) {
      wp_mkdir_p(ABSPATH . 'wp-content/uploads/hintr');
    }

    $post_types = get_post_types(['exclude_from_search' => false], 'names');

    unset($post_types['revision']);
    unset($post_types['wp_global_styles']);
    unset($post_types['attachment']);

    foreach ($post_types as $post_type) {
      if (!file_exists(ABSPATH . 'wp-content/uploads/hintr/' . $post_type . '.json')) {
        file_put_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post_type . '.json', json_encode([]));
        hintr_create_json($post_type);
      }
    }
  }
}

/**
 * Create a JSON file for a post type.
 *
 * This function creates a JSON file for a post type.
 * The JSON file contains the ID, post type, title, and URL of each post.
 *
 * @param string $post_type The post type to create the JSON file for.
 * @return void
 * @since 0.0.1
 */
if (!function_exists('hintr_create_json')) {
  function hintr_create_json($post_type) {
    $posts_data = [];
    $posts = get_posts([
      'post_status' => 'publish',
      'post_type' => $post_type,
      'posts_per_page' => -1
    ]);
    foreach($posts as $post) {
      $posts_data[$post->ID] = [
        'post_type' => $post->post_type,
        'title' => $post->post_title,
        'url' => get_permalink($post)
      ];
    }
    file_put_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post_type . '.json', json_encode($posts_data));
  }
}

/**
 * Update the JSON file for a post.
 *
 * This function updates the JSON file for a post.
 * The JSON file contains the ID, post type, title, and URL of each post.
 *
 * @param WP_Post $post The post to update the JSON file for.
 * @return void
 * @since 0.0.1
 */
if (!function_exists('hintr_update_json_post')) {
  function hintr_update_json_post($post) {
    $posts = json_decode(file_get_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post->post_type . '.json'), true);
    $posts[$post->ID]['title'] = $post->post_title;
    $posts[$post->ID]['url'] = get_permalink($post);

    file_put_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post->post_type . '.json', json_encode($posts));
  }
}

/**
 * Delete the JSON file for a post.
 *
 * This function deletes the JSON file for a post.
 * The JSON file contains the ID, post type, title, and URL of each post.
 *
 * @param WP_Post $post The post to delete the JSON file for.
 * @return void
 * @since 0.0.1
 */
if (!function_exists('hintr_delete_json_post')) {
  function hintr_delete_json_post($post) {
    $posts = json_decode(file_get_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post->post_type . '.json'), true);
    unset($posts[$post->ID]);

    file_put_contents(ABSPATH . 'wp-content/uploads/hintr/' . $post->post_type . '.json', json_encode($posts));
  }
}

/**
 * Generates an HTML template for a search suggestion item.
 *
 * This function outputs an `<li>` element containing an anchor `<a>`
 * element formatted for displaying search suggestions. The function
 * uses output buffering to return the generated HTML as a string.
 *
 * @return string The HTML structure for a search suggestion item.
 */
if (!function_exists('hintr_hint_template')) {
  function hintr_hint_template() {
    ob_start(); ?>
    <li><a class="hintr-nav-item" href="url">title</a></li>
    <?php return ob_get_clean();
  }
}

/**
 * Enqueue the plugin scripts and styles.
 *
 * This function is called when WordPress enqueues scripts and styles.
 * Enqueue the hintr.css and hintr.js files.
 *
 * @return void
 *
 * @since 0.0.1
 */
if (!function_exists('hintr_enqueue_scripts')) {
  add_action('wp_enqueue_scripts', 'hintr_enqueue_scripts');
  function hintr_enqueue_scripts() {

    $plugin_path = plugin_dir_path(dirname(__FILE__));
    $plugin_url = plugin_dir_url(dirname(__FILE__));

    wp_enqueue_style('hintr', $plugin_url . 'assets/css/hintr-public.css', [], filemtime($plugin_path . 'assets/css/hintr-public.css'));
    wp_enqueue_script('hintr', $plugin_url . 'assets/js/hintr-public.js', ['jquery'], filemtime($plugin_path . 'assets/js/hintr-public.js'), true);
    wp_localize_script('hintr', 'hintrData', [
      'uploadDir' => wp_upload_dir()['baseurl'] . '/hintr/',
      'hint' => hintr_hint_template()
    ]);
  }
}

/**
 * Save the post.
 *
 * This function is called when a post is saved.
 * If the post status is publish, update the JSON file for the post.
 * If the post status is not publish, delete the JSON file for the post.
 *
 * @param int $post_id The ID of the post.
 * @param WP_Post $post The post object.
 * @return void
 *
 * @since 0.0.1
 */
if (!function_exists('hintr_save_post')) {
  add_action('save_post', 'hintr_save_post', 10, 2);
  function hintr_save_post($post_id, $post) {

    $validation = [
      defined('DOING_AUTOSAVE') && DOING_AUTOSAVE,
      $post->post_type === 'revision'
    ];

    if (in_array(false, $validation)) {
      return;
    }

    if ($post->post_status === 'publish') {
      hintr_update_json_post($post);
    } else {
      hintr_delete_json_post($post);
    }
  }
}

/**
 * Delete the post.
 *
 * This function is called when a post is deleted.
 * Delete the JSON file for the post.
 *
 * @param WP_Post $post The post object.
 * @return void
 *
 * @since 0.0.1
 */
if (!function_exists('hintr_delete_post')) {
  add_action('delete_post', 'hintr_delete_post');
  function hintr_delete_post($post) {
    hintr_delete_json_post($post);
  }
}