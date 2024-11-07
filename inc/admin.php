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
    add_settings_section('hintr_settings', 'Search Keywords In', '', 'hintr');
    add_settings_field('hintr_post_types', 'Post Types', 'hintr_settings_post_types', 'hintr', 'hintr_settings', [
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

if (!function_exists('hintr_settings_post_types')) {
  function hintr_settings_post_types($args) {
    $available_post_types = get_post_types(['public' => true], 'objects');
    $hintr_settings = get_option('hintr_settings');
    $selected_post_types = $hintr_settings ? $hintr_settings['post_types'] : ['post', 'page']; ?>

    <select id="hintr-default-post-types" name="hintr_settings[post_types][]" multiple>
      <?php foreach ($available_post_types as $post_type): ?>
        <?php if (isset($post_type)): ?>
          <option value="<?= $post_type->name ?>" <?php selected(in_array($post_type->name, $selected_post_types), true); ?>><?= $post_type->label ?></option>
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
    <p class="description" id="new-admin-email-description"><?= $args['description'] ?></p>
  <?php }
}

if (!function_exists('hintr_settings_default_post_metadata')) {
  function hintr_settings_default_post_metadata($args) {
    $hintr_settings = get_option('hintr_settings');
    $selected_post_types = $hintr_settings ? $hintr_settings['post_types'] : ['post', 'page'];

    foreach($selected_post_types as $post_type) {
      if (post_type_exists($post_type)) {
        $post_type_object = get_post_type_object($post_type);


        $meta_keys = hintr_get_post_type_metadata($post_type);



        $checked_meta_keys = $hintr_settings['search_in'][$post_type] ?? []; ?>

        <div class="hintr-form-group">
          <p class="hintr-label"><?= $post_type_object->label ?></p>
          <?php if ($meta_keys): ?>
            <div class="hintr-checkboxes">
              <?php foreach ($meta_keys as $meta_key): ?>
                <label class="hintr-checkbox" for="<?= $post_type . '-' . $meta_key ?>">
                  <input <?php checked(in_array($meta_key, $checked_meta_keys), true) ?> id="<?= $post_type . '-' . $meta_key ?>" name="hintr_settings[search_in][<?= $post_type ?>][]" type="checkbox" value="<?= $meta_key ?>">
                  <?= $meta_key ?>
                </label>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            No meta keys found for this post type.
          <?php endif; ?>
        </div>

      <?php }
    } ?>
  <?php }
}
