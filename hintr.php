<?php
/**
 * Plugin Name: Hintr - Lightning Fast Search Suggestions
 * Description: A plugin that enhances WordPress search by providing a dropdown of search suggestions with minimal loading time for improved user experience.
 * Author: Jose Martin Cipriano
 * Version: 1.0.0
 * Author URI: https://www.linkedin.com/in/jmcipriano
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

class Hintr {
  private $plugin_path;
  private $plugin_url;
  private $plugin_settings;

  public function __construct() {
    $this->plugin_path = plugin_dir_path(__FILE__);
    $this->plugin_url = plugin_dir_url(__FILE__);
    $this->plugin_settings = get_option('hintr_settings');
  }
}

new Hintr;