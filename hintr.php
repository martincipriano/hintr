<?php
/**
 * Plugin Name: Hintr
 * Description: A plugin that adds search suggestions to the search form.
 * Author: Martin Cipriano
 * Version: 0.0.1
 */

if (is_admin()) {
  require_once plugin_dir_path(__FILE__) . 'inc/admin.php';
} else {
  require_once plugin_dir_path(__FILE__) . 'inc/public.php';
}
