<?php

// Create an options page for the plugin.

if (!function_exists('hntr_create_settings')) {
  add_action('admin_menu', 'hntr_create_settings');
  function hntr_create_settings() {
    add_options_page('Hntr Options', 'Hntr', 'manage_options', 'hntr', 'hntr_settings');
  }
}

if (!function_exists('hntr_settings')) {
  function hntr_settings() { ?>
    <div class="wrap">
      <h2>Hntr Options</h2>
      <form action="options.php" method="post">
        <?php
          settings_fields('hntr_settings');
          do_settings_sections('hntr');
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }
}
