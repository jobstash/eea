<?php

function shortcode_function($attrs) {

  /*
    Insert the function called by add_shortcode.
    $atts is the array of values passed by wordpress shortcode.
  */

}

function register_shortcodes(){
  
  /*
    Add new shortcode uncommenting the line below.
    The first value is the name of shortcode. The second is the function that calls.
  */

  // add_shortcode('shortcode', 'shortcode_function');

}


function add_title_body_class($classes) {
  global $post;
  
  if (isset($post)) {
      // Ottiene il titolo della pagina e lo converte in una classe CSS valida
      $page_title = sanitize_title($post->post_title);
      $classes[] = 'title-' . $page_title;
      
      // Se la pagina ha un parent, aggiungi anche il suo titolo
      if ($post->post_parent) {
          $parent = get_post($post->post_parent);
          $parent_title = sanitize_title($parent->post_title);
          $classes[] = 'parent-title-' . $parent_title;
      }
  }
  
  return $classes;
}
add_filter('body_class', 'add_title_body_class');

add_action( 'init', 'register_shortcodes');

function my_custom_mce_buttons($buttons) {
  array_unshift($buttons, 'styleselect'); // Aggiunge il menu a tendina
  return $buttons;
}
add_filter('mce_buttons', 'my_custom_mce_buttons');


function my_custom_mce_formats($init_array) {
  $style_formats = array(
      array(
          'title' => 'Gradient Text',
          'inline' => 'span',
          'classes' => 'gradient-text',
      ),
  );

  $init_array['style_formats'] = json_encode($style_formats);
  return $init_array;
}
add_filter('tiny_mce_before_init', 'my_custom_mce_formats');
