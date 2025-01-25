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
    add_action('admin_init', [$this, 'after_settings_saved']);
    add_action('save_post', [$this, 'after_post_saved'], 10, 2);
  }

  public function create_settings_page() : void
  {
    $page_title = __('Hintr Options', 'hintr');
    $menu_title = __('Hintr', 'hintr');
    $capability = 'manage_options';
    $slug       = 'hintr';
    $callback   = [$this, 'settings_page'];

    add_options_page($page_title, $menu_title, $capability, $slug, $callback);
  }

  public function register_settings_page() : void
  {
    $page = 'hintr';

    $option_group         = 'hintr_settings';
    $option_name          = 'hintr_settings';
    $validation_callback  = [$this, 'validate_settings'];
    register_setting($option_group, $option_name, $validation_callback);

    $section_id     = 'hintr_search_in';
    $section_title  = __('Search Keywords In', 'hintr');
    $section_cb     = '';
    add_settings_section($section_id, $section_title, $section_cb, $page);

    $field_id     = 'hintr_post_types';
    $field_title  = __('Post Types', 'hintr');
    $field_cb     = [$this, 'post_type_field'];
    $args         = ['description' => __('Select the default post types from which suggestions will be sourced.', 'hintr')];
    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $field_id     = 'hintr_metadata';
    $field_title  = __('Post Metadata', 'hintr');
    $field_cb     = [$this, 'metadata_field'];
    $args         = ['description' => __('Select the default post metadata from which suggestions will be sourced.', 'hintr')];
    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $section_id     = 'hintr_other_settings';
    $section_title  = __('Other Settings', 'hintr');
    $section_cb     = '';
    add_settings_section('hintr_other_settings', $section_title, $section_cb, $page);

    $field_id     = 'hintr_count';
    $field_title  = __('Suggestion Count', 'hintr');
    $field_cb     = [$this, 'count_field'];
    $args         = ['description' => __('The number of suggestions to display.', 'hintr')];
    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);

    $field_id     = 'hintr_auto';
    $field_title  = __('Auto Apply', 'hintr');
    $field_cb     = [$this, 'auto_field'];
    $args         = ['description' => __('Use suggestions with the default search input.', 'hintr')];
    add_settings_field($field_id, $field_title, $field_cb, $page, $section_id, $args);
  }

  public function settings_page() : void
  {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html_e('Hintr Options', 'hintr'); ?></h1>
      <form action="options.php" method="post">
        <?php
          settings_fields('hintr_settings');
          do_settings_sections('hintr');
          submit_button(__('Save Settings', 'hintr'));
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
          <option value="<?php echo esc_attr($post_type->name) ?>" <?php selected(in_array($post_type->name, $selected_post_types), true); ?>>
            <?php echo esc_html($post_type->label) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <p class="description"><?php echo esc_html($args['description']) ?></p>
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
        $input_id           = 'hintr-' . sanitize_key($post_type) . '-metadata'; ?>

        <?php if ($meta_keys): ?>
          <div class="hintr-form-group hintr-metadata-group">
            <label for="<?php echo esc_attr($input_id) ?>"><?php echo esc_html($post_type_object->label) ?></label>
            <select class="hintr-select" id="<?php echo esc_attr($input_id) ?>" name="hintr_settings[meta_keys][<?php echo esc_attr($post_type) ?>][]" multiple>
              <?php foreach ($meta_keys as $meta_key): ?>
                <option value="<?php echo esc_attr($meta_key) ?>" <?php selected(in_array($meta_key, $selected_meta_keys), true); ?>>
                  <?php echo esc_html($meta_key) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif;
      }
    }
  }

  public function count_field($args) : void
  {
    $count = $this->plugin_settings['count'] ?? 10; ?>

    <div class="hintr-form-group">
      <input type="number" class="hintr-input" id="hintr-count" name="hintr_settings[count]" value="<?php echo esc_attr($count) ?>">
      <p class="description"><?php echo esc_html($args['description']) ?></p>
    </div>
  <?php }

  public function auto_field($args) : void
  {
    $auto = $this->plugin_settings['auto'] ?>
    <div class="hintr-form-group">
      <input <?php checked($auto, true); ?> type="checkbox" class="hintr-input" id="hintr-auto" name="hintr_settings[auto]" value="true">
      <?php echo esc_html($args['description']); ?>
      <p class="description" style="max-width: 767px;"><strong>Note</strong>: Integrating auto-suggestions with animated inputs can lead to layout issues, so customizing the design of the suggestions dropdown is crucial to ensure a seamless user experience.</p>
    </div>
  <?php }

  public function validate_settings($input) : array
  {
    $post_types = $input['post_types'] ?? [];
    $meta_keys = $input['meta_keys'] ?? [];
    $count = $input['count'] ?? 10;
    $auto = ($input['auto'] === 'true' ? true : false) ?? false;

    $output = [
      'auto' => $auto,
      'count' => $count,
      'search_in' => []
    ];

    // This first loop might look redundant,
    // but it's necessary to ensure that the post types is set
    // if there were no metadata keys selected for that post type
    foreach($post_types as $post_type) {
      $output['search_in'][sanitize_key($post_type)] = [];
    }

    // Now add the metadata keys to the post types
    foreach($meta_keys as $post_type => $meta_keys) {
      if (isset($output['search_in'][sanitize_key($post_type)])) {
        $output['search_in'][sanitize_key($post_type)] = $input['meta_keys'][sanitize_key($post_type)];
      }
    }

    return $output;
  }

  public function after_settings_saved() : void
  {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
      update_option('hintr_last_updated', time());
      $this->create_json_file();
    }
  }

  public function after_post_saved($post_id, $post) : void
  {
    $selected_post_types = array_keys($this->plugin_settings['search_in'] ?? []);
    if (
      (!wp_is_post_revision($post_id) && !wp_is_post_autosave($post_id)) &&
      in_array($post->post_type, $selected_post_types)
    ) {
      update_option('hintr_last_updated', time());
      $this->create_json_file();
    }
  }
}

new Hintr_Settings;
