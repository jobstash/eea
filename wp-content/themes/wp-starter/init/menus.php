<?php

/*
 * Enable WordPress menu support uncommenting the line below
 */
// add_theme_support('menus');

function register_custom_menus()
{
  /*
   * Place here all your register_nav_menu() calls.
   */

  register_nav_menus(
    array(
      'main_menu' => 'Main Menu',
      // 'secondary_menu' => 'Secondary Menu',
      'footer_menu_1' => 'Footer Menu 1',
      'footer_menu_2' => 'Footer Menu 2',
      'lang_switcher' => 'Language Switcher',
      // 'footer_menu_3' => 'Footer Menu 3',
      // 'footer_menu_4' => 'Footer Menu 4',
      // 'footer_menu_bottom' => 'Footer Menu Bottom'
    )
  );
}

add_action('init', 'register_custom_menus');
