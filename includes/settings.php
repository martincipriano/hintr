<?php

if (!defined('ABSPATH')) {
  exit;
}

class Hintr_Settings extends Hintr_Admin {

  public function __construct()
  {
    parent::__construct();
    add_action('admin_menu', [$this, 'create_settings_page']);
    add_action('admin_init', [$this, 'register_settings_page']);
  }

  public function create_settings_page() : void
  {
    $page_title = 'Hintr Options';
    $menu_title = 'Hintr';
    $capability = 'manage_options';
    $slug       = 'hintr';
    $callback   = [$this, 'settings_page'];

    add_options_page($page_title, $menu_title, $capability, $slug, $callback);
  }

  public function register_settings_page() : void
  {
    $page       = 'hintr'; // Should be the same as the slug used in add_options_page
    $section_id = 'hintr_settings_section';

    $option_group         = 'hintr_settings';
    $option_name          = 'hintr_settings';
    $validation_callback  = [$this, 'validate_settings'];

    register_setting($option_group, $option_name, $validation_callback);

    $section_title  = 'Search Keywords In';
    $section_cb     = '';

    add_settings_section($section_id, $section_title, $section_cb, $page);

    $field_id     = 'hintr_post_types';
    $field_title  = 'Post Types';
    $field_cb     = [$this, 'post_type_field'];
    $args         = ['description' => 'Select the default post types from which suggestions will be sourced.'];

    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $field_id     = 'hintr_metadata';
    $field_title  = 'Post Metadata';
    $field_cb     = [$this, 'metadata_field'];
    $args         = ['description' => 'Select the default post metadata from which suggestions will be sourced.'];

    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);
  }

  public function settings_page() : void
  {
    ?>
    <div class="wrap">
      <h1><?= _e('Hintr Options', 'hintr'); ?></h1>
      <form action="options.php" method="post">
        <?php
          settings_fields('hintr_settings');
          do_settings_sections('hintr');
          submit_button('Save Settings');
        ?>
      </form>
    </div>
    <?php
  }

  public function post_type_field($args) : void
  {
    $selected_post_types = array_keys($this->plugin_settings['search_in'] ?? []);
    $public_post_types = get_post_types(['public' => true], 'objects');
    $excluded_post_types = ['revision', 'wp_global_styles', 'wp_navigation'];

    $public_post_types = array_filter($public_post_types, function($post_type) use ($excluded_post_types) {
      return !in_array($post_type->name, $excluded_post_types);
    }); ?>

    <div class="hintr-form-group">
      <select class="hintr-select" id="hintr-post-types" name="hintr_settings[post_types][]" multiple>
        <?php foreach ($public_post_types as $post_type): ?>
          <option value="<?= $post_type->name ?>" <?php selected(in_array($post_type->name, $selected_post_types), true); ?>>
            <?= $post_type->label ?>
          </option>
        <?php endforeach; ?>
      </select>
      <p class="description"><?= $args['description'] ?></p>
    </div>
  <?php }

  public function metadata_field() : void
  {
    $selected_post_types = array_keys($this->plugin_settings['search_in'] ?? []);

    foreach ($selected_post_types as $post_type) {
      if (post_type_exists($post_type)) {

        $post_type_object   = get_post_type_object($post_type);
        $meta_keys          = $this->get_meta_keys($post_type);
        $selected_meta_keys = $this->plugin_settings['search_in'][$post_type] ?? [];
        $input_id           = 'hintr-' . $post_type . '-metadata'; ?>

        <?php if ($meta_keys): ?>
          <div class="hintr-form-group">
            <label for="<?= $input_id ?>"><?= $post_type_object->label ?></label>
            <select class="hintr-select" id="<?= $input_id ?>" name="hintr_settings[meta_keys][<?= $post_type ?>][]" multiple>
              <?php foreach ($meta_keys as $meta_key): ?>
                <option value="<?= $meta_key ?>" <?php selected(in_array($meta_key, $selected_meta_keys), true); ?>>
                  <?= $meta_key ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php else: ?>
          No available meta keys found for this post type.
        <?php endif;
      }
    }
  }

  public function validate_settings($input) : array
  {
    $post_types = $input['post_types'] ?? [];
    $meta_keys = $input['meta_keys'] ?? [];
    $output = [
      'search_in' => []
    ];

    // This first loop might look redundant,
    // but it's necessary to ensure that the post types is set
    // if there were no metadata keys selected for that post type
    foreach($post_types as $post_type) {
      $output['search_in'][$post_type] = [];
    }

    // Now add the metadata keys to the post types
    foreach($meta_keys as $post_type => $meta_keys) {
      if (isset($output['search_in'][$post_type])) {
        $output['search_in'][$post_type] = $input['meta_keys'][$post_type];
      }
      
    }

    return $output;
  }
}

new Hintr_Settings;
