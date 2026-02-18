<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

// CI: deploy on push to develop (staging) / main (production)
// Make theme available for translation
// Community translations can be found at https://github.com/roots/sage-translations
load_theme_textdomain('presspack', get_template_directory() . '/lang');

// functions.php is empty so you can easily track what code is needed in order to Vite + Tailwind JIT run well
$theme_dir = get_template_directory();
require_once $theme_dir . '/lib/helpers.php';
require_once $theme_dir . '/init/timber.php';

// init admin panel
require_once $theme_dir . '/init/custom_admin_panel.php';

// Init scripts
require_once $theme_dir . '/init/custom_post_types.php';
require_once $theme_dir . '/init/custom_fields.php';
require_once $theme_dir . '/init/hooks.php';
require_once $theme_dir . '/init/login_template.php';
require_once $theme_dir . '/init/thumbnail_sizes.php';
require_once $theme_dir . '/init/menus.php';
require_once $theme_dir . '/init/shortcodes.php';
require_once $theme_dir . '/init/acf_blocks.php';
require_once $theme_dir . '/init/structured-data.php';

// Block REST API user enumeration (security)
add_filter('rest_endpoints', function ($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\\d]+)']);
    }
    return $endpoints;
});

// Main switch to get frontend assets from a Vite dev server OR from production built folder
// it is recommended to move it into wp-config.php
if (defined('WP_ENVIRONMENT_TYPE')) {
  define('IS_VITE_DEVELOPMENT', WP_ENVIRONMENT_TYPE === 'local');
} else {
  define('IS_VITE_DEVELOPMENT', false);
}

include $theme_dir . '/init/vite.php';
