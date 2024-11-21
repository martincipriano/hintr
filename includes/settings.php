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
      <form method="post" action="options.php">
        <?php
          settings_fields('hintr_settings');
          do_settings_sections('hintr');
          submit_button('Save Settings');
        ?>
      </form>
    </div>
    <?php
  }
}

new Hintr_Settings;