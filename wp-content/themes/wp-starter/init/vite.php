<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

/*
 * VITE & Tailwind JIT development
 * Inspired by https://github.com/andrefelipe/vite-php-setup
 *
 */

// dist subfolder - defined in vite.config.json
define('DIST_DEF', 'dist');

// defining some base urls and paths
define('DIST_URI', get_template_directory_uri() . '/' . DIST_DEF);
define('DIST_PATH', get_template_directory() . '/' . DIST_DEF);

// js enqueue settings
define('JS_DEPENDENCY', array()); // array('jquery') as example
define('JS_LOAD_IN_FOOTER', true); // load scripts in footer?

// Default server address; NODE_HOST/NODE_PORT must be reachable by the browser (e.g. localhost:3000)
$vite_host = getenv('NODE_HOST') ?: 'localhost';
$vite_port = getenv('NODE_PORT') ?: '3000';
define('VITE_SERVER', 'http://' . $vite_host . ':' . $vite_port);

// Must match vite.config.js base in dev: /wp-content/themes/wp-starter/
define('VITE_ENTRY_POINT', '/wp-content/themes/wp-starter/main.js');

function enqueue_vite_scripts()
{

  if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {

    // Load Vite script in footer so the page paints first and doesn't stall waiting for dev server
    function vite_footer_module_hook()
    {
      echo '<script type="module" crossorigin src="' . VITE_SERVER . VITE_ENTRY_POINT . '" defer></script>';
    }
    add_action('wp_footer', 'vite_footer_module_hook', 5);
  } else {

    // production version, 'npm run build' must be executed in order to generate assets
    $manifest_path = DIST_PATH . '/manifest.json';
    if (!is_file($manifest_path)) {
      return;
    }
    $manifest = json_decode(file_get_contents($manifest_path), true);

    if (is_array($manifest)) {

      $manifest_key = array_keys($manifest);
      $key = array_search('main.js', $manifest_key);

      if ($key !== false) {

        foreach (@$manifest[$manifest_key[$key]]['css'] as $css_file) {
          wp_enqueue_style('main', DIST_URI . '/' . $css_file);
        }

        $js_file = @$manifest[$manifest_key[$key]]['file'];
        if (!empty($js_file)) {
          wp_enqueue_script('main', DIST_URI . '/' . $js_file, JS_DEPENDENCY, '', JS_LOAD_IN_FOOTER);
        }
      }
    }
  }
}

// enqueue hook
add_action('wp_enqueue_scripts', 'enqueue_vite_scripts');

add_action('enqueue_block_editor_assets', function () {

  $manifest_path = DIST_PATH . '/manifest.json';
  if (!is_file($manifest_path)) {
    return;
  }
  $manifest = json_decode(file_get_contents($manifest_path), true);

  if (is_array($manifest)) {

    $manifest_key = array_keys($manifest);
    $key = array_search('main.js', $manifest_key);

    if ($key !== false) {

      foreach (@$manifest[$manifest_key[$key]]['css'] as $css_file) {
        wp_enqueue_style('main', DIST_URI . '/' . $css_file);
      }

      $js_file = @$manifest[$manifest_key[$key]]['file'];
      if (!empty($js_file)) {
        wp_enqueue_script('main', DIST_URI . '/' . $js_file, JS_DEPENDENCY, '', JS_LOAD_IN_FOOTER);
      }
    }
  }
});
