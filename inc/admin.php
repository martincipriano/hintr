<?php

// Create an options page for the plugin.

if (!function_exists('hntr_create_options')) {
  add_action('admin_menu', 'hntr_create_options');
  function hntr_create_options() {
    add_options_page('Hntr Options', 'Hntr', 'manage_options', 'hntr', 'hntr_options');
  }
}

if (!function_exists('hntr_options')) {
  function hntr_options() { ?>
    <div class="wrap">
      <h2>Hntr Options</h2>
      <form action="options.php" method="post">
        <?php
          settings_fields('hntr_options');
          do_settings_sections('hntr');
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }
}
