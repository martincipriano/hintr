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
  private $plugin_uploads_url;
  private $plugin_settings;

  public function __construct() {
    $this->plugin_path = plugin_dir_path(__FILE__);
    $this->plugin_url = plugin_dir_url(__FILE__);
    $this->plugin_uploads_url = wp_upload_dir()['baseurl'] . '/hintr/';
    $this->plugin_settings = get_option('hintr_settings');
  }

  public function enqueue_scripts() : void
  {
    wp_enqueue_style('hintr', $this->plugin_url . 'assets/css/hintr.css', [], filemtime($this->plugin_path . 'assets/css/hintr.css'), 'all');
    wp_enqueue_script('hintr', $this->plugin_url . 'assets/js/hintr.js', [], filemtime($this->plugin_path . 'assets/js/hintr.js'), true);

    wp_localize_script('hintr', 'hintr', [
      'uploads_url' => $this->plugin_uploads_url,
      'search_in' => $this->plugin_settings
    ]);
  }
}

new Hintr;