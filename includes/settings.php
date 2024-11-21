<?php

class Hintr_Settings extends Hintr_Admin {

  public function __construct()
  {
    add_action('admin_menu', [$this, 'create_settings_page']);
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

  public function register_settings_page() : void
  {
    $page       = 'hintr'; // Should be the same as the slug used in add_options_page
    $section_id = 'hintr_settings_section';

    $option_group         = 'hintr_settings';
    $option_name          = 'hintr_settings';
    $validation_callback  = [$this, 'validate_settings'];

    register_setting($option_group, $option_name, $validation_callback);

    $section_title  = _e('Search Keywords In', 'hintr');
    $section_cb     = _e('', 'hintr');

    add_settings_section($section_id, $section_title, $section_cb, $page);

    $field_id     = 'hintr_post_types';
    $field_title  = _e('Post Types', 'hintr');
    $field_cb     = [$this, 'post_type_field'];
    $args         = ['description' => _e('Select the default post types from which suggestions will be sourced.', 'hintr')];

    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $field_id     = 'hintr_metadata';
    $field_title  = _e('Post Metadata', 'hintr');
    $field_cb     = [$this, 'metadata_field'];
    $args         = ['description' => _e('Select the default post metadata from which suggestions will be sourced.', 'hintr')];

    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $field_id     = 'hintr_reindex';
    $field_title  = _e('Re-index Post Types', 'hintr');
    $field_cb     = [$this, 'reindex_field'];
    $args         = [
      'description' => _e('Trigger a rebuild of the JSON data files used for search indexing.', 'hintr'),
      'note'        => _e('Rebuilding the JSON files may take time depending on the number of posts and post metadata.', 'hintr')
    ];

    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);
  }

  public function post_type_field() : void
  {
    // Get the plugin settings
    $selected = array_keys($this->plugin_settings['search_in'] ?? []);
  }
}

new Hintr_Settings;