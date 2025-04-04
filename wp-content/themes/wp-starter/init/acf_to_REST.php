<?php
function create_ACF_meta_in_REST()
{
  $postypes_to_exclude = ['acf-field-group', 'acf-field'];
  $extra_postypes_to_include = ["page", "post"];
  // "_builtin" : If true, will return WordPress default post types. Use false to return only custom post types.
  $post_types = array_diff(get_post_types(["_builtin" => false], 'names'), $postypes_to_exclude);

  array_push($post_types, $extra_postypes_to_include);

  foreach ($post_types as $post_type) {
    register_rest_field(
      $post_type,
      'acf',
      [
        'get_callback'    => 'expose_ACF_fields',
        'schema'          => null,
      ]
    );
  }
}

function expose_ACF_fields($object)
{
  $ID = $object['id'];
  return get_fields($ID);
}

// if (class_exists('ACF')) {
//   add_action('rest_api_init', 'create_ACF_meta_in_REST');
// }
