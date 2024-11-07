<?php

if (!function_exists('hintr_admin_enqueue_scripts')) {
  add_action('admin_enqueue_scripts', 'hintr_admin_enqueue_scripts');
  function hintr_admin_enqueue_scripts() {
    $plugin_path = plugin_dir_path(dirname(__FILE__));
    $plugin_url = plugin_dir_url(dirname(__FILE__));

    wp_enqueue_style('slim-select', $plugin_url . 'assets/css/slimselect.min.css', [], '2.9.2');
    wp_enqueue_style('hintr-admin', $plugin_url . 'assets/css/hintr-admin.css', [], filemtime($plugin_path . 'assets/css/hintr-admin.css'));

    wp_enqueue_script('slim-select', $plugin_url . 'assets/js/slimselect.min.js', [], '2.9.2', true);
    wp_enqueue_script('hintr-admin', $plugin_url . 'assets/js/hintr-admin.js', ['slim-select'], filemtime($plugin_path . 'assets/js/hintr-admin.js'), true);
  }
}

if (!function_exists('hintr_get_post_type_metadata')) {
  function hintr_get_post_type_metadata($post_type) {
    global $wpdb;
    $meta_keys = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT pm.meta_key FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.post_type = %s", $post_type));
    return $meta_keys;
  }
}

if (!function_exists('hintr_create_settings')) {
  add_action('admin_menu', 'hintr_create_settings');
  function hintr_create_settings() {
    add_options_page('Hintr Options', 'Hintr', 'manage_options', 'hintr', 'hintr_settings');
  }
}

if (!function_exists('hintr_settings')) {
  function hintr_settings() { ?>
    <div class="wrap">
      <h2>Hintr Options</h2>
      <form action="options.php" method="post">
        <?php
          settings_fields('hintr_settings');
          do_settings_sections('hintr');
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }
}

if (!function_exists('hintr_register_settings')) {
  add_action('admin_init', 'hintr_register_settings');
  function hintr_register_settings() {
    register_setting('hintr_settings', 'hintr_settings', 'hintr_settings_validate');
    add_settings_section('hintr_settings', '', '', 'hintr');
    add_settings_field('hintr_default_post_types', 'Post Types', 'hintr_settings_default_post_types', 'hintr', 'hintr_settings', [
      'description' => 'Select the default post types from which suggestions will be sourced.'
    ]);
    add_settings_field('hintr_default_post_metadata', 'Post Metadata', 'hintr_settings_default_post_metadata', 'hintr', 'hintr_settings', [
      'description' => 'Select the default post metadata from which suggestions will be sourced.'
    ]);
  }
}

if (!function_exists('hintr_settings_validate')) {
  function hintr_settings_validate($input) {
    return $input;
  }
}

if (!function_exists('hintr_settings_default_post_types')) {
  function hintr_settings_default_post_types($args) {
    $available_post_types = get_post_types(['exclude_from_search' => false], 'object');
    $default_post_types = ['post', 'page'];

    $hintr_settings = get_option('hintr_settings');
    if ($hintr_settings) {
      $default_post_types = $hintr_settings['default_post_types'] ?? [];
    } ?>

    <select id="hintr-default-post-types" name="hintr_settings[default_post_types][]" multiple>
      <?php foreach ($available_post_types as $post_type) { ?>
        <option value="<?= $post_type->name ?>" <?php selected(in_array($post_type->name, $default_post_types), true); ?>><?= $post_type->label ?></option>
      <?php } ?>
    </select>
    <p class="description" id="new-admin-email-description"><?= $args['description'] ?></p>
  <?php }
}

if (!function_exists('hintr_settings_default_post_metadata')) {
  function hintr_settings_default_post_metadata($args) {
    $available_post_types = get_post_types(['exclude_from_search' => false], 'object');
    $default_post_types = ['post', 'page'];

    $hintr_settings = get_option('hintr_settings');
    if ($hintr_settings) {
      $default_post_types = $hintr_settings['default_post_types'] ?? [];
    }

    foreach($default_post_types as $post_type) {
      $meta_keys = hintr_get_post_type_metadata($post_type);
      if ($meta_keys) {
        foreach ($meta_keys as $meta_key) { ?>
          <label class="hintr-checkbox" for="<?= $post_type . '-' . $meta_key ?>"><input id="<?= $post_type . '-' . $meta_key ?>" type="checkbox" value="<?= $meta_key ?>"><?= $meta_key ?></label>
        <?php }
      } else {
        echo 'No meta keys found for this post type.';
      }
    } ?>

    <!--select name="hintr_settings[default_post_types]">
      <?php foreach ($available_post_types as $post_type) { ?>
        <option value="<?= $post_type->name ?>" <?php selected(in_array($post_type->name, $default_post_types), true); ?>><?= $post_type->label ?></option>
      <?php } ?>
    </select-->
    <p class="description" id="new-admin-email-description"><?= $args['description'] ?></p>
  <?php }
}
