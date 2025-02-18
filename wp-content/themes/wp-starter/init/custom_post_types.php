<?php

function manage_custom_post_types_and_custom_taxonomies()
{

  /**
   * Add new post type
   */

  /**
   * Some examples
   */
  new_post_type("member", "member", 'dashicons-admin-post', array('title', 'editor', 'thumbnail'), array('has_archive' => false));
  new_post_type("team", "team", 'dashicons-admin-post', array('title', 'editor', 'thumbnail'), array('has_archive' => false));
  // new_post_type("office", "offices", 'dashicons-admin-post', array('title', 'thumbnail'), array('has_archive' => false));
  // new_post_type("job", "jobs", 'dashicons-admin-post', array('title', 'editor', 'thumbnail'), array('has_archive' => false));
  // new_post_type("case_study", "case_studies", 'dashicons-admin-post', array('title', 'editor', 'excerpt',  'thumbnail'), array('show_in_rest' => true, 'has_archive' => false, 'taxonomies'  => array('category')));
  // new_post_type("service", "services", 'dashicons-admin-post', array('title', 'editor'), array('has_archive' => false));
  // new_post_type("audience", "audiences", 'dashicons-admin-post', array('title'), array('has_archive' => false, 'publicly_queryable'  => false));

  /**
   * Add new taxonomy
   */

  /**
   * Some examples
   */
  // new_taxonomy("department", "departments", array('team_member', 'job'), array('show_admin_column' => true, 'show_in_rest' => true));
  // new_taxonomy("location", "locations", array('job'), array('show_admin_column' => true, 'show_in_rest' => true));
  // new_taxonomy("insight_type", "insight_types", array('post'), array('show_admin_column' => true, 'show_in_rest' => true));
}

add_action('init', 'manage_custom_post_types_and_custom_taxonomies');

// Disable post tags
// add_action('init', function () {
//   register_taxonomy('post_tag', []);
// });
