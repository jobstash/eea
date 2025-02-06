<?php

//==== Custom admin panel ====

function modify_footer_admin()
{
  // TODO change this text
  echo 'Website done by ...';
}

add_filter('admin_footer_text', 'modify_footer_admin');


// Disable admin bar
// show_admin_bar(false);

function remove_admin_bar_links()
{
  global $wp_admin_bar;
  $elements = array('wp-logo', 'about', 'wporg', 'documentation', 'support-forums', 'feedback', 'updates', 'comments', 'new-content');
  foreach ($elements as $element) {
    $wp_admin_bar->remove_menu($element);
  }
}

add_action('wp_before_admin_bar_render', 'remove_admin_bar_links');

// Rimuove elementi dalla bacheca
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_dashboard_widgets()
{
  global $wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_welcome']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  // unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);

  remove_action('welcome_panel', 'wp_welcome_panel');
}

/*************************/

$current_user = wp_get_current_user();


if ($current_user->user_login != 'piramid' && $current_user->user_login != 'admin') {
  //Rimuove elementi del menu laterale
  add_action('admin_menu', 'remove_menu_items');

  //Rimuove box dei post
  add_action('admin_menu', 'customize_meta_boxes');

  add_action('admin_head', 'remove_contact_form');
  add_action('admin_head', 'remove_stuff');
  add_action('admin_head', 'remove_theme_options');
  //rimuove i widget
  add_action('widgets_init', 'unregister_default_wp_widgets', 1);

  //Rimuove avviso di update
  // add_action('admin_menu', 'wphidenag');
}

//Rimuove elementi del menu
function remove_menu_items()
{

  global $menu;
  $restricted = array(__('Plugins'), __('Tools'), __('Settings'), __('Link'));

  end($menu);
  while (prev($menu)) {
    $value = explode(' ', $menu[key($menu)][0]);
    if (in_array($value[0] != NULL ? $value[0] : "", $restricted)) {
      unset($menu[key($menu)]);
    }
  }
}

//Rimuove box dei post
function customize_meta_boxes()
{
  /* Removes meta boxes from Posts */
  remove_meta_box('postcustom', 'post', 'normal');
  remove_meta_box('postimagediv', 'post', 'normal');
  remove_meta_box('categorydiv', 'post', 'normal');
  remove_meta_box('trackbacksdiv', 'post', 'normal');
  remove_meta_box('commentstatusdiv', 'post', 'normal');
  remove_meta_box('commentsdiv', 'post', 'normal');
  remove_meta_box('tagsdiv-post_tag', 'post', 'normal');
  remove_meta_box('postexcerpt', 'post', 'normal');
  /* Removes meta boxes from pages */
  remove_meta_box('postcustom', 'page', 'normal');
  remove_meta_box('trackbacksdiv', 'page', 'normal');
  remove_meta_box('commentstatusdiv', 'page', 'normal');
  remove_meta_box('commentsdiv', 'page', 'normal');
}

//rimuovo i widget non utilizzati
function unregister_default_wp_widgets()
{
  unregister_widget('WP_Widget_Pages');
  unregister_widget('WP_Widget_Calendar');
  unregister_widget('WP_Widget_Archives');
  unregister_widget('WP_Widget_Links');
  unregister_widget('WP_Widget_Meta');
  unregister_widget('WP_Widget_Search');
  unregister_widget('WP_Widget_Categories');
  unregister_widget('WP_Widget_Recent_Comments');
  unregister_widget('WP_Widget_Tag_Cloud');
  unregister_widget('WP_Widget_RSS');
  unregister_widget('WP_Widget_Akismet');
}

// Modifico posizione ed etichette delle tab
// Modifico posizione ed etichette delle tab
function wpse_custom_menu_order($menu_ord)
{
  if (!$menu_ord) return true;

  return array(
    'index.php', // Dashboard
    'separator1', // First separator
    'edit.php?post_type=page', // Pages
    'edit.php', // Insights
    'edit.php?post_type=case_study', // Case studies
    'edit.php?post_type=team_member', // Team Members
    'edit.php?post_type=office', // Offices
    'edit.php?post_type=job', // Jobs
    'edit.php?post_type=service', // Jobs
    'edit.php?post_type=audience', // Jobs
    'upload.php', // Media
    'separator2', // Second separator
    'themes.php', // Appearance
    'plugins.php', // Plugins
    'users.php', // Users
    'tools.php', // Tools
    'options-general.php', // Settings
    'separator-last', // Last separator
  );
}
add_filter('custom_menu_order', 'wpse_custom_menu_order', 10, 1);
add_filter('menu_order', 'wpse_custom_menu_order', 10, 1);


function remove_contact_form()
{
  echo "<style type='text/css' media='screen'>#toplevel_page_wpcf7{display:none;}</style>";
}

function remove_stuff()
{
  echo "<style type='text/css' media='screen'>
  #toplevel_page_edit-post_type-acf-field-group, #toplevel_page_meowapps-main-menu, #toplevel_page_itsec, #wp-admin-bar-itsec_admin_bar_menu{display:none;}</style>";
}


function remove_theme_options()
{
  echo "<style type='text/css' media='screen'>#toplevel_page_studioepthemes{display:none;}</style>";
}
