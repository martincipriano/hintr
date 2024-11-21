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
}

new Hintr_Settings;