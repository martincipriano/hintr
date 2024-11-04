<?php

// Create an options page for the plugin.

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
  }
}


// Validate the settings.
function hintr_settings_validate($input) {
  return $input;
}

if (!function_exists('hintr_settings_default_post_types')) {
  function hintr_settings_default_post_types($args) {
    $hintr_settings = get_option('hintr_settings');

    $available_post_types = get_post_types(['exclude_from_search' => false], 'object');
    $default_post_types = explode(',', $hintr_settings['default_post_types']);

    if (!$default_post_types) {
      $default_post_types = ['post', 'page'];
    } ?>

    <select name="hintr_settings[default_post_types]">
      <?php foreach ($available_post_types as $post_type) { ?>
        <option value="<?= $post_type->name ?>" <?php selected(in_array($post_type->name, $default_post_types), true); ?>><?= $post_type->label ?></option>
      <?php } ?>
    </select>
    <p class="description" id="new-admin-email-description"><?= $args['description'] ?></p>
  <?php }
}