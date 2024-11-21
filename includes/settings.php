<?php

class Hintr_Settings {
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
    $option_group = 'hintr_settings';
    $option_name = 'hintr_settings';
    $validation_callback = [$this, 'validate_settings'];

    register_setting($option_group, $option_name, $validation_callback);

    $section_id = 'hintr_settings_page';
    $title = _e('Search Keywords In', 'hintr');
    $callback = '';
    $page = 'hintr'; // Should be the same as the slug used in add_options_page

    add_settings_section($section_id, $title, $callback, $page);
  }
}

new Hintr_Settings;