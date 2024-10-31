<?php

/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

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
    wp_enqueue_style('hintr', plugin_dir_url(__FILE__) . 'hintr.css', [], filemtime(plugin_dir_path(__FILE__) . 'hintr.css'));
    wp_enqueue_script('hintr', plugin_dir_url(__FILE__) . 'hintr.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'hintr.js'), true);
  }
}