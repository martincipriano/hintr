<?php
/**
 * Plugin Name: Hintr
 * Description: A plugin that enhances WordPress search by providing a dropdown of search suggestions with minimal loading time for improved user experience.
 * Author: Jose Martin Cipriano
 * Version: 1.1.5
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author URI: https://www.linkedin.com/in/jmcipriano
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('ABSPATH')) {
  exit;
}

include_once plugin_dir_path(__FILE__) . 'includes/frontend.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin.php';
include_once plugin_dir_path(__FILE__) . 'includes/settings.php';
